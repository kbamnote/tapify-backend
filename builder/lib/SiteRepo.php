<?php
/**
 * SiteRepo — all database access for the no-code builder.
 *
 * Touches ONLY the new builder tables (sites, site_versions, media_assets,
 * form_submissions). It never reads or writes vcards / whatsapp_stores / any
 * existing table.
 *
 * Version model
 * -------------
 *  - The DRAFT is a single mutable row (kind='draft'). Autosave UPDATEs it in
 *    place and bumps `rev`. We do NOT insert a row per keystroke, otherwise the
 *    table would explode during editing.
 *  - PUBLISH inserts an immutable snapshot (kind='published') and points
 *    sites.published_version_id at it. That snapshot is what the public
 *    renderer serves, so editing never affects the live site.
 *  - ROLLBACK just copies an old snapshot back into the draft (and can republish).
 *
 * Concurrency
 * -----------
 *  `rev` is the optimistic-lock token. A client saves with the rev it loaded;
 *  if someone else (web vs mobile app) saved in between, the UPDATE matches 0
 *  rows and we report a conflict instead of silently overwriting their work.
 */

require_once __DIR__ . '/../../config/database.php';

class SiteConflictException extends Exception {}
class SiteNotFoundException extends Exception {}

class SiteRepo
{
    /* ------------------------------------------------------------ sites */

