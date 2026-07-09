# Ting Warehouse Management System — Project Context

## 0. Cara Pakai File Ini
Ini adalah source of truth operasional untuk Claude Code di project ini. PRD lengkap ada di `docs/PRD.md` — **SELALU baca section yang relevan sebelum implementasi apapun, jangan menebak atau mengasumsikan.** File ini berisi ringkasan yang sering dipakai + aturan keras yang tidak boleh dilanggar, tapi PRD tetap otoritas tertinggi kalau ada perbedaan.

**Mindset project ini: production-grade, bukan MVP asal jalan.** Solo developer bukan alasan untuk menurunkan standar — justru karena tidak ada code reviewer lain, kualitas harus dijaga lewat disiplin: test, validasi ketat, error handling lengkap, dan tidak ada shortcut yang "nanti diperbaiki belakangan". Setiap kode yang ditulis harus asumsinya langsung dipakai customer sungguhan yang uangnya dipertaruhkan.

---

## 1. Domain Business — Wajib Dipahami Sebelum Ngoding

Ting Warehouse adalah freight forwarding China→Jakarta. Istilah domain yang HARUS dipahami dengan benar (salah paham di sini = salah di semua lapisan kode):

| Istilah | Arti |
|---------|------|
| **Sharing** | Barang dicampur dengan customer lain dalam satu box/container |
| **Direct** | Box khusus untuk satu customer saja, per batch |
| **Handcarry** | Barang dibawa langsung (tidak lewat cargo laut/udara) |
| **Dropship** | Customer kirim langsung ke pembeli akhir mereka, bukan ke diri sendiri |
| **Resi** | Nomor resi/tracking dari supplier China saat customer setor barang |
| **CO (Certificate of Origin) / proof_co** | Bukti foto barang saat disetor customer |
| **Box** | Unit pengiriman (bukan barang individual) — berisi banyak `items` |
| **Fee TAX** | Biaya berdasarkan berat/volume barang × rate |
| **Fee WH** | Warehouse fee, tiered berdasarkan berat |
| **Fee Packing** | Biaya packing, tiered (150/1000/2000 breakpoint + extra per kg) |
| **Sensitive item** | Barang kategori khusus (kena rate lebih mahal, wajib declare saat setor resi) |
| **ETD / ETA** | Estimated Time Departure / Arrival — diupdate admin, tampil ke customer |

**Alur uang (paling kritis, jangan sampai salah urutan status):**
```
Customer setor resi → Admin recap & assign ke box → Box status: OPEN
→ Box penuh/ditutup → SENT_TO_CARGO → OTW_INA (dalam perjalanan)
→ Admin timbang & ukur di Indonesia → UP_INVOICE (generate invoice, hitung fee)
→ Customer bayar → WAITING_VERIFICATION → Admin verifikasi → VERIFIED
→ Customer request checkout → Admin packing & kirim → DONE
```
Status TIDAK BOLEH melompat (misal OPEN langsung ke DONE). Setiap transisi status harus melalui middleware/service yang validasi urutan — jangan biarkan raw `update()` mengubah status sembarangan dari controller manapun.

---

## 2. Tech Stack (PRD §21)

| Layer | Teknologi | Catatan |
|-------|-----------|---------|
| Framework | Laravel 12 | |
| Frontend interaktif | Livewire v3 | Untuk semua form dinamis, real-time validation, upload progress |
| Template | Blade | |
| Styling | Tailwind CSS v4 | Utility-first, jangan tulis custom CSS kecuali benar-benar tidak bisa dihindari |
| JS ringan | Alpine.js | Dropdown, modal, toggle — bukan untuk business logic |
| Database | MySQL 8.0 | |
| Auth | Laravel Breeze (session-based) | |
| ORM | Eloquent | Parameter binding wajib, tidak ada raw query tanpa alasan kuat |

Perintah umum:
```bash
php artisan serve
npm run dev          # watch mode saat development
npm run build         # production build
php artisan test      # jalankan semua test
php artisan test --filter=NamaTest
php artisan migrate:fresh --seed   # reset DB + seed ulang
```

