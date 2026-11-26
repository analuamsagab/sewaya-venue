# AGENTS.md

## Project

SEWAYA — venue/room reservation app for a final-semester project.
Native PHP 8.2 + MySQL. **No framework, no Composer, no npm, no bundler.** Do not add one.
Docs and UI copy are Indonesian. Keep them Indonesian.

## Runtime

- PHP: `C:\xampp\php\php.exe` — **not on PATH**, always call it with the full path.
- Serve: `C:\xampp\php\php.exe -S localhost:8000 -t "D:\Project Akhir Semester"`
- DB: Docker container `db-sewaya` → `127.0.0.1:3306`, database `sewaya`
- phpMyAdmin: http://localhost:8080
- MySQL 26.10 stores timestamps in **UTC** (`NOW()` == `UTC_TIMESTAMP()`).
  Run `SET time_zone = '+07:00'` on every PDO connection, otherwise every date is 7 hours off.
- Installed extensions do **not** include `gd`, `imagick`, `intl`, `zip`.
  Format Rupiah with `number_format()`, never `NumberFormatter`.
  No server-side image resizing, no ZIP-based backup.
- Uploads allowed: `upload_max_filesize=40M`, `post_max_size=40M`, `disable_functions` empty.

## Database conventions (existing — follow, do not rename)

- Tables singular lowercase (`role`, `room`), except `users`.
- PK pattern `id_<entity>` with `auto_increment`.
- Columns in Indonesian snake_case (`nama_lengkap`, `no_telp`, `deskripsi`, `kapasitas`).
- `role.permission` is `json` NOT NULL — seed it, an insert without it fails.
- `users.status` has no default — always set it explicitly.

Existing tables:

```
role(id_role, nama_role, permission json, created_at, updated_at)
room(id_room, nama, deskripsi, kapasitas)
users(id_user, id_role, nama_lengkap, email, no_telp, password, status)
  FK users.id_role -> role.id_role
```

Added by `database/001_init.sql`, same singular convention:

```
room     + kategori, lantai, harga_per_jam, foto, status
booking(id_booking, kode, id_user, id_room, mulai, selesai, jumlah_peserta,
        total_biaya, status, alasan_penolakan, created_at, updated_at)
booking_status_log(id_log, id_booking, status_lama, status_baru, catatan, dibaca, created_at)
password_reset(id_reset, email, token_hash, expires_at, used_at, created_at)
```

Required fixes already tracked in `database/001_init.sql`:
`users.password varchar(16)` → `varchar(255)` (bcrypt hash is 60 chars), `room.deskripsi` → `TEXT`.

## Source of truth

- **Figma**: file `38FmRxRE3rNWZpiwb6eR1F`, page `User` (node `2001:46`). One page only.
  Screens: Landing, Dashboard, Login, Register, Forget Password, New Password.
  No Admin/CMS screens exist — that area is out of scope.
- Routes are whitelisted in `index.php`: `beranda`, `login`, `register`, `lupa-sandi`,
  `sandi-baru`, `dashboard`. JSON endpoints: `api/auth.php?action=login|register|lupa-sandi|sandi-baru|logout`.
- Figma has **no variables** (`get_variable_defs` returns `{}`), so design tokens are
  transcribed by hand into `assets/css/tokens.css`. That file is the only place for raw values.
- `assets/img/*.png` are **stale page screenshots** (dashboard says "Rizqi", Figma says "Aditya").
  Use them only as a visual reference. Never use them as page content or as component assets.
- Business rules: `analisis-awal.md` (32 rules) wins over `business-rules-sistem-reservasi.md`
  (30 rules, missing the activity-log rule) when they disagree.

## UI rules

- Colors, radii, fonts only via tokens in `assets/css/tokens.css`. No raw hex elsewhere.
- Components are PHP functions in `includes/components/*.php` returning HTML strings.
- Layout with flex/grid. `get_design_context` returns React+Tailwind with absolute
  positioning — translate it, never paste it. Only the hero photo overlay may be absolute.
- Fonts self-hosted in `assets/fonts/` (Inter, Playfair Display, Inria Sans). No runtime CDN.
- Icons: Lucide paths vendored into `assets/icons/sprite.svg`. Use
  `<svg class="icon"><use href="/assets/icons/sprite.svg#icon-x"></use></svg>`.
- Unbuilt screens are rendered disabled (`aria-disabled="true"`, tooltip "Belum tersedia").
  Do not invent routes or placeholder pages.
- The landing "Pesan Ruangan" button links to `dashboard`; `auth_require()` redirects guests
  to login with the flash "Silakan masuk dulu untuk memesan ruangan."

## Security rules

- PDO prepared statements only. Never interpolate SQL.
- CSRF token in `X-CSRF-Token` on every non-GET request; verify server-side.
- `?page=` routing uses a whitelist array, never a raw include.
- Escape all output with `htmlspecialchars($s, ENT_QUOTES, 'UTF-8')`.
- Uploads: validate real MIME with `finfo`, cap at 2 MB, derive the extension from the MIME
  (not the client filename), random filename, store under `uploads/`.

## Unresolved product gaps (ask, do not invent)

- Booking status has no `Selesai`, but ratings are only allowed after a booking is finished.
- "Kehilangan" report category: `fitur-ui-ux.md` gives it to Users, `analisis-awal.md` to Admin.
- Discount tiers, operating hours, and corkage fee are all "set by management" with no values.

## Commands

There is no test suite or linter. Verification is manual:

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t "D:\Project Akhir Semester"
C:\xampp\php\php.exe -l path\to\file.php          # syntax check one file
C:\xampp\php\php.exe database\seed.php            # reset data demo (CLI only)
```

Seed accounts, all with password `password`: `aditya@sewaya.test` (User),
`admin@sewaya.test` (Admin), `superadmin@sewaya.test` (Superadmin).
