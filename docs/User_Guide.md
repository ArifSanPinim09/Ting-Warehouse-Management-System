# USER GUIDE
# Ting Warehouse Management System
# Versi 2.1 — Juli 2026

---

# Daftar Isi

1. Tentang Sistem
2. FASE 1 — Setup Awal Sistem (Admin)
3. FASE 2 — Aktivasi Customer & Pembuatan Box
4. FASE 3 — Barang Masuk (Setor Resi & WH China)
5. FASE 4 — Barang No Tuan & Lelang
6. FASE 5 — Invoice & Pembayaran
7. FASE 6 — Checkout & Pengiriman
8. FASE 7 — Komplain
9. FASE 8 — Owner Panel
10. FASE 9 — Fitur Umum (Kalkulator, Notifikasi, Profile)
11. Status — Referensi Cepat
12. Pesan Error & Sukses

---

# 1. Tentang Sistem

Ting Warehouse Management System adalah website operasional untuk perusahaan freight forwarding China → Jakarta. Sistem ini menggantikan Airtable dan Google Sheets yang sebelumnya digunakan secara manual.

| Informasi | Detail |
|-----------|--------|
| Website | www.tingwarehouse.my.id |
| Bahasa | Indonesia |
| Perangkat | Laptop, HP Android, iPhone |
| Browser | Chrome, Safari, Firefox (versi terbaru) |

### Tiga Role Pengguna

| Role | Siapa | Jumlah | Akses Utama |
|------|-------|--------|-------------|
| Customer | Pengguna jasa forwarding (pedagang, reseller, dropshipper) | 50-200 | Setor resi, bayar invoice, checkout, komplain |
| Admin | Staff operasional di kantor/gudang | 3 orang | Kelola box, invoice, verifikasi, rate, customer |
| Owner | Pemilik bisnis | 1 orang | Semua akses admin + laporan keuangan + kelola user |

### Sidebar Admin (7 Grup, 15 Menu)

| Grup | Menu | URL |
|------|------|-----|
| Overview | Dashboard | /admin/dashboard |
| Barang & Box | Manage Box | /admin/manage-boxes |
| Barang & Box | Recap | /admin/recap |
| Barang & Box | Input No Tuan | /admin/no-tuan/create |
| Barang & Box | Barang Lelang | /admin/lelang |
| Keuangan | Generate Invoice | /admin/invoices |
| Keuangan | Verifikasi | /admin/verification |
| Keuangan | Checkout | /admin/checkouts |
| Customer | Info Customer | /admin/customers |
| Customer | Komplain | /admin/complains |
| Pengaturan | Est Update | /admin/est-update |
| Pengaturan | Pengaturan Rate | /admin/settings |
| Pengaturan | History Kurs | /admin/kurs-history |

### Sidebar Customer (8 Menu)

| Menu | URL |
|------|-----|
| Dashboard | /dashboard |
| My Box | /box/sharing |
| Invoice | /invoice |
| Checkout | /checkout |
| Komplain | /komplain |
| Kalkulator | /kalkulator |
| No Tuan | /no-tuan |
| Resi Belum Dikenali | /unmatched-resi |

### Sidebar Owner (2 Grup Tambahan, 6 Menu)

| Grup | Menu | URL |
|------|------|-----|
| Owner | Owner Dashboard | /owner/dashboard |
| Owner | Laporan Keuangan | /owner/finance |
| Manajemen | Manage Admin | /owner/manage-admin |
| Manajemen | Manage Users | /owner/users |
| Manajemen | All Data | /owner/data |
| Manajemen | Audit Log | /owner/audit-log |

---

# 2. FASE 1 — Setup Awal Sistem (Admin)

Sebelum sistem bisa digunakan, admin harus melakukan setup awal terlebih dahulu: login, mengatur kurs Yuan, dan mengatur rate pengiriman.

---

## LANGKAH 1 — Customer Mendaftar

**Siapa:** Customer
**Halaman:** /register

Customer membuka website dan mendaftar sebagai pengguna baru.

### Form Register

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nama | Text | "Nama lengkap" | Wajib, min 3 karakter |
| Email | Email | "email@contoh.com" | Wajib, format email, tidak boleh sama |
| No Telepon | Text | "08123456789" | Wajib, angka saja, min 10 digit |
| No KTP | Text | "16 digit No KTP" | Wajib, tepat 16 digit, tidak boleh sama |
| Alamat | Textarea | "Alamat lengkap" | Wajib, min 10 karakter |
| Password | Password | "Min 8 karakter" | Wajib, min 8 karakter |
| Konfirmasi Password | Password | "Ulangi password" | Wajib, harus sama dengan password |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Daftar | Kirim data registrasi |
| Sudah punya akun? Masuk | Ke halaman login |

### Setelah Submit

- Akun berstatus **PENDING** (belum bisa login)
- Pesan: "Registrasi berhasil! Menunggu aktivasi dari admin."
- Customer menunggu admin mengaktifkan akunnya

---

## LANGKAH 2 — Admin Login Pertama Kali

**Siapa:** Admin
**Halaman:** /login

Admin login ke sistem untuk pertama kali.

### Form Login

| Field | Tipe | Aturan |
|-------|------|--------|
| Email | Email | Wajib |
| Password | Password | Wajib |
| Remember Me | Checkbox | Opsional, tetap login meski browser ditutup |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Masuk | Login ke sistem |

### Setelah Login

- Admin diarahkan ke Dashboard Admin (/admin/dashboard)

### Error Login

| Kondisi | Pesan |
|---------|-------|
| Email/password salah | "Email atau password salah" |
| Akun PENDING | "Akun belum aktif. Hubungi admin." |
| Akun INACTIVE | "Akun telah dinonaktifkan." |
| Gagal 5x | "Akun terkunci. Coba lagi dalam 15 menit." |

---

## LANGKAH 3 — Admin Melihat Dashboard

**Siapa:** Admin
**Halaman:** /admin/dashboard

Dashboard admin menampilkan ringkasan operasional.

### Stat Cards

| Kartu | Informasi |
|-------|-----------|
| Box Sharing | Jumlah box sharing OPEN / CLOSED |
| Box Direct | Jumlah box direct OPEN / CLOSED |
| Customer Aktif | Jumlah customer aktif |
| Invoice Pending | Invoice menunggu verifikasi |

### Notifikasi

| Komponen | Keterangan |
|----------|------------|
| Registrasi Baru | Customer baru mendaftar |
| Payment Request | Customer upload bukti transfer |
| Complain Request | Customer ajukan komplain |

### Shortcut

| Menu | Klik ke |
|------|---------|
| Est Update | /admin/est-update |
| Recap | /admin/recap |
| Verification | /admin/verification |
| Customer | /admin/customers |
| Komplain | /admin/complains |

### Bell Icon (Notifikasi)

Di pojok kanan atas ada ikon lonceng yang menunjukkan jumlah notifikasi belum dibaca.

---

## LANGKAH 4 — Admin Mengatur Kurs Yuan (WAJIB DULU)

**Siapa:** Admin
**Halaman:** /admin/kurs-history

Sebelum membuat box atau menghitung biaya, admin HARUS mengatur kurs Yuan → Rupiah terlebih dahulu. Kurs ini digunakan oleh kalkulator customer dan perhitungan invoice.

### Sidebar Admin → "Pengaturan" → "History Kurs"

### Halaman History Kurs

| Komponen | Keterangan |
|----------|------------|
| Tombol "Input Kurs Baru" | Buka form input kurs |
| Tabel History | Daftar semua kurs yang pernah diinput |

### Tabel History Kurs