---

## 3. Arsitektur & Prinsip Wajib

### 3.1 Service Layer — Business Logic TIDAK BOLEH di Controller
Semua business logic (kalkulasi fee, status transition, validasi cross-entity) HARUS berada di service class di `app/Services/`, bukan di Controller atau Livewire component. Controller/Livewire hanya orchestration: terima input → panggil service → return response.

Alasan: fee calculation dipakai di ≥2 tempat (kalkulator customer §4.8, generate invoice admin §4.10). Kalau logic ada di controller, akan terjadi duplikasi dan risiko dua tempat itu menghasilkan angka berbeda — ini fatal untuk aplikasi yang menangani uang customer.

### 3.2 Satu Source of Truth untuk Fee Calculation
`FeeCalculationService` (atau nama serupa) adalah SATU-SATUNYA tempat rumus fee dihitung. Dilarang keras menulis ulang rumus `(P×L×T)/6` atau logic tiered pricing di tempat lain manapun, termasuk di Blade view atau Livewire component untuk "quick display".

### 3.3 Settings/Rate Selalu dari Database, Never Hardcode
17 parameter rate (PRD §4.12) diambil dari tabel `settings` setiap kali dipakai — jangan cache permanen tanpa invalidation, jangan hardcode di config file. Admin harus bisa update rate kapan saja dan efeknya langsung terasa di kalkulator customer maupun invoice baru (bukan invoice yang sudah dibuat — itu harus tetap pakai rate saat invoice dibuat, snapshot).

**Penting:** invoice yang sudah dibuat (`invoices` table) sudah punya `fee_tax`, `fee_wh`, `fee_packing` tersimpan sebagai nilai final — itu snapshot historis. Perubahan rate di masa depan TIDAK BOLEH mengubah invoice lama.

### 3.4 Role & Authorization
- 3 role: `owner`, `admin`, `customer` (PRD §3)
- Owner = superset dari Admin (semua akses admin + finance + user management)
- Authorization dicek di 2 layer: route middleware (block akses halaman) DAN policy/gate di dalam controller (block akses data spesifik — misal customer A tidak bisa lihat invoice customer B meski keduanya sama-sama role customer)
- Jangan pernah percaya `customer_id` dari request/form input untuk menentukan data ownership — selalu dari `auth()->id()`

### 3.5 File Upload
Semua file upload (proof_co, payment_proof, packing_photo, video/photo komplain) harus:
- Divalidasi tipe & ukuran sesuai PRD §12.5 SEBELUM disimpan
- Disimpan dengan nama file random/hashed, bukan nama asli (hindari collision & path traversal)
- Untuk V1 pakai local storage, tapi struktur kode harus pakai Laravel `Storage` facade (bukan akses filesystem langsung) supaya migrasi ke S3-compatible storage di V2 tidak perlu rewrite

---

## 4. Fee Calculation — Referensi Cepat (PRD §4.8, §4.12)

```
Volume (m³ equivalent) = (Panjang × Lebar × Tinggi) / 6
Dasar Perhitungan      = MAX(berat_aktual, volume)
Fee TAX                = Dasar × rate (pilih dari 12 varian berdasarkan: 
                          sharing/direct × air/sea × sensitive/non-sensitive)
Fee WH                 = tiered berdasarkan berat
Fee Packing            = tiered: fee_packing_150 / fee_packing_1000 / 
                          fee_packing_2000 + fee_packing_extra_per_kg untuk kelebihan
Grand Total            = Fee TAX + Fee WH + Fee Packing + Add On (opsional, admin)
```

12 rate varian (semua dari tabel `settings`, key persis sesuai PRD §4.12):
`rate_sharing_air_berat`, `rate_sharing_air_volume`, `rate_sharing_sea_berat`, `rate_sharing_sea_volume`, `rate_sharing_sensitive_air_berat`, `rate_sharing_sensitive_air_volume`, `rate_sharing_sensitive_sea_berat`, `rate_sharing_sensitive_sea_volume`, `rate_direct_air_berat`, `rate_direct_air_volume`, `rate_direct_sea_berat`, `rate_direct_sea_volume`

