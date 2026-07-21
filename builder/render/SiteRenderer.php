<?php
/**
 * SiteRenderer — renders a published builder site (the JSON document produced by
 * the website builder) to a self-contained HTML page, served from Railway at
 * <slug>.tapify.co.in exactly like a vCard.
 *
 * Why PHP and not the Next.js app:
 *   - the same wildcard *.tapify.co.in already points here, so every published
 *     site is instantly live with NO per-site DNS or Vercel work;
 *   - fully server-rendered HTML is the best case for SEO;
 *   - it lives beside the vCards, so there is one hosting story, not two.
 *
 * The Next.js app remains the editor (the interactive builder UI). Both draw the
 * SAME document — the section manifests are the shared contract — so this is a
 * faithful port of src/sections/* and src/lib/theme.ts.
 */

require_once __DIR__ . '/../lib/SiteRepo.php';

class SiteRenderer
{
    /** @var string current site slug (for the contact form's hidden field) */
    private static $slug = '';

    /* ------------------------------------------------------------- entry */

    /** Fast check used by the router: is this subdomain a PUBLISHED builder site? */
    public static function hasPublishedSite(string $slug): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') return false;
        try {
            $stmt = getDB()->prepare(
                "SELECT 1 FROM sites
                 WHERE slug = ? AND status = 'published' AND published_version_id IS NOT NULL
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Render the page at $path for site $slug. Echoes the page and returns true,
     * or 404s (still returns true — the host belongs to this site either way).
     */
    public static function renderBySlug(string $slug, string $path = '/'): bool
    {
        // This is a customer-facing page: never leak PHP notices/warnings into the
        // HTML. They are still written to the error log for debugging.
        @ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

        self::$slug = strtolower(trim($slug));

        $site = SiteRepo::findBySlug(self::$slug);
        if (!$site || ($site['status'] ?? '') === 'disabled') { self::notFound(); return true; }

        $published = SiteRepo::getPublished($site);
        $doc = is_array($published) ? ($published['doc'] ?? null) : null;
        if (!is_array($doc) || empty($doc['pages'])) { self::notFound(); return true; }

        $norm = ($path === '' || $path === '/') ? '/' : rtrim($path, '/');
        $page = null;
        foreach ($doc['pages'] as $p) {
            if (($p['slug'] ?? '') === $norm) { $page = $p; break; }
        }
        if (!$page || ($page['visible'] ?? true) === false) { self::notFound(); return true; }

        header('Content-Type: text/html; charset=utf-8');
        echo self::renderDocument($doc, $page);
        return true;
    }

    private static function notFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Not found</title>'
           . '<meta name="viewport" content="width=device-width, initial-scale=1">'
           . '<style>body{font-family:system-ui,sans-serif;text-align:center;padding:60px 20px;color:#334155}h1{font-size:3rem;margin:0}</style>'
           . '</head><body><h1>404</h1><p>This page could not be found.</p></body></html>';
    }

    /* ------------------------------------------------------------- page */

    private static function renderDocument(array $doc, array $page): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $head  = self::head($doc, $page, $fonts);

        $sections = '';
        foreach (($page['sections'] ?? []) as $s) {
            if (($s['visible'] ?? true) === false) continue;
            if (!empty($s['style']['hidden'])) continue;
            $sections .= self::section($s, $doc);
        }

        // The theme tokens go in a <style> block, NOT an inline style attribute:
        // font values contain double quotes ("DM Sans") which would otherwise
        // truncate a double-quoted style="" and drop every var after them
        // (fonts, spacing, radius). In a stylesheet the quotes are valid CSS.
        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . $head
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . $sections . "</main>"
             . "</body></html>";
    }

    private static function head(array $doc, array $page, ?string $fonts): string
    {
        $site = $doc['site'] ?? [];
        $seo  = $page['seo'] ?? [];
        $name = $site['name'] ?? 'Website';
        $title = trim((string)($seo['title'] ?? '')) !== ''
            ? $seo['title']
            : ($page['title'] ?? $name) . ' | ' . $name;

        $host = $_SERVER['HTTP_HOST'] ?? (self::$slug . '.tapify.co.in');
        $canonical = $seo['canonical'] ?? ('https://' . $host . ($page['slug'] === '/' ? '' : $page['slug']));
        $og = self::media($seo['ogImage'] ?? null);
        $favicon = self::media($site['favicon'] ?? null);

        $h  = '<meta charset="utf-8">';
        $h .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $h .= '<title>' . self::esc($title) . '</title>';
        if (!empty($seo['description'])) $h .= '<meta name="description" content="' . self::esc($seo['description']) . '">';
        if (!empty($seo['keywords']) && is_array($seo['keywords'])) $h .= '<meta name="keywords" content="' . self::esc(implode(', ', $seo['keywords'])) . '">';
        $h .= '<meta name="robots" content="' . self::esc($seo['robots'] ?? 'index,follow') . '">';
        $h .= '<link rel="canonical" href="' . self::esc($canonical) . '">';
        if ($favicon) $h .= '<link rel="icon" href="' . self::esc($favicon) . '">';

        // Open Graph / Twitter
        $h .= '<meta property="og:title" content="' . self::esc($title) . '">';
        if (!empty($seo['description'])) $h .= '<meta property="og:description" content="' . self::esc($seo['description']) . '">';
        $h .= '<meta property="og:type" content="website">';
        $h .= '<meta property="og:site_name" content="' . self::esc($name) . '">';
        $h .= '<meta property="og:url" content="' . self::esc($canonical) . '">';
        if ($og) {
            $h .= '<meta property="og:image" content="' . self::esc($og) . '">';
            $h .= '<meta name="twitter:card" content="summary_large_image">';
            $h .= '<meta name="twitter:image" content="' . self::esc($og) . '">';
        } else {
            $h .= '<meta name="twitter:card" content="summary">';
        }

        if ($fonts) $h .= '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="' . self::esc($fonts) . '">';
        return $h;
    }

    /* --------------------------------------------------------- theme */

