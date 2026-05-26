# SOP Penggunaan Sistem Monitoring Dashboard Industrial Engineering

## 1. Judul

**SOP Penggunaan Sistem Monitoring Dashboard Industrial Engineering**

## 2. Tujuan SOP

SOP ini dibuat untuk:

- Menstandarkan cara penggunaan sistem.
- Memastikan setiap request tercatat.
- Memastikan setiap proses update sesuai PIC.
- Mengurangi follow up manual.
- Mempermudah monitoring pekerjaan Industrial Engineering.

## 3. Ruang Lingkup

SOP ini berlaku untuk proses:

- Request handling
- Memo approval
- Drawing
- Material / BOM
- Budget / PR
- Material arrival
- Workshop schedule
- Workshop progress
- Final check
- Handover
- Report

## 4. Role dan Tanggung Jawab

| Role | Tanggung Jawab |
|---|---|
| Admin IE | Membuat dan memonitor request, memastikan flow berjalan, membuat report, mengatur master data, dan mengatur user role jika memiliki akses admin. |
| Customer | Membuat request, upload memo, melihat status request, dan menerima hasil handover. |
| Drafter | Mengupdate drawing progress, upload drawing, dan input kebutuhan material / BOM. |
| Purchasing | Membantu proses Budget / PR dan mengupdate material arrival. |
| Workshop | Melihat schedule dan mengupdate progress pekerjaan. |
| QC | Melakukan final check dan menentukan OK / NG. |
| Manager | Melihat dashboard, melihat report, monitoring delay, dan monitoring workload. |

## 5. Alur Proses Sistem

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

- **Customer Request**: Request dibuat dan data kebutuhan diinput.
- **Memo Approval**: Memo direview dan disetujui atau ditolak.
- **Drawing Progress**: Drafter mengerjakan drawing sampai selesai.
- **Material / BOM**: Kebutuhan material dan estimasi biaya diinput.
- **Budget / PR**: PR dibuat dan diproses sampai PO Created.
- **Material Arrival**: Kedatangan material diupdate.
- **Workshop Schedule**: Jadwal pengerjaan workshop dibuat.
- **Workshop Progress**: Progress pekerjaan workshop diupdate.
- **Final Check**: QC melakukan pengecekan akhir.
- **Handover**: Hasil pekerjaan diserahterimakan.
- **Closed**: Request selesai dan ditutup.

## 6. SOP Login

1. Buka browser.
2. Masuk ke URL aplikasi.
3. Input email dan password.
4. Klik **Login**.
5. Pastikan dashboard terbuka.

## 7. SOP Membuat Request Baru

Untuk Admin IE / Customer:

1. Buka menu **Request Monitoring**.
2. Klik **Tambah Request**.
3. Isi data berikut:
   - Tanggal request
   - Requester
   - Department
   - Line / Area
   - Jenis request
   - Priority
   - Target date
   - Description
4. Upload memo jika ada.
5. Klik **Simpan Request**.
6. Pastikan request muncul di list.

## 8. SOP Memo Approval

Untuk Admin / Manager:

1. Buka menu **Memo Approval**.
2. Pilih request.
3. Klik **Lihat Memo**.
4. Jika sesuai, klik **Approve**.
5. Jika tidak sesuai, klik **Reject** dan isi alasan.
6. Request hanya lanjut ke drawing jika memo **Approved**.

## 9. SOP Drawing Progress

Untuk Drafter:

1. Buka **Drawing Progress**.
2. Pilih request yang memo-nya **Approved**.
3. Assign drafter.
4. Klik **Start Drawing**.
5. Jika ada revisi, pilih **Revision** dan isi catatan.
6. Jika selesai, upload drawing dan klik **Done**.

## 10. SOP Material / BOM

Untuk Drafter / Admin IE:

1. Buka **Material / BOM**.
2. Pilih request dengan drawing **Done**.
3. Input material:
   - Nama material
   - Spesifikasi
   - Qty
   - Unit
   - Estimasi harga
   - Status material
4. Simpan material.
5. Pastikan total estimasi biaya muncul.

## 11. SOP Budget / PR

Untuk Admin IE / Purchasing:

1. Buka **Budget / PR**.
2. Pilih request yang sudah punya BOM.
3. Buat PR.
4. Submit PR.
5. Jika disetujui, klik **Approve**.
6. Jika sudah dibuat PO, klik **PO Created**.
7. Jika ditolak, klik **Reject** dan isi alasan.

## 12. SOP Material Arrival

Untuk Purchasing / Admin IE:

1. Buka **Material Arrival**.
2. Pilih request dengan PR status **PO Created**.
3. Update arrived qty.
4. Jika sebagian datang, status menjadi **Partial Arrived**.
5. Jika semua qty datang, status menjadi **Complete**.

