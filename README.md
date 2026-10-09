# Stockify — Inventory Management System

Stockify adalah aplikasi manajemen persediaan barang berbasis website yang dibuat menggunakan PHP dan MySQL. Aplikasi ini dikembangkan sebagai Mini Project 3 untuk membantu mengelola produk, kategori, supplier, serta transaksi barang masuk dan keluar.

## Fitur

* Login dan autentikasi pengguna berdasarkan role Admin dan Staff.
* Dashboard ringkasan persediaan.
* CRUD (Create, Read, Update, Delete) kategori, produk, dan supplier.
* Relasi produk dan supplier menggunakan tabel penghubung.
* Pencatatan transaksi barang masuk dan barang keluar.
* Riwayat transaksi persediaan.
* Laporan stok dan nilai persediaan.
* Pencarian produk berdasarkan nama atau SKU.
* Filter kategori dan pengurutan data.
* Validasi jumlah stok agar barang keluar tidak melebihi stok tersedia.
* Prepared statements untuk membantu mencegah SQL injection.
* Escaping output untuk membantu mencegah XSS.

## Teknologi

* PHP
* MySQL
* PDO
* XAMPP
* Visual Studio Code
* phpMyAdmin

## Cara Menjalankan

1. Instal dan buka XAMPP.
2. Jalankan Apache dan MySQL.
3. Letakkan folder project bernama `stockify` di `C:\xampp\htdocs\`.
4. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
5. Impor file `database/schema.sql` sesuai struktur database project.
6. Periksa konfigurasi koneksi database pada `config/database.php`.
7. Buka aplikasi melalui `http://localhost/stockify/public/login.php`.

## Struktur Project

* `config/` — konfigurasi koneksi database.
* `database/` — skema database SQL.
* `helpers/` — fungsi autentikasi dan bantuan.
* `public/` — halaman aplikasi.
* `views/` — tampilan bersama.

## Keamanan

Aplikasi menggunakan autentikasi session, pembatasan akses berdasarkan role, prepared statements, password hashing, serta escaping output. Hak akses tetap harus diperiksa di sisi server.

## Catatan

Pastikan konfigurasi database sesuai dengan lingkungan lokal. Gunakan akun demo yang benar-benar tersedia pada database lokal. Jangan menyimpan password asli, token, atau informasi rahasia di repository publik.

---

**Project:** Stockify — Inventory Management System
**Bahasa:** PHP
**Database:** MySQL
