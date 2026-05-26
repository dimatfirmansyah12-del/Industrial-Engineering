# Monitoring Dashboard Industrial Engineering

## 1. Judul Project

**Monitoring Dashboard Industrial Engineering**

## 2. Deskripsi Singkat

Website ini digunakan untuk monitoring request handling/workshop Industrial Engineering di area manufaktur. Sistem membantu memantau pekerjaan dari awal request dibuat, memo approval, drawing, kebutuhan material, proses purchasing, kedatangan material, schedule workshop, progress workshop, final check, sampai handover dan request ditutup.

Tujuan utama aplikasi ini adalah agar proses request lebih mudah dipantau, status pekerjaan lebih jelas, dan setiap bagian yang terlibat bisa melihat pekerjaan sesuai hak akses masing-masing.

## 3. Flow Sistem

Flow utama sistem:

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

## 4. Teknologi yang Digunakan

- Laravel
- Laravel Breeze
- Blade
- Tailwind CSS
- MySQL / XAMPP
- Chart.js
- VS Code
- Git Bash

## 5. Cara Menjalankan Project Setiap Hari

Langkah menjalankan project:

1. Buka XAMPP.
2. Start **Apache**.
3. Start **MySQL**.
4. Buka terminal di folder project.
5. Jalankan command:

```bash
php artisan serve
```

6. Buka terminal kedua di folder project.
7. Jalankan command:

```bash
npm run dev
```

8. Buka browser:

```text
http://127.0.0.1:8000
```

## 6. Command Penting Laravel

Command yang sering digunakan:

```bash
php artisan migrate
php artisan db:seed
php artisan route:list
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan storage:link
```

Keterangan singkat:

- `php artisan migrate` untuk menjalankan perubahan struktur database.
- `php artisan db:seed` untuk mengisi data awal jika seeder tersedia.
- `php artisan route:list` untuk melihat daftar route aplikasi.
- `php artisan view:clear` untuk membersihkan cache view Blade.
- `php artisan cache:clear` untuk membersihkan cache aplikasi.
- `php artisan route:clear` untuk membersihkan cache route.
- `php artisan config:clear` untuk membersihkan cache konfigurasi.
- `php artisan storage:link` untuk membuat akses file upload dari folder public.

## 7. Cara Backup Database Lewat phpMyAdmin

Langkah backup database:

1. Buka browser.
2. Masuk ke:

```text
http://localhost/phpmyadmin
```

3. Pilih database:

```text
ie_monitoring
```

4. Klik menu **Export**.
5. Pilih metode **Quick**.
6. Pilih format **SQL**.
7. Klik **Go**.
8. Simpan file `.sql` ke folder backup yang aman.

Contoh folder backup:

```text
D:\Backup IE Monitoring\Database
```

## 8. Cara Restore Database

Langkah restore database:

1. Buka phpMyAdmin.
2. Buat database baru dengan nama:

```text
ie_monitoring
```

3. Klik database tersebut.
4. Klik menu **Import**.
5. Pilih file backup `.sql`.
6. Klik **Go**.
7. Cek file `.env` project, pastikan nama database sudah sesuai.

Contoh konfigurasi `.env`:

```env
DB_DATABASE=ie_monitoring
DB_USERNAME=root
DB_PASSWORD=
```

8. Jika diperlukan, jalankan:

```bash
php artisan migrate
```

## 9. Cara Backup File Upload

File upload tersimpan di:

```text
storage/app/public
```

File tersebut bisa diakses oleh browser melalui:

```text
public/storage
```

Folder penting yang perlu dibackup:

- `storage/app/public/ie-requests`
- `storage/app/public/workshop-progress`
- `storage/app/public/final-checks`
- `storage/app/public/handovers`
- `storage/app/public/request-comments`

Simpan backup file upload di tempat yang aman, misalnya:

```text
D:\Backup IE Monitoring\Uploads
```

## 10. Cara Restore File Upload

Langkah restore file upload:

1. Copy folder backup upload ke:

```text
storage/app/public
```

2. Pastikan struktur folder tetap sama seperti sebelum backup.
3. Jalankan command:

```bash
php artisan storage:link
```

4. Buka data yang memiliki attachment untuk memastikan file bisa diakses.

## 11. Struktur Folder Penting

Folder dan file penting dalam project:

