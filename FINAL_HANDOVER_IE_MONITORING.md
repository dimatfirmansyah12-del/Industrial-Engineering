# Final Handover Project Monitoring Dashboard Industrial Engineering

## 1. Judul

**Final Handover Project Monitoring Dashboard Industrial Engineering**

## 2. Informasi Project

- Nama project: **Monitoring Dashboard Industrial Engineering**
- Platform: Web Laravel
- Database: MySQL / XAMPP
- Frontend: Blade + Tailwind CSS
- Authentication: Laravel Breeze
- Tujuan utama: Digitalisasi monitoring request Industrial Engineering dari memo sampai handover.

## 3. Ringkasan Masalah Awal

Sebelum sistem ini dibuat, proses request ke Industrial Engineering masih banyak dilakukan secara manual. Request menggunakan memo dan proses follow up dilakukan melalui komunikasi langsung, chat, atau pengecekan manual.

Masalah utama yang ditemukan:

- Proses request masih manual.
- Tracking status sulit dilakukan secara real time.
- Approval dan follow up banyak dilakukan manual.
- Memo dan pekerjaan berpotensi terlambat.
- Report sulit dibuat dengan cepat.
- Tidak ada dashboard real time.
- Data tidak transparan antar bagian.

## 4. Ringkasan Solusi

Sistem web ini dibuat untuk memusatkan data request Industrial Engineering dalam satu aplikasi. Setiap request memiliki status, histori aktivitas, komentar, file pendukung, dan flow proses dari awal sampai selesai.

Solusi yang disediakan:

- Request IE dipusatkan dalam satu sistem web.
- Setiap request memiliki status dan histori.
- Setiap proses memiliki modul sendiri.
- Dashboard menampilkan summary, delay, due soon, pipeline, dan attention list.
- Report bisa difilter dan diexport CSV.
- Work order bisa dicetak melalui browser print.
- TV dashboard bisa ditampilkan di area kerja/lobby.

## 5. Flow Utama Sistem

```text
Customer Request
-> Memo Approval
-> Drawing Progress
-> Material / BOM
-> Budget / PR
-> Material Arrival
-> Workshop Schedule
-> Workshop Progress
-> Final Check
-> Handover
-> Closed
```

Penjelasan singkat:

- **Customer Request**: User membuat request dan mengisi kebutuhan pekerjaan.
- **Memo Approval**: Memo direview dan disetujui oleh pihak berwenang.
- **Drawing Progress**: Drafter mengerjakan drawing sampai selesai.
- **Material / BOM**: Kebutuhan material dan estimasi biaya diinput.
- **Budget / PR**: Purchasing membuat dan memproses PR sampai PO Created.
- **Material Arrival**: Kedatangan material dimonitor sampai complete.
- **Workshop Schedule**: Workshop membuat jadwal pengerjaan.
- **Workshop Progress**: Progress pekerjaan workshop diupdate.
- **Final Check**: QC melakukan pengecekan akhir.
- **Handover**: Pekerjaan diserahterimakan ke user/customer.
- **Closed**: Request selesai dan ditutup.

## 6. Daftar Modul Sistem

| No | Modul | Fungsi Utama |
|---|---|---|
| 1 | Dashboard | Menampilkan summary request, chart, pipeline, delay, due soon, dan attention list. |
| 2 | TV Dashboard | Menampilkan dashboard full screen untuk TV/lobby/area monitoring. |
| 3 | Request Monitoring | Membuat, melihat, mengubah, menghapus, dan memfilter request. |
| 4 | Memo Approval | Mengelola approval atau reject memo. |
| 5 | Drawing Progress | Mengelola assignment drafter dan progress drawing. |
| 6 | Material / BOM | Mengelola kebutuhan material dan estimasi biaya. |
| 7 | Budget / PR | Mengelola proses PR sampai PO Created. |
| 8 | Material Arrival | Mengelola status kedatangan material. |
| 9 | Workshop Schedule | Mengelola jadwal pekerjaan workshop. |
| 10 | Workshop Progress | Mengelola status dan persentase progress workshop. |
| 11 | Final Check | Mengelola pengecekan akhir oleh QC. |
| 12 | Handover | Mengelola proses serah terima pekerjaan. |
| 13 | Report | Melihat dan memfilter report request. |
| 14 | Kanban Board | Melihat request secara visual berdasarkan status. |
| 15 | Print Work Order | Mencetak detail request sebagai dokumen kerja. |
| 16 | Activity Log | Mencatat histori aktivitas request. |
| 17 | Komentar Request | Menambahkan diskusi, catatan, dan attachment pada request. |
| 18 | User Management | Admin mengatur role user. |
| 19 | Master Data Department | Admin mengelola data department. |
| 20 | Master Data Line / Area | Admin mengelola data line atau area. |

