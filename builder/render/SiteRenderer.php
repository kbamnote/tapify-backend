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

        // Legal pages: /privacy and /terms. Content lives in the footer section.
        if ($norm === '/privacy' || $norm === '/terms') {
            $kind = $norm === '/privacy' ? 'privacy' : 'terms';
            [$title, $body] = self::legalContent($doc, $kind);
            if (trim($body) !== '') {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderLegal($doc, $title, $body, $norm);
                return true;
            }
            self::notFound();
            return true;
        }

        // Blog post detail: /post/<slug>. Posts live inside blog sections (not in
        // doc.pages), so this renders a single post with the site's header/footer.
        if (preg_match('#^/post/([a-z0-9][a-z0-9-]*)$#', $norm, $pm)) {
            $posts = self::allPosts($doc);
            if (isset($posts[$pm[1]])) {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderPostDetail($doc, $posts[$pm[1]]);
                return true;
            }
            self::notFound();
            return true;
        }

        // Product/service detail: /service/<slug> — photo gallery + description.
        if (preg_match('#^/service/([a-z0-9][a-z0-9-]*)$#', $norm, $sm)) {
            $services = self::allServices($doc);
            if (isset($services[$sm[1]])) {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderServiceDetail($doc, $services[$sm[1]]);
                return true;
            }
            self::notFound();
            return true;
        }

        $page = null;
        foreach ($doc['pages'] as $p) {
            if (($p['slug'] ?? '') === $norm) { $page = $p; break; }
        }
        // Built-in cart page. Looked up after doc.pages so a site that builds its
        // own /cart keeps it.
        if (!$page && $norm === '/cart') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderCartPage($doc);
            return true;
        }
        // Built-in login / signup page. This is where the header's Login/Signup
        // button points; after doc.pages so a site that builds its own /account
        // keeps it.
        if (!$page && $norm === '/account') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderAccountPage($doc);
            return true;
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
             . self::carouselScript()
             . self::cartScript()
             . self::animScript()
             . self::countScript()
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

    /** object-fit/position for the "Image fit" option (avoids cropping faces). */
    /**
     * How a photo sits inside its frame — the section's visual crop.
     *
     * Stored either as a legacy string ("cover"/"top"/"contain") from before the
     * cropper existed, or as {fit,x,y,zoom} written by the builder's crop tool.
     * Zoom needs a transform, which paints outside the element box, so the paint
     * is clipped back to that box (following $radius) to keep it off the layout.
     */
    private static function imgFit($fit, string $radius = '0'): string
    {
        if (!is_array($fit)) {
            if ($fit === 'contain') return 'object-fit:contain;object-position:center;';
            if ($fit === 'top')     return 'object-fit:cover;object-position:top;';
            return 'object-fit:cover;object-position:center;';
        }
        if (($fit['fit'] ?? 'cover') === 'contain') {
            return 'object-fit:contain;object-position:center;';
        }
        $x = round(min(100, max(0, (float)($fit['x'] ?? 50))), 2);
        $y = round(min(100, max(0, (float)($fit['y'] ?? 50))), 2);
        $z = round(min(4, max(1, (float)($fit['zoom'] ?? 1))), 3);
        $css = 'object-fit:cover;object-position:' . $x . '% ' . $y . '%;';
        if ($z > 1.001) {
            $css .= 'transform:scale(' . $z . ');transform-origin:' . $x . '% ' . $y . '%;'
                  . 'clip-path:inset(0' . ($radius !== '0' ? ' round ' . $radius : '') . ');';
        }
        return $css;
    }

    /** A colour we are willing to drop straight into a style attribute. */
    private static function isColor($v): bool
    {
        return is_string($v) && preg_match('/^#[0-9a-f]{3,8}$/i', $v) === 1;
    }

    /** SectionShell: padding / bg / align / radius + bg-image + overlay + container. */
    /** @param string $backdrop full-bleed layer behind the content (e.g. a hero video) */
    private static function shell(array $s, string $inner, string $extraStyle = '', string $backdrop = ''): string
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

        // Per-section font colours. Emitted as custom properties so every heading
        // and paragraph inside the section picks them up, including the ones that
        // set their own colour inline.
        $vars = '';
        if (self::isColor($style['headingColor'] ?? null)) $vars .= '--tf-heading:' . $style['headingColor'] . ';';
        if (self::isColor($style['textColor'] ?? null))    $vars .= '--tf-text:' . $style['textColor'] . ';';

        $secStyle = 'padding-top:calc(' . $pad . 'px*var(--space-scale));padding-bottom:calc(' . $pad . 'px*var(--space-scale));'
                  . 'text-align:' . $align . ';' . $bgc . $color . $radius . $vars;

        $bgLayer = '';
        if ($bgImg) {
            $bgLayer = '<div aria-hidden="true" class="tf-bgimg" style="background-image:url(' . self::esc($bgImg) . ')"></div>'
                     . '<div aria-hidden="true" class="tf-overlay" style="background:rgba(2,6,23,' . $overlay . ')"></div>';
        }

        $anim = (!empty($style['animation']) && $style['animation'] !== 'none')
            ? ' data-anim="' . self::esc($style['animation']) . '"' : '';

        return '<section id="' . self::esc($s['id'] ?? '') . '" class="tf-section tf-al-' . $align . '" style="' . $secStyle . $extraStyle . '"' . $anim . '>'
             . $bgLayer
             . $backdrop
             . '<div class="tf-container tf-rel">' . $inner . '</div>'
             . '</section>';
    }

    private static function sectionHeader(?string $label, ?string $heading, ?string $sub, bool $light = false): string
    {
        if (!$label && !$heading && !$sub) return '';
        $h = '<div class="tf-head">';
        if ($label)   $h .= '<p class="tf-eyebrow"' . ($light ? ' style="color:var(--tf-text,rgba(255,255,255,.8))"' : '') . '>' . self::esc($label) . '</p>';
        if ($heading) $h .= '<h2 class="tf-h2">' . self::esc($heading) . '</h2>';
        if ($sub)     $h .= '<p class="tf-sub"' . ($light ? ' style="color:var(--tf-text,rgba(255,255,255,.85))"' : '') . '>' . self::esc($sub) . '</p>';
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

    /** A real carousel — arrows, dots, autoplay, swipe (wired by the page script). */
    private static function carousel(array $slidesHtml, int $autoplay = 4000): string
    {
        if (!$slidesHtml) return '';
        $slides = '';
        foreach ($slidesHtml as $sl) $slides .= '<div class="tf-cslide">' . $sl . '</div>';
        $multi = count($slidesHtml) > 1;
        $arrows = $multi
            ? '<button type="button" class="tf-cprev" aria-label="Previous">&#8249;</button>'
              . '<button type="button" class="tf-cnext" aria-label="Next">&#8250;</button>'
            : '';
        $dots = $multi ? '<div class="tf-cdots"></div>' : '';
        return '<div class="tf-carousel" data-autoplay="' . (int)$autoplay . '">'
             . '<div class="tf-cviewport"><div class="tf-ctrack">' . $slides . '</div>' . $arrows . '</div>'
             . $dots
             . '</div>';
    }

    /** A marquee — cards scroll continuously in a loop. Pure CSS, pauses on hover. */
    private static function marquee(array $slidesHtml, int $secsPerSlide = 3): string
    {
        if (!$slidesHtml) return '';
        $one = '';
        foreach ($slidesHtml as $sl) $one .= '<div class="tf-mqslide">' . $sl . '</div>';
        $dur = max(10, count($slidesHtml) * $secsPerSlide);
        // The set is duplicated so translateX(-50%) loops seamlessly.
        return '<div class="tf-marquee">'
             . '<div class="tf-mqtrack" style="animation-duration:' . $dur . 's">' . $one . $one . '</div>'
             . '</div>';
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
            case 'hours':        return self::secHours($s, $doc);
            case 'blog':         return self::secBlog($s, $doc);
            case 'appointment':  return self::secAppointment($s, $doc);
            case 'embed':        return self::secEmbed($s, $doc);
            case 'share':        return self::secShare($s, $doc);
            case 'account':      return self::secAccount($s, $doc);
            default:             return '';
        }
    }

    private static function secHeader(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'left';
        $dark = self::isDarkBg($s);
        $logo = self::media($p['logo'] ?? null);
        $logoPx = ['small'=>28,'medium'=>36,'large'=>48,'extra-large'=>64][$p['logoSize'] ?? 'medium'] ?? 36;
        $brandInner = $logo
            ? '<img src="' . self::esc($logo) . '" alt="' . self::esc($doc['site']['name'] ?? '') . '" style="height:' . $logoPx . 'px;width:auto;object-fit:contain;display:block">'
            : '<span style="font-size:18px;font-weight:700;font-family:var(--font-heading)">' . self::esc($doc['site']['name'] ?? '') . '</span>';
        // The logo always links back to the home page.
        $brand = '<a href="/" aria-label="' . self::esc($doc['site']['name'] ?? '') . ' home" style="display:inline-flex;align-items:center;color:inherit;text-decoration:none">' . $brandInner . '</a>';

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

        // Optional cart icon button.
        $cartIcon = '';
        if (!empty($p['showCart'])) {
            $cartIcon = '<a href="' . self::esc($p['cartHref'] ?? '/cart') . '" aria-label="Cart" style="position:relative;display:inline-flex;align-items:center;justify-content:center;padding:8px;border-radius:6px;color:inherit;text-decoration:none;border:1px solid rgba(120,120,120,.28)">'
                  . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>'
                  // Filled in by cartScript() and hidden while the cart is empty.
                  . '<span data-tf-cart-count style="display:none;position:absolute;top:-7px;right:-7px;min-width:19px;height:19px;padding:0 5px;'
                  . 'border-radius:999px;background:var(--color-primary);color:var(--color-primary-fg);font-size:11px;font-weight:700;line-height:19px;text-align:center">0</span>'
                  . '</a>';
        }

        // Commerce group: when the account system is on, the cart is gated behind
        // login — a "Login / Signup" button shows when signed out, and the cart +
        // account icon appear once signed in (toggled by the auth script from the
        // customer token). When accounts are off, the cart shows ungated.
        if (!empty($p['showAccount'])) {
            $accHref     = self::esc($p['accountHref'] ?? '/account');
            $accountIcon = '<a href="' . $accHref . '" aria-label="My account" style="display:inline-flex;align-items:center;justify-content:center;padding:8px;border-radius:6px;color:inherit;text-decoration:none;border:1px solid rgba(120,120,120,.28)">'
                . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>';
            $loginBtn    = '<a href="' . $accHref . '" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:var(--radius);font-size:14px;font-weight:600;color:var(--color-primary-fg);background:var(--color-primary);text-decoration:none">'
                . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Login / Signup</a>';
            $cart = '<span data-tf-auth="out" style="display:inline-flex">' . $loginBtn . '</span>'
                  . '<span data-tf-auth="in" style="display:none;align-items:center;gap:12px">' . $accountIcon . $cartIcon . '</span>';
        } else {
            $cart = $cartIcon;
        }
        $burger    = '<label for="' . $toggleId . '" class="tf-burger" aria-label="Menu">&#9776;</label>';
        $navDesk   = '<div class="tf-nav tf-nav-desktop">' . $links . '</div>';
        $ctaEl     = $cta ?: '';

        if ($variant === 'nav-center') {
            // Logo left · menu centered · button/cart right.
            $bar = '<div class="tf-header-bar" style="justify-content:space-between">'
                 . '<div style="flex-shrink:0">' . $brand . '</div>'
                 . '<div class="tf-nav tf-nav-desktop" style="flex:1;justify-content:center">' . $links . '</div>'
                 . '<div style="display:flex;align-items:center;gap:12px">' . $ctaEl . $cart . $burger . '</div>'
                 . '</div>';
        } elseif ($variant === 'center') {
            // Logo centered on top · menu centered below.
            $bar = '<div class="tf-header-bar" style="flex-direction:column;gap:8px">'
                 . '<div class="tf-center-top">' . $brand
                 .   '<div class="tf-mobile-only" style="align-items:center;gap:12px">' . $cart . $burger . '</div>'
                 . '</div>'
                 . '<div class="tf-nav tf-nav-desktop" style="width:100%;justify-content:center">' . $links . $ctaEl . $cart . '</div>'
                 . '</div>';
        } elseif ($variant === 'split') {
            // Menu left · logo centered · button/cart right.
            $bar = '<div class="tf-header-bar" style="justify-content:space-between;position:relative">'
                 . '<div style="display:flex;align-items:center;gap:16px">' . $navDesk . $burger . '</div>'
                 . '<div class="tf-brand-center">' . $brand . '</div>'
                 . '<div style="display:flex;align-items:center;gap:12px">' . $ctaEl . $cart . '</div>'
                 . '</div>';
        } else {
            // "left" (default): logo left · menu + button right.
            $bar = '<div class="tf-header-bar">' . $brand
                 . '<div style="display:flex;align-items:center;gap:20px">' . $navDesk . $ctaEl . $cart . $burger . '</div>'
                 . '</div>';
        }

        $mnav = '<nav class="tf-nav tf-mnav">' . $links . ($cta ? '<div style="margin-top:8px">' . $cta . '</div>' : '') . '</nav>';

        // Show the cart/account vs the login button based on the customer token.
        $authJs = !empty($p['showAccount'])
            ? '<script>(function(){var t;try{t=localStorage.getItem("tf_customer_' . self::esc(self::$slug) . '");}catch(e){}var yes=!!t;'
              . 'document.querySelectorAll("[data-tf-auth=in]").forEach(function(el){el.style.display=yes?"inline-flex":"none";});'
              . 'document.querySelectorAll("[data-tf-auth=out]").forEach(function(el){el.style.display=yes?"none":"inline-flex";});})();</script>'
            : '';

        return '<header class="tf-header" style="' . $bgc . $color . ($sticky ? '' : 'position:static;') . '">'
             . '<input type="checkbox" id="' . $toggleId . '" class="tf-navtoggle" hidden>'
             . '<div class="tf-container">' . $bar . '</div>'
             . '<div class="tf-container">' . $mnav . '</div>'
             . '</header>' . $authJs;
    }

    private static function secHero(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'centered-bg';
        $img = self::media($p['image'] ?? null);
        $biz = $doc['business'] ?? [];

        // A video (uploaded file, or a pasted .mp4 / YouTube / Vimeo link) wins
        // over the image. It plays muted + looped as the hero backdrop.
        $upl = self::media($p['video'] ?? null);
        $lnk = trim((string)($p['videoUrl'] ?? ''));
        $embed = null;
        if (!$upl && $lnk !== '') {
            if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})#i', $lnk, $m)) {
                $embed = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&playsinline=1';
            } elseif (preg_match('#vimeo\.com/(\d+)#i', $lnk, $m)) {
                $embed = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&muted=1&loop=1&background=1';
            }
        }
        $fileVid = $upl ?: (($lnk !== '' && !$embed) ? $lnk : null);
        $hasVid = (bool)($fileVid || $embed);
        if ($hasVid && $variant !== 'split') {
            // The backdrop is dark, so force light-on-dark treatment.
            $s['style'] = array_merge($s['style'] ?? [], ['bg' => 'dark']);
        }
        $onDark = self::isDarkBg($s);
        // Full-viewport hero, like a landing page.
        $fh = !empty($p['fullHeight']) ? 'min-height:100vh;min-height:100dvh;display:flex;flex-direction:column;justify-content:center;' : '';

        $body = '';
        if (!empty($p['badge'])) $body .= '<span class="tf-badge">' . self::esc($p['badge']) . '</span>';
        $body .= '<h1 class="tf-h1">' . self::esc($p['heading'] ?? '') . '</h1>';
        if (!empty($p['sub'])) $body .= '<p class="tf-lead">' . self::esc($p['sub']) . '</p>';

        $btns = self::btn($p['ctaPrimary'] ?? null, $onDark) . self::btn($p['ctaSecondary'] ?? null, $onDark, 'ghost');
        if (!empty($p['showWhatsapp']) && !empty($biz['whatsapp'])) $btns .= self::btn(['text'=>'WhatsApp','href'=>'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']),'newTab'=>true,'style'=>'ghost'], $onDark);
        if (!empty($p['showCall']) && !empty($biz['phone'])) $btns .= self::btn(['text'=>'Call now','href'=>'tel:' . $biz['phone'],'style'=>'ghost'], $onDark);
        $body .= '<div class="tf-btns">' . $btns . '</div>';

        if ($variant === 'split' && ($hasVid || $img)) {
            if ($hasVid) {
                $media = '<div style="width:100%;aspect-ratio:16/9;overflow:hidden;border-radius:var(--radius)">'
                       . ($fileVid
                          ? '<video src="' . self::esc($fileVid) . '" autoplay muted loop playsinline controls style="width:100%;height:100%;object-fit:cover"></video>'
                          : '<iframe src="' . self::esc($embed) . '" allow="autoplay; encrypted-media; picture-in-picture" style="width:100%;height:100%;border:0"></iframe>')
                       . '</div>';
            } else {
                $media = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? '') . '" style="width:100%;object-fit:cover;border-radius:var(--radius);max-height:460px">';
            }
            $inner = '<div class="tf-two" style="text-align:left"><div>' . $body . '</div>' . $media . '</div>';
            return self::shell($s, $inner, $fh);
        }

        // Video backdrop wins; otherwise the Image field IS the background.
        $backdrop = '';
        if ($hasVid) {
            $ov = isset($s['style']['overlay']) ? (float)$s['style']['overlay'] : 0.55;
            $backdrop = '<div aria-hidden="true" style="position:absolute;inset:0;overflow:hidden">'
                      . ($fileVid
                         ? '<video src="' . self::esc($fileVid) . '" autoplay muted loop playsinline style="width:100%;height:100%;object-fit:cover"></video>'
                         : '<iframe src="' . self::esc($embed) . '" allow="autoplay; encrypted-media; picture-in-picture" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);height:110%;width:177.78vh;min-width:110%;border:0"></iframe>')
                      . '<div style="position:absolute;inset:0;background:rgba(2,6,23,' . $ov . ')"></div></div>';
        } elseif ($variant === 'centered-bg' && !empty($p['image'])) {
            $s['style'] = array_merge($s['style'] ?? [], [
                'bg'      => 'image',
                'bgMedia' => $p['image'],
                'overlay' => $s['style']['overlay'] ?? 0.55,
            ]);
        }
        return self::shell($s, '<div class="tf-hero-wrap">' . $body . '</div>', $fh, $backdrop);
    }

    private static function secAbout(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'image-left';
        $img = self::media($p['image'] ?? null);
        $hasImage = $variant !== 'text-only' && $img;

        $text = '<div>' . self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, null);
        if (!empty($p['body'])) $text .= '<p class="tf-body" style="white-space:pre-line;color:var(--tf-text,var(--color-muted))">' . self::esc($p['body']) . '</p>';
        if (!empty($p['showPillars']) && !empty($p['pillars'])) {
            $text .= '<div class="tf-grid tf-c2" style="margin-top:32px">';
            foreach ($p['pillars'] as $pl) {
                $text .= '<div class="tf-card" style="padding:20px"><h3 style="font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pl['title'] ?? '') . '</h3>'
                       . (!empty($pl['text']) ? '<p style="margin-top:8px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($pl['text']) . '</p>' : '') . '</div>';
            }
            $text .= '</div>';
        }
        $text .= '</div>';

        if (!$hasImage) return self::shell($s, '<div style="max-width:768px;margin:0 auto">' . $text . '</div>');

        $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'About') . '" loading="lazy" style="width:100%;border-radius:var(--radius);max-height:460px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
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

        $cards = [];
        foreach ($items as $it) {
            $img = self::media($it['image'] ?? null);
            // An item with a full description has its own product page. The photo
            // and the title link straight to it — a separate "View details" button
            // is one extra thing to notice for something the card already implies.
            $href = trim((string)($it['body'] ?? '')) !== ''
                ? '/service/' . self::itemSlug($it, 'service')
                : '';
            $open = $href !== '' ? '<a href="' . self::esc($href) . '" style="color:inherit;text-decoration:none;display:block">' : '';
            $shut = $href !== '' ? '</a>' : '';

            $c = '<div class="tf-card">';
            if ($showImages && $img) {
                $c .= $open . '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy" style="height:176px;width:100%;' . self::imgFit($p['imageFit'] ?? null) . '">' . $shut;
            }
            $c .= '<div style="padding:20px">';
            $c .= $open . '<h3 style="font-family:var(--font-heading);font-size:18px;font-weight:600">' . self::esc($it['title'] ?? '') . '</h3>' . $shut;
            if (!empty($it['meta'])) $c .= '<p style="margin-top:4px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($it['meta']) . '</p>';
            if (!empty($it['price'])) $c .= '<p style="margin-top:6px;font-size:16px;font-weight:700;color:var(--color-primary)">' . self::esc($it['price']) . '</p>';
            if (!empty($it['desc'])) $c .= '<p style="margin-top:8px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($it['desc']) . '</p>';
            // Items without a description have no page to open, so they keep
            // whatever custom button the customer configured.
            if ($href === '' && !empty($it['cta']['text'])) {
                $l = $it['cta']; $l['style'] = $l['style'] ?? 'link'; $c .= '<div style="margin-top:16px">' . self::btn($l) . '</div>';
            }
            $c .= '</div></div>';
            $cards[] = $c;
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'carousel' ? self::carousel($cards)
            : '<div class="' . self::gridClass($variant) . '" style="text-align:left">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    private static function secGallery(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'grid-3';
        $imgs = array_values(array_filter($p['images'] ?? [], fn($i) => self::media($i['image'] ?? null)));
        if (!$imgs) return '';
        $g = $variant === 'grid-4' ? 'g4' : 'g3';
        $lightbox = ($p['lightbox'] ?? true) !== false;

        $slides = [];
        foreach ($imgs as $im) {
            $src = self::media($im['image']);
            $fig = '<figure class="tf-galfig"><img src="' . self::esc($src) . '" alt="' . self::esc($im['alt'] ?? '') . '" loading="lazy" style="' . self::imgFit($im['fit'] ?? ($p['imageFit'] ?? null)) . '">'
                 . (!empty($im['alt']) ? '<figcaption>' . self::esc($im['alt']) . '</figcaption>' : '') . '</figure>';
            $slides[] = $lightbox ? '<a href="' . self::esc($src) . '" target="_blank" rel="noopener noreferrer">' . $fig . '</a>' : $fig;
        }
        $body = $variant === 'marquee' ? self::marquee($slides)
            : ($variant === 'slider' ? self::carousel($slides)
            : '<div class="tf-gal ' . $g . '">' . implode('', $slides) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    private static function secStats(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $onDark = self::isDarkBg($s);
        $countUp = ($p['countUp'] ?? true) !== false;
        $cells = '';
        foreach ($items as $it) {
            $raw = (float)($it['value'] ?? 0);
            $val = number_format($raw);
            // Count-up: keep the final value in the HTML (SEO + no-JS), and let the
            // script animate from 0 when the section scrolls into view.
            $num = $countUp
                ? '<span class="tf-count" data-count="' . (int)round($raw) . '">' . self::esc($val) . '</span>'
                : self::esc($val);
            $suffixColor = $onDark ? 'inherit' : 'var(--color-accent)';
            $cells .= '<div style="text-align:center">'
                    . '<div style="font-family:var(--font-heading);font-size:34px;font-weight:700">' . $num
                    . '<span style="color:' . $suffixColor . '">' . self::esc($it['suffix'] ?? '') . '</span></div>'
                    . '<p style="margin-top:4px;font-size:14px;' . ($onDark ? 'opacity:.85' : 'color:var(--tf-text,var(--color-muted))') . '">' . self::esc($it['label'] ?? '') . '</p>'
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
        $cls = $variant === 'cards-4' ? 'tf-c4' : ($variant === 'cards-2' ? 'tf-c2' : 'tf-c3');

        $cards = [];
        foreach ($items as $pr) {
            $img = self::media($pr['photo'] ?? null);
            $c = '<div class="tf-card" style="padding:24px;text-align:center">';
            if ($img) {
                $st = ($round ? 'margin:0 auto;height:112px;width:112px;border-radius:999px;' : 'margin:0 auto;height:160px;width:100%;border-radius:var(--radius);') . self::imgFit($p['imageFit'] ?? null, $round ? '999px' : 'var(--radius)');
                $c .= '<img src="' . self::esc($img) . '" alt="' . self::esc($pr['name'] ?? '') . '" loading="lazy" style="' . $st . '">';
            } else {
                $initial = function_exists('mb_substr') ? mb_substr($pr['name'] ?? '?', 0, 1, 'UTF-8') : substr($pr['name'] ?? '?', 0, 1);
                $c .= '<div aria-hidden="true" style="margin:0 auto;height:112px;width:112px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;border-radius:999px;background:var(--color-surface);color:var(--tf-text,var(--color-muted))">' . self::esc($initial) . '</div>';
            }
            $c .= '<h3 style="margin-top:16px;font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pr['name'] ?? '') . '</h3>';
            if (!empty($pr['role'])) $c .= '<p style="margin-top:2px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($pr['role']) . '</p>';
            if (!empty($pr['meta'])) $c .= '<p style="margin-top:8px;display:inline-block;padding:4px 12px;font-size:12px;background:var(--color-surface);color:var(--tf-text,var(--color-muted));border-radius:999px">' . self::esc($pr['meta']) . '</p>';
            if (!empty($pr['bio'])) $c .= '<p style="margin-top:12px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($pr['bio']) . '</p>';
            if (!empty($pr['link']['text'])) { $l = $pr['link']; $l['style'] = $l['style'] ?? 'link'; $c .= '<div style="margin-top:12px">' . self::btn($l) . '</div>'; }
            $c .= '</div>';
            $cards[] = $c;
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'slider' ? self::carousel($cards)
            : '<div class="tf-grid ' . $cls . '">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
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
                   . (!empty($r['role']) ? '<span style="display:block;font-size:12px;color:var(--tf-text,var(--color-muted))">' . self::esc($r['role']) . '</span>' : '') . '</span>'
                   . '</figcaption></figure>';
            return self::shell($s, $inner);
        }

        $cards = [];
        foreach ($items as $r) {
            $photo = self::media($r['photo'] ?? null);
            $cards[] = '<div class="tf-card" style="padding:24px;text-align:left">' . $stars($r['rating'] ?? 5)
                    . '<blockquote style="margin:12px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))">“' . self::esc($r['quote'] ?? '') . '”</blockquote>'
                    . '<div style="margin-top:20px;display:flex;align-items:center;gap:12px">'
                    . ($photo ? '<img src="' . self::esc($photo) . '" alt="' . self::esc($r['name'] ?? '') . '" loading="lazy" style="height:40px;width:40px;border-radius:999px;object-fit:cover">' : '')
                    . '<div><p style="font-size:14px;font-weight:600;margin:0">' . self::esc($r['name'] ?? '') . '</p>'
                    . (!empty($r['role']) ? '<p style="font-size:12px;color:var(--tf-text,var(--color-muted));margin:0">' . self::esc($r['role']) . '</p>' : '') . '</div>'
                    . '</div></div>';
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'slider' ? self::carousel($cards, ($p['autoplay'] ?? true) === false ? 0 : 4000)
            : '<div class="tf-grid tf-c3">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
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
                   . (!empty($it['a']) ? '<p style="white-space:pre-line;padding:0 20px 16px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($it['a']) . '</p>' : '')
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

    /** Built-in contact form, used when the site has no custom form. Its id and
     *  field set match the fallback in form-submit.php. */
    private static function defaultContactForm(): array
    {
        return [
            'id' => 'contact',
            'submitText' => 'Send message',
            'successMessage' => 'Thank you — your message has been sent.',
            'fields' => [
                ['name'=>'name','label'=>'Your name','type'=>'text','required'=>true],
                ['name'=>'phone','label'=>'Phone','type'=>'tel','required'=>true],
                ['name'=>'email','label'=>'Email','type'=>'email'],
                ['name'=>'message','label'=>'Message','type'=>'textarea'],
            ],
        ];
    }

    /** Optional customer login/signup for e-commerce sites (token in localStorage). */
    private static function secAccount(array $s, array $doc): string
    {
        $p    = $s['props'] ?? [];
        $uid  = 'ac' . substr(md5(($s['id'] ?? '') . 'acc'), 0, 6);
        $ph   = ($p['showPhone'] ?? true) !== false;
        $in   = 'width:100%;padding:10px 12px;font-size:14px;margin-bottom:10px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $btn  = 'width:100%;padding:12px;font-size:14px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer';
        $tab  = 'flex:1;padding:8px;font-size:14px;font-weight:600;border-radius:var(--radius);cursor:pointer';

        $card = '<div id="' . $uid . '" style="max-width:420px;margin:0 auto;text-align:left;padding:28px;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);box-shadow:0 8px 30px rgba(16,24,40,.08)">'
            // Signed-in view (shown by JS when a token exists).
            . '<div data-view="me" style="display:none;text-align:center">'
            .   '<p style="font-size:18px;font-weight:700;margin:0">Hi <span data-me-name></span> &#128075;</p>'
            .   '<p style="margin:6px 0 0;font-size:14px;color:var(--color-muted)">You&#39;re signed in.</p>'
            .   '<button type="button" data-act="signout" style="margin-top:18px;padding:10px 20px;font-size:14px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Sign out</button>'
            .   '<div data-orders style="margin-top:26px;text-align:left"></div>'
            . '</div>'
            // Auth forms.
            . '<div data-view="auth">'
            .   '<div style="display:flex;gap:8px;margin-bottom:18px">'
            .     '<button type="button" data-tab="login" style="' . $tab . ';background:var(--color-primary);color:var(--color-primary-fg);border:none">Sign in</button>'
            .     '<button type="button" data-tab="signup" style="' . $tab . ';background:transparent;color:var(--color-text);border:1px solid var(--color-border)">Create account</button>'
            .   '</div>'
            .   '<form data-form>'
            .     '<input data-signup name="name" placeholder="Your name" style="' . $in . ';display:none">'
            .     '<input name="email" type="email" placeholder="Email" required style="' . $in . '">'
            .     ($ph ? '<input data-signup name="phone" type="tel" placeholder="Phone (optional)" style="' . $in . ';display:none">' : '')
            .     '<input name="password" type="password" placeholder="Password" required minlength="6" style="' . $in . '">'
            .     '<button type="submit" data-submit style="' . $btn . '">Sign in</button>'
            .     '<p data-msg style="margin:12px 0 0;text-align:center;font-size:13px;color:#dc2626"></p>'
            .   '</form>'
            . '</div>'
            . '</div>';

        $cfg = json_encode(['u' => $uid, 'site' => self::$slug, 'api' => self::apiBase()]);
        $script = '<script>(function(){var C=' . $cfg . ';var root=document.getElementById(C.u);if(!root)return;'
            . 'var KEY="tf_customer_"+C.site,mode="login";'
            . 'function $(s){return root.querySelector(s);}function all(s){return root.querySelectorAll(s);}'
            . 'function esc(s){var d=document.createElement("div");d.textContent=(s==null?"":s);return d.innerHTML;}'
            . 'function show(me){$("[data-view=me]").style.display=me?"block":"none";$("[data-view=auth]").style.display=me?"none":"block";if(me&&me.name)$("[data-me-name]").textContent=me.name;if(me)loadOrders();}'
            . 'function get(){try{return JSON.parse(localStorage.getItem(KEY));}catch(e){return null;}}'
            . 'function loadOrders(){var a=get();if(!a||!a.token)return;var box=$("[data-orders]");if(!box)return;box.innerHTML="<p style=\"font-size:13px;color:var(--color-muted)\">Loading your orders…</p>";'
            . 'fetch(C.api+"/api/sites/customer-orders.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({site:C.site,token:a.token})}).then(function(r){return r.json();}).then(function(res){'
            . 'var os=(res&&res.data&&res.data.orders)||[];'
            . 'if(!os.length){box.innerHTML="<p style=\"font-size:15px;font-weight:700;margin:0 0 8px\">My Orders</p><p style=\"font-size:13px;color:var(--color-muted);margin:0\">No orders yet.</p>";return;}'
            . 'var h="<p style=\"font-size:15px;font-weight:700;margin:0 0 12px\">My Orders</p>";'
            . 'os.forEach(function(o){h+="<div style=\"border:1px solid var(--color-border);border-radius:var(--radius);padding:12px;margin-bottom:8px\">"'
            . '+"<div style=\"display:flex;justify-content:space-between;gap:8px\"><strong style=\"font-size:14px\">"+esc(o.item_title||"Order")+"</strong><span style=\"font-size:12px;color:var(--color-muted)\">#"+esc(o.id)+"</span></div>"'
            . '+"<div style=\"font-size:13px;color:var(--color-muted);margin-top:3px\">"+esc(o.price||"")+((o.quantity>1)?(" × "+esc(o.quantity)):"")+" · "+esc(o.status||"")+"</div></div>";});'
            . 'box.innerHTML=h;}).catch(function(){box.innerHTML="";});}'
            . 'show(get());'
            . 'function setMode(m){mode=m;all("[data-tab]").forEach(function(b){var on=b.getAttribute("data-tab")===m;b.style.background=on?"var(--color-primary)":"transparent";b.style.color=on?"var(--color-primary-fg)":"var(--color-text)";b.style.border=on?"none":"1px solid var(--color-border)";});'
            . 'all("[data-signup]").forEach(function(el){el.style.display=(m==="signup")?"block":"none";el.required=(m==="signup"&&el.name==="name");});'
            . '$("[data-submit]").textContent=(m==="login")?"Sign in":"Create account";$("[data-msg]").textContent="";}'
            . 'all("[data-tab]").forEach(function(b){b.addEventListener("click",function(){setMode(b.getAttribute("data-tab"));});});'
            . 'var so=$("[data-act=signout]");if(so)so.addEventListener("click",function(){localStorage.removeItem(KEY);show(null);});'
            . '$("[data-form]").addEventListener("submit",function(e){e.preventDefault();var f=e.target,btn=$("[data-submit]"),msg=$("[data-msg]");'
            . 'var body={action:(mode==="signup"?"register":"login"),site:C.site,email:f.email.value,password:f.password.value};'
            . 'if(mode==="signup"){body.name=f.name.value;if(f.phone)body.phone=f.phone.value;}'
            . 'btn.disabled=true;var ob=btn.textContent;btn.textContent="Please wait…";'
            . 'fetch(C.api+"/api/sites/customer-auth.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(body)})'
            . '.then(function(r){return r.json();}).then(function(res){btn.disabled=false;btn.textContent=ob;'
            . 'if(res&&res.success){localStorage.setItem(KEY,JSON.stringify({name:res.data.name,email:res.data.email,token:res.data.token}));show({name:res.data.name});}'
            . 'else{msg.textContent=(res&&res.message)||"Something went wrong.";}})'
            . '.catch(function(){btn.disabled=false;btn.textContent=ob;msg.textContent="Connection error.";});});})();</script>';

        $inner = self::sectionHeader(null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $card . $script;
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
        if (!$form) $form = self::defaultContactForm();  // no forms editor yet — always show a contact form

        // Contact detail rows.
        $rows = '';
        $add = function ($label, $val, $href = null) {
            $v = $href ? '<a href="' . self::esc($href) . '" style="font-size:14px;color:var(--color-text)">' . self::esc($val) . '</a>'
                       : '<p style="font-size:14px;color:var(--tf-text,var(--color-muted));margin:0">' . self::esc($val) . '</p>';
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
                $inputStyle = 'width:100%;padding:8px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
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
        } elseif (in_array($variant, ['form-map', 'form-only'], true)) {
            // No custom form configured — use the built-in enquiry form. It needs
            // no setup and lands in the dashboard's "Website Inquiries" page.
            $uid = 'iq' . substr(md5(($s['id'] ?? '') . 'inq'), 0, 6);
            $in  = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
            $lb  = 'display:block;margin-bottom:4px;font-size:12px;font-weight:600';
            $formEl = '<form id="' . $uid . '" style="display:grid;gap:12px;text-align:left">'
                . '<div><label style="' . $lb . '">Name *</label><input class="' . $uid . '-f" data-k="name" required style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Email</label><input class="' . $uid . '-f" data-k="email" type="email" style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Phone *</label><input class="' . $uid . '-f" data-k="phone" type="tel" required style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Subject</label><input class="' . $uid . '-f" data-k="subject" style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Message *</label><textarea class="' . $uid . '-f" data-k="message" rows="4" required style="' . $in . '"></textarea></div>'
                . '<button type="submit" style="padding:12px;font-size:15px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">'
                . self::esc($p['submitText'] ?? 'Send enquiry') . '</button>'
                . '<p id="' . $uid . '-msg" style="margin:0;font-size:13px;font-weight:600"></p></form>'
                . '<script>(function(){var U=' . json_encode($uid) . ',S=' . json_encode(self::$slug) . ',B=' . json_encode(self::apiBase()) . ';'
                . 'var fm=document.getElementById(U),m=document.getElementById(U+"-msg");if(!fm)return;'
                . 'fm.addEventListener("submit",function(e){e.preventDefault();var d={};'
                . 'fm.querySelectorAll("."+U+"-f").forEach(function(el){d[el.getAttribute("data-k")]=(el.value||"").trim();});'
                . 'var b=fm.querySelector("button[type=submit]"),ob=b.textContent;b.disabled=true;b.textContent="Sending…";'
                . 'fetch(B+"/api/sites/inquiry-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
                . 'body:JSON.stringify({site:S,name:d.name,email:d.email,phone:d.phone,subject:d.subject,message:d.message})})'
                . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;b.textContent=ob;'
                . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Thank you! We will get back to you shortly.";fm.reset();}'
                . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not send your message.";}})'
                . '.catch(function(){b.disabled=false;b.textContent=ob;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>';
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
        // Custom uploaded social icons (independent of the Business-Info text links).
        $iconPx = ['small'=>28,'medium'=>36,'large'=>48,'extra-large'=>64][$p['socialIconSize'] ?? 'medium'] ?? 36;
        $icons = '';
        foreach (($p['socialIcons'] ?? []) as $si) {
            $src = self::media($si['icon'] ?? null);
            $href = trim((string)($si['href'] ?? ''));
            if (!$src || $href === '') continue;
            $lbl = self::esc($si['label'] ?? 'Social link');
            $icons .= '<a href="' . self::esc($href) . '" target="_blank" rel="noopener noreferrer" aria-label="' . $lbl . '" title="' . $lbl . '" style="display:inline-flex;opacity:.8;text-decoration:none">'
                    . '<img src="' . self::esc($src) . '" alt="' . self::esc($si['label'] ?? '') . '" style="height:' . $iconPx . 'px;width:' . $iconPx . 'px;object-fit:contain"></a>';
        }
        if ($icons) $brand .= '<div style="margin-top:16px;display:flex;flex-wrap:wrap;align-items:center;gap:12px">' . $icons . '</div>';
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
        // Privacy Policy / Terms links — each shown only when its content exists.
        $legal = '';
        if (($p['showLegal'] ?? true) !== false) {
            $ls = [];
            if (trim((string)($p['privacyBody'] ?? '')) !== '') $ls[] = '<a href="/privacy" style="text-decoration:underline;color:inherit">Privacy Policy</a>';
            if (trim((string)($p['termsBody'] ?? '')) !== '')   $ls[] = '<a href="/terms" style="text-decoration:underline;color:inherit">Terms &amp; Conditions</a>';
            if ($ls) $legal = '<p style="margin:0;display:flex;gap:14px;flex-wrap:wrap">' . implode('', $ls) . '</p>';
        }
        $bottom = '<div style="margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.15);display:flex;flex-direction:column;gap:8px;justify-content:space-between;align-items:center;font-size:12px;opacity:.7" class="tf-foot-bottom">'
                . '<p style="margin:0">' . self::esc($copy) . '</p>'
                . $legal
                . (($p['showBranding'] ?? true) !== false ? '<p style="margin:0">Powered by <a href="https://tapify.co.in" target="_blank" rel="noopener noreferrer" style="text-decoration:underline">Tapify</a></p>' : '')
                . '</div>';

        return self::shell($s, $body . $bottom);
    }

    /* ------------------------------------------------- new sections */

    private static function secHours(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $hours = $doc['business']['hours'] ?? [];
        $labels = ['mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday','sun'=>'Sunday'];
        $byDay = [];
        foreach ($hours as $h) { if (!empty($h['day'])) $byDay[strtolower($h['day'])] = $h; }
        if (!$byDay) return '';
        $today = strtolower(date('D'));
        $hl = ($p['highlightToday'] ?? true) !== false;
        $rows = '';
        foreach (['mon','tue','wed','thu','fri','sat','sun'] as $d) {
            if (!isset($byDay[$d])) continue;
            $h = $byDay[$d];
            $val = 'Closed';
            if (empty($h['closed']) && (!empty($h['open']) || !empty($h['close']))) {
                $val = self::esc(trim(($h['open'] ?? '') . ' – ' . ($h['close'] ?? ''), " –"));
            }
            $isToday = $hl && ($d === $today);
            $st = 'display:flex;justify-content:space-between;gap:16px;padding:12px 16px;font-size:15px;border-radius:var(--radius)';
            $st .= $isToday ? ';background:var(--color-primary);color:var(--color-primary-fg);font-weight:600' : ';border:1px solid var(--color-border)';
            $rows .= '<li style="' . $st . '"><span>' . self::esc($labels[$d]) . ($isToday ? ' · Today' : '') . '</span><span>' . $val . '</span></li>';
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s))
               . '<ul style="list-style:none;margin:0 auto;padding:0;max-width:520px;display:flex;flex-direction:column;gap:8px;text-align:left">' . $rows . '</ul>';
        if (!empty($p['note'])) $inner .= '<p style="margin-top:16px;font-size:13px;opacity:.7">' . self::esc($p['note']) . '</p>';
        return self::shell($s, $inner);
    }

    /* ------------------------------------------------------- blog detail */

    /** URL-safe slug for a post: its slug field, else built from the title. */
    private static function itemSlug(array $item, string $fallback = 'item'): string
    {
        $s = strtolower(trim((string)($item['slug'] ?? '')));
        if ($s === '') $s = strtolower(trim((string)($item['title'] ?? '')));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        $s = trim((string)$s, '-');
        return $s !== '' ? $s : $fallback;
    }

    /** Every post in the document, keyed by slug (first wins on a clash). */
    private static function allPosts(array $doc): array
    {
        $out = [];
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'blog') continue;
                foreach (($s['props']['posts'] ?? []) as $post) {
                    $slug = self::itemSlug($post, 'post');
                    if (!isset($out[$slug])) $out[$slug] = $post;
                }
            }
        }
        return $out;
    }

    /** Every service/product with a full description, keyed by slug. */
    private static function allServices(array $doc): array
    {
        $out = [];
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'services') continue;
                foreach (($s['props']['items'] ?? []) as $item) {
                    if (trim((string)($item['body'] ?? '')) === '') continue;
                    $slug = self::itemSlug($item, 'service');
                    if (!isset($out[$slug])) $out[$slug] = $item;
                }
            }
        }
        return $out;
    }

    /** The first visible section of a type anywhere in the site (for header/footer). */
    private static function chromeSection(array $doc, string $type): string
    {
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === $type && ($s['visible'] ?? true) !== false && empty($s['style']['hidden'])) {
                    return self::section($s, $doc);
                }
            }
        }
        return '';
    }

    /** Plain-text article body -> paragraphs (blank line separates paragraphs). */
    private static function articleBody(string $body): string
    {
        $body = trim($body);
        if ($body === '') return '';
        $out = '';
        foreach (preg_split('/\n\s*\n/', $body) as $para) {
            $para = trim((string)$para);
            if ($para !== '') $out .= '<p style="margin:0 0 18px">' . nl2br(self::esc($para)) . '</p>';
        }
        return $out;
    }

    /** [title, body] for a legal page, pulled from the first footer section. */
    private static function legalContent(array $doc, string $kind): array
    {
        $key   = $kind === 'privacy' ? 'privacyBody' : 'termsBody';
        $title = $kind === 'privacy' ? 'Privacy Policy' : 'Terms & Conditions';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'footer' && trim((string)($s['props'][$key] ?? '')) !== '') {
                    return [$title, (string)$s['props'][$key]];
                }
            }
        }
        return [$title, ''];
    }

    /** A simple legal page (title + body) wrapped in the site header/footer. */
    private static function renderLegal(array $doc, string $title, string $body, string $path): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $pseudo = ['slug' => $path, 'title' => $title, 'seo' => ['title' => trim($title . ' | ' . ($doc['site']['name'] ?? ''), ' |'), 'robots' => 'noindex,follow']];
        $article = '<article class="tf-container" style="max-width:760px;padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<h1 style="margin:0 0 24px;font-family:var(--font-heading);font-size:34px;line-height:1.15;font-weight:700">' . self::esc($title) . '</h1>'
            . '<div style="font-size:16px;line-height:1.75">' . self::articleBody($body) . '</div>'
            . '</article>';
        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::animScript()
             . "</body></html>";
    }

    /** Render one blog post on its own page, wrapped in the site header/footer. */
    private static function renderPostDetail(array $doc, array $post): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/post/' . self::itemSlug($post, 'post'),
            'title' => $post['title'] ?? 'Post',
            'seo'   => [
                'title'       => trim(($post['title'] ?? 'Post') . ' | ' . $name, ' |'),
                'description' => $post['excerpt'] ?? '',
                'ogImage'     => $post['image'] ?? null,
                'robots'      => 'index,follow',
            ],
        ];

        // Where "Back" goes: the page that holds a blog section, else home.
        $backPath = '/';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'blog') { $backPath = $pg['slug'] ?? '/'; break 2; }
            }
        }

        $cover = self::media($post['image'] ?? null);
        $article = '<article class="tf-container" style="max-width:760px;padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-bottom:22px;font-size:14px;font-weight:600;color:var(--color-primary);text-decoration:none">&larr; Back</a>'
            . (!empty($post['date']) ? '<p style="margin:0 0 8px;font-size:13px;font-weight:600;color:var(--color-accent)">' . self::esc($post['date']) . '</p>' : '')
            . '<h1 style="margin:0 0 22px;font-family:var(--font-heading);font-size:38px;line-height:1.15;font-weight:700">' . self::esc($post['title'] ?? '') . '</h1>'
            . ($cover ? '<img src="' . self::esc($cover) . '" alt="' . self::esc($post['title'] ?? '') . '" style="width:100%;border-radius:var(--radius);margin-bottom:30px">' : '')
            . '<div style="font-size:17px;line-height:1.75">' . self::articleBody($post['body'] ?? '') . '</div>'
            . '</article>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * Render one service/product on its own page: a photo gallery (like a
     * product page on Amazon/Flipkart) plus title, price and full description.
     */
    /** Absolute origin of this Tapify backend, for the public JSON endpoints. */
    private static function apiBase(): string
    {
        if (defined('SITE_URL') && SITE_URL) return rtrim(SITE_URL, '/');
        if (defined('PUBLIC_URL') && PUBLIC_URL) return rtrim(PUBLIC_URL, '/');
        return 'https://app.tapify.co.in';
    }

    /**
     * Customer reviews for one item: the approved list plus a "write a review"
     * form. Both talk to the builder's own endpoints — nothing shared with the
     * vCard backend.
     */
    private static function reviewsBlock(array $item): string
    {
        $slug = self::itemSlug($item, 'service');
        $uid  = 'rv' . substr(md5($slug), 0, 6);
        $in   = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb   = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

        $stars = '';
        for ($i = 5; $i >= 1; $i--) $stars .= '<option value="' . $i . '">' . str_repeat('★', $i) . ' (' . $i . ')</option>';

        return '<section style="margin-top:48px;padding-top:32px;border-top:1px solid var(--color-border);text-align:left">'
            . '<h2 style="margin:0 0 4px;font-family:var(--font-heading);font-size:24px;font-weight:700">Customer reviews</h2>'
            . '<div id="' . $uid . '-list" style="margin-top:16px"><p style="font-size:14px;color:var(--tf-text,var(--color-muted))">Loading reviews…</p></div>'
            . '<form id="' . $uid . '-form" style="margin-top:28px;max-width:460px;padding:18px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-surface)">'
            . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Write a review</p>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Your name *</label><input name="name" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Rating *</label><select name="rating" style="' . $in . '">' . $stars . '</select></div>'
            . '<div style="margin-bottom:14px"><label style="' . $lb . '">Review *</label><textarea name="comment" rows="3" required style="' . $in . '"></textarea></div>'
            . '<button type="submit" style="padding:11px 22px;font-size:14px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Submit review</button>'
            . '<p id="' . $uid . '-msg" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
            . '</form>'
            . '<script>(function(){var U=' . json_encode($uid) . ',S=' . json_encode(self::$slug) . ',I=' . json_encode($slug) . ',B=' . json_encode(self::apiBase()) . ';'
            . 'var list=document.getElementById(U+"-list"),f=document.getElementById(U+"-form"),m=document.getElementById(U+"-msg");'
            . 'function esc(t){var d=document.createElement("div");d.textContent=t;return d.innerHTML;}'
            . 'function load(){fetch(B+"/api/sites/reviews.php?site="+encodeURIComponent(S)+"&item="+encodeURIComponent(I),{headers:{"Accept":"application/json"}})'
            . '.then(function(r){return r.json();}).then(function(res){var d=(res&&res.data)||[];'
            . 'if(!d.length){list.innerHTML="<p style=\'font-size:14px;color:var(--tf-text,var(--color-muted))\'>No reviews yet — be the first.</p>";return;}'
            . 'list.innerHTML=d.map(function(x){return "<div style=\'padding:14px 0;border-bottom:1px solid var(--color-border)\'>"'
            . '+"<div style=\'color:#f59e0b;font-size:14px\'>"+"★".repeat(x.rating)+"</div>"'
            . '+"<p style=\'margin:4px 0 0;font-size:14px;font-weight:700\'>"+esc(x.name)+"</p>"'
            . '+"<p style=\'margin:4px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))\'>"+esc(x.comment)+"</p></div>";}).join("");})'
            . '.catch(function(){list.innerHTML="";});}'
            . 'load();'
            . 'f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button[type=submit]");b.disabled=true;'
            . 'fetch(B+"/api/sites/review-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
            . 'body:JSON.stringify({site:S,item_slug:I,name:f.name.value,rating:f.rating.value,comment:f.comment.value})})'
            . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;'
            . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Thank you! Your review has been posted.";f.reset();load();}'
            . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not submit.";}})'
            . '.catch(function(){b.disabled=false;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>'
            . '</section>';
    }

    /** Selling price, MRP and the discount label, from an item's free-text prices. */
    private static function priceBits(array $item): array
    {
        $sell = trim((string)($item['price'] ?? ''));
        $mrp  = trim((string)($item['mrp'] ?? ''));
        $off  = '';
        if ($sell !== '' && $mrp !== '') {
            $ns = (float)preg_replace('/[^0-9.]/', '', $sell);
            $nm = (float)preg_replace('/[^0-9.]/', '', $mrp);
            if ($nm > 0 && $ns > 0 && $ns < $nm) $off = round((($nm - $ns) / $nm) * 100) . '% Off';
        }
        return [$sell, $mrp, $off];
    }

    /**
     * The product gallery: one big photo with a thumbnail strip beside it.
     *
     * A carousel hides everything but the current shot, which is the wrong shape
     * for a product page — buyers want to see at a glance how many photos there
     * are and jump straight to the one they care about.
     */
    private static function productGallery(array $photos, string $uid): string
    {
        if (!$photos) return '';
        $main = '<div class="tf-pmain"><img id="' . $uid . '-main" src="' . self::esc($photos[0]['src']) . '" alt="'
              . self::esc($photos[0]['alt']) . '"></div>';

        if (count($photos) < 2) return '<div class="tf-pgal">' . $main . '</div>';

        $thumbs = '';
        foreach ($photos as $i => $ph) {
            $thumbs .= '<button type="button" class="tf-pthumb' . ($i === 0 ? ' is-active' : '') . '"'
                     . ' data-src="' . self::esc($ph['src']) . '" aria-label="Photo ' . ($i + 1) . '">'
                     . '<img src="' . self::esc($ph['src']) . '" alt="" loading="lazy"></button>';
        }

        $script = '<script>(function(){var g=document.getElementById(' . json_encode($uid . '-gal') . ');if(!g)return;'
                . 'var m=document.getElementById(' . json_encode($uid . '-main') . ');'
                . 'g.addEventListener("click",function(e){var b=e.target.closest?e.target.closest(".tf-pthumb"):null;if(!b)return;'
                . 'm.src=b.getAttribute("data-src");'
                . 'Array.prototype.forEach.call(g.querySelectorAll(".tf-pthumb"),function(t){t.classList.remove("is-active");});'
                . 'b.classList.add("is-active");});})();</script>';

        return '<div class="tf-pgal" id="' . $uid . '-gal"><div class="tf-pthumbs">' . $thumbs . '</div>' . $main . '</div>' . $script;
    }

    /** The optional "Product Information" spec table. */
    private static function productInfoTable(array $item): string
    {
        $rows = '';
        foreach (($item['productInfo'] ?? []) as $r) {
            $k = trim((string)($r['label'] ?? ''));
            $v = trim((string)($r['value'] ?? ''));
            if ($k === '' && $v === '') continue;
            $rows .= '<tr>'
                   . '<th style="padding:9px 0;text-align:left;font-size:13px;font-weight:500;color:var(--tf-text,var(--color-muted));vertical-align:top;width:45%">' . self::esc($k) . '</th>'
                   . '<td style="padding:9px 0;text-align:left;font-size:13px;font-weight:700">' . self::esc($v) . '</td>'
                   . '</tr>';
        }
        if ($rows === '') return '';

        $title = trim((string)($item['productInfoTitle'] ?? '')) ?: 'Product Information';
        return '<section class="tf-card" style="margin-top:22px;padding:18px;text-align:left">'
             . '<h2 style="margin:0 0 8px;font-family:var(--font-heading);font-size:17px;font-weight:700">' . self::esc($title) . '</h2>'
             . '<table style="width:100%;border-collapse:collapse">' . $rows . '</table>'
             . '</section>';
    }

    /**
     * "Best sellers" — the shop's other products, shown under the one being
     * viewed so a visitor who lands straight on a product page has somewhere to
     * go next. Each card opens its own detail page.
     */
    private static function relatedProducts(array $doc, array $item, string $backPath): string
    {
        // Settings live on the Services section, so find the one holding this item.
        $slug = self::itemSlug($item, 'service');
        $cfg = null;
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $sec) {
                if (($sec['type'] ?? '') !== 'services') continue;
                foreach (($sec['props']['items'] ?? []) as $it) {
                    if (self::itemSlug($it, 'service') === $slug) { $cfg = $sec['props']; break 3; }
                }
                if ($cfg === null) $cfg = $sec['props'] ?? [];   // fall back to the first one
            }
        }
        if (($cfg['showRelated'] ?? true) === false) return '';

        $others = [];
        foreach (self::allServices($doc) as $sl => $it) {
            if ($sl === $slug) continue;
            $others[] = [$sl, $it];
            if (count($others) >= 8) break;
        }
        if (!$others) return '';

        $cards = '';
        foreach ($others as [$sl, $it]) {
            [$sell, $mrp, $off] = self::priceBits($it);
            $img = self::media($it['image'] ?? null);
            $cards .= '<a class="tf-bscard" href="/service/' . self::esc($sl) . '">'
                . ($img ? '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy">' : '')
                . '<p class="tf-bstitle">' . self::esc($it['title'] ?? '') . '</p>'
                . (($sell !== '' || $mrp !== '') ? '<p class="tf-bsprice">'
                    . ($sell !== '' ? '<span class="tf-bssell">' . self::esc($sell) . '</span>' : '')
                    . ($mrp !== '' ? '<s>' . self::esc($mrp) . '</s>' : '')
                    . ($off !== '' ? '<span class="tf-bsoff">' . strtoupper($off) . '</span>' : '')
                    . '</p>' : '')
                . '</a>';
        }

        $heading = trim((string)($cfg['relatedHeading'] ?? '')) ?: 'Best sellers';
        return '<section style="margin-top:56px;text-align:center">'
             . '<h2 style="margin:0;font-family:var(--font-heading);font-size:28px;font-weight:800;letter-spacing:.02em;text-transform:uppercase">' . self::esc($heading) . '</h2>'
             . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-top:8px;font-size:13px;font-weight:600;color:inherit;text-decoration:underline">VIEW ALL</a>'
             . '<div class="tf-bsgrid">' . $cards . '</div>'
             . '</section>';
    }

    private static function renderServiceDetail(array $doc, array $item): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/service/' . self::itemSlug($item, 'service'),
            'title' => $item['title'] ?? 'Service',
            'seo'   => [
                'title'       => trim(($item['title'] ?? 'Service') . ' | ' . $name, ' |'),
                'description' => $item['desc'] ?? '',
                'ogImage'     => $item['image'] ?? null,
                'robots'      => 'index,follow',
            ],
        ];

        // Where "Back" goes: the page that holds a Services section, else home.
        $backPath = '/';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'services') { $backPath = $pg['slug'] ?? '/'; break 2; }
            }
        }

        // Photos: the card's own image first, then the extra gallery photos —
        // a product-style set of shots the customer can flip through.
        $photos = [];
        $cover = self::media($item['image'] ?? null);
        if ($cover) $photos[] = ['src' => $cover, 'alt' => $item['title'] ?? ''];
        foreach (($item['gallery'] ?? []) as $g) {
            $src = self::media($g['image'] ?? null);
            if ($src) $photos[] = ['src' => $src, 'alt' => $g['alt'] ?? ($item['title'] ?? '')];
        }

        $puid = 'pg' . substr(md5(self::itemSlug($item, 'service')), 0, 6);
        $gallery = self::productGallery($photos, $puid);

        // --- price block: selling price, struck-through MRP and the discount % ---
        $priceHtml = '';
        if (!empty($item['price']) || !empty($item['mrp'])) {
            [$sell, $mrp, $off] = self::priceBits($item);
            $priceHtml = '<div style="margin:10px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:10px">'
                . ($sell !== '' ? '<span style="font-size:28px;font-weight:800;color:var(--color-primary)">' . self::esc($sell) . '</span>' : '')
                . ($mrp !== '' ? '<span style="font-size:16px;color:var(--tf-text,var(--color-muted));text-decoration:line-through">' . self::esc($mrp) . '</span>' : '')
                . ($off !== '' ? '<span style="font-size:14px;font-weight:700;color:#16a34a">' . $off . '</span>' : '')
                . '</div>';
        }
        $ctaHtml = !empty($item['cta']['text']) ? self::btn($item['cta']) : '';

        // --- ordering: option picker + Add to Cart / Buy Now + order form ---
        $orderHtml = '';
        if (($item['enableOrder'] ?? true) !== false) {
            $uid  = 'od' . substr(md5(self::itemSlug($item, 'service')), 0, 6);
            $opts = array_values(array_filter(array_map('trim', (array)($item['variantOptions'] ?? []))));
            $vlabel = trim((string)($item['variantLabel'] ?? ''));
            $in = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
            $lb = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

            $variantEl = '';
            if ($vlabel !== '' && $opts) {
                $o = '';
                foreach ($opts as $ov) $o .= '<option>' . self::esc($ov) . '</option>';
                $variantEl = '<div style="margin-top:18px;max-width:280px"><label style="' . $lb . '">' . self::esc($vlabel) . '</label>'
                           . '<select id="' . $uid . '-var" style="' . $in . '">' . $o . '</select></div>';
            }

            $btnRow = '<div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:12px">'
                . '<button type="button" id="' . $uid . '-cart" style="padding:13px 26px;font-size:15px;font-weight:700;border:1px solid var(--color-primary);border-radius:var(--radius);background:transparent;color:var(--color-primary);cursor:pointer">Add to Cart</button>'
                . '<button type="button" id="' . $uid . '-buy" style="padding:13px 26px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Buy Now</button>'
                . '<span id="' . $uid . '-note" role="status" style="align-self:center;font-size:13px;font-weight:600;color:#16a34a"></span>'
                . '</div>';

            // Checkout form (hidden until Buy Now / cart checkout).
            $form = '<form id="' . $uid . '-form" style="display:none;margin-top:22px;max-width:420px;text-align:left;padding:18px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-surface)">'
                . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Your details</p>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Name *</label><input name="name" required style="' . $in . '"></div>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Contact number *</label><input name="phone" type="tel" required style="' . $in . '"></div>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Email</label><input name="email" type="email" style="' . $in . '"></div>'
                . '<div style="margin-bottom:14px"><label style="' . $lb . '">Additional note (optional)</label><textarea name="note" rows="2" placeholder="Any special instructions for this order…" style="' . $in . '"></textarea></div>'
                . '<button type="submit" style="width:100%;padding:12px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Place order</button>'
                . '<p id="' . $uid . '-msg" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
                . '</form>';

            $payload = json_encode([
                'site'    => self::$slug,
                'item'    => $item['title'] ?? '',
                'slug'    => self::itemSlug($item, 'service'),
                'price'   => $item['price'] ?? '',
                'mrp'     => $item['mrp'] ?? '',
                'vlabel'  => $vlabel,
                'image'   => $cover ?: '',
            ]);
            $script = '<script>(function(){var U=' . json_encode($uid) . ',D=' . $payload . ';'
                . 'var f=document.getElementById(U+"-form"),n=document.getElementById(U+"-note"),m=document.getElementById(U+"-msg");'
                . 'function variant(){var v=document.getElementById(U+"-var");return v?v.value:"";}'
                . 'document.getElementById(U+"-cart").addEventListener("click",function(){'
                . 'var n2=window.tfCart.add({slug:D.slug,item:D.item,price:D.price,mrp:D.mrp,'
                . 'vlabel:D.vlabel,variant:variant(),image:D.image,qty:1});'
                . 'n.innerHTML="Added to cart ("+n2+") &middot; <a href=\"/cart\" style=\"color:inherit\">View cart</a>";});'
                . 'document.getElementById(U+"-buy").addEventListener("click",function(){f.style.display="block";f.scrollIntoView({behavior:"smooth",block:"center"});});'
                . 'f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button[type=submit]");b.disabled=true;b.textContent="Placing…";'
                . 'var fd={site:D.site,item:D.item,item_slug:D.slug,price:D.price,mrp:D.mrp,option_label:D.vlabel,option_value:variant(),'
                . 'name:f.name.value,phone:f.phone.value,email:f.email.value,note:(f.note?f.note.value:""),'
                . 'customer_token:(function(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+D.site))||{}).token||"";}catch(e){return "";}})()};'
                . 'fetch("' . self::apiBase() . '/api/sites/order-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(fd)})'
                . '.then(function(r){return r.json();}).then(function(res){'
                . 'if(res&&res.success){var oid=(res.data&&res.data.id)?res.data.id:"";m.style.color="#16a34a";m.textContent="✓ Order placed!"+(oid?" Your order number is #"+oid+".":"")+" We will contact you shortly.";f.reset();b.disabled=false;b.textContent="Place order";}'
                . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not place the order.";b.disabled=false;b.textContent="Place order";}})'
                . '.catch(function(){m.style.color="#dc2626";m.textContent="Connection error.";b.disabled=false;b.textContent="Place order";});});})();</script>';

            $orderHtml = $variantEl . $btnRow . $form . $script;
        }

        $reviewsHtml = self::reviewsBlock($item);

        $article = '<article class="tf-container" style="padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-bottom:22px;font-size:14px;font-weight:600;color:var(--color-primary);text-decoration:none">&larr; Back</a>'
            . '<div class="tf-two" style="align-items:start">'
            . '<div>' . $gallery . '</div>'
            . '<div style="text-align:left">'
            . (!empty($item['meta']) ? '<p style="margin:0 0 6px;font-size:13px;font-weight:600;color:var(--color-accent)">' . self::esc($item['meta']) . '</p>' : '')
            . '<h1 style="margin:0;font-family:var(--font-heading);font-size:32px;line-height:1.2;font-weight:700">' . self::esc($item['title'] ?? '') . '</h1>'
            . $priceHtml
            . $orderHtml
            . ($ctaHtml ? '<div style="margin-top:20px">' . $ctaHtml . '</div>' : '')
            . self::productInfoTable($item)
            . '<div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--color-border);font-size:16px;line-height:1.75">' . self::articleBody($item['body'] ?? '') . '</div>'
            . '</div></div>'
            . $reviewsHtml
            . self::relatedProducts($doc, $item, $backPath)
            . '</article>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * The cart page at /cart.
     *
     * The list itself only exists in the visitor's browser, so the server sends
     * an empty shell and the script fills it in. Checkout posts one request per
     * line, because order-submit.php records a single item per row -- that keeps
     * every line individually visible in the dashboard's Orders list.
     */
    private static function renderCartPage(array $doc): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/cart',
            'title' => 'Your cart',
            'seo'   => [
                'title'       => trim('Your cart | ' . $name, ' |'),
                'description' => '',
                'robots'      => 'noindex,nofollow',
            ],
        ];

        $in = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

        $form = '<form id="tf-cart-form" style="text-align:left">'
            . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Your details</p>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Name *</label><input name="name" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Contact number *</label><input name="phone" type="tel" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Email</label><input name="email" type="email" style="' . $in . '"></div>'
            . '<div style="margin-bottom:14px"><label style="' . $lb . '">Note (optional)</label><textarea name="note" rows="2" style="' . $in . '"></textarea></div>'
            . '<button type="submit" style="width:100%;padding:13px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Place order</button>'
            . '<p id="tf-cart-msg" role="status" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
            . '</form>';

        $summary = '<aside class="tf-card" style="padding:20px;text-align:left">'
            . '<div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;margin-bottom:16px">'
            . '<span>Total</span><span id="tf-cart-total">&mdash;</span></div>'
            . $form . '</aside>';

        $article = '<article class="tf-container" style="padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<h1 style="margin:0 0 24px;font-family:var(--font-heading);font-size:30px;font-weight:700">Your cart</h1>'
            . '<p id="tf-cart-empty" style="display:none;font-size:15px;color:var(--tf-text,var(--color-muted))">'
            . 'Your cart is empty. <a href="/" style="color:var(--color-primary);font-weight:600">Continue shopping</a></p>'
            . '<div id="tf-cart-wrap" style="display:none">'
            . '<div class="tf-two" style="align-items:start;text-align:left">'
            . '<div id="tf-cart-items"></div>' . $summary
            . '</div></div>'
            . '<div id="tf-cart-done" style="display:none;text-align:center;padding:40px 0">'
            . '<p style="font-size:20px;font-weight:700;color:#16a34a">&#10003; Order placed</p>'
            . '<p id="tf-cart-nums" style="margin-top:6px;font-size:14px;font-weight:600;color:var(--color-primary)"></p>'
            . '<p style="margin-top:8px;font-size:15px;color:var(--tf-text,var(--color-muted))">Thank you! We will contact you shortly to confirm.</p>'
            . '<a href="/" style="display:inline-block;margin-top:18px;font-size:14px;font-weight:600;color:var(--color-primary)">Continue shopping</a></div>'
            . '</article>';

        $script = str_replace(
            ['__API__', '__SITE__'],
            [self::apiBase(), self::esc(self::$slug)],
            self::cartPageScript()
        );

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::cartScript() . $script . self::animScript()
             . "</body></html>";
    }

    /** Built-in login / signup page — the account section wrapped in header/footer. */
    private static function renderAccountPage(array $doc): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        // Reuse an account section the customer placed on a page (for its heading
        // and phone toggle); otherwise synthesize a default one so the button
        // always lands on a real login/signup page.
        $acc = null;
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'account') { $acc = $s; break 2; }
            }
        }
        if (!$acc) {
            $acc = [
                'id'    => 'account',
                'type'  => 'account',
                'props' => ['heading' => 'My Account', 'sub' => 'Sign in or create an account to track your orders.'],
                'style' => ['pad' => 'lg'],
            ];
        }
        // Force it visible even if the source section was hidden.
        unset($acc['style']['hidden']);
        $acc['visible'] = true;

        $pseudo = [
            'slug'  => '/account',
            'title' => 'My Account',
            'seo'   => [
                'title'       => trim('My Account | ' . $name, ' |'),
                'description' => '',
                'robots'      => 'noindex,nofollow',
            ],
        ];

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">"
             .   self::chromeSection($doc, 'header')
             .   self::section($acc, $doc)
             .   self::chromeSection($doc, 'footer')
             . "</main>"
             . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    private static function cartPageScript(): string
    {
        return <<<'JS'
<script>(function(){
var SITE="__SITE__";
var wrap=document.getElementById("tf-cart-wrap"),list=document.getElementById("tf-cart-items"),
    empty=document.getElementById("tf-cart-empty"),done=document.getElementById("tf-cart-done"),
    totalEl=document.getElementById("tf-cart-total"),form=document.getElementById("tf-cart-form"),
    msg=document.getElementById("tf-cart-msg");
// Prices are free text ("Rs 1,299", "$40"), so pull the number out for the maths
// and reuse whatever prefix the customer typed when printing the total.
function num(p){var n=parseFloat(String(p||"").replace(/[^0-9.]/g,""));return isFinite(n)?n:0;}
function cur(p){var m=String(p||"").match(/^[^0-9]+/);return m?m[0].trim():"";}
function esc(t){var d=document.createElement("div");d.textContent=t==null?"":t;return d.innerHTML;}
function render(){
  var c=window.tfCart.read();
  if(!c.length){wrap.style.display="none";empty.style.display="";return;}
  empty.style.display="none";wrap.style.display="";
  var html="",total=0,sym="";
  for(var i=0;i<c.length;i++){
    var it=c[i],q=parseInt(it.qty,10)||1;
    total+=num(it.price)*q; if(!sym)sym=cur(it.price);
    html+='<div class="tf-card" style="display:flex;gap:14px;padding:14px;margin-bottom:12px;align-items:center">'
      +(it.image?'<img src="'+esc(it.image)+'" alt="" style="width:74px;height:74px;object-fit:cover;border-radius:var(--radius);flex-shrink:0">':'')
      +'<div style="flex:1;min-width:0">'
      +'<a href="/service/'+esc(it.slug)+'" style="font-size:15px;font-weight:700;color:inherit;text-decoration:none">'+esc(it.item)+'</a>'
      +(it.variant?'<p style="margin:2px 0 0;font-size:12px;color:var(--tf-text,var(--color-muted))">'+esc(it.vlabel||"Option")+': '+esc(it.variant)+'</p>':'')
      +(it.price?'<p style="margin:4px 0 0;font-size:14px;font-weight:700;color:var(--color-primary)">'+esc(it.price)+'</p>':'')
      +'<div style="margin-top:8px;display:flex;align-items:center;gap:8px">'
      +'<button type="button" data-act="dec" data-i="'+i+'" aria-label="Decrease quantity" style="width:26px;height:26px;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg);color:inherit;cursor:pointer">&minus;</button>'
      +'<span style="min-width:20px;text-align:center;font-size:14px;font-weight:700">'+q+'</span>'
      +'<button type="button" data-act="inc" data-i="'+i+'" aria-label="Increase quantity" style="width:26px;height:26px;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg);color:inherit;cursor:pointer">+</button>'
      +'<button type="button" data-act="del" data-i="'+i+'" style="margin-left:6px;font-size:12px;color:#dc2626;background:none;border:0;cursor:pointer">Remove</button>'
      +'</div></div></div>';
  }
  list.innerHTML=html;
  totalEl.textContent=total>0?((sym?sym+" ":"")+total.toLocaleString()):"—";
}
list.addEventListener("click",function(e){
  var b=e.target.closest?e.target.closest("button[data-act]"):null;if(!b)return;
  var c=window.tfCart.read(),i=parseInt(b.getAttribute("data-i"),10),a=b.getAttribute("data-act");
  if(!c[i])return;
  if(a==="inc")c[i].qty=(parseInt(c[i].qty,10)||1)+1;
  else if(a==="dec"){c[i].qty=(parseInt(c[i].qty,10)||1)-1;if(c[i].qty<1)c.splice(i,1);}
  else c.splice(i,1);
  window.tfCart.write(c);render();
});
form.addEventListener("submit",function(e){
  e.preventDefault();
  var c=window.tfCart.read();if(!c.length)return;
  var b=form.querySelector("button[type=submit]");b.disabled=true;b.textContent="Placing…";
  msg.style.color="";msg.textContent="";
  // One request per line so each item lands as its own order row.
  Promise.all(c.map(function(it){
    return fetch("__API__/api/sites/order-submit.php",{method:"POST",
      headers:{"Content-Type":"application/json","Accept":"application/json"},
      body:JSON.stringify({site:SITE,item:it.item,item_slug:it.slug,price:it.price,mrp:it.mrp||"",
        option_label:it.vlabel||"",option_value:it.variant||"",quantity:parseInt(it.qty,10)||1,
        name:form.name.value,phone:form.phone.value,email:form.email.value,note:form.note.value,
        customer_token:(function(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+SITE))||{}).token||"";}catch(e){return "";}})()})
    }).then(function(r){return r.json();});
  })).then(function(all){
    if(!all.every(function(r){return r&&r.success;}))throw new Error("partial");
    var ids=all.map(function(r){return (r.data&&r.data.id)?("#"+r.data.id):"";}).filter(Boolean);
    var nums=document.getElementById("tf-cart-nums");
    if(nums&&ids.length)nums.textContent=(ids.length>1?"Order numbers: ":"Order number: ")+ids.join(", ");
    window.tfCart.write([]);
    wrap.style.display="none";empty.style.display="none";done.style.display="";
  }).catch(function(){
    msg.style.color="#dc2626";msg.textContent="Could not place the order. Please try again.";
    b.disabled=false;b.textContent="Place order";
  });
});
if(document.readyState!=="loading")render();else document.addEventListener("DOMContentLoaded",render);
})();</script>
JS;
    }

    private static function secBlog(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $posts = $p['posts'] ?? [];
        if (!$posts) return '';

        // Build each card once — the grid, carousel and slider all reuse them.
        $slides = [];
        foreach ($posts as $b) {
            $img = self::media($b['image'] ?? null);
            $c = '<div class="tf-card">';
            if ($img) $c .= '<img src="' . self::esc($img) . '" alt="' . self::esc($b['title'] ?? '') . '" loading="lazy" style="height:200px;width:100%;object-fit:cover">';
            $c .= '<div style="padding:20px;text-align:left">';
            if (!empty($b['date'])) $c .= '<p style="margin:0 0 6px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($b['date']) . '</p>';
            $c .= '<h3 style="margin:0;font-family:var(--font-heading);font-size:19px;font-weight:600">' . self::esc($b['title'] ?? '') . '</h3>';
            if (!empty($b['excerpt'])) $c .= '<p style="margin:8px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($b['excerpt']) . '</p>';
            // A post with a full article opens its own page; otherwise fall back to
            // the external link if one was given.
            if (trim((string)($b['body'] ?? '')) !== '') {
                $l = ['text' => ($b['linkText'] ?? 'Read more'), 'href' => '/post/' . self::itemSlug($b, 'post'), 'style' => 'link'];
                $c .= '<div style="margin-top:14px">' . self::btn($l) . '</div>';
            } elseif (!empty($b['href'])) {
                $l = ['text' => ($b['linkText'] ?? 'Read more'), 'href' => $b['href'], 'style' => 'link', 'newTab' => true];
                $c .= '<div style="margin-top:14px">' . self::btn($l) . '</div>';
            }
            $c .= '</div></div>';
            $slides[] = $c;
        }

        $variant = $s['variant'] ?? 'grid-3';
        if ($variant === 'slider') {
            $body = self::marquee($slides);
        } elseif ($variant === 'carousel') {
            $body = self::carousel($slides);
        } else {
            $gcls = $variant === 'grid-2' ? 'tf-grid tf-c2' : 'tf-grid tf-c3';
            $body = '<div class="' . $gcls . '" style="text-align:left">' . implode('', $slides) . '</div>';
        }

        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $body;
        return self::shell($s, $inner);
    }

    private static function secAppointment(array $s, array $doc): string
    {
        $p   = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $wa  = preg_replace('/\D+/', '', (string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        $email  = trim((string)($biz['email'] ?? ''));
        $notify = $p['alsoNotify'] ?? 'whatsapp';
        if ($notify === 'whatsapp' && $wa === '') $notify = $email !== '' ? 'email' : 'none';
        $services = array_values(array_filter(array_map('trim', (array)($p['services'] ?? []))));
        $uid = 'ap' . substr(md5(($s['id'] ?? '') . 'apt'), 0, 6);
        $in  = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb  = 'display:block;margin-bottom:4px;font-size:12px;font-weight:600';

        // Times are picked in 12-hour AM/PM form; the API converts to 24h.
        $times = '<option value="">Select a time…</option>';
        for ($h = 0; $h < 24; $h++) {
            foreach ([0, 30] as $m) {
                $ampm  = $h < 12 ? 'AM' : 'PM';
                $hh    = ($h % 12) ?: 12;
                $times .= '<option>' . sprintf('%02d:%02d %s', $hh, $m, $ampm) . '</option>';
            }
        }

        $f  = '<div><label style="' . $lb . '">Name *</label><input class="' . $uid . '-f" data-k="name" required style="' . $in . '"></div>';
        $f .= '<div><label style="' . $lb . '">Phone *</label><input class="' . $uid . '-f" data-k="phone" type="tel" required style="' . $in . '"></div>';
        $f .= '<div><label style="' . $lb . '">Email</label><input class="' . $uid . '-f" data-k="email" type="email" style="' . $in . '"></div>';
        $f .= '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">'
            . '<div><label style="' . $lb . '">Date *</label><input class="' . $uid . '-f" data-k="date" type="date" required min="' . date('Y-m-d') . '" style="' . $in . '"></div>'
            . '<div><label style="' . $lb . '">Time *</label><select class="' . $uid . '-f" data-k="time" required style="' . $in . '">' . $times . '</select></div>'
            . '</div>';
        // Choosing a service is compulsory whenever services are configured.
        if ($services) {
            $opts = '<option value="">Select a service…</option>';
            foreach ($services as $sv) $opts .= '<option>' . self::esc($sv) . '</option>';
            $f .= '<div><label style="' . $lb . '">Service *</label><select class="' . $uid . '-f" data-k="service" required style="' . $in . '">' . $opts . '</select></div>';
        }
        $f .= '<div><label style="' . $lb . '">Message</label><textarea class="' . $uid . '-f" data-k="notes" rows="3" style="' . $in . '"></textarea></div>';

        $form = '<form id="' . $uid . '" style="display:grid;gap:12px;max-width:480px;margin:0 auto;text-align:left">' . $f
              . '<button type="submit" style="margin-top:4px;padding:12px;font-size:15px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">'
              . self::esc($p['submitText'] ?? 'Request appointment') . '</button>'
              . '<p id="' . $uid . '-msg" style="margin:0;font-size:13px;font-weight:600"></p></form>';

        $cfg = json_encode([
            'site'   => self::$slug,
            'api'    => self::apiBase(),
            'notify' => $notify,
            'wa'     => $wa,
            'email'  => $email,
        ]);

        $script = '<script>(function(){var U=' . json_encode($uid) . ',C=' . $cfg . ';'
            . 'var fm=document.getElementById(U),m=document.getElementById(U+"-msg");if(!fm)return;'
            . 'fm.addEventListener("submit",function(e){e.preventDefault();'
            . 'var d={};fm.querySelectorAll("."+U+"-f").forEach(function(el){d[el.getAttribute("data-k")]=(el.value||"").trim();});'
            . 'var b=fm.querySelector("button[type=submit]");b.disabled=true;var ob=b.textContent;b.textContent="Sending…";'
            . 'fetch(C.api+"/api/sites/appointment-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
            . 'body:JSON.stringify({site:C.site,name:d.name,phone:d.phone,email:d.email,date:d.date,time:d.time,service:d.service||"",notes:d.notes||""})})'
            . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;b.textContent=ob;'
            . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Appointment requested! We will confirm shortly.";'
            . 'var t=["Appointment request","Name: "+d.name,"Phone: "+d.phone];'
            . 'if(d.date)t.push("Date: "+d.date);if(d.time)t.push("Time: "+d.time);if(d.service)t.push("Service: "+d.service);if(d.notes)t.push("Message: "+d.notes);'
            . 'var txt=t.join("\n");'
            . 'if(C.notify==="whatsapp"&&C.wa){window.open("https://wa.me/"+C.wa+"?text="+encodeURIComponent(txt),"_blank");}'
            . 'else if(C.notify==="email"&&C.email){window.open("mailto:"+C.email+"?subject="+encodeURIComponent("Appointment request")+"&body="+encodeURIComponent(txt),"_blank");}'
            . 'fm.reset();}'
            . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not send the request.";}})'
            . '.catch(function(){b.disabled=false;b.textContent=ob;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>';

        $header  = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s));
        $variant = $s['variant'] ?? 'form';
        $img     = self::media($p['image'] ?? null);

        // Optional split layout: photo on one side, the form on the other.
        if (in_array($variant, ['image-left', 'image-right'], true) && $img) {
            $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'Appointment') . '" loading="lazy"'
                   . ' style="width:100%;border-radius:var(--radius);max-height:520px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
            // The form is centred inside its own column, so drop the auto margins.
            $col  = '<div style="text-align:left">' . str_replace('margin:0 auto;', '', $form) . '</div>';
            $cols = $variant === 'image-left' ? ($image . $col) : ($col . $image);
            return self::shell($s, $header . '<div class="tf-two" style="align-items:center;text-align:left">' . $cols . '</div>' . $script);
        }

        return self::shell($s, $header . $form . $script);
    }

    private static function secEmbed(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'grid-2';
        // Instagram's own embed is ~540px wide, which is far too big once it sits
        // in a multi-column grid. Cap each card to its column's share.
        $maxW = $variant === 'grid-3' ? 300 : ($variant === 'single' ? 540 : 380);

        $blocks = '';
        foreach (($p['embeds'] ?? []) as $it) {
            $url = trim((string)($it['url'] ?? ''));
            if ($url === '' || !preg_match('#instagram\.com#i', $url)) continue;
            $blocks .= '<blockquote class="instagram-media" data-instgrm-permalink="' . self::esc($url) . '" data-instgrm-version="14" style="background:#fff;border:0;border-radius:3px;box-shadow:0 0 1px rgba(0,0,0,.5),0 1px 10px rgba(0,0,0,.15);margin:0 auto;max-width:' . $maxW . 'px;min-width:0;width:100%;padding:0"></blockquote>';
        }
        if ($blocks === '') return '';
        $gcls = $variant === 'grid-3' ? 'tf-grid tf-c3' : ($variant === 'single' ? '' : 'tf-grid tf-c2');
        $grid = $gcls ? ('<div class="' . $gcls . '" style="justify-items:center">' . $blocks . '</div>') : ('<div style="max-width:' . $maxW . 'px;margin:0 auto">' . $blocks . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $grid
               . '<script async src="//www.instagram.com/embed.js"></script>'
               . '<script>if(window.instgrm&&window.instgrm.Embeds)window.instgrm.Embeds.process();</script>';
        return self::shell($s, $inner);
    }

    private static function secShare(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $host = $_SERVER['HTTP_HOST'] ?? (self::$slug . '.tapify.co.in');
        $url = trim((string)($p['url'] ?? '')) ?: ('https://' . $host);
        $enc = rawurlencode($url);
        $title = rawurlencode($doc['site']['name'] ?? 'Check this out');
        $light = self::isDarkBg($s);
        $col = $light ? '#fff' : 'var(--color-text)';
        $qr = '';
        if (($p['showQr'] ?? true) !== false) {
            // An uploaded QR wins; otherwise generate one from the link.
            $qsrc = self::media($p['qrImage'] ?? null)
                 ?: ('https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' . $enc);
            $qr = '<div style="display:inline-block;padding:14px;background:#fff;border-radius:var(--radius);box-shadow:0 8px 24px rgba(16,24,40,.12)"><img src="' . self::esc($qsrc) . '" alt="QR code" width="200" height="200" style="display:block;width:200px;height:200px;object-fit:contain"></div>';
        }
        $bStyle = 'display:inline-flex;align-items:center;gap:8px;padding:10px 16px;font-size:14px;font-weight:600;text-decoration:none;border-radius:var(--radius);border:1px solid var(--color-border);color:' . $col;
        $btns = [];
        if (($p['whatsapp'] ?? true) !== false) $btns[] = '<a href="https://wa.me/?text=' . $title . '%20' . $enc . '" target="_blank" rel="noopener" style="' . $bStyle . '">WhatsApp</a>';
        if (($p['facebook'] ?? true) !== false) $btns[] = '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $enc . '" target="_blank" rel="noopener" style="' . $bStyle . '">Facebook</a>';
        if (($p['twitter'] ?? true) !== false) $btns[] = '<a href="https://twitter.com/intent/tweet?url=' . $enc . '&text=' . $title . '" target="_blank" rel="noopener" style="' . $bStyle . '">X</a>';
        $uid = 'sh' . substr(md5(($s['id'] ?? '') . 'shr'), 0, 6);
        $btns[] = '<button type="button" id="' . $uid . '" data-url="' . self::esc($url) . '" style="' . $bStyle . ';background:none;cursor:pointer">Copy link</button>';
        $copyJs = '<script>(function(){var b=document.getElementById(' . json_encode($uid) . ');if(!b)return;b.addEventListener("click",function(){var u=b.getAttribute("data-url");var d=function(){var o=b.textContent;b.textContent="Copied!";setTimeout(function(){b.textContent=o;},1500);};if(navigator.clipboard){navigator.clipboard.writeText(u).then(d,d);}else{d();}});})();</script>';
        $header  = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, $light);
        $variant = $s['variant'] ?? 'default';
        $img     = self::media($p['image'] ?? null);

        // Optional split layout: photo on one side, QR + share buttons on the other.
        if (in_array($variant, ['image-left', 'image-right'], true) && $img) {
            $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'Share') . '" loading="lazy"'
                   . ' style="width:100%;border-radius:var(--radius);max-height:520px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
            $col = '<div style="text-align:center">'
                 . ($qr ? ('<div style="margin-bottom:22px">' . $qr . '</div>') : '')
                 . '<div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center">' . implode('', $btns) . '</div>'
                 . '</div>';
            $cols = $variant === 'image-left' ? ($image . $col) : ($col . $image);
            return self::shell($s, $header . '<div class="tf-two" style="align-items:center">' . $cols . '</div>' . $copyJs);
        }

        $inner = $header
               . ($qr ? ('<div style="margin-bottom:22px">' . $qr . '</div>') : '')
               . '<div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center">' . implode('', $btns) . '</div>' . $copyJs;
        return self::shell($s, $inner);
    }

    /* --------------------------------------------------------- carousel js */

    /** Wires every .tf-carousel: arrows, dots, autoplay (pause on hover), swipe. */
    /**
     * The cart itself. Kept in localStorage under a per-site key so one browser
     * can shop several Tapify sites without them bleeding into each other, and
     * exposed as window.tfCart so the product page, the header badge and the
     * cart page all read and write the same list.
     *
     * There is no server-side cart: an order only reaches the backend when the
     * customer checks out, which keeps published sites entirely static.
     */
    private static function cartScript(): string
    {
        $key = json_encode('tf_cart_' . self::$slug);
        return '<script>(function(){var K=' . $key . ';'
             . 'function read(){try{var c=JSON.parse(localStorage.getItem(K)||"[]");return Array.isArray(c)?c:[];}catch(e){return [];}}'
             . 'function write(c){try{localStorage.setItem(K,JSON.stringify(c));}catch(e){}badge();}'
             . 'function count(){var n=0,c=read();for(var i=0;i<c.length;i++)n+=(parseInt(c[i].qty,10)||1);return n;}'
             . 'function badge(){var n=count();Array.prototype.forEach.call(document.querySelectorAll("[data-tf-cart-count]"),'
             . 'function(el){el.textContent=n;el.style.display=n?"":"none";});}'
             // The same product in the same option is one line with a bigger qty,
             // not a second line.
             . 'function add(it){var c=read(),f=null;'
             . 'for(var i=0;i<c.length;i++){if(c[i].slug===it.slug&&(c[i].variant||"")===(it.variant||"")){f=c[i];break;}}'
             . 'if(f){f.qty=(parseInt(f.qty,10)||1)+(parseInt(it.qty,10)||1);}else{c.push(it);}write(c);return count();}'
             . 'window.tfCart={read:read,write:write,count:count,add:add,refresh:badge};'
             . 'window.addEventListener("storage",function(e){if(e.key===K)badge();});'
             . 'if(document.readyState!=="loading")badge();else document.addEventListener("DOMContentLoaded",badge);'
             . '})();</script>';
    }

    private static function carouselScript(): string
    {
        return <<<'JS'
<script>(function(){
function init(car){
  if(car.dataset.tfc)return;car.dataset.tfc="1";
  var track=car.querySelector(".tf-ctrack");if(!track)return;
  var slides=Array.prototype.slice.call(track.children),n=slides.length;if(n<2)return;
  var dots=car.querySelector(".tf-cdots"),prev=car.querySelector(".tf-cprev"),next=car.querySelector(".tf-cnext");
  function cur(){var c=track.scrollLeft+track.clientWidth/2,b=0,bd=Infinity;for(var i=0;i<n;i++){var e=slides[i],cc=e.offsetLeft+e.offsetWidth/2,d=Math.abs(cc-c);if(d<bd){bd=d;b=i;}}return b;}
  function go(i){i=(i%n+n)%n;var e=slides[i];track.scrollTo({left:e.offsetLeft,behavior:"smooth"});}
  if(dots){for(var i=0;i<n;i++){(function(k){var b=document.createElement("button");if(k===0)b.className="active";b.setAttribute("aria-label","Go to slide "+(k+1));b.addEventListener("click",function(){go(k);rest();});dots.appendChild(b);})(i);}}
  function sync(){var a=cur();if(dots)for(var i=0;i<dots.children.length;i++)dots.children[i].className=(i===a)?"active":"";}
  var tk=false;track.addEventListener("scroll",function(){if(!tk){requestAnimationFrame(function(){sync();tk=false;});tk=true;}},{passive:true});
  if(prev)prev.addEventListener("click",function(){go(cur()-1);rest();});
  if(next)next.addEventListener("click",function(){go(cur()+1);rest();});
  var iv=parseInt(car.getAttribute("data-autoplay"),10)||0,timer=null;
  function play(){stop();if(iv>0)timer=setInterval(function(){var a=cur();go(a>=n-1?0:a+1);},iv);}
  function stop(){if(timer){clearInterval(timer);timer=null;}}
  function rest(){play();}
  car.addEventListener("mouseenter",stop);car.addEventListener("mouseleave",play);
  car.addEventListener("touchstart",stop,{passive:true});
  play();
}
function boot(){var c=document.querySelectorAll(".tf-carousel");for(var i=0;i<c.length;i++)init(c[i]);}
if(document.readyState!=="loading")boot();else document.addEventListener("DOMContentLoaded",boot);
})();</script>
JS;
    }

    /** Reveal sections with a data-anim on scroll. Progressive enhancement: if
     *  JavaScript is off or IntersectionObserver is missing, nothing is ever
     *  hidden, so the content always shows. */
    private static function animScript(): string
    {
        return <<<'JS'
<script>(function(){var els=document.querySelectorAll("[data-anim]");if(!els.length)return;
if(!("IntersectionObserver" in window)){for(var i=0;i<els.length;i++)els[i].classList.add("tf-in");return;}
document.documentElement.classList.add("tf-anim-ready");
var io=new IntersectionObserver(function(en){en.forEach(function(e){if(e.isIntersecting){e.target.classList.add("tf-in");io.unobserve(e.target);}});},{threshold:0.12,rootMargin:"0px 0px -8% 0px"});
for(var i=0;i<els.length;i++)io.observe(els[i]);})();</script>
JS;
    }

    /** Count numbers up from 0 to their value when the stat scrolls into view. */
    private static function countScript(): string
    {
        return <<<'JS'
<script>(function(){var els=document.querySelectorAll(".tf-count[data-count]");if(!els.length)return;
function fmt(n){try{return n.toLocaleString("en-IN");}catch(e){return String(n);}}
var reduce=window.matchMedia&&window.matchMedia("(prefers-reduced-motion: reduce)").matches;
if(reduce||!("IntersectionObserver" in window)){return;}
function run(el){var target=parseFloat(el.getAttribute("data-count"))||0,done=false;
var io=new IntersectionObserver(function(en){en.forEach(function(e){if(e.isIntersecting&&!done){done=true;var dur=1600,start=null;
function tick(t){if(!start)start=t;var p=Math.min(1,(t-start)/dur);el.textContent=fmt(Math.round(target*(1-Math.pow(1-p,3))));if(p<1)requestAnimationFrame(tick);}
requestAnimationFrame(tick);io.disconnect();}});},{threshold:0.4});io.observe(el);}
for(var i=0;i<els.length;i++)run(els[i]);})();</script>
JS;
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
.tf-h1{font-size:34px;font-weight:700;line-height:1.1;color:var(--tf-heading,inherit)}
@media(min-width:768px){.tf-h1{font-size:48px}}
@media(min-width:1024px){.tf-h1{font-size:60px}}
.tf-h2{font-size:30px;font-weight:700;color:var(--tf-heading,inherit)}
@media(min-width:768px){.tf-h2{font-size:36px}}
.tf-lead{margin-top:16px;font-size:16px;line-height:1.6;opacity:.9;max-width:640px}
@media(min-width:768px){.tf-lead{font-size:18px}}
.tf-hero-wrap{max-width:768px}
.tf-al-center .tf-hero-wrap,.tf-al-center .tf-sub,.tf-al-center .tf-lead{margin-left:auto;margin-right:auto}
.tf-al-center .tf-btns{justify-content:center}
.tf-al-right .tf-hero-wrap,.tf-al-right .tf-sub,.tf-al-right .tf-lead{margin-left:auto}
.tf-al-right .tf-btns{justify-content:flex-end}
.tf-head{margin-bottom:40px}
.tf-eyebrow{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.14em;color:var(--tf-text,var(--color-accent));margin:0 0 8px}
.tf-sub{margin-top:12px;font-size:16px;line-height:1.6;color:var(--tf-text,var(--color-muted));max-width:640px}
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
.tf-mobile-only{display:flex}
.tf-brand-center{position:static}
.tf-center-top{display:flex;width:100%;align-items:center;justify-content:space-between}
@media(min-width:769px){.tf-mobile-only{display:none}.tf-brand-center{position:absolute;left:50%;transform:translateX(-50%)}.tf-center-top{justify-content:center}}
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
.tf-carousel{position:relative}
.tf-cviewport{position:relative}
.tf-ctrack{position:relative;display:flex;gap:24px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none}
.tf-ctrack::-webkit-scrollbar{display:none}
.tf-cslide{flex:0 0 85%;max-width:85%;scroll-snap-align:start}
@media(min-width:640px){.tf-cslide{flex:0 0 46%;max-width:46%}}
@media(min-width:1024px){.tf-cslide{flex:0 0 31%;max-width:31%}}
.tf-cprev,.tf-cnext{position:absolute;top:50%;transform:translateY(-50%);display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:999px;border:1px solid var(--color-border);background:var(--color-bg);color:var(--color-text);font-size:20px;line-height:1;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.12);z-index:2}
.tf-cprev{left:-6px}.tf-cnext{right:-6px}
.tf-cprev:hover,.tf-cnext:hover{opacity:.85}
.tf-cdots{display:flex;justify-content:center;gap:8px;margin-top:16px}
.tf-cdots button{width:8px;height:8px;padding:0;border:0;border-radius:999px;background:var(--color-border);cursor:pointer;transition:width .25s,background .25s}
.tf-cdots button.active{width:20px;background:var(--color-primary)}
.tf-marquee{overflow:hidden;position:relative}
.tf-mqtrack{display:flex;width:max-content;animation-name:tf-mqscroll;animation-timing-function:linear;animation-iteration-count:infinite}
.tf-marquee:hover .tf-mqtrack{animation-play-state:paused}
.tf-mqslide{flex:0 0 260px;margin-right:24px}
@media(min-width:768px){.tf-mqslide{flex:0 0 320px}}
@keyframes tf-mqscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@media(prefers-reduced-motion:reduce){.tf-mqtrack{animation:none;overflow-x:auto;max-width:100%}}
.tf-pgal{display:flex;gap:14px;align-items:flex-start}
.tf-pthumbs{display:flex;flex-direction:column;gap:10px;width:78px;flex-shrink:0;max-height:520px;overflow-y:auto;scrollbar-width:thin}
.tf-pthumb{padding:0;width:78px;height:78px;border:2px solid var(--color-border);border-radius:8px;background:var(--color-surface);cursor:pointer;overflow:hidden;flex-shrink:0}
.tf-pthumb.is-active{border-color:var(--color-primary)}
.tf-pthumb img{width:100%;height:100%;object-fit:cover;display:block}
.tf-pmain{flex:1;min-width:0;display:flex;align-items:center;justify-content:center;height:520px;background:var(--color-surface);border-radius:var(--radius);overflow:hidden}
.tf-pmain img{max-width:100%;max-height:100%;object-fit:contain}
@media(max-width:640px){.tf-pgal{flex-direction:column-reverse}.tf-pthumbs{flex-direction:row;width:auto;max-height:none;overflow-x:auto}.tf-pmain{height:340px}}
.tf-bsgrid{display:grid;grid-template-columns:repeat(2,1fr);gap:26px;margin-top:34px;text-align:left}
@media(min-width:640px){.tf-bsgrid{grid-template-columns:repeat(3,1fr)}}
@media(min-width:1024px){.tf-bsgrid{grid-template-columns:repeat(4,1fr)}}
.tf-bscard{display:block;color:inherit;text-decoration:none}
.tf-bscard img{width:100%;height:230px;object-fit:cover;border-radius:6px}
.tf-bstitle{margin:14px 0 0;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.01em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tf-bsprice{margin:8px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:8px;font-size:13px}
.tf-bssell{font-size:17px;font-weight:800}
.tf-bsprice s{color:var(--tf-text,var(--color-muted))}
.tf-bsoff{font-weight:800;color:#dc2626}
.tf-anim-ready [data-anim]{opacity:0;transition:opacity .7s ease,transform .7s ease;will-change:opacity,transform}
.tf-anim-ready [data-anim="slide-up"]{transform:translateY(34px)}
.tf-anim-ready [data-anim="zoom"]{transform:scale(.93)}
.tf-anim-ready [data-anim].tf-in{opacity:1;transform:none}
@media(prefers-reduced-motion:reduce){.tf-anim-ready [data-anim]{opacity:1;transform:none;transition:none}}
CSS;
    }
}
