# Manual Developer — ASA Palestina

Panduan teknis bagi developer untuk memperbarui script, aset, dan gambar pada aplikasi web **ASA Palestina**.

---

## 1. Pengenalan Proyek

ASA Palestina adalah aplikasi filantropi (penggalangan dana) yang dibangun dengan **PHP native** tanpa framework, dioptimalkan untuk shared web hosting.

| Komponen         | Teknologi                       |
| ---------------- | ------------------------------- |
| Backend          | PHP native (PDO/MySQL)          |
| Frontend publik  | Bootstrap 4.1.3 + jQuery 1.12.4 |
| Halaman admin    | Bootstrap 5.3.8 (bundle InApp)  |
| Editor artikel   | Summernote 0.9.0 (CDN jsDelivr) |
| Grafik dashboard | ApexCharts (CDN jsDelivr)       |
| Peta kontak      | Leaflet 1.9.4 (CDN unpkg)       |

### Struktur Folder

```
asa-project/
├── asa_config.php.example    # Contoh konfigurasi environment (DB, APP_NAME, IS_DEV)
├── asa_config.php            # Konfigurasi environment (DB, APP_NAME, IS_DEV) — GIT-IGNORED
├── asa_app/
│   ├── core/                 # Infrastruktur (database, session, guard, image)
│   │   ├── database.php
│   │   ├── session.php
│   │   ├── guard.php
│   │   └── image.php
│   ├── pages/                # SEMUA halaman web (satu file = satu route)
│   │   ├── index.php, about.php, program.php, events.php, blog.php,
│   │   │   blog_details.php, donate.php, contact.php, signin.php, setup.php, 404.php
│   │   └── admin/            # Halaman khusus admin
│   │       ├── index.php, campaigns.php, create-campaign.php, edit-campaign.php,
│   │       ├── donations.php, distributions.php, articles.php, gallery.php,
│   │       ├── users.php, contacts.php, signout.php, ...
│   ├── components/           # Partial/include layout frontend publik
│   │   ├── head_link.php, preload.php, header.php, footer.php, body_script.php
│   │   └── admin/            # Partial/include layout admin
│   │       ├── head_link.php, navbar.php, sidebar.php, footer.php, body_script.php
│   └── database/             # Dump SQL & patch
│       ├── dump.sql, patch1.sql, patch2.sql
└── public_html/              # DOCUMENT ROOT (di-upload ke hosting)
    ├── index.php             # Front controller / router
    ├── .htaccess             # Rewrite URL + header cache
    ├── assets/               # Aset frontend publik (Bootstrap 4)
    │   ├── css/, js/, img/, fonts/, scss/
    │   └── admin/            # Aset halaman admin (Bootstrap 5)
    │       ├── css/main.css, js/main.js, images/, tabler-icons.*
    └── uploads/              # Gambar hasil upload (git-ignored)
        ├── article/, campaign/, distribution/, donation/, gallery/
```

> **Catatan:** manual ini mengikuti kondisi kode aktual. Utility berada di `asa_app/core/`, dan kode membaca file konfigurasi di root: `asa_config.php`.

---

## 2. Routing (URL → File)

- Router utama: `public_html/index.php`.
- Aturan: `/namahalaman` → `asa_app/pages/namahalaman.php` (mendukung path bersarang, misal `/admin/campaigns` → `asa_app/pages/admin/campaigns.php`).
- Pengecualian:
  - `/` → `asa_app/pages/index.php`
  - `/admin` → `asa_app/pages/admin/index.php`
  - `/adminer` → `asa_app/pages/adminer-5.4.4-mysql-en.php` (tool DB — **jangan dipakai di produksi**)
- URL yang tidak cocok → `asa_app/pages/404.php`.
- Keamanan router: path divalidasi regex `#^/[a-zA-Z0-9_\-/]*$#` dan dicek `realpath()` berada di dalam `asa_app/pages/`.
- `.htaccess` mengirim semua request yang bukan file/folder ke `index.php` (syarat: mod_rewrite aktif).

**Menambah halaman baru:** cukup buat file baru di `asa_app/pages/` — route otomatis aktif tanpa registrasi apa pun.

---

## 3. Konfigurasi Environment — `asa_config.php`

```php
define('APP_NAME', 'ASA Depok - Lembaga Kemanusiaan untuk Palestina');
define('IS_DEV', true);        // true = menampilkan ribbon "Under Development"
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_name');
define('DB_USER', 'db_user');
define('DB_PASS', 'db_pass');
```