| Kolom | Keterangan |
|-------|------------|
| Kurs | Nilai kurs (contoh: 2660) |
| Tanggal Berlaku | Tanggal kurs ini berlaku |
| Diinput Oleh | Nama admin/owner yang input |
| Tanggal Input | Kapan data diinput |

### Form Input Kurs Baru

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nilai Kurs | Number | Contoh: 2660 | Wajib, harus angka |
| Tanggal Berlaku | Date | - | Wajib, tidak boleh tanggal masa depan |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup form |
| Simpan | Simpan kurs baru |

### Setelah Submit

- Data masuk ke tabel history kurs
- Kurs terbaru otomatis jadi default untuk transaksi baru
- Customer bisa lihat di Dashboard: "Kurs Hari Ini: Rp 2.660"
- Pesan: "Kurs berhasil diupdate."

### Aturan Penting

- Tidak bisa input kurs dengan tanggal yang sudah ada (harus unik)
- Invoice yang sudah dibuat tetap pakai kurs saat invoice dibuat (snapshot)

---

## LANGKAH 5 — Admin Mengatur Rate Pengiriman

**Siapa:** Admin
**Halaman:** /admin/settings

Admin mengatur 17 parameter rate pengiriman. Rate ini digunakan untuk kalkulator dan generate invoice.

### Sidebar Admin → "Pengaturan" → "Pengaturan Rate"

### Halaman Settings — 3 Tab

#### Tab "Sharing" — Rate Barang Sharing

| Field | Keterangan | Default |
|-------|------------|---------|
| Rate Sharing Air Berat | Rp per kg (berat aktual) | 255 |
| Rate Sharing Air Volume | Rp per kg (volume) | 230 |
| Rate Sharing Sea Berat | Rp per kg (berat aktual) | 70 |
| Rate Sharing Sea Volume | Rp per kg (volume) | 83 |
| Rate Sharing Sensitive Air Berat | Rp per kg | 315 |
| Rate Sharing Sensitive Air Volume | Rp per kg | 315 |
| Rate Sharing Sensitive Sea Berat | Rp per kg | 95 |
| Rate Sharing Sensitive Sea Volume | Rp per kg | 95 |

#### Tab "Direct" — Rate Barang Direct

| Field | Keterangan | Default |
|-------|------------|---------|
| Rate Direct Air Berat | Rp per kg | 230 |
| Rate Direct Air Volume | Rp per kg | 160 |
| Rate Direct Sea Berat | Rp per kg | 70 |
| Rate Direct Sea Volume | Rp per kg | 90 |

#### Tab "Packing" — Fee Packing (Tiered)

| Field | Keterangan | Default |
|-------|------------|---------|
| Fee Packing ≤150 gram | Rp per barang | 5.000 |
| Fee Packing ≤1000 gram | Rp per barang | 6.500 |
| Fee Packing ≤2000 gram | Rp per barang | 8.000 |
| Fee Packing Extra per kg | Rp per kg kelebihan | 1.500 |

### Tombol per Tab

| Tombol | Fungsi |
|--------|--------|
| Simpan | Update rate + konfirmasi |

### Setelah Simpan

- Rate tersimpan di database
- Langsung berlaku untuk kalkulator dan invoice baru
- Invoice lama TIDAK berubah (tetap pakai rate saat dibuat)
- Pesan: "Rate berhasil diupdate."
- Info "Terakhir diupdate: [tanggal]" di halaman settings

---

## LANGKAH 6 — Admin Mengaktivasi Akun Customer

**Siapa:** Admin
**Halaman:** /admin/customers (Info Customer)

Admin mengaktivasi customer yang baru mendaftar agar bisa login.

### Sidebar Admin → "Customer" → "Info Customer"

### Halaman Info Customer

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email/customer |
| Filter Status | Pending / Active / Inactive |
| Tabel Customer | Daftar semua customer |

### Tabel Customer

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Email | Alamat email |
| No Telepon | Nomor HP |
| Status | Badge: PENDING (kuning) / ACTIVE (hijau) / INACTIVE (merah) |
| Terdaftar | Tanggal registrasi |

### Proses Aktivasi

1. Klik nama customer yang berstatus PENDING
2. Detail muncul: nama, email, telepon, KTP (16 digit), alamat lengkap
3. Klik tombol **"Aktivasi"**
4. Konfirmasi: "Aktivasi customer ini?"
5. Status: PENDING → ACTIVE
6. Pesan: "Akun [nama] berhasil diaktivasi."
7. Customer mendapat notifikasi: "Akun Anda sudah aktif."

### Fitur Lain di Info Customer

| Tombol | Fungsi | Kapan Muncul |
|--------|--------|-------------|
| Nonaktifkan | Customer tidak bisa login | Status ACTIVE |
| Aktivasi (kembali) | Aktifkan ulang | Status INACTIVE |

---

# 3. FASE 2 — Aktivasi Customer & Pembuatan Box

---

## LANGKAH 7 — Customer Login Pertama Kali

**Siapa:** Customer
**Halaman:** /login

Customer yang sudah diaktivasi bisa login.

### Setelah Login

- Customer diarahkan ke Dashboard (/dashboard)

---

## LANGKAH 8 — Customer Melihat Dashboard

**Siapa:** Customer
**Halaman:** /dashboard

### Stat Cards (4 kartu)

| Kartu | Informasi | Klik ke |
|-------|-----------|---------|
| Box Aktif | Jumlah box OPEN/SENT/OTW | /box/sharing |
| Invoice Belum Bayar | Jumlah + total nominal | /invoice |
| Barang Bulan Ini | Jumlah barang bulan ini | /setor-resi |
| Resi Bulan Ini | Jumlah resi bulan ini | /setor-resi |

### Alert Banner (muncul jika ada data WH China yang belum dikenali)

| Komponen | Keterangan |
|----------|------------|
| Ikon | Lingkaran biru dengan "i" |
| Pesan | "Ada X resi dari gudang China yang belum dikenali" |
| Sub-pesan | "Klik di sini untuk melihat dan mengklaim resi yang mungkin milik Anda." |
| Klik ke | /unmatched-resi |
| Tidak muncul jika | Semua data WH China sudah matched |

### Status Box (tabel daftar box)

| Kolom (Desktop) | Keterangan |
|-----------------|------------|
| Nomor Box | Tracking number atau "Box #ID" |
| Kode Box | Batch name |
| Jenis Kirim | AIR (biru) atau SEA (cyan) |
| ETD | Estimasi tanggal keberangkatan |
| ETA | Estimasi tanggal tiba |
| Status | Badge status box |
| Barang | Jumlah barang di box |

Di mobile, box tampil sebagai card list (bukan tabel).

Klik salah satu box → **Detail Box** modal:
- Info box (Status, Tipe, Jenis Kirim, Kode Box)
- Tabel barang: No, Nama Barang, Qty, Berat (dari data WH China), Volume, Keterangan (badge status item jika bukan active)

### Menu Cepat (7 shortcut)

Di desktop tampil sebagai grid 2 kolom. Di mobile tampil sebagai scroll horizontal.

| Menu | Klik ke | Warna Ikon |
|------|---------|------------|
| Setor Resi | /setor-resi | Biru |
| My Box | /box/sharing | Hijau |
| Invoice | /invoice | Kuning |
| Checkout | /checkout | Ungu |
| Komplain | /komplain | Merah |
| Kalkulator | /kalkulator | Abu-abu |
| No Tuan | /no-tuan | Oranye |

### Rate Hari Ini

| Informasi | Keterangan |
|-----------|------------|
| Kurs Yuan | Rp X (dari history kurs terbaru) |
| Rate Air | Rp X/kg (rate sharing air berat) |
| Rate Sea | Rp X/kg (rate sharing sea berat) |
| Tombol "Buka Kalkulator" | Ke /kalkuler |

