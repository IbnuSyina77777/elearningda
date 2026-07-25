# DRAFT PROPOSAL / DOKUMEN SKRIPSI
**Judul:** Rancang Bangun Sistem Informasi E-Learning Terintegrasi Payment Gateway Menggunakan Framework Laravel

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Perkembangan teknologi informasi saat ini telah memberikan dampak yang signifikan terhadap berbagai sektor, termasuk sektor pendidikan. Sistem E-Learning telah menjadi solusi utama dalam memfasilitasi proses Kegiatan Belajar Mengajar (KBM) agar dapat berjalan lebih fleksibel tanpa batasan ruang dan waktu. Namun, sebagian besar institusi pendidikan masih memisahkan antara sistem akademik (KBM) dengan sistem administrasi keuangan (pembayaran SPP/tagihan sekolah). 

Pemisahan ini sering kali menyebabkan inefisiensi, seperti tata usaha yang harus merekap pembayaran secara manual, serta siswa yang harus melakukan konfirmasi pembayaran dengan mengirimkan bukti transfer fisik/digital kepada pihak sekolah. 

Oleh karena itu, diperlukan sebuah sistem E-Learning yang tidak hanya menangani pengelolaan materi dan tugas, tetapi juga terintegrasi langsung dengan *Payment Gateway* (seperti Midtrans) untuk menangani pembayaran tagihan sekolah secara otomatis dan *real-time*. Dengan adanya sistem ini, diharapkan proses akademik dan keuangan dapat berjalan beriringan secara transparan, efektif, dan efisien.

### 1.2 Rumusan Masalah
Berdasarkan latar belakang di atas, rumusan masalah dalam penelitian ini adalah:
1. Bagaimana merancang dan membangun sistem E-Learning yang dapat mengelola KBM (materi, tugas, penilaian) secara efektif?
2. Bagaimana mengintegrasikan sistem pembayaran tagihan sekolah menggunakan *Payment Gateway* (Midtrans) ke dalam platform E-Learning tersebut?
3. Bagaimana mengotomatisasi verifikasi pembayaran tagihan siswa tanpa campur tangan manual dari pihak admin/tata usaha?

### 1.3 Tujuan Penelitian
Tujuan dari penelitian ini adalah:
1. Menghasilkan sistem E-Learning berbasis *web* yang mempermudah interaksi akademik antara guru dan siswa.
2. Mengimplementasikan integrasi API *Payment Gateway* (Midtrans) untuk memfasilitasi pembayaran tagihan secara mandiri oleh siswa.
3. Membangun sistem yang mampu memverifikasi status pembayaran tagihan secara otomatis melalui mekanisme *Webhook*.

### 1.4 Batasan Masalah
Agar penelitian lebih terarah, diberikan batasan masalah sebagai berikut:
1. Sistem dibangun menggunakan arsitektur MVC dengan *framework* PHP Laravel.
2. Proses KBM dibatasi pada pengelolaan materi, pengumpulan tugas, dan input nilai. (Tidak mencakup *Video Conference* bawaan).
3. Saluran pembayaran bergantung pada layanan yang disediakan oleh *Payment Gateway* Midtrans (Virtual Account, QRIS, dll).

---

## BAB II: TINJAUAN PUSTAKA

### 2.1 E-Learning
E-Learning adalah suatu sistem pembelajaran yang memanfaatkan media elektronik dan jaringan internet untuk menyampaikan materi pembelajaran, berinteraksi, maupun mengevaluasi peserta didik.

### 2.2 Payment Gateway
*Payment Gateway* merupakan layanan aplikasi *e-commerce* yang mengotorisasi pembayaran kartu kredit atau pembayaran langsung bagi e-bisnis maupun retail online. Dalam penelitian ini, Midtrans digunakan sebagai pihak ketiga yang menangani kompleksitas integrasi dengan berbagai bank di Indonesia.

