/**
 * TAPIFY — Cloudflare Worker that serves a customer's own domain.
 *
 * WHY THIS EXISTS
 * Railway caps custom domains per service and both slots on tapify-backend are
 * already used (app.tapify.co.in and the *.tapify.co.in wildcard). So customer
 * domains are never added to Railway. This Worker sits on the customer's domain
 * and fetches the page from the site's ordinary Tapify subdomain instead:
 *
 *   www.galaxycardecor.com  ->  this Worker  ->  galaxycardecor.tapify.co.in
 *
 * Railway therefore never learns the custom domain exists, the cap never
 * applies, and Cloudflare issues the HTTPS certificate for free.
 *
 * ── SETUP, ONCE PER CUSTOMER ──────────────────────────────────────────────
 *  1. Add the customer's domain to Cloudflare (free plan is enough) and point
 *     the registrar at Cloudflare's nameservers.
 *  2. DNS: add a PROXIED (orange cloud) record for the domain. The target does
 *     not matter — this Worker replaces the response — but Cloudflare needs a
 *     record to exist. An AAAA to 100:: is the usual placeholder.
 *  3. Deploy this Worker and add a route for  example.com/*  and  www.example.com/*
 *  4. Set the variable TAPIFY_ORIGIN to the site's subdomain, e.g.
 *     galaxycardecor.tapify.co.in
 *  5. In Tapify: POST /api/sites/set-domain.php {site_id, domain}
 *     then POST the same with {action:"verify"} to confirm and go live.
 *
 * One Worker per customer is simplest. To run several domains from a single
 * Worker, replace the TAPIFY_ORIGIN lookup with a map of hostname -> subdomain.
 */

export default {
  async fetch(request, env) {
    const origin = env.TAPIFY_ORIGIN;           // e.g. "galaxycardecor.tapify.co.in"
    if (!origin) {
      return new Response('TAPIFY_ORIGIN is not set on this Worker.', { status: 500 });
    }

    const incoming = new URL(request.url);
    const target = new URL(request.url);
    target.hostname = origin;
    target.protocol = 'https:';
    target.port = '';

    // Rebuild the request against the Tapify subdomain. The visitor's real
    // hostname travels in X-Forwarded-Host: SiteRenderer uses it for canonical
    // tags, share links and the QR code, but ONLY after checking it matches the
    // domain recorded for that site — so this header cannot be used to hijack
    // another customer's canonical tags.
    const headers = new Headers(request.headers);
    headers.set('X-Forwarded-Host', incoming.host);
    headers.set('X-Forwarded-Proto', 'https');
    // Host must be the origin's, or Railway will not route the request at all.
    headers.set('Host', origin);

    const upstream = new Request(target.toString(), {
      method: request.method,
      headers,
      body: ['GET', 'HEAD'].includes(request.method) ? undefined : request.body,
      redirect: 'manual',                       // handled below
    });

    let res;
    try {
      res = await fetch(upstream);
    } catch (e) {
      return new Response('Upstream unreachable: ' + e.message, { status: 502 });
    }

    // Any redirect the origin issues points at the Tapify subdomain. Rewrite it
    // back to the customer's domain, otherwise a form post or a trailing-slash
    // redirect quietly moves the visitor off their own site.
    const out = new Headers(res.headers);
    const loc = out.get('Location');
    if (loc) {
      try {
        const l = new URL(loc, target);
        if (l.hostname === origin) {
          l.hostname = incoming.hostname;
          out.set('Location', l.toString());
        }
      } catch (_) { /* leave a malformed Location alone */ }
    }
    // Cookies scoped to the origin host would be dropped by the browser.
    if (out.has('Set-Cookie')) {
      out.delete('Set-Cookie');
    }

    return new Response(res.body, {
      status: res.status,
      statusText: res.statusText,
      headers: out,
    });
  },
};
