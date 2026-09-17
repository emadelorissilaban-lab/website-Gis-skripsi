<?php 
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit;
}
include 'koneksi.php'; 

// 1. Ambil ID dari URL
$id_reklame = $_GET['id'];

// 2. Ambil data lama berdasarkan id_reklame
$query = mysqli_query($koneksi, "SELECT * FROM tb_reklame WHERE id_reklame='$id_reklame'");
$data = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Reklame</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            background-color: #f0f2f5; 
            font-family: 'Poppins', sans-serif; 
            margin: 0;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        /* --- Animasi Background Partikel Pin Lokasi --- */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, #f0f2f5 0%, #e0e4e8 100%);
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

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            animation: animate 20s linear infinite;
            bottom: -150px;
        }

        /* Pin Lokasi dengan Warna Transparan (Merah, Kuning, Hijau) */
        .p-red { color: rgba(255, 59, 48, 0.5); font-size: 40px; }
        .p-yellow { color: rgba(255, 204, 0, 0.5); font-size: 35px; }
        .p-green { color: rgba(52, 199, 89, 0.5); font-size: 45px; }
        .p-blue { color: rgba(0, 122, 255, 0.3); font-size: 30px; }

        .circles li:nth-child(1) { left: 10%; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3) { left: 70%; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; animation-delay: 1s; }
        .circles li:nth-child(6) { left: 85%; animation-delay: 3s; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .circles li:nth-child(8) { left: 50%; animation-delay: 15s; animation-duration: 40s; }
        .circles li:nth-child(9) { left: 15%; animation-delay: 2s; animation-duration: 30s; }
        .circles li:nth-child(10){ left: 80%; animation-delay: 5s; animation-duration: 15s; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(360deg); opacity: 0; }
        }

        /* --- Styling Card Form --- */
        .card-form { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        /* Judul Warna Biru Sesuai Permintaan */
        .title-blue { color: #0d6efd; font-weight: 700; }
        
        .form-label { font-weight: 600; color: #495057; }
        .input-group-text { background-color: #f8f9fa; border-right: none; }
        .form-control, .form-select { border-radius: 10px; }
        .btn-success { border-radius: 10px; transition: 0.3s; background-color: #198754; border: none; }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3); }
    </style>
</head>
<body class="py-5 d-flex align-items-center min-vh-100">

    <div class="bg-animation">
        <ul class="circles">
            <li class="p-red"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="p-green"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="p-yellow"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="p-blue"><i class="bi bi-geo-fill"></i></li>
            <li class="p-red"><i class="bi bi-geo-fill"></i></li>
            <li class="p-green"><i class="bi bi-geo-alt"></i></li>
            <li class="p-yellow"><i class="bi bi-geo-fill"></i></li>
            <li class="p-blue"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="p-red"><i class="bi bi-geo-alt"></i></li>
            <li class="p-green"><i class="bi bi-geo-fill"></i></li>
        </ul>
    </div>

    <div class="container" style="max-width: 600px; position: relative; z-index: 10;">
        <div class="card card-form">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <i class="bi bi-pencil-square title-blue" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="title-blue">Edit Objek Pajak</h3>
                    <p class="text-muted small">Perbarui informasi data reklame di bawah ini</p>
                </div>

                <form action="proses_crud.php?aksi=update" method="post">
                    <input type="hidden" name="id_reklame" value="<?php echo $data['id_reklame']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small">Nama Pemilik / Perusahaan</label>
                        <input type="text" name="nama_pemilik" class="form-control" value="<?php echo $data['nama_pemilik']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Jenis Reklame</label>
                        <select name="jenis_reklame" class="form-select" required>
                            <option value="Billboard" <?php if($data['jenis_reklame'] == 'Billboard') echo 'selected'; ?>>Billboard</option>
                            <option value="Baliho" <?php if($data['jenis_reklame'] == 'Baliho') echo 'selected'; ?>>Baliho</option>
                            <option value="Spanduk" <?php if($data['jenis_reklame'] == 'Spanduk') echo 'selected'; ?>>Spanduk</option>
                            <option value="Neon Box" <?php if($data['jenis_reklame'] == 'Neon Box') echo 'selected'; ?>>Neon Box</option>
                            <option value="Shop Sign" <?php if($data['jenis_reklame'] == 'Shop Sign') echo 'selected'; ?>>Shop Sign</option>
                            <option value="Papan Nama/ Sign Board" <?php if($data['jenis_reklame'] == 'Papan Nama/ Sign Board') echo 'selected'; ?>>Papan Nama/ Sign Board</option>
                            <option value="Flag Chain/ Sunscreen" <?php if($data['jenis_reklame'] == 'Flag Chain/ Sunscreen') echo 'selected'; ?>>Flag Chain/ Sunscreen</option>
                            <option value="Shop Sign" <?php if($data['jenis_reklame'] == 'Shop Sign') echo 'selected'; ?>>Shop Sign</option>\
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Lokasi (Alamat)</label>
                        <input type="text" name="lokasi" class="form-control" value="<?php echo $data['lokasi']; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small">Latitude (X)</label>
                            <input type="text" name="latitude" class="form-control" value="<?php echo $data['latitude']; ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small">Longitude (Y)</label>
                            <input type="text" name="longitude" class="form-control" value="<?php echo $data['longitude']; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small">Panjang (m)</label>
                            <input type="number" step="0.01" name="panjang" class="form-control" value="<?php echo $data['panjang']; ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small">Lebar (m)</label>
                            <input type="number" step="0.01" name="lebar" class="form-control" value="<?php echo $data['lebar']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small">Nilai Pajak (Z)</label>
                        <div class="input-group">
                            <span class="input-group-text text-success fw-bold">Rp</span>
                            <input type="number" name="nilai_pajak" class="form-control" value="<?php echo $data['nilai_pajak']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Periode Pajak</label>
                        <input type="text" name="periode_pajak" class="form-control" value="<?php echo $data['periode_pajak']; ?>" required>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle"></i> SIMPAN PERUBAHAN
                        </button>
                        <a href="admin.php" class="btn btn-outline-secondary py-2 border-0">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center mt-4 text-muted small">
            © 2026 Ema Deloris SIlaban / Universitas HKBP Nommensen Pematangsiantar
        </div>
    </div>
</body>
</html>