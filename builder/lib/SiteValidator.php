<?php
/**
 * SiteValidator — server-side validation of a Site Document.
 *
 * Runs on EVERY save. The document is user input, so nothing is trusted:
 * an invalid document is rejected, never stored and never rendered. This is
 * also what will make future AI generation safe — the AI emits JSON and this
 * class is the gate.
 *
 * Two passes (see builder/schema/README.md):
 *   1. Document shape  — pages/sections/theme/ids/slugs/limits.
 *   2. Section props   — against each section's own manifest.
 *
 * Deliberately dependency-free (this backend has no composer vendor/), so this
 * is a focused validator for the invariants that matter rather than a generic
 * JSON Schema engine.
 */

require_once __DIR__ . '/SchemaRegistry.php';

class SiteValidator
{
    /** @var string[] */
    private $errors = [];

    /**
     * When false (draft autosaves), "required content" checks are skipped so a
     * work-in-progress can always be saved — you should never be blocked from
     * saving because a headline is momentarily blank. Structure is still fully
     * validated (ids, slugs, types, limits), and publish.php re-validates in
     * strict mode, so an incomplete site still cannot go live.
     */
    private bool $strict = true;

    public const MAX_PAGES            = 50;
    public const MAX_SECTIONS_PER_PAGE = 60;
    public const MAX_DOC_BYTES        = 2097152; // 2 MB of JSON is already a huge site

    /**
     * @return string[] list of human-readable errors; empty array === valid
     */
    public function validate($doc, bool $strict = true): array
    {
        $this->strict = $strict;
        $this->errors = [];

        if (!is_array($doc)) {
            return ['Document must be a JSON object.'];
        }

        $encoded = json_encode($doc);
        if ($encoded !== false && strlen($encoded) > self::MAX_DOC_BYTES) {
            $this->err('', 'Document is too large (max ' . round(self::MAX_DOC_BYTES / 1048576, 1) . ' MB).');
            return $this->errors;
        }

        // --- schemaVersion ---
        if (($doc['schemaVersion'] ?? null) !== 1) {
            $this->err('schemaVersion', 'must be 1');
        }

        // --- site ---
        $site = $doc['site'] ?? null;
        if (!is_array($site)) {
            $this->err('site', 'must be an object');
        } elseif ($this->strict && !$this->nonEmptyString($site['name'] ?? null)) {
            $this->err('site.name', 'is required');
        }

        // --- theme ---
        $this->validateTheme($doc['theme'] ?? null);

        // --- pages ---
        $pages = $doc['pages'] ?? null;
        if (!is_array($pages) || count($pages) < 1) {
            $this->err('pages', 'must contain at least one page');
            return $this->errors;
        }
        if (count($pages) > self::MAX_PAGES) {
            $this->err('pages', 'too many pages (max ' . self::MAX_PAGES . ')');
        }

        $pageIds = [];
        $slugs   = [];
        $sectionIds = []; // section ids must be unique across the whole document
        foreach ($pages as $i => $page) {
            $this->validatePage($page, "pages[$i]", $pageIds, $slugs, $sectionIds);
        }

        if (!in_array('/', $slugs, true)) {
            $this->err('pages', 'one page must have slug "/" (the home page)');
        }

        // --- forms ---
        $forms = $doc['forms'] ?? [];
        if ($forms !== [] && !$this->isList($forms)) {
            $this->err('forms', 'must be an array');
        } elseif (is_array($forms)) {
            $formIds = [];
            foreach ($forms as $i => $form) {
                $this->validateForm($form, "forms[$i]", $formIds);
            }
        }

        return $this->errors;
    }

    // ---------------------------------------------------------------- theme

