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
