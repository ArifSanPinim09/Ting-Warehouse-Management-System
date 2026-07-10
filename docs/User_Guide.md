# USER GUIDE
# Ting Warehouse Management System
# Versi 2.1 — Juli 2026

---

# Daftar Isi

1. Tentang Sistem
2. Alur Lengkap dari Awal hingga Akhir
3. Status — Referensi Cepat
4. Pesan Error & Sukses

---

# 1. Tentang Sistem

Ting Warehouse Management System adalah website operasional untuk perusahaan freight forwarding China → Jakarta. Sistem ini menggantikan Airtable dan Google Sheets.

| Informasi | Detail |
|-----------|--------|
| Website | www.tingwarehouse.my.id |
| Bahasa | Indonesia |
| Perangkat | Laptop, HP Android, iPhone |

### Tiga Role Pengguna

| Role | Siapa | Akses |
|------|-------|-------|
| Customer | Pengguna jasa forwarding | Dashboard, Setor Resi, Invoice, Checkout, Komplain |
| Admin | Staff operasional (3 orang) | Kelola box, invoice, verifikasi, rate, customer |
| Owner | Pemilik bisnis | Semua akses admin + laporan keuangan + kelola user |

---

# 2. Alur Lengkap dari Awal hingga Akhir

Dokumen ini mengalir mengikuti proses bisnis nyata. Setiap langkah bergantian antara Customer, Admin, dan Owner sesuai urutan kerja yang sebenarnya.

---

## LANGKAH 1 — Customer Mendaftar

**Siapa:** Customer
**Halaman:** /register

Customer membuka website dan mendaftar sebagai pengguna baru.

### Form Register

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nama | Text | "Nama lengkap" | Wajib, min 3 karakter |
| Email | Email | "email@contoh.com" | Wajib, format email, tidak boleh sama dengan yang sudah daftar |
| No Telepon | Text | "08123456789" | Wajib, angka saja, min 10 digit |
| No KTP | Text | "16 digit No KTP" | Wajib, tepat 16 digit angka, tidak boleh sama |
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

## LANGKAH 2 — Admin Aktivasi Akun Customer

**Siapa:** Admin
**Halaman:** /admin/customers (Info Customer)

Admin login terlebih dahulu, lalu masuk ke halaman Info Customer untuk mengaktivasi akun customer yang baru mendaftar.

### Form Login Admin

| Field | Tipe | Aturan |
|-------|------|--------|
| Email | Email | Wajib |
| Password | Password | Wajib |
| Remember Me | Checkbox | Opsional |

Setelah login, admin diarahkan ke Dashboard Admin (/admin/dashboard).

### Sidebar Admin — Grup "Customer" → "Info Customer"

Admin klik menu **Info Customer** di sidebar.

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
2. Detail customer muncul (nama, email, telepon, KTP, alamat)
3. Klik tombol **"Aktivasi"**
4. Konfirmasi: "Aktivasi customer ini?"
5. Status berubah: PENDING → ACTIVE
6. Pesan: "Akun [nama] berhasil diaktivasi."
7. Customer mendapat notifikasi: "Akun Anda sudah aktif."

---

## LANGKAH 3 — Customer Login Pertama Kali

**Siapa:** Customer
**Halaman:** /login

Customer yang sudah diaktivasi bisa login.

### Form Login

| Field | Tipe | Aturan |
|-------|------|--------|
| Email | Email | Wajib |
| Password | Password | Wajib |
| Remember Me | Checkbox | Opsional |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Masuk | Login ke sistem |

### Setelah Login

- Customer diarahkan ke Dashboard (/dashboard)

---

## LANGKAH 4 — Customer Melihat Dashboard

**Siapa:** Customer
**Halaman:** /dashboard

Dashboard menampilkan ringkasan aktivitas customer.

### Stat Cards (4 kartu di atas halaman)

| Kartu | Informasi | Klik ke |
|-------|-----------|---------|
| Box Aktif | Jumlah box OPEN/SENT/OTW | /box/sharing |
| Invoice Belum Bayar | Jumlah + total nominal | /invoice |
| Barang Bulan Ini | Jumlah barang bulan ini | /setor-resi |
| Resi Bulan Ini | Jumlah resi bulan ini | /setor-resi |

### Alert Banner (muncul jika ada data WH China yang belum dikenali)

| Komponen | Keterangan |
|----------|------------|
| Pesan | "Ada X resi dari gudang China yang belum dikenali" |
| Klik ke | /unmatched-resi |

### Status Box (tabel daftar box milik customer)

| Kolom (Desktop) | Keterangan |
|-----------------|------------|
| Nomor Box | Tracking number atau "Box #ID" |
| Kode Box | Batch name |
| Jenis Kirim | AIR atau SEA |
| ETD | Estimasi tanggal keberangkatan |
| ETA | Estimasi tanggal tiba |
| Status | Badge status box |
| Barang | Jumlah barang di box |

