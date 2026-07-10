# USER GUIDE
# Ting Warehouse Management System
# Versi 2.1 — Juli 2026

---

# Daftar Isi

1. Tentang Sistem ini
2. Role Pengguna
3. Halaman Publik (Login, Register, Lupa Password)
4. CUSTOMER — Lengkap (Dari Daftar hingga Selesai)
5. ADMIN — Lengkap (Semua Halaman & Fitur)
6. OWNER — Lengkap (Semua Halaman & Fitur)
7. Status Barang & Box — Referensi Cepat
8. Pesan Error & Sukses

---

# 1. Tentang Sistem ini

Ting Warehouse Management System adalah website operasional untuk perusahaan freight forwarding China → Jakarta. Sistem ini menggantikan Airtable dan Google Sheets yang sebelumnya digunakan.

| Informasi | Detail |
|-----------|--------|
| Website | www.tingwarehouse.my.id |
| Bahasa | Indonesia |
| Browser | Chrome, Safari, Firefox (terbaru) |
| Perangkat | Laptop, HP Android, iPhone |

### Tiga Role Pengguna

| Role | Siapa | Jumlah |
|------|-------|--------|
| Customer | Pengguna jasa forwarding (pedagang, reseller, dropshipper) | 50-200 orang |
| Admin | Staff operasional di kantor/gudang | 3 orang |
| Owner | Pemilik bisnis (Ahmad Ting) | 1 orang |

---

# 2. Halaman Publik

Halaman ini bisa diakses tanpa login.

---

## 2.1 Halaman Utama (/)

Halaman pertama saat membuka website. Menampilkan informasi tentang layanan Ting Warehouse.

| Komponen | Isi |
|----------|-----|
| Header | Logo "Ting Warehouse" + tombol Login/Register |
| Navigasi | Link ke Login dan Register |

---

## 2.2 Login (/login)

Halaman untuk masuk ke sistem.

### Form Login

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Email | Email | Ya | Alamat email yang terdaftar |
| Password | Password | Ya | Password akun |
| Remember Me | Checkbox | Tidak | Tetap login meski browser ditutup |

### Tombol yang Tersedia

| Tombol | Fungsi |
|--------|--------|
| Masuk | Login ke sistem |
| Lupa Password? | Ke halaman reset password |
| Belum punya akun? Daftar | Ke halaman register |

### Setelah Login

Sistem akan mengarahkan ke halaman dashboard sesuai role:

| Role | Halaman Tujuan |
|------|---------------|
| Customer | /dashboard |
| Admin | /admin/dashboard |
| Owner | /owner/dashboard |

### Kondisi Error

| Kondisi | Pesan |
|---------|-------|
| Email atau password salah | "Email atau password salah" |
| Akun belum aktif (PENDING) | "Akun belum aktif. Hubungi admin." |
| Akun dinonaktifkan (INACTIVE) | "Akun telah dinonaktifkan." |
| Gagal 5x berturut-turut | "Akun terkunci. Coba lagi dalam 15 menit." |

---

## 2.3 Register (/register)

Halaman untuk mendaftar sebagai customer baru.

### Form Register

| Field | Tipe | Placeholder | Required | Validasi |
|-------|------|-------------|----------|----------|
| Nama | Text | "Nama lengkap" | Ya | Min 3 karakter |
| Email | Email | "email@contoh.com" | Ya | Format email valid, unik |
| No Telepon | Text | "08123456789" | Ya | Min 10 digit, angka saja |
| No KTP | Text | "16 digit No KTP" | Ya | Tepat 16 digit, angka saja, unik |
| Alamat | Textarea | "Alamat lengkap" | Ya | Min 10 karakter |
| Password | Password | "Min 8 karakter" | Ya | Min 8 karakter |
| Konfirmasi Password | Password | "Ulangi password" | Ya | Harus sama dengan password |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Daftar | Kirim registrasi |
| Sudah punya akun? Masuk | Kembali ke halaman login |

### Setelah Registrasi

1. Akun berstatus PENDING
2. Muncul pesan: "Registrasi berhasil! Menunggu aktivasi dari admin."
3. Customer belum bisa login sampai admin mengaktifkan akunnya

---

## 2.4 Lupa Password (/forgot-password)

| Field | Tipe | Required |
|-------|------|----------|
| Email | Email | Ya |

| Tombol | Fungsi |
|--------|--------|
| Kirim Link Reset | Mengirim email reset password |

### Setelah Submit

- Link reset dikirim ke email
- Link berlaku 60 menit
- Buka link → isi password baru → login

---

## 2.5 Reset Password (/reset-password/{token})

| Field | Tipe | Required |
|-------|------|----------|
| Email | Email | Ya |
| Password | Password | Ya (min 8 karakter) |
| Confirm Password | Password | Ya (harus sama) |

| Tombol | Fungsi |
|--------|--------|
| Reset Password | Simpan password baru |

---

# 3. CUSTOMER — Lengkap

## Alur Customer dari Awal hingga Akhir

```
Register (PENDING) → Admin Aktivasi → Login → Dashboard
→ Setor Resi → My Box → Tunggu Invoice → Bayar Invoice
→ Upload Bukti → Admin Verifikasi → Checkout → Isi Alamat
→ Admin Packing + Kirim → Terima Barang
→ (Komplain jika ada masalah)
```

---

## 3.1 Dashboard Customer (/dashboard)

Halaman utama customer setelah login. Menampilkan ringkasan aktivitas.

### Stat Cards (4 kartu di atas)