    private static function themeVars(array $t): string
    {
        $fallback = [
            'primary' => '#2563EB', 'secondary' => '#1D4ED8', 'accent' => '#F7941D',
            'bg' => '#FFFFFF', 'surface' => '#F5F7FB', 'text' => '#111827',
            'muted' => '#6B7280', 'border' => '#E5E7EB',
        ];
        $c = $fallback;
        foreach (($t['color'] ?? []) as $k => $v) {
            if (is_string($v) && trim($v) !== '') $c[$k] = $v;
        }

        $radiusMap    = ['none'=>'0px','sm'=>'6px','md'=>'12px','lg'=>'18px','xl'=>'26px','pill'=>'999px'];
        $spacingMap   = ['compact'=>'0.75','comfortable'=>'1','spacious'=>'1.35'];
        $containerMap = ['narrow'=>'900px','normal'=>'1200px','wide'=>'1440px','full'=>'100%'];

        $radius    = $radiusMap[$t['radius'] ?? 'md'] ?? '12px';
        $spacing   = $spacingMap[$t['spacing'] ?? 'comfortable'] ?? '1';
        $container = $containerMap[$t['container'] ?? 'normal'] ?? '1200px';

        $head = !empty($t['font']['heading']) ? '"' . $t['font']['heading'] . '", system-ui, sans-serif' : 'system-ui, sans-serif';
        $body = !empty($t['font']['body']) ? '"' . $t['font']['body'] . '", system-ui, sans-serif' : 'system-ui, sans-serif';

        $vars = [
            '--color-primary'      => $c['primary'],
            '--color-primary-dark' => self::shade($c['primary'], -0.18),
            '--color-primary-fg'   => self::readableOn($c['primary']),
            '--color-secondary'    => $c['secondary'],
            '--color-accent'       => $c['accent'],
            '--color-accent-fg'    => self::readableOn($c['accent']),
            '--color-bg'           => $c['bg'],
            '--color-surface'      => $c['surface'],
            '--color-text'         => $c['text'],
            '--color-muted'        => $c['muted'],
            '--color-border'       => $c['border'],
            '--font-heading'       => $head,
            '--font-body'          => $body,
            '--radius'             => $radius,
            '--space-scale'        => $spacing,
            '--container'          => $container,
        ];
        $out = '';
        foreach ($vars as $k => $v) $out .= $k . ':' . $v . ';';
        return $out;
    }

    private static function googleFonts(array $t): ?string
    {
        $fams = [];
        foreach ([$t['font']['heading'] ?? null, $t['font']['body'] ?? null] as $f) {
            $f = trim((string)$f);
            if ($f !== '' && stripos($f, 'system') !== 0) $fams[$f] = true;
        }
        if (!$fams) return null;
        $q = [];
        foreach (array_keys($fams) as $f) {
            $q[] = 'family=' . str_replace('%20', '+', rawurlencode($f)) . ':wght@400;500;600;700';
        }
        return 'https://fonts.googleapis.com/css2?' . implode('&', $q) . '&display=swap';
    }

