<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web GIS Simalungun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; overflow: hidden; }
        #peta { height: 100vh; width: 100%; z-index: 1; }
        .sidebar {
            position: absolute; top: 20px; left: 20px; width: 350px;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px; border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 999; backdrop-filter: blur(10px);
        }
        .legend-item { display: flex; align-items: center; margin-bottom: 8px; font-size: 14px; }
        .dot { height: 15px; width: 15px; border-radius: 50%; display: inline-block; margin-right: 10px; }
        .dot-red { background: red; box-shadow: 0 0 10px red; }
        .dot-yellow { background: gold; box-shadow: 0 0 10px gold; }
        .dot-green { background: green; box-shadow: 0 0 10px green; }
        @media (max-width: 768px) {
            .sidebar { width: 90%; left: 5%; top: auto; bottom: 20px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h5 class="fw-bold mb-1">GIS Reklame Simalungun</h5>
        <p class="text-muted small mb-3">Pemetaan Zonasi Pajak dengan K-Means</p>
        
        <hr>
        <h6 class="fw-bold small">LEGENDA CLUSTER</h6>
        <div class="legend-item"><span class="dot dot-red"></span> Potensi Pajak Tinggi</div>
        <div class="legend-item"><span class="dot dot-yellow"></span> Potensi Pajak Sedang</div>
        <div class="legend-item"><span class="dot dot-green"></span> Potensi Pajak Rendah</div>
        
        <div class="mt-4 d-grid">
            <a href="admin.php" class="btn btn-dark btn-sm rounded-pill">Masuk sebagai Admin</a>
        </div>
    </div>

    <div id="peta"></div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('peta').setView([2.9700, 99.0680], 13);
        
        // Peta Dasar (Bisa diganti Satelit kalau mau lebih keren)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var dataReklame = <?php
            $q = mysqli_query($koneksi, "SELECT * FROM tb_reklame");
            $rows = [];
            while($r = mysqli_fetch_assoc($q)){ $rows[] = $r; }
            echo json_encode($rows);
        ?>;

        dataReklame.forEach(function(item){
            var warna = '#ccc';
            var ket = 'Belum Hitung';
            
            if(item.cluster == 1) { warna = 'red'; ket='Tinggi'; }
            if(item.cluster == 2) { warna = 'gold'; ket='Sedang'; }
            if(item.cluster == 3) { warna = 'green'; ket='Rendah'; }

            L.circleMarker([item.latitude, item.longitude], {
                color: 'white', weight: 2,
                fillColor: warna, fillOpacity: 0.9, radius: 12
            }).addTo(map)
            .bindPopup(`
                <div style="text-align:center;">
                    <b style="color:${warna}">${item.nama_titik}</b><br>
                    <small>Pajak: Rp ${parseInt(item.nilai_pajak).toLocaleString()}</small><br>
                    <span class="badge bg-dark mt-1">Cluster ${ket}</span>
                </div>
            `);
        });
    </script>
</body>
</html>