### 2.3 Framework Laravel
Laravel adalah kerangka kerja (*framework*) aplikasi *web* berbasis PHP yang bersifat *open-source*. Laravel menggunakan konsep MVC (Model-View-Controller) yang memisahkan antara logika sistem, antarmuka pengguna, dan interaksi basis data, sehingga kode menjadi lebih rapi dan terstruktur.

---

## BAB III: METODOLOGI PENELITIAN

### 3.1 Metode Pengembangan Sistem
Penelitian ini menggunakan metode pengembangan sistem SDLC (*Software Development Life Cycle*) dengan model *Waterfall*, yang meliputi tahapan:
1. **Analisis Kebutuhan**: Mengumpulkan kebutuhan fungsional sistem akademik dan keuangan.
2. **Desain Sistem**: Pembuatan Use Case Diagram, Activity Diagram, Sequence Diagram, dan Entity Relationship Diagram (ERD).
3. **Implementasi**: Penulisan kode program menggunakan Laravel 11 dan *database* MySQL.
4. **Pengujian**: Melakukan uji fungsionalitas menggunakan metode *Black-box Testing*.

---

## BAB IV: HASIL DAN PEMBAHASAN

### 4.1 Analisis Sistem yang Diusulkan (To-Be)
Sistem yang diusulkan menggabungkan dua pilar utama. Pilar pertama menangani manajemen akademik yang memungkinkan guru membagikan modul dan menilai siswa. Pilar kedua menangani manajemen keuangan di mana admin (TU) dapat men-generate tagihan yang kemudian akan dibayar oleh siswa melalui antarmuka Midtrans. Verifikasi dilakukan otomatis oleh sistem saat Midtrans mengirimkan notifikasi *Settlement*. 

### 4.2 Perancangan Sistem (*System Design*)
*(Catatan: Anda dapat melampirkan gambar dari file HTML yang telah digenerate sebelumnya di bagian ini)*
1. **Use Case Diagram**: Menggambarkan interaksi Admin, Guru, Siswa, dan Sistem Midtrans.
2. **Activity Diagram**: Menggambarkan alur aktivitas belajar dan proses transaksi siswa.
3. **Sequence Diagram**: Memvisualisasikan pertukaran pesan dari saat *Checkout* tagihan hingga *Webhook* diterima.
4. **ERD (Entity Relationship Diagram)**: Memetakan relasi antara tabel `users`, `students`, `bills`, dan `transactions`.

### 4.3 Implementasi dan Integrasi
Bagian ini membahas antarmuka pengguna (*User Interface*) yang dirancang, seperti:
- **Halaman Dashboard Siswa**: Menampilkan jadwal dan daftar tagihan tertunda (Pending Bills).
- **Halaman Checkout**: Menampilkan antarmuka Snap Midtrans.
- **Logika Webhook**: *Controller* yang dikonfigurasi untuk menerima respon HTTP dari Midtrans guna memperbarui tabel basis data.

---

## BAB V: PENUTUP

### 5.1 Kesimpulan
1. Sistem E-Learning terintegrasi *Payment Gateway* telah berhasil dirancang dan dibangun menggunakan Laravel, memfasilitasi kebutuhan akademik sekaligus menertibkan administrasi keuangan sekolah.
2. Integrasi Midtrans berhasil menggeser paradigma pembayaran manual menjadi otomatis, di mana siswa dapat membayar tagihan dengan berbagai metode pembayaran digital kapan saja dan di mana saja.
3. Otomatisasi melalui *webhook* secara signifikan memotong beban kerja tata usaha dalam memverifikasi dan mencatat bukti transaksi satu per satu.

### 5.2 Saran
1. Sistem dapat dikembangkan lebih lanjut dengan menambahkan fitur *Computer Based Test* (CBT) interaktif untuk ujian pilihan ganda.
2. Penambahan integrasi notifikasi WhatsApp Gateway untuk memberi peringatan kepada orang tua/siswa saat jatuh tempo tagihan tiba.
