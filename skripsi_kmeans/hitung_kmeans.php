<?php 
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit; 
}
include 'koneksi.php'; 

// 0. Kosongkan history lama setiap kali tombol hitung diklik
$_SESSION['history_kmeans'] = []; 

$jumlah_cluster = 3; 
$maksimum_iterasi = 10; 

// 1. Ambil Data dari Database
$data_reklame = [];
$q = mysqli_query($koneksi, "SELECT * FROM tb_reklame ORDER BY id_reklame ASC"); 
while($r = mysqli_fetch_assoc($q)){ 
    $data_reklame[] = $r; 
}

if(empty($data_reklame)){ 
    header("Location: admin.php?error=kosong"); 
    exit; 
}

// 2. INPUT CENTROID AWAL DARI EXCEL (Ganti angka di bawah ini dengan angka di sel B2, C2, D2 Excel kamu)
$centroids = [
    [
        'pajak' => 5340375, // Ganti dengan angka Pajak di baris centroid 1 Tinggi Excel
        'lat'   => 3.158358,   // Ganti dengan angka Latitude di baris centroid 1 Excel
        'long'  => 99.328803   // Ganti dengan angka Longitude di baris centroid 1 Excel
    ],
    [
        'pajak' => 4563060,  // Ganti dengan angka Pajak di baris centroid 2 Sedang Excel
        'lat'   => 3.161153,   // Ganti dengan angka Latitude di baris centroid 2 Excel
        'long'  => 99.324632   // Ganti dengan angka Longitude di baris centroid 2 Excel
    ],
    [
        'pajak' => 1946205,  // Ganti dengan angka Pajak di baris centroid 3 Rendah Excel
        'lat'   => 3.158303,   // Ganti dengan angka Latitude di baris centroid 3 Excel
        'long'  => 99.326682   // Ganti dengan angka Longitude di baris centroid 3 Excel
    ]
];


// 3. Proses Iterasi K-Means
for ($iterasi = 0; $iterasi < $maksimum_iterasi; $iterasi++) {
    $kelompok = [[], [], []];
    $log_iterasi = []; 

    foreach ($data_reklame as $data) {
        $jarak_temp = [];
        foreach ($centroids as $idx => $pusat) {
            // RUMUS EUCLIDEAN (Pajak, Lat, Long)
            $pajak_diff = pow(($data['nilai_pajak'] - $pusat['pajak']), 2);
            $lat_diff   = pow(($data['latitude'] - $pusat['lat']), 2);
            $long_diff  = pow(($data['longitude'] - $pusat['long']), 2);
            $jarak_temp[$idx] = sqrt($pajak_diff + $lat_diff + $long_diff);
        }

        // Cari cluster dengan jarak terdekat
        $cluster_pilih = array_keys($jarak_temp, min($jarak_temp))[0];
        $kelompok[$cluster_pilih][] = $data;

        // Catat detail hitungan untuk history
        $log_iterasi[] = [
            'nama' => $data['nama_pemilik'],
            'pajak' => $data['nilai_pajak'],
            'jarak' => $jarak_temp,
            'cluster' => $cluster_pilih
        ];
    }

    // SIMPAN HISTORY KE SESSION UNTUK HALAMAN DETAIL
    $_SESSION['history_kmeans'][] = [
        'iterasi' => $iterasi + 1,
        'centroids' => $centroids,
        'detail' => $log_iterasi,
        'jumlah_anggota' => [count($kelompok[0]), count($kelompok[1]), count($kelompok[2])]
    ];

    // 4. Hitung Ulang Centroid Baru (Mean)
    $centroids_baru = [];
    foreach ($kelompok as $idx => $anggota) {
        if (count($anggota) > 0) {
            $total_pajak = 0; $total_lat = 0; $total_long = 0;
            foreach ($anggota as $a) {
                $total_pajak += $a['nilai_pajak'];
                $total_lat   += $a['latitude'];
                $total_long  += $a['longitude'];
            }
            $centroids_baru[$idx] = [
                'pajak' => $total_pajak / count($anggota),
                'lat'   => $total_lat / count($anggota),
                'long'  => $total_long / count($anggota)
            ];
        } else {
            $centroids_baru[$idx] = $centroids[$idx];
        }
    }
    
    // Berhenti jika posisi centroid sudah stabil (Konvergen)
    if ($centroids == $centroids_baru) break;
    $centroids = $centroids_baru;
}

// 5. Penentuan Label (Ranking berdasarkan pajak)
$sort_helper = [];
foreach ($centroids as $idx => $c) { $sort_helper[$idx] = $c['pajak']; }
arsort($sort_helper); 
$rank = array_keys($sort_helper);
$label_map = [
    $rank[0] => "Potensi Tinggi",
    $rank[1] => "Potensi Sedang",
    $rank[2] => "Potensi Rendah"
];

// 6. Simpan Hasil Akhir ke Database
mysqli_query($koneksi, "TRUNCATE TABLE tb_cluster");
foreach ($kelompok as $idx => $anggota) {
    $label = $label_map[$idx];
    $centroid_akhir = $centroids[$idx]['pajak'];

    foreach ($anggota as $reklame) {
        $id_rek = $reklame['id_reklame'];
        mysqli_query($koneksi, "INSERT INTO tb_cluster (id_reklame, label_cluster, centroid_akhir) 
                                VALUES ('$id_rek', '$label', '$centroid_akhir')");
    }
}

// Kembali ke dashboard dengan pesan sukses
header("Location: admin.php?status=sukses_kmeans");
exit;
?>



