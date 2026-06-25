# Wildcard subdomain setup (`*.tapify.co.in`)

Goal: serve each business mini-site at **`https://<slug>.tapify.co.in`** while keeping
the existing **`https://app.tapify.co.in/<slug>`** (and `https://tapify.co.in/<slug>`)
URLs working.

**Stack:** backend = PHP on **Railway** (Nixpacks → Nginx + PHP‑FPM, front controller
`index.php`). Frontend/dashboard = **Vercel** (`tapify.co.in` + `www`). MySQL on Railway.

The application code already resolves the slug from **either** the path **or** the
subdomain (`tapify_subdomain_slug()` in `config/database.php`, used by `index.php`).
There is **no web-server config file to edit on Railway** — `.htaccess` is ignored by
Nginx; it is kept only for an Apache fallback. The only work is: a custom domain on
Railway, one DNS record, and one env var.

## What the code does
- `index.php`: if the request has no path slug, it derives the slug from the host
  (`<slug>.tapify.co.in`). `app`, `www`, `api`, `dashboard`, … are reserved and
  ignored (`tapify_reserved_subdomains()`), so they keep their current behavior.
- `public_card_url($alias)` builds the canonical public URL — subdomain form when
  `USE_SUBDOMAIN_URLS=1`, otherwise the legacy path form.
- Slug → subdomain is **1:1 and lossless**: `business-slug` → `business-slug.tapify.co.in`
  (hyphens are valid DNS labels). Slugs that aren't valid DNS labels fall back to the
  path URL automatically. No database or UI changes.

## 1. Railway — add the wildcard custom domain
On the **backend service** (the same one already serving `app.tapify.co.in`):
- **Settings → Networking → Custom Domain → add** `*.tapify.co.in`.
- Railway shows a **CNAME target** (e.g. `abcd1234.up.railway.app`) and provisions a
  **wildcard TLS certificate automatically** — no manual SSL.
- Keep the existing `app.tapify.co.in` domain on the same service; both coexist and
  the one PHP app handles every host (it reads `HTTP_HOST`).

## 2. DNS — add the wildcard record
Wherever `tapify.co.in` DNS is authoritative (registrar / Vercel DNS / Cloudflare),
add the record using the target Railway gave you in step 1:

```
Type    Name   Value
CNAME   *      <the-target-railway-showed>.up.railway.app
```
- Leave the existing `@` (apex → Vercel), `www` (→ Vercel) and `app` (→ Railway)
  records untouched — an explicit record always beats the wildcard.
- If your DNS host rejects a wildcard CNAME, use the A/ALIAS value Railway provides
  instead (Railway documents this per domain).

## 3. Railway — set the env var
Backend service → **Variables**:
- `USE_SUBDOMAIN_URLS=1` once steps 1–2 are verified (this makes the dashboard/API
  advertise the `slug.tapify.co.in` URLs). Set `0` if you deploy before DNS/TLS are
  live, then flip to `1` afterwards. (Code default is `1`.)
- `PUBLIC_BASE_DOMAIN=tapify.co.in` is optional (already the default).
- A push to the connected repo triggers a Railway redeploy that picks up the new code.

## 4. Vercel (frontend) — no change required
- The wildcard `*.tapify.co.in` points at **Railway**, so business subdomains bypass
  Vercel entirely. Apex + `www` stay on Vercel.
- The existing `tapify.co.in/<slug>` → `app.tapify.co.in/<slug>` proxy in `vercel.json`
  keeps the path URLs working — leave it as-is.
- **Do NOT** add `*.tapify.co.in` as a Vercel domain (that would send subdomains to
  Vercel instead of Railway).
- The `vcard-edit.js` helper ships on the next Vercel deploy (auto on git push). To
  flip the dashboard toast to subdomain form, `USE_SUBDOMAIN_URLS` is already `true`
  there.

## 5. Deployment order
1. Deploy backend code (env `USE_SUBDOMAIN_URLS=0` if DNS/TLS not ready yet — routing
   still works, only the *displayed* URL stays legacy).
2. Railway custom domain (step 1) + DNS record (step 2). Wait for Railway to show the
   domain as **Active / cert issued**.
3. Verify: `https://<an-existing-slug>.tapify.co.in` loads the mini-site, and
   `https://app.tapify.co.in/<slug>` still works.
4. Set `USE_SUBDOMAIN_URLS=1` so new share links/QRs use the subdomain form.

## Notes / fallbacks
- If Railway wildcard domains are unavailable on your plan, the alternative is to send
  `*.tapify.co.in` through Vercel and add a Vercel rewrite
  `{"source":"/(.*)","has":[{"type":"host","value":"(?<s>[^.]+)\\.tapify\\.co\\.in"}],"destination":"https://app.tapify.co.in/:s"}`
  — but Railway-native wildcard is simpler and avoids an extra hop.
- The on-card QR code (`templates/vcard*.php`) still encodes the path URL, which keeps
  working. To switch those to the subdomain form too, replace each template's
  `$cardUrl = 'https://app.tapify.co.in/'.(...)` with
  `$cardUrl = public_card_url($vcard['url_alias'] ?? $vcardId);` (optional).
- The committed `.htaccess` front-controller rewrite only matters if you ever move the
  backend to Apache; on Railway/Nginx it is ignored and harmless.
