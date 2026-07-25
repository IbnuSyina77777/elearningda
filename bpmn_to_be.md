# Business Process Model and Notation (BPMN) To-Be: E-Learning System

Berikut adalah rancangan proses bisnis **To-Be** (yang diharapkan) untuk aplikasi E-Learning ini, mencakup dua pilar utama: **Kegiatan Belajar Mengajar (KBM)** dan **Administrasi Keuangan**.

Rancangan ini menggunakan *Swimlanes* untuk memisahkan peran dari masing-masing aktor: **Admin**, **Guru**, **Siswa**, dan **Sistem (E-Learning & Midtrans)**.

```mermaid
flowchart TD
    %% Styling
    classDef startEnd fill:#d4edda,stroke:#28a745,stroke-width:2px,shape:circle;
    classDef process fill:#cce5ff,stroke:#007bff,stroke-width:2px;
    classDef system fill:#e2e3e5,stroke:#383d41,stroke-width:2px,stroke-dasharray: 5 5;
    classDef decision fill:#fff3cd,stroke:#ffc107,stroke-width:2px,shape:diamond;
    
    subgraph Admin [Admin]
        direction TB
        A_Start((Mulai)):::startEnd
        A_Master[Kelola Data Master<br/>Siswa, Guru, Kelas]:::process
        A_Kategori[Buat Kategori Pembayaran<br/>Pilih Target: Kelas / Perorangan]:::process
        
        A_Start --> A_Master
        A_Master --> A_Kategori
    end

    subgraph Guru [Guru]
        direction TB
        G_Start((Mulai)):::startEnd
        G_Materi[Buat Materi & Tugas]:::process
        G_Nilai[Periksa Tugas & Beri Nilai]:::process
        
        G_Start --> G_Materi
    end

    subgraph Siswa [Siswa]
        direction TB
        S_Start((Mulai)):::startEnd
        S_Belajar[Akses Materi & Kerjakan Tugas]:::process
        S_Tagihan[Cek Notifikasi Tagihan]:::process
        S_Metode{Pilih Metode Pembayaran?}:::decision
        S_Bayar[Lakukan Pembayaran]:::process
        S_Selesai((Selesai)):::startEnd
        
        S_Start --> S_Belajar
        S_Belajar --> |Submit Tugas| G_Nilai
    end

    subgraph Sistem [Sistem E-Learning & Payment Gateway]
        direction TB
        Sys_Bill[Generate Tagihan Otomatis<br/>Berdasarkan Target]:::system
        Sys_Rekap[Rekap Nilai Siswa]:::system
        Sys_Midtrans[Gateway Midtrans<br/>Virtual Account / E-Wallet]:::system
        Sys_Webhook[Terima Webhook Payment]:::system
        Sys_Update[Update Status Tagihan<br/>Otomatis Lunas]:::system
        
        G_Nilai --> Sys_Rekap
        Sys_Rekap --> S_Selesai
        
        A_Kategori --> Sys_Bill
        Sys_Bill --> |Sistem Memberi Notif| S_Tagihan
        
        S_Tagihan --> S_Metode
        S_Metode --> |Pilih Bank/Qris| Sys_Midtrans
        Sys_Midtrans --> S_Bayar
        S_Bayar --> Sys_Webhook
        Sys_Webhook --> Sys_Update
        Sys_Update --> S_Selesai
    end
```

### Penjelasan Flow (To-Be)
1. **Pilar Akademik (KBM)**
   - **Guru** menyiapkan materi dan tugas.
   - **Siswa** mengakses materi tersebut, lalu mengerjakan dan mensubmit tugas.
   - **Guru** memberikan penilaian yang kemudian masuk ke **Sistem** untuk direkap (Transkrip/Rapor otomatis).

2. **Pilar Administrasi (Keuangan)**
   - **Admin** mengatur data utama dan membuat *Kategori Pembayaran*. Pada sistem *To-Be*, Admin bisa memilih apakah kategori ditujukan untuk **Satu Kelas** penuh atau **Perorangan (Siswa tertentu)**.
   - **Sistem** secara otomatis men-generate data tagihan (Billing) berdasarkan target yang dipilih Admin dan menampilkannya di dashboard siswa terkait.
   - **Siswa** melihat tagihan dan memilih saluran pembayaran melalui integrasi **Midtrans** (Virtual Account, QRIS, dll).
   - Setelah siswa membayar, Midtrans mengirimkan **Webhook** ke sistem.
   - **Sistem** memperbarui status tagihan menjadi *Lunas (Paid)* secara *real-time* tanpa perlu konfirmasi manual dari Admin.
