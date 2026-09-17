<?php 
// 1. MENYALAKAN PELACAK EROR (Biar tidak blank putih lagi kalau ada salah koding)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Cek login
if($_SESSION['status'] != "login"){ // [cite: 2531]
    header("location:login.php?pesan=belum_login"); // [cite: 2532]
    exit;
}
include 'koneksi.php'; // [cite: 2535]

// 2. Ambil hitungan total masing-masing cluster untuk grafik lingkaran (Pie Chart)
$count_tinggi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Tinggi'")); // [cite: 2274]
$count_sedang = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Sedang'")); // [cite: 2274]
$count_rendah = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Rendah'")); // [cite: 2274]
$grand_total  = $count_tinggi + $count_sedang + $count_rendah;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Klasterisasi K-Means Pajak Reklame</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #fff; color: #000; }
        .garis-kop { border-bottom: 4px double #000; margin-top: 5px; margin-bottom: 25px; }
        .table th { background-color: #f1f2f6 !important; color: #000; font-size: 12px; text-transform: uppercase; font-weight: bold; text-align: center; }
        .table td { font-size: 12px; }
        .judul-kelompok { padding: 10px; font-weight: bold; font-size: 13px; margin-top: 25px; border-radius: 5px; text-transform: uppercase; }
        
        /* Menyembunyikan tombol cetak saat kertas PDF dicetak */
        @media print {
            .no-cetak { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-4">

    <div class="container no-cetak mb-4 text-end">
        <button onclick="window.print()" class="btn btn-danger fw-bold"><i class="bi bi-printer"></i> KLIK UNTUK SIMPAN JADI PDF</button>
        <a href="admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="container text-center">
        <h4 class="mb-1 fw-bold">BADAN PENGELOLA KEUANGAN DAN PENDAPATAN DAERAH</h4> <h5 class="mb-1 fw-bold">KABUPATEN SIMALUNGUN</h5> <p class="mb-0 small text-muted">Data Evaluasi Potensi Pajak Reklame - Wilayah Studi: Kecamatan Bandar</p> <div class="garis-kop"></div>
    </div>

    <div class="container">
        <h5 class="text-center fw-bold mb-4">LAPORAN HASIL ANALISIS K-MEANS CLUSTERING<br>ZONASI POTENSI PAJAK REKLAME</h5> <div class="row justify-content-center mb-5">
            <div class="col-md-5 text-center">
                <h6 class="fw-bold mb-3">Grafik Persentase Hasil Pembagian Cluster (Total: <?php echo $grand_total; ?> Data)</h6>
                <div style="width: 260px; height: 260px; margin: 0 auto;">
                    <canvas id="grafikLaporan"></canvas>
                </div>
            </div>
        </div>

        <?php 
        // Susunan Urutan Kelompok: Tinggi -> Sedang -> Rendah sesuai instruksi Ema
        $susunan_cluster = [
            ['label' => 'Potensi Tinggi (C1)', 'warna_bg' => 'bg-danger text-white', 'kunci' => 'Potensi Tinggi'],
            ['label' => 'Potensi Sedang (C2)', 'warna_bg' => 'bg-warning text-dark', 'kunci' => 'Potensi Sedang'],
            ['label' => 'Potensi Rendah (C3)', 'warna_bg' => 'bg-success text-white', 'kunci' => 'Potensi Rendah']
        ];

        foreach($susunan_cluster as $cls) {
            echo "<div class='judul-kelompok " . $cls['warna_bg'] . " mb-2'>KELOMPOK DATA: " . $cls['label'] . "</div>";
        ?>
            <table class="table table-bordered table-striped align-middle mb-4">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Pemilik</th>
                        <th>Jenis Reklame</th>
                        <th>Lokasi Objek</th>
                        <th width="12%">Ukuran</th>
                        <th width="15%">Nilai Pajak</th>
                        <th width="10%">Periode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Mengambil rincian data dengan LEFT JOIN agar aman jika ada relasi kosong
                    $sql_rincian = "SELECT tb_reklame.*, tb_cluster.label_cluster 
                                    FROM tb_reklame 
                                    LEFT JOIN tb_cluster ON tb_reklame.id_reklame = tb_cluster.id_reklame 
                                    WHERE tb_cluster.label_cluster = '".$cls['kunci']."'
                                    ORDER BY tb_reklame.id_reklame DESC"; // [cite: 1769]
                                    
                    $eksekusi = mysqli_query($koneksi, $sql_rincian);
                    $nomor = 1;
                    $subtotal_pajak = 0;
                    $jumlah_data_kelompok = mysqli_num_rows($eksekusi);

                    if($jumlah_data_kelompok == 0) {
                        echo "<tr><td colspan='7' class='text-center text-muted py-3'>Tidak ada data dalam kelompok cluster ini.</td></tr>";
                    } else {
                        while($row = mysqli_fetch_array($eksekusi)) {
                            $subtotal_pajak += $row['nilai_pajak']; // [cite: 1773]
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $nomor++; ?></td>
                            <td class="fw-bold"><?php echo $row['nama_pemilik']; ?></td> <td><?php echo $row['jenis_reklame']; ?></td> <td><?php echo $row['lokasi']; ?></td> <td class="text-center"><?php echo $row['panjang']; ?>m x <?php echo $row['lebar']; ?>m</td> <td class="fw-bold text-end">Rp <?php echo number_format($row['nilai_pajak'], 0, ',', '.'); ?></td> <td class="text-center"><?php echo $row['periode_pajak']; ?></td>
                        </tr>
                    <?php 
                        }
                    }
                    ?>
                    <tr class="table-light fw-bold" style="border-top: 2px solid #000;">
                        <td colspan="5" class="text-end">JUMLAH OBJEK REKLAME KELOMPOK <?php echo strtoupper($cls['kunci']); ?> :</td>
                        <td colspan="2" class="text-center text-danger"><?php echo $jumlah_data_kelompok; ?> Objek Pajak</td>
                    </tr>
                    <tr class="table-light fw-bold">
                        <td colspan="5" class="text-end">SUBTOTAL POTENSI PENDAPATAN PAJAK :</td>
                        <td colspan="2" class="text-end text-primary">Rp <?php echo number_format($subtotal_pajak, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php 
        } 
        ?>

        <div class="card my-4" style="border: 2px dashed #000;">
            <div class="card-body bg-light p-3">
                <div class="row align-items-center">
                    <div class="col-7">
                        <h6 class="m-0 fw-bold text-uppercase" style="letter-spacing: 1px;">TOTAL KESELURUHAN DATA OBJEK REKLAME (GRAND TOTAL) :</h6>
                    </div>
                    <div class="col-5 text-end">
                        <h4 class="m-0 fw-bold text-success"><?php echo $grand_total; ?> DATA REKLAME TERKLASTERISASI</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Membuat grafik lingkaran sesuai angka riil di database
        const ctxLaporan = document.getElementById('grafikLaporan').getContext('2d');
        new Chart(ctxLaporan, {
            type: 'pie',
            data: {
                labels: ['Tinggi (C1)', 'Sedang (C2)', 'Rendah (C3)'],
                datasets: [{
                    data: [<?php echo $count_tinggi; ?>, <?php echo $count_sedang; ?>, <?php echo $count_rendah; ?>],
                    backgroundColor: ['#e74c3c', '#f1c40f', '#2ecc71'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Trigger otomatis popup cetak dokumen/simpan PDF setelah grafik ter-render sempurna
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>