# HOME CARE UNAI | Demo modern Laravel 13 + Tailwind CSS 4

Repositori hasil modernisasi project `PROJECT.rar` (7 halaman HTML asli) menjadi aplikasi demonstrasi Home Care dengan dua tampilan yang berbeda: **pelanggan/viewer** dan **admin**.

> **PERHATIAN:** ini **prototipe**, bukan layanan kesehatan resmi, bukan sistem pengelolaan informasi medis, dan tidak memproses pembayaran. Semua contoh tarif, profil tenaga kesehatan, status, serta wilayah adalah fiktif. Login sengaja dilewati sesuai kebutuhan demo. **Jangan deploy secara publik sebagai aplikasi produksi** tanpa autentikasi, otorisasi, audit privasi, validasi petugas, keamanan data, dan integrasi operasional yang memadai.

## Pilihan A: buka demo tanpa install (untuk review paling cepat)

1. Buka folder `preview`.
2. Klik dua kali **`index.html`** di browser modern. Jika browser memblokir akses lokal, jalankan server statis: `python -m http.server 8765 --directory preview`, lalu akses `http://127.0.0.1:8765`.
3. Klik **Masuk**. Pilih **Masuk sebagai pelanggan** atau **Masuk sebagai admin** (tanpa password).
4. Coba buat pesanan dari menu **Pesan layanan**. Setelah dibuat, sistem membuka **Lihat Struk**. Anda dapat **Download PDF** atau **Cetak**.
5. Semua CRUD pada **preview** memakai `localStorage` browser, sehingga data demo bertahan setelah halaman di-refresh. Admin memiliki menu **Pengaturan → Reset semua data demo** untuk memulai dari awal.

**Tidak membutuhkan Node, Composer, API, atau database** untuk versi preview. Preview dirancang sebagai simulasi yang berjalan mandiri, bukan Laravel yang sedang di-host.

## Pilihan B: backend Laravel asli (untuk pengembangan)

### Prasyarat

- PHP 8.3+ dengan ekstensi Laravel standar, khususnya `mbstring`, `PDO_SQLite` (untuk database contoh), `openssl`, `fileinfo`, `ctype`, `xml`, `tokenizer`.
- Composer 2.x.
- Node.js versi terbaru yang kompatibel dengan Vite 8, dan npm.

Jalankan dari folder project:

```bash
composer install
cp .env.example .env                 # Windows: copy .env.example .env
php artisan key:generate
# Linux/macOS: touch database/database.sqlite
# Windows PowerShell: New-Item database/database.sqlite -ItemType File
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Akses `http://127.0.0.1:8000`. Jika ingin hot reload ketika mengembangkan antarmuka, pada terminal tambahan jalankan `npm run dev`.

File `.env.example` menggunakan `HOMECARE_DEMO_MODE=true`, **hanya untuk development lokal**. Jika dinonaktifkan, halaman demo diblokir; untuk produksi autentikasi dan middleware role yang sebenarnya harus dibangun dulu.

## Fitur yang tersedia

### Pelanggan / viewer

- Beranda responsif, katalog layanan dengan kategori, profil tenaga kesehatan, ulasan publik, informasi bantuan, dan pendaftaran mitra.
- Simulasi pemesanan lengkap: layanan, unit/durasi, jarak tempuh manual, tanggal dan jam, opsi mendesak, add-on, kode promo, dan data kontak/alamat.
- Rincian biaya **langsung berubah** saat pemesan mengubah pilihan.
- Konfirmasi pesanan demo, riwayat pemesanan, status layanan, **pratinjau struk**, **unduh PDF struk**, dan cetak.
- Struk mencakup nomor pemesanan, pemesan, jadwal, alamat, tenaga kesehatan (bila ada), semua komponen biaya, diskon, serta total.

### Admin

- Dashboard ringkasan total pesanan, nilai estimasi, petugas tersedia, dan progres status.
- CRUD layanan, tim medis, pelanggan, promo, area layanan, pendaftaran mitra, dan ulasan.
- Daftar pesanan dengan pencarian, ubah status, tetapkan tenaga kesehatan, dan hapus pesanan.
- Pencegahan penugasan staf tidak tersedia dan bentrok jadwal untuk slot tanggal yang sama.
- **Laporan operasional** dan ekspor CSV. **Pengaturan** simulasi dan reset preview.
- Tombol struk di setiap baris pemesanan; sama seperti viewer, dapat diunduh atau dicetak.

### Petugas / caregiver

- Login demo khusus petugas tanpa kredensial.
- Dashboard yang hanya menampilkan penugasan untuk profil petugas contoh.
- Ringkasan jadwal aktif, lokasi kunjungan, status, dan catatan kebutuhan.
- Semua identitas, kredensial, alamat, dan aktivitas diberi konteks sebagai data fiktif demo.

### Alur pemesanan lima langkah

1. Pilih layanan dan durasi.
2. Tentukan tanggal, waktu, serta prioritas.
3. Pilih add-on dan kode promo.
4. Isi informasi pemesan dan lokasi kunjungan.
5. Tinjau seluruh data serta estimasi biaya sebelum konfirmasi.

