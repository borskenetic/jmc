# Uploading to a new Git repository

Use this checklist before the first push to a **new** remote (GitHub, GitLab, etc.).

## 1. Secrets and local-only files

| Item | Action |
|------|--------|
| `.env` | Must **never** be committed (already in `.gitignore`). Copy from `.env.example` on each machine. |
| Production `.env` | Create only on the server. Use `APP_ENV=production`, `APP_DEBUG=false`, real DB credentials. |
| Old repo / chat | If `.env` or API keys ever appeared in git history or chat, **rotate** DB passwords, `APP_KEY`, mail, SMS, and SSO keys before going public. |

## 2. What this repo includes

- Full Laravel app (ACD library attendance, students, employees, kiosk scanner, ID cards).
- Branding: `public/branding/branding.css`, logos under `public/images/`.
- Deploy notes: `deploy/hostinger/README.md`.
- **Not** included: `vendor/` (run `composer install`), user uploads, ID template PNGs (see `public/images/id_templates/README.md`).

## 3. Create the empty remote

On GitHub: **New repository** → name e.g. `acd-attendance` → **do not** add a README if you already have one locally.

## 4. Point git at the new remote and push

From the project root (PowerShell):

```powershell
cd d:\attendance-system

# Optional: drop link to the old remote
git remote remove origin

git remote add origin https://github.com/YOUR_ORG/YOUR_REPO.git

git add -A
git status
# Confirm .env is NOT listed

git commit -m "Initial commit: ACD library attendance system"
git push -u origin main
```

If the new repo uses `master` instead of `main`:

```powershell
git branch -M main
git push -u origin main
```

## 5. After clone (you or teammates)

```bash
composer install
cp .env.example .env   # Windows: copy .env.example .env
php artisan key:generate
# Edit .env with database credentials
php artisan migrate
php artisan db:seed --class=AdminUserSeeder   # optional first admin
php artisan serve
```

Production: `composer install --no-dev`, `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`. See `README.md` and `deploy/hostinger/README.md`.

## 6. Optional: fresh git history

Only if you need a **single** clean commit with no old history (e.g. leaked secrets in past commits):

```powershell
Remove-Item -Recurse -Force .git
git init
git add -A
git commit -m "Initial commit: ACD library attendance system"
git branch -M main
git remote add origin https://github.com/YOUR_ORG/YOUR_REPO.git
git push -u origin main
```

## 7. Hostinger deploy reminder

- Document root: `public_html/acd/public` (not `public_html/acd/` alone).
- Edit assets under **`public/`** (`public/branding/`, `public/css/`, `public/images/`).
- Do not commit `default.php` (Hostinger placeholder; ignored by git).