- Dibaca oleh setiap halaman via `require_once .../asa_config.php` dan oleh `asa_app/core/database.php`.
- File ini **tidak di-commit ke git** (ada di `.gitignore`). Salin dari `asa_config.php.example` saat setup lingkungan baru, lalu sesuaikan kredensial DB.
- **Sebelum produksi:** ubah `IS_DEV` menjadi `false` agar ribbon "Under Development" tidak tampil.

---

## 4. Menambah / Mengubah Halaman

### Halaman Publik (Bootstrap 4)

1. Buat file baru, misal `asa_app/pages/program-baru.php`.
2. Ikuti pola halaman yang sudah ada:

```php
<?php require_once __DIR__ . '/../core/session.php'; ?>
<?php require_once __DIR__ . '/../core/database.php'; ?>
<?php require_once __DIR__ . '/../asa_config.php'; // path menyesuaikan ?>
<!doctype html>
<html lang="id">
<head>
    <?php require_once __DIR__ . '/../components/preload.php'; ?>
    <?php require_once __DIR__ . '/../components/head_link.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/../components/header.php'; ?>
    <!-- konten halaman -->
    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    <?php require_once __DIR__ . '/../components/body_script.php'; ?>
</body>
</html>
```

3. Route otomatis: `/program-baru`.

### Halaman Admin (Bootstrap 5)

1. Buat file di `asa_app/pages/admin/`.
2. **Wajib** include guard agar hanya user login yang bisa akses:

```php
<?php
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';   // redirect ke /signin jika belum login
require_once __DIR__ . '/../../core/database.php';
$CURRENT_PAGE = 'campaigns'; // dipakai sidebar.php untuk highlight menu
?>
```

3. Layout admin memakai `components/admin/`: `head_link.php`, `navbar.php`, `sidebar.php`, `footer.php`, `body_script.php`.
4. Tambahkan menu baru di `asa_app/components/admin/sidebar.php` (label + ikon `ti ti-*` + URL `/admin/...`).

### Konvensi Halaman List Admin

- 10 baris per halaman, pagination `?page=N`.
- Hapus record: form POST inline (`action=delete`, hidden id) + konfirmasi JS.
- Format Rupiah: `number_format($nilai, 0, ',', '.')` dengan prefix `Rp`.

---

## 5. Memperbarui Aset (CSS/JS/Gambar)

### 5.1 Lokasi Aset

| Frontend publik             | Halaman admin                                    |
| --------------------------- | ------------------------------------------------ |
| `public_html/assets/css/`   | `public_html/assets/admin/css/main.css`          |
| `public_html/assets/js/`    | `public_html/assets/admin/js/main.js`            |
| `public_html/assets/img/`   | `public_html/assets/admin/images/`               |
| `public_html/assets/fonts/` | `public_html/assets/admin/tabler-icons.*`        |
| Bootstrap **4**             | Bootstrap **5** (ter-bundle di main.css/main.js) |

Jangan tertukar: komponen frontend memakai Bootstrap 4 (`assets/css/bootstrap.min.css` + jQuery), halaman admin memakai bundle Bootstrap 5 (`assets/admin/`).

### 5.2 Mengubah CSS

- Perubahan style frontend publik: `public_html/assets/css/style.css` (hasil kompilasi).
- **Sumber kebenaran (source of truth)** ada di `public_html/assets/scss/` (`style.scss` + partial seperti `_color.scss`, `_variables.scss`). Jika repo dikompilasi ulang dari SCSS, ubah SCSS-nya — editan langsung ke `style.css` bisa tertimpa.
- Style admin: `public_html/assets/admin/css/main.css` (bundle hasil build template InApp, 610 KB). Edit langsung file ini (tidak ada sumber SCSS-nya di repo).

### 5.3 Mengubah JS

- Frontend: `public_html/assets/js/` (plugin + `main.js`). Urutan include script ada di `asa_app/components/body_script.php` — jika menambah library baru, daftarkan di sana.
- Admin: `public_html/assets/admin/js/main.js` (ES module bundle, sudah termasuk Bootstrap 5 + Popper).

### 5.4 Mengubah / Menambah Gambar Statis

- Gambar frontend: `public_html/assets/img/` (subfolder `logo/`, `hero/`, `gallery/`, `blog/`, `post/`).
- Gambar admin: `public_html/assets/admin/images/` (`logo-a.png` sidebar, `logo-asa.png` halaman login/setup).
- Logo frontend direferensikan di `asa_app/components/header.php`; logo footer di `asa_app/components/footer.php`.
- Pertahankan nama & dimensi file agar tidak perlu ubah kode.

### 5.5 Cache Browser (PENTING)

`.htaccess` menetapkan:

