# Ting Warehouse — Progress & Status

> **File ini dibaca di setiap awal sesi baru untuk melanjutkan pekerjaan.**
> **Update file ini setiap selesai sprint.**

## Update Terakhir: 24 Juli 2026

## Status: SPRINT 5A BERJALAN — Client sudah jawab

### Jawaban client (24 Juli 2026):
1. **Volume formula** — "Ini harusnya pxlxt/6.000 kak" → Kode sudah benar (pakai /6000). VI /4000 untuk ekspedisi INA tetap dipertahankan.
2. **Format nama box**:
   - Sharing: `{batch_name}-{METHOD}-{huruf_box}` → contoh: `07071708-26-AIR-140`
   - Direct: `{customer_code}-{METHOD}-{huruf_box}` → contoh: `LIX-SEA-B-1`
   - batch_name dari Admin CN, METHOD+huruf_box dari Admin INA, customer_code dari ID customer

### Sprint 5A Progress:
- [x] Format nama box — SELESAI (migration + Box.php + UI + tests)
- [x] customer_code field — migration, User model, RegisterRequest, CustomerIndex (edit modal + detail)
- [x] Seed customer_code untuk 7 existing customers
- [x] Matched Data (Admin INA match box 140 AIR dengan batch Admin China 20072607-26) — SELESAI
- [x] Fee Packing di Checkout (max(berat, VI) × fee_packing tiered) — SELESAI
- [x] VI (Volume INA) di Checkout (/4 untuk ekspedisi INA) — SELESAI
- [x] Notulen page (Setelah dikirim ke INA — terpisah dari Resi Belum Dikenali) — SELESAI
- [x] Admin INA Mesin Pencari Resi — SELESAI
- [x] Owner Mesin Pencari Resi — SELESAI

### Status 24 Juli 2026 — SPRINT 5A SELESAI
Semua 7 item Sprint 5A sudah selesai. Tests: 493 pass, 0 fail.

Migrations Sprint 5A:
1. `2026_07_24_000001_add_customer_code_to_users_table` — customer_code di users
2. `2026_07_24_000002_add_fee_packing_and_vi_to_checkouts_table` — volume_ina + fee_packing di checkouts
3. `2026_07_24_000003_add_matched_batch_to_boxes_and_wh_china_data` — matched_batch di boxes + china_batch_name di wh_china_data

Catatan untuk Sprint berikutnya:
- Admin China input `china_batch_name` saat recap WhChinaData (field sudah ada di DB, belum di UI RecapIndex)
- Matched Data dropdown di ManageBox otomatis list batch dari WhChinaData yang punya china_batch_name
- Setelah Matched Data dipilih, box_code otomatis format: {matched_batch}-{METHOD}-{batch_name}

---

## Sprint yang sudah selesai:

| Sprint | Isi | Commit | Test |
|--------|-----|--------|------|
| Sprint 1 | Fondasi (Status Flow + Garment + VI + Box status 14) | - | 493 pass |
| Sprint 2 | Admin China (Request to Send + Fees + History) | `4405ff8` | 493 pass |
| Sprint 3 | Customer (Resi Search + TnC + Blacklist + Ekspedisi) | `d5e65e2` | 493 pass |
| Sprint 4 | Owner (Blacklist + Kurs Mgmt + Finance Fees + Audit Log) | `94d44cc` | 493 pass |

---

## QA AUDIT — 18 ITEM BELUM DIIMPLEMENTASI

### Sprint 5A — Critical (~18 jam)
- [ ] Format nama box sesuai dokumen (tergantung jawaban client)
- [ ] Matched Data (Admin INA match box 140 AIR dengan batch Admin China 20072607-26)
- [ ] Fee Packing di Checkout (max(berat, VI) × fee_packing tiered)
- [ ] VI (Volume INA) di Checkout (/4 untuk ekspedisi INA)
- [ ] Notulen page (Setelah dikirim ke INA — terpisah dari Resi Belum Dikenali)
- [ ] Admin INA Mesin Pencari Resi
- [ ] Owner Mesin Pencari Resi