| Kartu | Informasi | Klik ke |
|-------|-----------|---------|
| Box Aktif | Jumlah box dengan status OPEN/SENT/OTW | /box/sharing |
| Invoice Belum Bayar | Jumlah + total nominal invoice yang belum dibayar | /invoice |
| Barang Bulan Ini | Jumlah barang yang diinput bulan ini | /setor-resi |
| Resi Bulan Ini | Jumlah resi yang diinput bulan ini | /setor-resi |

### Alert Banner (muncul jika ada data WH China yang belum dikenali)

| Komponen | Isi |
|----------|-----|
| Pesan | "Ada X resi dari gudang China yang belum dikenali" |
| Sub-pesan | "Klik di sini untuk melihat dan mengklaim resi yang mungkin milik Anda." |
| Klik ke | /unmatched-resi |

### Status Box (tabel daftar box)

| Kolom (Desktop) | Keterangan |
|-----------------|------------|
| Nomor Box | Tracking number atau "Box #ID" |
| Kode Box | Batch name |
| Jenis Kirim | AIR (biru) atau SEA (cyan) |
| ETD | Estimasi tanggal keberangkatan |
| ETA | Estimasi tanggal tiba |
| Status | Badge status box (Terbuka, Dikirim ke Cargo, dll) |
| Barang | Jumlah barang di box |

Klik salah satu box → muncul **Detail Box** modal yang menampilkan:
- Info box (Status, Tipe, Jenis Kirim, Kode Box)
- Tabel barang: No, Nama Barang, Qty, Berat, Volume, Keterangan

### Menu Cepat (7 shortcut)

| Menu | Klik ke | Ikon |
|------|---------|------|
| Setor Resi | /setor-resi | + |
| My Box | /box/sharing | Box |
| Invoice | /invoice | Dokumen |
| Checkout | /checkout | Truk |
| Komplain | /komplain | Peringatan |
| Kalkulator | /kalkulator | Kalkulator |
| No Tuan | /no-tuan | Arsip |

### Rate Hari Ini

| Informasi | Keterangan |
|-----------|------------|
| Kurs Yuan | Rp X (dari history kurs terbaru) |
| Rate Air | Rp X/kg (rate sharing air berat) |
| Rate Sea | Rp X/kg (rate sharing sea berat) |
| Tombol "Buka Kalkulator" | Ke halaman kalkulator |

### Notifikasi (5 terbaru)

| Komponen | Keterangan |
|----------|------------|
| Judul notifikasi | Contoh: "Invoice baru", "Pembayaran diverifikasi" |
| Pesan | Detail notifikasi |
| Waktu | "X menit yang lalu" |
| Badge "X baru" | Jumlah notifikasi belum dibaca |

### Tombol & FAB (Floating Action Button)

| Lokasi | Tombol | Fungsi |
|--------|--------|--------|
| Header (desktop) | "Setor Resi" | Ke /setor-resi |
| FAB (mobile) | Tombol hijau bulat di kanan bawah | Ke /setor-resi |

---

## 3.2 My Box Sharing (/box/sharing)

Halaman untuk melihat box sharing milik customer.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking Number, Tanggal, ETD, ETA, Status |
| Box List | Card per box dengan status badge |
| Box Detail | Expand/collapse: daftar barang di box |
| Barang Row | Nama, Qty, Harga, Foto Bukti, Status Arrived |

### Empty State

"Tidak ada barang di box sharing" + tombol "Setor Resi Sekarang"

---

## 3.3 My Box Direct (/box/direct)

Halaman untuk melihat box direct milik customer.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking, Batch, Status |
| Tombol "Request Direct Sharing" | Request box direct baru |
| Batch List | Card per batch dengan daftar barang |
| Tombol per batch | "Request to Close" |

---

## 3.4 Setor Resi (/setor-resi)

Halaman untuk mendaftarkan barang baru yang akan dikirim.

### Form Setor Resi

| Field | Tipe | Placeholder | Required | Validasi |
|-------|------|-------------|----------|----------|
| Box Tujuan | Select | "Pilih box..." | Ya | Hanya box dengan status OPEN |
| Nama Barang | Text | "Contoh: Baju kaos, Sepatu, dll" | Ya | Min 2 karakter |
| Jumlah | Number | - | Ya | Min 1, max 9999 |
| Harga (Yuan) | Number | "0.00" | Ya | Min 0.01, max 999999 |
| Nomor Resi | Text | "Nomor resi dari supplier China" | Ya | Min 3 karakter |
| Foto Bukti (Proof CO) | File | - | Ya | JPG/PNG/WebP, max 5MB |
| Barang Sensitive | Checkbox | - | Tidak | Centang jika barang kategori khusus |
| Jenis Sensitive | Select | "Pilih jenis..." | Jika sensitive | Elektronik, Baterai, Cairan, Kosmetik, Makanan, Obat-obatan, Magnet, Lainnya |

### Indikator Real-time WH China

Saat customer mengetik nomor resi, sistem otomatis mengecek apakah ada data dari gudang China dengan resi yang sama:

| Kondisi | Tampilan |
|---------|----------|
| Resi ditemukan di WH China | Box hijau: "Resi ditemukan di data gudang China!" + info Berat, Ukuran, Tanggal, Foto barang |
| Resi tidak ditemukan | Tidak ada indikator |
| Resi < 3 karakter | Tidak ada pengecekan |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Kembali ke dashboard |
| Daftarkan Barang | Submit form |

### Setelah Submit

- Pesan sukses: "Barang berhasil didaftarkan."
- Jika resi cocok dengan data WH China → otomatis terhubung (matched)
- Form direset untuk input barang berikutnya