Klik salah satu box → **Detail Box** modal:
- Info box (Status, Tipe, Jenis Kirim, Kode)
- Tabel barang: No, Nama Barang, Qty, Berat, Volume, Keterangan

### Menu Cepat (7 shortcut)

| Menu | Klik ke |
|------|---------|
| Setor Resi | /setor-resi |
| My Box | /box/sharing |
| Invoice | /invoice |
| Checkout | /checkout |
| Komplain | /komplain |
| Kalkulator | /kalkulator |
| No Tuan | /no-tuan |

### Rate Hari Ini

| Informasi | Keterangan |
|-----------|------------|
| Kurs Yuan | Rp X |
| Rate Air | Rp X/kg |
| Rate Sea | Rp X/kg |
| Tombol "Buka Kalkulator" | Ke /kalkulator |

### Notifikasi (5 terbaru)

| Komponen | Keterangan |
|----------|------------|
| Judul | Jenis notifikasi |
| Pesan | Detail |
| Waktu | "X menit yang lalu" |

### FAB (Floating Action Button) — Mobile

Tombol hijau bulat di kanan bawah → langsung ke Setor Resi.

---

## LANGKAH 5 — Admin Membuat Box Baru

**Siapa:** Admin
**Halaman:** /admin/manage-boxes (Manage Box)

Admin membuat box baru untuk menampung barang customer.

### Sidebar Admin → "Barang & Box" → "Manage Box"

### Halaman Manage Box

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box berdasarkan tracking/batch |
| Filter | Tipe (Sharing/Direct/Handcarry), Status, Customer, Tanggal |
| Tombol "Tambah Box" | Buka modal form buat box |

### Form Tambah Box (Modal)

| Field | Tipe | Aturan |
|-------|------|--------|
| Tipe Box | Select | Wajib: Sharing / Direct / Handcarry |
| Metode | Select | Wajib: Air / Sea |
| Customer | Select | Opsional: Pilih customer atau "Tanpa Customer" |
| Tracking Number | Text | Opsional |
| Batch Name | Text | Opsional |
| Catatan | Textarea | Opsional |

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

## LANGKAH 6 — Customer Melihat My Box

**Siapa:** Customer
**Halaman:** /box/sharing atau /box/direct

Customer melihat box yang sudah dibuat admin.

### My Box Sharing (/box/sharing)

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking Number, Tanggal, ETD, ETA, Status |
| Box List | Card per box dengan status badge |
| Barang Row | Nama, Qty, Harga, Foto, Status Arrived |

### My Box Direct (/box/direct)

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking, Batch, Status |
| Tombol "Request Direct Sharing" | Request box direct baru |
| Batch List | Card per batch + daftar barang |
| Tombol "Request to Close" | Tutup batch |

### Empty State

"Tidak ada barang di box sharing" + tombol "Setor Resi Sekarang"

---

## LANGKAH 7 — Customer Setor Resi (Input Barang)

**Siapa:** Customer
**Halaman:** /setor-resi

Customer mendaftarkan barang yang akan dikirim.

### Form Setor Resi

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Box Tujuan | Select | "Pilih box..." | Wajib, hanya box OPEN |
| Nama Barang | Text | "Contoh: Baju kaos" | Wajib, min 2 karakter |
| Jumlah | Number | - | Wajib, min 1 |
| Harga (Yuan) | Number | "0.00" | Wajib, min 0.01 |
| Nomor Resi | Text | "Nomor resi dari supplier China" | Wajib |
| Foto Bukti (Proof CO) | File | - | Wajib, JPG/PNG/WebP, max 5MB |
| Barang Sensitive | Checkbox | - | Opsional |
| Jenis Sensitive | Select | "Pilih jenis..." | Wajib jika sensitive: Elektronik, Baterai, Cairan, Kosmetik, Makanan, Obat-obatan, Magnet, Lainnya |

### Indikator Real-time WH China

Saat customer mengetik nomor resi, sistem otomatis mengecek:

| Kondisi | Tampilan |
|---------|----------|
| Resi ditemukan di WH China | Box hijau: "Resi ditemukan di data gudang China!" + Berat, Ukuran, Tanggal, Foto |
| Resi tidak ditemukan | Tidak ada indikator |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Kembali ke dashboard |
| Daftarkan Barang | Submit |

### Setelah Submit

- Barang tersimpan di box yang dipilih
- Jika resi cocok dengan data WH China → otomatis matched
- last_setor_date box diupdate
- Notifikasi ke admin
- Pesan: "Barang berhasil didaftarkan."

### Error

| Kondisi | Pesan |
|---------|-------|
| Resi duplikat di box sama | "Nomor resi sudah terdaftar di box ini" |
| Box ditutup | "Box sudah ditutup. Tidak bisa menambah barang." |
| Format foto salah | "Format foto harus jpg, png, atau webp" |
| Foto terlalu besar | "Ukuran foto maksimal 5MB" |

---

## LANGKAH 8 — Admin Input Data WH China (Recap)

**Siapa:** Admin
**Halaman:** /admin/recap (Recap)