### Harga simulasi

Rumus: `total = (tarif dasar × unit) + perjalanan + malam + akhir pekan + prioritas + addon + platform − diskon`.

| Komponen | Aturan demo |
|---|---|
| Tarif | Tergantung layanan per jam, kunjungan, atau hari |
| Perjalanan | 5 km pertama gratis, berikutnya Rp4.500/km (dibulatkan ke atas) |
| Malam | +20% tarif dasar pada 20:00–05:59 WIB |
| Akhir pekan | +10% tarif dasar (Sabtu dan Minggu) |
| Prioritas | +Rp75.000 |
| Platform | Rp5.000 |
| Add-on | Paket perlengkapan Rp40.000; laporan Rp15.000; pendamping Rp90.000 |
| Promo | CARE10 (10%, maks. Rp50.000); HEMAT25 (Rp25.000) |

Jarak **tidak** dihitung dari GPS/Google Maps; itu adalah input manual untuk demo. Harga ini tidak mewakili tarif nyata dari universitas/perusahaan.

## Struktur folder

```text
app/Http/Controllers/Api/       # API quotation, booking, CRUD, bootstrap
app/Http/Middleware/            # Pembatasan halaman admin untuk sesi demo
app/Models/                     # Model layanan, booking, customer, promo, dll.
app/Services/PricingEngine.php  # Sumber kalkulasi harga server
config/homecare.php             # Aturan tarif demo
routes/web.php                  # Route SPA dan API
resources/js/app.js             # Antarmuka interaktif tunggal
resources/js/pricing.js         # Perhitungan harga reaktif di client
resources/css/app.css           # Tailwind v4 + custom responsive design
resources/views/app.blade.php   # Shell Laravel
preview/                        # Demo mandiri tanpa backend
    index.html
    assets/app.js               # Bundle offline dari sumber antarmuka sama
    assets/app.css
    assets/logo-unai.png
    assets/homecare-legacy.png
 database/migrations/           # Tabel relasional SQLite/MySQL
 database/seeders/              # Data contoh untuk Laravel
 tests/                          # Unit/feature tests
```

> `preview/assets/app.js` dan `preview/assets/app.css` merupakan build manual mandiri yang dihasilkan dari sumber `resources/`, sehingga **jika mengubah sumber** perlu membangun ulang preview secara terpisah. Pada Laravel, `npm run build` menjalankan Vite dan Tailwind secara normal.

## Pengujian

```bash
npm run check                  # Tes JavaScript, perhitungan dan simulasi struk
php artisan test              # Tes feature Laravel setelah composer install
```

GitHub Actions menjalankan kedua jalur tersebut pada setiap push ke `main` dan pull request. CI menggunakan SQLite untuk pengujian Laravel.

## Arsitektur deployment

`vercel.json` menyajikan folder `preview/` sebagai frontend demo di Vercel. Preview menyimpan data simulasi di browser dan **bukan** backend Laravel produksi.

Laravel tetap menjadi backend sumber kebenaran untuk quotation, booking, penugasan, CRUD, dan penyimpanan snapshot harga. Deployment operasional memerlukan hosting PHP terpisah serta PostgreSQL/MySQL persisten; kredensial dan URL API harus dikonfigurasi melalui environment variables, bukan dikomit ke Git.

Target frontend demo:

- Project: `home-care-zvenians`
- URL: `https://home-care-zvenians.vercel.app`
- Branch produksi: `main`

Pada lingkungan pembuatan project ini, `npm run check` dan lint seluruh PHP berhasil. **Pengujian Laravel secara langsung belum dilakukan** karena Composer dan ekstensi PHP untuk database tidak tersedia di lingkungan pembuat, bukan karena sudah diverifikasi berjalan.

## Peta menu lama dan baru

| Sebelum | Sesudah |
|---|---|
| `index.html` | Beranda pelanggan |
| `layanan.html` | Katalog layanan + simulasi tarif |
| `pemesanan.html` | Form pemesanan + rincian harga + riwayat + struk PDF |
| `daftarPerawat.html` | Pendaftaran mitra + pengelolaan lamaran admin |
| `DaftarAkun.html`, `login.html` | UI login demo (langsung masuk tanpa password) |
| `gabung.html` | Fitur komunitas/partisipasi di pendaftaran mitra |
| CRUD MockAPI / browser | Endpoint CRUD Laravel + migrasi + seeder (dan fallback preview localStorage) |

## Berikutnya sebelum produksi

- Auth user sesungguhnya, role/permission berbasis policy, CSRF/session hardening dan audit akses per pasien.
- Perizinan/validasi kompetensi tenaga kesehatan, kepatuhan perlindungan data pribadi dan manajemen persetujuan.
- Geocoding/routing API agar jarak dihitung berdasarkan rute, bukan estimasi manual.
- Availability engine yang lebih kompleks (zona waktu, cuti, perjalanan, kalender), pembayaran terverifikasi, pembatalan/refund, dan invoice resmi.
- Notifikasi WA/email, manajemen file secara aman, log/audit, rate limiting dan tes end-to-end lintas browser.
