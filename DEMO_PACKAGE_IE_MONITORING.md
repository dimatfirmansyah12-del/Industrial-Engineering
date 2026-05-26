# Improvement Digitalisasi Monitoring Industrial Engineering

## 1. Judul

**Improvement Digitalisasi Monitoring Industrial Engineering**

## 2. Background / Latar Belakang

Sebelumnya proses request ke Industrial Engineering masih banyak dilakukan secara manual. Request dari produksi atau office umumnya menggunakan memo, lalu proses approval dan follow up dilakukan secara manual melalui komunikasi langsung, chat, atau pengecekan dokumen.

Kondisi tersebut membuat proses tracking pekerjaan menjadi kurang praktis. Admin IE sulit melihat status request secara real time, sementara drafter, purchasing, workshop, QC, manager, dan customer belum memiliki satu sumber data yang sama.

Selain itu, request berpotensi terlambat karena belum ada monitoring deadline yang jelas. Data juga sulit ditampilkan secara visual di TV, lobby, atau area monitoring karena belum tersedia dashboard terpusat.

## 3. Problem Sebelum Improvement

- Memo sering tertinggal atau tidak cepat ditindaklanjuti.
- Approval membutuhkan follow up manual.
- Tracking status request tidak transparan.
- Follow up masih dilakukan melalui chat, lisan, atau pengecekan manual.
- Admin sulit mengetahui pekerjaan yang sudah delay.
- Workshop sulit melihat prioritas pekerjaan.
- Report masih dibuat manual.
- Tidak ada histori perubahan status yang rapi.
- Tidak ada dashboard real time untuk monitoring.

## 4. Tujuan Improvement

Improvement ini bertujuan membuat dashboard monitoring Industrial Engineering berbasis web agar proses request dapat dipantau dari awal sampai selesai.

Tujuan utama:

- Membuat dashboard monitoring IE berbasis web.
- Memusatkan data request dalam satu sistem.
- Mempermudah tracking proses dari memo sampai handover.
- Mengurangi follow up manual.
- Mempercepat review status pekerjaan.
- Membuat data lebih transparan antar bagian.
- Memudahkan reporting.
- Memungkinkan dashboard ditampilkan di TV untuk monitoring.

## 5. Scope Sistem

Modul yang masuk dalam scope sistem:

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
- User Management
- Master Data Department
- Master Data Line / Area

## 6. Flow Sistem Baru

Flow sistem baru:

```text
Customer membuat request dan upload memo
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

Penjelasan singkat setiap tahap:

- **Customer membuat request dan upload memo**
  Customer atau admin membuat request baru, mengisi detail kebutuhan, target date, priority, dan melampirkan memo jika tersedia.

- **Memo Approval**
  Manager atau admin melakukan review dan approval memo sebelum request lanjut ke proses berikutnya.

- **Drawing Progress**
  Drafter melakukan proses drawing, mulai dari assignment, start drawing, revision jika diperlukan, sampai drawing done.

- **Material / BOM**
  Drafter atau admin menginput kebutuhan material dan estimasi biaya berdasarkan drawing atau kebutuhan pekerjaan.

- **Budget / PR**
  Purchasing membuat PR, submit, approve, reject jika perlu, sampai PO Created.

- **Material Arrival**
  Purchasing mengupdate kedatangan material, baik partial arrived maupun complete.

- **Workshop Schedule**
  Workshop membuat jadwal pengerjaan dan menentukan PIC workshop.

- **Workshop Progress**
  Workshop mengupdate progress pekerjaan, status pekerjaan, persentase progress, dan dokumentasi progress.

- **Final Check**
  QC melakukan pengecekan akhir, menentukan hasil OK, NG, atau need rework.

- **Handover**
  Pekerjaan diserahterimakan ke customer atau user terkait.

- **Closed**
  Request selesai dan status ditutup.

## 7. Before vs After

| Before | After |
|---|---|
| Manual follow up | Semua request masuk dashboard |
| Data tersebar | Data terpusat dalam satu sistem |
| Status tidak transparan | Status terlihat real time |
| Sulit tahu delay | Ada due soon dan delay |
| Report manual | Ada export report |
| Tidak ada history | Ada activity log |
| Approval tidak termonitor | Ada flow approval dan status approval |
| Work order belum rapi | Ada print work order |
| Akses data belum dibatasi | Ada role user |
| Flow pekerjaan tidak terlihat end-to-end | Ada flow dari request sampai handover |

## 8. Manfaat untuk Department

### Customer

- Bisa membuat request dengan informasi lebih jelas.
- Bisa melihat status request.
- Handover lebih terdokumentasi.

### Admin IE

- Mudah monitoring semua request.
- Mudah membuat report.
- Mudah melihat request delay dan priority.
- Lebih mudah melakukan follow up berdasarkan data.

### Drafter

- Drawing progress lebih terkontrol.
- Drawing file terdokumentasi.
- Request yang perlu drawing lebih mudah dipantau.

### Purchasing

- PR dan material arrival lebih jelas.
- Material yang belum lengkap bisa dimonitor.
- Status pembelian lebih mudah dilihat oleh bagian terkait.

### Workshop

- Jadwal kerja lebih jelas.
- Progress pekerjaan bisa diupdate.
- Prioritas pekerjaan lebih mudah diketahui.

### Manager

- Bisa melihat dashboard summary.
- Bisa melihat delay, due soon, dan workload.
- Bisa mengambil keputusan berdasarkan data yang lebih cepat terlihat.

## 9. Fitur Utama yang Akan Didemokan

1. Login sebagai admin.
2. Dashboard monitoring.
3. Tambah request.
4. Upload memo.
5. Approve memo.
6. Update drawing progress.
7. Input Material / BOM.
8. Buat Budget / PR.
9. Update Material Arrival.
10. Buat Workshop Schedule.
11. Update Workshop Progress.
12. Final Check.
13. Handover.
14. Report dan Export CSV.
15. Kanban Board.
16. Print Work Order.
17. Activity Log.
18. Komentar request.

## 10. Skenario Demo

Skenario demo:

**Request pembuatan trolley material untuk Welding Line 1.**

Data contoh:

| Field | Data |
|---|---|
| Requester | Budi |
| Department | Production |
| Line / Area | Welding Line 1 |
| Request Type | Equipment |
| Priority | High |
| Target Date | 7 hari dari sekarang |
| Description | Membuat trolley material untuk support proses welding. |

Step demo:

1. Login admin.
2. Tambah request.
3. Upload memo.
4. Masuk Memo Approval dan approve.
5. Masuk Drawing Progress, assign drafter, start, done upload drawing.
6. Masuk Material / BOM, input material.
7. Masuk Budget / PR, buat PR sampai PO Created.
8. Masuk Material Arrival, update material complete.
9. Masuk Workshop Schedule, buat jadwal.
10. Masuk Workshop Progress, update progress 30%, lalu 100% Done.
11. Masuk Final Check, Passed OK.
12. Masuk Handover, Received.
13. Request berubah Closed.
14. Buka Dashboard.
15. Buka Report dan Export CSV.
16. Buka Print Work Order.
17. Tampilkan Activity Log.

## 11. KPI / Dampak yang Diharapkan

- Mengurangi follow up manual.
- Mempercepat visibility status request.
- Mengurangi request terlambat.
- Mempermudah prioritas pekerjaan.
- Mempermudah audit histori request.
- Mempermudah report ke atasan.
- Mempermudah monitoring di TV dashboard.

## 12. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| User belum terbiasa input data | Training singkat dan SOP penggunaan |
| Data tidak update | Tentukan PIC tiap proses |
| File upload tidak rapi | Standar format upload memo/drawing |
| Server lokal mati | Backup database dan storage rutin |

## 13. Next Improvement

- Email notification.
- Approval digital bertingkat.
- Integrasi QR code work order.
- Upload foto progress via mobile.
- Dashboard TV mode.
- Export PDF otomatis.
- SLA monitoring.
- Analisis workload drafter/workshop.
- Integrasi dengan purchasing system.

## 14. Penutup

Dengan dashboard ini, proses request Industrial Engineering menjadi lebih transparan, terukur, dan mudah dimonitor dari awal sampai selesai.