Admin memasukkan data barang dari gudang China. Data ini nantinya dicocokkan dengan resi customer.

### Sidebar Admin → "Barang & Box" → "Recap"

### Halaman Recap — 2 Tab

**Summary Stats (6 kartu):**

| Kartu | Informasi |
|-------|-----------|
| Box | Total box |
| Barang | Total item dari customer |
| WH China | Total data WH China |
| Matched | Data yang sudah cocok |
| Unmatched | Data yang belum cocok |
| Revenue | Total revenue |

**Filter:**

| Filter | Keterangan |
|--------|------------|
| Search | Cari resi, nama, customer |
| Tipe | Sharing / Direct / Handcarry |
| Metode | Air / Sea |
| Tanggal | Dari - Sampai |

---

### Tab "Customer" — Data dari Setor Resi

| Kolom | Keterangan |
|-------|------------|
| No Resi | Nomor resi barang |
| Nama Barang | Nama barang |
| Qty | Jumlah |
| Harga (¥) | Harga Yuan |
| Box | Box tempat barang |
| Customer | Nama customer |
| Status | Matched (hijau) / Unmatched (kuning) |

---

### Tab "WH China" — Data dari Gudang China

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
| Aksi | Edit / Hapus |

**Tombol:**

| Tombol | Fungsi |
|--------|--------|
| Auto Match | Jalankan auto-matching semua data unmatched |
| Input Data WH | Buka modal input data WH China |

### Form Input Data WH China (Modal)

| Field | Tipe | Aturan |
|-------|------|--------|
| Nomor Resi | Text | Wajib |
| Berat (kg) | Number | Wajib |
| Ukuran Box | Text | Wajib (contoh: 40x30x25 cm) |
| Biaya Jasa | Number | Opsional (tersembunyi dari customer) |
| Foto Barang | File | Opsional |

### Setelah Submit

- Data masuk ke tabel wh_china_data
- Sistem otomatis cari match: ada customer yang setor resi dengan nomor yang sama?
  - YA → Auto-match, status "Matched"
  - TIDAK → Status "Unmatched", menunggu customer klaim

---

## LANGKAH 9 — Customer Klaim Resi dari WH China (Jika Ada)

**Siapa:** Customer
**Halaman:** /unmatched-resi (Resi Belum Dikenali)

Jika ada data WH China yang resinya milik customer tapi customer belum setor resi, customer bisa klaim dari halaman ini.

### Info Box

Pesan biru: "Jika Anda melihat nomor resi yang merupakan milik Anda, klik 'Klaim Resi' lalu isi data barang."

### Daftar Resi (Grid Card)

| Informasi | Keterangan |
|-----------|------------|
| Nomor Resi | Dari data WH China |
| Tanggal | Kapan data dimasukkan |
| Berat | Dari WH China (kg) |
| Ukuran | Dimensi dari WH China |
| Foto | Foto dari WH China |
| Badge | "Belum Dikenali" (kuning) |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Resi Ini | Buka modal form klaim |

### Modal Form Klaim

| Field | Tipe | Aturan |
|-------|------|--------|
| Data dari Gudang China | Info | Berat + Ukuran (read-only) |
| Pilih Box | Select | Wajib, hanya box OPEN milik customer |
| Nama Barang | Text | Wajib, min 2 karakter |
| Jumlah | Number | Wajib, min 1 |
| Harga (Yuan) | Number | Wajib, min 0.01 |
| Barang Sensitive | Checkbox | Opsional |
| Jenis Sensitive | Select | Wajib jika sensitive |
| Foto Bukti (CO) | File | Wajib, JPG/PNG/WebP, max 5MB |

### Setelah Klaim

- Barang dibuat + auto-match dengan data WH China
- Pesan: "Resi [nomor] berhasil diklaim dan terhubung ke data WH China."

---

## LANGKAH 10 — Admin Manage Box (Update Status & Kelola Barang)

**Siapa:** Admin
**Halaman:** /admin/manage-boxes

Admin mengelola box: update status, lihat barang, tandai barang khusus.

### Detail Box (klik salah satu box)

| Komponen | Keterangan |
|----------|------------|
| Info Box | Status, Tipe, Jenis Kirim, Kode |
| Timeline Status | OPEN → SENT TO CARGO → OTW INA → UP INVOICE → DONE |
| Tabel Barang | Daftar barang + status per item |

### Update Status Box

| Status Saat | Tombol | Status Baru |
|-------------|--------|-------------|
| OPEN | "Tutup Box" | CLOSED (customer tidak bisa setor lagi) |
| CLOSED | "Buka Box" | OPEN |
| OPEN/SENT_TO_CARGO | "Sent to Cargo" | SENT_TO_CARGO |
| SENT_TO_CARGO | "OTW Indonesia" | OTW_INA |
| OTW_INA | "UP Invoice" | UP_INVOICE |
| UP_INVOICE | "DONE" | DONE |

### Status Item di Box

