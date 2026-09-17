# Website GIS Skripsi: Pemetaan Zonasi Pajak Reklame

Website ini adalah Sistem Informasi Geografis (GIS) berbasis web yang mengintegrasikan algoritma **K-Means Clustering** (Artificial Intelligence) untuk memetakan zonasi potensi pajak reklame. 
Studi kasus proyek ini dilakukan pada Badan Pengelola Keuangan dan Pendapatan Daerah (BPKPD) Kabupaten Simalungun (Kecamatan Bandar) guna membantu optimalisasi pendapatan daerah.

## Fitur Utama
**Pemetaan Interaktif:** Menampilkan titik lokasi reklame secara visual pada peta.
**K-Means Clustering:** Mengelompokkan area berdasarkan tingkat potensi pajaknya menggunakan algoritma Machine Learning/AI.
**Manajemen Data:** Mengelola data titik reklame dan melihat hasil analisis *clustering*.

## Teknologi yang Digunakan
**Bahasa Pemrograman:** PHP / Python 
**Frontend:** HTML, CSS, JavaScript
**Database:** MySQL
**Algoritma AI:** K-Means Clustering

## Screenshot Tampilan Website
### 1. Halaman Utama (Dashboard Terintegrasi)
<img width="709" height="731" alt="image" src="https://github.com/user-attachments/assets/080f5e1d-25e7-46ce-be1e-737e994ec485" />

Halaman ini menggunakan konsep *Single Page Interface* yang merangkum seluruh fungsionalitas sistem agar admin BPKPD dapat melakukan pengawasan dengan cepat. Fitur utama pada halaman ini meliputi:
**Statistik Dinamis (Widget):** Menampilkan *real-time* total objek pajak reklame dan hasil klasifikasinya (Pajak Tinggi, Sedang, dan Rendah).
**Peta Interaktif (Web GIS):** Memvisualisasikan titik koordinat reklame menggunakan **Leaflet.js**. Warna *marker* pada peta otomatis menyesuaikan dengan hasil perhitungan algoritma **K-Means Clustering**, dilengkapi fitur *popup* untuk melihat detail tiap objek.
**Tabel Manajemen Data:** Menampilkan *database* seluruh reklame yang terintegrasi langsung. Tabel ini berfungsi sebagai data input yang siap diproses oleh algoritma AI secara langsung dari halaman yang sama.

### 2. Fitur Manajemen Data Reklame (CRUD)
<img width="709" height="583" alt="image" src="https://github.com/user-attachments/assets/b9ab57b5-7562-45e3-a934-e94cf4ef6ac4" />
<img width="709" height="582" alt="image" src="https://github.com/user-attachments/assets/4f954438-db76-4557-8ad5-7a2ef96ed23e" />

**Penjelasan:** 
Sistem menyediakan antarmuka form yang *clean* dan intuitif untuk kebutuhan administrasi data. Admin dapat melakukan operasi CRUD (Create, Update, Delete) untuk mengelola data objek pajak baru. Form ini dirancang untuk memastikan kelengkapan atribut (seperti koordinat spasial dan nilai pajak) sebelum data diproses lebih lanjut oleh algoritma K-Means.

### 3. Transparansi Algoritma (Detail Iterasi K-Means)
<img width="591" height="1083" alt="image" src="https://github.com/user-attachments/assets/1e044cfc-d387-4393-aa92-ff3a1e11ce48" />

**Penjelasan:** 
Sistem ini tidak beroperasi sebagai *black-box*. Halaman ini secara khusus dibuat untuk menampilkan proses perhitungan matematis algoritma K-Means secara detail dan transparan pada setiap iterasinya. Menampilkan pergerakan nilai *centroid* dan jarak data hingga mencapai titik **konvergen** (titik stabil di mana *cluster* tidak lagi berubah). Fitur ini berfungsi untuk memvalidasi keakuratan dan logika AI di balik hasil pemetaan.

### 4. Laporan Akhir & Ekspor Dokumen (Reporting)
<img width="709" height="615" alt="image" src="https://github.com/user-attachments/assets/9ef82575-bc7e-49c9-9775-4037a1f76b87" />

**Penjelasan:** 
Halaman ini adalah hasil *output* akhir sistem yang dirancang khusus untuk memenuhi kebutuhan administratif BPKPD dalam pengambilan keputusan. Hasil perhitungan rumit dari algoritma AI diterjemahkan menjadi laporan komprehensif dengan fitur utama:
**Visualisasi Terstruktur:** Menampilkan *Pie Chart* persentase hasil klaster secara dinamis. Data reklame diurutkan secara otomatis berdasarkan hierarki klaster (Potensi Tinggi, Sedang, Rendah) lengkap dengan kalkulasi *subtotal* pendapatan per kelompok.
**Fitur Ekspor Fleksibel:** Sistem menyediakan kemampuan *Export* dokumen ke dalam format **PDF** (siap cetak lengkap dengan kop surat resmi instansi) dan format **Excel** (untuk kebutuhan pengolahan angka lebih lanjut).
