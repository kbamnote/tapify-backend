<?php
/**
 * SchemaRegistry — loads the builder contract from builder/schema/.
 *
 * This is the ONLY place that knows where the manifests live. The renderer,
 * both editors and (later) the AI generator all read the registry through
 * api/sites/schema.php, which is a thin wrapper over this class.
 *
 * Nothing here touches the database or any existing Tapify code.
 */

class SchemaRegistry
{
    /** @var array<string,array>|null cached section manifests keyed by type */
    private static $sections = null;
    /** @var array|null cached site JSON Schema */
    private static $siteSchema = null;

    private static function schemaDir(): string
    {
        return __DIR__ . '/../schema';
    }

    private static function readJson(string $path)
    {
        if (!is_file($path)) return null;
        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') return null;
        $data = json_decode($raw, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    /** The Site Document JSON Schema (pass 1 of validation). */
    public static function siteSchema(): array
    {
        if (self::$siteSchema === null) {
            self::$siteSchema = self::readJson(self::schemaDir() . '/site.schema.json') ?: [];
        }
        return self::$siteSchema;
    }

    /** All section manifests, keyed by type. */
    public static function sections(): array
    {
        if (self::$sections !== null) return self::$sections;

        $out = [];
        foreach (glob(self::schemaDir() . '/sections/*.json') ?: [] as $file) {
            $m = self::readJson($file);
            if (is_array($m) && !empty($m['type'])) {
                $out[$m['type']] = $m;
            }
        }
        ksort($out);
        self::$sections = $out;
        return $out;
    }

    /** One manifest, or null if the type is unknown. */
    public static function section(string $type): ?array
    {
        $all = self::sections();
        return $all[$type] ?? null;
    }

    public static function hasSection(string $type): bool
    {
        return self::section($type) !== null;
    }

    /** Theme presets, keyed by preset id. */
    public static function themes(): array
    {
        $out = [];
        foreach (glob(self::schemaDir() . '/themes/*.json') ?: [] as $file) {
            $t = self::readJson($file);
            if (is_array($t) && !empty($t['preset'])) $out[$t['preset']] = $t;
        }
        return $out;
    }

    /**
     * Industry recipes: industry id => recommended section types (drives the
     * "suggested sections" UI and, later, AI generation).
     */
    public static function industries(): array
    {
        $out = [];
        foreach (glob(self::schemaDir() . '/industries/*.json') ?: [] as $file) {
            $i = self::readJson($file);
            if (is_array($i) && !empty($i['id'])) $out[$i['id']] = $i;
        }
        return $out;
    }

    /**
     * Section types offered for an industry. A manifest opts in with
     * "industries": ["*"] (all) or an explicit list.
     */
    public static function sectionsForIndustry(?string $industry): array
    {
        $out = [];
        foreach (self::sections() as $type => $m) {
            $scope = $m['industries'] ?? ['*'];
            if (in_array('*', $scope, true) || ($industry && in_array($industry, $scope, true))) {
                $out[] = $type;
            }
        }
        return $out;
    }

    /**
     * Everything an editor needs in one payload. Both the web builder and the
     * mobile app call this once and render their whole UI from it.
     */
    public static function bundle(): array
    {
        return [
            'schemaVersion' => 1,
            'siteSchema'    => self::siteSchema(),
            'sections'      => array_values(self::sections()),
            'themes'        => array_values(self::themes()),
            'industries'    => array_values(self::industries()),
        ];
    }

    /**
     * A ready-to-insert section instance built from the manifest defaults.
     * Used by "Add section" in both editors and by the AI generator.
     */
    public static function newSectionInstance(string $type, ?string $variant = null): ?array
    {
        $m = self::section($type);
        if (!$m) return null;

        $defaults = $m['defaults'] ?? [];
        $variant  = $variant ?: ($defaults['variant'] ?? ($m['variants'][0]['id'] ?? null));

        $section = [
            'id'      => self::newSectionId(),
            'type'    => $type,
            'visible' => true,
            'props'   => $defaults['props'] ?? new stdClass(),
        ];
        if ($variant) $section['variant'] = $variant;

        $styleDefaults = $m['style']['defaults'] ?? null;
        if (is_array($styleDefaults) && $styleDefaults) $section['style'] = $styleDefaults;

        return $section;
    }

    /** Stable, collision-resistant section id (never reused). */
    public static function newSectionId(): string
    {
        return 's_' . bin2hex(random_bytes(5));
    }
}