### Notifikasi (5 terbaru)

| Komponen | Keterangan |
|----------|------------|
| Judul | Jenis notifikasi (contoh: "Akun diaktivasi") |
| Pesan | Detail notifikasi |
| Waktu | "X menit yang lalu" |
| Dot biru | Notifikasi belum dibaca |

### FAB (Floating Action Button) — Mobile Saja

Tombol hijau bulat (+) di pojok kanan bawah → langsung ke Setor Resi.

---

## LANGKAH 9 — Admin Membuat Box Baru

**Siapa:** Admin
**Halaman:** /admin/manage-boxes (Manage Box)

Admin membuat box untuk menampung barang customer.

### Sidebar Admin → "Barang & Box" → "Manage Box"

### Halaman Manage Box

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box berdasarkan tracking/batch |
| Filter Tipe | Sharing / Direct / Handcarry |
| Filter Status | OPEN / SENT_TO_CARGO / OTW_INA / UP_INVOICE / DONE / CLOSED |
| Filter Customer | Pilih customer tertentu |
| Filter Tanggal | Range tanggal |
| Tombol "Tambah Box" | Buka modal form buat box baru |
| Tabel Box | Daftar semua box |

### Tabel Box

| Kolom | Keterangan |
|-------|------------|
| Info Box | Tracking number / batch name |
| Tipe | Sharing / Direct / Handcarry |
| Metode | Air / Sea |
| Customer | Nama customer (jika direct) |
| Status | Badge status |
| Barang | Jumlah item |
| Tanggal | Tanggal dibuat |

### Form Tambah Box (Modal)

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Tipe Box | Select | - | Wajib: Sharing / Direct / Handcarry |
| Metode | Select | - | Wajib: Air / Sea |
| Customer | Select | "Tanpa Customer" | Opsional: Pilih customer (untuk direct) |
| Tracking Number | Text | "Opsional" | Opsional, max 100 karakter |
| Batch Name | Text | "Opsional" | Opsional, max 100 karakter |
| Catatan | Textarea | "Catatan opsional..." | Opsional, max 1000 karakter |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Simpan | Buat box baru |

### Setelah Submit

- Box dibuat dengan status **OPEN**
- open_date otomatis diisi waktu sekarang
- Pesan: "Box berhasil dibuat."

---

## LANGKAH 10 — Customer Melihat My Box

**Siapa:** Customer
**Halaman:** /box/sharing atau /box/direct

Customer melihat box yang sudah dibuat admin.

### My Box Sharing (/box/sharing)

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking Number, Tanggal, ETD, ETA, Status |
| Box List | Card per box dengan status badge |
| Box Detail | Expand/collapse: daftar barang di box |
| Barang Row | Nama, Qty, Harga, Foto Bukti, Status Arrived |

### My Box Direct (/box/direct)

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking, Batch, Status |
| Tombol "Request Direct Sharing" | Request box direct baru |
| Batch List | Card per batch + daftar barang |
| Tombol per batch | "Request to Close" (tutup batch) |

### Empty State (jika belum ada box)

"Ilustrasi box kosong" + "Belum ada barang di box sharing" + tombol "Setor Resi Sekarang"

---

# 4. FASE 3 — Barang Masuk (Setor Resi & WH China)

---

## LANGKAH 11 — Customer Setor Resi (Input Barang)

**Siapa:** Customer
**Halaman:** /setor-resi

Customer mendaftarkan barang yang akan dikirim dari China.

### Sidebar Customer → "Setor Resi"

### Info Box

Pesan biru: "Cara Klaim Resi" — penjelasan cara klaim resi dari WH China.

### Form Setor Resi

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Box Tujuan | Select | "Pilih box..." | Wajib, hanya box OPEN milik customer |
| Nama Barang | Text | "Contoh: Baju kaos, Sepatu, dll" | Wajib, min 2 karakter |
| Jumlah | Number | - | Wajib, min 1, max 9999 |
| Harga (Yuan) | Number | "0.00" | Wajib, min 0.01, max 999999, prefix ¥ |
| Nomor Resi | Text | "Nomor resi dari supplier China" | Wajib, min 3 karakter |
| Foto Bukti (Proof CO) | File | - | Wajib, JPG/PNG/WebP, max 5MB |
| Barang Sensitive | Checkbox | - | Opsional |
| Jenis Sensitive | Select | "Pilih jenis..." | Wajib jika sensitive: Elektronik, Baterai, Cairan, Kosmetik, Makanan, Obat-obatan, Magnet, Lainnya |

### Indikator Real-time WH China

Saat customer mengetik nomor resi, sistem otomatis mengecek ke database (debounce 500ms):

| Kondisi | Tampilan |
|---------|----------|
| Resi ditemukan di data WH China | Box hijau: "Resi ditemukan di data gudang China!" + info Berat, Ukuran, Tanggal, Foto barang dari WH China |
| Resi tidak ditemukan | Tidak ada indikator |
| Resi < 3 karakter | Tidak ada pengecekan |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Kembali ke dashboard |
| Daftarkan Barang | Submit form |

### Setelah Submit

- Barang tersimpan di box yang dipilih dengan status **active**
- Jika resi cocok dengan data WH China → otomatis **matched** (terhubung)
- last_setor_date box diupdate ke waktu sekarang
- Notifikasi ke admin
- Form direset untuk input barang berikutnya
- Pesan sukses hijau: "Barang berhasil didaftarkan."

### Error

| Kondisi | Pesan |
|---------|-------|
| Resi duplikat di box sama | "Nomor resi sudah terdaftar di box ini" |
| Box ditutup | "Box sudah ditutup. Tidak bisa menambah barang." |
| Format foto salah | "Format file harus jpg, png, atau webp" |
| Foto terlalu besar | "Ukuran file maksimal 5MB" |
| Jenis sensitive kosong | "Pilih jenis sensitive item" |

---

## LANGKAH 12 — Admin Input Data WH China (Recap)

**Siapa:** Admin
**Halaman:** /admin/recap

Admin memasukkan data barang dari gudang China. Data ini nantinya dicocokkan dengan resi customer.

### Sidebar Admin → "Barang & Box" → "Recap"

### Summary Stats (6 kartu di atas)

| Kartu | Informasi |
|-------|-----------|
| Box | Total box |
| Barang | Total item dari customer |
| WH China | Total data WH China |
| Matched | Data yang sudah cocok |
| Unmatched | Data yang belum cocok |
| Revenue | Total revenue |

### Filter & Tabs

| Komponen | Keterangan |
|----------|------------|
| Search | Cari resi, nama, customer |
| Filter Tipe | Sharing / Direct / Handcarry |
| Filter Metode | Air / Sea |
| Filter Tanggal | Dari - Sampai |
| Tab "Customer" | Data dari setor resi customer |
| Tab "WH China" | Data dari gudang China |

### Tab "Customer" — Data Setor Resi

| Kolom | Keterangan |
|-------|------------|
| No Resi | Nomor resi barang |
| Nama Barang | Nama barang |
| Qty | Jumlah |
| Harga (¥) | Harga dalam Yuan |
| Box | Box tempat barang |
| Customer | Nama customer |
| Status | Matched (hijau) / Unmatched (kuning) |

### Tab "WH China" — Data Gudang China

| Kolom | Keterangan |
|-------|------------|
| No Resi | Resi dari WH China |
| Berat | Berat (kg) |
| Ukuran | Dimensi box |
| Biaya Jasa | Biaya jasa (admin only, tersembunyi dari customer) |
| Foto | Link foto barang |
| Match | Nama barang + customer (jika matched) |
| Status | Matched / Unmatched |
| Tanggal | Tanggal input |
| Aksi | Tombol Edit / Hapus |

