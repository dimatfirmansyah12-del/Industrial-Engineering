# Materi Training Penggunaan Monitoring Dashboard Industrial Engineering

## 1. Judul

**Materi Training Penggunaan Monitoring Dashboard Industrial Engineering**

## 2. Tujuan Training

Training ini bertujuan agar:

- User memahami alur sistem.
- User bisa login.
- User tahu menu yang sesuai dengan rolenya.
- User bisa update proses sesuai tanggung jawab.
- User memahami pentingnya update data tepat waktu.
- User tahu cara melihat status, report, dan dashboard.

## 3. Peserta Training

Peserta training:

- Admin IE
- Customer / Requester
- Drafter
- Purchasing
- Workshop
- QC
- Manager

## 4. Durasi Training

Rekomendasi total durasi training: **2 jam**.

Pembagian waktu:

- 15 menit: pengenalan background masalah
- 15 menit: overview flow sistem
- 20 menit: demo dashboard dan request monitoring
- 20 menit: demo modul proses
- 20 menit: praktek user sesuai role
- 20 menit: report, kanban, print work order
- 10 menit: tanya jawab

## 5. Background Masalah

Sebelumnya proses request Industrial Engineering masih banyak dilakukan secara manual. Follow up sering dilakukan melalui chat atau komunikasi lisan. Akibatnya status pekerjaan sulit dilihat secara cepat, delay sulit terpantau, dan report masih perlu dibuat manual.

Dashboard ini dibuat untuk memusatkan data request dan monitoring agar setiap bagian bisa melihat status pekerjaan sesuai hak aksesnya.

## 6. Flow Sistem