| Status | Badge | Tombol Aksi |
|--------|-------|-------------|
| active | - | "No Tuan" (tandai barang tidak diklaim) |
| no_tuan | Oranye | "Klaim WH" (tandai untuk lelang) |
| claimed | Hijau | - |
| klaim_wh | Merah | - |
| shipped | Biru | - |

### Tandai Barang No Tuan

1. Klik item status "active"
2. Klik tombol "No Tuan"
3. Konfirmasi: "Tandai barang '[nama]' sebagai No Tuan?"
4. Status: active → no_tuan
5. Barang muncul di halaman customer /no-tuan

### Tandai Barang Klaim WH

1. Klik item status "no_tuan"
2. Klik tombol "Klaim WH"
3. Konfirmasi: "Barang '[nama]' akan ditandai Klaim WH untuk dijual/dilelang. Lanjutkan?"
4. Status: no_tuan → klaim_wh
5. Barang masuk halaman Barang Lelang (/admin/lelang)
6. Customer TIDAK BISA klaim lagi

---

## LANGKAH 11 — Admin Input Barang No Tuan Langsung

**Siapa:** Admin
**Halaman:** /admin/no-tuan/create

Untuk barang yang tiba di warehouse tanpa ada customer yang setor resi, admin bisa input langsung.

### Sidebar Admin → "Barang & Box" → "Input No Tuan"

### Info Box

Pesan kuning: "Barang yang tiba di warehouse tanpa ada customer yang setor resi. Barang akan otomatis tampil di halaman 'No Tuan' customer."

### Form Input Barang

| Field | Tipe | Placeholder | Aturan |
|-------|------|-------------|--------|
| Nama Barang | Text | "Contoh: Sepatu Nike Air Max" | Wajib, min 2 karakter |
| Jumlah | Number | - | Wajib, min 1 |
| Box | Select | "Pilih box..." | Wajib, box OPEN/CLOSED |
| Deskripsi | Textarea | "Deskripsi barang..." | Opsional, max 1000 |
| Foto Barang | File | - | Opsional, JPG/PNG, max 5MB |
| Catatan | Textarea | "Catatan..." | Opsional, max 500 |

### Setelah Submit

- Barang langsung status: **no_tuan**, customer_id: null
- Barang otomatis muncul di halaman customer /no-tuan
- Pesan: "Barang '[nama]' berhasil ditambahkan sebagai No Tuan."

---

## LANGKAH 12 — Customer Klaim Barang No Tuan

**Siapa:** Customer
**Halaman:** /no-tuan

Customer melihat barang No Tuan dan mengklaim yang merupakan miliknya.

### Info Box

Pesan kuning: "Klaim barang dikenakan denda Rp 5.000 per barang. Denda ditagih bersamaan dengan pembayaran invoice berikutnya."

### Daftar Barang (Grid Card)

| Informasi | Keterangan |
|-----------|------------|
| Nama Barang | Nama item |
| Nomor Resi | Resi (jika ada) |
| Jumlah | Qty |
| Harga | Yuan |
| Box | Box asal |
| Badge "Sensitive" | Jika barang sensitive |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Barang | Buka modal klaim |

### Modal Klaim

| Komponen | Keterangan |
|----------|------------|
| Info Barang | Nama, Resi, Qty (read-only) |
| Warning | "Klaim akan dikenakan denda Rp 5.000. Lanjutkan?" |
| Bukti Pembelian | File (JPG/PNG, max 5MB) — foto nota/resi |
| Keterangan | Textarea opsional (max 500) |

### Setelah Klaim

- Status: no_tuan → claimed
- customer_id diisi ke customer yang klaim
- Denda Rp 5.000 dibuat di sistem
- Notifikasi: "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan."

---

## LANGKAH 13 — Admin Barang Lelang

**Siapa:** Admin
**Halaman:** /admin/lelang

Admin melihat dan mengelola barang yang sudah di-klaim WH.

### Sidebar Admin → "Barang & Box" → "Barang Lelang"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama barang |
| Filter Status | Klaim WH / Dijual / Lelang |
| Filter Customer | Per customer |
| Filter Tanggal | Per tanggal |

### Tabel Barang

| Kolom | Keterangan |
|-------|------------|
| Nama Barang | Nama item |
| No Resi | Resi |
| Box | Box asal |
| Qty | Jumlah |
| Customer | Nama customer |
| Status | Badge |
| Aksi | Tandai Dijual / Tandai Lelang |

---

## LANGKAH 14 — Admin Update Estimasi (ETD/ETA)

**Siapa:** Admin
**Halaman:** /admin/est-update

Admin mengupdate estimasi keberangkatan dan kedatangan box.

### Sidebar Admin → "Pengaturan" → "Est Update"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box |
| Tabel | Daftar box dengan ETD/ETA saat ini |

### Form Update (klik salah satu box)

| Field | Tipe | Aturan |
|-------|------|--------|
| Box | Info | Read-only |
| ETD | Date | Opsional |
| ETA | Date | Opsional |
| Catatan | Textarea | Opsional |

### Setelah Simpan