### Error yang Mungkin Muncul

| Kondisi | Pesan |
|---------|-------|
| Resi duplikat di box yang sama | "Nomor resi sudah terdaftar di box ini" |
| Box sudah ditutup | "Box sudah ditutup. Tidak bisa menambah barang." |
| Format foto salah | "Format foto harus jpg, png, atau webp" |
| Foto terlalu besar | "Ukuran foto maksimal 5MB" |

---

## 3.5 Resi Belum Dikenali (/unmatched-resi)

Halaman untuk melihat data dari gudang China yang belum terhubung dengan data customer.

### Info Box

Pesan biru: "Jika Anda melihat nomor resi yang merupakan milik Anda, klik 'Klaim Resi' lalu isi data barang. Sistem akan otomatis menghubungkan data Anda dengan data gudang China."

### Daftar Resi (Grid Card)

| Informasi | Keterangan |
|-----------|------------|
| Nomor Resi | Dari data WH China |
| Tanggal Input | Kapan data dimasukkan admin |
| Berat | Dari data WH China (kg) |
| Ukuran Box | Dimensi dari WH China |
| Foto Barang | Foto dari WH China (jika ada) |
| Badge | "Belum Dikenali" (kuning) |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Resi Ini | Buka modal form klaim |

### Modal Form Klaim Resi

| Field | Tipe | Required | Validasi |
|-------|------|----------|----------|
| Data dari Gudang China | Info | - | Berat + Ukuran (read-only) |
| Pilih Box | Select | Ya | Hanya box OPEN milik customer |
| Nama Barang | Text | Ya | Min 2 karakter |
| Jumlah | Number | Ya | Min 1 |
| Harga (Yuan) | Number | Ya | Min 0.01 |
| Barang Sensitive | Checkbox | Tidak | - |
| Jenis Sensitive | Select | Jika sensitive | 8 opsi |
| Foto Bukti Barang (CO) | File | Ya | JPG/PNG/WebP, max 5MB |

### Tombol Modal

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Klaim Resi | Submit → buat barang + auto-match dengan WH China |

### Empty State

"Semua resi sudah dikenali" — tidak ada data WH China yang menunggu.

---

## 3.6 Invoice (/invoice)

Halaman untuk melihat dan membayar invoice.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Tracking, Box, Status |
| Tabel Invoice | Daftar invoice dengan kolom lengkap |

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice Number | Nomor invoice unik |
| Box | Box terkait |
| Weight | Berat barang (kg) |
| Volume | Volume barang |
| Fee TAX | Biaya berdasarkan berat/volume × rate |
| Fee WH | Warehouse fee (tiered) |
| Fee Packing | Biaya packing (tiered) |
| Grand Total | Total keseluruhan |
| Status | Badge status |

### Status Invoice

| Status | Badge | Arti |
|--------|-------|------|
| waiting_payment | Kuning | Menunggu pembayaran customer |
| waiting_verification | Biru | Sudah bayar, menunggu verifikasi admin |
| verified | Hijau | Pembayaran terverifikasi |

### Tombol

| Tombol | Muncul Saat | Fungsi |
|--------|-------------|--------|
| Bayar | Status waiting_payment | Buka halaman bayar |

---

## 3.7 Bayar Invoice (/invoice/{id}/pay)

Halaman untuk membayar invoice.

### Form Pembayaran

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Metode Pembayaran | Radio | Ya | Transfer / QRIS |
| Bukti Transfer | File | Ya | JPG/PNG, max 5MB |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Upload Bukti | Kirim bukti transfer |

### Setelah Submit

- Status invoice berubah: waiting_payment → waiting_verification
- Pesan: "Bukti transfer berhasil dikirim. Menunggu verifikasi admin."

---

## 3.8 Buat Invoice (/create-invoice)

Halaman untuk membuat invoice fleksibel (Shopee-style). Customer bisa memilih barang mana saja yang ingin dijadikan 1 invoice.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Daftar Barang | Checklist barang yang sudah arrived dan belum ada di invoice |
| Tombol "Pilih Semua" | Centang/hapus semua |
| Dimensi Input | Panjang (cm), Lebar (cm), Tinggi (cm) |
| Preview | Perhitungan otomatis (berat, volume, fee TAX, fee WH, fee packing, total) |

### Form

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Checkbox per barang | Checkbox | Min 1 barang | Pilih barang untuk invoice |
| Panjang (cm) | Number | Ya | Dimensi box |
| Lebar (cm) | Number | Ya | Dimensi box |
| Tinggi (cm) | Number | Ya | Dimensi box |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Buat Invoice | Submit → generate invoice dari barang yang dipilih |

---

## 3.9 Checkout (/checkout)

Halaman untuk meminta pengiriman barang setelah invoice terverifikasi.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Filter | Invoice Number, Status |
| Daftar Checkout | List checkout requests + status |

### Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning | Menunggu proses admin |
| on_process | Biru | Sedang diproses |
| sent | Hijau | Sudah dikirim |

### Tombol "Request Checkout"

Muncul jika ada invoice verified. Buka modal form checkout:

### Form Checkout

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Invoice | Select | Ya | Pilih invoice yang sudah verified |
| Tipe Alamat | Radio | Ya | Personal / Dropship |
| Nama Penerima | Text | Ya | Min 3 karakter |
| No Telepon Penerima | Tel | Ya | Min 10 digit |
| Alamat Lengkap | Textarea | Ya | Min 10 karakter |
| Nama Pengirim | Text | Ya (jika dropship) | Nama customer |
| No Telepon Pengirim | Tel | Ya (jika dropship) | No HP customer |
| Konfirmasi | Checkbox | Ya | Centang untuk konfirmasi |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Request Checkout | Submit |