```text
Request
-> Memo Approval
-> Drawing
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

- **Request**: Request dibuat oleh customer atau admin.
- **Memo Approval**: Memo direview dan disetujui atau ditolak.
- **Drawing**: Drafter mengerjakan drawing sampai selesai.
- **Material / BOM**: Material dan estimasi biaya diinput.
- **Budget / PR**: PR dibuat dan diproses sampai PO Created.
- **Material Arrival**: Kedatangan material diupdate.
- **Workshop Schedule**: Jadwal pekerjaan dibuat.
- **Workshop Progress**: Progress pekerjaan diupdate.
- **Final Check**: QC melakukan pengecekan akhir.
- **Handover**: Hasil pekerjaan diserahterimakan.
- **Closed**: Request selesai.

## 7. Penjelasan Role

### Admin IE

- Membuat request.
- Monitoring seluruh proses.
- Melihat dashboard.
- Membuat report.
- Mengatur master data.
- Mengatur user role.

### Customer

- Membuat request.
- Melihat status request.
- Menerima handover.

### Drafter

- Update drawing progress.
- Upload drawing.
- Input material / BOM.

### Purchasing

- Update Budget / PR.
- Update Material Arrival.

### Workshop

- Melihat schedule.
- Update progress pekerjaan.

### QC

- Melakukan final check.
- Menentukan OK / NG.

### Manager

- Monitoring dashboard.
- Review report.
- Melihat delay dan due soon.

## 8. Materi Training Admin IE

Checklist yang harus bisa:

- Login.
- Membuka dashboard.
- Membuat request.
- Upload memo/drawing.
- Melihat request monitoring.
- Menggunakan filter.
- Membuka detail request.
- Melihat activity log.
- Membuat report.
- Export CSV.
- Print Work Order.
- Mengatur master data department dan line area.
- Mengatur role user.

## 9. Materi Training Customer

Checklist yang harus bisa:

- Login.
- Membuat request.
- Mengisi data request dengan benar.
- Upload memo.
- Melihat status request.
- Membuka detail request.
- Memberi komentar.
- Melihat handover.

## 10. Materi Training Drafter

Checklist yang harus bisa:

- Membuka Drawing Progress.
- Assign drafter.
- Start drawing.
- Mengisi revision note.
- Upload drawing.
- Menandai drawing Done.
- Membuka Material / BOM.
- Input material.
- Edit material.
- Hapus material jika salah.
- Memastikan total estimasi benar.

## 11. Materi Training Purchasing

Checklist yang harus bisa:

- Membuka Budget / PR.
- Membuat PR.
- Submit PR.
- Approve/Reject PR sesuai role jika diperbolehkan.
- Mark as PO Created.
- Membuka Material Arrival.
- Update arrived qty.
- Menandai material Partial Arrived atau Complete.

## 12. Materi Training Workshop

Checklist yang harus bisa:

- Membuka Workshop Schedule.
- Melihat jadwal kerja.
- Membuka Workshop Progress.
- Update progress status.
- Input progress percentage.
- Isi problem note jika Hold/Rework.
- Upload foto/file progress.
- Menandai pekerjaan Done saat 100%.

## 13. Materi Training QC

Checklist yang harus bisa:

- Membuka Final Check.
- Membuat final check.
- Start checking.
- Menandai Passed jika OK.
- Menandai Need Rework jika perlu perbaikan.
- Menandai Failed jika NG.
- Mengisi problem note dan correction note.

## 14. Materi Training Manager

Checklist yang harus bisa:

- Membuka Dashboard.
- Membaca summary card.
- Membaca Attention Dashboard.
- Membaca Pipeline Monitoring.
- Membuka Report.
- Filter report.
- Export CSV.
- Membuka Kanban Board.
- Melihat detail request.
- Melihat activity log.

## 15. Simulasi Training

Skenario latihan:

**Nama request:** Pembuatan Trolley Material Welding Line 1

Data:

| Field | Data |
|---|---|
| Requester | Budi |
| Department | Production |
| Line / Area | Welding Line 1 |
| Request Type | Equipment |
| Priority | High |
| Target Date | 7 hari dari hari training |
| Description | Membuat trolley material untuk support proses welding. |

Langkah praktek:

1. Customer/Admin membuat request.
2. Upload memo.
3. Admin/Manager approve memo.
4. Drafter start drawing.
5. Drafter upload drawing dan Done.
6. Drafter/Admin input material BOM.
7. Purchasing buat PR sampai PO Created.
8. Purchasing update material arrival complete.
9. Workshop membuat schedule.
10. Workshop update progress sampai Done.
11. QC final check Passed.
12. Admin handover Received.
13. Manager cek dashboard dan report.
14. Admin print work order.
15. User cek activity log.

## 16. Kesalahan Umum Saat Training

| Kesalahan | Dampak | Solusi |
|---|---|---|
| Lupa upload memo | Request tidak bisa lanjut approval | Upload memo di request/edit. |
| Memo belum approved | Drawing tidak muncul | Approve memo dulu. |
| Drawing belum Done | BOM tidak muncul | Selesaikan drawing dulu. |
| PR belum PO Created | Material Arrival tidak muncul | Update PR sampai PO Created. |
| Material belum Complete | Workshop Schedule tidak muncul | Update arrived qty sampai lengkap. |
| Progress Done tapi percentage bukan 100 | Update gagal | Isi percentage 100%. |
| Final Check Need Rework | Harus kembali ke workshop | Update workshop progress ulang. |
| Handover belum Received | Request belum Closed | Selesaikan handover. |

## 17. Aturan Disiplin Update Data

- Setiap PIC wajib update status sesuai kondisi aktual.
- Catatan wajib diisi jika reject, hold, rework, failed, atau rejected handover.
- File upload harus sesuai format.
- Jangan menggunakan akun orang lain.
- Jangan menutup request sebelum handover diterima.
- Admin IE wajib review dashboard setiap hari.

## 18. Evaluasi Training

Checklist evaluasi:

- User bisa login.
- User tahu menu sesuai role.
- User bisa menjalankan tugas sesuai role.
- User paham flow proses.
- User paham kapan request masuk ke modul berikutnya.
- User bisa membaca dashboard.
- User bisa mencari request.
- User bisa memberi komentar.
- User tahu siapa yang harus dihubungi jika error.

## 19. Pertanyaan yang Mungkin Muncul

**Q: Kenapa menu saya tidak lengkap?**  
A: Karena menu mengikuti role user.

**Q: Kenapa request tidak muncul di Drawing Progress?**  
A: Karena memo belum Approved.

**Q: Kenapa request tidak muncul di Material / BOM?**  
A: Karena drawing belum Done.

**Q: Kenapa request tidak muncul di Workshop Schedule?**  
A: Karena material belum Complete.

**Q: Kenapa saya tidak bisa hapus komentar orang lain?**  
A: Hanya admin atau pemilik komentar yang bisa hapus.

**Q: Kenapa request customer lain tidak terlihat?**  
A: Role customer hanya melihat request miliknya sendiri.

## 20. Penutup

Training ini bertujuan agar setiap bagian dapat menggunakan sistem dengan konsisten, sehingga proses request Industrial Engineering menjadi lebih transparan, cepat dimonitor, dan mudah dievaluasi.

