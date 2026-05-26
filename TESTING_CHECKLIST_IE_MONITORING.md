# Testing Checklist IE Monitoring

Gunakan checklist ini untuk testing manual sebelum aplikasi dipakai di lingkungan kerja.

Status bisa diisi manual dengan: `OK`, `NG`, atau `Perlu Dicek`.

| No | Modul | Test Case | Expected Result | Status |
|---|---|---|---|---|
| 1 | Auth | Register user baru | User berhasil dibuat dan bisa login | |
| 2 | Auth | Login | User berhasil masuk ke dashboard | |
| 3 | Auth | Logout | User keluar dan kembali ke halaman login | |
| 4 | Auth | Role default customer | User register baru memiliki role customer | |
| 5 | User Management | Admin buka `/users` | Halaman User Management terbuka | |
| 6 | User Management | Admin ubah role user | Role user berubah dan tersimpan | |
| 7 | User Management | Customer buka `/users` | Akses ditolak dengan 403 | |
| 8 | Dashboard | Dashboard terbuka | Dashboard tampil tanpa error | |
| 9 | Dashboard | Card total request benar | Jumlah total request sesuai data | |
| 10 | Dashboard | Card delay benar | Request delay dihitung sesuai target date | |
| 11 | Dashboard | Card due soon benar | Request deadline 3 hari ke depan dihitung | |
| 12 | Dashboard | Chart tampil | Semua chart dashboard tampil | |
| 13 | Dashboard | Pipeline monitoring tampil | Data pipeline/status tampil | |
| 14 | Dashboard | Attention dashboard tampil | Panel Perhatian Hari Ini tampil dan hanya menampilkan data aktif | |
| 15 | Request Monitoring | Tambah request | Request baru berhasil dibuat | |
| 16 | Request Monitoring | Nomor request otomatis | Request number dibuat otomatis | |
| 17 | Request Monitoring | Edit request | Data request berhasil diubah | |
| 18 | Request Monitoring | Detail request | Detail request tampil lengkap | |
| 19 | Request Monitoring | Delete request | Request berhasil dihapus oleh admin | |
| 20 | Request Monitoring | Upload memo | File memo tersimpan dan bisa dibuka | |
| 21 | Request Monitoring | Upload drawing | File drawing tersimpan dan bisa dibuka | |
| 22 | Request Monitoring | Filter search | Data terfilter berdasarkan keyword | |
| 23 | Request Monitoring | Filter status | Data terfilter berdasarkan status | |
| 24 | Request Monitoring | Filter priority | Data terfilter berdasarkan priority | |
| 25 | Request Monitoring | Filter deadline | Data terfilter Delay atau Due Soon | |
| 26 | Request Monitoring | Pagination | Pindah halaman data berjalan normal | |
| 27 | Request Monitoring | Quick update status | Status berubah tanpa error | |
| 28 | Request Monitoring | Quick update priority | Priority berubah tanpa error | |
| 29 | Memo Approval | Request dengan memo muncul | Request yang butuh approval tampil di Memo Approval | |
| 30 | Memo Approval | Approve memo | Memo berubah approved dan status request ikut update | |
| 31 | Memo Approval | Reject memo dengan alasan | Memo rejected dan alasan tersimpan | |
| 32 | Memo Approval | Request tanpa memo tidak muncul | Request tanpa memo tidak tampil jika logic mensyaratkan memo | |
| 33 | Drawing Progress | Request memo approved muncul | Request siap drawing tampil | |
| 34 | Drawing Progress | Assign drafter | Drafter berhasil disimpan | |
| 35 | Drawing Progress | Start drawing | Drawing status berubah on progress | |
| 36 | Drawing Progress | Revision drawing | Revision note tersimpan | |
| 37 | Drawing Progress | Done drawing | Drawing status berubah done dan request status ikut update | |
| 38 | Drawing Progress | Upload drawing file | File drawing tersimpan dan bisa dibuka | |
| 39 | Material / BOM | Request drawing done muncul | Request siap BOM tampil | |
| 40 | Material / BOM | Tambah material | Material berhasil ditambahkan | |
| 41 | Material / BOM | Edit material | Material berhasil diubah | |
| 42 | Material / BOM | Hapus material | Material berhasil dihapus | |
| 43 | Material / BOM | Total price otomatis | Total price sesuai qty x estimated price | |
| 44 | Material / BOM | Total estimation benar | Total estimasi BOM sesuai semua material | |
| 45 | Budget / PR | Request punya BOM muncul | Request dengan BOM tampil di Budget / PR | |
| 46 | Budget / PR | Buat PR | PR berhasil dibuat | |
| 47 | Budget / PR | Submit PR | PR berubah submitted | |
| 48 | Budget / PR | Approve PR | PR berubah approved | |
| 49 | Budget / PR | Reject PR | PR berubah rejected dan alasan tersimpan | |
| 50 | Budget / PR | Mark as PO Created | PR berubah PO Created | |
| 51 | Material Arrival | Request PR PO Created muncul | Request siap update material arrival tampil | |
| 52 | Material Arrival | Update arrived qty partial | Qty datang sebagian tersimpan | |
| 53 | Material Arrival | Status Partial Arrived | Arrival status berubah Partial Arrived | |
| 54 | Material Arrival | Update arrived qty complete | Qty datang lengkap tersimpan | |
| 55 | Material Arrival | Status Complete | Arrival status berubah Complete | |
| 56 | Workshop Schedule | Request material complete muncul | Request siap schedule tampil | |
| 57 | Workshop Schedule | Buat schedule | Schedule berhasil dibuat | |
| 58 | Workshop Schedule | Ready to Work | Schedule berubah Ready to Work | |
| 59 | Workshop Schedule | Reschedule | Jadwal berhasil diubah | |
| 60 | Workshop Schedule | Cancel schedule | Schedule berhasil cancelled | |
| 61 | Workshop Progress | Schedule Ready to Work muncul | Schedule siap progress tampil | |
| 62 | Workshop Progress | Update progress On Progress | Progress berubah On Progress | |
| 63 | Workshop Progress | Upload foto/file progress | File progress tersimpan dan bisa dibuka | |
| 64 | Workshop Progress | Hold wajib problem note | Hold ditolak jika problem note kosong | |
| 65 | Workshop Progress | Rework wajib problem note | Rework ditolak jika problem note kosong | |
| 66 | Workshop Progress | Done wajib 100% | Done ditolak jika percentage belum 100% | |
| 67 | Final Check | Workshop Done muncul | Request siap final check tampil | |
| 68 | Final Check | Buat final check | Final check berhasil dibuat | |
| 69 | Final Check | Start checking | Check status berubah Checking | |
| 70 | Final Check | Need Rework | Status berubah Need Rework dan note tersimpan | |
| 71 | Final Check | Passed OK | Result berubah OK dan status request ikut update | |
| 72 | Final Check | Failed NG | Result berubah NG dan note tersimpan | |
| 73 | Handover | Final Check Passed OK muncul | Request siap handover tampil | |
| 74 | Handover | Buat handover | Handover berhasil dibuat | |
| 75 | Handover | Start handover | Handover status berubah process | |
| 76 | Handover | Received | Handover received berhasil | |
| 77 | Handover | Request menjadi Closed | Status request berubah Closed setelah received | |
| 78 | Handover | Reject handover tidak close request | Request tidak berubah Closed saat handover rejected | |
| 79 | Report | Filter tanggal | Report terfilter berdasarkan tanggal | |
| 80 | Report | Filter status | Report terfilter berdasarkan status | |
| 81 | Report | Filter priority | Report terfilter berdasarkan priority | |
| 82 | Report | Filter department | Report terfilter berdasarkan department | |
| 83 | Report | Filter deadline | Report terfilter Delay atau Due Soon | |
| 84 | Report | Export CSV | File CSV berhasil terdownload dan data sesuai filter | |
| 85 | Kanban Board | Board terbuka | Kanban tampil tanpa error | |
| 86 | Kanban Board | Request muncul sesuai status | Card request berada di kolom status yang benar | |
| 87 | Kanban Board | Filter department | Board terfilter berdasarkan department | |
| 88 | Kanban Board | Filter priority | Board terfilter berdasarkan priority | |
| 89 | Kanban Board | Filter delay/due soon | Board terfilter berdasarkan deadline | |
| 90 | Kanban Board | Customer hanya lihat request sendiri | Customer tidak melihat request user lain | |
| 91 | Print Work Order | Print page terbuka | Halaman print terbuka tanpa error | |
| 92 | Print Work Order | Tidak ada sidebar | Halaman print tidak menampilkan sidebar/navbar dashboard | |
| 93 | Print Work Order | Data lengkap tampil | Data request, material, PR, workshop, final check, handover tampil jika ada | |
| 94 | Print Work Order | Bisa Ctrl+P Save as PDF | Browser bisa print atau save as PDF | |
| 95 | Print Work Order | Customer tidak bisa print request orang lain | Akses ditolak dengan 403 | |
| 96 | Activity Log | Created Request tercatat | Log Created Request muncul di detail request | |
| 97 | Activity Log | Status Updated tercatat | Log status update muncul | |
| 98 | Activity Log | Memo Approved tercatat | Log Memo Approved muncul | |
| 99 | Activity Log | Drawing Done tercatat | Log Drawing Done muncul | |
| 100 | Activity Log | Handover Received tercatat | Log Handover Received muncul | |
| 101 | Activity Log | Request Closed tercatat | Log Request Closed muncul | |
| 102 | Komentar | Tambah komentar | Komentar tersimpan dan tampil | |
| 103 | Komentar | Tambah attachment komentar | Attachment komentar tersimpan | |
| 104 | Komentar | Attachment bisa dibuka | Link attachment bisa dibuka | |
| 105 | Komentar | User tidak bisa hapus komentar orang lain | Akses delete ditolak dengan 403 | |
| 106 | Komentar | Admin bisa hapus komentar | Admin bisa menghapus semua komentar | |
| 107 | Backup & Restore | Export database via phpMyAdmin | File `.sql` berhasil dibuat | |
| 108 | Backup & Restore | Backup `storage/app/public` | Folder upload berhasil disalin | |
| 109 | Backup & Restore | Restore database | Database hasil restore bisa dipakai | |
| 110 | Backup & Restore | `storage:link` berjalan | File upload bisa diakses lewat browser | |

