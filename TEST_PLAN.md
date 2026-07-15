# 🧪 PRODUCTION READINESS TEST PLAN
## Ting Warehouse Management System
### Tanggal: 15 Juli 2026

---

## 1. METODOLOGI

**Pendekatan:** End-to-End mengikuti alur bisnis asli (bukan test per-fitur terisolasi).
**Urutan:** Sesuai flow uang & barang → Customer Setor → Admin Recap → Admin Invoice → Customer Bayar → Admin Verify → Customer Checkout → Admin Kirim.

**4 Role yang di-test:**
| Role | Email | Password | Routes |
|------|-------|----------|--------|
| Customer 1 | rina@tokorina.com | password | /dashboard, /box/*, /setor-resi, /invoice, /checkout, /komplain, /kalkulator, /no-tuan, /unmatched-resi |
| Admin | admin@tingwarehouse.com | password | /admin/* (14 halaman) |
| Owner | owner@tingwarehouse.com | password | /owner/* (7 halaman) + semua /admin/* |
| WH China | china@tingwarehouse.com | password | /china/* (4 halaman) |

---

## 2. TEST SCENARIOS (25 Scenarios)

### Phase 1: Auth & Access Control (3 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 1.1 | Customer login → redirect /dashboard | 200, dashboard muncul | |
| 1.2 | Customer akses /admin/dashboard → 403 Forbidden | RoleMiddleware block | |
| 1.3 | Guest akses /dashboard → redirect /login | Auth middleware block | |

### Phase 2: Customer — Setor Resi (3 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 2.1 | Buka /setor-resi → form lengkap | Dropdown box (hanya OPEN), nama, qty, harga, resi, checkboxes, foto | |
| 2.2 | Submit barang baru → item created | Item di DB, qty sesuai, resi tercatat | |
| 2.3 | Dropdown hanya tampilkan box OPEN | Box DONE/OTW_INA tidak muncul | |

### Phase 3: Admin — Manage Box (3 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 3.1 | /admin/manage-boxes → 8 boxes tampil | Tabel lengkap, semua status visible | |
| 3.2 | Filter by status → hanya box sesuai | Filter berfungsi | |
| 3.3 | Klik box → detail items tampil | Item list muncul dengan benar | |

### Phase 4: Admin — Recap (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 4.1 | /admin/recap → 27 customer items | Tab 1: items dengan resi, customer, box | |
| 4.2 | Tab WH China → 11 records | 8 matched, 3 unmatched, huruf_box visible | |

### Phase 5: Admin — Generate Invoice (3 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 5.1 | /admin/invoices → 4 invoices tampil | Tabel dengan fee breakdown | |
| 5.2 | Buat invoice baru → preview fee muncul | FeeCalculationService calculate benar | |
| 5.3 | Submit → invoice created + box status update | Status box berubah | |

### Phase 6: Admin — Verifikasi (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 6.1 | /admin/verification → 1 pending (Diana INV-003) | Tabel waiting_verification | |
| 6.2 | Verifikasi → status jadi verified | DB update, notifikasi terkirim | |

### Phase 7: Customer — Invoice & Payment (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 7.1 | /invoice → tampilkan invoice milik customer | Hanya milik sendiri, bukan milik orang lain | |
| 7.2 | Upload bukti bayar → status waiting_verification | File tersimpan, status berubah | |

### Phase 8: Customer — Checkout (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 8.1 | /checkout → tampilkan checkout customer | Data penerima, alamat, status | |
| 8.2 | Request checkout → admin terima | Checkout request created | |

### Phase 9: Admin — Checkout & Komplain (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 9.1 | /admin/checkouts → 2 checkouts | on_process + sent | |
| 9.2 | /admin/complains → 3 complains | open + in_review + resolved | |

### Phase 10: Owner — Finance & Edit Tax (3 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 10.1 | /owner/finance → 4 invoices, revenue stats | Summary cards correct | |
| 10.2 | Edit Fee Tax → modal → save → grand total recalculated | Tax berubah, audit log tercatat | |
| 10.3 | /owner/dashboard → revenue, customers, admins | Data sesuai DB | |

### Phase 11: WH China (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 11.1 | /china/dashboard → 11 WH China records | Huruf box, berat, matched/unmatched | |
| 11.2 | /china/requests → 7 items with request_type | Badge: bubble wrap, stripping, dll | |

### Phase 12: Cross-cutting (2 tests)
| # | Scenario | Expected | Pass? |
|---|----------|----------|-------|
| 12.1 | Kalkulator → hitung fee | Sesuai FeeCalculationService | |
| 12.2 | Unmatched Resi → customer klaim | Klaim berhasil | |

---

## 3. EXECUTION METHOD

Setiap test di-eksekusi via:
1. **PHP tinker** — query DB langsung untuk verify data
2. **Browser** — login, navigasi, lihat output
3. **Unit tests** — `php artisan test`

**Laporan:** Setiap test dicatat: PASS/FAIL + evidence (screenshot/query result).
**Bug:** Jika FAIL, langsung document: bug description, severity, steps to reproduce.

---

## 4. EXIT CRITERIA

- [ ] Semua 25 scenarios PASS atau ada documented reason kenapa FAIL
- [ ] 0 Critical bugs (data loss, security bypass, fee calculation wrong)
- [ ] 0 High bugs (data leak antar customer, broken auth)
- [ ] Medium/Low bugs didocument untuk fix nanti
- [ ] `php artisan test` — all pass
- [ ] `npm run build` — success
