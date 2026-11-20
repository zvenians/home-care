# Notes: hasil review awal project lama

File sumber: `PROJECT.rar`, berisi `PROJECT/index.html`, `layanan.html`, `pemesanan.html`, `login.html`, `DaftarAkun.html`, `daftarPerawat.html`, `gabung.html`, file CSS, dan gambar logo/banner.

Arah modernisasi:
- konsep Home Care UNAI dan alur menu utama dipertahankan
- Bootstrap 4 / Vue 2 dan data eksternal MockAPI pada proyek lama diganti struktur Laravel 13 + Tailwind CSS 4 + endpoint API lokal
- tampilan pelanggan dan admin dipisah, login bypass khusus demo
- pemesanan memiliki pricing engine yang mempertimbangkan durasi, jarak, waktu, prioritas, add-on, promo dan total estimasi
- CRUD basis data dan riwayat struk ditambahkan
- PDF struk dibuat lokal pada browser tanpa library tambahan

Batasan: Tidak semua perubahan dapat dianggap teruji sepenuhnya sebelum menjalankan Composer, migrasi, dan tes browser di lingkungan Laravel sebenarnya. Ini adalah prototipe fungsional untuk direview, bukan produk siap deploy.