    public static function findById($siteId): ?array
    {
        $stmt = getDB()->prepare("SELECT * FROM sites WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$siteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = getDB()->prepare("SELECT * FROM sites WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function listForUser($userId): array
    {
        $stmt = getDB()->prepare(
            "SELECT id, slug, name, industry, status, published_at, created_at, updated_at
             FROM sites WHERE user_id = ? ORDER BY updated_at DESC"
        );
        $stmt->execute([(int)$userId]);
        return $stmt->fetchAll();
    }

    public static function ownedBy(array $site, $userId): bool
    {
        return (int)$site['user_id'] === (int)$userId;
    }

    /** Every site (admin/staff view), with the owning client's name/email. */
    public static function listAll(): array
    {
        $stmt = getDB()->query(
            "SELECT s.id, s.slug, s.name, s.industry, s.status, s.published_at, s.created_at, s.updated_at,
                    s.user_id, u.name AS owner_name, u.email AS owner_email
             FROM sites s LEFT JOIN users u ON u.id = s.user_id
             ORDER BY s.updated_at DESC"
        );
        return $stmt->fetchAll();
    }

    /** Hard-delete a site and all its versions. Gate to admins at the endpoint. */
    public static function deleteSite(int $siteId): void
    {
        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            // Drop the version pointers first so the version rows can be removed
            // regardless of FK direction.
            $pdo->prepare("UPDATE sites SET draft_version_id = NULL, published_version_id = NULL WHERE id = ?")->execute([$siteId]);
            $pdo->prepare("DELETE FROM site_versions WHERE site_id = ?")->execute([$siteId]);
            $pdo->prepare("DELETE FROM sites WHERE id = ?")->execute([$siteId]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function slugAvailable(string $slug, $exceptSiteId = null): bool
    {
        $sql = "SELECT id FROM sites WHERE slug = ?";
        $params = [$slug];
        if ($exceptSiteId) { $sql .= " AND id != ?"; $params[] = (int)$exceptSiteId; }
        $stmt = getDB()->prepare($sql . " LIMIT 1");
        $stmt->execute($params);
        if ($stmt->fetch()) return false;

        // A retired slug still 301s to whichever site used to own it, so handing
        // it to a DIFFERENT site would make that address ambiguous. The site that
        // retired it may reclaim it (renaming back is allowed).
        return self::slugHistoryOwner($slug, $exceptSiteId) === null;
    }

    /**
     * Normalise a slug to a DNS label. Shared by create and rename so the two
     * can never drift apart.
     *
     * @return string '' when the input cannot be made into a valid label
     */
    public static function normaliseSlug(string $raw, string $fallbackFrom = ''): string
    {
        $slug = strtolower(trim($raw));
        if ($slug === '' && $fallbackFrom !== '') {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $fallbackFrom));
        }
        $slug = trim(preg_replace('/-+/', '-', $slug), '-');
        return preg_match('/^[a-z0-9](?:[a-z0-9-]{1,61}[a-z0-9])?$/', $slug) ? $slug : '';
    }

    /** The human-readable rule, so every caller reports it identically. */
    const SLUG_RULE = 'Address must be 3-63 characters: a-z, 0-9 and hyphens, not starting or ending with a hyphen.';

    // ── Slug history / redirects ──────────────────────────────────────────────

    /**
     * Site id that retired this slug, or null. Used both to resolve redirects
     * and to stop a retired address being reissued to someone else.
     */
    public static function slugHistoryOwner(string $slug, $exceptSiteId = null): ?int
    {
        $sql = "SELECT site_id FROM site_slug_history WHERE old_slug = ?";
        $params = [$slug];
        if ($exceptSiteId) { $sql .= " AND site_id != ?"; $params[] = (int)$exceptSiteId; }
        try {
            $stmt = getDB()->prepare($sql . " LIMIT 1");
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Table not migrated yet — behave as if there is no history rather
            // than breaking site creation.
            return null;
        }
        return $row ? (int)$row['site_id'] : null;
    }

    /**
     * Resolve a retired slug to the site's CURRENT slug for a 301.
     * Returns null when there is nothing to redirect to, and deliberately
     * returns null if the target equals the requested slug — that would be a
     * redirect loop (possible after renaming foo -> bar -> foo).
     */
    public static function redirectTargetForSlug(string $slug): ?string
    {
        $siteId = self::slugHistoryOwner($slug);
        if (!$siteId) return null;

        $stmt = getDB()->prepare("SELECT slug, published_version_id FROM sites WHERE id = ? LIMIT 1");
        $stmt->execute([$siteId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || empty($row['published_version_id'])) return null;   // nothing live to send them to
        if ($row['slug'] === $slug) return null;                         // loop guard

        return $row['slug'];
    }

    /**
     * Rename a site's public address, recording the old one so it keeps working.
     *
     * @throws Exception with a customer-safe message
     */
    public static function changeSlug(array $site, string $newSlug, $userId): array
    {
        $siteId  = (int)$site['id'];
        $oldSlug = (string)$site['slug'];

        if ($newSlug === $oldSlug) {
            throw new Exception('That is already this website\'s address.');
        }
        if (!self::slugAvailable($newSlug, $siteId)) {
            throw new Exception('That address is already taken. Try another.');
        }

        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            self::lockSite($pdo, $siteId);

            // Re-check under the lock: two renames racing for the same address
            // would otherwise both pass the check above.
            $chk = $pdo->prepare("SELECT id FROM sites WHERE slug = ? AND id != ? LIMIT 1");
            $chk->execute([$newSlug, $siteId]);
            if ($chk->fetch()) throw new Exception('That address was just taken. Try another.');

            // Reclaiming an address this site previously retired: drop the stale
            // redirect first, or it would point at itself.
            $pdo->prepare("DELETE FROM site_slug_history WHERE old_slug = ? AND site_id = ?")
                ->execute([$newSlug, $siteId]);

            $pdo->prepare("UPDATE sites SET slug = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$newSlug, $siteId]);

            // Keep the old address working. INSERT IGNORE because the same slug
            // may already be recorded from an earlier rename of this same site.
            $pdo->prepare("INSERT IGNORE INTO site_slug_history (site_id, old_slug, changed_by) VALUES (?, ?, ?)")
                ->execute([$siteId, $oldSlug, $userId ?: null]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        return ['slug' => $newSlug, 'previous_slug' => $oldSlug];
    }

    /**
     * Create a site plus its initial draft version.
     * @return array the new site row
     */
    public static function create($userId, string $name, string $slug, ?string $industry, array $doc): array
    {
        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO sites (user_id, slug, name, industry, status) VALUES (?, ?, ?, ?, 'draft')"
            );
            $stmt->execute([(int)$userId, $slug, $name, $industry]);
            $siteId = (int)$pdo->lastInsertId();

            $versionId = self::insertVersion($pdo, $siteId, $doc, 1, 'draft', $userId, 'web', 'Initial draft');

            $pdo->prepare("UPDATE sites SET draft_version_id = ? WHERE id = ?")
                ->execute([$versionId, $siteId]);

            $pdo->commit();
            return self::findById($siteId);
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /* --------------------------------------------------------- versions */

    private static function nextRev(PDO $pdo, int $siteId): int
    {
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(rev), 0) + 1 FROM site_versions WHERE site_id = ?");
        $stmt->execute([$siteId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Serialise concurrent writers for one site (row lock on the sites row).
     * Without this, an autosave and a publish (or two saves) can read the same
     * MAX(rev) and both allocate the same rev — a duplicate uk_site_rev crash.
     * Must be called inside an open transaction.
     */
    private static function lockSite(PDO $pdo, int $siteId): void
    {
        $pdo->prepare("SELECT id FROM sites WHERE id = ? FOR UPDATE")->execute([$siteId]);
    }

    private static function insertVersion(PDO $pdo, int $siteId, array $doc, int $rev, string $kind, $userId, string $source, ?string $label = null): int
    {
        $stmt = $pdo->prepare(
            "INSERT INTO site_versions (site_id, rev, doc, schema_version, kind, label, author_user_id, source)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $siteId,
            $rev,
            json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            (int)($doc['schemaVersion'] ?? 1),
            $kind,
            $label,
            $userId ? (int)$userId : null,
            $source,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function getVersion($versionId): ?array
    {
        $stmt = getDB()->prepare("SELECT * FROM site_versions WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$versionId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $row['doc'] = json_decode($row['doc'], true);
        return $row;
    }

    /**
     * The working draft: ['rev' => int, 'doc' => array, 'version_id' => int].
     */
    public static function getDraft(array $site): ?array
    {
        if (empty($site['draft_version_id'])) return null;
        $v = self::getVersion($site['draft_version_id']);
        if (!$v) return null;
        return ['version_id' => (int)$v['id'], 'rev' => (int)$v['rev'], 'doc' => $v['doc'], 'updated_at' => $v['created_at']];
    }

    /**
     * The live document. Returns null when the site has never been published —
     * callers must then 404 rather than fall back to the draft.
     */
    public static function getPublished(array $site): ?array
    {
        if (empty($site['published_version_id'])) return null;
        $v = self::getVersion($site['published_version_id']);
        if (!$v) return null;
        return ['version_id' => (int)$v['id'], 'rev' => (int)$v['rev'], 'doc' => $v['doc'], 'published_at' => $site['published_at']];
    }

    /** Snapshot history (published + labelled), newest first. Draft excluded. */
    public static function listVersions($siteId, int $limit = 50): array
    {
        $stmt = getDB()->prepare(
            "SELECT id, rev, kind, label, author_user_id, source, schema_version, created_at,
                    CHAR_LENGTH(doc) AS doc_bytes
             FROM site_versions
             WHERE site_id = ? AND kind = 'published'
             ORDER BY created_at DESC
             LIMIT " . (int)$limit
        );
        $stmt->execute([(int)$siteId]);
        return $stmt->fetchAll();
    }

    /* ------------------------------------------------------------ saving */

    /**
     * Autosave the draft, guarded by optimistic locking.
     *
     * @param int $baseRev the rev the client loaded/last saw
     * @throws SiteConflictException when someone else saved in the meantime
     * @return array ['rev' => int, 'version_id' => int]
     */
    public static function saveDraft(array $site, array $doc, int $baseRev, $userId, string $source = 'web'): array
    {
        $pdo = getDB();
        $siteId = (int)$site['id'];

        // No draft yet (e.g. site created outside the builder) -> create one.
        if (empty($site['draft_version_id'])) {
            $pdo->beginTransaction();
            try {
                self::lockSite($pdo, $siteId);
                $rev = self::nextRev($pdo, $siteId);
                $vid = self::insertVersion($pdo, $siteId, $doc, $rev, 'draft', $userId, $source);
                $pdo->prepare("UPDATE sites SET draft_version_id = ? WHERE id = ?")->execute([$vid, $siteId]);
                $pdo->commit();
                return ['rev' => $rev, 'version_id' => $vid];
            } catch (Exception $e) { $pdo->rollBack(); throw $e; }
        }

        $pdo->beginTransaction();
        try {
            self::lockSite($pdo, $siteId);
            $newRev = self::nextRev($pdo, $siteId);

            // The WHERE rev = ? is the lock: 0 affected rows === stale client.
            $stmt = $pdo->prepare(
                "UPDATE site_versions
                    SET doc = ?, rev = ?, schema_version = ?, author_user_id = ?, source = ?
                  WHERE id = ? AND site_id = ? AND rev = ? AND kind = 'draft'"
            );
            // json_encode returning false here would bind an empty string and
            // silently wipe the customer's draft while still reporting success.
            // Fail loudly instead of destroying their work.
            $encoded = json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($encoded === false) {
                throw new Exception('Could not encode the document (' . json_last_error_msg() . ').');
            }

            $stmt->execute([
                $encoded,
                $newRev,
                (int)($doc['schemaVersion'] ?? 1),
                $userId ? (int)$userId : null,
                $source,
                (int)$site['draft_version_id'],
                $siteId,
                $baseRev,
            ]);

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                throw new SiteConflictException('This site was changed somewhere else since you loaded it.');
            }

            $pdo->prepare("UPDATE sites SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$siteId]);
            $pdo->commit();
            return ['rev' => $newRev, 'version_id' => (int)$site['draft_version_id']];
        } catch (SiteConflictException $e) {
            throw $e;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Publish: snapshot the current draft and point the site at it.
     * The draft is left untouched so editing can continue immediately.
     */
    public static function publish(array $site, $userId, ?string $label = null, string $source = 'web'): array
    {
        $draft = self::getDraft($site);
        if (!$draft) throw new SiteNotFoundException('Nothing to publish — this site has no draft.');

        $pdo = getDB();
        $siteId = (int)$site['id'];
        $pdo->beginTransaction();
        try {
            self::lockSite($pdo, $siteId);
            $rev = self::nextRev($pdo, $siteId);
            $vid = self::insertVersion($pdo, $siteId, $draft['doc'], $rev, 'published', $userId, $source, $label);

            $pdo->prepare(
                "UPDATE sites
                    SET published_version_id = ?, status = 'published', published_at = CURRENT_TIMESTAMP
                  WHERE id = ?"
            )->execute([$vid, $siteId]);

            $pdo->commit();
            return ['version_id' => $vid, 'rev' => $rev];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Copy an old snapshot back into the draft (non-destructive: the live site
     * only changes if the user then publishes).
     */
    public static function revertDraftTo(array $site, $versionId, $userId, string $source = 'web'): array
    {
        $v = self::getVersion($versionId);
        if (!$v || (int)$v['site_id'] !== (int)$site['id']) {
            throw new SiteNotFoundException('Version not found for this site.');
        }
        $draft = self::getDraft($site);
        $baseRev = $draft ? $draft['rev'] : 0;
        return self::saveDraft($site, $v['doc'], $baseRev, $userId, $source);
    }

    public static function setStatus(array $site, string $status): void
    {
        getDB()->prepare("UPDATE sites SET status = ? WHERE id = ?")
               ->execute([$status, (int)$site['id']]);
    }
}
