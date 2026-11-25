# Home Care UNAI — arah desain dan batasan implementasi

## Identitas visual

Antarmuka menggunakan burgundy, cranberry red, pale rose, dan permukaan putih dengan logo UNAI. Foto tenaga kesehatan disimpan sebagai WebP lokal; sumber dan hak pakainya tercatat di [IMAGE_CREDITS.md](IMAGE_CREDITS.md). Tipografi dan komposisi mengikuti source CSS yang digunakan di aplikasi.

## Perilaku antarmuka

Tampilan pelanggan, admin, dan petugas memiliki alur masing-masing. Pemesanan menggunakan lima langkah dengan validasi dan rincian biaya langsung. Interaksi hover dan masuk halaman tidak boleh menghalangi konten, fokus keyboard, atau tampilan mobile.

## Batasan teknis

Laravel tetap menjadi backend untuk pemesanan dan operasi data. Preview Vercel adalah demonstrasi statis mandiri dengan penyimpanan lokal dan tidak merepresentasikan backend Laravel produksi. Tarif dan struk pada preview harus konsisten dengan aturan demo; data contoh tidak boleh ditampilkan sebagai layanan klinis sungguhan.

## Pemeriksaan sebelum perubahan dinyatakan selesai

Periksa alur pelanggan, admin, dan petugas; kalkulasi biaya; CRUD demo; tampilan dan ekspor struk; layout mobile; console browser; serta hasil build. Untuk backend PHP, jalankan pengujian Laravel pada lingkungan dengan Composer dan database demo terpisah.
