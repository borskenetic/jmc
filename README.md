# CMU Library Attendance System

Laravel application for library **attendance scanning**, patron (student) records, employee records, ID cards, attendance logs, and admin tools.

Repository: [github.com/borskenetic/attendance-system](https://github.com/borskenetic/attendance-system)

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ (or MariaDB)
- GD extension (QR codes on ID cards and student edit preview)
- Optional: Imagick (not required)

## Quick start (new database)

```bash
git clone https://github.com/borskenetic/attendance-system.git
cd attendance-system

composer install
cp .env.example .env
php artisan key:generate
```

Create an empty MySQL database, then set `.env`:

```env
DB_DATABASE=cmu_local
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and optional sample data:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Default seeded data

- Sample **programs**, **5 students**, **5 employees** (see `database/seeders/`)
- Roles: `student`, `faculty` (for employee records)

Create your first **login user** (not seeded by default):

```bash
php artisan db:seed --class=AdminUserSeeder
```

Default (change after first login): `admin@library.local` / `password`

Or use **Admin → Create Account** after creating one user manually in tinker.

## Migrations

Active migrations live in `database/migrations/` as `2026_05_22_000001` … `000015`.

Older library/book migrations are kept in `database/migrations/_retired/` and are **not** run by Laravel (subfolder is ignored).

| Step | Command |
|------|---------|
| Fresh install | `php artisan migrate` |
| Reset local DB | `php artisan migrate:fresh` (drops all tables) |
| With samples | `php artisan migrate:fresh --seed` |

See [database/migrations/README.md](database/migrations/README.md) for the table list.

**Verified:** `php artisan migrate:fresh --seed` completes successfully on a clean database.

## Main routes

| URL | Description |
|-----|-------------|
| `/` | Public home / FAQ |
| `/attendance` | Kiosk scanner |
| `/login` | Staff / admin login |
| `/students` | Student list |
| `/employees` | Employee list |
| `/attendance-logs` | Attendance logs & reports |
| `/view-users` | User accounts (admin) |

## Branding

Edit colors and fonts in `public/branding/branding.css` or set `BRANDING_CSS` in `.env`.

**Hostinger / production:** After editing branding CSS, deploy `public/branding/branding.css` (commit + pull, or upload via FTP). Then run `php artisan config:clear` on the server if you changed `BRANDING_CSS` in `.env`. Purge **LiteSpeed Cache** in hPanel if colors still look old. The app appends `?v=` (file modification time) to the stylesheet URL so browsers fetch the latest file after deploy.

## ID card assets

Student IDs use **three template sets** (selected from each student’s `educational_level`):

| Folder | Used for |
|--------|----------|
| `images/id_templates/grade_school/` | Grade school patrons |
| `images/id_templates/high_school/` | Junior & senior high school |
| `images/id_templates/college/` | College patrons |

Each folder needs `front.png` and `back.png`. Legacy files at `images/id_templates/front.png` and `back.png` still work as fallbacks for college.

Text positions, barcode/QR sizes, and photo placement are configured **per level** in `config/id_cards.php` under `student_levels` (each level has its own `front`, `back`, and optional `photo` blocks). Coordinates are in pixels matching that level’s template image size.

After adding legacy templates, run:

```bash
php artisan id-cards:init-student-templates
```

That copies them into all three folders so you can customize each design. Employee templates stay at `front_employee.png` / `back_employee.png`. Template paths are gitignored; copy from your design files after clone.

## Optional SMS modem

Set in `.env`:

```env
SMS_MODEM_URL=http://127.0.0.1:5000/send-sms
SMS_MODEM_API_KEY=your-secret-api-key
```

## Push / deploy notes

- Never commit `.env`
- Run `composer install --no-dev` on production
- Set `APP_DEBUG=false`, `APP_ENV=production`
- `php artisan config:cache` and `php artisan route:cache` after deploy

## License

MIT (Laravel framework components retain their respective licenses.)