### Tombol Utama

| Tombol | Fungsi |
|--------|--------|
| Auto Match | Jalankan auto-matching untuk semua data unmatched |
| Input Data WH | Buka modal input data WH China |

### Form Input Data WH China (Modal)

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nomor Resi | Text | - | Wajib |
| Berat (kg) | Number | - | Wajib |
| Ukuran Box | Text | "Contoh: 40x30x25 cm" | Wajib |
| Biaya Jasa | Number | - | Opsional (tersembunyi dari customer) |
| Foto Barang | File | - | Opsional |

### Tombol Modal

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Simpan | Input data WH China |

### Setelah Submit

- Data masuk ke tabel wh_china_data (status: Unmatched)
- Sistem otomatis cari match: ada customer yang setor resi dengan nomor yang sama?
  - **YA** → Auto-match, status berubah "Matched"
  - **TIDAK** → Status tetap "Unmatched", menunggu customer klaim atau setor resi

### Edit & Hapus Data WH China

| Aksi | Tombol | Konfirmasi |
|------|--------|------------|
| Edit | Ikon pensil | Buka modal edit |
| Hapus | Ikon tempat sampah | "Yakin hapus data WH China ini?" |

---

## LANGKAH 13 — Customer Klaim Resi dari WH China

**Siapa:** Customer
**Halaman:** /unmatched-resi (Resi Belum Dikenali)

Jika ada data WH China yang belum matched, customer bisa melihat dan mengklaim resi yang mungkin miliknya.

### Sidebar Customer → "Resi Belum Dikenali"

### Info Box

Pesan biru: "Jika Anda melihat nomor resi yang merupakan milik Anda, klik 'Klaim Resi' lalu isi data barang. Sistem akan otomatis menghubungkan data Anda dengan data gudang China."

### Daftar Resi (Grid Card, 3 kolom desktop, 1 kolom mobile)

| Informasi | Keterangan |
|-----------|------------|
| Nomor Resi | Dari data WH China (font monospace) |
| Tanggal Input | Kapan data dimasukkan admin |
| Berat | Dari WH China (kg) |
| Ukuran | Dimensi dari WH China |
| Foto Barang | Foto dari WH China (jika ada) |
| Badge | "Belum Dikenali" (kuning) |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Resi Ini | Buka modal form klaim |

### Modal Form Klaim Resi

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Data dari Gudang China | Info box | - | Menampilkan Berat + Ukuran (read-only) |
| Pilih Box | Select | "Pilih box tujuan..." | Wajib, hanya box OPEN milik customer |
| Nama Barang | Text | "Contoh: iPhone 15 Case" | Wajib, min 2 karakter |
| Jumlah | Number | - | Wajib, min 1 |
| Harga (Yuan) | Number | "0.00" | Wajib, min 0.01 |
| Barang Sensitive | Checkbox | - | Opsional |
| Jenis Sensitive | Select | "Pilih jenis..." | Wajib jika sensitive |
| Foto Bukti Barang (CO) | File | - | Wajib, JPG/PNG/WebP, max 5MB |

### Tombol Modal

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Klaim Resi | Submit |

### Setelah Klaim

- Item dibuat di box yang dipilih dengan resi dari WH China
- Data WH China otomatis matched ke item baru
- last_setor_date box diupdate
- Pesan: "Resi [nomor] berhasil diklaim dan terhubung ke data WH China."

### Empty State

"Semua resi sudah dikenali" + ikon centang + "Saat ini tidak ada resi dari gudang China yang menunggu klaim."

---

# 5. FASE 4 — Barang No Tuan & Lelang

---

## LANGKAH 14 — Admin Tandai Barang sebagai No Tuan

**Siapa:** Admin
**Halaman:** /admin/manage-boxes (Manage Box)

Jika ada barang di box yang tidak diklaim customer mana pun, admin menandainya sebagai "No Tuan".

### Proses

1. Buka Manage Box → klik box yang berisi barang tidak diklaim
2. Detail box muncul → lihat tabel barang
3. Klik tombol **"No Tuan"** di sebelah barang status "active"
4. Konfirmasi: "Tandai barang '[nama]' sebagai No Tuan?"
5. Status barang: active → **no_tuan**
6. Barang sekarang muncul di halaman customer /no-tuan
7. Pesan: "Barang '[nama]' ditandai sebagai No Tuan."

---

## LANGKAH 15 — Admin Input Barang No Tuan Langsung

**Siapa:** Admin
**Halaman:** /admin/no-tuan/create

Untuk barang yang tiba di warehouse tanpa ada customer yang setor resi sama sekali, admin bisa input langsung sebagai No Tuan.

### Sidebar Admin → "Barang & Box" → "Input No Tuan"

### Info Box

Pesan kuning: "Barang yang tiba di warehouse tanpa ada customer yang setor resi. Barang akan otomatis tampil di halaman 'No Tuan' customer dan bisa diklaim dengan denda Rp 5.000."

### Form Input Barang

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nama Barang | Text | "Contoh: Sepatu Nike Air Max" | Wajib, min 2 karakter |
| Jumlah | Number | - | Wajib, min 1, max 9999 |
| Box | Select | "Pilih box tempat barang ini berada" | Wajib, hanya box OPEN/CLOSED |
| Deskripsi Barang | Textarea | "Deskripsi barang, ciri-ciri, atau info lainnya..." | Opsional, max 1000 karakter |
| Foto Barang | File | - | Opsional, JPG/PNG, max 5MB |
| Catatan | Textarea | "Catatan tambahan untuk admin..." | Opsional, max 500 karakter |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Kembali ke Manage Box |
| Input Barang No Tuan | Submit |

### Setelah Submit

- Barang dibuat dengan status: **no_tuan**
- customer_id: null (belum ada pemilik)
- resi_number: null (tidak ada yang setor)
- proof_co: foto yang diupload (jika ada)
- arrived_china: true (barang sudah di warehouse)
- Barang otomatis muncul di halaman customer /no-tuan
- Audit log: "Admin input barang No Tuan: '[nama]'"
- Pesan: "Barang '[nama]' berhasil ditambahkan sebagai No Tuan."

---

## LANGKAH 16 — Customer Klaim Barang No Tuan

**Siapa:** Customer
**Halaman:** /no-tuan

Customer melihat barang No Tuan dan mengklaim yang merupakan miliknya.

### Sidebar Customer → "No Tuan"

### Info Box

Pesan kuning: "Klaim barang dikenakan denda Rp 5.000 per barang. Denda ditagih bersamaan dengan pembayaran invoice berikutnya."

### Daftar Barang (Grid Card, 3 kolom desktop, 1 kolom mobile)

| Informasi | Keterangan |
|-----------|------------|
| Nama Barang | Nama item |
| Nomor Resi | Resi (jika ada, jika tidak "-") |
| Jumlah | Qty barang |
| Harga | Harga dalam Yuan (jika diketahui) |
| Box | Box tempat barang berada (tracking/batch) |
| Badge "Sensitive" | Muncul jika barang sensitive |
| Badge "No Tuan" | Badge oranye |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Barang | Buka modal klaim |

### Modal Klaim Barang

| Komponen | Keterangan |
|----------|------------|
| Info Barang | Nama, Resi, Qty (read-only, abu-abu) |
| Warning | "Klaim akan dikenakan denda Rp 5.000. Lanjutkan?" (kuning) |
| Bukti Pembelian | File upload (JPG/PNG, max 5MB) — foto nota/resi beli dari supplier |
| Keterangan | Textarea opsional (max 500 karakter) — catatan tambahan |

