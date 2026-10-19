# Manual Admin — ASA Palestina

Panduan penggunaan aplikasi untuk admin/pengelola website **ASA Palestina**.

---

## 1. Login & Akses

### Masuk (Login)

1. Buka halaman login: `https://domain-anda/signin`
2. Masukkan **email** dan **password** Anda.
3. Klik tombol masuk. Jika berhasil, Anda akan diarahkan ke dashboard admin (`/admin`).

### Admin Pertama (Belum Ada Akun)

Jika belum ada satu pun pengguna, akses `/signin` akan otomatis mengarahkan ke **`/setup`**. Isi nama, email, dan password untuk membuat akun admin pertama, lalu gunakan akun tersebut untuk login.

### Keluar (Logout)

Klik menu **foto/nama pengguna** di pojok kanan atas bilah atas → **Signout**. Selalu logout setelah selesai, terutama di komputer bersama.

### Lupa Password

Gunakan menu "Lupa password" hanya jika tersedia; saat ini cara reset password adalah meminta pengembang/DB admin mengubah password lewat menu **Pengelola** (lihat §8) atau database.

---

## 2. Dashboard (`/admin`)

Dashboard menampilkan ringkasan operasional:

- **Kartu statistik**: total program, program aktif, total donasi (yang sudah `paid`), total penyaluran, donasi menunggu verifikasi (`pending`), jumlah pengelola, jumlah transaksi.
- **5 program terbaru** beserta dana terkumpul.
- **5 donasi terbaru** dan **5 penyaluran terbaru**.
- **Grafik batang**: donasi vs penyaluran per bulan (tahun berjalan).
- **Grafik lingkaran**: program aktif vs program lainnya.

Gunakan dashboard untuk memantau progres sebelum masuk ke menu-menu di bawah.

---

## 3. Program Donasi (Menu: Program)

Kelola program penggalangan dana. Terbit di website publik di halaman **/program** dan beranda.

### Melihat Daftar

- Menu **Program** → daftar program (10 per halaman, gunakan tombol halaman di bawah tabel).

### Menambah Program

1. Klik **Tambah** (Create) → form baru.
2. Isi:
   - **Judul** — slug (URL) dibuat otomatis dari judul.
   - **Deskripsi** — penjelasan program.
   - **Target donasi (Rp)** — nominal target, mis. `100000000`.
   - **Tanggal mulai** dan **tanggal selesai** (opsional).
   - **Status** — lihat tabel status di bawah.
   - **Gambar** — format **JPG/JPEG**, nama file otomatis.
3. Klik simpan.

### Status Program

| Status      | Arti            | Tampil di website publik? |
| ----------- | --------------- | ------------------------- |
| `draft`     | Masih rancangan | Tidak                     |
| `active`    | Sedang berjalan | **Ya**                    |
| `completed` | Selesai         | Tidak                     |
| `cancelled` | Dibatalkan      | Tidak                     |

> Progress bar di website = jumlah donasi berstatus **`paid`** dibagi target.

### Mengubah / Menghapus

- Klik **Edit** pada baris program → ubah data → simpan. Gambar lama tetap ada; pilih gambar baru hanya jika ingin mengganti.
- Klik **Hapus** → konfirmasi dialog → data terhapus. (Gambar file di server tidak ikut terhapus otomatis.)

---

## 4. Donasi (Menu: Donasi)

Catat dan verifikasi transaksi donasi.

### Verifikasi Donasi dari Website

Donasi yang masuk lewat formulir publik (**/donate**) berstatus awal **`pending`**. Untuk menghitung ke progress program:

1. Buka menu **Donasi**, cari transaksi (nama donatur/tanggal).
2. Klik **Edit**.
3. Ubah **status** menjadi **`paid`** (sudah diterima/transfer verified) → simpan.
4. Setelah `paid`, nominal otomatis terhitung di progress program dan total di dashboard.

### Status Donasi

| Status      | Arti                                    |
| ----------- | --------------------------------------- |
| `pending`   | Menunggu verifikasi/bukti transfer      |
| `paid`      | Diterima — **dihitung** ke total donasi |
| `cancelled` | Ditolak/batal — tidak dihitung          |

### Menambah Donasi Manual

Untuk donasi offline (tunai/transfer langsung yang dicatat manual):

1. Menu **Donasi** → **Tambah**.
2. Isi nama donatur, email, telepon, jumlah (angka), tanggal & waktu, program terkait, status, catatan (opsional).
3. **Bukti transfer** (`proof_image`): upload gambar JPG/JPEG bila ada.
4. Simpan.

### Kolom Informasi

- **Program** — donasi terikat pada satu program; judul program ditampilkan di daftar.
- **Bukti** — ikon/tautan bukti transfer bila diunggah.

---

## 5. Distribusi / Penyaluran (Menu: Distribusi)

Mencatat penyaluran dana ke program (laporan penggunaan dana).

1. Menu **Distribusi** → **Tambah**.
2. Isi **program** terkait, **judul** penyaluran, **deskripsi**, **jumlah (Rp)**, **tanggal** penyaluran, dan **gambar** dokumentasi (JPG/JPEG).
3. Simpan. Data tampil di dashboard (5 penyaluran terbaru) dan laporan bulanan grafik.