    /** Darken a hex colour (matches theme.ts shade()). */
    private static function shade(string $hex, float $amt = -0.15): string
    {
        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($hex), $m)) return $hex;
        $h = $m[1];
        if (strlen($h) === 3) $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        $num = hexdec($h);
        $r = max(0, min(255, (int)round((($num >> 16) & 255) * (1 + $amt))));
        $g = max(0, min(255, (int)round((($num >> 8) & 255) * (1 + $amt))));
        $b = max(0, min(255, (int)round(($num & 255) * (1 + $amt))));
        return sprintf('#%06x', ($r << 16) | ($g << 8) | $b);
    }

    /** Readable text colour (#111827 or #FFFFFF) for a background (matches theme.ts). */
    private static function readableOn(string $hex): string
    {
        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($hex), $m)) return '#FFFFFF';
        $h = $m[1];
        if (strlen($h) === 3) $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        $num = hexdec($h);
        $r = ($num >> 16) & 255; $g = ($num >> 8) & 255; $b = $num & 255;
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $lum > 0.6 ? '#111827' : '#FFFFFF';
    }

    /* --------------------------------------------------------- helpers */

    private static function esc($v): string
    {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }

    /** Resolve "media:<id>" | URL | /path to a servable src. */
    private static function media($ref): ?string
    {
        if (!is_string($ref) || $ref === '') return null;
        if (preg_match('#^(https?://|/)#i', $ref)) return $ref;
        if (preg_match('/^media:(\d+)$/', $ref, $m)) {
            $base = defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in';
            return $base . '/api/sites/media.php?id=' . $m[1];
        }
        return null;
    }

    private static function isDarkBg(array $s): bool
    {
        $bg = $s['style']['bg'] ?? 'default';
        return in_array($bg, ['primary', 'dark', 'image'], true);
    }

    /** Background css [background, color] for a section bg token. */
    private static function bgCss(string $bg, ?string $img): array
    {
        switch ($bg) {
            case 'surface': return ['background:var(--color-surface);', 'color:var(--color-text);'];
            case 'primary': return ['background:var(--color-primary);', 'color:var(--color-primary-fg);'];
            case 'dark':    return ['background:#0F172A;', 'color:#F8FAFC;'];
            case 'image':   return ['', 'color:#fff;'];
            case 'none':    return ['', ''];
            default:        return ['background:var(--color-bg);', 'color:var(--color-text);'];
        }
    }

    /** SectionShell: padding / bg / align / radius / bg-image + overlay + container. */
    private static function shell(array $s, string $inner): string
    {
        $style = $s['style'] ?? [];
        $padMap = ['none'=>0,'sm'=>28,'md'=>48,'lg'=>72,'xl'=>104];
        $pad = $padMap[$style['paddingY'] ?? 'lg'] ?? 72;

        $bg = $style['bg'] ?? 'default';
        $bgImg = ($bg === 'image') ? self::media($style['bgMedia'] ?? null) : null;
        if ($bg === 'image' && !$bgImg) $bg = 'primary';      // never invisible
        $overlay = isset($style['overlay']) ? (float)$style['overlay'] : ($bgImg ? 0.5 : 0);

        [$bgc, $color] = self::bgCss($bg, $bgImg);
        $align = in_array($style['align'] ?? '', ['center','right'], true) ? $style['align'] : 'left';
        $radius = !empty($style['radius']) ? ('border-radius:' . (['none'=>'0','sm'=>'6px','md'=>'12px','lg'=>'18px','xl'=>'26px'][$style['radius']] ?? '0') . ';') : '';

        $secStyle = 'padding-top:calc(' . $pad . 'px*var(--space-scale));padding-bottom:calc(' . $pad . 'px*var(--space-scale));'
                  . 'text-align:' . $align . ';' . $bgc . $color . $radius;

        $bgLayer = '';
        if ($bgImg) {
            $bgLayer = '<div aria-hidden="true" class="tf-bgimg" style="background-image:url(' . self::esc($bgImg) . ')"></div>'
                     . '<div aria-hidden="true" class="tf-overlay" style="background:rgba(2,6,23,' . $overlay . ')"></div>';
        }

        return '<section id="' . self::esc($s['id'] ?? '') . '" class="tf-section tf-al-' . $align . '" style="' . $secStyle . '">'
             . $bgLayer
             . '<div class="tf-container tf-rel">' . $inner . '</div>'
             . '</section>';
    }

    private static function sectionHeader(?string $label, ?string $heading, ?string $sub, bool $light = false): string
    {
        if (!$label && !$heading && !$sub) return '';
        $h = '<div class="tf-head">';
        if ($label)   $h .= '<p class="tf-eyebrow"' . ($light ? ' style="color:rgba(255,255,255,.8)"' : '') . '>' . self::esc($label) . '</p>';
        if ($heading) $h .= '<h2 class="tf-h2">' . self::esc($heading) . '</h2>';
        if ($sub)     $h .= '<p class="tf-sub"' . ($light ? ' style="color:rgba(255,255,255,.85)"' : '') . '>' . self::esc($sub) . '</p>';
        return $h . '</div>';
    }

    /** CtaButton port. */
    private static function btn(?array $link, bool $onDark = false, string $fallback = 'primary'): string
    {
        if (empty($link['text']) || empty($link['href'])) return '';
        $variant = $link['style'] ?? $fallback ?: 'primary';
        switch ($variant) {
            case 'secondary': $st = 'background:var(--color-accent);color:var(--color-accent-fg);border-radius:var(--radius);'; break;
            case 'ghost': $st = $onDark
                ? 'background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.45);border-radius:var(--radius);'
                : 'background:transparent;color:var(--color-text);border:1px solid var(--color-border);border-radius:var(--radius);'; break;
            case 'link': $st = 'padding:0;color:' . ($onDark ? '#fff' : 'var(--color-primary)') . ';text-decoration:underline;background:none;'; break;
            default: $st = $onDark
                ? 'background:var(--color-accent);color:var(--color-accent-fg);border-radius:var(--radius);'
                : 'background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius);';
        }
        $cls = $variant === 'link' ? 'tf-btn tf-btn-link' : 'tf-btn';
        $tgt = !empty($link['newTab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
        return '<a class="' . $cls . '" href="' . self::esc($link['href']) . '"' . $tgt . ' style="' . $st . '">' . self::esc($link['text']) . '</a>';
    }

    private static function gridClass(string $variant): string
    {
        if ($variant === 'cards-2' || $variant === 'list') return 'tf-grid tf-c2';
        if ($variant === 'cards-4') return 'tf-grid tf-c4';
        return 'tf-grid tf-c3';
    }

    /* --------------------------------------------------------- sections */

    private static function section(array $s, array $doc): string
    {
        switch ($s['type'] ?? '') {
            case 'header':       return self::secHeader($s, $doc);
            case 'hero':         return self::secHero($s, $doc);
            case 'about':        return self::secAbout($s, $doc);
            case 'services':     return self::secServices($s, $doc);
            case 'gallery':      return self::secGallery($s, $doc);
            case 'stats':        return self::secStats($s, $doc);
            case 'team':         return self::secTeam($s, $doc);
            case 'testimonials': return self::secTestimonials($s, $doc);
            case 'faq':          return self::secFaq($s, $doc);
            case 'cta':          return self::secCta($s, $doc);
            case 'contact':      return self::secContact($s, $doc);
            case 'footer':       return self::secFooter($s, $doc);
            default:             return '';
        }
    }

    private static function secHeader(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'left';
        $dark = self::isDarkBg($s);
        $logo = self::media($p['logo'] ?? null);
        $brand = $logo
            ? '<img src="' . self::esc($logo) . '" alt="' . self::esc($doc['site']['name'] ?? '') . '" style="height:36px;width:auto;object-fit:contain">'
            : '<span style="font-size:18px;font-weight:700;font-family:var(--font-heading)">' . self::esc($doc['site']['name'] ?? '') . '</span>';

        // Auto-menu from visible pages when no custom links.
        $custom = array_values(array_filter($p['links'] ?? [], fn($l) => !empty($l['text']) && !empty($l['href'])));
        $items = $custom;
        if (!$items) {
            foreach (($doc['pages'] ?? []) as $pg) {
                if (($pg['visible'] ?? true) === false) continue;
                $items[] = ['text' => $pg['title'] ?? '', 'href' => $pg['slug'] ?? '/'];
            }
        }
        $links = '';
        foreach ($items as $l) $links .= '<a href="' . self::esc($l['href']) . '">' . self::esc($l['text']) . '</a>';

        $cta = self::btn($p['cta'] ?? null, $dark);
        $toggleId = 'nav-' . self::esc($s['id'] ?? 'h');

        [$bgc, $color] = self::bgCss($s['style']['bg'] ?? 'default', null);
        $sticky = ($p['sticky'] ?? true) !== false;

        $bar = '<div class="tf-header-bar">' . $brand
             . '<div class="tf-nav tf-nav-desktop">' . $links . ($cta ? '<span style="margin-left:8px">' . $cta . '</span>' : '') . '</div>'
             . '<label for="' . $toggleId . '" class="tf-burger" aria-label="Menu">&#9776;</label>'
             . '</div>';

        $mnav = '<nav class="tf-nav tf-mnav">' . $links . ($cta ? '<div style="margin-top:8px">' . $cta . '</div>' : '') . '</nav>';

        return '<header class="tf-header" style="' . $bgc . $color . ($sticky ? '' : 'position:static;') . '">'
             . '<input type="checkbox" id="' . $toggleId . '" class="tf-navtoggle" hidden>'
             . '<div class="tf-container">' . $bar . '</div>'
             . '<div class="tf-container">' . $mnav . '</div>'
             . '</header>';
    }

    private static function secHero(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'centered-bg';
        $img = self::media($p['image'] ?? null);
        $biz = $doc['business'] ?? [];
        $onDark = self::isDarkBg($s);

        $body = '';
        if (!empty($p['badge'])) $body .= '<span class="tf-badge">' . self::esc($p['badge']) . '</span>';
        $body .= '<h1 class="tf-h1">' . self::esc($p['heading'] ?? '') . '</h1>';
        if (!empty($p['sub'])) $body .= '<p class="tf-lead">' . self::esc($p['sub']) . '</p>';

        $btns = self::btn($p['ctaPrimary'] ?? null, $onDark) . self::btn($p['ctaSecondary'] ?? null, $onDark, 'ghost');
        if (!empty($p['showWhatsapp']) && !empty($biz['whatsapp'])) $btns .= self::btn(['text'=>'WhatsApp','href'=>'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']),'newTab'=>true,'style'=>'ghost'], $onDark);
        if (!empty($p['showCall']) && !empty($biz['phone'])) $btns .= self::btn(['text'=>'Call now','href'=>'tel:' . $biz['phone'],'style'=>'ghost'], $onDark);
        $body .= '<div class="tf-btns">' . $btns . '</div>';

        if ($variant === 'split' && $img) {
            $inner = '<div class="tf-two" style="text-align:left">'
                   . '<div>' . $body . '</div>'
                   . '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? '') . '" style="width:100%;object-fit:cover;border-radius:var(--radius);max-height:460px">'
                   . '</div>';
            return self::shell($s, $inner);
        }

        // "Centered on background image": the Image field IS the background.
        if ($variant === 'centered-bg' && !empty($p['image'])) {
            $s['style'] = array_merge($s['style'] ?? [], [
                'bg'      => 'image',
                'bgMedia' => $p['image'],
                'overlay' => $s['style']['overlay'] ?? 0.55,
            ]);
        }
        return self::shell($s, '<div class="tf-hero-wrap">' . $body . '</div>');
    }

    private static function secAbout(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'image-left';
        $img = self::media($p['image'] ?? null);
        $hasImage = $variant !== 'text-only' && $img;

        $text = '<div>' . self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, null);
        if (!empty($p['body'])) $text .= '<p class="tf-body" style="white-space:pre-line;color:var(--color-muted)">' . self::esc($p['body']) . '</p>';
        if (!empty($p['showPillars']) && !empty($p['pillars'])) {
            $text .= '<div class="tf-grid tf-c2" style="margin-top:32px">';
            foreach ($p['pillars'] as $pl) {
                $text .= '<div class="tf-card" style="padding:20px"><h3 style="font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pl['title'] ?? '') . '</h3>'
                       . (!empty($pl['text']) ? '<p style="margin-top:8px;font-size:14px;color:var(--color-muted)">' . self::esc($pl['text']) . '</p>' : '') . '</div>';
            }
            $text .= '</div>';
        }
        $text .= '</div>';

        if (!$hasImage) return self::shell($s, '<div style="max-width:768px;margin:0 auto">' . $text . '</div>');

        $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'About') . '" loading="lazy" style="width:100%;object-fit:cover;border-radius:var(--radius);max-height:460px">';
        $cols = $variant === 'image-left' ? ($image . $text) : ($text . $image);
        return self::shell($s, '<div class="tf-two" style="text-align:left">' . $cols . '</div>');
    }

    private static function secServices(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $variant = $s['variant'] ?? 'cards-3';
        $showImages = $variant !== 'list';

        $cards = '';
        foreach ($items as $it) {
            $img = self::media($it['image'] ?? null);
            $cards .= '<div class="tf-card">';
            if ($showImages && $img) $cards .= '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy" style="height:176px;width:100%;object-fit:cover">';
            $cards .= '<div style="padding:20px">';
            $cards .= '<h3 style="font-family:var(--font-heading);font-size:18px;font-weight:600">' . self::esc($it['title'] ?? '') . '</h3>';
            if (!empty($it['meta'])) $cards .= '<p style="margin-top:4px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($it['meta']) . '</p>';
            if (!empty($it['desc'])) $cards .= '<p style="margin-top:8px;font-size:14px;color:var(--color-muted)">' . self::esc($it['desc']) . '</p>';
            if (!empty($it['cta']['text'])) { $l = $it['cta']; $l['style'] = $l['style'] ?? 'link'; $cards .= '<div style="margin-top:16px">' . self::btn($l) . '</div>'; }
            $cards .= '</div></div>';
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
               . '<div class="' . self::gridClass($variant) . '" style="text-align:left">' . $cards . '</div>';
        return self::shell($s, $inner);
    }

    private static function secGallery(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'grid-3';
        $imgs = array_values(array_filter($p['images'] ?? [], fn($i) => self::media($i['image'] ?? null)));
        if (!$imgs) return '';
        $g = $variant === 'grid-4' ? 'g4' : ($variant === 'grid-3' ? 'g3' : 'g3');
        $lightbox = ($p['lightbox'] ?? true) !== false;

        $cells = '';
        foreach ($imgs as $im) {
            $src = self::media($im['image']);
            $fig = '<figure class="tf-galfig"><img src="' . self::esc($src) . '" alt="' . self::esc($im['alt'] ?? '') . '" loading="lazy">'
                 . (!empty($im['alt']) ? '<figcaption>' . self::esc($im['alt']) . '</figcaption>' : '') . '</figure>';
            $cells .= $lightbox ? '<a href="' . self::esc($src) . '" target="_blank" rel="noopener noreferrer">' . $fig . '</a>' : $fig;
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
               . '<div class="tf-gal ' . $g . '">' . $cells . '</div>';
        return self::shell($s, $inner);
    }

    private static function secStats(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $onDark = self::isDarkBg($s);
        $cells = '';
        foreach ($items as $it) {
            $val = number_format((float)($it['value'] ?? 0));
            $suffixColor = $onDark ? 'inherit' : 'var(--color-accent)';
            $cells .= '<div style="text-align:center">'
                    . '<div style="font-family:var(--font-heading);font-size:34px;font-weight:700">' . self::esc($val)
                    . '<span style="color:' . $suffixColor . '">' . self::esc($it['suffix'] ?? '') . '</span></div>'
                    . '<p style="margin-top:4px;font-size:14px;' . ($onDark ? 'opacity:.85' : 'color:var(--color-muted)') . '">' . self::esc($it['label'] ?? '') . '</p>'
                    . '</div>';
        }
        $n = count($items);
        $cls = $n >= 4 ? 'tf-c4' : ($n === 3 ? 'tf-c3' : 'tf-c2');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, $onDark)
               . '<div class="tf-grid ' . $cls . '">' . $cells . '</div>';
        return self::shell($s, $inner);
    }

    private static function secTeam(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $variant = $s['variant'] ?? 'cards-3';
        $round = $variant === 'circles';
        $cls = $variant === 'cards-4' ? 'tf-c4' : 'tf-c3';

        $cards = '';
        foreach ($items as $pr) {
            $img = self::media($pr['photo'] ?? null);
            $cards .= '<div class="tf-card" style="padding:24px;text-align:center">';
            if ($img) {
                $st = $round ? 'margin:0 auto;height:112px;width:112px;object-fit:cover;border-radius:999px' : 'margin:0 auto;height:160px;width:100%;object-fit:cover;border-radius:var(--radius)';
                $cards .= '<img src="' . self::esc($img) . '" alt="' . self::esc($pr['name'] ?? '') . '" loading="lazy" style="' . $st . '">';
            } else {
                $initial = function_exists('mb_substr') ? mb_substr($pr['name'] ?? '?', 0, 1, 'UTF-8') : substr($pr['name'] ?? '?', 0, 1);
                $cards .= '<div aria-hidden="true" style="margin:0 auto;height:112px;width:112px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;border-radius:999px;background:var(--color-surface);color:var(--color-muted)">' . self::esc($initial) . '</div>';
            }
            $cards .= '<h3 style="margin-top:16px;font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pr['name'] ?? '') . '</h3>';
            if (!empty($pr['role'])) $cards .= '<p style="margin-top:2px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($pr['role']) . '</p>';
            if (!empty($pr['meta'])) $cards .= '<p style="margin-top:8px;display:inline-block;padding:4px 12px;font-size:12px;background:var(--color-surface);color:var(--color-muted);border-radius:999px">' . self::esc($pr['meta']) . '</p>';
            if (!empty($pr['bio'])) $cards .= '<p style="margin-top:12px;font-size:14px;color:var(--color-muted)">' . self::esc($pr['bio']) . '</p>';
            if (!empty($pr['link']['text'])) { $l = $pr['link']; $l['style'] = $l['style'] ?? 'link'; $cards .= '<div style="margin-top:12px">' . self::btn($l) . '</div>'; }
            $cards .= '</div>';
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
               . '<div class="tf-grid ' . $cls . '">' . $cards . '</div>';
        return self::shell($s, $inner);
    }

    private static function secTestimonials(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $variant = $s['variant'] ?? 'cards-3';

        $stars = function ($n) {
            $full = max(0, min(5, (int)round($n ?: 5)));
            return '<div style="color:var(--color-accent);font-size:14px">' . str_repeat('★', $full) . '<span style="opacity:.25">' . str_repeat('★', 5 - $full) . '</span></div>';
        };

        if ($variant === 'single') {
            $r = $items[0];
            $photo = self::media($r['photo'] ?? null);
            $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
                   . '<figure style="max-width:768px;margin:0 auto">'
                   . '<blockquote style="font-family:var(--font-heading);font-size:22px;line-height:1.5;margin:0">“' . self::esc($r['quote'] ?? '') . '”</blockquote>'
                   . '<figcaption style="margin-top:20px;display:flex;align-items:center;justify-content:center;gap:12px">'
                   . ($photo ? '<img src="' . self::esc($photo) . '" alt="' . self::esc($r['name'] ?? '') . '" loading="lazy" style="height:44px;width:44px;border-radius:999px;object-fit:cover">' : '')
                   . '<span><span style="display:block;font-size:14px;font-weight:600">' . self::esc($r['name'] ?? '') . '</span>'
                   . (!empty($r['role']) ? '<span style="display:block;font-size:12px;color:var(--color-muted)">' . self::esc($r['role']) . '</span>' : '') . '</span>'
                   . '</figcaption></figure>';
            return self::shell($s, $inner);
        }

        $cards = '';
        foreach ($items as $r) {
            $photo = self::media($r['photo'] ?? null);
            $cards .= '<div class="tf-card" style="padding:24px;text-align:left">' . $stars($r['rating'] ?? 5)
                    . '<blockquote style="margin:12px 0 0;font-size:14px;color:var(--color-muted)">“' . self::esc($r['quote'] ?? '') . '”</blockquote>'
                    . '<div style="margin-top:20px;display:flex;align-items:center;gap:12px">'
                    . ($photo ? '<img src="' . self::esc($photo) . '" alt="' . self::esc($r['name'] ?? '') . '" loading="lazy" style="height:40px;width:40px;border-radius:999px;object-fit:cover">' : '')
                    . '<div><p style="font-size:14px;font-weight:600;margin:0">' . self::esc($r['name'] ?? '') . '</p>'
                    . (!empty($r['role']) ? '<p style="font-size:12px;color:var(--color-muted);margin:0">' . self::esc($r['role']) . '</p>' : '') . '</div>'
                    . '</div></div>';
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
               . '<div class="tf-grid tf-c3">' . $cards . '</div>';
        return self::shell($s, $inner);
    }

    private static function secFaq(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = array_values(array_filter($p['items'] ?? [], fn($i) => !empty($i['q'])));
        if (!$items) return '';
        $two = ($s['variant'] ?? '') === 'two-column';
        $openFirst = ($p['openFirst'] ?? true) !== false;

        $rows = '';
        foreach ($items as $i => $it) {
            $open = ($openFirst && $i === 0) ? ' open' : '';
            $rows .= '<details class="tf-faqitem"' . $open . ' style="background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);text-align:left">'
                   . '<summary style="font-family:var(--font-heading);padding:16px 20px;font-size:14px;font-weight:600"><span style="display:flex;justify-content:space-between;gap:16px">' . self::esc($it['q']) . '<span class="tf-faq-plus" aria-hidden="true" style="color:var(--color-accent)">+</span></span></summary>'
                   . (!empty($it['a']) ? '<p style="white-space:pre-line;padding:0 20px 16px;font-size:14px;color:var(--color-muted)">' . self::esc($it['a']) . '</p>' : '')
                   . '</details>';
        }
        $wrap = $two ? '<div class="tf-grid tf-c2 tf-faq">' : '<div class="tf-faq" style="max-width:768px;margin:0 auto;display:grid;gap:12px">';
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $wrap . $rows . '</div>';
        return self::shell($s, $inner);
    }

    private static function secCta(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $variant = $s['variant'] ?? 'banner';
        $onDark = self::isDarkBg($s) || $variant === 'card';

        $btns = self::btn($p['ctaPrimary'] ?? null, $onDark) . self::btn($p['ctaSecondary'] ?? null, $onDark, 'ghost');
        if (!empty($p['showWhatsapp']) && !empty($biz['whatsapp'])) $btns .= self::btn(['text'=>'WhatsApp','href'=>'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']),'newTab'=>true,'style'=>'ghost'], $onDark);
        if (!empty($p['showCall']) && !empty($biz['phone'])) $btns .= self::btn(['text'=>'Call now','href'=>'tel:' . $biz['phone'],'style'=>'ghost'], $onDark);

        $text = '<div>' . (!empty($p['heading']) ? '<h2 class="tf-h2">' . self::esc($p['heading']) . '</h2>' : '')
              . (!empty($p['sub']) ? '<p class="tf-lead">' . self::esc($p['sub']) . '</p>' : '') . '</div>';

        if ($variant === 'split') {
            return self::shell($s, '<div class="tf-two" style="text-align:left">' . $text . '<div class="tf-btns" style="justify-content:flex-end">' . $btns . '</div></div>');
        }
        $inner = '<div style="max-width:640px;margin:0 auto">' . $text . '<div class="tf-btns" style="margin-top:24px">' . $btns . '</div></div>';
        if ($variant === 'card') {
            return self::shell($s, '<div style="max-width:768px;margin:0 auto;padding:32px;background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius)">' . $inner . '</div>');
        }
        return self::shell($s, $inner);
    }

    private static function secContact(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $variant = $s['variant'] ?? 'form-map';

        // The form declared for this section (or the first one).
        $form = null;
        foreach (($doc['forms'] ?? []) as $f) {
            if (($f['id'] ?? null) === ($p['formId'] ?? null)) { $form = $f; break; }
        }
        if (!$form && !empty($doc['forms'])) $form = $doc['forms'][0];

        // Contact detail rows.
        $rows = '';
        $add = function ($label, $val, $href = null) {
            $v = $href ? '<a href="' . self::esc($href) . '" style="font-size:14px;color:var(--color-text)">' . self::esc($val) . '</a>'
                       : '<p style="font-size:14px;color:var(--color-muted);margin:0">' . self::esc($val) . '</p>';
            return '<div><p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-accent);margin:0">' . self::esc($label) . '</p>' . $v . '</div>';
        };
        if (($p['showPhone'] ?? true) !== false && !empty($biz['phone'])) $rows .= $add('Phone', $biz['phone'], 'tel:' . $biz['phone']);
        if (($p['showWhatsapp'] ?? true) !== false && !empty($biz['whatsapp'])) $rows .= $add('WhatsApp', $biz['whatsapp'], 'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']));
        if (($p['showEmail'] ?? true) !== false && !empty($biz['email'])) $rows .= $add('Email', $biz['email'], 'mailto:' . $biz['email']);
        if (($p['showAddress'] ?? true) !== false && !empty($biz['address'])) $rows .= $add('Address', $biz['address']);
        $details = '<div style="display:grid;gap:16px;text-align:left">' . $rows . '</div>';

        // Form.
        $formEl = '';
        if ($form) {
            $api = defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in';
            $sent = $_GET['sent'] ?? null;
            $banner = '';
            if ($sent === '1' || $sent === '0') {
                $ok = $sent === '1';
                $msg = $ok ? ($form['successMessage'] ?? 'Thank you — your message has been sent.') : 'Sorry, your message could not be sent. Please try again.';
                $banner = '<div role="status" style="margin-bottom:16px;padding:12px 16px;font-size:14px;border-radius:var(--radius);border:1px solid ' . ($ok ? '#86efac' : '#fca5a5') . ';background:' . ($ok ? '#f0fdf4' : '#fef2f2') . ';color:' . ($ok ? '#166534' : '#991b1b') . '">' . self::esc($msg) . '</div>';
            }
            $fields = '';
            foreach (($form['fields'] ?? []) as $f) {
                $name = $f['name'] ?? ''; if ($name === '') continue;
                $req = !empty($f['required']) ? ' required' : '';
                $lbl = self::esc($f['label'] ?? $name) . (!empty($f['required']) ? ' *' : '');
                $ph = self::esc($f['placeholder'] ?? '');
                $inputStyle = 'width:100%;padding:8px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg)';
                if (($f['type'] ?? '') === 'textarea') {
                    $ctrl = '<textarea id="' . self::esc($name) . '" name="' . self::esc($name) . '"' . $req . ' placeholder="' . $ph . '" rows="4" style="' . $inputStyle . '"></textarea>';
                } elseif (($f['type'] ?? '') === 'select') {
                    $opts = '';
                    foreach (($f['options'] ?? []) as $o) $opts .= '<option value="' . self::esc($o) . '">' . self::esc($o) . '</option>';
                    $ctrl = '<select id="' . self::esc($name) . '" name="' . self::esc($name) . '"' . $req . ' style="' . $inputStyle . '">' . $opts . '</select>';
                } else {
                    $type = self::esc($f['type'] ?? 'text');
                    $ctrl = '<input id="' . self::esc($name) . '" name="' . self::esc($name) . '" type="' . $type . '"' . $req . ' placeholder="' . $ph . '" style="' . $inputStyle . '">';
                }
                $fields .= '<div><label for="' . self::esc($name) . '" style="display:block;margin-bottom:4px;font-size:12px;font-weight:600">' . $lbl . '</label>' . $ctrl . '</div>';
            }
            $formEl = '<form method="post" action="' . self::esc($api) . '/api/sites/form-submit.php" style="display:grid;gap:12px;text-align:left">'
                    . $banner
                    . '<input type="hidden" name="form_id" value="' . self::esc($form['id'] ?? '') . '">'
                    . '<input type="hidden" name="site" value="' . self::esc(self::$slug) . '">'
                    . '<div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"><label for="' . self::esc($form['id'] ?? 'f') . '-hp">Leave empty</label><input id="' . self::esc($form['id'] ?? 'f') . '-hp" type="text" name="_hp" tabindex="-1" autocomplete="off"></div>'
                    . $fields
                    . '<button type="submit" class="tf-btn" style="background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius)">' . self::esc($form['submitText'] ?? 'Send') . '</button>'
                    . '</form>';
        }

        // Map.
        $mapSrc = null;
        if (($p['showMap'] ?? true) !== false && in_array($variant, ['form-map', 'details-map'], true)) {
            $q = trim((string)($p['mapUrl'] ?? $biz['mapUrl'] ?? $biz['address'] ?? ''));
            if ($q !== '') $mapSrc = preg_match('#/maps/embed|output=embed#', $q) ? $q : ('https://www.google.com/maps?q=' . rawurlencode($q) . '&output=embed');
        }

        $header = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null);

        if ($variant === 'cards') {
            $cards = '';
            // reuse detail rows as cards
            $cards = '<div class="tf-grid tf-c4">' . $rows . '</div>';
            return self::shell($s, $header . $cards);
        }

        $left = ($variant === 'form-map' && $formEl) ? ($formEl . '<div style="margin-top:24px">' . $details . '</div>') : ($formEl ?: $details);
        $map = $mapSrc ? '<iframe src="' . self::esc($mapSrc) . '" title="Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="border:0;border-radius:var(--radius);min-height:320px;height:100%;width:100%"></iframe>' : '';
        $grid = $mapSrc ? ('<div class="tf-two" style="align-items:stretch">' . '<div>' . $left . '</div>' . $map . '</div>') : ('<div style="max-width:640px;margin:0 auto">' . $left . '</div>');
        return self::shell($s, $header . $grid);
    }

    private static function secFooter(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $simple = ($s['variant'] ?? '') === 'simple';
        $logo = self::media($p['logo'] ?? null);
        $socialLabels = ['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','linkedin'=>'LinkedIn','twitter'=>'X','telegram'=>'Telegram'];

        $brand = '<div>';
        $brand .= $logo ? '<img src="' . self::esc($logo) . '" alt="' . self::esc($doc['site']['name'] ?? '') . '" style="height:40px;width:auto;object-fit:contain">'
                        : '<p style="font-size:18px;font-weight:700;font-family:var(--font-heading);margin:0">' . self::esc($doc['site']['name'] ?? '') . '</p>';
        if (!empty($p['blurb'])) $brand .= '<p style="margin-top:12px;max-width:24rem;font-size:14px;opacity:.75">' . self::esc($p['blurb']) . '</p>';
        if (($p['showSocial'] ?? true) !== false && !empty($biz['social'])) {
            $soc = '';
            foreach ($biz['social'] as $k => $url) {
                if (!$url) continue;
                $soc .= '<a href="' . self::esc($url) . '" target="_blank" rel="noopener noreferrer" style="font-size:12px;opacity:.75;text-decoration:none">' . self::esc($socialLabels[$k] ?? $k) . '</a>';
            }
            if ($soc) $brand .= '<div style="margin-top:16px;display:flex;flex-wrap:wrap;gap:12px">' . $soc . '</div>';
        }
        $brand .= '</div>';

        $body = $simple ? ('<div style="text-align:center">' . $brand . '</div>') : ('<div class="tf-grid tf-c4" style="text-align:left">' . $brand);
        if (!$simple) {
            foreach (($p['columns'] ?? []) as $col) {
                $links = '';
                foreach (($col['links'] ?? []) as $l) $links .= '<li style="margin-bottom:6px"><a href="' . self::esc($l['href'] ?? '#') . '" style="text-decoration:none;opacity:.75">' . self::esc($l['text'] ?? '') . '</a></li>';
                $body .= '<div><p style="font-size:14px;font-weight:600;margin:0 0 12px">' . self::esc($col['title'] ?? '') . '</p><ul style="list-style:none;padding:0;margin:0;font-size:14px">' . $links . '</ul></div>';
            }
            if (($p['showContact'] ?? true) !== false && (!empty($biz['phone']) || !empty($biz['email']) || !empty($biz['address']))) {
                $ci = '';
                if (!empty($biz['phone'])) $ci .= '<li style="margin-bottom:6px"><a href="tel:' . self::esc($biz['phone']) . '" style="text-decoration:none">' . self::esc($biz['phone']) . '</a></li>';
                if (!empty($biz['email'])) $ci .= '<li style="margin-bottom:6px"><a href="mailto:' . self::esc($biz['email']) . '" style="text-decoration:none">' . self::esc($biz['email']) . '</a></li>';
                if (!empty($biz['address'])) $ci .= '<li>' . self::esc($biz['address']) . '</li>';
                $body .= '<div><p style="font-size:14px;font-weight:600;margin:0 0 12px">Contact</p><ul style="list-style:none;padding:0;margin:0;font-size:14px;opacity:.75">' . $ci . '</ul></div>';
            }
            $body .= '</div>';
        }

        $year = date('Y');
        $copy = trim((string)($p['copyright'] ?? '')) !== '' ? $p['copyright'] : ('© ' . $year . ' ' . ($doc['site']['name'] ?? '') . '. All rights reserved.');
        $bottom = '<div style="margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.15);display:flex;flex-direction:column;gap:8px;justify-content:space-between;align-items:center;font-size:12px;opacity:.7" class="tf-foot-bottom">'
                . '<p style="margin:0">' . self::esc($copy) . '</p>'
                . (($p['showBranding'] ?? true) !== false ? '<p style="margin:0">Powered by <a href="https://tapify.co.in" target="_blank" rel="noopener noreferrer" style="text-decoration:underline">Tapify</a></p>' : '')
                . '</div>';

        return self::shell($s, $body . $bottom);
    }

    /* --------------------------------------------------------- base css */

    private static function baseCss(): string
    {
        return <<<CSS
*,*::before,*::after{box-sizing:border-box}
body{margin:0}
img{max-width:100%;display:block}
.tf-site{font-family:var(--font-body);color:var(--color-text);background:var(--color-bg);line-height:1.6;-webkit-font-smoothing:antialiased}
.tf-container{max-width:var(--container);margin:0 auto;padding:0 20px;width:100%}
@media(min-width:768px){.tf-container{padding:0 32px}}
.tf-section{position:relative;overflow:hidden;width:100%}
.tf-rel{position:relative;z-index:1}
.tf-bgimg{position:absolute;inset:0;background-size:cover;background-position:center}
.tf-overlay{position:absolute;inset:0}
h1,h2,h3{font-family:var(--font-heading);line-height:1.15;margin:0}
p{margin:0}
a{color:inherit}
.tf-h1{font-size:34px;font-weight:700;line-height:1.1}
@media(min-width:768px){.tf-h1{font-size:48px}}
@media(min-width:1024px){.tf-h1{font-size:60px}}
.tf-h2{font-size:30px;font-weight:700}
@media(min-width:768px){.tf-h2{font-size:36px}}
.tf-lead{margin-top:16px;font-size:16px;line-height:1.6;opacity:.9;max-width:640px}
@media(min-width:768px){.tf-lead{font-size:18px}}
.tf-hero-wrap{max-width:768px}
.tf-al-center .tf-hero-wrap,.tf-al-center .tf-sub,.tf-al-center .tf-lead{margin-left:auto;margin-right:auto}
.tf-al-center .tf-btns{justify-content:center}
.tf-al-right .tf-hero-wrap,.tf-al-right .tf-sub,.tf-al-right .tf-lead{margin-left:auto}
.tf-al-right .tf-btns{justify-content:flex-end}
.tf-head{margin-bottom:40px}
.tf-eyebrow{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.14em;color:var(--color-accent);margin:0 0 8px}
.tf-sub{margin-top:12px;font-size:16px;line-height:1.6;color:var(--color-muted);max-width:640px}
.tf-badge{display:inline-flex;align-items:center;gap:8px;margin-bottom:20px;padding:8px 16px;font-size:12px;font-weight:600;background:var(--color-accent);color:var(--color-accent-fg);border-radius:999px}
.tf-btns{margin-top:32px;display:flex;flex-wrap:wrap;gap:12px}
.tf-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 24px;font-size:14px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:transform .2s}
.tf-btn:hover{transform:translateY(-2px)}
.tf-btn-link:hover{transform:none}
.tf-card{height:100%;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);overflow:hidden;box-shadow:0 1px 2px rgba(16,24,40,.04),0 8px 24px rgba(16,24,40,.06)}
.tf-grid{display:grid;gap:24px}
.tf-c2,.tf-c3{grid-template-columns:1fr}
.tf-c4{grid-template-columns:1fr 1fr}
@media(min-width:640px){.tf-c2{grid-template-columns:1fr 1fr}.tf-c3{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.tf-c3{grid-template-columns:repeat(3,1fr)}.tf-c4{grid-template-columns:repeat(4,1fr)}}
.tf-two{display:grid;gap:40px;grid-template-columns:1fr;align-items:center}
@media(min-width:1024px){.tf-two{grid-template-columns:1fr 1fr}}
.tf-gal{display:grid;gap:16px;grid-template-columns:1fr 1fr}
@media(min-width:768px){.tf-gal.g3{grid-template-columns:repeat(3,1fr)}.tf-gal.g4{grid-template-columns:repeat(4,1fr)}}
.tf-galfig{position:relative;margin:0;overflow:hidden;border-radius:var(--radius)}
.tf-galfig img{height:208px;width:100%;object-fit:cover}
.tf-galfig figcaption{position:absolute;left:0;right:0;bottom:0;padding:12px;font-size:12px;color:#fff;text-align:left;background:linear-gradient(to top,rgba(0,0,0,.7),transparent)}
.tf-header{position:sticky;top:0;z-index:40;border-bottom:1px solid rgba(120,120,120,.18)}
.tf-header-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 0}
.tf-nav{display:flex;align-items:center;gap:24px}
.tf-nav a{font-size:14px;font-weight:500;opacity:.85;text-decoration:none}
.tf-nav a:hover{opacity:1}
.tf-burger{display:none;font-size:22px;line-height:1;cursor:pointer;padding:4px 8px;border:1px solid currentColor;border-radius:8px}
.tf-mnav{display:none}
@media(max-width:768px){
  .tf-nav-desktop{display:none}
  .tf-burger{display:inline-flex;align-items:center}
  .tf-mnav{flex-direction:column;align-items:stretch;gap:4px;padding:8px 0 12px;border-top:1px solid rgba(120,120,120,.18)}
  .tf-mnav a{padding:8px 4px;border-radius:8px}
  .tf-navtoggle:checked ~ .tf-container .tf-mnav{display:flex}
}
.tf-faqitem summary{cursor:pointer;list-style:none}
.tf-faqitem summary::-webkit-details-marker{display:none}
.tf-faqitem[open] .tf-faq-plus{transform:rotate(45deg)}
.tf-faq-plus{transition:transform .2s;display:inline-block}
@media(min-width:640px){.tf-foot-bottom{flex-direction:row!important}}
iframe{max-width:100%}
CSS;
    }
}