### Setelah Submit

- Status checkout: request
- Pesan: "Request checkout berhasil dikirim."

---

## 3.10 Komplain (/komplain)

Halaman untuk mengajukan dan melacak komplain.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Tombol "Ajukan Komplain" | Buka modal form komplain |
| Filter | Status |
| Daftar Komplain | List komplain + status badge |

### Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Baru diajukan |
| in_review | Biru | Sedang ditinjau |
| processing | Biru | Sedang diproses |
| resolved | Hijau | Selesai |

### Form Komplain

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Jenis Komplain | Select | Ya | Kurang Barang Ekspedisi, Tidak Arrived China/Indonesia, Kurang China/Indonesia |
| Resolusi | Radio | Ya | Refund / Penggantian |
| No Invoice | Text | Tidak | No invoice terkait |
| No Resi | Text | Tidak | No resi terkait |
| Deskripsi | Textarea | Ya | Min 10 karakter, max 2000 |
| Video | File | Tidak | MP4/MOV, max 50MB |
| Foto | File | Tidak | JPG/PNG, max 5MB |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Ajukan Komplain | Submit |

### Setelah Submit

- Status: open
- Pesan: "Komplain berhasil diajukan."

---

## 3.11 Kalkulator (/kalkulator)

Halaman untuk menghitung estimasi biaya pengiriman.

### Form Kalkulator

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Metode | Radio | Ya | Air / Sea |
| Tipe | Radio | Ya | Sharing / Direct |
| Berat (kg) | Number | Ya | Berat aktual barang |
| Panjang (cm) | Number | Ya | Dimensi box |
| Lebar (cm) | Number | Ya | Dimensi box |
| Tinggi (cm) | Number | Ya | Dimensi box |
| Barang Sensitive | Checkbox | Tidak | Centang jika sensitive |

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

## 3.12 No Tuan (/no-tuan)

Halaman untuk melihat dan mengklaim barang yang tidak diklaim customer mana pun.

### Info Box

Pesan kuning: "Klaim barang dikenakan denda Rp 5.000 per barang. Denda ditagih bersamaan dengan pembayaran invoice berikutnya."

### Daftar Barang (Grid Card)

| Informasi | Keterangan |
|-----------|------------|
| Nama Barang | Nama barang |
| Nomor Resi | Resi barang (jika ada) |
| Jumlah | Qty barang |
| Harga | Harga dalam Yuan |
| Box | Box tempat barang berada |
| Badge "Sensitive" | Muncul jika barang sensitive |
| Badge "No Tuan" | Badge oranye |

### Tombol per Card

| Tombol | Fungsi |
|--------|--------|
| Klaim Barang | Buka modal klaim |

### Modal Klaim Barang

| Komponen | Keterangan |
|----------|------------|
| Info Barang | Nama, Resi, Qty (read-only) |
| Warning | "Klaim akan dikenakan denda Rp 5.000. Lanjutkan?" |
| Bukti Pembelian | File upload (JPG/PNG, max 5MB) — foto nota/resi beli |
| Keterangan | Textarea opsional (max 500 karakter) |

### Tombol Modal

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Klaim Barang | Submit |

### Setelah Klaim

- Status barang: no_tuan → claimed
- customer_id di-update ke customer yang klaim
- Denda Rp 5.000 dibuat di tabel denda_claims
- Notifikasi: "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan."
- Denda akan masuk ke invoice berikutnya

### Empty State

"Tidak ada barang No Tuan" — tidak ada barang yang bisa diklaim.

---

## 3.13 Profile (/profile)

Halaman untuk mengelola profil customer.

| Komponen | Keterangan |
|----------|------------|
| Update Profile | Ubah nama, email |
| Update Password | Ubah password (password lama + baru) |
| Hapus Akun | Hapus akun permanen (butuh konfirmasi password) |

---

## 3.14 Notifikasi (/notifications)

Halaman untuk melihat semua notifikasi.

| Komponen | Keterangan |
|----------|------------|
| Daftar Notifikasi | List semua notifikasi dengan badge unread/read |
| Tombol "Tandai Semua Dibaca" | Mark all as read |
| Klik notifikasi | Mark as read |

---

# 4. ADMIN — Lengkap

## Sidebar Admin (7 grup, 15 menu)

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

---

## 4.1 Dashboard Admin (/admin/dashboard)

Halaman utama admin. Menampilkan ringkasan operasional.

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

---

## 4.2 Manage Box (/admin/manage-boxes)

Halaman untuk mengelola semua box pengiriman.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box berdasarkan tracking/batch |
| Filter | Tipe (Sharing/Direct/Handcarry), Status, Customer, Tanggal |
| Tombol "Tambah Box" | Buka modal form buat box baru |

### Form Tambah Box (Modal)

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Tipe Box | Select | Ya | Sharing / Direct / Handcarry |
| Metode | Select | Ya | Air / Sea |
| Customer | Select | Tidak | Pilih customer atau "Tanpa Customer" |
| Tracking Number | Text | Tidak | Nomor tracking |
| Batch Name | Text | Tidak | Nama batch |
| Catatan | Textarea | Tidak | Catatan opsional |

### Detail Box (klik salah satu box)

