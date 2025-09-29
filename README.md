# Helpdesk PHP

Helpdesk sederhana berbasis PHP (MySQL) untuk pengelolaan tiket dukungan dengan tiga peran: Admin, Support, dan User. UI telah dimodernisasi dan mendukung tema Light/Dark yang dipersistenkan dengan `localStorage`.

## Fitur Utama
- Autentikasi user sederhana (register/login/logout)
- User: buat tiket, lihat tiket, balas percakapan, tidak bisa balas jika tiket `closed`
- Support: daftar tiket, lihat detail, ubah status, balas
- Admin: kelola tiket, lihat detail laporan tiket
- Tema Light/Dark global (persist `appTheme`)
- UI modern: topbar, card, tabel dengan badge dan aksi

## Teknologi
- PHP 7+/8+ (procedural)
- MySQL/MariaDB (via XAMPP)
- HTML/CSS (Poppins, Font Awesome)

## Instalasi
1. Clone repository:
   ```bash
   git clone https://github.com/opikbtk/helpdesk.git
   ```
2. Import database:
   - Buat database `helpdesk` di MySQL.
   - Import file `helpdesk.sql` (file ini di-ignore; masukkan manual).
3. Konfigurasi koneksi:
   - Buat `includes/database.php` berisi koneksi MySQL, contoh:
     ```php
     <?php
     $conn = new mysqli('localhost','root','','helpdesk');
     if ($conn->connect_error) { die('Koneksi gagal: '.$conn->connect_error); }
     ?>
     ```
4. Jalankan di XAMPP:
   - Taruh proyek di `C:/xampp/htdocs/helpdesk`
   - Akses `http://localhost/helpdesk`

## Struktur Direktori
- `admin/` halaman admin (kelola tiket, detail)
- `support/` halaman support (daftar tiket, detail, update status)
- `user/` halaman user (dashboard, tiket saya, buat/lihat tiket)
- `includes/` helper dan koneksi database (database.php di-ignore)
- `css/` stylesheet

## Keamanan & Catatan
- Hindari commit file kredensial: `includes/database.php`, `Login.txt`, `helpdesk.sql` sudah ada di `.gitignore`.
- Disarankan menggunakan prepared statements untuk semua query (sebagian sudah, sebagian masih plain). Lanjutkan migrasi untuk cegah SQL injection.
- Lakukan hard refresh (Ctrl+F5) jika perubahan CSS tidak muncul.

## Roadmap
- Pagination dan pencarian/penyaringan tiket (status/keyword)
- Dashboard chart ringkas (Chart.js)
- Lengkapi prepared statements di semua modul

## Lisensi
MIT (opsional, tambahkan LICENSE bila perlu)
