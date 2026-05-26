# Demo Script IE Monitoring

## 1. Opening

Selamat pagi/siang Bapak/Ibu.

Pada kesempatan ini saya akan menjelaskan improvement digitalisasi monitoring request Industrial Engineering.

Improvement ini berupa aplikasi web **Monitoring Dashboard Industrial Engineering** yang digunakan untuk memantau request pekerjaan dari awal, mulai dari memo approval, drawing, material, workshop, final check, sampai handover dan closed.

Tujuan utamanya adalah membuat proses request lebih transparan, lebih mudah dipantau, dan lebih cepat diketahui statusnya oleh bagian terkait.

## 2. Background Masalah

Sebelumnya, proses request ke Industrial Engineering masih banyak dilakukan secara manual.

Request biasanya masuk melalui memo, kemudian proses approval dan follow up dilakukan melalui komunikasi langsung, chat, atau pengecekan manual.

Kondisi ini menyebabkan beberapa kendala.

Pertama, status request tidak selalu terlihat secara real time.

Kedua, admin perlu melakukan follow up manual ke drafter, purchasing, workshop, QC, atau user terkait.

Ketiga, jika ada request yang mendekati deadline atau sudah delay, informasinya tidak langsung terlihat dalam satu dashboard.

Selain itu, data request juga belum terpusat, sehingga sulit jika ingin ditampilkan di TV monitoring atau dijadikan bahan report ke atasan.

## 3. Tujuan Improvement

Improvement ini dibuat untuk membantu Industrial Engineering dalam memonitor seluruh request secara digital.

Dengan sistem ini, setiap request memiliki alur yang jelas, mulai dari customer request sampai handover.

Data request disimpan dalam satu sistem, sehingga admin, manager, drafter, purchasing, workshop, QC, dan customer dapat melihat informasi sesuai hak akses masing-masing.

Sistem ini juga membantu mengurangi follow up manual, mempercepat pengecekan status, dan memudahkan pembuatan report.

## 4. Penjelasan Flow Sistem

Flow utama sistem dimulai dari customer membuat request dan upload memo.

Setelah itu request masuk ke proses **Memo Approval**.

Jika memo sudah approved, proses berlanjut ke **Drawing Progress** untuk assignment drafter, start drawing, revision jika diperlukan, sampai drawing selesai.

Setelah drawing selesai, masuk ke **Material / BOM** untuk input kebutuhan material dan estimasi biaya.

Kemudian purchasing melanjutkan ke **Budget / PR**, mulai dari pembuatan PR sampai PO Created.

Setelah PO Created, material dimonitor di **Material Arrival** sampai status material complete.

Jika material sudah lengkap, workshop membuat jadwal di **Workshop Schedule**.

Kemudian progress pekerjaan diupdate melalui **Workshop Progress**.

Setelah pekerjaan selesai, dilakukan **Final Check** oleh QC.

Jika hasil final check OK, proses dilanjutkan ke **Handover**.

Setelah handover received, request berubah menjadi **Closed**.

## 5. Demo Step-by-Step

Untuk demo ini, skenario yang digunakan adalah request pembuatan trolley material untuk Welding Line 1.

Data contoh:

- Requester: Budi
- Department: Production
- Line / Area: Welding Line 1
- Request Type: Equipment
- Priority: High
- Target Date: 7 hari dari sekarang
- Description: Membuat trolley material untuk support proses welding.

Langkah demo:

1. Pertama, saya login sebagai admin.
2. Setelah login, kita masuk ke dashboard.
3. Di dashboard ini terlihat summary request, status pekerjaan, delay, due soon, dan perhatian hari ini.
4. Selanjutnya saya masuk ke menu Request Monitoring.
5. Saya klik Tambah Request.
6. Saya isi data request sesuai skenario, lalu upload memo.
7. Setelah request dibuat, kita masuk ke Memo Approval.
8. Di sini memo dapat direview, lalu saya approve.
9. Setelah memo approved, request masuk ke Drawing Progress.
10. Saya assign drafter, kemudian update status drawing menjadi start.
11. Setelah drawing selesai, saya update menjadi done dan upload file drawing.
12. Berikutnya masuk ke Material / BOM.
13. Saya input material yang dibutuhkan, termasuk qty, satuan, dan estimasi harga.
14. Setelah BOM tersedia, proses lanjut ke Budget / PR.
15. Saya buat PR, submit, approve, lalu mark as PO Created.
16. Setelah PO Created, saya masuk ke Material Arrival.
17. Di sini saya update kedatangan material sampai complete.
18. Jika material sudah complete, saya masuk ke Workshop Schedule.
19. Saya buat jadwal workshop dan set ready to work.
20. Kemudian masuk ke Workshop Progress.
21. Saya update progress menjadi 30 persen, lalu lanjut sampai 100 persen dan status Done.
22. Setelah workshop done, masuk ke Final Check.
23. QC melakukan final check dan hasilnya Passed OK.
24. Setelah final check OK, masuk ke Handover.
25. Saya proses handover sampai received.
26. Setelah handover received, status request menjadi Closed.
27. Setelah flow selesai, saya kembali ke Dashboard untuk melihat update summary.
28. Lalu saya buka Report untuk melihat data request dan export CSV.
29. Saya juga buka Print Work Order untuk menunjukkan dokumen kerja yang bisa dicetak atau disimpan sebagai PDF.
30. Terakhir, saya tampilkan Activity Log untuk melihat histori perubahan request dari awal sampai selesai.

## 6. Before-After

Sebelum improvement, follow up request masih dilakukan manual.

Data tersebar, status request tidak selalu transparan, dan admin perlu mengecek satu per satu ke bagian terkait.

Request yang delay juga sulit terlihat cepat karena belum ada dashboard khusus.

Setelah improvement, semua request masuk ke dashboard.

Status bisa dilihat secara real time, ada indikator due soon dan delay, ada activity log, ada report export, ada print work order, dan akses user diatur berdasarkan role.

Dengan begitu, proses monitoring menjadi lebih rapi dan lebih mudah dikontrol.

## 7. Benefit

Benefit untuk customer adalah request menjadi lebih jelas dan statusnya bisa dipantau.

Untuk admin IE, semua request bisa dimonitor dari satu dashboard, termasuk request urgent, due soon, dan delay.

Untuk drafter, drawing progress lebih mudah dikontrol dan file drawing terdokumentasi.

Untuk purchasing, PR dan material arrival lebih mudah dipantau.

Untuk workshop, jadwal dan progress pekerjaan lebih jelas.

Untuk manager, dashboard summary dan report bisa membantu review workload, delay, dan status request secara lebih cepat.

Secara keseluruhan, sistem ini membantu mengurangi follow up manual, meningkatkan transparansi, dan memudahkan report ke atasan.

## 8. Closing

Demikian demo improvement digitalisasi monitoring Industrial Engineering.

Dengan dashboard ini, proses request Industrial Engineering menjadi lebih transparan, terukur, dan mudah dimonitor dari awal sampai selesai.

Harapannya sistem ini bisa membantu pekerjaan harian IE, mempercepat follow up, dan menjadi dasar monitoring yang bisa ditampilkan di area kerja maupun TV dashboard.

Terima kasih.