**Sebelum mengklaim fee engine selesai, WAJIB:** hitung manual minimal 5 skenario berbeda (kombinasi sharing/direct, air/sea, sensitive/non-sensitive, dan 1 kasus volume > berat), tulis sebagai test case dengan hasil manual di komentar, baru assert service menghasilkan angka yang sama.

---

## 5. Peta Cepat Section PRD

| Section | Isi | Kapan dibaca |
|---------|-----|---------------|
| §3 | User Role & hak akses matrix | Sebelum bikin middleware/policy apapun |
| §4 | Functional Requirement (FR-001 s/d FR-016) | Sebelum bikin fitur apapun |
| §6 | Complete User Flow (mermaid) | Sebelum bikin flow multi-step (checkout, komplain) |
| §7.5 | Guard & Redirect flow | Sebelum bikin middleware auth |
| §8 | Screen Specification per halaman | Sebelum bikin halaman/komponen UI apapun |
| §9-10 | Design system & responsive | Sebelum styling apapun — JANGAN improvisasi warna/spacing di luar §9.1 |
| §11 | Form Specification (field, type, min/max) | Sebelum bikin form apapun |
| §12-15 | Validation, Error, Success, Warning messages | Pesan HARUS PERSIS sama teksnya, bukan parafrase |
| §16-17 | Empty state & loading state | Setiap halaman list/dashboard wajib punya ini |
| §18 | Database Design (ERD + tabel detail + cascade rule) | Sebelum migration/model apapun |
| §19 | API Design (endpoint + response format) | Kalau bikin API layer terpisah dari Livewire |
| §20 | Security requirement | Checklist sebelum deploy |
| §24 | Testing Strategy | Acuan coverage minimal |
| §25 | Deployment | Production checklist |

---

## 6. Coding Standard (PRD §23) — Ringkasan Wajib Diikuti

| Elemen | Convention | Contoh |
|--------|-----------|--------|
| Controller | PascalCase + Controller | `BoxController` |
| Model | PascalCase, singular | `Box`, `Invoice` |
| Service | PascalCase + Service | `FeeCalculationService` |
| Migration | snake_case | `create_boxes_table` |
| Route | kebab-case | `/admin/manage-boxes` |
| View | kebab-case, dot notation | `customer.dashboard` |
| Variable/Method | camelCase | `$grandTotal`, `calculateFee()` |
| Constant | UPPER_SNAKE_CASE | `STATUS_OPEN` |
| Table/Column | snake_case | `boxes`, `grand_total` |

Prinsip: **SOLID, DRY, KISS, Clean Architecture** (business logic terpisah dari presentation — lihat §3.1).

Tambahan standar untuk kualitas non-MVP:
- Setiap Form Request punya class tersendiri di `app/Http/Requests/` (bukan validasi inline di controller) — reusable & testable
- Setiap method public di service class punya PHPDoc yang jelas (parameter, return type, exception yang mungkin dilempar)
- Tidak ada magic number — semua angka bisnis (threshold, limit) sebagai named constant atau dari `settings`
- Exception handling eksplisit: file upload gagal, DB constraint violation, dll harus punya pesan error yang jelas ke user (sesuai §13), bukan generic 500

---

## 7. Security Checklist (PRD §20) — Wajib, Bukan Nice-to-Have