- Gambar: `Expires` + `Cache-Control: max-age=2592000` (≈ 1 bulan)
- CSS/JS: `Cache-Control: max-age=31536000, public` (1 tahun)

Konsekuensinya, setelah update CSS/JS/gambar, pengguna lama bisa melihat versi lama. Solusi:

1. **Cache-busting** — tambahkan query string pada pemanggilan file, mis. `style.css?v=20261018` (lakukan di `components/head_link.php` / `body_script.php`), atau
2. **Ganti nama file** (mis. `style.v2.css`) dan update referensinya, atau
3. Minta user hard refresh (`Ctrl+F5`).

Untuk upload gambar konten (folder `uploads/`), nama file selalu baru (`uniqid()`), jadi aman dari cache lama.

---

## 6. Upload Gambar (Konten)

Ditangani oleh `asa_app/core/image.php` — fungsi `createImageVersions($sumber, $tujuan, $namaFile)`.

- Dipanggil dari halaman admin `create-*.php` / `edit-*.php` (form wajib `enctype="multipart/form-data"`, input `name="image"`; donasi memakai `name="proof_image"`).
- **Format diterima server: `jpg` / `jpeg`** (whitelist ekstensi di kode; fungsi GD sebenarnya juga mendukung PNG/WebP).
- Nama file hasil: `uniqid()` (otomatis, unik).
- Menghasilkan 3 varian gambar:

| Varian                | Keterangan          |
| --------------------- | ------------------- |
| `<nama>-large.<ext>`  | lebar maks 1600px   |
| `<nama>-small.<ext>`  | lebar maks 800px    |
| `<nama>-square.<ext>` | 400×400 center-crop |

- Folder penyimpanan (web-accessible, git-ignored):

| Jenis        | Folder                              |
| ------------ | ----------------------------------- |
| Artikel      | `public_html/uploads/article/`      |
| Program      | `public_html/uploads/campaign/`     |
| Distribusi   | `public_html/uploads/distribution/` |
| Bukti donasi | `public_html/uploads/donation/`     |
| Galeri       | `public_html/uploads/gallery/`      |

- Nama file disimpan di kolom `image` / `proof_image` pada database; halaman memanggil `/uploads/<tipe>/<image>`.
- File lama **tidak otomatis terhapus** saat diganti/dihapus record — bersihkan manual bila perlu.

---

## 7. Database

### Impor / Inisialisasi

Impor berurutan (charset `utf8mb4`):

1. `asa_app/database/dump.sql`
2. `asa_app/database/patch1.sql`
3. `asa_app/database/patch2.sql`

Koneksi dibuat oleh `asa_app/core/database.php` (PDO, `ERRMODE_EXCEPTION`, fetch assoc).

### Tabel Utama

| Tabel           | Fungsi                 | Kolom penting                                                                                 |
| --------------- | ---------------------- | --------------------------------------------------------------------------------------------- |
| `users`         | Pengelola/admin        | `email` unik, `password` (bcrypt), `role` enum `admin`/`staff`                                |
| `campaigns`     | Program donasi         | `slug` unik, `target_amount`, `status` (`draft`/`active`/`completed`/`cancelled`), `image`    |
| `donations`     | Transaksi donasi       | `campaign_id`, `donor_name`, `amount`, `status` (`pending`/`paid`/`cancelled`), `proof_image` |
| `distributions` | Penyaluran dana        | `campaign_id`, `amount`, `distribution_date`, `image`                                         |
| `articles`      | Artikel/blog           | `slug` unik, `content` (HTML Summernote), `status` (`draft`/`published`), `user_id`           |
| `galleries`     | Foto kegiatan          | `title`, `image`                                                                              |
| `contacts`      | Pesan dari form kontak | `name`, `email`, `subject`, `message`                                                         |

### Aturan Bisnis

- **Progress donasi (raised)** = `SUM(donations.amount) WHERE status = 'paid'` — dipakai di `/`, `/program`, `/donate`, dan dashboard admin.
- Hanya `campaigns.status = 'active'` yang tampil di halaman publik.
- Hanya `articles.status = 'published'` yang tampil di `/blog`.
- Donasi dari form publik `/donate` masuk dengan status `pending` dan harus diverifikasi admin menjadi `paid`.

### Menambah Kolom / Perubahan Skema

1. Buat file patch baru (mis. `asa_app/database/patch3.sql`), jangan mengedit `dump.sql` yang sudah dipakai di produksi.
2. Jalankan patch di DB produksi lewat phpMyAdmin/SSH.
3. Commit patch ke repo.

---

## 8. Autentikasi & Keamanan