| Komponen | Keterangan |
|----------|------------|
| Info Box | Status, Tipe, Jenis Kirim, Kode Box |
| Timeline Status | OPEN → SENT TO CARGO → OTW INA → UP INVOICE → DONE |
| Tabel Barang | Daftar barang di box dengan status per item |
| Tombol Status | Update status box (sesuai urutan) |
| Tombol Close/Open | Tutup/buka box untuk setor resi |
| Tombol "Tandai No Tuan" | Untuk item status active → no_tuan |
| Tombol "Klaim WH" | Untuk item status no_tuan → klaim_wh |

### Status Box & Aksi

| Status Saat | Tombol yang Muncul | Aksi |
|-------------|-------------------|------|
| OPEN | "Tutup Box", "Sent to Cargo" | Close box atau kirim ke cargo |
| CLOSED | "Buka Box", "Sent to Cargo" | Buka kembali atau kirim ke cargo |
| SENT_TO_CARGO | "OTW Indonesia" | Update ke dalam perjalanan |
| OTW_INA | "UP Invoice" | Generate invoice |
| UP_INVOICE | "DONE" | Selesai |

### Status Item di Box

| Status | Badge | Tombol Aksi |
|--------|-------|-------------|
| active | Tidak ada badge | "No Tuan" (tandai sebagai tidak diklaim) |
| no_tuan | Oranye "No Tuan" | "Klaim WH" (tandai untuk lelang) |
| claimed | Hijau "Diklaim" | - |
| klaim_wh | Merah "Klaim WH" | - |
| shipped | Biru "Shipped" | - |

---

## 4.3 Recap (/admin/recap)

Halaman untuk merekapitulasi barang masuk dari supplier China. Desain 2-panel.

### Summary Stats (6 kartu)

| Kartu | Informasi |
|-------|-----------|
| Box | Total box |
| Barang | Total item |
| WH China | Total data WH China |
| Matched | Data yang sudah cocok |
| Unmatched | Data yang belum cocok |
| Revenue | Total revenue |

### Tab "Customer"

Menampilkan data barang dari setor resi customer.

| Kolom | Keterangan |
|-------|------------|
| No Resi | Nomor resi barang |
| Nama Barang | Nama barang |
| Qty | Jumlah |
| Harga (¥) | Harga dalam Yuan |
| Box | Box tempat barang |
| Customer | Nama customer |
| Status | Matched (hijau) / Unmatched (kuning) |

### Tab "WH China"

Menampilkan data dari gudang China.

| Kolom | Keterangan |
|-------|------------|
| No Resi | Nomor resi dari WH China |
| Berat | Berat barang (kg) |
| Ukuran | Dimensi box |
| Biaya Jasa | Biaya jasa WH China (admin only) |
| Foto | Link foto barang |
| Match | Nama barang + customer (jika matched) |
| Status | Matched / Unmatched |
| Tanggal | Tanggal input |
| Aksi | Edit / Hapus |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Auto Match | Jalankan auto-matching untuk semua data unmatched |
| Input Data WH | Buka modal input data WH China |

### Filter

| Filter | Keterangan |
|--------|------------|
| Search | Cari resi, nama, customer |
| Tipe | Sharing / Direct / Handcarry |
| Metode | Air / Sea |
| Tanggal | Dari - Sampai |

### Form Input Data WH China (Modal)

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Nomor Resi | Text | Ya | Resi dari WH China |
| Berat (kg) | Number | Ya | Berat barang |
| Ukuran Box | Text | Ya | Dimensi (contoh: 40x30x25 cm) |
| Biaya Jasa | Number | Tidak | Tidak terlihat customer |
| Foto Barang | File | Tidak | Foto dari WH China |

---

## 4.4 Input Barang No Tuan (/admin/no-tuan/create)

Halaman untuk admin menginput barang yang tiba di warehouse tanpa ada yang setor resi.

### Info Box

Pesan kuning: "Barang yang tiba di warehouse tanpa ada customer yang setor resi. Barang akan otomatis tampil di halaman 'No Tuan' customer dan bisa diklaim dengan denda Rp 5.000."

### Form Input Barang

| Field | Tipe | Placeholder | Required | Validasi |
|-------|------|-------------|----------|----------|
| Nama Barang | Text | "Contoh: Sepatu Nike Air Max" | Ya | Min 2 karakter |
| Jumlah | Number | - | Ya | Min 1, max 9999 |
| Box | Select | "Pilih box tempat barang ini berada" | Ya | Hanya box OPEN/CLOSED |
| Deskripsi Barang | Textarea | "Deskripsi barang, ciri-ciri..." | Tidak | Max 1000 karakter |
| Foto Barang | File | - | Tidak | JPG/PNG, max 5MB |
| Catatan | Textarea | "Catatan tambahan untuk admin..." | Tidak | Max 500 karakter |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Kembali ke Manage Box |
| Input Barang No Tuan | Submit → barang langsung status no_tuan |

### Setelah Submit

- Barang dibuat dengan status: no_tuan
- customer_id: null (belum ada pemilik)
- resi_number: null (tidak ada yang setor)
- Barang otomatis muncul di halaman customer /no-tuan

---

## 4.5 Barang Lelang (/admin/lelang)

Halaman untuk melihat dan mengelola barang yang sudah di-klaim WH untuk dijual/dilelang.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama barang |
| Filter Status | Klaim WH / Dijual / Lelang |
| Filter Customer | Filter per customer |
| Filter Tanggal | Filter per tanggal |
| Summary | Total barang, total nilai |

### Tabel Barang

| Kolom | Keterangan |
|-------|------------|
| Nama Barang | Nama item |
| No Resi | Resi barang |
| Box | Box asal |
| Qty | Jumlah |
| Customer | Nama customer (jika ada) |
| Status | Badge status |
| Aksi | Tandai Dijual / Tandai Lelang |