### Tombol Modal

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Klaim Barang | Submit |

### Setelah Klaim (di belakang layar)

1. Sistem lock row barang (cegah 2 customer klaim bersamaan)
2. Cek: status masih no_tuan?
3. Status: no_tuan → **claimed**
4. customer_id diisi ke customer yang klaim
5. Denda Rp 5.000 dibuat (status: pending)
6. Notifikasi ke customer: "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan."
7. Denda akan masuk ke invoice berikutnya saat admin generate invoice

### Error Klaim

| Kondisi | Pesan |
|---------|-------|
| Barang sudah diklaim orang lain | "Barang sudah diklaim oleh customer lain." |
| Bukti pembelian kosong | "Upload bukti pembelian (foto nota/resi)" |
| Format file salah | "Format foto harus jpg, png" |
| File terlalu besar | "Ukuran foto maksimal 5MB" |

### Empty State

"Tidak ada barang No Tuan" + "Saat ini tidak ada barang yang tersedia untuk diklaim."

---

## LANGKAH 17 — Admin Barang Lelang

**Siapa:** Admin
**Halaman:** /admin/lelang

Barang No Tuan yang terlalu lama tidak diklaim bisa ditandai untuk dijual/dilelang.

### Sidebar Admin → "Barang & Box" → "Barang Lelang"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama barang |
| Filter Status | Klaim WH / Dijual / Lelang |
| Filter Customer | Per customer |
| Filter Tanggal | Per tanggal |
| Summary | Total barang, total nilai |

### Tabel Barang

| Kolom | Keterangan |
|-------|------------|
| Nama Barang | Nama item |
| No Resi | Resi |
| Box | Box asal |
| Qty | Jumlah |
| Customer | Nama customer (jika ada) |
| Status | Badge |
| Aksi | Tombol "Tandai Dijual" / "Tandai Lelang" |

### Aksi Barang

| Status Saat | Tombol | Status Baru |
|-------------|--------|-------------|
| klaim_wh | "Tandai Dijual" | dijual |
| klaim_wh | "Tandai Lelang" | lelang |
| dijual | "Tandai Lelang" | lelang |

### Detail Box (klik box)

| Komponen | Keterangan |
|----------|------------|
| Timeline Status | OPEN → SENT TO CARGO → OTW INA → UP INVOICE → DONE |
| Tabel Barang | Daftar barang + status per item |
| Tombol Status | Update status box sesuai urutan |
| Tombol Close/Open | Tutup/buka box |
| Tombol "No Tuan" | Untuk item active → no_tuan |
| Tombol "Klaim WH" | Untuk item no_tuan → klaim_wh |

### Status Box & Aksi

| Status Saat | Tombol | Status Baru |
|-------------|--------|-------------|
| OPEN | "Tutup Box" | CLOSED |
| CLOSED | "Buka Box" | OPEN |
| OPEN | "Sent to Cargo" | SENT_TO_CARGO |
| SENT_TO_CARGO | "OTW Indonesia" | OTW_INA |
| OTW_INA | "UP Invoice" | UP_INVOICE |
| UP_INVOICE | "DONE" | DONE |

### Status Item di Box

| Status | Badge | Tombol Aksi |
|--------|-------|-------------|
| active | - | "No Tuan" |
| no_tuan | Oranye "No Tuan" | "Klaim WH" |
| claimed | Hijau "Diklaim" | - |
| klaim_wh | Merah "Klaim WH" | - |
| shipped | Biru "Shipped" | - |

---

# 6. FASE 5 — Invoice & Pembayaran

---

## LANGKAH 18 — Admin Update Estimasi (ETD/ETA)

**Siapa:** Admin
**Halaman:** /admin/est-update

Admin mengupdate estimasi keberangkatan dan kedatangan box. Info ini tampil di dashboard customer.

### Sidebar Admin → "Pengaturan" → "Est Update"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box |
| Tabel | Daftar box dengan ETD/ETA saat ini |

### Form Update (klik salah satu box)

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Box | Info | - | Read-only |
| ETD | Date | - | Opsional, estimasi keberangkatan |
| ETA | Date | - | Opsional, estimasi tiba |
| Catatan | Textarea | "Tambahkan catatan..." | Opsional |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup form |
| Simpan | Update ETD/ETA |

### Setelah Simpan

- ETD/ETA diupdate di box
- Notifikasi ke customer: "Estimasi box [nomor] diupdate"
- Customer bisa lihat di Dashboard dan My Box

---

## LANGKAH 19 — Admin Generate Invoice

**Siapa:** Admin
**Halaman:** /admin/invoices

Setelah box sampai di Indonesia (status OTW_INA), admin generate invoice.

### Sidebar Admin → "Keuangan" → "Generate Invoice"

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice Number | Nomor invoice unik |
| Customer | Nama customer |
| Box | Box terkait |
| Berat (kg) | Berat aktual |
| Fee TAX | Biaya tax (berat/volume × rate) |
| Fee WH | Warehouse fee (tiered) |
| Fee Packing | Biaya packing (tiered) |
| Grand Total | Total keseluruhan |
| Status | Badge status |

### Form Generate Invoice (Modal — klik "Buat Invoice")

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Pilih Box | Select | - | Wajib, box OTW_INA |
| Berat (kg) | Number | "Berat (kg)" | Wajib |
| Panjang (cm) | Number | - | Wajib |
| Lebar (cm) | Number | - | Wajib |
| Tinggi (cm) | Number | - | Wajib |
| Biaya Tambahan | Number | - | Opsional (default: 0) |

### Preview Invoice (otomatis saat isi field)

| Komponen | Rumus |
|----------|-------|
| Volume | (P × L × T) / 6 |
| Dasar | MAX(berat aktual, volume) |
| Fee TAX | Dasar × Rate (sharing/direct × air/sea × sensitive) |
| Fee WH | Tiered berdasarkan berat |
| Fee Packing | Tiered: ≤150gr / ≤1000gr / ≤2000gr / >2000gr |
| Denda Total | Total denda pending customer (otomatis) |
| Grand Total | Fee TAX + Fee WH + Fee Packing + Denda + Add On |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Generate Invoice | Submit |

### Setelah Generate

- Invoice dibuat dengan status: **waiting_payment**
- payment_deadline otomatis: invoice_date + 7 hari
- Denda pending → status: **tagged** (masuk ke invoice)
- Box status: OTW_INA → **UP_INVOICE**
- Notifikasi ke customer: "Invoice [nomor] sudah tersedia. Total: Rp X"
- Pesan: "Invoice [nomor] berhasil dibuat."

### Detail Invoice (klik salah satu invoice di tabel)

Menampilkan rincian lengkap: nomor, customer, box, berat, volume, semua fee, denda, grand total, status, tanggal.

---

## LANGKAH 20 — Customer Buat Invoice Fleksibel (Shopee-style)

**Siapa:** Customer
**Halaman:** /create-invoice

Customer juga bisa membuat invoice sendiri dengan memilih barang mana saja yang ingin dijadikan 1 invoice.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Daftar Barang | Checklist barang yang sudah arrived & belum ada di invoice |
| Tombol "Pilih Semua" | Centang/hapus semua barang |
| Dimensi Input | Panjang (cm), Lebar (cm), Tinggi (cm) |
| Preview | Perhitungan biaya otomatis |

### Form

| Field | Tipe | Aturan |
|-------|------|--------|
| Checkbox per barang | Checkbox | Min 1 barang harus dipilih |
| Panjang (cm) | Number | Wajib |
| Lebar (cm) | Number | Wajib |
| Tinggi (cm) | Number | Wajib |

### Setelah Submit