Edit/hapus mengikuti pola sama seperti menu Program.

---

## 6. Artikel (Menu: Artikel)

Kelola artikel/blog yang tampil di halaman publik **/blog**.

### Menambah Artikel

1. Menu **Artikel** → **Tambah**.
2. Isi:
   - **Judul** — slug URL dibuat otomatis (huruf kecil, spasi jadi `-`).
   - **Konten** — tulis dengan editor rich text (**Summernote**): teks tebal/miring, heading, gambar teks, daftar, dll.
   - **Gambar sampul** — JPG/JPEG, nama file otomatis.
   - **Status** — `draft` (belum tampil) atau `published` (**tampil di /blog**).
3. Simpan.

### Mengubah Artikel

- Klik **Edit** → ubah konten → simpan.
- Ubah status `draft` ↔ `published` untuk menyembunyikan/menerbitkan artikel.
- Judul tidak disarankan diubah terlalu sering (slug ikut berubah/tergantung implementasi).

### Catatan

- Artikel hanya tampil publik jika statusnya **published**.
- Penulis tercatat otomatis dari akun yang login.

---

## 7. Galeri Kegiatan (Menu: Galeri)

Foto kegiatan yang tampil di halaman publik **/events**.

1. Menu **Galeri** → **Tambah foto**.
2. Isi **judul**, **deskripsi** (opsional), dan **gambar** — **wajib**, format JPG/JPEG.
3. Simpan.

Edit/hapus: gunakan tombol pada daftar foto. Gambar yang dihapus dari daftar tidak otomatis terhapus dari server.

---

## 8. Pengelola / Pengguna (Menu: Pengelola)

Kelola akun yang bisa login ke halaman admin.

### Menambah Pengelola

1. Menu **Pengelola** → **Tambah**.
2. Isi **nama**, **email** (harus unik), **password** dan **ulangi password**.
3. Pilih **role**:
   - `admin` — pengelola penuh
   - `staff` — petugas
4. Simpan.

> **Catatan:** saat ini role belum membedakan hak akses — semua akun login dapat mengakses semua menu admin. Tetap gunakan password yang kuat untuk setiap akun.

### Mengubah Pengelola

- Klik **Edit** → ubah nama/email/role.
- **Biarkan kolom password kosong** jika tidak ingin mengganti password (password lama tetap berlaku). Isi hanya jika ingin mengganti.

### Menghapus

- Klik **Hapus** → konfirmasi. Jangan menghapus akun yang sedang Anda pakai untuk login.

---

## 9. Pesan (Menu: Pesan)

Inbox pesan dari formulir kontak website publik (**/kontak**).

- Menu **Pesan** → daftar pesan masuk (nama, email, subjek, tanggal).
- Klik pesan untuk **melihat detail** (isi lengkap).
- **Hapus** pesan yang sudah tidak diperlukan.

Balasan dilakukan manual lewat email Anda (email pengirim tertera di pesan).

---

## 10. Aturan Umum Penggunaan

- **Pagination**: daftar data menampilkan 10 baris per halaman; gunakan navigasi halaman di bawah tabel.
- **Konfirmasi hapus**: selalu ada dialog konfirmasi sebelum data terhapus — periksa dulu data yang dipilih.
- **Upload gambar**: format **JPG/JPEG** saja; nama file dibuat otomatis (tidak perlu khawatir bentrok nama). Gambar otomatis dikecilkan untuk performa.
- **Mata uang**: semua nominal dalam **Rupiah (Rp)**, ditulis sebagai angka tanpa titik/koma (mis. `10000000` = Rp10.000.000).
- **Search/Filter tombol**: pada beberapa halaman tampil sebagai hiasan template dan belum berfungsi — gunakan pagination untuk menelusuri data.
- **Ribbon "Under Development"**: tampil di website publik jika mode dev aktif; hilangkan oleh developer saat sudah rilis.

---

## 11. Tips Keamanan

1. Gunakan **password kuat** (huruf besar/kecil + angka, minimal 8 karakter).
2. **Jangan bagikan** akun; buat akun terpisah untuk tiap pengelola (menu Pengelola).
3. **Logout** setelah selesai, terutama di komputer bersama.
4. Jangan simpan password di catatan publik atau browser di komputer umum.
5. Verifikasi donasi ke **`paid`** hanya setelah bukti transfer benar-benar diterima.

---

## 12. Ringkasan Menu Admin

| Menu       | URL                    | Fungsi                    |
| ---------- | ---------------------- | ------------------------- |
| Dashboard  | `/admin/`              | Statistik & grafik        |
| Program    | `/admin/campaigns`     | Kelola program donasi     |
| Donasi     | `/admin/donations`     | Catat & verifikasi donasi |
| Distribusi | `/admin/distributions` | Catat penyaluran dana     |
| Artikel    | `/admin/articles`      | Kelola blog               |
| Galeri     | `/admin/gallery`       | Foto kegiatan (/events)   |
| Pengelola  | `/admin/users`         | Akun pengguna             |
| Pesan      | `/admin/contacts`      | Inbox kontak              |
| Signout    | `/admin/signout`       | Keluar                    |