## 7. Daftar Role dan Hak Akses

| Role | Hak Akses Utama |
|---|---|
| admin | Akses penuh ke seluruh modul, master data, dan user management. |
| manager | Akses dashboard, memo approval, handover, report, dan monitoring. |
| customer | Membuat request dan melihat request miliknya sendiri. |
| drafter | Mengelola drawing progress dan Material / BOM. |
| purchasing | Mengelola Budget / PR dan Material Arrival. |
| workshop | Mengelola Workshop Schedule dan Workshop Progress. |
| qc | Mengelola Final Check. |

## 8. Akun Default

```text
Email: admin@ie-monitoring.local
Password: password
Role: admin
```

Catatan:

Password default wajib diganti jika sistem mulai dipakai.

## 9. Cara Menjalankan Project di Local

1. Buka XAMPP.
2. Start Apache.
3. Start MySQL.
4. Buka terminal di folder project.
5. Jalankan:

```bash
php artisan serve
```

6. Buka terminal kedua.
7. Jalankan:

```bash
npm run dev
```

8. Buka browser:

```text
http://127.0.0.1:8000
```

## 10. Command Penting

| Command | Fungsi |
|---|---|
| `php artisan migrate` | Menjalankan migration database. |
| `php artisan db:seed` | Mengisi data awal seperti admin, department, dan line area. |
| `php artisan route:list` | Melihat daftar route aplikasi. |
| `php artisan migrate:status` | Melihat status migration. |
| `php artisan view:clear` | Membersihkan cache view Blade. |
| `php artisan cache:clear` | Membersihkan cache aplikasi. |
| `php artisan route:clear` | Membersihkan cache route. |
| `php artisan config:clear` | Membersihkan cache konfigurasi. |
| `php artisan storage:link` | Membuat akses file upload dari public storage. |
| `composer dump-autoload` | Memperbarui autoload class Composer. |
| `npm run dev` | Menjalankan Vite untuk asset CSS/JS development. |

## 11. Struktur Folder Penting

- `routes/web.php`
- `routes/auth.php`
- `app/Http/Controllers`
- `app/Models`
- `app/Http/Middleware`
- `database/migrations`
- `database/seeders`
- `resources/views`
- `resources/views/components`
- `storage/app/public`
- `public/storage`

Catatan:

Jangan menaruh route aplikasi di `routes/auth.php`. Route aplikasi harus berada di `routes/web.php`.

## 12. Database dan Tabel Penting

| Tabel | Fungsi |
|---|---|
| `users` | Menyimpan data user dan role. |
| `ie_requests` | Menyimpan data utama request Industrial Engineering. |
| `departments` | Menyimpan master data department. |
| `line_areas` | Menyimpan master data line atau area. |
| `request_materials` | Menyimpan data material / BOM. |
| `purchase_requests` | Menyimpan data Budget / PR. |
| `workshop_schedules` | Menyimpan jadwal dan progress workshop utama. |
| `workshop_progress_logs` | Menyimpan log progress workshop. |
| `final_checks` | Menyimpan data final check. |
| `handovers` | Menyimpan data handover. |
| `request_activities` | Menyimpan activity log request. |
| `request_comments` | Menyimpan komentar dan attachment request. |

## 13. File Dokumentasi yang Sudah Dibuat

- `README_IE_MONITORING.md`
- `PRODUCTION_READINESS_IE_MONITORING.md`
- `DEMO_PACKAGE_IE_MONITORING.md`
- `DEMO_SCRIPT_IE_MONITORING.md`
- `SOP_IE_MONITORING.md`
- `TRAINING_MATERIAL_IE_MONITORING.md`
- `TRAINING_CHECKLIST_IE_MONITORING.md`
- `TESTING_CHECKLIST_IE_MONITORING.md`
- `FINAL_HANDOVER_IE_MONITORING.md`

## 14. Cara Backup Database

1. Buka:

```text
http://localhost/phpmyadmin
```

2. Pilih database `ie_monitoring`.
3. Klik **Export**.
4. Pilih **Quick**.
5. Pilih format **SQL**.
6. Klik **Go**.
7. Simpan file backup dengan format:

```text
ie_monitoring_db_YYYY-MM-DD.sql
```

## 15. Cara Backup File Upload

Folder upload ada di:

```text
storage/app/public
```