- `routes/web.php`
  Berisi semua route utama aplikasi.

- `app/Http/Controllers`
  Berisi controller untuk mengatur logic halaman dan proses data.

- `app/Models`
  Berisi model Laravel yang mewakili tabel database.

- `database/migrations`
  Berisi file perubahan struktur database.

- `database/seeders`
  Berisi file untuk mengisi data awal database.

- `resources/views`
  Berisi file tampilan Blade.

- `resources/views/components`
  Berisi component Blade seperti layout, sidebar, page header, dan summary card.

- `storage/app/public`
  Berisi file upload dari aplikasi.

## 12. Daftar Modul Website

Modul yang tersedia:

- Dashboard
- Request Monitoring
- Memo Approval
- Drawing Progress
- Material / BOM
- Budget / PR
- Material Arrival
- Workshop Schedule
- Workshop Progress
- Final Check
- Handover
- Report
- Kanban Board
- Print Work Order
- Master Data Department
- Master Data Line / Area
- User Management

## 13. Daftar Role User

Role yang tersedia:

- `admin`
  Memiliki akses penuh ke semua menu dan pengaturan.

- `manager`
  Melihat monitoring, approval, report, dan handover.

- `customer`
  Membuat request dan melihat request miliknya sendiri.

- `drafter`
  Mengelola proses drawing dan Material / BOM.

- `purchasing`
  Mengelola Budget / PR dan Material Arrival.

- `workshop`
  Mengelola Workshop Schedule dan Workshop Progress.

- `qc`
  Mengelola Final Check.

## 14. Troubleshooting Umum

### Masalah: Route not defined

Solusi:

```bash
php artisan route:clear
php artisan route:list
```

Cek juga apakah route sudah dibuat di:

```text
routes/web.php
```

### Masalah: View not found

Solusi:

1. Cek nama file di folder:

```text
resources/views
```

2. Jalankan:

```bash
php artisan view:clear
```

### Masalah: Component not found

Solusi:

1. Cek file component di:

```text
resources/views/components
```

2. Pastikan nama component sesuai dengan pemanggilan di Blade.

Contoh:

```blade
<x-dashboard-layout>
```

Berarti file component harus tersedia sesuai struktur Laravel.

### Masalah: File upload tidak bisa dibuka

Solusi:

```bash
php artisan storage:link
```

Pastikan file memang ada di:

```text
storage/app/public
```

### Masalah: Database error

Solusi:

1. Cek file `.env`.
2. Pastikan XAMPP MySQL aktif.
3. Pastikan nama database benar.
4. Jalankan:

```bash
php artisan migrate
```

### Masalah: CSS tidak berubah

Solusi:

```bash
npm run dev
```

Lalu refresh browser dengan:

```text
Ctrl + F5
```

### Masalah: Undefined variable

Solusi:

1. Cek controller halaman tersebut.
2. Pastikan variable dikirim ke view menggunakan `compact()`.

Contoh:

```php
return view('dashboard', compact('totalRequest'));
```

## 15. Checklist Sebelum Dipakai

Checklist pengecekan aplikasi:

- [ ] Bisa login
- [ ] Dashboard terbuka
- [ ] Bisa tambah request
- [ ] Bisa upload memo
- [ ] Bisa approve memo
- [ ] Bisa update drawing
- [ ] Bisa input BOM
- [ ] Bisa buat PR
- [ ] Bisa update material arrival
- [ ] Bisa buat schedule workshop
- [ ] Bisa update progress workshop
- [ ] Bisa final check
- [ ] Bisa handover
- [ ] Bisa export CSV
- [ ] Bisa print work order
- [ ] Bisa backup database

## 16. Catatan Maintenance

Catatan penting untuk maintenance:

- Backup database minimal seminggu sekali.
- Backup file upload juga, jangan hanya database.
- Jangan edit `routes/auth.php` sembarangan karena digunakan oleh Laravel Breeze.
- Semua route aplikasi utama ada di `routes/web.php`.
- Jika tambah menu baru, update sidebar component di `resources/views/components/sidebar.blade.php`.
- Jika tambah kolom database, buat migration baru.
- Jangan menghapus migration lama jika database sudah digunakan.
- Setelah mengubah route atau view, jalankan clear cache jika halaman belum berubah.

Command clear cache yang aman digunakan:

```bash
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