- ETD/ETA diupdate
- Notifikasi ke customer
- Customer bisa lihat di Dashboard dan My Box

---

## LANGKAH 15 — Admin Generate Invoice

**Siapa:** Admin
**Halaman:** /admin/invoices

Admin membuat invoice setelah box sampai di Indonesia (status OTW_INA).

### Sidebar Admin → "Keuangan" → "Generate Invoice"

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice Number | Nomor invoice |
| Customer | Nama customer |
| Box | Box terkait |
| Berat (kg) | Berat |
| Fee TAX | Biaya tax |
| Fee WH | Warehouse fee |
| Fee Packing | Biaya packing |
| Grand Total | Total |
| Status | Badge |

### Form Generate Invoice (Modal — klik "Buat Invoice")

| Field | Tipe | Aturan |
|-------|------|--------|
| Pilih Box | Select | Wajib, box OTW_INA |
| Berat (kg) | Number | Wajib |
| Panjang (cm) | Number | Wajib |
| Lebar (cm) | Number | Wajib |
| Tinggi (cm) | Number | Wajib |
| Biaya Tambahan | Number | Opsional (default: 0) |

### Preview Invoice (otomatis saat isi field)

| Komponen | Rumus |
|----------|-------|
| Volume | (P × L × T) / 6 |
| Dasar | MAX(berat, volume) |
| Fee TAX | Dasar × Rate |
| Fee WH | Tiered berdasarkan berat |
| Fee Packing | Tiered |
| Denda Total | Denda pending customer |
| Grand Total | Semua fee + denda |

### Setelah Generate

- Invoice dibuat dengan status: **waiting_payment**
- payment_deadline otomatis: invoice_date + 7 hari
- Notifikasi ke customer: "Invoice [nomor] sudah tersedia. Total: Rp X"

---

## LANGKAH 16 — Customer Membayar Invoice

**Siapa:** Customer
**Halaman:** /invoice lalu /invoice/{id}/pay

Customer melihat invoice dan membayar.

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
| Weight | Berat |
| Volume | Volume |
| Fee TAX | Biaya tax |
| Fee WH | Warehouse fee |
| Fee Packing | Biaya packing |
| Grand Total | Total |
| Status | Badge |

### Tombol

| Tombol | Muncul Saat | Fungsi |
|--------|-------------|--------|
| Bayar | Status waiting_payment | Ke halaman bayar |

### Form Bayar Invoice (/invoice/{id}/pay)

| Field | Tipe | Aturan |
|-------|------|--------|
| Metode Pembayaran | Radio | Wajib: Transfer / QRIS |
| Bukti Transfer | File | Wajib, JPG/PNG, max 5MB |

### Setelah Submit

- Status: waiting_payment → **waiting_verification**
- Pesan: "Bukti transfer berhasil dikirim. Menunggu verifikasi admin."

---

## LANGKAH 17 — Customer Buat Invoice Fleksibel (Shopee-style)

**Siapa:** Customer
**Halaman:** /create-invoice

Customer bisa membuat invoice sendiri dengan memilih barang mana saja.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Daftar Barang | Checklist barang yang sudah arrived & belum ada di invoice |
| Tombol "Pilih Semua" | Centang/hapus semua |
| Dimensi | Panjang, Lebar, Tinggi (cm) |
| Preview | Perhitungan otomatis |

### Form

| Field | Tipe | Aturan |
|-------|------|--------|
| Checkbox per barang | Checkbox | Min 1 barang |
| Panjang (cm) | Number | Wajib |
| Lebar (cm) | Number | Wajib |
| Tinggi (cm) | Number | Wajib |

### Setelah Submit

- Invoice dibuat dari barang yang dipilih
- Hitungan biaya otomatis

---

## LANGKAH 18 — Admin Verifikasi Pembayaran

**Siapa:** Admin
**Halaman:** /admin/verification

Admin memverifikasi bukti transfer customer.

### Sidebar Admin → "Keuangan" → "Verifikasi"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari invoice/customer |
| Filter | Status (default: waiting_verification) |
| Tabel | Daftar invoice menunggu verifikasi |

### Detail Invoice (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Invoice | Nomor, customer, box, total |
| Bukti Transfer | Gambar bukti |
| Metode | Transfer / QRIS |

### Tombol

| Tombol | Fungsi | Konfirmasi |
|--------|--------|------------|
| Verifikasi (hijau) | Setujui | "Verifikasi pembayaran ini?" |
| Tolak (merah) | Tolak | Modal: isi alasan penolakan |

### Setelah Verifikasi

- Status: waiting_verification → **verified**
- Denda yang tagged → paid
- Notifikasi ke customer: "Pembayaran Anda diverifikasi"

### Setelah Penolakan

- Status: waiting_verification → **waiting_payment**
- Notifikasi ke customer: "Pembayaran ditolak. Alasan: [alasan]"

---

## LANGKAH 19 — Customer Request Checkout

**Siapa:** Customer
**Halaman:** /checkout

