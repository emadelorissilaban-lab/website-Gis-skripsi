<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Administrator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden; 
            position: relative;
        }

        /* Container utama: Dibuat lebih lega agar card terlihat ngambang sempurna */
        .main-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* --- Animasi Partikel Berwarna --- */
        .area {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
        }

        .circles {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; margin: 0; padding: 0;
        }

        .circles li {
            position: absolute; display: block; list-style: none;
            animation: animate 25s linear infinite; bottom: -150px;
        }

        .c-red { background: rgba(255, 107, 107, 0.4) !important; color: rgba(255, 107, 107, 0.4) !important; }
        .c-yellow { background: rgba(254, 202, 87, 0.4) !important; color: rgba(254, 202, 87, 0.4) !important; }
        .c-green { background: rgba(29, 209, 161, 0.4) !important; color: rgba(29, 209, 161, 0.4) !important; }
        .c-white { background: rgba(255, 255, 255, 0.2) !important; color: rgba(255, 255, 255, 0.2) !important; }

        .circles li.pin { background: transparent !important; font-size: 35px; }

        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; border-radius: 50%; }
        .circles li:nth-child(2) { left: 10%; width: 40px; height: 40px; animation-delay: 2s; animation-duration: 12s; border-radius: 10px; }
        .circles li:nth-child(3) { left: 70%; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; width: 30px; height: 30px; animation-delay: 0s; border-radius: 50%; }
        .circles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; border-radius: 50%; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .circles li:nth-child(8) { left: 50%; animation-delay: 15s; animation-duration: 45s; }
        .circles li:nth-child(9) { left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; border-radius: 50%; }
        .circles li:nth-child(10){ left: 85%; width: 120px; height: 120px; animation-delay: 0s; animation-duration: 11s; border-radius: 10px; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }

        /* --- UKURAN CARD DIPERKECIL --- */
        .card-login {
            width: 100%;
            max-width: 360px; /* Ukuran diperkecil dari 420px agar lebih ringkas */
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            background: rgba(255, 255, 255, 0.98);
            z-index: 10;
        }

        .logo-wrapper {
            width: 75px; /* Logo diperkecil sedikit agar proporsional */
            height: 75px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .btn-login {
            background: #4e54c8;
            border: none;
            padding: 10px;
            font-weight: 600;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-login:hover { background: #373db0; transform: translateY(-2px); }
        .input-group-text { border-right: none; background: #fff; color: #4e54c8; font-size: 14px; }
        .form-control { border-left: none; font-size: 14px; }
    </style>
</head>
<body>

    <div class="area">
        <ul class="circles">
            <li class="c-white"></li>
            <li class="c-red"></li>
            <li class="pin c-yellow"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="c-green"></li>
            <li class="c-red"></li>
            <li class="c-yellow"></li>
            <li class="c-white"></li>
            <li class="pin c-green"><i class="bi bi-geo-alt-fill"></i></li>
            <li class="c-green"></li>
            <li class="c-red"></li>
        </ul>
    </div>

    <div class="main-wrapper">
        <div class="card card-login mx-auto">
            <div class="card-header bg-transparent border-0 pt-4 text-center">
                <div class="logo-wrapper">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mt-3 mb-0">Login Administrator</h5>
                <p class="text-muted" style="font-size: 11px;">BPKPD Kabupaten Simalungun</p>
            </div>
            
            <div class="card-body px-4 pb-4 pt-0">
                <?php 
                if(isset($_GET['pesan'])){
                    if($_GET['pesan']=="gagal") echo "<div class='alert alert-danger border-0 py-2 small text-center mb-3' style='font-size:11px;'>Username/Password salah!</div>";
                    if($_GET['pesan']=="belum_login") echo "<div class='alert alert-warning border-0 py-2 small text-center mb-3' style='font-size:11px;'>Silakan login dahulu.</div>";
                    if($_GET['pesan']=="logout") echo "<div class='alert alert-success border-0 py-2 small text-center mb-3' style='font-size:11px;'>Berhasil logout.</div>";
                }
                ?>

                <form action="cek_login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px;">Username</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px;">Password</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-login text-uppercase">Masuk</button>
                        <a href="index.php" class="btn btn-link text-muted text-decoration-none" style="font-size: 11px;">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-transparent border-0 pb-4 text-center">
                <small class="text-muted opacity-50" style="font-size: 9px;">© 2026 Universitas HKBP Nommensen</small>
            </div>
        </div>
    </div>

</body>
</html>