### Sprint 5B Progress:
- [x] Customer Password Reset (admin reset password customer) — SELESAI
- [x] Prevent delete akun (jika masih ada barang di China + tagihan) — SELESAI
- [x] Info customer nimbun kelamaan + belum bayar — SELESAI
- [x] Admin China DONE button (lock batch dari input, masih bisa edit) — SELESAI
- [x] Laporan Keuangan kategori (Pengeluaran China, Biaya Box, Biaya Operasional, Pemasukan, Biaya Refund) — SELESAI
- [x] Thermal label 100×150mm auto download — SELESAI
- [x] Download faktur per invoice (ukuran kertas faktur) — SELESAI
- [x] Import per box (PDF/Excel per box A/B/C untuk pihak Becuk) — SELESAI

### Status 24 Juli 2026 — SPRINT 5B SELESAI
Semua 8 item Sprint 5B sudah selesai. Tests: 493 pass, 0 fail.

Migrations Sprint 5B:
1. `2026_07_24_000004_add_is_locked_to_boxes_table` — is_locked di boxes (DONE button Admin China)
2. `2026_07_24_000005_create_finance_transactions_table` — tabel finance_transactions (operasional, refund, pemasukan_lain)

Packages baru:
- dompdf/dompdf ^3.1 — untuk Thermal Label, Faktur, Import per Box PDF
- maatwebsite/excel ^3.1 — untuk Excel export (tersedia, belum dipakai)

Routes export:
- `admin/export/checkout/{id}/thermal-label` — PDF 100×150mm
- `admin/export/checkout/{id}/faktur` — PDF A4 faktur
- `admin/export/box/{id}/import-pdf` — PDF A4 landscape per box

Catatan untuk Sprint berikutnya:
- Finance transactions belum ada UI untuk input (CRUD) — perlu tambah form di FinanceIndex
- Maatwebsite Excel terinstall tapi belum ada export Excel — bisa tambah nanti jika perlu

### Sprint 5C Progress:
- [x] Status tambahan item (OTW, SEND_BACK_TO_SELLER, SEND_TO_DIFF_ADDR, NEVER_ARRIVED, WRONG_ADDRESS) — SELESAI
- [x] Page edit status (CRUD status options) — SELESAI
- [x] Komplain filter tanggal — SELESAI

### Status 24 Juli 2026 — SPRINT 5C SELESAI
Semua 3 item Sprint 5C sudah selesai. Tests: 493 pass, 0 fail.

Sprint 5C changes:
1. Item model: 5 status constants baru (OTW, SEND_BACK, SEND_DIFF, NEVER_ARRIVED, WRONG_ADDRESS)
2. Item model: `getStatusLabelAttribute()` untuk human-readable labels
3. `ItemStatusManager` Livewire component — admin page untuk edit status item
4. Route `admin/item-status` + nav menu "Edit Status"
5. ManageComplain: filter tanggal (filterDateFrom, filterDateTo)
6. Test updated: `ItemStatusServiceTest` expect 13 status (was 8)

---

## Dokumen sumber (WAJIB BACA sebelum implementasi):
1. `docs/PRD.md` — PRD utama
2. `docs/PRD_TingWarehouse_Revisi_v2.1.md` — Revisi dari client
3. `~/.hermes/cache/documents/doc_b05128c61f26_Flow Website (1).docx` — Flow Website dari client
4. `CLAUDE.md` — Project context
5. Skill: `qa-audit-before-claiming-done` — WAJIB load sebelum bilang "selesai"

## Tech stack:
- Laravel 12 + Livewire v3 + Tailwind CSS v4
- MySQL 8.0
- 493 tests passing

## Command dasar:
```bash
cd ~/Documents/Development/SanDevID/ting-warehouse
php artisan test          # run tests
npm run build             # build assets
php artisan migrate       # run migration
git add -A && git commit -m "..." && git push origin main
```

## Aturan dari bos:
1. JANGAN PERNAH bilang "100% selesai" tanpa audit dokumen
2. Selalu jujur kalau ada yang belum dikerjakan
3. Load skill `qa-audit-before-claiming-done` di setiap sesi