### Aksi per Barang

| Aksi dari | Aksi ke | Tombol |
|-----------|---------|--------|
| klaim_wh | dijual | "Tandai Dijual" |
| klaim_wh | lelang | "Tandai Lelang" |
| dijual | lelang | "Tandai Lelang" |

---

## 4.6 Generate Invoice (/admin/invoices)

Halaman untuk membuat invoice.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari invoice |
| Filter Status | waiting_payment / waiting_verification / verified |
| Tombol "Buat Invoice" | Buka modal generate invoice |
| Tabel Invoice | Daftar semua invoice |

### Tabel Invoice

| Kolom | Keterangan |
|-------|------------|
| Invoice Number | Nomor invoice |
| Customer | Nama customer |
| Box | Box terkait |
| Berat (kg) | Berat barang |
| Fee TAX | Biaya tax |
| Fee WH | Warehouse fee |
| Fee Packing | Biaya packing |
| Grand Total | Total keseluruhan |
| Status | Badge status |

### Form Generate Invoice (Modal)

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Pilih Box | Select | Ya | Box dengan status OTW_INA |
| Berat (kg) | Number | Ya | Berat aktual barang |
| Panjang (cm) | Number | Ya | Dimensi |
| Lebar (cm) | Number | Ya | Dimensi |
| Tinggi (cm) | Number | Ya | Dimensi |
| Biaya Tambahan | Number | Tidak | Add on (default: 0) |

### Preview Invoice

Saat admin mengisi field, preview otomatis muncul menampilkan:
- Volume = (P × L × T) / 6
- Dasar = MAX(berat, volume)
- Fee TAX = Dasar × Rate
- Fee WH = Tiered
- Fee Packing = Tiered
- Denda Total = Total denda pending customer
- Grand Total = Semua fee + denda

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup modal |
| Generate Invoice | Submit → buat invoice + notifikasi ke customer |

### Detail Invoice (klik salah satu invoice)

Menampilkan rincian lengkap invoice.

---

## 4.7 Verifikasi (/admin/verification)

Halaman untuk memverifikasi pembayaran customer.

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
| Bukti Transfer | Gambar bukti transfer |
| Metode | Transfer / QRIS |

### Tombol

| Tombol | Fungsi | Konfirmasi |
|--------|--------|------------|
| Verifikasi (hijau) | Setujui pembayaran | "Verifikasi pembayaran ini?" |
| Tolak (merah) | Tolak pembayaran | Buka modal alasan penolakan |

### Setelah Verifikasi

- Status invoice: waiting_verification → verified
- Notifikasi ke customer: "Pembayaran Anda diverifikasi"
- Denda yang tagged → paid

### Setelah Penolakan

- Status invoice: waiting_verification → waiting_payment
- Notifikasi ke customer: "Pembayaran ditolak. Alasan: [alasan]"

---

## 4.8 Checkout (/admin/checkouts)

Halaman untuk memproses request checkout dari customer.

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
| Alamat | Tipe (Personal/Dropship), Nama, No Telp, Alamat |
| Pengirim | Nama + No Telp (jika dropship) |

### Tombol "Proses Checkout"

Buka modal:

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Foto Packing | File | Ya | Foto barang yang sudah dikemas |
| Nomor Resi/Tracking | Text | Ya | Nomor tracking pengiriman |

### Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning | Menunggu proses |
| on_process | Biru | Sedang diproses |
| sent | Hijau | Sudah dikirim |

---

## 4.9 Info Customer (/admin/customers)

Halaman untuk melihat dan mengelola data customer.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email/customer |
| Filter Status | Pending / Active / Inactive |
| Tabel | Daftar customer |

### Tabel Customer

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Email | Alamat email |
| No Telepon | Nomor HP |
| Status | Badge status (Pending/Active/Inactive) |
| Terdaftar | Tanggal registrasi |

### Detail Customer (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Lengkap | Nama, email, telepon, KTP, alamat |
| Status | Badge status |
| Aksi | Aktivasi / Nonaktifkan |

### Tombol Aksi

| Tombol | Muncul Saat | Fungsi | Konfirmasi |
|--------|-------------|--------|------------|
| Aktivasi | Status PENDING | Aktifkan akun customer | "Aktivasi customer ini?" |
| Nonaktifkan | Status ACTIVE | Nonaktifkan akun customer | "Customer tidak bisa login setelah dinonaktifkan." |

---

## 4.10 Komplain (/admin/complains)

Halaman untuk menangani komplain customer.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari customer/resi |
| Filter Status | open / in_review / processing / resolved |
| Tabel | Daftar komplain |

### Detail Komplain (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Jenis Komplain | Tipe masalah |
| Resolusi | Refund / Penggantian |
| Deskripsi | Detail komplain |
| Video | Video bukti (jika ada) |
| Foto | Foto bukti (jika ada) |
| Status | Badge status |

### Tombol Update Status

| Status Saat | Tombol | Status Baru |
|-------------|--------|-------------|
| open | "Tinjau" | in_review |
| in_review | "Proses" | processing |
| processing | "Selesai" | resolved |

---

## 4.11 Est Update (/admin/est-update)

Halaman untuk mengupdate estimasi keberangkatan dan kedatangan box.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari box |
| Tabel | Daftar box dengan ETD/ETA saat ini |

### Form Update (klik salah satu box)

| Field | Tipe | Required | Keterangan |
|-------|------|----------|------------|
| Box | Info | - | Nama box (read-only) |
| ETD | Date | Tidak | Estimasi tanggal keberangkatan |
| ETA | Date | Tidak | Estimasi tanggal tiba |
| Catatan | Textarea | Tidak | Catatan tambahan |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup form |
| Simpan | Update ETD/ETA + notifikasi ke customer |