Customer meminta pengiriman barang setelah invoice terverifikasi.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Invoice Number, Status |
| Daftar Checkout | List checkout + status |

### Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning | Menunggu proses admin |
| on_process | Biru | Sedang diproses |
| sent | Hijau | Sudah dikirim |

### Tombol "Request Checkout"

Muncul jika ada invoice verified. Buka modal:

### Form Checkout

| Field | Tipe | Aturan |
|-------|------|--------|
| Invoice | Select | Wajib, invoice verified |
| Tipe Alamat | Radio | Wajib: Personal / Dropship |
| Nama Penerima | Text | Wajib, min 3 karakter |
| No Telepon Penerima | Tel | Wajib, min 10 digit |
| Alamat Lengkap | Textarea | Wajib, min 10 karakter |
| Nama Pengirim | Text | Wajib jika dropship |
| No Telepon Pengirim | Tel | Wajib jika dropship |
| Konfirmasi | Checkbox | Wajib |

### Setelah Submit

- Status: request
- Pesan: "Request checkout berhasil dikirim."

---

## LANGKAH 20 — Admin Proses Checkout

**Siapa:** Admin
**Halaman:** /admin/checkouts

Admin memproses request checkout dari customer.

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
| Alamat | Tipe, Nama, No Telp, Alamat |
| Pengirim | Nama + No Telp (jika dropship) |

### Tombol "Proses Checkout"

| Field | Tipe | Aturan |
|-------|------|--------|
| Foto Packing | File | Wajib |
| Nomor Resi/Tracking | Text | Wajib |

### Setelah Proses

- Status: request → on_process → **sent**
- Notifikasi ke customer: "Barang Anda sedang diproses. Tracking: [nomor]"

---

## LANGKAH 21 — Customer Ajukan Komplain

**Siapa:** Customer
**Halaman:** /komplain

Customer mengajukan komplain jika ada masalah dengan barang.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Tombol "Ajukan Komplain" | Buka modal form |
| Filter | Status |
| Daftar Komplain | List + status badge |

### Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Baru diajukan |
| in_review | Biru | Sedang ditinjau |
| processing | Biru | Sedang diproses |
| resolved | Hijau | Selesai |

### Form Komplain

| Field | Tipe | Aturan |
|-------|------|--------|
| Jenis Komplain | Select | Wajib: Kurang Barang Ekspedisi, Tidak Arrived China/Indonesia, Kurang China/Indonesia |
| Resolusi | Radio | Wajib: Refund / Penggantian |
| No Invoice | Text | Opsional |
| No Resi | Text | Opsional |
| Deskripsi | Textarea | Wajib, min 10 karakter |
| Video | File | Opsional, MP4/MOV, max 50MB |
| Foto | File | Opsional, JPG/PNG, max 5MB |

### Setelah Submit

- Status: open
- Notifikasi ke admin
- Pesan: "Komplain berhasil diajukan."

---

## LANGKAH 22 — Admin Tangani Komplain

**Siapa:** Admin
**Halaman:** /admin/complains

Admin menangani komplain customer.

### Sidebar Admin → "Customer" → "Komplain"

### Detail Komplain (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Jenis | Tipe masalah |
| Resolusi | Refund / Penggantian |
| Deskripsi | Detail |
| Video | Bukti video |
| Foto | Bukti foto |

### Tombol Update Status

| Status Saat | Tombol | Status Baru |
|-------------|--------|-------------|
| open | "Tinjau" | in_review |
| in_review | "Proses" | processing |
| processing | "Selesai" | resolved |

Setiap perubahan status → notifikasi ke customer.

---

## LANGKAH 23 — Admin Kelola Rate & Kurs

**Siapa:** Admin
**Halaman:** /admin/settings dan /admin/kurs-history

### Pengaturan Rate (/admin/settings)

**Sidebar Admin → "Pengaturan" → "Pengaturan Rate"**

3 tab: Sharing, Direct, Packing.

**Tab Sharing:**

| Field | Keterangan |
|-------|------------|
| Rate Sharing Air Berat | Rp/kg |
| Rate Sharing Air Volume | Rp/kg |
| Rate Sharing Sea Berat | Rp/kg |
| Rate Sharing Sea Volume | Rp/kg |
| Rate Sharing Sensitive Air Berat | Rp/kg |
| Rate Sharing Sensitive Air Volume | Rp/kg |
| Rate Sharing Sensitive Sea Berat | Rp/kg |
| Rate Sharing Sensitive Sea Volume | Rp/kg |

**Tab Direct:**

| Field | Keterangan |
|-------|------------|
| Rate Direct Air Berat | Rp/kg |
| Rate Direct Air Volume | Rp/kg |
| Rate Direct Sea Berat | Rp/kg |
| Rate Direct Sea Volume | Rp/kg |

**Tab Packing:**

| Field | Keterangan |
|-------|------------|
| Fee Packing ≤150 gram | Rp |
| Fee Packing ≤1000 gram | Rp |
| Fee Packing ≤2000 gram | Rp |
| Fee Packing Extra per kg | Rp |