- Invoice dibuat dari barang yang dipilih
- Hitungan biaya otomatis (Fee TAX, Fee WH, Fee Packing)
- Status: waiting_payment

---

## LANGKAH 21 — Customer Bayar Invoice

**Siapa:** Customer
**Halaman:** /invoice lalu /invoice/{id}/pay

### Sidebar Customer → "Invoice"

### Halaman Invoice (/invoice)

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking, Box, Status |
| Tabel Invoice | Daftar invoice customer |

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice Number | Nomor invoice |
| Box | Box terkait |
| Weight | Berat (kg) |
| Volume | Volume |
| Fee TAX | Biaya tax |
| Fee WH | Warehouse fee |
| Fee Packing | Biaya packing |
| Grand Total | Total keseluruhan |
| Status | Badge |

### Status Invoice

| Status | Badge | Arti |
|--------|-------|------|
| waiting_payment | Kuning "Menunggu Pembayaran" | Customer belum bayar |
| waiting_verification | Biru "Menunggu Verifikasi" | Sudah bayar, admin belum verif |
| verified | Hijau "Terverifikasi" | Pembayaran terverifikasi |

### Tombol

| Tombol | Muncul Saat | Fungsi |
|--------|-------------|--------|
| Bayar | Status waiting_payment | Ke halaman bayar |

### Form Bayar Invoice (/invoice/{id}/pay)

| Field | Tipe | Aturan |
|-------|------|--------|
| Metode Pembayaran | Radio | Wajib: Transfer / QRIS |
| Bukti Transfer | File | Wajib, JPG/PNG, max 5MB |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Upload Bukti | Kirim bukti transfer |

### Setelah Submit

- Status: waiting_payment → **waiting_verification**
- Pesan: "Bukti transfer berhasil dikirim. Menunggu verifikasi admin."

---

## LANGKAH 22 — Admin Verifikasi Pembayaran

**Siapa:** Admin
**Halaman:** /admin/verification

### Sidebar Admin → "Keuangan" → "Verifikasi"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari invoice/customer |
| Filter Status | Default: waiting_verification |
| Tabel | Daftar invoice menunggu verifikasi |

### Tabel

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor invoice |
| Customer | Nama customer |
| Total | Nominal |
| Metode | Transfer / QRIS |
| Status | Badge |

### Detail Invoice (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Invoice | Nomor, customer, box, total |
| Bukti Transfer | Gambar bukti (bisa diklik untuk perbesar) |
| Metode | Transfer / QRIS |

### Tombol

| Tombol | Fungsi | Konfirmasi |
|--------|--------|------------|
| Verifikasi (hijau) | Setujui pembayaran | "Verifikasi pembayaran ini?" |
| Tolak (merah) | Tolak pembayaran | Modal: isi alasan penolakan |

### Setelah Verifikasi

- Status: waiting_verification → **verified**
- Denda yang tagged → **paid**
- Notifikasi ke customer: "Pembayaran Anda diverifikasi."
- Audit log: "Pembayaran invoice [nomor] diverifikasi"

### Setelah Penolakan

- Status: waiting_verification → **waiting_payment**
- Customer harus upload bukti baru
- Notifikasi ke customer: "Pembayaran ditolak. Alasan: [alasan]"

---

# 7. FASE 6 — Checkout & Pengiriman

---

## LANGKAH 23 — Customer Request Checkout

**Siapa:** Customer
**Halaman:** /checkout

Setelah invoice terverifikasi, customer bisa request pengiriman barang.

### Sidebar Customer → "Checkout"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Invoice Number, Status |
| Daftar Checkout | List checkout requests + status |

### Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning "Menunggu Proses" | Menunggu admin |
| on_process | Biru "Sedang Diproses" | Admin sedang proses |
| sent | Hijau "Terkirim" | Barang sudah dikirim |

### Tombol "Request Checkout"

Muncul jika ada invoice verified. Buka modal:

### Form Checkout

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Invoice | Select | - | Wajib, invoice verified |
| Tipe Alamat | Radio | - | Wajib: Personal / Dropship |
| Nama Penerima | Text | "Nama penerima" | Wajib, min 3 karakter |
| No Telepon Penerima | Tel | "No telp" | Wajib, min 10 digit |
| Alamat Lengkap | Textarea | "Alamat lengkap" | Wajib, min 10 karakter |
| Nama Pengirim | Text | "Nama pengirim" | Wajib jika dropship |
| No Telepon Pengirim | Tel | "No telp pengirim" | Wajib jika dropship |
| Konfirmasi | Checkbox | - | Wajib: centang konfirmasi |

### Perbedaan Personal vs Dropship

| Field | Personal | Dropship |
|-------|----------|----------|
| Alamat tujuan | Alamat sendiri | Alamat penerima |
| Nama Pengirim | Tidak perlu | Wajib |
| No Telp Pengirim | Tidak perlu | Wajib |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Request Checkout | Submit |

### Setelah Submit

- Checkout dibuat dengan status: **request**
- Pesan: "Request checkout berhasil dikirim."

---

## LANGKAH 24 — Admin Proses Checkout

**Siapa:** Admin
**Halaman:** /admin/checkouts

### Sidebar Admin → "Keuangan" → "Checkout"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari invoice/customer |
| Filter Status | request / on_process / sent |
| Tabel | Daftar checkout requests |

### Detail Checkout (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Invoice | Nomor, customer, total |
| Alamat | Tipe (Personal/Dropship), Nama Penerima, No Telp, Alamat |
| Pengirim | Nama + No Telp (jika dropship) |

### Tombol "Proses Checkout"

| Field | Tipe | Aturan |
|-------|------|--------|
| Foto Packing | File | Wajib, foto barang yang sudah dikemas |
| Nomor Resi/Tracking | Text | Wajib, nomor tracking pengiriman |

### Setelah Proses

- Status: request → **on_process** → **sent**
- Foto packing tersimpan
- Tracking number tersimpan
- Notifikasi ke customer: "Barang Anda sedang diproses. Tracking: [nomor]"
- Box status: UP_INVOICE → **DONE**

---

# 8. FASE 7 — Komplain

---

## LANGKAH 25 — Customer Ajukan Komplain

**Siapa:** Customer
**Halaman:** /komplain

### Sidebar Customer → "Komplain"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Tombol "Ajukan Komplain" | Buka modal form |
| Filter Status | Semua / open / in_review / processing / resolved |
| Daftar Komplain | List komplain + status badge |

### Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Baru diajukan |
| in_review | Biru | Sedang ditinjau admin |
| processing | Biru | Sedang diproses |
| resolved | Hijau | Selesai |

### Form Komplain (Modal)

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Jenis Komplain | Select | "Pilih jenis" | Wajib: Kurang Barang Ekspedisi, Tidak Arrived China/Indonesia, Kurang China/Indonesia |
| Resolusi | Radio | - | Wajib: Refund / Penggantian |
| No Invoice | Text | "No invoice" | Opsional |
| No Resi | Text | "No resi" | Opsional |
| Deskripsi | Textarea | "Jelaskan masalah" | Wajib, min 10 karakter, max 2000 |
| Video | File | - | Opsional, MP4/MOV, max 50MB |
| Foto | File | - | Opsional, JPG/PNG, max 5MB |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Ajukan Komplain | Submit |

### Setelah Submit

- Komplain dibuat dengan status: **open**
- Notifikasi ke admin
- Pesan: "Komplain berhasil diajukan."

---

## LANGKAH 26 — Admin Tangani Komplain

**Siapa:** Admin
**Halaman:** /admin/complains

### Sidebar Admin → "Customer" → "Komplain"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari customer/resi |
| Filter Status | open / in_review / processing / resolved |
| Tabel | Daftar komplain |

