# SwapSkill Backend

Backend API untuk SwapSkill, aplikasi barter keahlian mahasiswa. Backend ini menangani autentikasi, profil mahasiswa, skill, tawaran barter, bookmark, review, notifikasi, statistik personal, dan panel admin.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum untuk token API
- Filament 5 untuk admin panel
- MySQL sebagai database utama
- PHPUnit untuk test
- Laravel Pint untuk formatting

## Fitur Utama

- Register dan login mahasiswa menggunakan token Sanctum.
- Verifikasi user sebelum mahasiswa bisa membuat tawaran barter.
- CRUD profil, skill portofolio, dan update password.
- Skill board dengan search, filter skill, sort, pagination, dan bookmark.
- Rekomendasi barter berdasarkan skill yang dimiliki dan dibutuhkan user.
- Review antar user dengan proteksi agar tidak review diri sendiri atau spam review user yang sama.
- Notifikasi untuk bookmark dan review.
- Admin panel Filament untuk mengelola data.

## Struktur Arsitektur

Project mulai diarahkan ke Clean Architecture pragmatis untuk Laravel:

```text
app/
├── Application/          # Use case dan DTO alur bisnis
├── Domain/               # Contract/interface domain
├── Infrastructure/       # Implementasi persistence Eloquent
├── Http/                 # Controller, middleware, request
├── Models/               # Eloquent model
├── Policies/             # Authorization policy
└── Providers/            # Service binding dan konfigurasi app
```

Pedoman singkat:

- Controller hanya menerima request, validasi, authorization, dan response.
- Business flow diletakkan di `Application/*/UseCases`.
- Contract repository diletakkan di `Domain/*/Contracts`.
- Query Eloquent diletakkan di `Infrastructure/Persistence`.
- Model Eloquent tetap dipakai sesuai gaya Laravel, tidak dipaksa menjadi pure entity.

## Instalasi Lokal

Clone repo:

```bash
git clone https://github.com/fredyyfajarr/swapskill-be.git
cd swapskill-be
```

Install dependency:

```bash
composer install
```

Buat file environment:

```bash
cp .env.example .env
php artisan key:generate
```

Untuk Windows PowerShell:

```powershell
copy .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`:

```env
APP_URL=http://127.0.0.1:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swapskill_db
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Jalankan server:

```bash
php artisan serve
```

Backend default berjalan di:

```text
http://127.0.0.1:8000
```

## Akun Test Seeder

Seeder membuat akun test verified:

```text
email: test@swapskill.test
password: password
```

Seeder juga membuat data skill, 20 user dummy verified, dan 50 tawaran barter dummy.

## Endpoint API Utama

Public:

```text
POST /api/register
POST /api/login
GET  /api/skills
```

Private, membutuhkan token Sanctum:

```text
POST   /api/logout
GET    /api/profile
PUT    /api/profile
PUT    /api/profile/password
POST   /api/profile/skills
DELETE /api/profile/skills/{skillId}
GET    /api/profile/stats
GET    /api/users/{id}/profile
GET    /api/users/{id}/reviews
POST   /api/reviews
GET    /api/bookmarks
POST   /api/posts/{post}/bookmark
GET    /api/notifications
POST   /api/notifications/read
DELETE /api/notifications/clear
GET    /api/posts
```

Khusus user verified:

```text
POST   /api/posts
GET    /api/posts/recommendations
POST   /api/posts/{post}/whatsapp
PATCH  /api/posts/{post}/status
DELETE /api/posts/{post}
```

## Integrasi Frontend

Frontend menggunakan `NEXT_PUBLIC_API_URL` yang mengarah ke backend API:

```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api
```

Login mengembalikan token. Frontend menyimpan token dan mengirim header:

```text
Authorization: Bearer <token>
```

## Admin Panel

Filament tersedia sebagai admin panel Laravel. Jalankan backend, lalu akses route panel sesuai konfigurasi Filament di project.

## Perintah Development

```bash
php artisan serve
php artisan route:list --path=api
php artisan test
vendor/bin/pint
```

Jika test memakai SQLite in-memory, pastikan extension PHP `pdo_sqlite` aktif.

## Catatan Migration

Tabel `reviews` hanya boleh dibuat oleh satu migration. Jika muncul error `Table 'reviews' already exists`, pastikan tidak ada migration duplikat yang sama-sama menjalankan `Schema::create('reviews')`.

## Repository Terkait

Frontend SwapSkill:

```text
https://github.com/fredyyfajarr/swapskill-fe
```