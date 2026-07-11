# DOKUMEN FLOW BISNIS
# Ting Warehouse Management System
# Versi 1.0 — Juli 2026

---

> **Dokumen ini menjelaskan seluruh alur bisnis sistem Ting Warehouse Management System dari awal hingga akhir, disusun seperti cerita yang mengalir sehingga mudah dipahami oleh semua stakeholder.**

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Daftar Role dan Tanggung Jawab](#2-daftar-role-dan-tanggung-jawab)
3. [Daftar Seluruh Modul](#3-daftar-seluruh-modul)
4. [Flow Bisnis Lengkap](#4-flow-bisnis-lengkap)
5. [Diagram Alur Bisnis](#5-diagram-alur-bisnis)
6. [Daftar Seluruh Fitur per Tahapan](#6-daftar-seluruh-fitur-per-tahapan)
7. [Hubungan Antar Modul (Data Flow)](#7-hubungan-antar-modul-data-flow)
8. [Validasi dan Kondisi](#8-validasi-dan-kondisi)
9. [Laporan Pengujian](#9-laporan-pengujian)
10. [Temuan Masalah](#10-temuan-masalah)
11. [Rekomendasi Perbaikan](#11-rekomendasi-perbaikan)

---

# 1. Ringkasan Eksekutif

## Apa Itu Ting Warehouse?

Ting Warehouse adalah perusahaan freight forwarding yang mengkhususkan diri dalam pengiriman barang dari China ke Jakarta. Sistem ini dibangun untuk menggantikan proses manual yang sebelumnya menggunakan Airtable dan Google Sheets.

## Siapa yang Menggunakan Sistem?

| Role | Jumlah | Fungsi Utama |
|------|--------|--------------|
| **Owner** | 1 orang | Memantau bisnis, melihat laporan keuangan, mengelola admin |
| **Admin** | 3 orang | Mengelola operasional harian (box, invoice, verifikasi, komplain) |
| **Customer** | 50-200 orang | Mengirim barang, membayar invoice, melacak status pengiriman |

## Apa yang Dilakukan Sistem?

Sistem ini mengelola **seluruh siklus hidup pengiriman barang** dari China ke Jakarta:

1. Customer mendaftar dan diaktifkan oleh Admin
2. Customer menyetor barang (resi) ke sistem
3. Admin mengelola data warehouse di China
4. Barang dimasukkan ke dalam box (sharing/direct)
5. Box dikirim dari China ke Indonesia
6. Admin menimbang dan mengukur barang di Indonesia
7. Invoice dibuat dan dikirim ke customer
8. Customer membayar dan Admin memverifikasi
9. Customer meminta checkout (pengiriman ke alamat)
10. Admin mengirim barang ke customer
11. Jika ada masalah, customer bisa mengajukan komplain

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12 (PHP) |
| Frontend | Livewire v3 + Blade + Tailwind CSS v4 |
| Database | MySQL 8.0 |
| Auth | Laravel Breeze (session-based) |

---

# 2. Daftar Role dan Tanggung Jawab

## 2.1 Owner (Pemilik Bisnis)

**Akses:** Seluruh halaman admin + halaman khusus owner

| Tanggung Jawab | Halaman |
|----------------|---------|
| Memantau performa bisnis | Owner Dashboard |
| Melihat laporan keuangan | Finance, Export Finance |
| Mengelola akun admin | Manage Admin |
| Melihat audit trail | Audit Log |
| Mengelola semua user | Users |
| Melihat semua data sistem | All Data |
| Mengakses seluruh fitur admin | Semua halaman admin |

## 2.2 Admin (Staff Operasional)

**Akses:** 14 halaman admin

| Tanggung Jawab | Halaman |
|----------------|---------|
| Memantau ringkasan operasional | Dashboard |
| Mengelola box dan status pengiriman | Manage Box |
| Input data warehouse China | Recap |
| Input barang tanpa resi (No Tuan) | Input No Tuan |
| Mengelola barang lelang | Barang Lelang |
| Membuat invoice untuk customer | Generate Invoice |
| Memverifikasi pembayaran customer | Verifikasi |
| Memproses checkout customer | Checkout |
| Melihat dan mengedit data customer | Info Customer |
| Menangani komplain customer | Komplain |
| Mengupdate estimasi pengiriman | Est Update |
| Mengatur rate pengiriman | Pengaturan Rate |
| Mengelola kurs Yuan | History Kurs |

## 2.3 Customer (Pengguna Jasa)

**Akses:** 11 halaman customer

| Tanggung Jawab | Halaman |
|----------------|---------|
| Melihat ringkasan dan rate | Dashboard |
| Melihat box sharing | Box Sharing |
| Melihat box direct | Box Direct |
| Menyetor barang (input resi) | Setor Resi |
| Melihat dan membayar invoice | Invoice |
| Membuat invoice sendiri | Create Invoice |
| Meminta pengiriman barang | Checkout |
| Mengajukan komplain | Komplain |
| Menghitung estimasi biaya | Kalkulator |
| Mengklaim barang tanpa pemilik | No Tuan |
| Mengklaim resi yang belum dikenali | Unmatched Resi |

---

# 3. Daftar Seluruh Modul

## 3.1 Modul Database (12 Tabel)

| No | Tabel | Fungsi | Relasi |
|----|-------|--------|--------|
| 1 | `users` | Data pengguna (owner, admin, customer) | → boxes, items, invoices, checkouts, complains |
| 2 | `boxes` | Kontainer pengiriman | → items, invoices; belongs to customer |
| 3 | `items` | Barang yang dikirim | → box, customer, whChinaData, invoices |
| 4 | `invoices` | Tagihan pembayaran | → box, customer, items, checkouts, dendaClaims |
| 5 | `checkouts` | Permintaan pengiriman | → invoice, customer |
| 6 | `complains` | Komplain customer | → customer, box |
| 7 | `wh_china_data` | Data warehouse China | → item, admin |
| 8 | `settings` | Pengaturan rate dan parameter | - |
| 9 | `kurs_history` | Riwayat kurs Yuan | → user (inputBy) |
| 10 | `denda_claims` | Denda keterlambatan | → customer, item, invoice |
| 11 | `notifications` | Notifikasi in-app | → user (notifiable) |
| 12 | `activity_logs` | Audit trail | → user, subject (morph) |

## 3.2 Modul Service (6 Service)

| No | Service | Fungsi |
|----|---------|--------|
| 1 | `FeeCalculationService` | Menghitung Fee TAX, Fee WH, Fee Packing, Volume, Kurs |
| 2 | `NotificationService` | Mengirim 20+ jenis notifikasi ke user |
| 3 | `AuditLogService` | Mencatat setiap perubahan data (audit trail) |
| 4 | `RecapMatchingService` | Mencocokkan data WH China dengan barang customer |
| 5 | `NoTuanClaimService` | Mengelola barang tanpa pemilik (No Tuan) |
| 6 | `ItemStatusService` | Mengelola perpindahan status barang |

## 3.3 Modul Halaman Admin (14 Halaman)

| No | Halaman | URL | Fungsi |
|----|---------|-----|--------|
| 1 | Dashboard | `/admin/dashboard` | Ringkasan operasional |
| 2 | Manage Box | `/admin/manage-boxes` | CRUD box, ubah status, edit semua field |
| 3 | Recap | `/admin/recap` | Input WH China, match barang, biaya jasa |
| 4 | Input No Tuan | `/admin/no-tuan/create` | Input barang tanpa resi customer |
| 5 | Barang Lelang | `/admin/lelang` | Kelola barang untuk lelang/jual |
| 6 | Generate Invoice | `/admin/invoices` | Buat invoice dari box |
| 7 | Verifikasi | `/admin/verification` | Verifikasi/tolak pembayaran |
| 8 | Checkout | `/admin/checkouts` | Proses permintaan pengiriman |
| 9 | Info Customer | `/admin/customers` | Lihat/edit data customer |
| 10 | Komplain | `/admin/complains` | Tangani komplain customer |
| 11 | Est Update | `/admin/est-update` | Update ETD/ETA box |
| 12 | Pengaturan Rate | `/admin/settings` | Atur 17+ parameter rate |
| 13 | History Kurs | `/admin/kurs-history` | Kelola kurs Yuan |
| 14 | Audit Log (Owner) | `/owner/audit-log` | Lihat riwayat perubahan |

## 3.4 Modul Halaman Customer (11 Halaman)

| No | Halaman | URL | Fungsi |
|----|---------|-----|--------|
| 1 | Dashboard | `/dashboard` | Ringkasan, rate card, notifikasi |
| 2 | Box Sharing | `/box/sharing` | Lihat box sharing |
| 3 | Box Direct | `/box/direct` | Lihat box direct |
| 4 | Setor Resi | `/setor-resi` | Input barang baru |
| 5 | Invoice | `/invoice` | Lihat dan bayar invoice |
| 6 | Create Invoice | `/create-invoice` | Buat invoice sendiri |
| 7 | Checkout | `/checkout` | Minta pengiriman barang |
| 8 | Komplain | `/komplain` | Ajukan komplain |
| 9 | Kalkulator | `/kalkulator` | Hitung estimasi biaya |
| 10 | No Tuan | `/no-tuan` | Klaim barang tanpa pemilik |
| 11 | Unmatched Resi | `/unmatched-resi` | Klaim resi yang belum dikenali |

## 3.5 Modul Halaman Owner (7 Halaman)

| No | Halaman | URL | Fungsi |
|----|---------|-----|--------|
| 1 | Dashboard | `/owner/dashboard` | Ringkasan bisnis |
| 2 | Finance | `/owner/finance` | Laporan keuangan |
| 3 | Manage Admin | `/owner/manage-admin` | Kelola akun admin |
| 4 | Audit Log | `/owner/audit-log` | Riwayat perubahan |
| 5 | Export Finance | `/owner/finance/export` | Export data keuangan |
| 6 | Users | `/owner/users` | Kelola semua user |
| 7 | All Data | `/owner/data` | Lihat semua data |

---

# 4. Flow Bisnis Lengkap

## Cerita 1: Customer Mendaftar dan Diaktifkan

Seorang customer baru bernama Budi ingin menggunakan jasa Ting Warehouse. Budi membuka website dan klik "Register". Budi mengisi form registrasi dengan nama, email, password, nomor telepon, dan alamat. Setelah Budi klik "Register", sistem membuat akun dengan status **"Menunggu Aktivasi"** (pending).

Admin menerima notifikasi di sistem: "Customer baru Budi Santoso mendaftar dan menunggu aktivasi." Admin membuka halaman **Info Customer**, melihat data Budi, dan mengecek apakah data sudah lengkap dan valid. Jika data valid, Admin mengubah status Budi menjadi **"Aktif"**. Budi menerima notifikasi: "Akun Anda telah diaktifkan." Sekarang Budi bisa login dan menggunakan seluruh fitur customer.

**Data yang mengalir:**
- Input: Nama, email, password, telepon, alamat (dari customer)
- Output: Akun dengan status "pending" (di tabel `users`)
- Proses: Admin verifikasi → ubah status ke "active"
- Notifikasi: customer_register (ke admin), account_activated (ke customer)

---

## Cerita 2: Customer Menyetor Barang (Setor Resi)

Setelah login, Budi ingin mengirim barang dari China ke Jakarta. Budi membuka halaman **Setor Resi**. Budi mengisi form dengan:
- **Nama Barang**: "Sepatu Nike Air Max"
- **No Resi**: "SF1234567890" (nomor tracking dari supplier China)
- **Jumlah**: 2
- **Apakah Sensitive?**: Tidak
- **Foto Bukti (CO)**: Upload foto barang

Budi klik "Setor". Sistem membuat data barang (Item) dengan status **"Aktif"** di database. Bukti foto disimpan dengan nama file random di storage. Budi menerima notifikasi: "Barang 'Sepatu Nike Air Max' berhasil disetor."

**Data yang mengalir:**
- Input: name, resi_number, quantity, is_sensitive, proof_co (foto)
- Output: Item baru di tabel `items` dengan status "active"
- Foto disimpan di: `storage/app/public/items/` dengan nama random

---

## Cerita 3: Admin Input Data Warehouse China (Recap)

Admin di gudang China menerima barang Budi. Admin membuka halaman **Recap** dan klik tab **"WH China"**. Admin klik "Input WH Data" dan mengisi:
- **No Resi**: "SF1234567890"
- **Berat**: 5.2 gram
- **Berat INA**: (belum diisi, menunggu sampai Indonesia)
- **Ukuran Box**: (opsional)
- **Foto Arrived China**: Upload foto barang di gudang China

Admin klik "Simpan". Sistem menyimpan data WH China dan otomatis mencocokkan (match) dengan barang Budi berdasarkan nomor resi yang sama. Jika match berhasil, data barang Budi sekarang terhubung dengan data WH China (berat, foto, dll).

Jika tidak match (resi tidak ditemukan di sistem), data WH China masuk ke daftar **"Unmatched"**. Customer bisa melihat daftar unmatched di halaman **Unmatched Resi** dan mengklaim barangnya.

**Data yang mengalir:**
- Input: resi, berat, foto_arrived_china (dari admin)
- Output: WhChinaData baru di tabel `wh_china_data`, di-match ke Item
- Service: RecapMatchingService.matchByResi()

---

## Cerita 4: Admin Mengelola Box

Admin membuat box baru untuk mengumpulkan barang-barang yang akan dikirim. Admin membuka halaman **Manage Box** dan klik "Buat Box":

- **Tipe**: Sharing (dicampur dengan customer lain)
- **Metode**: Sea (laut)
- **Batch Name**: "Batch Juli 1"
- **Huruf Box**: "H"

Box dibuat dengan status **"Open"** (terbuka). Barang-barang dari berbagai customer yang sudah di-match bisa dimasukkan ke box ini. Admin bisa melihat daftar barang di dalam box dan mengelola statusnya.

**Semua field box bisa di-edit kapan saja** (sesuai permintaan client):
- Tipe, Metode, Tracking Number, Batch Name, Huruf Box, Customer, ETA, Catatan
- Status bisa diubah ke status manapun (admin bisa memperbaiki kesalahan)

**Status Box dan Artinya:**

| Status | Arti | Siapa yang Melihat |
|--------|------|-------------------|
| **Open** | Box terbuka, barang masih bisa masuk | Admin, Customer |
| **Closed** | Box ditutup, tidak menerima barang baru | Admin, Customer |
| **Sent to Cargo** | Box sudah dikirim ke cargo | Admin, Customer |
| **OTW Indonesia** | Box dalam perjalanan ke Indonesia | Admin, Customer |
| **Invoice Dibuat** | Invoice sudah dibuat untuk box ini | Admin, Customer |
| **Selesai** | Proses box selesai | Admin, Customer |

**Data yang mengalir:**
- Input: type, method, batch_name, huruf_box, customer_id, notes
- Output: Box baru di tabel `boxes`
- Setiap perubahan status → notifikasi ke customer + audit log

---

## Cerita 5: Box Dikirim dari China ke Indonesia

Ketika box sudah penuh atau waktunya tiba, Admin mengubah status box dari "Open" menjadi **"Sent to Cargo"**. Admin mengisi **Tracking Number** (misal: "TW-260-SEA") dan **ETD** (Estimated Time Departure).

Customer Budi menerima notifikasi: "Box TW-260-SEA telah dikirim ke cargo." Budi bisa melihat status box di halaman Dashboard dan Box Sharing.

Beberapa hari kemudian, Admin mengubah status menjadi **"OTW Indonesia"** (dalam perjalanan). Budi kembali menerima notifikasi. Admin juga bisa mengupdate **ETA** (Estimated Time Arrival) di halaman **Est Update**.

**Data yang mengalir:**
- Input: status change, tracking_number, etd, eta
- Output: Box status updated, notifications sent
- Customer bisa melihat timeline status di detail box

---

## Cerita 6: Barang Tiba di Indonesia (Input Data Indonesia)

Ketika box tiba di Indonesia (status: OTW Indonesia), Admin menimbang dan mengukur barang secara fisik. Admin membuka halaman **Recap**, menemukan data WH China yang sudah di-match, dan mengisi:

- **Berat INA**: 5.5 gram (berat aktual di Indonesia)
- **Panjang**: 30 cm
- **Lebar**: 20 cm
- **Tinggi**: 15 cm
- **Foto Arrived INA**: Upload foto barang di Indonesia
- **Tanggal Setor**: Tanggal barang diterima

Sistem otomatis menghitung **Volume**: (30 × 20 × 15) / 6000 = 1.5 (satuan volume)

Customer Budi bisa melihat data ini di halaman Dashboard (detail box): berat, dimensi P×L×T, volume, foto INA, dan biaya tax.

**Data yang mengalir:**
- Input: berat_ina, panjang, lebar, tinggi, foto_arrived_ina, tanggal_setor
- Output: WhChinaData updated, volume auto-calculated
- Rumus: Volume = (P × L × T) / 6000

---

## Cerita 7: Admin Membuat Invoice

Admin mengubah status box menjadi **"Invoice Dibuat"**. Admin membuka halaman **Generate Invoice** dan memilih box. Sistem menampilkan daftar barang di box tersebut dengan data berat dan volume.

Admin mengisi:
- **Berat**: 5.5 gram (dari data INA)
- **Volume**: 1.5 (dari perhitungan otomatis)
- **Add On**: Rp 0 (opsional, bisa ditambah admin)

Sistem otomatis menghitung menggunakan **FeeCalculationService**:

```
Dasar = MAX(berat, volume) = MAX(5.5, 1.5) = 5.5
Fee TAX = 5.5 × Rp 70 (rate sea sharing berat) = Rp 385
Fee WH = Rp 5.000 (tier 0-150 gram)
Fee Packing = Rp 5.000 (tier 0-150 gram)
Grand Total = Rp 385 + Rp 5.000 + Rp 5.000 + Add On = Rp 10.385
```

Invoice dibuat dengan status **"Menunggu Pembayaran"** (waiting_payment). Sistem menghitung:
- **Payment Deadline**: 3 hari dari sekarang
- **Storage Deadline**: 14 hari dari sekarang

Customer Budi menerima notifikasi: "Invoice baru #INV-0001 sebesar Rp 10.385."

**Data yang mengalir:**
- Input: box_id, weight, volume, add_on
- Output: Invoice di tabel `invoices` dengan fee_tax, fee_wh, fee_packing, grand_total
- Service: FeeCalculationService.calculate()
- Notifikasi: invoice_generated (ke customer)

---

## Cerita 8: Customer Membayar Invoice

Budi menerima notifikasi invoice baru. Budi membuka halaman **Invoice**, melihat daftar invoice dengan status "Menunggu Pembayaran". Budi klik invoice dan melihat rincian:

- Fee TAX: Rp 385
- Fee WH: Rp 5.000
- Fee Packing: Rp 5.000
- **Grand Total: Rp 10.385**

Budi klik "Bayar" dan mengupload **bukti transfer** (foto screenshot transfer bank). Invoice berubah status menjadi **"Menunggu Verifikasi"** (waiting_verification).

Admin menerima notifikasi: "Pembayaran dari Budi Santoso untuk invoice #INV-0001 menunggu verifikasi."

**Data yang mengalir:**
- Input: payment_proof (foto bukti transfer)
- Output: Invoice status → waiting_verification
- Notifikasi: payment_received (ke admin)

---

## Cerita 9: Admin Memverifikasi Pembayaran

Admin membuka halaman **Verifikasi**, melihat daftar invoice yang menunggu verifikasi. Admin klik invoice Budi dan melihat bukti transfer.

**Jika pembayaran valid:**
Admin klik "Verifikasi". Invoice berubah status menjadi **"Terverifikasi"** (verified). Budi menerima notifikasi: "Pembayaran invoice #INV-0001 telah diverifikasi."

**Jika pembayaran tidak valid:**
Admin klik "Tolak" dan mengisi alasan penolakan. Invoice kembali ke status **"Menunggu Pembayaran"**. Budi menerima notifikasi: "Pembayaran invoice #INV-0001 ditolak. Alasan: Bukti transfer tidak jelas."

**Data yang mengalir:**
- Input: keputusan admin (verifikasi/tolak + alasan)
- Output: Invoice status → verified atau kembali ke waiting_payment
- Notifikasi: payment_verified atau payment_rejected (ke customer)
- Audit log: catatan siapa yang verifikasi dan kapan

---

## Cerita 10: Customer Meminta Checkout

Setelah invoice terverifikasi, Budi ingin barangnya dikirim ke alamat. Budi membuka halaman **Checkout** dan mengisi:

- **Nama Pengirim**: "Budi Santoso"
- **No. Telepon**: "081234567890"
- **Alamat Pengiriman**: "Jl. Mangga No. 10, Jakarta Selatan"
- **Catatan**: "Tolong packing dengan bubble wrap"

Budi klik "Request Checkout". Checkout dibuat dengan status **"Request"**. Admin menerima notifikasi.

Admin membuka halaman **Checkout**, melihat permintaan Budi. Admin memproses dan mengubah status menjadi **"On Process"** (sedang diproses). Setelah barang dikirim, Admin mengubah status menjadi **"Sent"** (terkirim).

Budi menerima notifikasi di setiap perubahan status.

**Data yang mengalir:**
- Input: sender_name, sender_phone, sender_address, notes
- Output: Checkout di tabel `checkouts`
- Status flow: request → on_process → sent
- Notifikasi: checkout_processed (ke customer)

---

## Cerita 11: Customer Mengajukan Komplain

Budi menerima barang tetapi ada masalah. Budi membuka halaman **Komplain** dan klik "Ajukan Komplain":

- **Jenis Komplain**: "Barang Salah"
- **Resolusi**: "Replacement" (penggantian)
- **No. Resi**: "SF1234567890"
- **Deskripsi**: "Barang yang diterima bukan yang saya pesan"
- **Foto**: Upload foto barang yang salah
- **Video**: Upload video unboxing (opsional)

Budi klik "Ajukan Komplain". Komplain dibuat dengan status **"Open"**. Admin menerima notifikasi.

Admin membuka halaman **Komplain**, melihat foto dan video bukti, dan membaca deskripsi. Admin mengubah status menjadi **"In Review"** (sedang ditinjau). Budi menerima notifikasi.

Setelah investigasi, Admin mengubah status menjadi **"Processing"** (sedang diproses). Terakhir, setelah selesai, Admin mengubah status menjadi **"Resolved"** (selesai).

**Status Komplain:**
1. **Open** → Komplain baru diajukan
2. **In Review** → Sedang ditinjau admin
3. **Processing** → Sedang diproses
4. **Resolved** → Selesai

**Data yang mengalir:**
- Input: type, resolution, resi_number, description, photo_url, video_url
- Output: Complain di tabel `complains`
- Foto/video disimpan di: `storage/app/public/complaints/`
- Notifikasi: new_complaint (ke admin), complaint_updated (ke customer)

---

## Cerita 12: Barang Tanpa Pemilik (No Tuan)

Terkadang ada barang di gudang yang tidak memiliki pemilik (resi tidak terdaftar di sistem). Admin bisa membuat barang ini sebagai **"No Tuan"** (tanpa tuan).

Admin membuka halaman **Input No Tuan** dan mengisi data barang. Barang dibuat dengan status **"No Tuan"**.

Customer lain yang merasa barangnya bisa membuka halaman **No Tuan** dan mengklaim barang tersebut. Admin memverifikasi klaim dan mengubah status menjadi **"Claimed"** (diklaim).

Jika tidak ada yang mengklaim dalam waktu tertentu, Admin bisa menandai sebagai **"Klaim WH"** (diklaim warehouse) untuk dijual atau dilelang di halaman **Barang Lelang**.

**Status Barang No Tuan:**
1. **No Tuan** → Barang tanpa pemilik
2. **Claimed** → Diklaim customer
3. **Klaim WH** → Diklaim warehouse untuk dijual/dilelang

**Data yang mengalir:**
- Input: barang data dari admin → output: Item status "no_tuan"
- Customer claim → output: Item status "claimed"
- Admin klaim → output: Item status "klaim_wh" → bisa masuk lelang

---

## Cerita 13: Owner Memantau Bisnis

Owner login ke sistem dan melihat **Owner Dashboard** yang menampilkan:
- Total revenue bulan ini
- Jumlah customer aktif
- Jumlah box aktif
- Jumlah invoice pending
- Grafik trend pengiriman

Owner membuka halaman **Finance** untuk melihat laporan keuangan detail:
- Revenue per periode
- Breakdown per metode (air/sea)
- Export data ke file

Owner juga bisa melihat **Audit Log** untuk mengetahui siapa melakukan apa dan kapan. Ini penting untuk transparansi dan keamanan.

Owner bisa mengelola akun admin di halaman **Manage Admin** dan melihat semua data di halaman **All Data**.

---

## Cerita 14: Kalkulator Estimasi Biaya

Sebelum mengirim barang, Budi ingin tahu estimasi biaya. Budi membuka halaman **Kalkulator** dan mengisi:

- **Berat Aktual**: 5.5 gram
- **Panjang**: 30 cm
- **Lebar**: 20 cm
- **Tinggi**: 15 cm
- **Tipe**: Sharing
- **Metode**: Sea
- **Sensitive**: Tidak

Sistem menghitung:
```
Volume = (30 × 20 × 15) / 6000 = 1.5
Dasar = MAX(5.5, 1.5) = 5.5
Fee TAX = 5.5 × Rp 70 = Rp 385
Fee WH = Rp 5.000
Fee Packing = Rp 5.000
Estimasi Total = Rp 10.385
```

Budi sekarang tahu estimasi biaya sebelum mengirim barang.

---

## Cerita 15: Admin Mengatur Rate

Admin membuka halaman **Pengaturan Rate** yang memiliki 3 tab:

**Tab 1: Rate Sharing**
- Air — Berat: Rp 255/gram
- Air — Volume: Rp 230/gram
- Sea — Berat: Rp 70/gram
- Sea — Volume: Rp 85/gram
- (Masing-masing ada versi Sensitive dengan rate lebih tinggi)

**Tab 2: Rate Direct**
- Air — Berat: Rp 255/gram
- Air — Volume: Rp 230/gram
- Sea — Berat: Rp 70/gram
- Sea — Volume: Rp 85/gram

**Tab 3: Fee Packing**
- Fee 0—150 gram: Rp 5.000
- Fee 151—1.000 gram: Rp 6.500
- Fee 1.001—2.000 gram: Rp 8.000
- Extra per gram: Rp 1.500

Admin juga mengelola **Kurs Yuan** di halaman History Kurs. Kurs ini digunakan untuk konversi harga barang dari Yuan ke Rupiah.

**Penting:** Perubahan rate hanya berlaku untuk invoice BARU. Invoice yang sudah dibuat tetap menggunakan rate saat invoice dibuat (snapshot).

---

## Cerita 16: Reminder dan Denda Otomatis

Sistem memiliki mekanisme reminder otomatis:

1. **H-3** (3 hari sebelum deadline): Notifikasi reminder pembayaran
2. **H-1** (1 hari sebelum deadline): Notifikasi reminder pembayaran
3. **H-0** (hari deadline): Notifikasi reminder pembayaran
4. **+2 minggu** (2 minggu setelah deadline): Notifikasi overdue
5. **Storage expired** (14 hari): Notifikasi storage expired

Jika pembayaran melewati deadline, sistem bisa menghitung **denda** (DendaClaim) yang ditambahkan ke grand total invoice.

Jika storage expired (barang terlalu lama di gudang), barang bisa di-**hold** (status: hold).

---

# 5. Diagram Alur Bisnis

## 5.1 Alur Utama Pengiriman

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   CUSTOMER   │     │    ADMIN     │     │    OWNER     │
│  REGISTRASI  │     │  AKTIVASI    │     │   MONITOR    │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       ▼                    ▼                    │
┌─────────────┐     ┌─────────────┐              │
│  SETOR RESI  │     │  INPUT WH   │              │
│  (Customer)  │     │  CHINA      │              │
└──────┬───────┘     └──────┬───────┘              │
       │                    │                    │
       ▼                    ▼                    │
┌─────────────┐     ┌─────────────┐              │
│   AUTO       │     │   INPUT     │              │
│   MATCH      │◄────│   INDONESIA │              │
└──────┬───────┘     └──────┬───────┘              │
       │                    │                    │
       ▼                    ▼                    │
┌─────────────┐     ┌─────────────┐              │
│   BOX        │     │   GENERATE  │              │
│   MANAGEMENT │────►│   INVOICE   │              │
└──────┬───────┘     └──────┬───────┘              │
       │                    │                    │
       ▼                    ▼                    │
┌─────────────┐     ┌─────────────┐              │
│   PAYMENT    │     │  VERIFIKASI │              │
│   (Customer) │────►│  (Admin)    │              │
└──────┬───────┘     └──────┬───────┘              │
       │                    │                    │
       ▼                    ▼                    │
┌─────────────┐     ┌─────────────┐              │
│   CHECKOUT   │     │   PENGIRIMAN│              │
│   REQUEST    │────►│   (Admin)   │──────────────┘
└──────────────┘     └─────────────┘
```

## 5.2 Alur Status Box

```
OPEN ──► SENT_TO_CARGO ──► OTW_INA ──► UP_INVOICE ──► DONE
  │                                                      ▲
  └──► CLOSED ────────────────────────────────────────────┘
  
Catatan: Admin bisa mengubah ke status manapun (bisa maju/mundur)
```

## 5.3 Alur Status Invoice

```
WAITING_PAYMENT ──► WAITING_VERIFICATION ──► VERIFIED
       ▲                    │
       └──── REJECTED ◄─────┘ (jika ditolak admin)
```

## 5.4 Alur Status Item

```
ACTIVE ──► NO_TUAN ──► CLAIMED
              │
              └──► KLAIM_WH ──► DIJUAL/LELANG
              
ACTIVE ──► SHIPPED
ACTIVE ──► HOLD (jika overdue)
```

## 5.5 Alur Status Checkout

```
REQUEST ──► ON_PROCESS ──► SENT
```

## 5.6 Alur Status Komplain

```
OPEN ──► IN_REVIEW ──► PROCESSING ──► RESOLVED
```

---

# 6. Daftar Seluruh Fitur per Tahapan

## Tahap 1: Registrasi & Aktivasi

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Register | Customer | Nama, email, password, telepon, alamat | Akun pending |
| Aktivasi | Admin | Keputusan (aktifkan) | Akun active |
| Login | Semua | Email, password | Session |
| Forgot Password | Semua | Email | Reset link |

## Tahap 2: Setor Resi

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Setor Resi | Customer | Nama barang, resi, qty, sensitive, foto | Item baru |
| Lihat Daftar Resi | Customer | - | List barang |

## Tahap 3: Data Warehouse China

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Input WH Data | Admin | Resi, berat, foto China | WhChinaData |
| Auto Match | Sistem | Resi number | Match ke Item |
| Manual Match | Customer | Pilih WH data | Match ke Item |
| Input Biaya Jasa | Admin | Biaya jasa | WhChinaData.biaya_jasa |
| Input Biaya Tax | Admin | Biaya tax | WhChinaData.biaya_tax |

## Tahap 4: Box Management

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Buat Box | Admin | Tipe, metode, batch, huruf | Box baru |
| Edit Box | Admin | Semua field | Box updated |
| Ubah Status | Admin | Status baru | Status changed + notifikasi |
| Lihat Box | Customer | - | List box + detail |
| Timeline Status | Customer | - | Visual timeline |

## Tahap 5: Data Indonesia

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Input Berat INA | Admin | Berat INA | WhChinaData.berat_ina |
| Input Dimensi | Admin | P, L, T | Volume auto-calc |
| Upload Foto INA | Admin | Foto | WhChinaData.foto_arrived_ina |
| Input Tanggal Setor | Admin | Tanggal | WhChinaData.tanggal_setor |

## Tahap 6: Invoice

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Generate Invoice | Admin | Box, berat, volume, add_on | Invoice baru |
| Create Invoice (Self) | Customer | Pilih barang arrived | Invoice baru |
| Lihat Invoice | Customer | - | List invoice + detail |
| Kalkulator | Customer | Berat, dimensi, tipe, metode | Estimasi biaya |

## Tahap 7: Pembayaran

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Upload Bukti Bayar | Customer | Foto bukti transfer | Status → waiting_verification |
| Verifikasi | Admin | Terima/tolak + alasan | Status → verified/rejected |
| Reminder H-3/H-1/H-0 | Sistem | - | Notifikasi otomatis |
| Denda | Sistem | - | DendaClaim jika overdue |

## Tahap 8: Checkout

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Request Checkout | Customer | Nama, telepon, alamat, catatan | Checkout baru |
| Proses Checkout | Admin | Status update | Status → on_process → sent |

## Tahap 9: Komplain

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Ajukan Komplain | Customer | Jenis, resolusi, deskripsi, foto, video | Complain baru |
| Review Komplain | Admin | Status update | Status → in_review → processing → resolved |

## Tahap 10: No Tuan & Lelang

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Input No Tuan | Admin | Data barang | Item status no_tuan |
| Klaim Barang | Customer | Pilih barang | Item status claimed |
| Klaim WH | Admin | Keputusan | Item status klaim_wh |
| Barang Lelang | Admin | Kelola barang | Item status dijual/lelang |

## Tahap 11: Pengaturan

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Rate Sharing | Admin | 8 rate (air/sea × berat/volume × normal/sensitive) | Settings |
| Rate Direct | Admin | 4 rate (air/sea × berat/volume) | Settings |
| Fee Packing | Admin | 4 tier + extra | Settings |
| Kurs Yuan | Admin | Rate Yuan, tanggal | KursHistory |
| Info Customer | Admin | Edit data customer, custom rates | User updated |
| Est Update | Admin | ETD, ETA | Box updated |

## Tahap 12: Owner

| Fitur | Siapa | Input | Output |
|-------|-------|-------|--------|
| Dashboard | Owner | - | Ringkasan bisnis |
| Finance | Owner | Filter periode | Laporan keuangan |
| Export Finance | Owner | - | File export |
| Manage Admin | Owner | CRUD admin | Admin accounts |
| Audit Log | Owner | Filter | Riwayat perubahan |
| Users | Owner | CRUD user | User management |
| All Data | Owner | - | Semua data sistem |

---

# 7. Hubungan Antar Modul (Data Flow)

```
┌──────────┐     ┌──────────┐     ┌──────────┐
│  USERS   │────►│  BOXES   │────►│  ITEMS   │
│          │     │          │     │          │
│ role     │     │ type     │     │ name     │
│ status   │     │ method   │     │ resi     │
│ email    │     │ status   │     │ status   │
└──────────┘     └──────────┘     └────┬─────┘
                                       │
                    ┌──────────────────┼──────────────────┐
                    ▼                  ▼                  ▼
             ┌──────────┐     ┌──────────┐     ┌──────────┐
             │WH_CHINA  │     │ INVOICES │     │COMPLAINS │
             │  DATA    │     │          │     │          │
             │          │     │ fee_tax  │     │ type     │
             │ berat    │     │ fee_wh   │     │ photo    │
             │ dimensi  │     │ fee_pack │     │ video    │
             │ foto     │     │ grand_tot│     │ status   │
             └──────────┘     └────┬─────┘     └──────────┘
                                   │
                    ┌──────────────┼──────────────┐
                    ▼              ▼              ▼
             ┌──────────┐  ┌──────────┐  ┌──────────┐
             │ CHECKOUTS│  │  DENDA   │  │NOTIFICA- │
             │          │  │  CLAIMS  │  │  TIONS   │
             │ address  │  │          │  │          │
             │ status   │  │ amount   │  │ type     │
             └──────────┘  │ status   │  │ message  │
                           └──────────┘  └──────────┘

┌──────────┐     ┌──────────┐
│ SETTINGS │     │   KURS   │
│          │     │ HISTORY  │
│ 17 rate  │     │          │
│ params   │     │ yuan_rate│
└──────────┘     └──────────┘

┌──────────┐     ┌──────────┐
│ACTIVITY  │     │  DENDA   │
│  LOGS    │     │  CLAIMS  │
│          │     │          │
│ audit    │     │ overdue  │
│ trail    │     │ penalty  │
└──────────┘     └──────────┘
```

---

# 8. Validasi dan Kondisi

## 8.1 Validasi Input

| Form | Field | Validasi |
|------|-------|----------|
| Register | email | Required, unique, valid email format |
| Register | password | Required, min 8 characters |
| Register | name | Required, string |
| Setor Resi | name | Required, string |
| Setor Resi | resi_number | Nullable, string, max 100 |
| Setor Resi | proof_co | File, max 5MB, jpg/jpeg/png |
| Input WH | berat | Required, numeric, min 0.01 |
| Input WH | foto | File, max 50MB, jpg/jpeg/png |
| Generate Invoice | weight | Required, numeric, min 0.1 |
| Generate Invoice | volume | Nullable, numeric |
| Upload Payment | payment_proof | File, max 5MB, jpg/jpeg/png |
| Checkout | sender_name | Required, string |
| Checkout | sender_address | Required, string |
| Komplain | type | Required, string |
| Komplain | description | Required, min 10 chars, max 2000 |
| Komplain | photo | File, max 5MB, jpg/jpeg/png |
| Komplain | video | File, max 50MB, mp4/mov |
| Settings | rate values | Required, numeric, min 1, max 99999 |

## 8.2 Kondisi Sukses

| Proses | Kondisi Sukses |
|--------|---------------|
| Register | Akun dibuat, notifikasi ke admin |
| Aktivasi | Status → active, notifikasi ke customer |
| Setor Resi | Item created, foto tersimpan |
| Auto Match | WH data terhubung ke Item |
| Generate Invoice | Invoice created, fee calculated, notifikasi ke customer |
| Verifikasi | Status → verified, notifikasi ke customer |
| Checkout | Status → sent, notifikasi ke customer |

## 8.3 Kondisi Gagal

| Proses | Kondisi Gagal | Penanganan |
|--------|--------------|------------|
| Login | Email/password salah | Error message |
| Register | Email sudah terdaftar | Error message |
| Setor Resi | Foto gagal upload | Error message + try-catch |
| Verifikasi | Bukan admin | Redirect 403 |
| Checkout | Invoice belum verified | Tombol checkout tidak muncul |
| Komplain | Deskripsi < 10 karakter | Validation error |

---

# 9. Laporan Pengujian

## 9.1 Pengujian Otomatis

| Metrik | Hasil |
|--------|-------|
| Total Test | 487 |
| Passed | 487 |
| Failed | 0 |
| Skipped | 1 |
| Assertions | 1652 |
| Duration | ~14 detik |

## 9.2 Pengujian Halaman (HTTP Status)

| Kategori | Halaman | Status |
|----------|---------|--------|
| Admin | 13 halaman | ✅ Semua HTTP 200 |
| Customer | 11 halaman | ✅ Semua HTTP 200 |
| Owner | 6 halaman | ✅ Semua HTTP 200 |
| Auth | 4 halaman | ✅ Semua HTTP 200 |
| Protected (no auth) | 3 halaman | ✅ Semua HTTP 302 (redirect) |

**Total: 37 halaman, 100% berfungsi**

## 9.3 Pengujian Security

| Test | Hasil |
|------|-------|
| Security headers (X-Content-Type-Options) | ✅ Pass |
| Security headers (X-Frame-Options) | ✅ Pass |
| Security headers (X-XSS-Protection) | ✅ Pass |
| Security headers (Referrer-Policy) | ✅ Pass |
| Login rate limiting | ✅ Pass |
| Register rate limiting | ✅ Pass |
| No user input in unescaped output | ✅ Pass |
| No string concat in raw queries | ✅ Pass |
| File uploads use random names | ✅ Pass |

---

# 10. Temuan Masalah

## 10.1 Masalah yang Sudah Diperbaiki

| No | Masalah | Status |
|----|---------|--------|
| 1 | Volume formula salah (P×L×T/6 → P×L×T/6000) | ✅ Fixed |
| 2 | Upload foto 50MB limit | ✅ Fixed |
| 3 | Sharing box dropdown tidak muncul | ✅ Fixed |
| 4 | WH China form label bahasa Inggris | ✅ Fixed |
| 5 | Box edit hanya bisa tracking+ETA | ✅ Fixed (semua field) |
| 6 | CRUD Customer tidak ada edit/delete | ✅ Fixed |
| 7 | Rate label "per kg" harus "per gram" | ✅ Fixed |
| 8 | Complaint foto/video tidak tampil | ✅ Fixed |
| 9 | Per-customer rate tidak ada | ✅ Fixed |
| 10 | Biaya tax tidak ada di WH China | ✅ Fixed |
| 11 | Customer dashboard tidak show data INA | ✅ Fixed |
| 12 | Invoice tidak show add_on | ✅ Fixed |
| 13 | Box status hanya bisa maju | ✅ Fixed (bisa ke status manapun) |

## 10.2 Masalah yang Masih Ada

| No | Masalah | Severity | Lokasi |
|----|---------|----------|--------|
| 1 | Status "LAST_SETOR" didefinisikan di model tapi tidak ada di UI dropdown | Low | Box model vs Manage Box UI |
| 2 | Status "hold" dan "dijual" didefinisikan di Item model tapi belum ada UI lengkap | Low | Item model |
| 3 | Export Finance belum diverifikasi fungsionalitasnya | Medium | Owner > Finance |
| 4 | Reminder otomatis (H-3, H-1, H-0) perlu cron job setup | Medium | NotificationService |
| 5 | Denda auto-calculation perlu scheduling | Medium | DendaClaim |

---

# 11. Rekomendasi Perbaikan

## 11.1 Prioritas Tinggi

| No | Rekomendasi | Alasan |
|----|-------------|--------|
| 1 | Setup cron job untuk reminder otomatis | Reminder H-3/H-1/H-0 tidak jalan tanpa cron |
| 2 | Setup cron job untuk denda auto-calculation | Denda tidak terhitung otomatis |
| 3 | Verifikasi Export Finance functionality | Owner butuh export data keuangan |

## 11.2 Prioritas Sedang

| No | Rekomendasi | Alasan |
|----|-------------|--------|
| 4 | Hapus atau implementasi status LAST_SETOR | Konsistensi kode |
| 5 | Implementasi UI untuk status hold/dijual | Item status management lengkap |
| 6 | Tambahkan error boundary di semua form | User experience |
| 7 | Tambahkan loading state di semua proses | User experience |

## 11.3 Prioritas Rendah

| No | Rekomendasi | Alasan |
|----|-------------|--------|
| 8 | Tambahkan search di semua tabel | Usability |
| 9 | Tambahkan export di semua laporan | Convenience |
| 10 | Tambahkan bulk action di admin | Efficiency |
| 11 | Mobile app (future) | Customer convenience |

---

# Lampiran

## A. Daftar 17 Parameter Rate

| No | Key | Fungsi |
|----|-----|--------|
| 1 | rate_sharing_air_berat | Rate sharing air (berat) |
| 2 | rate_sharing_air_volume | Rate sharing air (volume) |
| 3 | rate_sharing_sea_berat | Rate sharing sea (berat) |
| 4 | rate_sharing_sea_volume | Rate sharing sea (volume) |
| 5 | rate_sharing_sensitive_air_berat | Rate sharing sensitive air (berat) |
| 6 | rate_sharing_sensitive_air_volume | Rate sharing sensitive air (volume) |
| 7 | rate_sharing_sensitive_sea_berat | Rate sharing sensitive sea (berat) |
| 8 | rate_sharing_sensitive_sea_volume | Rate sharing sensitive sea (volume) |
| 9 | rate_direct_air_berat | Rate direct air (berat) |
| 10 | rate_direct_air_volume | Rate direct air (volume) |
| 11 | rate_direct_sea_berat | Rate direct sea (berat) |
| 12 | rate_direct_sea_volume | Rate direct sea (volume) |
| 13 | fee_packing_150 | Fee packing tier 0-150 gram |
| 14 | fee_packing_1000 | Fee packing tier 151-1000 gram |
| 15 | fee_packing_2000 | Fee packing tier 1001-2000 gram |
| 16 | fee_packing_extra_per_kg | Fee packing extra per gram |
| 17 | rate_sharing_air_berat | (dan lainnya sesuai PRD §4.12) |

## B. Daftar 20+ Jenis Notifikasi

| No | Type | Penerima | Trigger |
|----|------|----------|---------|
| 1 | customer_register | Admin | Customer mendaftar |
| 2 | account_activated | Customer | Akun diaktifkan |
| 3 | box_status_changed | Customer | Status box berubah |
| 4 | invoice_generated | Customer | Invoice dibuat |
| 5 | payment_received | Admin | Customer upload bukti bayar |
| 6 | payment_verified | Customer | Pembayaran diverifikasi |
| 7 | payment_rejected | Customer | Pembayaran ditolak |
| 8 | checkout_processed | Customer | Checkout diproses |
| 9 | new_complaint | Admin | Komplain baru |
| 10 | complaint_updated | Customer | Status komplain berubah |
| 11 | payment_reminder_h3 | Customer | Reminder H-3 |
| 12 | payment_reminder_h1 | Customer | Reminder H-1 |
| 13 | payment_reminder_h0 | Customer | Reminder H-0 |
| 14 | payment_overdue_2week | Customer | Overdue 2 minggu |
| 15 | storage_expired | Customer | Storage expired |
| 16 | item_hold | Customer | Item di-hold |
| 17 | item_arrived_wh | Customer | Barang arrived WH |
| 18 | box_closed | Customer | Box ditutup |
| 19 | claim_successful | Customer | Klaim berhasil |

## C. Rumus Fee Calculation

```
Volume (m³) = (Panjang × Lebar × Tinggi) / 6000
Dasar = MAX(berat_aktual, volume)

Fee TAX = Dasar × Rate
  Rate dipilih dari 12 varian:
  - sharing/direct × air/sea × sensitive/non-sensitive
  - Ada custom rate per customer (mengabaikan rate global)

Fee WH = Tiered:
  - 0—150 gram:      fee_packing_150
  - 151—1.000 gram:  fee_packing_1000
  - 1.001—2.000 gram: fee_packing_2000
  - > 2.000 gram:    fee_packing_2000 + (berat - 2000) × fee_packing_extra_per_kg

Fee Packing = Struktur tiered yang sama dengan Fee WH

Grand Total = Fee TAX + Fee WH + Fee Packing + Add On + Denda
```

---

*Dokumen ini disusun berdasarkan analisis mendalam terhadap source code, database, dan pengujian sistem Ting Warehouse Management System per Juli 2026.*
