# Production Readiness IE Monitoring

Dokumen ini berisi checklist persiapan aplikasi **Monitoring Dashboard Industrial Engineering** sebelum dipakai untuk demo atau penggunaan internal di area Industrial Engineering.

## 1. Cek Environment

File environment Laravel ada di:

```text
.env
```

Untuk development lokal, konfigurasi yang umum dipakai:

```env
APP_NAME="IE Monitoring"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

Untuk nanti jika aplikasi dipakai di server internal kantor:

```env
APP_NAME="IE Monitoring"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://alamat-server-internal
```

Konfigurasi database MySQL / XAMPP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ie_monitoring
DB_USERNAME=root
DB_PASSWORD=
```

Catatan penting:

- `APP_DEBUG=true` boleh digunakan saat development karena membantu melihat error teknis.
- `APP_DEBUG=false` wajib digunakan saat aplikasi sudah dipakai banyak orang.
- Jika `APP_DEBUG=true` dibiarkan di production, detail error teknis bisa terlihat oleh user.
- Pastikan `APP_URL` sesuai alamat aplikasi yang dibuka user.

## 2. Cek Storage Link

File upload aplikasi disimpan di:

```text
storage/app/public
```

Agar file upload bisa dibuka dari browser, jalankan:

```bash
php artisan storage:link
```

Folder upload penting:

- `storage/app/public/ie-requests`
- `storage/app/public/workshop-progress`
- `storage/app/public/final-checks`
- `storage/app/public/handovers`
- `storage/app/public/request-comments`

Jika file upload tidak bisa dibuka:

1. Cek apakah file benar-benar ada di `storage/app/public`.
2. Jalankan ulang `php artisan storage:link`.
3. Pastikan link file di aplikasi menggunakan format `storage/...`.

## 3. Cek Cache Command

Command setelah deploy atau setelah ada perubahan route, config, cache, atau view:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Jika aplikasi sudah production dan route/config/view sudah stabil, cache bisa dibuat dengan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Catatan:

- Untuk development lokal, cache tidak wajib.
- Jika masih sering edit route atau view, lebih aman memakai command `clear`.
- Jika setelah edit halaman perubahan tidak muncul, jalankan `php artisan view:clear`.

## 4. Cek Akun Admin

Seeder sudah menyediakan admin default:

```text
email: admin@ie-monitoring.local
password: password
role: admin
```

Untuk membuat atau memperbarui data awal:

```bash
php artisan db:seed
```

Catatan keamanan:

- Setelah sistem mulai dipakai, password admin default wajib diganti.
- Jangan gunakan password `password` untuk pemakaian harian.
- Simpan akun admin hanya untuk user yang benar-benar berwenang.

## 5. Security Basic

Checklist keamanan dasar:

- Semua route aplikasi utama harus berada di middleware `auth`.
- Route master data Department dan Line / Area hanya untuk `admin`.
- Route User Management hanya untuk `admin`.
- Route Report dan Export CSV hanya untuk `admin` dan `manager`.
- Customer hanya boleh melihat request miliknya sendiri.
- File upload harus divalidasi dengan `mimes` dan `max size`.
- Delete action harus memakai konfirmasi sebelum submit.
- Jangan tampilkan error teknis ke user saat production, gunakan `APP_DEBUG=false`.

Hasil cek route terakhir:

- `routes/auth.php` hanya berisi route Laravel Breeze/auth.
- Route aplikasi utama berada di `routes/web.php`.
- Master Data memakai `role:admin`.
- User Management memakai `role:admin`.
- Report dan Export CSV memakai `role:admin,manager`.
- Request detail, dashboard, kanban, komentar, dan print berada di middleware `auth`.

Jika nanti menemukan route penting yang belum diberi auth/role, contoh pola perbaikan di `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        // route admin only
    });

    Route::middleware('role:admin,manager')->group(function () {
        // route admin dan manager
    });
});
```

## 6. Backup Policy

Backup minimal dilakukan seminggu sekali.

Yang wajib dibackup:

1. Database MySQL.
2. Folder upload `storage/app/public`.

Format nama file backup:

```text
ie_monitoring_db_YYYY-MM-DD.sql
ie_monitoring_storage_YYYY-MM-DD.zip
```

Contoh:

```text
ie_monitoring_db_2026-05-17.sql
ie_monitoring_storage_2026-05-17.zip
```

Rekomendasi lokasi backup:

- Hard disk lokal khusus backup.
- External drive.
- Folder server internal yang dibatasi aksesnya.

Jangan hanya backup database. File upload juga penting karena berisi memo, drawing, evidence, foto progress, dan attachment komentar.

## 7. Demo Flow Untuk Atasan

Alur demo singkat:

1. Login sebagai admin.
2. Buka Dashboard.
3. Tampilkan summary card, chart, pipeline, dan Perhatian Hari Ini.
4. Buka Request Monitoring.
5. Tambah Request.
6. Upload Memo.
7. Buka Memo Approval.
8. Approve Memo.
9. Buka Drawing Progress.
10. Assign drafter.
11. Start drawing.
12. Done drawing dan upload drawing file.
13. Buka Material / BOM.
14. Input BOM/material.
15. Buka Budget / PR.
16. Buat PR.
17. Submit PR.
18. Approve PR.
19. Mark as PO Created.
20. Buka Material Arrival.
21. Update material sampai Complete.
22. Buka Workshop Schedule.
23. Buat schedule workshop.
24. Ready to Work.
25. Buka Workshop Progress.
26. Update progress sampai Done.
27. Buka Final Check.
28. Final Check Passed OK.
29. Buka Handover.
30. Handover Received.
31. Pastikan request menjadi Closed.
32. Tampilkan Report.
33. Export CSV.
34. Print Work Order.
35. Tampilkan Kanban Board.

## 8. Checklist Siap Pakai

Checklist sebelum aplikasi dipakai:

- [ ] Login admin bisa.
- [ ] User role bisa diatur.
- [ ] Request bisa dibuat.
- [ ] Upload file bisa dibuka.
- [ ] Semua modul flow bisa jalan.
- [ ] Dashboard angka sesuai.
- [ ] Report export bisa.
- [ ] Print Work Order bisa.
- [ ] Backup database sudah dicoba.
- [ ] Restore database sudah dicoba.
- [ ] Backup folder `storage/app/public` sudah dicoba.
- [ ] `php artisan storage:link` sudah berjalan.
- [ ] `APP_DEBUG` siap dimatikan jika production.
- [ ] Password admin default sudah diganti sebelum dipakai harian.

## 9. Command Final Checking

Jalankan command berikut sebelum demo atau setelah deploy:

```bash
php artisan route:list
php artisan migrate:status
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

Jika autoload bermasalah:

```bash
composer dump-autoload
```

Jika file upload tidak terbuka:

```bash
php artisan storage:link
```

Jika CSS tidak berubah:

```bash
npm run dev
```

## 10. Catatan Production

Jika aplikasi sudah digunakan oleh banyak orang:

- Ubah `APP_ENV=production`.
- Ubah `APP_DEBUG=false`.
- Pastikan database sudah dibackup.
- Pastikan folder upload sudah dibackup.
- Ganti password admin default.
- Jangan edit file langsung di server tanpa backup.
- Catat perubahan penting setiap kali ada update aplikasi.

