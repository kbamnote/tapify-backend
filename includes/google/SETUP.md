# Google Business Profile integration — setup

Two-way sync of a customer's Google Business Profile (name, description, phone,
website) from the Tapify app. Backend does server-side OAuth; the app opens the
consent page in a browser and returns via the `tapifapp://` deep link.

## 0. Run the migration
Open `run-gbp-migration.php` in a browser (creates the two tables), then delete it.

## 1. Google Cloud project
1. https://console.cloud.google.com → create/select a project.
2. **APIs & Services → Library** → enable:
   - *My Business Account Management API*
   - *My Business Business Information API*

## 2. Request API access (THE GATE — do this early)
The Business Profile APIs are allowlisted. Until approved, calls return
`PERMISSION_DENIED` with **0 quota**, even though the APIs are "enabled".
- Submit the access-request form: https://developers.google.com/my-business/content/prereqs
  (Google → "Business Profile APIs" access request). Approval can take days–weeks.

## 3. OAuth consent screen
1. **APIs & Services → OAuth consent screen** → External.
2. Add the scope `https://www.googleapis.com/auth/business.manage` (restricted).
3. While unverified, add your testers under **Test users** — only they can connect.
4. For public launch, submit for **OAuth verification** (required for restricted scopes).

## 4. OAuth client (Web)
1. **APIs & Services → Credentials → Create credentials → OAuth client ID → Web application**.
2. **Authorized redirect URI** — must match exactly:
   ```
   https://app.tapify.co.in/api/google/gbp/callback.php
   ```
3. Copy the **Client ID** and **Client secret**.

## 5. Backend environment variables
```
GOOGLE_CLIENT_ID=<client id>
GOOGLE_CLIENT_SECRET=<client secret>
# optional overrides (defaults shown):
GOOGLE_OAUTH_REDIRECT=https://app.tapify.co.in/api/google/gbp/callback.php
APP_DEEP_LINK=tapifapp://gbp-connected
```
Until `GOOGLE_CLIENT_ID`/`SECRET` are set, the app shows "Not available yet".

## 6. App
The `tapifapp` scheme is already registered (app.json), so the OAuth callback
deep-links back automatically. Rebuild the app so the new screen ships.

## Endpoints
`api/google/gbp/` — `connect-url` (get consent URL), `callback` (browser return),
`status`, `location` (GET fields), `update` (PATCH fields), `locations`,
`select-location`, `disconnect`.

## Notes / next phases
- Editable now: business name, description, phone, website. Categories, hours and
  address are shown read-only (structured writes = phase 2).
- Replying to reviews needs the older, separately-gated Business Profile v4 API.
- Tokens are stored per user in `google_business_connections`; consider encrypting
  `refresh_token` at rest for extra safety.
