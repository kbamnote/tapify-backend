<?php
/**
 * TAPIFY — leads created by the WhatsApp "check my Google score" bot, and the
 * link between one of those leads and the app account it eventually became.
 *
 * ── Why attribution is done by PHONE NUMBER ──
 * The obvious approach is Play's Install Referrer, but that needs a native
 * library in the app and therefore a rebuild and a store release before a
 * single lead can be measured. The next idea is a claim code the customer types
 * in — which works, and which most people will not bother to do.
 *
 * We already hold the one identifier that appears on both sides: the bot knows
 * the WhatsApp number it was talking to, and registration asks for a phone. So
 * matching is a lookup on the last ten digits, it needs no app change at all,
 * and it cannot be skipped by a customer who ignores an onboarding prompt.
 *
 * Matching on the LAST TEN DIGITS because the two sides genuinely disagree
 * about format: WhatsApp hands us "919373720903", the registration form gets
 * "9373720903" or "+91 93737 20903" depending on who typed it.
 */
class VisibilityLeads
{
    /** Wait this long before a lead is worth chasing again. */
    const FOLLOWUP_AFTER_DAYS = 7;

    /** Never chase the same person more than this many times. */
    const MAX_FOLLOWUPS = 2;

    /** Last 10 digits — the only part both sides reliably agree on. */
    public static function phone10($phone): string
    {
        $d = preg_replace('/\D/', '', (string)$phone);
        return strlen($d) > 10 ? substr($d, -10) : $d;
    }

    public static function ensureTable(PDO $db): void
    {
        try {
            $db->exec(
                "CREATE TABLE IF NOT EXISTS visibility_leads (
                   id             INT AUTO_INCREMENT PRIMARY KEY,
                   phone          VARCHAR(32)  NOT NULL,
                   phone10        VARCHAR(10)  NOT NULL,
                   place_id       VARCHAR(255) NULL,
                   business_name  VARCHAR(255) NULL,
                   address        VARCHAR(512) NULL,
                   score          INT          NULL,
                   band           VARCHAR(20)  NULL,
                   source         VARCHAR(40)  NOT NULL DEFAULT 'whatsapp',
                   followups_sent INT          NOT NULL DEFAULT 0,
                   last_followup  TIMESTAMP    NULL,
                   app_user_id    INT          NULL,
                   converted_at   TIMESTAMP    NULL,
                   gbp_connected_at TIMESTAMP  NULL,
                   created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                   updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                   UNIQUE KEY uk_phone10 (phone10),
                   INDEX idx_followup (converted_at, followups_sent, created_at)
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        } catch (Exception $e) {
            error_log('[VIS-LEAD] table setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Remember that this number was shown a score.
     *
     * One row per phone: someone who checks three businesses is still one lead,
     * and the row keeps their most recent check. Recording must never break the
     * conversation, so every failure is swallowed.
     */
    public static function record(PDO $db, $phone, array $place, array $score): void
    {
        $p10 = self::phone10($phone);
        if ($p10 === '') return;
        try {
            self::ensureTable($db);
            $st = $db->prepare(
                "INSERT INTO visibility_leads
                   (phone, phone10, place_id, business_name, address, score, band, source)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'whatsapp')
                 ON DUPLICATE KEY UPDATE
                   place_id      = VALUES(place_id),
                   business_name = VALUES(business_name),
                   address       = VALUES(address),
                   score         = VALUES(score),
                   band          = VALUES(band)"
            );
            $st->execute([
                (string)$phone, $p10,
                $place['place_id'] ?? null,
                mb_substr((string)($place['name'] ?? ''), 0, 255),
                mb_substr((string)($place['address'] ?? ''), 0, 512),
                isset($score['score']) ? (int)$score['score'] : null,
                $score['band'] ?? null,
            ]);
        } catch (Exception $e) {
            error_log('[VIS-LEAD] record failed: ' . $e->getMessage());
        }
    }

    /**
     * Called at registration. If this phone was scored by the bot, that lead
     * converted — mark it and return true.
     *
     * Never throws: a failed attribution must not stop somebody signing up.
     */
    public static function attribute(PDO $db, $phone, $userId): bool
    {
        $p10 = self::phone10($phone);
        if ($p10 === '' || !$userId) return false;
        try {
            $st = $db->prepare(
                "UPDATE visibility_leads
                    SET app_user_id = ?, converted_at = CURRENT_TIMESTAMP
                  WHERE phone10 = ? AND app_user_id IS NULL"
            );
            $st->execute([(int)$userId, $p10]);
            return $st->rowCount() > 0;
        } catch (Exception $e) {
            // Table may not exist yet on a host where the bot has never run.
            return false;
        }
    }

    /** The deeper conversion: they actually connected Google. */
    public static function markConnected(PDO $db, $userId): void
    {
        if (!$userId) return;
        try {
            $st = $db->prepare(
                "UPDATE visibility_leads SET gbp_connected_at = CURRENT_TIMESTAMP
                  WHERE app_user_id = ? AND gbp_connected_at IS NULL"
            );
            $st->execute([(int)$userId]);
        } catch (Exception $e) { /* nothing to do */ }
    }

    /**
     * Leads worth chasing: scored a while ago, never converted, not chased too
     * often already. Ordered oldest first so nobody is left behind.
     */
    public static function dueForFollowUp(PDO $db, int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        try {
            self::ensureTable($db);
            $st = $db->prepare(
                "SELECT id, phone, place_id, business_name, score, band, followups_sent
                   FROM visibility_leads
                  WHERE converted_at IS NULL
                    AND place_id IS NOT NULL
                    AND followups_sent < " . self::MAX_FOLLOWUPS . "
                    AND created_at <= (NOW() - INTERVAL " . self::FOLLOWUP_AFTER_DAYS . " DAY)
                    AND (last_followup IS NULL
                         OR last_followup <= (NOW() - INTERVAL " . self::FOLLOWUP_AFTER_DAYS . " DAY))
                  ORDER BY created_at ASC
                  LIMIT {$limit}"
            );
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /** Record that a follow-up went out, and the score it quoted. */
    public static function markFollowedUp(PDO $db, $id, $newScore = null): void
    {
        try {
            $st = $db->prepare(
                "UPDATE visibility_leads
                    SET followups_sent = followups_sent + 1,
                        last_followup  = CURRENT_TIMESTAMP,
                        score          = COALESCE(?, score)
                  WHERE id = ?"
            );
            $st->execute([$newScore !== null ? (int)$newScore : null, (int)$id]);
        } catch (Exception $e) {
            error_log('[VIS-LEAD] markFollowedUp failed: ' . $e->getMessage());
        }
    }

    /** Headline numbers for the admin panel. */
    public static function stats(PDO $db): array
    {
        try {
            $row = $db->query(
                "SELECT COUNT(*) AS leads,
                        SUM(converted_at IS NOT NULL)     AS installed,
                        SUM(gbp_connected_at IS NOT NULL) AS connected,
                        ROUND(AVG(score), 1)              AS avg_score
                   FROM visibility_leads"
            )->fetch(PDO::FETCH_ASSOC);
            return [
                'leads'     => (int)($row['leads'] ?? 0),
                'installed' => (int)($row['installed'] ?? 0),
                'connected' => (int)($row['connected'] ?? 0),
                'avg_score' => $row['avg_score'] !== null ? (float)$row['avg_score'] : null,
            ];
        } catch (Exception $e) {
            return ['leads' => 0, 'installed' => 0, 'connected' => 0, 'avg_score' => null];
        }
    }
}