### History Kurs (/admin/kurs-history)

**Sidebar Admin → "Pengaturan" → "History Kurs"**

| Komponen | Keterangan |
|----------|------------|
| Tombol "Input Kurs Baru" | Buka form |
| Tabel History | Daftar kurs + tanggal |

**Tabel:**

| Kolom | Keterangan |
|-------|------------|
| Kurs | Nilai (contoh: 2660) |
| Tanggal Berlaku | Tanggal |
| Diinput Oleh | Nama admin/owner |
| Tanggal Input | Kapan diinput |

**Form Input Kurs:**

| Field | Tipe | Aturan |
|-------|------|--------|
| Nilai Kurs | Number | Wajib |
| Tanggal Berlaku | Date | Wajib, tidak boleh masa depan |

---

## LANGKAH 24 — Customer Kalkulator Biaya

**Siapa:** Customer
**Halaman:** /kalkulator

Customer menghitung estimasi biaya pengiriman.

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
| Hitung | Hitung estimasi |
| Reset | Kosongkan form |

### Hasil

| Komponen | Rumus |
|----------|-------|
| Volume | (P × L × T) / 6 |
| Dasar | MAX(berat, volume) |
| Fee TAX | Dasar × Rate |
| Fee WH | Tiered |
| Fee Packing | Tiered |
| Grand Total | Fee TAX + Fee WH + Fee Packing |

---

## LANGKAH 25 — Owner Dashboard

**Siapa:** Owner
**Halaman:** /owner/dashboard

Owner login → sidebar "Owner" → "Owner Dashboard".

### Stat Cards

| Kartu | Informasi |
|-------|-----------|
| Revenue Bulan Ini | Total + growth % |
| Customer Aktif | Jumlah + baru bulan ini |
| Box Aktif | Jumlah + selesai bulan ini |
| Invoice Pending | Belum bayar + belum verif |

### Grafik Revenue

Grafik batang revenue per bulan (6 bulan terakhir).

### Top Customer

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Total Transaksi | Jumlah invoice |
| Total Revenue | Nominal |

### Recent Invoices

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor |
| Customer | Nama |
| Total | Nominal |
| Status | Badge |

### Recent Activity

Log aktivitas terbaru (audit trail).

---

## LANGKAH 26 — Owner Laporan Keuangan

**Siapa:** Owner
**Halaman:** /owner/finance

### Sidebar Owner → "Owner" → "Laporan Keuangan"

### Summary Cards

| Kartu | Informasi |
|-------|-----------|
| Total Revenue | Semua revenue |
| Outstanding | Belum dibayar |
| Profit | Revenue - Outstanding |
| Cash In | Total masuk |
| Cash Out | Total keluar |
| Total Invoice | Jumlah invoice |

### Filter

| Filter | Keterangan |
|--------|------------|
| Tanggal Dari/Sampai | Range tanggal |
| Bulan | Per bulan |
| Tahun | Per tahun |
| Customer | Per customer |
| Status | Per status |
| Search | Cari |

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor |
| Customer | Nama |
| Box | Box |
| Grand Total | Total |
| Status | Badge |
| Tanggal | Tanggal |

### Export

| Tombol | Format |
|--------|--------|
| Export CSV | .csv |
| Export Excel | .xlsx |

---

## LANGKAH 27 — Owner Manage Admin

**Siapa:** Owner
**Halaman:** /owner/manage-admin

### Sidebar Owner → "Manajemen" → "Manage Admin"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email |
| Filter Status | Active / Inactive / Pending |
| Tabel | Daftar admin |

### Detail Admin (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Lengkap | Nama, email, telepon, KTP, alamat |
| Status | Badge |
| Activity History | Log aktivitas |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Aktivasi | Aktifkan admin |
| Nonaktifkan | Nonaktifkan admin |

---

## LANGKAH 28 — Owner Manage Users

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

### Tabel

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama user |
| Email | Email |
| Role | Badge role |
| Status | Badge status |
| Terdaftar | Tanggal |

### Modal Ubah Role

| Field | Tipe |
|-------|------|
| Role Baru | Select: Customer / Admin / Owner |

---

## LANGKAH 29 — Owner All Data

**Siapa:** Owner
**Halaman:** /owner/data

### Sidebar Owner → "Manajemen" → "All Data"

### Tab

| Tab | Isi |
|-----|-----|
| Customers | Semua customer |
| Boxes | Semua box |
| Invoices | Semua invoice |
| Items | Semua barang |
| Checkouts | Semua checkout |
| Complains | Semua komplain |

### Komponen per Tab

| Komponen | Keterangan |
|----------|------------|
| Search | Cari data |
| Tabel | Daftar data |
| Pagination | Navigasi halaman |

---

## LANGKAH 30 — Owner Audit Log

**Siapa:** Owner
**Halaman:** /owner/audit-log

### Sidebar Owner → "Manajemen" → "Audit Log"

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari aktivitas/user |
| Filter Event | Jenis event |
| Tabel | Daftar log |

