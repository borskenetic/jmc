# Hostinger deployment (pantas.org + subdomains)

This project is set up for Hostinger’s usual layout:

| Site | File Manager path | Example URL |
|------|-------------------|-------------|
| Main domain | `public_html/` | `https://pantas.org` |
| Subdomain `acd` | `public_html/acd/` | `https://acd.pantas.org` |

Each subdomain is a **separate copy** of the app (or its own deploy) inside `public_html/{subdomain}/`.

---

## Recommended layout (subdomain `acd`)

Upload the **full Laravel project** into `public_html/acd/`:

```
public_html/acd/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/              ← document root must point here
│   ├── index.php
│   ├── .htaccess
│   ├── images/          ← Bannernew.jpg, logos, etc.
│   ├── branding/
│   └── css/
├── resources/
├── routes/
├── storage/             ← writable (775)
├── vendor/
├── .env
└── deploy/
```

### Point the subdomain to `public/`

In **hPanel → Websites → Subdomains → acd.pantas.org →** set **Document root** to:

```text
public_html/acd/public
```

Not `public_html/acd` alone. That way URLs are `https://acd.pantas.org/...` (no `/public` in the browser).

### `.env` on the server (`public_html/acd/.env`)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://acd.pantas.org

# Leave null unless you share cookies across *.pantas.org
SESSION_DOMAIN=null
SESSION_PATH=/
```

After editing `.env`:

```bash
cd ~/domains/pantas.org/public_html/acd   # path may vary; use File Manager SSH path
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Optional after deploy: `php artisan config:cache` and `php artisan route:cache`.

---

## If Hostinger will not let you set document root to `public/`

Use the **subdomain root** `.htaccess` so `public_html/acd/` forwards to `public/`:

1. Copy `deploy/hostinger/subdomain-root.htaccess` to `public_html/acd/.htaccess`.
2. Keep the full Laravel tree under `public_html/acd/` (including the `public` folder).

Do **not** copy `public/index.php` to `acd/index.php` unless you know you need the legacy layout.

---

## Main domain `pantas.org` (`public_html/`)

Same idea:

- Either document root = `public_html/public` (Laravel app one level above `public_html` — less common on Hostinger), **or**
- Laravel lives in `public_html/` with document root = `public_html/public` if the repo is inside a subfolder.

Most teams use the **main site** for marketing and a **subdomain per school** (`acd`, etc.) with the table above.

---

## Static files & cache (banners, branding)

**Important:** Laravel serves files from the **`public/`** folder only.

| Correct (edit this) | Wrong (File Manager trap — not used by the app) |
|---------------------|--------------------------------------------------|
| `public_html/acd/public/branding/branding.css` | `public_html/acd/branding/branding.css` |
| `public_html/acd/public/images/Bannernew.jpg` | `public_html/acd/images/Bannernew.jpg` |

If you edit `acd/branding/` (without `public/`), the live site will **not** change — or you will see **two different CSS files**:

- `https://acd.pantas.org/branding/branding.css?v=…` → usually `public/branding/` (with cache-bust)
- `https://acd.pantas.org/branding/branding.css` (no `?v=`) → often an **old LiteSpeed-cached** copy, or a leftover gold-theme file from an older deploy

**Fix:** Edit only `public_html/acd/public/branding/branding.css`, delete the duplicate `public_html/acd/branding/` folder (or keep a backup copy elsewhere), purge LiteSpeed Cache, and confirm every page `<link>` includes `?v=`.

The app adds `?v=` via `Branding::stylesheetUrl()`. The version is an **MD5 of the file contents** (not the old file-modification timestamp), so when you save new colors the `?v=` value changes automatically. If LiteSpeed still serves stale CSS for the old `?v=`, purge cache once or set `BRANDING_ASSET_VERSION=2` in `.env` and run `php artisan config:clear`.

---

## Checklist after upload

1. `composer install --no-dev` (SSH) in `public_html/acd/`.
2. Copy `.env.example` → `.env`, set `APP_URL`, database, `APP_KEY`.
3. `php artisan key:generate` (once).
4. `php artisan migrate --force`.
5. Permissions: `storage/` and `bootstrap/cache/` writable.
6. Confirm document root is `.../acd/public`.
7. Open `https://acd.pantas.org/up` — should return OK (health check).

---

## SSH paths vs File Manager

Hostinger may show:

- File Manager: `public_html/acd/`
- SSH: `~/domains/pantas.org/public_html/acd/` or similar

They are the same folder; only the prefix differs.
