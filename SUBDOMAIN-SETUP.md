# Wildcard subdomain setup (`*.tapify.co.in`)

Goal: serve each business mini-site at **`https://<slug>.tapify.co.in`** while keeping
the existing **`https://app.tapify.co.in/<slug>`** (and `https://tapify.co.in/<slug>`)
URLs working.

The application code already resolves the slug from **either** the path **or** the
subdomain (see `tapify_subdomain_slug()` in `config/database.php`, used by
`index.php` / `router.php`). The only thing left is infrastructure so that
`*.tapify.co.in` requests reach the same PHP front controller.

## What the code does
- `index.php` / `router.php`: if the request has no path slug, it derives the slug
  from the host (`<slug>.tapify.co.in`). `app`, `www`, `api`, `dashboard`, ... are
  reserved and ignored (see `tapify_reserved_subdomains()`).
- `public_card_url($alias)` builds the canonical public URL. It returns the
  subdomain form only when `USE_SUBDOMAIN_URLS=1`; otherwise the legacy path form.
- Slug → subdomain is a **1:1, lossless** mapping. A slug like `business-slug`
  becomes `business-slug.tapify.co.in` (hyphens are valid DNS labels). Slugs that
  are not valid DNS labels automatically fall back to the path URL.

## 1. DNS (where `tapify.co.in` is managed)
Add a wildcard record that points to the **backend** host (the same server/IP that
serves `app.tapify.co.in`). Leave the existing `@`, `www` (Vercel) and `app`
records untouched — an explicit record always wins over the wildcard.

```
Type   Name   Value                     TTL
A      *      <backend server IP>       3600      # IPv4
AAAA   *      <backend server IPv6>     3600      # only if you serve IPv6
```

(If the backend is reached via a hostname rather than a static IP, use
`CNAME  *  app.tapify.co.in.` instead — provided your DNS host allows wildcard CNAME.)

## 2. Wildcard TLS certificate
A normal cert for `app.tapify.co.in` does **not** cover subdomains. Issue a cert
that includes `*.tapify.co.in`.

- **Hostinger / cPanel:** add `*.tapify.co.in` as a subdomain pointed at the backend's
  document root, then issue/enable the wildcard SSL (AutoSSL / Let's Encrypt wildcard).
- **Let's Encrypt (certbot), self-managed:** wildcard requires a DNS-01 challenge:
  ```
  certbot certonly --manual --preferred-challenges=dns \
    -d 'tapify.co.in' -d '*.tapify.co.in'
  ```

## 3. Web server vhost — point the wildcard at the SAME docroot as `app`
The wildcard host must serve the existing backend document root (no separate site).

### Apache (Hostinger is Apache; `.htaccess` already does the front-controller rewrite)
```apache
<VirtualHost *:443>
    ServerName   app.tapify.co.in
    ServerAlias  *.tapify.co.in
    DocumentRoot /home/<user>/public_html        # existing backend docroot
    SSLEngine on
    SSLCertificateFile      /path/fullchain.pem
    SSLCertificateKeyFile   /path/privkey.pem
    <Directory /home/<user>/public_html>
        AllowOverride All                          # so .htaccess rewrite applies
        Require all granted
    </Directory>
</VirtualHost>
```
The rewrite that sends every non-file request to `index.php` lives in
`.htaccess` (committed in this repo). It is host-agnostic, so subdomains need no
extra rules.

### Nginx (equivalent, if not Apache)
```nginx
server {
    listen 443 ssl;
    server_name app.tapify.co.in *.tapify.co.in;
    root /var/www/tapify-backend;          # existing backend docroot
    index index.php;

    ssl_certificate     /path/fullchain.pem;
    ssl_certificate_key /path/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;   # front controller
    }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## 4. Vercel (frontend `tapify.co.in`) — no change required
The wildcard `*.tapify.co.in` points at the backend, so business subdomains bypass
Vercel entirely. The apex/`www` dashboard and the existing
`tapify.co.in/<slug>` → `app.tapify.co.in/<slug>` proxy in `vercel.json` keep
working. (Do **not** add `*.tapify.co.in` as a Vercel domain.)

## 5. Deployment order (avoid showing links that don't resolve yet)
1. Deploy the backend code with **`USE_SUBDOMAIN_URLS=0`** (env var). Subdomain
   *routing* already works; the dashboard still shows the legacy path URLs.
2. Add the wildcard **DNS** record (step 1) and the wildcard **TLS** cert (step 2).
3. Verify: `https://<an-existing-slug>.tapify.co.in` loads the mini-site.
4. Flip **`USE_SUBDOMAIN_URLS=1`** (and `USE_SUBDOMAIN_URLS = true` in
   `tapify-frontend/vcard-edit.js`) so new share links/QRs use the subdomain form.

## Notes
- No database or UI changes are required; `url_alias` is reused as the subdomain.
- The on-card QR code (in `templates/vcard*.php`) still encodes the path URL, which
  keeps working. To switch those to the subdomain form too, replace the per-template
  `$cardUrl = 'https://app.tapify.co.in/'.(...)` line with
  `$cardUrl = public_card_url($vcard['url_alias'] ?? $vcardId);` (optional).