## 13. SOP Workshop Schedule

Untuk Admin IE / Workshop:

1. Buka **Workshop Schedule**.
2. Pilih request yang materialnya **Complete**.
3. Buat schedule:
   - Planned start
   - Planned finish
   - PIC workshop
   - Estimated duration
4. Klik **Buat Schedule**.
5. Jika siap dikerjakan, klik **Ready to Work**.
6. Jika jadwal berubah, gunakan **Reschedule** dan isi alasan.

## 14. SOP Workshop Progress

Untuk Workshop:

1. Buka **Workshop Progress**.
2. Pilih schedule.
3. Update progress:
   - Not Started
   - On Progress
   - Hold
   - Rework
   - Done
4. Isi percentage progress.
5. Jika **Hold** atau **Rework**, wajib isi problem note.
6. Upload foto/file progress jika perlu.
7. Jika selesai, pilih **Done** dan percentage **100%**.

## 15. SOP Final Check

Untuk QC / Admin IE:

1. Buka **Final Check**.
2. Pilih request workshop **Done**.
3. Buat final check.
4. Klik **Start Checking**.
5. Jika hasil OK, klik **Passed**.
6. Jika ada masalah, pilih **Need Rework** atau **Failed** dan isi catatan masalah.

## 16. SOP Handover

Untuk Admin IE / Manager / Customer:

1. Buka **Handover**.
2. Pilih request final check **Passed OK**.
3. Buat handover.
4. Klik **Start Handover**.
5. Jika customer menerima hasil, klik **Received**.
6. Setelah **Received**, request menjadi **Closed**.
7. Jika customer menolak, klik **Reject** dan isi alasan.

## 17. SOP Report

Untuk Admin / Manager:

1. Buka **Report**.
2. Pilih filter:
   - Tanggal
   - Status
   - Priority
   - Department
   - Deadline
3. Klik **Terapkan Filter**.
4. Klik **Export CSV** jika perlu.
5. Buka file CSV di Excel.

## 18. SOP Kanban Board

1. Buka **Kanban Board**.
2. Lihat request berdasarkan status.
3. Gunakan filter department, priority, atau deadline.
4. Klik **Detail** untuk membuka request.

## 19. SOP Print Work Order

1. Buka **Detail Request**.
2. Klik **Print Work Order**.
3. Cek data request.
4. Klik **Print**.
5. Pilih printer atau **Save as PDF**.

## 20. SOP Komentar Request

1. Buka **Detail Request**.
2. Scroll ke **Komentar / Catatan Progress**.
3. Isi komentar.
4. Upload attachment jika perlu.
5. Klik **Simpan Komentar**.

## 21. SOP Activity Log

Activity log otomatis mencatat perubahan penting pada request.

Catatan:

- User tidak perlu input activity log secara manual.
- Activity log digunakan untuk audit histori request.
- Activity log membantu melihat siapa yang melakukan update dan kapan update dilakukan.

## 22. SOP TV Dashboard

1. Buka menu **TV Dashboard**.
2. Tekan **F11** untuk fullscreen.
3. Tampilkan di TV/lobby/area monitoring.
4. Dashboard auto refresh setiap 60 detik.

## 23. Aturan Pengisian Data

- Request harus memiliki description yang jelas.
- Memo/drawing harus diupload sesuai format.
- Status harus diupdate sesuai kondisi real.
- PIC wajib update progress tepat waktu.
- Catatan reject/rework/hold wajib jelas.
- Jangan menutup request sebelum handover diterima.

## 24. Format File Upload

Memo:

- PDF
- JPG
- PNG
- DOC
- DOCX

Drawing:

- PDF
- JPG
- PNG
- DWG
- DXF

Progress/Final/Handover evidence:

- JPG
- PNG
- PDF

## 25. Troubleshooting User

| Masalah | Solusi |
|---|---|
| Tidak bisa login | Cek email/password, hubungi admin. |
| Menu tidak muncul | Role user belum sesuai, hubungi admin. |
| File tidak bisa dibuka | Hubungi Admin IE / IT, cek storage link. |
| Data tidak muncul di modul berikutnya | Pastikan tahap sebelumnya sudah selesai. Contoh: Drawing Progress hanya muncul jika memo Approved. |
| Tidak bisa update progress Done | Pastikan percentage 100%. |

## 26. Catatan Penting

- Setiap PIC wajib update sesuai tanggung jawab.
- Data di dashboard tergantung kedisiplinan update.
- Backup dilakukan oleh admin/IT.
- Jangan share akun pribadi ke orang lain.

## 27. Penutup

Dengan SOP ini, penggunaan sistem Monitoring Dashboard Industrial Engineering diharapkan lebih konsisten, transparan, dan mudah dikontrol dari request sampai handover.