### Detail Komplain (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Jenis | Tipe masalah |
| Resolusi | Refund / Penggantian |
| Deskripsi | Detail komplain |
| Video | Video bukti (jika ada) |
| Foto | Foto bukti (jika ada) |
| Status | Badge status |

### Tombol Update Status

| Status Saat | Tombol | Status Baru | Notifikasi ke Customer |
|-------------|--------|-------------|----------------------|
| open | "Tinjau" | in_review | "Komplain Anda sedang diproses" |
| in_review | "Proses" | processing | - |
| processing | "Selesai" | resolved | "Komplain Anda sudah selesai: [keterangan]" |

---

# 9. FASE 8 — Owner Panel

Owner memiliki akses ke **SEMUA halaman admin** + halaman owner khusus.

---

## LANGKAH 27 — Owner Login & Dashboard

**Siapa:** Owner
**Halaman:** /owner/dashboard

Owner login → sidebar "Owner" → "Owner Dashboard".

### Stat Cards (4 kartu)

| Kartu | Informasi |
|-------|-----------|
| Revenue Bulan Ini | Total revenue + growth % dibanding bulan lalu |
| Customer Aktif | Jumlah aktif + baru bulan ini |
| Box Aktif | Jumlah aktif + selesai bulan ini |
| Invoice Pending | Belum bayar + belum verifikasi |

### Grafik Revenue

Grafik batang revenue per bulan (6 bulan terakhir).

### Top Customer (5 teratas)

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Total Transaksi | Jumlah invoice |
| Total Revenue | Nominal total |

### Recent Invoices (5 terbaru)

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor invoice |
| Customer | Nama customer |
| Total | Nominal |
| Status | Badge status |

### Recent Activity

Log aktivitas terbaru (audit trail) — siapa melakukan apa dan kapan.

### Quick Menu Links

| Menu | Klik ke |
|------|---------|
| Laporan Keuangan | /owner/finance |
| Manage Admin | /owner/manage-admin |
| Manage Users | /owner/users |
| All Data | /owner/data |
| Audit Log | /owner/audit-log |

---

## LANGKAH 28 — Owner Laporan Keuangan

**Siapa:** Owner
**Halaman:** /owner/finance

### Sidebar Owner → "Owner" → "Laporan Keuangan"

### Summary Cards (6 kartu)

| Kartu | Informasi |
|-------|-----------|
| Total Revenue | Semua revenue |
| Outstanding | Total belum dibayar |
| Profit | Revenue - Outstanding |
| Cash In | Total uang masuk |
| Cash Out | Total uang keluar |
| Total Invoice | Jumlah invoice |

### Filter

| Filter | Keterangan |
|--------|------------|
| Tanggal Dari | Tanggal mulai |
| Tanggal Sampai | Tanggal akhir |
| Bulan | Filter per bulan |
| Tahun | Filter per tahun |
| Customer | Filter per customer |
| Status | Filter per status invoice |
| Search | Cari invoice/customer |

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor invoice |
| Customer | Nama customer |
| Box | Box terkait |
| Grand Total | Total nominal |
| Status | Badge status |
| Tanggal | Tanggal invoice |

### Tombol Export

| Tombol | Format | Fungsi |
|--------|--------|--------|
| Export CSV | .csv | Download data keuangan CSV |
| Export Excel | .xlsx | Download data keuangan Excel |

---

## LANGKAH 29 — Owner Manage Admin

**Siapa:** Owner
**Halaman:** /owner/manage-admin

### Sidebar Owner → "Manajemen" → "Manage Admin"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email admin |
| Filter Status | Active / Inactive / Pending |
| Tabel | Daftar admin |

### Tabel Admin

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama admin |
| Email | Alamat email |
| Status | Badge status |
| Terdaftar | Tanggal registrasi |

### Detail Admin (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Lengkap | Nama, email, telepon, KTP, alamat |
| Status | Badge status |
| Activity History | Log aktivitas admin |

### Tombol Aksi

| Tombol | Fungsi | Konfirmasi |
|--------|--------|------------|
| Aktivasi | Aktifkan admin | "Aktivasi admin ini?" |
| Nonaktifkan | Nonaktifkan admin | "Admin tidak bisa login setelah dinonaktifkan." |

---

## LANGKAH 30 — Owner Manage Users

**Siapa:** Owner
**Halaman:** /owner/users

### Sidebar Owner → "Manajemen" → "Manage Users"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email |
| Filter Role | Customer / Admin / Owner |
| Filter Status | Active / Inactive / Pending |
| Tabel | Semua user |

### Tabel Users

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama user |
| Email | Alamat email |
| Role | Badge role (Customer/Admin/Owner) |
| Status | Badge status |
| Terdaftar | Tanggal registrasi |

### Detail User (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Lengkap | Semua data user |
| Tombol "Ubah Role" | Buka modal ubah role |

### Modal Ubah Role

| Field | Tipe |
|-------|------|
| Role Baru | Select: Customer / Admin / Owner |

---

## LANGKAH 31 — Owner All Data

**Siapa:** Owner
**Halaman:** /owner/data

### Sidebar Owner → "Manajemen" → "All Data"

### Tab (6 tab)

| Tab | Isi |
|-----|-----|
| Customers | Semua data customer |
| Boxes | Semua data box |
| Invoices | Semua data invoice |
| Items | Semua data barang |
| Checkouts | Semua data checkout |
| Complains | Semua data komplain |

### Komponen per Tab

| Komponen | Keterangan |
|----------|------------|
| Search | Cari data |
| Tabel | Daftar data sesuai tab |
| Pagination | Navigasi halaman |

---

## LANGKAH 32 — Owner Audit Log

**Siapa:** Owner
**Halaman:** /owner/audit-log

### Sidebar Owner → "Manajemen" → "Audit Log"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari aktivitas/user |
| Filter Event | Jenis event |
| Tabel | Daftar log |

### Tabel Audit Log

| Kolom | Keterangan |
|-------|------------|
| User | Siapa yang melakukan |
| Event | Jenis (created, updated, deleted, custom) |
| Subject | Model (Box, Invoice, Item, dll) |
| Perubahan | Detail old → new values |
| Waktu | Kapan terjadi |

### Detail Log (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| User Info | Siapa |
| Event Detail | Jenis + deskripsi |
| Old Values | Data sebelum diubah |
| New Values | Data setelah diubah |

---

# 10. FASE 9 — Fitur Umum

---

## LANGKAH 33 — Customer Kalkulator Biaya

**Siapa:** Customer
**Halaman:** /kalkulator

### Sidebar Customer → "Kalkulator"

### Form Kalkulator

| Field | Tipe | Aturan |
|-------|------|--------|
| Metode | Radio | Air / Sea |
| Tipe | Radio | Sharing / Direct |
| Berat (kg) | Number | Wajib |
| Panjang (cm) | Number | Wajib |
| Lebar (cm) | Number | Wajib |
| Tinggi (cm) | Number | Wajib |
| Barang Sensitive | Checkbox | Opsional |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Hitung | Hitung estimasi biaya |
| Reset | Kosongkan form |

### Hasil Perhitungan

| Komponen | Rumus |
|----------|-------|
| Volume | (P × L × T) / 6 |
| Dasar | MAX(berat aktual, volume) |
| Fee TAX | Dasar × Rate (sesuai metode/tipe/sensitive) |
| Fee WH | Tiered berdasarkan berat |
| Fee Packing | Tiered: ≤150gr / ≤1000gr / ≤2000gr / >2000gr |
| Grand Total | Fee TAX + Fee WH + Fee Packing |

---