- Login: `asa_app/pages/signin.php` — CSRF token (`$_SESSION['csrf_token']`), `password_verify()`, `session_regenerate_id(true)`.
- Guard: `asa_app/core/guard.php` — semua file di `pages/admin/` wajib me-`require`-nya; belum login → redirect `/signin`.
- Admin pertama: `asa_app/pages/setup.php` (hanya bisa diakses saat tabel `users` kosong).
- Logout: `/admin/signout` (`session_destroy`).
- Form publik (donasi, kontak, signin, setup) memakai CSRF; form admin belum.
- Role `admin`/`staff` tersimpan tapi **belum membedakan hak akses** — semua user login bisa mengakses semua halaman admin.

---

## 9. Deployment ke Hosting

1. Upload isi **`public_html/`** ke document root hosting (biasanya folder `public_html` di hosting juga).
2. Upload file proyek lainnya (`asa_app/`, `asa_config.php`) di **luar** document root (folder root hosting), sesuai path relatif yang dipakai.
3. Buat `asa_config.php` di root proyek di hosting (salin dari `config.php.example`), isi kredensial DB hosting, `IS_DEV = false`.
4. Impor database: `dump.sql` → `patch1.sql` → `patch2.sql` (phpMyAdmin atau CLI).
5. Pastikan folder `public_html/uploads/*` dapat ditulis (chmod 755/775, sesuai ketentuan hosting).
6. Pastikan mod_rewrite aktif di Apache agar routing berfungsi.
7. Buka `/setup` sekali untuk membuat akun admin pertama, lalu login di `/signin`.

---

## 10. Checklist Sebelum Produksi

- [ ] `IS_DEV` = `false` di `asa_config.php` (hilangkan ribbon "Under Development").
- [ ] **Hapus atau lindungi** `public_html/info.php` (mengekspos `phpinfo()`).
- [ ] **Hapus atau lindungi** route `/adminer` (admin DB publik) — file `asa_app/pages/adminer-5.4.4-mysql-en.php`.
- [ ] Ganti password DB default & pastikan `asa_config.php` tidak bisa diakses publik.
- [ ] Pertimbangkan hapus halaman demo template: `pages/elements.php`, `pages/admin/{inventory,create-product,reports,docs}.php`.
- [ ] Uji routing (`.htaccess`), login admin, upload gambar, form donasi & kontak.
- [ ] Setelah update aset, ingat aturan cache (lihat §5.5).

---

## 11. Catatan Bug Diketahui

Dicatat agar tidak terulang / bisa diperbaiki:

1. **Kolom `image` bisa berisi teks "Array"** — `createImageVersions()` mengembalikan _array_ 3 nama file, tetapi pemanggil (`create-*.php`/`edit-*.php`) memasukkan `$image` langsung ke query. PDO melempar warning "Array to string conversion" dan menyimpan string `"Array"`. Perbaikan: pilih satu varian, mis. `$image = $hasil['large'];` (atau simpan JSON dan decode saat tampil).
2. **Upload bukti donasi rusak** — di `admin/create-donation.php` dan `edit-donation.php`, kode memvalidasi `$_FILES['proof_image']` tapi memanggil `createImageVersions($_FILES['image']['tmp_name'], ...)` (key salah) dan menyimpan ke variabel `$image` bukan `$proofImage`.
3. **Konten artikel tampil ter-escape** — konten Summernote disimpan sebagai HTML, tapi `blog_details.php` me-render dengan `htmlspecialchars(...)` + `nl2br` sehingga tag HTML tampil sebagai teks. Putuskan salah satu: render HTML aman (`strip_tags` dengan whitelist) atau tampilkan plain text.
4. **Belum ada CSRF pada form admin** dan tidak ada rate limiting login.
5. **File lama tidak dihapus** saat gambar diganti atau record dihapus.

---

## 12. Referensi Cepat

| Butuh                 | Buka                                                   |
| --------------------- | ------------------------------------------------------ |
| Tambah halaman publik | `asa_app/pages/<nama>.php`                             |
| Tambah halaman admin  | `asa_app/pages/admin/<nama>.php` + `require guard.php` |
| Menu sidebar admin    | `asa_app/components/admin/sidebar.php`                 |
| Nav menu publik       | `asa_app/components/header.php`                        |
| Include CSS publik    | `asa_app/components/head_link.php`                     |
| Include JS publik     | `asa_app/components/body_script.php`                   |
| Koneksi DB            | `asa_app/core/database.php`                            |
| Proses upload gambar  | `asa_app/core/image.php`                               |
| Routing               | `public_html/index.php` + `public_html/.htaccess`      |
| Konfigurasi env       | `asa_config.php`                                       |
| Skema DB              | `asa_app/database/*.sql`                               |