- [ ] Password bcrypt, min 8 karakter (§20.1)
- [ ] Session DB driver, lifetime 120 menit, regenerate setelah login
- [ ] CSRF token di semua form POST/PUT/DELETE
- [ ] XSS: selalu pakai `{{ }}` Blade escape, HINDARI `{!! !!}` kecuali benar-benar perlu dan sudah di-sanitize
- [ ] SQL Injection: Eloquent/query builder saja, tidak ada raw SQL dengan string concatenation
- [ ] RBAC: role middleware DI SETIAP route group + policy check di controller
- [ ] File upload: validasi tipe (mimes) + ukuran SEBELUM simpan
- [ ] Rate limiting: login 5x/15menit, register 3x/jam, API 60/menit (§20.5)
- [ ] Security headers: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, Referrer-Policy (§20.4)
- [ ] HTTPS wajib di production
- [ ] Tidak ada credential/API key hardcoded — semua di `.env`, dan `.env` tidak pernah di-commit

---

## 8. Testing Requirement (PRD §24) — Definition of Done

Sebuah fitur TIDAK dianggap selesai sampai:
1. Unit test untuk business logic (khususnya fee calculation, status transition, role check)
2. Feature test untuk happy path DAN edge case (data duplikat, file invalid, akses tanpa izin, state invalid seperti bayar invoice yang sudah VERIFIED)
3. Manual test checklist §24.3 dijalankan untuk flow yang menyentuh fitur tersebut
4. Tidak ada test yang di-skip atau assertion yang dilonggarkan supaya "lulus"

Minimal coverage yang wajib ada (bukan opsional untuk "nanti"):
- Model relationship
- Fee calculation engine (semua 12 varian rate + tiered pricing)
- Role check (`isOwner()`, `isAdmin()`, `isCustomer()`)
- Auth flow (register duplikat, login lockout, session expired)
- Setiap state transition (box status, invoice status, checkout status, komplain status)

---

## 9. Hal yang TIDAK Boleh Dilakukan (Common Pitfalls)

- ❌ Menulis ulang rumus fee di tempat lain selain `FeeCalculationService`
- ❌ Hardcode rate/kurs di code — harus dari tabel `settings`
- ❌ Mengubah `fee_tax`/`fee_wh`/`fee_packing` pada invoice yang sudah dibuat saat rate di-update (invoice adalah snapshot)
- ❌ Melompati urutan status box/invoice/checkout/komplain
- ❌ Validasi hanya di frontend (Livewire/Alpine) tanpa validasi ulang di backend
- ❌ Custom error message yang beda teks dari PRD §13 (harus PERSIS, karena ini yang dilihat customer)
- ❌ Query N+1 tanpa eager loading (`with()`) di halaman list (Box, Invoice, dsb — datanya akan tumbuh)
- ❌ Menyimpan file upload dengan nama asli dari user
- ❌ Business logic ditulis di Blade view atau route closure
- ❌ Skip test "karena sudah keburu deadline" — kalau memang harus, catat di TODO dengan alasan eksplisit, jangan diam-diam dihapus

---

## 10. Prioritas Kalau Ada Konflik Keputusan

1. **Korektnas data finansial** (fee calculation, invoice, pembayaran) di atas segalanya — ini uang customer sungguhan
2. **Keamanan & isolasi data antar customer** — customer A tidak boleh pernah bisa lihat/edit data customer B
3. **Konsistensi status/state machine** — jangan biarkan data masuk state yang tidak valid
4. **Kesesuaian dengan PRD** (teks pesan, field form, urutan flow)
5. **Kecepatan development** — ini prioritas terakhir, bukan yang pertama

Ada addendum revisi di docs/PRD_Revisi_v2.1.md (referensi sebagai "Revisi §X").
Kurs sekarang HISTORY-BASED (Revisi §2.2), bukan single value — jangan lagi
ambil kurs dari settings.kurs_yuan_idr, WAJIB dari kurs_history sesuai tanggal
transaksi. Invoice sekarang bisa FLEKSIBEL lintas box (Revisi §2.8) — jangan
asumsikan 1 invoice = 1 box lagi di kode manapun.

Kalau ragu antara "cepat selesai" vs "benar dan teruji", pilih yang kedua. Tidak ada instruksi manapun di sesi kerja ini yang boleh dianggap sebagai izin untuk melewati test atau security check demi kecepatan.