    private function validateTheme($theme): void
    {
        if ($theme === null) return;
        if (!is_array($theme)) { $this->err('theme', 'must be an object'); return; }

        foreach (($theme['color'] ?? []) as $key => $val) {
            if (!is_string($val) || !preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $val)) {
                $this->err("theme.color.$key", 'must be a hex colour like #1B3A6B');
            }
        }
        $this->enum($theme['mode']      ?? null, ['light','dark'],                              'theme.mode');
        $this->enum($theme['radius']    ?? null, ['none','sm','md','lg','xl','pill'],           'theme.radius');
        $this->enum($theme['spacing']   ?? null, ['compact','comfortable','spacious'],          'theme.spacing');
        $this->enum($theme['container'] ?? null, ['narrow','normal','wide','full'],             'theme.container');
    }

    // ----------------------------------------------------------------- page

    private function validatePage($page, string $path, array &$pageIds, array &$slugs, array &$sectionIds): void
    {
        if (!is_array($page)) { $this->err($path, 'must be an object'); return; }

        $id = $page['id'] ?? null;
        if (!is_string($id) || !preg_match('/^[a-z0-9_-]{1,60}$/', $id)) {
            $this->err("$path.id", 'must match ^[a-z0-9_-]{1,60}$');
        } elseif (in_array($id, $pageIds, true)) {
            $this->err("$path.id", "duplicate page id \"$id\"");
        } else {
            $pageIds[] = $id;
        }

        $slug = $page['slug'] ?? null;
        if (!is_string($slug) || !preg_match('#^/[a-z0-9/-]*$#', $slug)) {
            $this->err("$path.slug", 'must start with "/" and contain only a-z 0-9 - /');
        } elseif (in_array($slug, $slugs, true)) {
            $this->err("$path.slug", "duplicate slug \"$slug\"");
        } else {
            $slugs[] = $slug;
        }

        if ($this->strict && !$this->nonEmptyString($page['title'] ?? null)) {
            $this->err("$path.title", 'is required');
        }

        $sections = $page['sections'] ?? null;
        if (!$this->isList($sections)) {
            $this->err("$path.sections", 'must be an array');
            return;
        }
        if (count($sections) > self::MAX_SECTIONS_PER_PAGE) {
            $this->err("$path.sections", 'too many sections (max ' . self::MAX_SECTIONS_PER_PAGE . ')');
        }

        foreach ($sections as $i => $section) {
            $this->validateSection($section, "$path.sections[$i]", $sectionIds);
        }
    }

    // -------------------------------------------------------------- section

    private function validateSection($section, string $path, array &$sectionIds): void
    {
        if (!is_array($section)) { $this->err($path, 'must be an object'); return; }

        $id = $section['id'] ?? null;
        if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{1,40}$/', $id)) {
            $this->err("$path.id", 'must match ^[a-zA-Z0-9_-]{1,40}$');
        } elseif (in_array($id, $sectionIds, true)) {
            $this->err("$path.id", "duplicate section id \"$id\" (ids must be unique across the site)");
        } else {
            $sectionIds[] = $id;
        }

        $type = $section['type'] ?? null;
        if (!is_string($type) || $type === '') {
            $this->err("$path.type", 'is required');
            return;
        }

        $manifest = SchemaRegistry::section($type);
        if (!$manifest) {
            // Unknown type => reject. Prevents a client (or an AI) inventing sections
            // the renderer cannot draw.
            $this->err("$path.type", "unknown section type \"$type\"");
            return;
        }

        // variant must exist in the manifest
        $variant = $section['variant'] ?? null;
        if ($variant !== null) {
            $ids = array_column($manifest['variants'] ?? [], 'id');
            if ($ids && !in_array($variant, $ids, true)) {
                $this->err("$path.variant", "unknown variant \"$variant\" for \"$type\" (allowed: " . implode(', ', $ids) . ')');
            }
        }

        if (isset($section['visible']) && !is_bool($section['visible'])) {
            $this->err("$path.visible", 'must be true/false');
        }

        // style keys must be supported by the manifest
        $supports = $manifest['style']['supports'] ?? [];
        foreach (['style' => $section['style'] ?? null,
                  'responsive.mobile' => $section['responsive']['mobile'] ?? null,
                  'responsive.tablet' => $section['responsive']['tablet'] ?? null] as $sub => $style) {
            if ($style === null) continue;
            if (!is_array($style)) { $this->err("$path.$sub", 'must be an object'); continue; }
            foreach (array_keys($style) as $k) {
                if ($supports && !in_array($k, $supports, true)) {
                    $this->err("$path.$sub.$k", "\"$type\" does not support style \"$k\"");
                }
            }
        }

        // ---- pass 2: props against the manifest ----
        $props = $section['props'] ?? [];
        if (!is_array($props)) { $this->err("$path.props", 'must be an object'); return; }
        $this->validateProps($props, $manifest['props'] ?? [], "$path.props", $variant);
    }

    /**
     * Validate a props object against a manifest field list. Recurses for repeaters.
     */
    private function validateProps(array $props, array $fields, string $path, ?string $variant): void
    {
        foreach ($fields as $f) {
            $key = $f['key'] ?? null;
            if (!$key) continue;

            // A field hidden for this variant is not required.
            $applies = true;
            if (isset($f['showIf']['variant']) && $variant !== null) {
                $applies = in_array($variant, (array)$f['showIf']['variant'], true);
            }

            $has = array_key_exists($key, $props) && $props[$key] !== null && $props[$key] !== '';
            if ($this->strict && !empty($f['required']) && $applies && !$has) {
                $this->err("$path.$key", 'is required');
                continue;
            }
            if (!$has) continue;

            $this->validateField($props[$key], $f, "$path.$key", $variant);
        }

        // A prop the CURRENT manifest doesn't declare is intentionally NOT an
        // error (this used to reject the document — removed after it broke a
        // real site). Manifests evolve (fields get renamed/removed) and there is
        // no migration system, so any site built under an older version of a
        // manifest would otherwise be permanently stuck: the stale key is
        // un-removable from the UI (the Inspector only edits fields the current
        // manifest knows about), and the old check ran even on lenient draft
        // saves, so the customer could never save or publish again. No renderer
        // ever reads a prop key it doesn't declare, so a stale key is inert,
        // unused data — safe to carry forward silently.
    }

    private function validateField($val, array $f, string $path, ?string $variant): void
    {
        $type = $f['type'] ?? 'text';

        switch ($type) {
            case 'text':
            case 'textarea':
            case 'richtext':
            case 'icon':
                if (!is_string($val)) { $this->err($path, 'must be text'); return; }
                if (!empty($f['maxLength']) && $this->strLen($val) > $f['maxLength']) {
                    $this->err($path, "is too long (max {$f['maxLength']})");
                }
                break;

            case 'number':
                if (!is_int($val) && !is_float($val)) { $this->err($path, 'must be a number'); return; }
                if (isset($f['min']) && $val < $f['min']) $this->err($path, "must be >= {$f['min']}");
                if (isset($f['max']) && $val > $f['max']) $this->err($path, "must be <= {$f['max']}");
                break;

            case 'toggle':
                if (!is_bool($val)) $this->err($path, 'must be true/false');
                break;

            case 'color':
                if (!is_string($val) || !preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $val)) {
                    $this->err($path, 'must be a hex colour');
                }
                break;

            case 'select':
                $opts = $f['options'] ?? [];
                if ($opts && !in_array($val, $opts, true)) {
                    $this->err($path, 'must be one of: ' . implode(', ', $opts));
                }
                break;

            case 'media':
                if (!is_string($val) || !preg_match('#^(media:[0-9]+|https?://.+|/.+)$#', $val)) {
                    $this->err($path, 'must be "media:<id>", an absolute URL, or a /path');
                }
                break;

            case 'link':
                if (!is_array($val)) { $this->err($path, 'must be a link object'); return; }
                foreach (array_keys($val) as $k) {
                    if (!in_array($k, ['text','href','newTab','style'], true)) {
                        $this->err("$path.$k", 'unknown link property');
                    }
                }
                if (isset($val['style'])) $this->enum($val['style'], ['primary','secondary','ghost','link'], "$path.style");
                if (isset($val['newTab']) && !is_bool($val['newTab'])) $this->err("$path.newTab", 'must be true/false');
                break;

            case 'list':
                if (!$this->isList($val)) { $this->err($path, 'must be an array'); return; }
                foreach ($val as $i => $s) {
                    if (!is_string($s)) $this->err("$path[$i]", 'must be text');
                }
                break;

            case 'repeater':
                if (!$this->isList($val)) { $this->err($path, 'must be an array'); return; }
                if (isset($f['min']) && count($val) < $f['min']) $this->err($path, "needs at least {$f['min']} item(s)");
                if (isset($f['max']) && count($val) > $f['max']) $this->err($path, "allows at most {$f['max']} item(s)");
                foreach ($val as $i => $item) {
                    if (!is_array($item)) { $this->err("$path[$i]", 'must be an object'); continue; }
                    $this->validateProps($item, $f['fields'] ?? [], "$path[$i]", $variant);
                }
                break;

            default:
                $this->err($path, "unsupported field type \"$type\" in manifest");
        }
    }

    // ----------------------------------------------------------------- form

    private function validateForm($form, string $path, array &$formIds): void
    {
        if (!is_array($form)) { $this->err($path, 'must be an object'); return; }

        $id = $form['id'] ?? null;
        if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{1,60}$/', $id)) {
            $this->err("$path.id", 'must match ^[a-zA-Z0-9_-]{1,60}$');
        } elseif (in_array($id, $formIds, true)) {
            $this->err("$path.id", "duplicate form id \"$id\"");
        } else {
            $formIds[] = $id;
        }

        $fields = $form['fields'] ?? null;
        if (!$this->isList($fields) || count($fields) < 1) {
            $this->err("$path.fields", 'must contain at least one field');
            return;
        }

        $allowed = ['text','email','tel','number','textarea','select','checkbox','radio','date','file'];
        $names = [];
        foreach ($fields as $i => $fld) {
            $p = "$path.fields[$i]";
            if (!is_array($fld)) { $this->err($p, 'must be an object'); continue; }

            $name = $fld['name'] ?? null;
            if (!is_string($name) || !preg_match('/^[a-zA-Z0-9_]{1,40}$/', $name)) {
                $this->err("$p.name", 'must match ^[a-zA-Z0-9_]{1,40}$');
            } elseif (in_array($name, $names, true)) {
                $this->err("$p.name", "duplicate field name \"$name\"");
            } else {
                $names[] = $name;
            }

            if ($this->strict && !$this->nonEmptyString($fld['label'] ?? null)) $this->err("$p.label", 'is required');
            $this->enum($fld['type'] ?? null, $allowed, "$p.type", $this->strict);
        }
    }

    // ---------------------------------------------------------------- utils

    private function err(string $path, string $msg): void
    {
        $this->errors[] = ($path === '' ? '' : $path . ' ') . $msg;
    }

    private function enum($val, array $allowed, string $path, bool $required = false): void
    {
        if ($val === null) { if ($required) $this->err($path, 'is required'); return; }
        if (!in_array($val, $allowed, true)) {
            $this->err($path, 'must be one of: ' . implode(', ', $allowed));
        }
    }

    private function nonEmptyString($v): bool
    {
        return is_string($v) && trim($v) !== '';
    }

    /**
     * Character (not byte) length. Uses mbstring when present, otherwise counts
     * UTF-8 code points directly — mbstring is NOT declared in composer.json, and
     * byte-length would wrongly reject valid Hindi/Devanagari or emoji content.
     */
    private function strLen(string $s): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($s, 'UTF-8');
        }
        $n = preg_match_all('/./us', $s);
        return $n === false ? strlen($s) : $n;
    }

    /** True for a JSON array (list), false for an object/assoc array. */
    private function isList($v): bool
    {
        if (!is_array($v)) return false;
        return $v === [] || array_keys($v) === range(0, count($v) - 1);
    }
}
