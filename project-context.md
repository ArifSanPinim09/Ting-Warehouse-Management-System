# Ting Warehouse — Project Context (Updated 2026-07-11)

## Status: ACTIVE — Client Testing Phase

## Tech Stack
- Laravel 12, Livewire v3, Tailwind CSS v4, Alpine.js, MySQL 8.0, PHP 8.4
- Auth: Laravel Breeze (session-based)
- Tests: 469 passing

## Recent Changes (2026-07-11)

### Revisi 1: WH China Form → English
- Form Input WH China diubah ke bahasa Inggris (China staff yang isi)
- Field rules: Resi Number*, Service Fee*, Photo* = wajib; Weight, Dimensions = opsional
- Hint: "Weight & dimensions can be filled later when goods arrive in Indonesia."
- Files: RecapIndex.php, recap/index.blade.php, RecapRedesignTest.php

### Revisi 2: Edit Tracking & ETA
- Tombol "Edit Tracking & ETA" di box detail panel
- Modal edit: Tracking Number + ETA (date picker)
- Audit log untuk perubahan
- Files: ManageBox.php, boxes/index.blade.php

### Fix: Upload Foto Setor Resi
- PHP upload_max_filesize → 50M, post_max_size → 55M
- Livewire temp upload max → 50MB

### Fix: Sharing Box Dropdown
- Query updated to include sharing box (customer_id=NULL)

## Pending for Next Session
- Client akan test revisi WH China form + Box edit tracking/ETA
- Feedback client belum masuk untuk revisi ini
- Browser Agent testing terbatas (Livewire wire:click issue)
- Perlu client verifikasi langsung via ngrok URL

## Key Files Modified
- `app/Livewire/Admin/RecapIndex.php` — WH China validation rules
- `app/Livewire/Admin/ManageBox.php` — Box edit tracking/ETA methods
- `resources/views/livewire/admin/recap/index.blade.php` — English form labels
- `resources/views/livewire/admin/boxes/index.blade.php` — Edit modal
- `tests/Feature/Admin/RecapRedesignTest.php` — Updated tests

## Server
- Ngrok: Running (URL per session)
- Laravel: `php artisan serve --host=0.0.0.0 --port=8000`
- Build: `npm run build`

## User Accounts
- Admin: admin1@tingwarehouse.com / password
- Customer: budi@gmail.com / password
- Owner: owner@tingwarehouse.com / password

## Updated: 2026-07-11 — Revisi 1 & 2

### Revisi 1: Box Huruf Box (bd70ee5)
- New huruf_box column on boxes table
- Display format: batch_name-huruf_box (e.g., 126-H)
- Box model: box_code + display_name accessors
- 10 new tests, all 479 tests passing

### Revisi 2: WH China New Columns (58ba0db)
- New columns: huruf_box, foto_arrived_china, foto_arrived_ina, tanggal_setor
- RecapIndex: form fields + validation + save logic
- Recap blade: table columns + modal form fields
- All 479 tests passing

### Revisi 3: Indonesia Measurements (def5522)
- New columns: berat_ina (decimal), panjang, lebar, tinggi, volume
- Volume auto-calculated: (P×L×T) / 6
- WhChinaData model: calculateVolume() + getEffectiveWeight()
- Table: Berat CN, Berat INA, Volume, P×L×T columns
- Modal: Indonesia Measurements section with auto-volume
- All 479 tests passing

### Revisi 4: CRUD Customer (7f9c8a7)
- Edit modal: name, email, phone, ktp_number, address, line_id, status
- Delete with confirmation + safety check (blocks if active boxes/invoices)
- Validation: required fields, unique email
- Audit log for all changes
- 8 new tests, all 487 tests passing

### Volume Formula Fix (8af27e3)
- Changed from P×L×T/6 to P×L×T/6000 (client-verified)
- Standard shipping volume weight formula
- Example: 60×40×50 → 20 (was 20,000)
- Updated in: FeeCalculationService, WhChinaData, RecapIndex, blade view, CLAUDE.md
- All 487 tests passing with recalculated expected values

### Client Revision Batch 2 (7772d3f, a6dd312, 285399b)
- Per-customer rates: custom_rate_air, custom_rate_sea on users table
- FeeCalculationService: added customRate parameter to calculate()
- Customer detail panel: shows Rate Air/Sea (Global or custom value)
- Edit Customer modal: Custom Rate section with Air/Sea inputs
- Biaya Tax: new field in wh_china_data table
- WH China table: new BIAYA TAX column
- WH China modal: Biaya Tax input field
- Customer Dashboard: shows arrived INA data per resi (P×L×T, Volume, Biaya Tax, Foto INA)
- Invoice format: shows Add On (tax tambahan) column
- All 487 tests passing