---

## 4.12 Pengaturan Rate (/admin/settings)

Halaman untuk mengatur 17 parameter rate pengiriman.

### Tab

| Tab | Isi |
|-----|-----|
| Sharing | Rate sharing (air/sea, berat/volume) |
| Direct | Rate direct (air/sea, berat/volume) |
| Packing | Fee packing (tiered) |

### Form Rate Sharing

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

### Form Rate Direct

| Field | Keterangan |
|-------|------------|
| Rate Direct Air Berat | Rp/kg |
| Rate Direct Air Volume | Rp/kg |
| Rate Direct Sea Berat | Rp/kg |
| Rate Direct Sea Volume | Rp/kg |

### Form Fee Packing

| Field | Keterangan |
|-------|------------|
| Fee Packing ≤150 gram | Rp |
| Fee Packing ≤1000 gram | Rp |
| Fee Packing ≤2000 gram | Rp |
| Fee Packing Extra per kg | Rp |

### Tombol per Tab

| Tombol | Fungsi |
|--------|--------|
| Simpan | Update rate + konfirmasi |

---

## 4.13 History Kurs (/admin/kurs-history)

Halaman untuk mengelola kurs Yuan → Rupiah.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Tombol "Input Kurs Baru" | Buka form input |
| Tabel History | Daftar kurs dengan tanggal |

### Tabel History Kurs

| Kolom | Keterangan |
|-------|------------|
| Kurs | Nilai kurs (contoh: 2660) |
| Tanggal Berlaku | Tanggal kurs berlaku |
| Diinput Oleh | Nama admin/owner |
| Tanggal Input | Kapan diinput |

### Form Input Kurs

| Field | Tipe | Required | Validasi |
|-------|------|----------|----------|
| Nilai Kurs | Number | Ya | Harus angka |
| Tanggal Berlaku | Date | Ya | Tidak boleh di masa depan |

### Tombol

| Tombol | Fungsi |
|--------|--------|
| Batal | Tutup form |
| Simpan | Simpan kurs baru |

### Aturan

- Kurs terbaru otomatis jadi default untuk transaksi baru
- Invoice lama tetap pakai kurs saat invoice dibuat
- Tidak bisa input kurs dengan tanggal yang sama (unik)

---

# 5. OWNER — Lengkap

## Sidebar Owner (7 grup + 2 grup owner = 9 grup, 21 menu)

Owner memiliki akses ke SEMUA halaman admin + halaman owner khusus.

### Grup Owner

| Grup | Menu | URL |
|------|------|-----|
| Owner | Owner Dashboard | /owner/dashboard |
| Owner | Laporan Keuangan | /owner/finance |
| Manajemen | Manage Admin | /owner/manage-admin |
| Manajemen | Manage Users | /owner/users |
| Manajemen | All Data | /owner/data |
| Manajemen | Audit Log | /owner/audit-log |

---

## 5.1 Owner Dashboard (/owner/dashboard)

Halaman utama owner. Menampilkan statistik bisnis.

### Stat Cards

| Kartu | Informasi |
|-------|-----------|
| Revenue Bulan Ini | Total revenue bulan ini + growth % |
| Customer Aktif | Jumlah customer aktif + baru bulan ini |
| Box Aktif | Jumlah box aktif + selesai bulan ini |
| Invoice Pending | Invoice menunggu pembayaran + verifikasi |

### Grafik Revenue

Grafik batang revenue per bulan (6 bulan terakhir).

### Top Customer

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Total Transaksi | Jumlah invoice |
| Total Revenue | Nominal total |

### Recent Invoices

| Kolom | Keterangan |
|-------|------------|
| Invoice | Nomor invoice |
| Customer | Nama customer |
| Total | Nominal |
| Status | Badge status |

### Recent Activity

| Komponen | Keterangan |
|----------|------------|
| Aktivitas | Log aktivitas terbaru (audit trail) |
| Waktu | Kapan terjadi |

### Notifikasi

| Komponen | Keterangan |
|----------|------------|
| Deadline | Invoice mendekati deadline |
| Komplain Baru | Komplain baru masuk |

---

## 5.2 Laporan Keuangan (/owner/finance)

Halaman untuk melihat laporan keuangan lengkap.

### Summary Cards

| Kartu | Informasi |
|-------|-----------|
| Total Revenue | Total semua revenue |
| Outstanding | Total belum dibayar |
| Profit | Revenue - Outstanding |
| Cash In | Total masuk |
| Cash Out | Total keluar |
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

## 5.3 Manage Admin (/owner/manage-admin)

Halaman untuk mengelola akun admin.

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

## 5.4 Manage Users (/owner/users)

Halaman untuk mengelola semua pengguna (customer, admin, owner).

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari nama/email |
| Filter Role | Customer / Admin / Owner |
| Filter Status | Active / Inactive / Pending |
| Tabel | Daftar semua user |

### Tabel Users

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama user |
| Email | Alamat email |
| Role | Badge role |
| Status | Badge status |
| Terdaftar | Tanggal registrasi |

### Detail User (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| Info Lengkap | Semua data user |
| Ubah Role | Ganti role user |

### Modal Ubah Role

| Field | Tipe | Keterangan |
|-------|------|------------|
| Role Baru | Select | Customer / Admin / Owner |

---

## 5.5 All Data (/owner/data)

Halaman untuk melihat semua data dalam satu tempat.

### Tab

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