Folder yang perlu dibackup:

- `storage/app/public/ie-requests`
- `storage/app/public/workshop-progress`
- `storage/app/public/final-checks`
- `storage/app/public/handovers`
- `storage/app/public/request-comments`

Format backup:

```text
ie_monitoring_storage_YYYY-MM-DD.zip
```

## 16. Cara Restore Project

1. Copy project ke folder server/laptop.
2. Jalankan `composer install` jika folder `vendor` belum ada.
3. Copy file `.env`.
4. Setting database di `.env`.
5. Buat database `ie_monitoring`.
6. Import file SQL lewat phpMyAdmin.
7. Restore folder `storage/app/public`.
8. Jalankan:

```bash
php artisan storage:link
php artisan view:clear
php artisan cache:clear
php artisan route:clear
```

9. Jalankan:

```bash
php artisan serve
npm run dev
```

## 17. Checklist Testing Final

- [ ] Login admin berhasil.
- [ ] User management berhasil.
- [ ] Request bisa dibuat.
- [ ] Memo bisa diupload.
- [ ] Memo approval berjalan.
- [ ] Drawing progress berjalan.
- [ ] BOM bisa dibuat.
- [ ] PR bisa dibuat.
- [ ] Material arrival bisa diupdate.
- [ ] Workshop schedule bisa dibuat.
- [ ] Workshop progress bisa diupdate.
- [ ] Final check berjalan.
- [ ] Handover bisa close request.
- [ ] Dashboard tampil.
- [ ] TV dashboard tampil.
- [ ] Report export CSV bisa.
- [ ] Kanban board tampil.
- [ ] Print work order bisa.
- [ ] Activity log tampil.
- [ ] Komentar request bisa.
- [ ] Role access berjalan.
- [ ] Customer hanya melihat request miliknya.
- [ ] Backup database berhasil.
- [ ] Backup storage berhasil.

## 18. Skenario Demo Final

1. Login admin.
2. Buka dashboard.
3. Tambah request baru.
4. Upload memo.
5. Approve memo.
6. Drawing progress sampai Done.
7. Input BOM.
8. Buat PR sampai PO Created.
9. Update material arrival sampai Complete.
10. Buat workshop schedule.
11. Workshop progress sampai Done.
12. Final check Passed.
13. Handover Received.
14. Request menjadi Closed.
15. Buka report.
16. Export CSV.
17. Print Work Order.
18. Buka Kanban.
19. Buka TV Dashboard.
20. Tunjukkan activity log.

## 19. Catatan Maintenance

- Backup database minimal seminggu sekali.
- Backup storage minimal seminggu sekali.
- Jangan edit file `auth.php` sembarangan.
- Jangan hapus migration lama.
- Jika tambah kolom database, buat migration baru.
- Jika tambah menu, update sidebar component.
- Jika upload file tidak terbuka, jalankan `php artisan storage:link`.
- Jika route error, cek `php artisan route:list`.
- Jika view error, jalankan `php artisan view:clear`.
- Jika CSS tidak berubah, jalankan `npm run dev`.

## 20. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| User tidak update data tepat waktu | Status monitoring tidak akurat | Tentukan PIC setiap proses dan jadwalkan review harian. |
| Database tidak dibackup | Data request bisa hilang | Backup database minimal seminggu sekali. |
| File upload hilang | Memo, drawing, evidence, dan attachment tidak bisa dibuka | Backup folder `storage/app/public` secara rutin. |
| Role user salah | User bisa salah akses atau tidak bisa mengakses menu yang dibutuhkan | Admin melakukan review role user secara berkala. |
| Server lokal mati | Aplikasi tidak bisa diakses | Siapkan prosedur restart server dan backup. |
| `APP_DEBUG` masih true saat production | Error teknis terlihat user | Set `APP_DEBUG=false` saat production. |
| Akun admin default belum diganti password | Risiko akses tidak sah | Ganti password admin default sebelum dipakai harian. |

## 21. Rekomendasi Next Improvement

- Email notification.
- WhatsApp notification internal.
- QR code untuk Work Order.
- Approval bertingkat.
- TV dashboard lebih interaktif.
- Export PDF otomatis.
- Integrasi dengan sistem purchasing.
- SLA monitoring.
- Analisis workload drafter dan workshop.
- Mobile upload progress photo.
- Audit log lebih detail.

## 22. Penutup

Project Monitoring Dashboard Industrial Engineering ini dibuat untuk membantu digitalisasi proses request IE agar lebih transparan, terukur, dan mudah dimonitor dari awal request sampai handover.

