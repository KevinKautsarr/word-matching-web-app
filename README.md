# LEXORA - Gamified English Learning Platform

LEXORA adalah prototipe aplikasi web pembelajaran bahasa Inggris yang dirancang dengan pendekatan *gamification*. Proyek ini ditujukan untuk membuat proses belajar kosakata (vocabulary) bahasa Inggris menjadi lebih interaktif, terstruktur, dan menyenangkan layaknya bermain game.

## Fitur Utama
1. **Sistem Unit & Lesson Berbasis Progres**: User harus menyelesaikan lesson demi lesson secara berurutan. Pelajaran baru akan terbuka (unlocked) setelah pelajaran sebelumnya diselesaikan.
2. **Game Match Madness**: Game mencocokkan kata (word matching) interaktif yang menjadi inti pembelajaran. Dilengkapi limit waktu, perhitungan skor otomatis, dan bonus akurasi.
3. **Daily Goals & Streak**: Sistem pelacakan rutinitas harian untuk menjaga konsistensi belajar user.
4. **Sistem Level & XP**: Menyelesaikan lesson akan memberikan XP untuk naik level.
5. **Dashboard & User Profile**: Visualisasi progress, pencapaian target harian, dan statistik pembelajaran.

---

## Prasyarat Lingkungan (Requirements)
- PHP 8.2+
- Composer
- Node.js & NPM (untuk memproses asset Vite)
- SQLite (default) atau MySQL

---

## Instalasi & Setup

1. **Clone & Install Dependensi Backend**
   ```bash
   composer install
   ```

2. **Setup File Environment**
   Salin `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   ```
   Pastikan konfigurasi `DB_CONNECTION` telah diset (direkomendasikan `sqlite` untuk setup awal yang cepat).

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

4. **Migrasi dan Seeding Database**
   Perintah ini akan membuat struktur tabel beserta data default (termasuk contoh User, Unit, Lesson, dan ratusan vocabulary).
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Install Dependensi Frontend & Compile Assets**
   ```bash
   npm install
   npm run build
   ```
   *(Opsional: gunakan `npm run dev` untuk hot-reloading selama development)*

---

## Menjalankan Aplikasi

Jalankan server lokal melalui Artisan:
```bash
php artisan serve
```
Akses aplikasi melalui browser: [http://localhost:8000](http://localhost:8000)

### 📌 Akun Demo / Uji Coba
Gunakan informasi kredensial berikut untuk masuk sebagai user *tester* yang sudah di-seed, atau Anda dapat membuat akun baru melalui halaman Register:
- **Email**: `demo@lexora.id`
- **Password**: `password`

---

## Menjalankan Testing

Proyek ini dilengkapi dengan Feature tests minimal untuk mengamankan fungsionalitas inti (seperti validasi skor game dan pembaruan profil pengguna).

Untuk mengeksekusi test, jalankan:
```bash
php artisan test
```

---

## Refactoring Terakhir (Peningkatan Stabilitas)
- **Keamanan Game Engine**: Logika perhitungan target/skor kelulusan lesson divalidasi keamanannya agar tidak lolos modifikasi (payload manipulasi diblokir).
- **Integritas Route**: Sinkronisasi method profil secara ketat pada routing (`PUT`), Form, dan Controller untuk mengurangi insiden *"MethodNotAllowed"* atau kesalahan render view.
- **Konsistensi Bisnis**: Peraturan standard kelulusan (passing grade 70%) disamakan baik pada UI dan backend Controller.
- **Portabilitas Seeder**: Migrasi logic pembersihan database (truncate) yang sebelumnya tidak kompatibel di SQLite diganti dengan facades Schema yang lebih aman di segala driver.

---
*Dibuat menggunakan Laravel 11.x, Tailwind CSS, Vanilla JS, dan Alpine.js.*
