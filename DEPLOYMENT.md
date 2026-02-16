# 📦 Panduan Deployment ISO Digital

## Kredensial Database Hosting

```
Database: eliteac1_ISO_DIGITAL
Username: eliteac1_iso_digital
Password: 123Q@zaqw
```

## Langkah-langkah Deployment

### 1. Upload File ke Hosting

Upload semua file ke hosting menggunakan FTP atau Git.

Jika menggunakan Git:
```bash
git add .
git commit -m "Deploy to hosting"
git push origin main
```

### 2. Konfigurasi File .env di Hosting

Copy `.env.example` menjadi `.env` dan ubah konfigurasi berikut:

```env
APP_NAME="ISO Digital"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://iso-digital.eliteacademia.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eliteac1_ISO_DIGITAL
DB_USERNAME=eliteac1_iso_digital
DB_PASSWORD=123Q@zaqw

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Jalankan Script Deployment

```bash
chmod +x deploy.sh
bash deploy.sh
```

Atau jalankan manual:

```bash
# Optimasi production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrasi database
php artisan migrate --force

# Link storage
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### 5. Konfigurasi Server

Pastikan document root mengarah ke folder `public`:
```
/home/eliteac1/iso-digital.eliteacademia.id/public
```

Atau jika subdomain ada di public_html:
```
/home/eliteac1/public_html/iso-digital/public
```

## Troubleshooting

### Error 500
1. Cek permission folder `storage` dan `bootstrap/cache`
2. Pastikan `.env` sudah dikonfigurasi
3. Cek log di `storage/logs/laravel.log`

### Database Connection Error
1. Pastikan kredensial database sudah benar
2. Cek apakah database sudah dibuat di hosting
3. Pastikan user database punya akses penuh

### Assets Tidak Muncul
1. Jalankan `php artisan storage:link`
2. Cek permission folder `public/storage`

## Update Aplikasi

Setelah melakukan perubahan di lokal:

```bash
# Push perubahan
git add .
git commit -m "Update description"
git push origin main

# Di hosting, pull perubahan dan jalankan deploy
git pull origin main
bash deploy.sh
```

## 📱 PWA (Progressive Web App) Features

Aplikasi ini sudah dilengkapi dengan fitur PWA untuk mobile-friendly experience.

### PWA Files
- `public/manifest.json` - PWA manifest configuration
- `public/sw.js` - Service worker untuk offline support
- `public/offline.html` - Offline fallback page
- `public/css/mobile-enhancements.css` - Mobile responsive CSS
- `public/icons/` - PWA icons (8 sizes)
- `public/apple-touch-icon.png` - iOS home screen icon

### HTTPS Requirement untuk Production
> **⚠️ PENTING:** Service worker hanya berfungsi di HTTPS (kecuali localhost).

Pastikan hosting sudah menggunakan SSL/HTTPS:
```
https://iso-digital.eliteacademia.id
```

### Generate PWA Icons
Jika logo berubah, generate ulang icons:

```bash
chmod +x generate-icons.sh
./generate-icons.sh
```

### Cara Install PWA ke Smartphone

**Android (Chrome):**
1. Buka `https://iso-digital.eliteacademia.id/admin`
2. Klik "Add to Home Screen" di Chrome menu
3. Icon akan muncul di home screen

**iOS (Safari):**
1. Buka `https://iso-digital.eliteacademia.id/admin`
2. Tap Share → "Add to Home Screen"
3. Icon akan muncul di home screen

### Verify PWA Installation

**Browser DevTools:**
1. Buka Chrome DevTools (F12)
2. Tab "Application"
3. Check:
   - ✅ Manifest loaded
   - ✅ Service Worker registered
   - ✅ Icons all present

**Lighthouse Audit:**
```bash
# Chrome DevTools → Lighthouse → PWA
# Target score: 90+/100
```

### PWA Features
- ✅ Install to home screen (Android & iOS)
- ✅ Offline support dengan caching
- ✅ Touch-friendly UI untuk mobile
- ✅ Bottom navigation bar di mobile
- ✅ Safe area insets untuk notch displays
- ✅ Standalone mode (fullscreen tanpa browser UI)