### Tabel

| Kolom | Keterangan |
|-------|------------|
| User | Siapa |
| Event | Jenis (created, updated, deleted) |
| Subject | Model (Box, Invoice, Item) |
| Perubahan | Detail old → new |
| Waktu | Kapan |

---

## LANGKAH 31 — Notifikasi & Profile

### Notifikasi (/notifications)

**Akses:** Semua role (sidebar → bell icon)

| Komponen | Keterangan |
|----------|------------|
| Daftar | Semua notifikasi |
| Tombol "Tandai Semua Dibaca" | Mark all as read |
| Badge unread | Jumlah belum dibaca |

### Profile (/profile)

**Akses:** Semua role (sidebar → avatar → Profil Saya)

| Komponen | Keterangan |
|----------|------------|
| Update Profile | Ubah nama, email |
| Update Password | Password lama + baru |
| Hapus Akun | Hapus permanen (konfirmasi password) |

---

## LANGKAH 32 — Admin Kelola Customer (Lanjutan)

**Siapa:** Admin
**Halaman:** /admin/customers

Selain aktivasi (Langkah 2), admin juga bisa:

### Tombol Aksi di Info Customer

| Tombol | Fungsi | Konfirmasi |
|--------|--------|------------|
| Nonaktifkan | Customer tidak bisa login | "Customer tidak bisa login setelah dinonaktifkan." |
| Aktivasi (kembali) | Aktifkan ulang | "Aktivasi customer ini?" |

---

# 3. Status — Referensi Cepat

## 3.1 Status Box

| Status | Badge | Arti |
|--------|-------|------|
| OPEN | Hijau "Terbuka" | Bisa setor resi |
| CLOSED | Abu-abu | Tidak bisa setor |
| SENT_TO_CARGO | Biru "Dikirim ke Cargo" | Dikirim dari China |
| OTW_INA | Kuning "Dalam Perjalanan" | Menuju Indonesia |
| UP_INVOICE | Biru "Invoice Dibuat" | Invoice digenerate |
| DONE | Hijau "Selesai" | Selesai |

## 3.2 Status Item

| Status | Badge | Arti | Bisa Diklaim? |
|--------|-------|------|---------------|
| active | - | Barang aktif | - |
| no_tuan | Oranye | Tidak ada pemilik | Ya (denda Rp 5.000) |
| claimed | Hijau "Diklaim" | Sudah diklaim | Tidak |
| klaim_wh | Merah "Klaim WH" | WH ambil untuk lelang | Tidak |
| shipped | Biru "Shipped" | Sudah dikirim | Tidak |
| hold | Abu-abu | Ditahan (deadline) | Tidak |

## 3.3 Status Invoice

| Status | Badge | Arti |
|--------|-------|------|
| waiting_payment | Kuning | Belum bayar |
| waiting_verification | Biru | Sudah bayar, belum verif |
| verified | Hijau | Terverifikasi |

## 3.4 Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning | Menunggu proses |
| on_process | Biru | Sedang diproses |
| sent | Hijau | Sudah dikirim |

## 3.5 Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Baru |
| in_review | Biru | Ditinjau |
| processing | Biru | Diproses |
| resolved | Hijau | Selesai |

## 3.6 Status Customer

| Status | Arti |
|--------|------|
| PENDING | Baru daftar, belum bisa login |
| ACTIVE | Sudah aktivasi, bisa login |
| INACTIVE | Dinonaktifkan |

## 3.7 Status Denda

| Status | Arti |
|--------|------|
| pending | Belum ditagih |
| tagged | Sudah masuk invoice |
| paid | Sudah dibayar |

---

# 4. Pesan Error & Sukses

## 4.1 Pesan Error

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
| Barang diklaim | "Barang sudah diklaim oleh customer lain." |

## 4.2 Pesan Sukses

| Aksi | Pesan |
|------|-------|
| Register | "Registrasi berhasil! Menunggu aktivasi dari admin." |
| Login | "Selamat datang, [nama]!" |
| Aktivasi | "Akun [nama] berhasil diaktivasi." |
| Setor Resi | "Barang berhasil didaftarkan." |
| Bayar Invoice | "Bukti transfer berhasil dikirim." |
| Verifikasi | "Pembayaran berhasil diverifikasi." |
| Checkout | "Request checkout berhasil dikirim." |
| Komplain | "Komplain berhasil diajukan." |
| Generate Invoice | "Invoice [nomor] berhasil dibuat." |
| Klaim No Tuan | "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan." |
| Input No Tuan | "Barang berhasil ditambahkan sebagai No Tuan." |
| Update Rate | "Rate berhasil diupdate." |
| Input Kurs | "Kurs berhasil diupdate." |
| Close Box | "Box berhasil ditutup." |

---

# END OF USER GUIDE

Dokumen ini mencakup SELURUH 38 halaman, 15+ modul, dan semua fitur di Ting Warehouse Management System v2.1.

Terakhir diperbarui: Juli 2026
