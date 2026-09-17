<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah ada proses hitung sebelumnya
if (!isset($_SESSION['history_kmeans']) || empty($_SESSION['history_kmeans'])) {
    echo "<script>alert('Silakan lakukan proses hitung K-Means terlebih dahulu!'); window.location='admin.php';</script> ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Perhitungan K-Means</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card-header { font-weight: bold; }
        .table-centroid { background-color: #f8f9fa; }
        .sticky-top-custom { position: sticky; top: 0; z-index: 1020; background: white; padding: 10px 0; border-bottom: 2px solid #dee2e6; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <div class="sticky-top-custom d-flex justify-content-between align-items-center">
        <h2>Detail Perhitungan Per Iterasi</h2>
        <a href="admin.php" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>

    <hr>

    <?php foreach ($_SESSION['history_kmeans'] as $h): ?>
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-primary text-white">
                ITERASI KE-<?php echo $h['iterasi']; ?>
            </div>
            <div class="card-body">
                
                <h5 class="text-primary">1. Centroid (Pusat Cluster) pada Iterasi ini:</h5>
                <table class="table table-bordered table-sm table-centroid mb-4">
                    <thead class="table-dark">
                        <tr>
                            <th>Cluster</th>
                            <th>Pajak (C1)</th>
                            <th>Latitude (C2)</th>
                            <th>Longitude (C3)</th>
                            <th>Jumlah Anggota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($h['centroids'] as $idx => $c): ?>
                        <tr>
                            <td><b>Cluster <?php echo $idx + 1; ?></b></td>
                            <td>Rp <?php echo number_format($c['pajak'], 0, ',', '.'); ?></td>
                            <td><?php echo $c['lat']; ?></td>
                            <td><?php echo $c['long']; ?></td>
                            <td><span class="badge bg-success"><?php echo $h['jumlah_anggota'][$idx]; ?> Data</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h5 class="text-primary">2. Detail Jarak Setiap Data ke Centroid:</h5>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-secondary sticky-top">
                            <tr>
                                <th>No</th>
                                <th>Nama Pemilik</th>
                                <th>Pajak</th>
                                <th>Jarak ke C1</th>
                                <th>Jarak ke C2</th>
                                <th>Jarak ke C3</th>
                                <th>Cluster Terpilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($h['detail'] as $no => $d): ?>
                            <tr>
                                <td><?php echo $no + 1; ?></td>
                                <td><?php echo $d['nama']; ?></td>
                                <td><?php echo number_format($d['pajak'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($d['jarak'][0], 4); ?></td>
                                <td><?php echo number_format($d['jarak'][1], 4); ?></td>
                                <td><?php echo number_format($d['jarak'][2], 4); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        if($d['cluster']==0) echo 'bg-danger'; 
                                        elseif($d['cluster']==1) echo 'bg-warning text-dark'; 
                                        else echo 'bg-success'; 
                                    ?>">
                                        Cluster <?php echo $d['cluster'] + 1; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="alert alert-info">
        <h5>Kesimpulan Konvergensi:</h5>
        Proses perhitungan berhenti karena pada iterasi terakhir, posisi <b>Centroid Baru</b> sudah sama dengan <b>Centroid Sebelumnya</b>. Data telah stabil (Konvergen).
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>