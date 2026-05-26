# Final Project Summary IE Monitoring

## 1. Nama Project

**Monitoring Dashboard Industrial Engineering**

Platform web berbasis Laravel untuk monitoring request Industrial Engineering dari memo sampai handover.

## 2. Problem Utama

Sebelum improvement, proses request Industrial Engineering masih banyak dilakukan secara manual. Memo, approval, follow up, progress drawing, kebutuhan material, pekerjaan workshop, final check, dan handover belum terpusat dalam satu sistem.

Dampaknya:

- Tracking status request sulit dilakukan secara real time.
- Follow up masih manual.
- Request delay sulit diketahui cepat.
- Report membutuhkan pekerjaan manual.
- Data tidak transparan antar bagian.
- Histori perubahan request belum terdokumentasi rapi.

## 3. Solusi

Dibuat aplikasi **Monitoring Dashboard Industrial Engineering** berbasis web untuk memusatkan request IE dalam satu sistem.

Solusi utama:

- Setiap request memiliki status dan flow proses.
- Setiap modul menangani tahap pekerjaan tertentu.
- Dashboard menampilkan summary, delay, due soon, pipeline, dan attention list.
- Report dapat difilter dan diexport CSV.
- Work order dapat dicetak.
- TV dashboard dapat ditampilkan di area kerja.
- Role user digunakan untuk membatasi akses.

## 4. Modul Utama

- Dashboard Final
- TV Dashboard
- Request Monitoring CRUD
- Memo Approval
- Drawing Progress
- Material / BOM
- Budget / PR
- Material Arrival
- Workshop Schedule
- Workshop Progress
- Final Check
- Handover
- Report dan Export CSV
- Kanban Board
- Print Work Order
- Activity Log
- Komentar Request
- User Management
- Master Data Department
- Master Data Line / Area

## 5. Benefit

Benefit yang diharapkan:

- Monitoring request lebih transparan.
- Status pekerjaan lebih mudah dilihat.
- Follow up manual berkurang.
- Request delay dan due soon lebih cepat diketahui.
- Report lebih mudah dibuat.
- Histori perubahan request terdokumentasi.
- Pekerjaan workshop lebih mudah diprioritaskan.
- Manager dapat melihat summary dan workload lebih cepat.
- Dashboard dapat ditampilkan di TV/lobby/area monitoring.

## 6. Status Project

Status project:

```text
Siap untuk demo dan uji coba penggunaan internal.
```

Yang sudah tersedia:

- Fitur utama request sampai handover.
- Role user dan hak akses.
- Dashboard dan TV dashboard.
- Report dan export CSV.
- Dokumentasi penggunaan, testing, demo, production readiness, dan final handover.
- Seeder data awal.
- Checklist testing manual.

Catatan:

Sebelum digunakan harian, lakukan testing final dengan data contoh dan ganti password admin default.

## 7. Next Improvement

Rekomendasi pengembangan berikutnya:

- Email notification.
- WhatsApp notification internal.
- QR code untuk Work Order.
- Approval bertingkat.
- Export PDF otomatis.
- SLA monitoring.
- Dashboard TV lebih interaktif.
- Analisis workload drafter dan workshop.
- Integrasi dengan sistem purchasing.
- Mobile upload progress photo.

