Pilihan yang sangat tepat! Sistem Antrian Klinik tidak hanya fungsional, tapi juga sangat mudah dinilai oleh dosen karena alur logika CRUD-nya jelas dan pemanfaatan library PHP-nya sangat masuk akal (mencetak nomor antrian/struk).

Berikut adalah rancangan struktur lengkap untuk website **Sistem Antrian Klinik** kamu, dari *database* hingga susunan file-nya.

### 1. Struktur Database (MySQL)

Untuk sistem antrian dasar, kamu membutuhkan 3 tabel utama.

* **Tabel `users**` (Menyimpan data admin dan pasien)
* `id_user` (Primary Key, Auto Increment)
* `nama_lengkap` (Varchar)
* `email` (Varchar)
* `password` (Varchar - *jangan lupa di-hash pakai password_hash() saat register*)
* `role` (Enum: 'admin', 'pasien')


* **Tabel `poli**` (Menyimpan data poli/layanan di klinik)
* `id_poli` (Primary Key, Auto Increment)
* `nama_poli` (Varchar, misal: Poli Umum, Poli Gigi)
* `keterangan` (Text)


* **Tabel `antrian**` (Menyimpan transaksi/booking)
* `id_antrian` (Primary Key, Auto Increment)
* `id_user` (Foreign Key dari tabel `users`)
* `id_poli` (Foreign Key dari tabel `poli`)
* `tanggal_berobat` (Date)
* `no_antrian` (Int)
* `status` (Enum: 'Menunggu', 'Diproses', 'Selesai', 'Batal')



---

### 2. Struktur Direktori dan File (Native PHP)

Agar kodemu rapi dan tidak pusing saat *debugging*, pisahkan file berdasarkan fungsinya. Berikut adalah susunan folder yang direkomendasikan:

```text
📁 klinik-app/
├── 📁 config/
│   └── database.php           # Koneksi ke MySQL (mysqli)
├── 📁 auth/
│   ├── login.php              # Halaman Login
│   ├── register.php           # Halaman Registrasi
│   ├── proses_login.php       # Logika cek email, password, dan set Session
│   └── logout.php             # Logika destroy Session
├── 📁 admin/
│   ├── dashboard.php          # Tampilan Read (Daftar semua antrian pasien)
│   ├── kelola_poli.php        # Tampilan CRUD untuk Master Data Poli
│   ├── proses_update.php      # Logika Update (Mengubah status antrian)
│   └── proses_delete.php      # Logika Delete (Menghapus antrian/poli)
├── 📁 pasien/
│   ├── dashboard.php          # Tampilan form booking & riwayat antrian pasien
│   ├── proses_booking.php     # Logika Create (Insert jadwal ke tabel antrian)
│   └── cetak_antrian.php      # Implementasi PHP Library (Generate PDF)
├── 📁 vendor/                 # Folder otomatis hasil Composer (PHP Library)
├── composer.json              # Konfigurasi library
└── index.php                  # Halaman Landing Page (Arahkan ke login)

```

---

### 3. Alur Penggunaan (User Flow) & Fitur CRUD

**A. Alur Pasien (User biasa):**

1. **Registrasi:** Pasien membuat akun dengan email dan password.
2. **Login:** Pasien masuk (Session `role` = 'pasien').
3. **Create:** Pasien memilih Poli dan Tanggal Berobat di dashboard. Sistem otomatis meng-generate nomor antrian (misal: mengambil nomor terakhir di tanggal tersebut lalu ditambah 1).
4. **Read:** Pasien melihat riwayat antriannya beserta status saat ini (Menunggu/Selesai).
5. **Cetak (PHP Library):** Pasien mengklik tombol "Cetak Tiket", sistem mengunduh file PDF berisi Nomor Antrian, Nama Poli, dan Tanggal.

**B. Alur Admin:**

1. **Login:** Admin masuk (Session `role` = 'admin').
2. **Read:** Admin melihat tabel daftar semua pasien yang antri hari ini. Desain tabel dibuat responsif menggunakan Tailwind CSS.
3. **Update:** Admin mengklik tombol aksi untuk mengubah status antrian pasien dari "Menunggu" menjadi "Diproses" lalu "Selesai".
4. **Delete:** Admin bisa menghapus data antrian jika ada pasien yang *spam* atau tidak datang.

---

### 4. Integrasi Teknologi & Tips Implementasi

* **Tailwind CSS:** Gunakan Play CDN agar kamu tidak perlu menginstal Node.js. Cukup tempel script ini di dalam tag `<head>` pada setiap file *view* kamu:
`<script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>`
* **PHP Library (Dompdf):** Gunakan **Dompdf** untuk membuat fitur cetak tiket. Ini library paling mudah untuk mengonversi HTML langsung ke PDF.
* Cara pasangnya di terminal: `composer require dompdf/dompdf`
* Panggil autoloader di file `cetak_antrian.php`: `require '../vendor/autoload.php';`



Langkah pertama yang paling krusial dalam PHP Native adalah memastikan *database* sudah terhubung dengan benar. Apakah kamu ingin saya buatkan kode `database.php` untuk koneksi MySQL beserta *query SQL* untuk membuat ketiga tabel di atas agar bisa langsung kamu gunakan?