## 5.6 Audit Log (/owner/audit-log)

Halaman untuk melihat log semua aktivitas di sistem.

### Komponen

| Komponen | Keterangan |
|----------|------------|
| Search | Cari aktivitas/user |
| Filter Event | Filter berdasarkan jenis event |
| Tabel | Daftar log aktivitas |

### Tabel Audit Log

| Kolom | Keterangan |
|-------|------------|
| User | Siapa yang melakukan |
| Event | Jenis aktivitas (created, updated, deleted, custom) |
| Subject | Model yang diubah (Box, Invoice, Item, dll) |
| Perubahan | Detail perubahan (old → new values) |
| Waktu | Kapan terjadi |

### Detail Log (klik salah satu)

| Komponen | Keterangan |
|----------|------------|
| User Info | Siapa yang melakukan |
| Event Detail | Jenis + deskripsi |
| Old Values | Data sebelum diubah |
| New Values | Data setelah diubah |

---

# 6. Status Barang & Box — Referensi Cepat

## 6.1 Status Box

| Status | Badge | Arti | Urutan |
|--------|-------|------|--------|
| OPEN | Hijau "Terbuka" | Box dibuka, bisa setor resi | 1 |
| CLOSED | Abu-abu | Box ditutup, tidak bisa setor | (bisa kapan saja) |
| SENT_TO_CARGO | Biru "Dikirim ke Cargo" | Sudah dikirim dari China | 2 |
| OTW_INA | Kuning "Dalam Perjalanan" | Dalam perjalanan ke Indonesia | 3 |
| UP_INVOICE | Biru "Invoice Dibuat" | Invoice sudah digenerate | 4 |
| DONE | Hijau "Selesai" | Proses selesai | 5 |

## 6.2 Status Item

| Status | Badge | Arti | Bisa Diklaim? |
|--------|-------|------|---------------|
| active | - | Barang aktif, menunggu diambil | - |
| no_tuan | Oranye "No Tuan" | Tidak ada pemilik | Ya (denda Rp 5.000) |
| claimed | Hijau "Diklaim" | Sudah diklaim customer | Tidak |
| klaim_wh | Merah "Klaim WH" | WH ambil untuk jual/lelang | Tidak |
| shipped | Biru "Shipped" | Sudah dikirim | Tidak |
| hold | Abu-abu | Ditahan (deadline lewat) | Tidak |
| dijual | - | Dijual WH | Tidak |
| lelang | - | Dilelang WH | Tidak |

## 6.3 Status Invoice

| Status | Badge | Arti |
|--------|-------|------|
| waiting_payment | Kuning "Menunggu Pembayaran" | Customer belum bayar |
| waiting_verification | Biru "Menunggu Verifikasi" | Sudah bayar, admin belum verif |
| verified | Hijau "Terverifikasi" | Pembayaran terverifikasi |

## 6.4 Status Checkout

| Status | Badge | Arti |
|--------|-------|------|
| request | Kuning "Menunggu Proses" | Customer request checkout |
| on_process | Biru "Sedang Diproses" | Admin sedang proses |
| sent | Hijau "Terkirim" | Barang sudah dikirim |

## 6.5 Status Komplain

| Status | Badge | Arti |
|--------|-------|------|
| open | Kuning | Komplain baru |
| in_review | Biru | Sedang ditinjau |
| processing | Biru | Sedang diproses |
| resolved | Hijau | Selesai |

## 6.6 Status Customer

| Status | Arti |
|--------|------|
| PENDING | Baru daftar, belum bisa login |
| ACTIVE | Sudah diaktivasi, bisa login |
| INACTIVE | Dinonaktifkan, tidak bisa login |

## 6.7 Status Denda

| Status | Arti |
|--------|------|
| pending | Denda belum ditagih |
| tagged | Denda sudah masuk ke invoice |
| paid | Denda sudah dibayar |

---

# 7. Pesan Error & Sukses

## 7.1 Pesan Error

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
| File terlalu besar | "Ukuran file maksimal 5MB" |
| Invoice sudah dibayar | "Invoice sudah dibayar" |
| Checkout belum verif | "Invoice belum terverifikasi" |
| Barang sudah diklaim | "Barang sudah diklaim oleh customer lain." |
| Box tidak ditemukan | "Box tidak ditemukan" |

## 7.2 Pesan Sukses

| Aksi | Pesan |
|------|-------|
| Register | "Registrasi berhasil! Menunggu aktivasi dari admin." |
| Login | "Selamat datang, [nama]!" |
| Setor Resi | "Barang berhasil didaftarkan." |
| Bayar Invoice | "Bukti transfer berhasil dikirim. Menunggu verifikasi admin." |
| Checkout | "Request checkout berhasil dikirim." |
| Komplain | "Komplain berhasil diajukan." |
| Aktivasi | "Akun [nama] berhasil diaktivasi." |
| Generate Invoice | "Invoice [nomor] berhasil dibuat." |
| Verifikasi | "Pembayaran berhasil diverifikasi." |
| Klaim No Tuan | "Barang berhasil diklaim. Denda Rp 5.000 ditambahkan." |
| Input No Tuan | "Barang '[nama]' berhasil ditambahkan sebagai No Tuan." |
| Update Rate | "Rate berhasil diupdate." |
| Input Kurs | "Kurs berhasil diupdate." |
| Close Box | "Box berhasil ditutup." |

---

# END OF USER GUIDE

Dokumen ini mencakup SELURUH fitur, halaman, dan modul yang ada di Ting Warehouse Management System v2.1.

Terakhir diperbarui: Juli 2026
