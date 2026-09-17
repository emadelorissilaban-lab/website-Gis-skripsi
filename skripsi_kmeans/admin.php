<?php 
session_start();
// Cek apakah user sudah login?
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit;
}
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Pajak Reklame & GIS BPKPD </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f0f2f5; 
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

    /* Animasi Bergoyang Otomatis */
    @keyframes floating {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-5px) rotate(1deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    /* Animasi Cahaya Berdenyut di Belakang */
    @keyframes pulse-glow {
        0% { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        50% { box-shadow: 0 10px 25px rgba(255,255,255,0.3); }
        100% { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    }

    .card-counter {
        position: relative;
        padding: 25px 20px;
        border-radius: 15px;
        color: #fff;
        overflow: hidden;
        border: none;
        z-index: 1;
        animation: floating 4s ease-in-out infinite; /* Kartu otomatis bergoyang pelan */
        transition: all 0.3s ease;
    }

    /* Warna Gradien yang Senada dengan Pin Peta */
    .bg-total { background: linear-gradient(45deg, #3498db, #2980b9); } /* Biru */
    .bg-tinggi { background: linear-gradient(45deg, #e74c3c, #c0392b); animation-delay: 0s; }  /* Merah Pin */
    .bg-sedang { background: linear-gradient(45deg, #f1c40f, #f39c12); animation-delay: 1s; }  /* Kuning Pin */
    .bg-rendah { background: linear-gradient(45deg, #2ecc71, #27ae60); animation-delay: 2s; }  /* Hijau Pin */

    /* Ikon Transparan di Pojok */
    .card-counter i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 4.5rem;
        opacity: 0.25;
        z-index: 0;
    }

    .card-counter h3, .card-counter span {
        position: relative;
        z-index: 2;
    }

    .card-counter h3 { font-size: 2.2rem; margin: 0; }
    .card-counter span { font-size: 1rem; font-weight: 500; }

    /* Efek saat disentuh tetap ada agar lebih interaktif */
    .card-counter:hover {
        transform: scale(1.05) !important;
        animation-play-state: paused; /* Berhenti goyang saat disentuh agar stabil */
        box-shadow: 0 15px 30px rgba(0,0,0,0.3) !important;
    }

        /* --- Animasi Background Particles --- */
        .bg-area {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* CSS untuk Dropdown Melayang di Peta */
        .map-overlay-select {
            position: absolute;
            top: 15px; /* Jarak dari judul peta */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 12px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #3498db;
        }
        .map-overlay-select label { font-size: 11px; font-weight: bold; margin: 0; color: #555; }
        .map-overlay-select select { border: none; font-size: 11px; font-weight: bold; outline: none; background: transparent; cursor: pointer; color: #3498db; text-transform: uppercase; }
        
        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            animation: animate 25s linear infinite;
            bottom: -150px;
        }

        /* Warna Kabur Cluster */
        .c-red { background: rgba(255, 107, 107, 0.15); color: rgba(255, 107, 107, 0.2); }
        .c-yellow { background: rgba(254, 202, 87, 0.15); color: rgba(254, 202, 87, 0.2); }
        .c-green { background: rgba(29, 209, 161, 0.15); color: rgba(29, 209, 161, 0.2); }

        .circles li.pin { background: transparent !important; font-size: 30px; }

        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; border-radius: 50%; }
        .circles li:nth-child(2) { left: 10%; width: 30px; height: 30px; animation-delay: 2s; animation-duration: 12s; border-radius: 5px; }
        .circles li:nth-child(3) { left: 70%; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; border-radius: 5px; }
        .circles li:nth-child(5) { left: 65%; width: 40px; height: 40px; animation-delay: 0s; border-radius: 50%; }
        .circles li:nth-child(6) { left: 85%; width: 110px; height: 110px; animation-delay: 3s; border-radius: 50%; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; border-radius: 10px;}
        .circles li:nth-child(8) { left: 50%; animation-delay: 15s; animation-duration: 45s; }
        .circles li:nth-child(9) { left: 20%; width: 25px; height: 25px; animation-delay: 2s; animation-duration: 35s; border-radius: 50%; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }

        /* --- UI Elements --- */
        .widget-link { text-decoration: none; display: block; cursor: pointer; }
        .widget-link:hover .card-counter { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .card-counter { box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; border-radius: 12px; padding: 20px; color: white; transition: .3s; position: relative; overflow: hidden; }
        .bg-primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        #peta_dashboard { height: 450px; width: 100%; border-radius: 0 0 15px 15px; z-index: 1; }
        .map-card-header { border-radius: 15px 15px 0 0 !important; border-bottom: none; }
        .map-container { border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: none; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); }
        .table-card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); }
        .btn-custom { border-radius: 50px; padding: 6px 20px; font-weight: 600; font-size: 14px; }
        .pin-icon { background: transparent; border: none; text-align: center; }
        .pin-icon i { font-size: 2.5rem; filter: drop-shadow(3px 5px 2px rgba(0,0,0,0.3)); display: block; }
   

                /* Trik membuat dropdown Laporan Akhir terbuka otomatis saat diarahkan kursor */
        .menu-laporan:hover .dropdown-menu {
            display: block;
            margin-top: 0;
}
   
   </style>
</head>
<body>

<div class="bg-area">
    <ul class="circles">
        <li class="c-red"></li>
        <li class="c-green"></li>
        <li class="pin c-yellow"><i class="bi bi-geo-alt-fill"></i></li>
        <li class="c-red"></li>
        <li class="c-yellow"></li>
        <li class="pin c-green"><i class="bi bi-geo-alt-fill"></i></li>
        <li class="c-white"></li>
        <li class="pin c-red"><i class="bi bi-geo-alt-fill"></i></li>
        <li class="c-green"></li>
    </ul>
</div>

<nav class="navbar navbar-dark bg-dark shadow mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin.php"><i class="bi bi-grid-fill"></i> DASHBOARD GIS PAJAK REKLAME BPKPD SIMALUNGUN</a>
    <div>
        <span class="text-white me-3 small">Halo, <?php echo $_SESSION['username']; ?>!</span>
        <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </div>
  </div>
</nav>

<div class="container mb-5">

    <div class="row mb-4">
        <?php
        $total    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_reklame"));
        $cluster1 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Tinggi'"));
        $cluster2 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Sedang'"));
        $cluster3 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Rendah'"));
        ?>
    
    <div class="col-md-3 mb-2">
        <a href="admin.php" class="text-decoration-none">
            <div class="card-counter bg-total">
                <i class="fas fa-map-marked-alt"></i> 
                <h3 class="fw-bold"><?php echo $total; ?></h3>
                <span>Total Titik</span>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-2">
        <a href="admin.php?filter=Potensi Tinggi" class="text-decoration-none">
            <div class="card-counter bg-tinggi">
                <i class="fas fa-chart-line"></i>
                <h3 class="fw-bold"><?php echo $cluster1; ?></h3>
                <span>Pajak Tinggi (C1)</span>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-2">
        <a href="admin.php?filter=Potensi Sedang" class="text-decoration-none">
            <div class="card-counter bg-sedang">
                <i class="fas fa-wallet"></i>
                <h3 class="fw-bold"><?php echo $cluster2; ?></h3>
                <span>Pajak Sedang (C2)</span>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-2">
        <a href="admin.php?filter=Potensi Rendah" class="text-decoration-none">
            <div class="card-counter bg-rendah">
                <i class="fas fa-coins"></i>
                <h3 class="fw-bold"><?php echo $cluster3; ?></h3>
                <span>Pajak Rendah (C3)</span>
            </div>
        </a>
    </div>
</div>

    <div class="card map-container mb-4">
        <div class="card-header map-card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <div class="d-flex flex-column">
        <h5 class="m-0 fw-bold text-secondary">
            <i class="bi bi-geo-alt-fill text-primary"></i> Peta Sebaran Pajak Reklame
        </h5>
        <small class="fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 2px; color: #007bff;">
         Wilayah Studi: Kecamatan Bandar, Simalungun
        </small>
    </div>

    <div class="d-flex align-items-center bg-light px-3 py-1 rounded-pill border shadow-sm" style="min-width: 280px;">
        <label class="me-2 mb-0 small fw-bold text-secondary" style="font-size: 12px; white-space: nowrap;">
            <i class="fas fa-map-marked-alt text-primary"></i> PILIH KECAMATAN :
        </label>
        <select id="pilihKecamatan" class="form-select form-select-sm border-0 bg-transparent fw-bold text-primary" style="outline: none; box-shadow: none; cursor: pointer;">
            <option value="Bandar" selected>BANDAR</option>
            <option value="Bandar Huluan">BANDAR HULUAN </option>
            <option value="Bandar Masilam">Bandar Masilam</option>
            <option value="Bosar Maligas">BOSAR MALIGAS</option>
            <option value="Dolog Masagal">DOLOG MASAGAL </option>
            <option value="Dolok Batu Nanggar">DOLOK BATU NANGGAR</option>
            <option value="Dolok Panribuan">DOLOK PANRIBUAN</option>
            <option value="Dolok Pardamean">DOLOK PARDAMEAN</option>
            <option value="Dolok Silau">DOLOK SILAU</option>
            <option value="Girsang Sipangan Bolon">GIRSANG SIPANGAN BOLON</option>
            <option value="Gunung Malela">GUNUNG MALELA </option>
            <option value="Gunung Maligas">GUNUNG MALIGAS </option>
            <option value="Haranggaol Horison">HARANGGAOL HORISON </option>
            <option value="Hatonduhan">HATONDUHAN </option>
            <option value="Huta Bayu Raja">HUTABAYU RAJA</option>
            <option value="Jawa Maraja Bah Jambi>">JAWA MARABAJA BAH JAMBI</option>
            <option value="Jorlang Hataran">JORLANG HATARAN</option>
            <option value="Pematang Silimahuta">PEMATANG SILIMAHUTA</option>
            <option value="Panei">PANEI</option>
            <option value="Panombeian Panei">PANOMBEIAN PANEI</option>
            <option value="Pematang Bandar">PEMATANG BANDAR</option>
            <option value="Pematang Sidamanik">PEMATANG SIDAMANIK</option>
            <option value="Purba">PURBA</option>
            <option value="Raya">RAYA</option>
            <option value="Raya Kahean">RAYA KAHEAN</option>
            <option value="Siantar">SIANTAR</option>
            <option value="Sidamanik">SIDAMANIK</option>
            <option value="Silimakuta">SILIMAKUTA</option>
            <option value="Silao Kahean">SILAO KAHEANPURBA</option>
            <option value="Tanah Jawa">TANAH JAWA</option>
            <option value="Tapian Dolok">TAPIAN DOLOK</option>
            <option value="Ujung Padang">UJUNG PADANG</option>  
        </select>
    </div>
    </div>
        <div class="card-body p-0">
            <div id="peta_dashboard"></div>
        </div>
    </div>


  <div class="row align-items-center mt-4 mb-3">
    <!-- Kolom Judul Diperkecil menjadi col-md-4 agar hemat tempat -->
    <div class="col-md-4">
        <h3 class="fw-bold text-dark m-0">
            <i class="fas fa-database text-primary me-2"></i> Data Reklame Terkini
        </h3>
        <p class="text-muted mb-0 small">Kelola data dan jalankan analisis K-Means.</p>
    </div>
    
    <!-- Kolom Tombol Diperlebar menjadi col-md-8 dan dikunci Flexbox (gap-2) agar WAJIB SATU BARIS -->
    <div class="col-md-8 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-2">
        
        <!-- 1. Grup Tombol Data (Tambah & Import) -->
        <div class="btn-group shadow-sm">
            <a href="tambah.php" class="btn btn-primary btn-sm px-3">
                <i class="fas fa-plus-circle me-1"></i> Tambah
            </a>
            <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="fas fa-file-csv me-1"></i> Import CSV
            </button>
        </div>

        <!-- 2. Grup Tombol K-Means (Hitung & Detail Iterasi) -->
        <div class="btn-group shadow-sm">
            <a href="hitung_kmeans.php" class="btn btn-dark btn-sm px-3">
                <i class="fas fa-sync-alt me-1"></i> Hitung
            </a>
            <a href="detail_kmeans.php" class="btn btn-info btn-sm px-3 text-white">
                <i class="fas fa-search-location me-1"></i> Detail Iterasi
            </a>
        </div>

        <!-- 3. Tombol Laporan Akhir Dropdown Melayang -->
        <div class="dropdown shadow-sm menu-laporan">
            <button class="btn btn-sm text-white dropdown-toggle px-3 fw-bold" type="button" id="dropdownLaporan" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #6c5ce7; border: none; line-height: 1.5; padding-top: .25rem; padding-bottom: .25rem;">
                <i class="fas fa-file-alt me-1"></i> Laporan Akhir
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-1" aria-labelledby="dropdownLaporan" style="border-radius: 10px; z-index: 9999;">
                <li>
                    <a class="dropdown-item py-2 fw-bold text-danger" href="cetak_laporan.php" target="_blank">
                        <i class="fas fa-file-pdf me-2"></i> Cetak PDF (Grafik)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 fw-bold text-success" href="export_excel.php">
                        <i class="fas fa-file-excel me-2"></i> Ekspor Excel (Data)
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>

<div class="modal fade" id="modalImport" data-bs-backdrop="false" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow" style="border-radius: 15px;">
      
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold" id="modalImportLabel"><i class="bi bi-file-earmark-excel"></i> Import Massal Data Reklame</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-warning small mb-3">
          <strong><i class="bi bi-info-circle"></i> Perhatian:</strong><br />
          Pastikan format data Excel (CSV) yang akan diunggah sesuai dengan standar kolom pada sistem. Silakan klik tombol biru di bawah ini untuk mengunduh template format data pajak reklame terlebih dahulu.
        </div>
        
        <div class="text-center mb-4">
            <a href="template_reklame.csv" download class="btn btn-primary fw-bold shadow-sm w-100 py-2">
                <i class="bi bi-download"></i> DOWNLOAD TEMPLATE DATA PAJAK REKLAME
            </a>
            <span class="text-muted small d-block mt-1">*Klik tombol biru di atas untuk menyimpan template ke laptop Anda</span>
        </div>

        <hr />

        <form action="proses_import.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label small fw-bold">Pilih File CSV Yang Sudah Siap (.csv)</label>
            <input type="file" name="file_csv" accept=".csv" class="form-control" required />
          </div>
          
          <div class="modal-footer px-0 pb-0 pt-3 border-top-0">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="import" class="btn btn-success btn-sm fw-bold">Mulai Sinkronisasi</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<div class="card table-card shadow">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto; scrollbar-width: thin;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top shadow-sm" style="top: 0; z-index: 999;">
                    <tr class="small text-uppercase text-dark">
                        <th class="py-3 px-3">No</th>
                            <th>Nama Pemilik</th>
                            <th>Jenis Reklame</th>
                            <th>Lokasi</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Panjang</th>
                            <th>Lebar</th>
                            <th>Pajak (Rp)</th>
                            <th>Periode Pajak</th>
                            <th class="text-center">Cluster</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $filter = isset($_GET['filter']) ? "WHERE tb_cluster.label_cluster='".$_GET['filter']."'" : "";
                        $query_sql = "SELECT tb_reklame.*, tb_cluster.label_cluster 
                                    FROM tb_reklame 
                                    LEFT JOIN tb_cluster ON tb_reklame.id_reklame = tb_cluster.id_reklame 
                                    $filter 
                                    ORDER BY tb_reklame.id_reklame DESC";

                        $data_query = mysqli_query($koneksi, $query_sql);
                        $no = 1;
                        $data_peta = []; 

                        while($d = mysqli_fetch_array($data_query)){
                            $data_peta[] = [
                                'nama'    => $d['nama_pemilik'],
                                'jenis'   => $d['jenis_reklame'],
                                'lokasi'  => $d['lokasi'],
                                'lat'     => (float)$d['latitude'],
                                'lng'     => (float)$d['longitude'],
                                'panjang' => $d['panjang'],
                                'lebar'   => $d['lebar'],
                                'pajak'   => $d['nilai_pajak'],
                                'periode' => $d['periode_pajak'],
                                'cluster' => $d['label_cluster']
                            ];

                            $badge = "<span class='badge bg-secondary'>Belum Dihitung</span>";
                            if($d['label_cluster'] == "Potensi Tinggi") $badge="<span class='badge bg-danger'>Tinggi</span>";
                            if($d['label_cluster'] == "Potensi Sedang") $badge="<span class='badge bg-warning text-dark'>Sedang</span>";
                            if($d['label_cluster'] == "Potensi Rendah") $badge="<span class='badge bg-success'>Rendah</span>";
                        ?>
                        <tr class="small">
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo $d['nama_pemilik']; ?></td>
                            <td><span class="text-primary"><?php echo $d['jenis_reklame']; ?></span></td>
                            <td><?php echo $d['lokasi']; ?></td>
                            <td class="text-muted small"><?php echo $d['latitude']; ?></td>
                            <td class="text-muted small"><?php echo $d['longitude']; ?></td>
                            <td><?php echo $d['panjang']; ?></td>
                            <td><?php echo $d['lebar']; ?></td>
                            <td class="fw-bold">Rp <?php echo number_format($d['nilai_pajak'], 0, ',', '.'); ?></td>
                           <td class="fw-bold text-success"><?php echo $d['periode_pajak']; ?></td>
                            <td class="text-center"><?php echo $badge; ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $d['id_reklame']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                    <a href="proses_crud.php?aksi=hapus&id=<?php echo $d['id_reklame']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
    var map = L.map('peta_dashboard').setView([3.1492, 99.3106], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var dataReklame = <?php echo json_encode($data_peta); ?>;

    dataReklame.forEach(function(item){
        var warnaText = 'text-secondary';
        var ket = 'Belum Hitung';

        if(item.cluster == "Potensi Tinggi") { warnaText = 'text-danger'; ket='Tinggi'; }
        else if(item.cluster == "Potensi Sedang") { warnaText = 'text-warning'; ket='Sedang'; }
        else if(item.cluster == "Potensi Rendah") { warnaText = 'text-success'; ket='Rendah'; }

        var pinIcon = L.divIcon({
            className: 'pin-icon',
            html: `<i class="bi bi-geo-alt-fill ${warnaText}"></i>`,
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -35]
        });

        var marker = L.marker([item.lat, item.lng], { icon: pinIcon }).addTo(map);
        
        marker.bindPopup(`
            <div style="width: 200px; font-family: 'Poppins', sans-serif;">
                <div class="text-center mb-2">
                    <b class="${warnaText}" style="font-size: 1.1rem; text-transform: uppercase;">${item.nama}</b><br>
                    <span class="badge bg-light text-dark border">${item.jenis}</span>
                </div>
                <hr style="margin: 5px 0;">
                <div class="small">
                    <i class="bi bi-geo-alt-fill text-muted"></i> <b>Lokasi:</b> ${item.lokasi}<br>
                    <i class="bi bi-aspect-ratio text-muted"></i> <b>Ukuran:</b> ${item.panjang}m x ${item.lebar}m<br>
                    <i class="bi bi-cash-stack text-muted"></i> <b>Pajak:</b> <span class="text-success fw-bold">Rp ${parseInt(item.pajak).toLocaleString('id-ID')}</span>
                </div>
                <div class="mt-2 p-1 text-center border-top">
                    <small class="fw-bold">Potensi: <span class="${warnaText}">Cluster ${ket}</span></small>
                </div>
            </div>
        `);
    });

    // Daftar koordinat 32 Kecamatan Simalungun
    const koordinatKecamatan = {
        "Bandar": [3.1751, 99.3172],
        "Bandar Huluan": [3.2045, 99.2341],
        "Bandar Masilam": [3.2212, 99.3456],
        "Bosar Maligas": [3.0769, 99.3905],
        "Dolog Masagal": [2.9542, 98.9211],
        "Dolok Batu Nanggar": [3.1432, 99.1321],
        "Dolok Panribuan": [2.7831, 99.0342],
        "Dolok Pardamean": [2.8543, 98.8542],
        "Dolok Silau": [3.1234, 98.6872],
        "Girsang Sipangan Bolon": [2.6713, 98.9372],
        "Gunung Malela": [3.0453, 99.2312],
        "Gunung Maligas": [3.0475, 99.1415],
        "Haranggaol Horison": [2.8631, 98.7342],
        "Hatonduhan": [2.8365, 99.2811],
        "Huta Bayu Raja": [2.9432, 99.3321],
        "Jawa Maraja Bah Jambi>": [2.9542, 99.1873],
        "Jorlang Hataran": [2.8453, 99.1121],
        "Pematang Silimahuta": [2.9341, 98.7121],
        "Panei": [2.9123, 99.0012],
        "Panombeian Panei": [2.9765, 99.0542],
        "Pematang Bandar": [3.1362, 99.2731],
        "Pematang Sidamanik": [2.7932, 98.9021],
        "Purba": [2.9121, 98.7832],
        "Raya": [2.9731, 98.9242],
        "Raya Kahean": [3.1232, 98.9561],
        "Siantar": [2.9566, 99.1132],
        "Sidamanik": [2.8471, 98.9561],
        "Silimakuta": [2.9912, 98.6651],
        "Silao Kahean": [3.1765, 98.8654],
        "Tanah Jawa": [2.8876, 99.2393],
        "Tapian Dolok": [3.0561, 99.1023],
        "Ujung Padang": [3.1121, 99.5321]
    };

    // Fungsi gerak otomatis (FlyTo) saat dropdown dipilih
    document.getElementById('pilihKecamatan').addEventListener('change', function() {
        const val = this.value;
        const targetCoords = koordinatKecamatan[val];
        if (targetCoords) {
            map.flyTo(targetCoords, 13, {
                animate: true,
                duration: 1.5
            });
        }
    });
</script>
</body>
</html>