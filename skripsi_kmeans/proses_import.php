<?php
include 'koneksi.php'; 

if (isset($_POST['import'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    $handle = fopen($file, "r");

    // Ambil header
    $header = fgetcsv($handle, 1000, ","); 
    if (count($header) == 1) {
        rewind($handle);
        $header = fgetcsv($handle, 1000, ";");
        $delimiter = ";";
    } else {
        $delimiter = ",";
    }

    $berhasil = 0;

    while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        // Ambil data dan bersihkan spasi
        $nama_pemilik  = mysqli_real_escape_string($koneksi, trim($data[array_search('nama_pemilik', $header)]));
        
        // JURUS KUNCI: Lewati jika kolom nama kosong
        if (empty($nama_pemilik)) continue;

        $jenis_reklame = mysqli_real_escape_string($koneksi, trim($data[array_search('jenis_reklame', $header)]));
        $lokasi        = mysqli_real_escape_string($koneksi, trim($data[array_search('lokasi', $header)]));
        $latitude      = str_replace(',', '.', trim($data[array_search('latitude', $header)]));
        $longitude     = str_replace(',', '.', trim($data[array_search('longitude', $header)]));
        $panjang       = str_replace(',', '.', trim($data[array_search('panjang', $header)]));
        $lebar         = str_replace(',', '.', trim($data[array_search('lebar', $header)]));
        $nilai_pajak   = str_replace(',', '.', trim($data[array_search('nilai_pajak', $header)]));
        $periode_pajak = str_replace(',', '.', trim($data[array_search('periode_pajak', $header)]));

        // LANGSUNG INSERT (Tanpa Cek Double karena tabel sudah kosong)
        $query = "INSERT INTO tb_reklame (nama_pemilik, jenis_reklame, lokasi, latitude, longitude, panjang, lebar, nilai_pajak, periode_pajak) 
                  VALUES ('$nama_pemilik', '$jenis_reklame', '$lokasi', '$latitude', '$longitude', '$panjang', '$lebar', '$nilai_pajak', 'periode_pajak')";
        
        if(mysqli_query($koneksi, $query)){
            $berhasil++;
        }
    }

    fclose($handle);
    echo "<script>alert('Selesai! $berhasil data berhasil dimasukkan ke database.'); window.location='admin.php';</script>";
}
?>