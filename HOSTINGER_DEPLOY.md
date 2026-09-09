# Hostinger Deploy + Dashboard Setup

## 1. Upload files
Upload everything in this repo to `public_html/` (or a subfolder) via hPanel File Manager or FTP.

## 2. Create MySQL database
hPanel → Databases → MySQL Databases → create a database + user, note down:
host, database name, username, password.

## 3. Import schema
phpMyAdmin → select your database → Import → upload `sql/schema.sql`.

This creates the `admin_users` and `content_items` tables and seeds:
- Default login: **username `admin`, password `ChangeMe123!`**
- Real experience entries (APU current role, Sonargaon previous role)

## 4. Configure database connection
Copy `config/db.sample.php` to `config/db.php` and fill in your real credentials
from step 2. `config/db.php` is gitignored — never commit it.

## 5. Import existing publications/media
Log in at `yourdomain.com/admin/login.php`, then visit
`yourdomain.com/admin/import_seed.php` once — it migrates
`data/publications.json` and `data/media.json` (81 publications + media items)
into the database. Safe to re-run; it skips sections that already have rows.

## 6. Change the default password
Dashboard → Change Password. Do this immediately.

## 7. Add new content
Dashboard → click any section (Publications, Media, Achievements, Education,
Experience, Projects, Certifications, Gallery, Skills, Teaching) → **+ Add New
Item**. Every public page fetches its list live from `api/data.php`, so new
items appear on the site immediately after saving.

## 8. Connect your domain
hPanel → Domains → point your purchased domain to this hosting account
(or if it's already on the same Hostinger account, just set it as the primary
domain / add as an addon domain pointing to this folder).

## Notes
- `config/db.php` and any local test files are gitignored.
- If a section has no items yet, its public page shows a friendly "nothing
  added yet" message instead of breaking.
- Gallery images: upload photos to the `image/` folder, then in the Gallery
  admin form set "Image URL" to `image/your-file.jpg`.
