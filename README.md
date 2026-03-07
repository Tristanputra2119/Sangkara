# Sangkara

Sistem Manajemen Internal Organisasi Sangkara berbasis web menggunakan Laravel dan Filament Admin Panel.

## Tech Stack

- **Framework:** Laravel 12
- **PHP Version:** 8.2+
- **Database:** PostgreSQL
- **Admin Panel:** Filament 5.0
- **Authentication:** Laravel Sanctum + Laravel Socialite (Google OAuth2)
- **Authorization:** Spatie Laravel Permission + Filament Shield
- **PDF Generation:** DomPDF
- **Document Generation:** PHPWord (DOCX)
- **Cache/Queue:** Redis (Predis)
- **Testing:** Pest PHP

## Requirements

- PHP >= 8.2
- PostgreSQL >= 12
- Composer
- Node.js & NPM
- Redis (optional, for queue and cache)

## Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd Sangkara
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

Copy `.env.example` ke `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Database Configuration

Edit file `.env` dan sesuaikan konfigurasi database PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sangkara
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

Buat database:

```bash
createdb sangkara
```

### 5. Google OAuth2 Setup

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang sudah ada
3. Aktifkan **Google+ API**
4. Buat **OAuth 2.0 Client ID** (Web Application)
5. Tambahkan **Authorized Redirect URIs**:
   - `http://127.0.0.1:8000/oauth/google/callback`
   - `http://localhost:8000/oauth/google/callback`
6. Copy **Client ID** dan **Client Secret** ke file `.env`:

```env
APP_URL=http://127.0.0.1:8000

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/oauth/google/callback"
```

### 6. Cloudinary Setup (untuk Image Upload)

1. Buka [Cloudinary Dashboard](https://cloudinary.com/)
2. Buat akun atau login ke akun yang sudah ada
3. Buka **Dashboard** dan copy kredensial:
   - **Cloud Name**
   - **API Key**
   - **API Secret**
4. Tambahkan kredensial ke file `.env`:

```env
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

> **Note:** Semua gambar yang diupload melalui RichEditor di Proposals, Reports, dan Meetings akan otomatis tersimpan di Cloudinary untuk performa yang lebih ringan.

### 7. Run Migrations

```bash
php artisan migrate --seed
```

### 8. Storage Link

```bash
php artisan storage:link
```

### 9. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Running the Application

### Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://127.0.0.1:8000`

### Build Assets

Development:
```bash
npm run dev
```

Production:
```bash
npm run build
```

### Queue Worker (Optional)

Jika menggunakan queue untuk background jobs:

```bash
php artisan queue:work
```

## Default Access

### Admin Panel

URL: `http://127.0.0.1:8000/sangkara`

### Login Methods

1. **Standard Login**: Email & Password
2. **Google OAuth2**: Login dengan akun Google

### Roles

- **super_admin**: Full access ke seluruh sistem
- **panel_user**: Access terbatas sesuai permission yang diberikan

> **Note**: User yang login pertama kali via Google OAuth akan otomatis mendapat role `panel_user`. Untuk upgrade ke `super_admin`, edit manual via database atau Artisan Tinker.

## Features

### Core Modules

1. **User Management**
   - User CRUD
   - Role & Permission Management (Filament Shield)
   - Google OAuth2 Integration

2. **Categories**
   - Category management untuk organisasi data

3. **Meetings**
   - Manajemen rapat/pertemuan
   - Track attendance & status
   - Meeting creator & attendees

4. **Proposals**
   - Manajemen proposal organisasi
   - Generate proposal documents

5. **Reports**
   - Generate laporan
   - Export to PDF/DOCX

6. **Transactions**
   - Financial transaction management
   - Track income & expenses

### Services

- **DocumentConversionService**: Convert dokumen antar format
- **FinancialService**: Business logic untuk transaksi keuangan
- **ProposalGeneratorService**: Generate dokumen proposal otomatis

## Configuration

### Sangkara Officials (Signatures)

Edit file `.env` untuk konfigurasi pejabat yang akan muncul di dokumen:

```env
SANGKARA_KETUA_NAMA="Nama Ketua"
SANGKARA_KETUA_NIM="000000000"
SANGKARA_SEKRE_NAMA="Nama Sekretaris"
SANGKARA_SEKRE_NIM="000000000"
```

## Testing

Jalankan test dengan Pest:

```bash
php artisan test
```

atau:

```bash
./vendor/bin/pest
```

## Code Quality

### Laravel Pint (Code Formatter)

```bash
./vendor/bin/pint
```

## Troubleshooting

### Error 400: redirect_uri_mismatch (Google OAuth)

Pastikan:
1. `APP_URL` di `.env` sesuai dengan URL yang diakses di browser
2. URL redirect di Google Cloud Console **sama persis** dengan `GOOGLE_REDIRECT_URI`
3. Clear config cache: `php artisan config:clear`

### User tidak bisa masuk setelah login Google

Pastikan:
1. Migration sudah dijalankan: `php artisan migrate`
2. Role `panel_user` atau `super_admin` sudah ada di database
3. User memiliki salah satu role tersebut

### Permission Denied di Filament

User harus memiliki role yang sesuai. Assign role via Tinker:

```bash
php artisan tinker
```

```php
$user = User::where('email', 'your@email.com')->first();
$user->assignRole('super_admin');
```

## License

Proprietary - Sangkara Organization

## Contact

Untuk pertanyaan dan support, hubungi tim development Sangkara.

---

**Last Updated**: March 2026