## LANGKAH 34 — Notifikasi (Semua Role)

**Siapa:** Semua role
**Halaman:** /notifications

### Akses

- Bell icon di pojok kanan atas (semua halaman)
- Badge angka merah = jumlah notifikasi belum dibaca
- Klik bell → dropdown 5 notifikasi terbaru
- Klik "Lihat Semua" → halaman /notifications

### Halaman Notifikasi

| Komponen | Keterangan |
|----------|------------|
| Daftar Notifikasi | List semua notifikasi |
| Badge unread | Dot biru = belum dibaca |
| Tombol "Tandai Semua Dibaca" | Mark all as read |

### Jenis Notifikasi

| Event | Target | Pesan |
|-------|--------|-------|
| Akun diaktivasi | Customer | "Akun Anda sudah aktif." |
| Invoice baru | Customer | "Invoice [nomor] sudah tersedia. Total: Rp X" |
| Pembayaran diverifikasi | Customer | "Pembayaran Anda diverifikasi." |
| Pembayaran ditolak | Customer | "Pembayaran ditolak. Alasan: [alasan]" |
| Checkout diproses | Customer | "Barang Anda sedang diproses. Tracking: [nomor]" |
| Komplain diproses | Customer | "Komplain Anda sedang diproses." |
| Komplain selesai | Customer | "Komplain Anda sudah selesai." |
| Box ditutup | Customer | "Box [nomor] sudah ditutup." |
| Klaim berhasil | Customer | "Barang berhasil diklaim. Denda: Rp 5.000" |
| Estimasi diupdate | Customer | "Estimasi box [nomor] diupdate." |
| Registrasi baru | Admin | Customer baru mendaftar |
| Payment request | Admin | Customer upload bukti transfer |
| Komplain baru | Admin | Customer ajukan komplain |

---

## LANGKAH 35 — Profile (Semua Role)

**Siapa:** Semua role
**Halaman:** /profile

### Akses

- Sidebar → avatar (huruf pertama nama) → dropdown → "Profil Saya"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Update Profile | Ubah nama, email |
| Update Password | Password lama + password baru + konfirmasi |
| Hapus Akun | Hapus akun permanen (butuh konfirmasi password) |

---

## LANGKAH 36 — Logout (Semua Role)

**Siapa:** Semua role

### Akses

- Sidebar → avatar → dropdown → tombol "Keluar" (merah)

### Setelah Logout

- Session dihapus
- Redirect ke halaman utama (/)

---

# 11. Status — Referensi Cepat

## 11.1 Status Box

| Status | Badge | Arti |
|--------|-------|------|
| OPEN | Hijau "Terbuka" | Box dibuka, customer bisa setor resi |
| CLOSED | Abu-abu "Ditutup" | Box ditutup, tidak bisa setor |
| SENT_TO_CARGO | Biru "Dikirim ke Cargo" | Sudah dikirim dari China |
| OTW_INA | Kuning "Dalam Perjalanan" | Menuju Indonesia |
| UP_INVOICE | Biru "Invoice Dibuat" | Invoice sudah digenerate |
| DONE | Hijau "Selesai" | Proses selesai |

## 11.2 Status Item

| Status | Badge | Arti | Bisa Diklaim? |
|--------|-------|------|---------------|
| active | - | Barang aktif | - |
| no_tuan | Oranye "No Tuan" | Tidak ada pemilik | Ya (denda Rp 5.000) |
| claimed | Hijau "Diklaim" | Sudah diklaim customer | Tidak |
| klaim_wh | Merah "Klaim WH" | WH ambil untuk jual/lelang | Tidak |
| shipped | Biru "Shipped" | Sudah dikirim | Tidak |
| hold | Abu-abu | Ditahan (deadline lewat) | Tidak |
| dijual | - | Dijual WH | Tidak |
| lelang | - | Dilelang WH | Tidak |

## 11.3 Status Invoice

| Status | Badge | Arti |
|--------|-------|------|
| waiting_payment | Kuning | Belum bayar |
| waiting_verification | Biru | Sudah bayar, belum verif |
| verified | Hijau | Terverifikasi |

## 11.4 Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning | Menunggu proses |
| on_process | Biru | Sedang diproses |
| sent | Hijau | Sudah dikirim |

## 11.5 Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Baru |
| in_review | Biru | Ditinjau |
| processing | Biru | Diproses |
| resolved | Hijau | Selesai |

## 11.6 Status Customer

| Status | Arti |
|--------|------|
| PENDING | Baru daftar, belum bisa login |
| ACTIVE | Sudah aktivasi, bisa login |
| INACTIVE | Dinonaktifkan, tidak bisa login |

## 11.7 Status Denda

| Status | Arti |
|--------|------|
| pending | Denda belum ditagih |
| tagged | Denda sudah masuk ke invoice |
| paid | Denda sudah dibayar |

---

# 12. Pesan Error & Sukses

## 12.1 Pesan Error

| Kondisi | Pesan |
|---------|-------|
| Email kosong | "Email wajib diisi" |
| Password salah | "Email atau password salah" |
| Akun PENDING | "Akun belum aktif. Hubungi admin." |
| Akun INACTIVE | "Akun telah dinonaktifkan." |
| Terkunci 5x | "Akun terkunci. Coba lagi dalam 15 menit." |
| Resi duplikat | "Nomor resi sudah terdaftar di box ini" |
| Box ditutup | "Box sudah ditutup. Tidak bisa menambah barang." |
| File salah format | "Format file harus jpg, png, atau webp" |
| File besar | "Ukuran file maksimal 5MB" |
| Video besar | "Ukuran video maksimal 50MB" |
| Barang diklaim | "Barang sudah diklaim oleh customer lain." |
| Invoice tidak ada | "Invoice tidak ditemukan" |
| Invoice sudah dibayar | "Invoice sudah dibayar" |
| Checkout belum verif | "Invoice belum terverifikasi" |
| Checkout duplikat | "Checkout sudah diajukan" |
| Alamat kosong | "Alamat wajib diisi" |
| Kurs duplikat | "Kurs untuk tanggal ini sudah ada." |

## 12.2 Pesan Sukses

| Aksi | Pesan |
|------|-------|
| Register | "Registrasi berhasil! Menunggu aktivasi dari admin." |
| Login | "Selamat datang, [nama]!" |
| Aktivasi | "Akun [nama] berhasil diaktivasi." |
| Setor Resi | "Barang berhasil didaftarkan." |
| Bayar Invoice | "Bukti transfer berhasil dikirim. Menunggu verifikasi admin." |
| Verifikasi | "Pembayaran berhasil diverifikasi." |
| Tolak Bayar | "Bukti transfer ditolak." |
| Generate Invoice | "Invoice [nomor] berhasil dibuat." |
| Checkout | "Request checkout berhasil dikirim." |
| Proses Checkout | "Checkout berhasil diproses." |
| Komplain | "Komplain berhasil diajukan." |
| Klaim No Tuan | "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan." |
| Input No Tuan | "Barang berhasil ditambahkan sebagai No Tuan." |
| Klaim WH | "Barang ditandai Klaim WH." |
| Update Rate | "Rate berhasil diupdate." |
| Input Kurs | "Kurs berhasil diupdate." |
| Close Box | "Box berhasil ditutup." |
| Open Box | "Box berhasil dibuka." |
| Update Estimasi | "Estimasi berhasil diupdate." |
| Input WH China | "Data WH China berhasil diinput." |

---

# END OF USER GUIDE

Dokumen ini mencakup SELURUH 36 langkah, 12 fase, semua halaman, modul, dan fitur di Ting Warehouse Management System v2.1.

Terakhir diperbarui: Juli 2026
