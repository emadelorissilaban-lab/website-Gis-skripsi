<?php
include 'koneksi.php';

// Cek apakah ada parameter aksi di URL
if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];

    // --- 1. LOGIKA TAMBAH DATA ---
    if ($aksi == "tambah") {
        $nama_pemilik  = $_POST['nama_pemilik'];
        $jenis_reklame = $_POST['jenis_reklame'];
        $lokasi        = $_POST['lokasi'];
        $latitude      = $_POST['latitude'];
        $longitude     = $_POST['longitude'];
        // --- TAMBAHKAN KODE INI DI SINI (Baris baru) ---
    $latitude  = str_replace(',', '.', $latitude);
    $longitude = str_replace(',', '.', $longitude);
    $nilai_pajak = str_replace('.', '', $_POST['nilai_pajak']);
        $panjang       = $_POST['panjang'];
        $lebar         = $_POST['lebar'];
        $nilai_pajak   = $_POST['nilai_pajak'];
        $periode = $_POST['periode_pajak'];

        $query = $_POST['nilai_pajak'];
        $query = "INSERT INTO tb_reklame (nama_pemilik, jenis_reklame, lokasi, latitude, longitude, panjang, lebar, nilai_pajak, periode_pajak) 
                  VALUES ('$nama_pemilik', '$jenis_reklame', '$lokasi', '$latitude', '$longitude', '$panjang', '$lebar', '$nilai_pajak', '$periode')";
        
        $simpan = mysqli_query($koneksi, $query);

        if ($simpan) {
            header("location:admin.php?pesan=tambah_berhasil");
        } else {
            echo "Gagal Tambah: " . mysqli_error($koneksi);
        }
    }

    

    // --- LOGIKA HAPUS DATA ---
elseif ($aksi == "hapus") {
    // Ambil 'id' yang dikirim dari admin.php
    $id_yang_mau_dihapus = $_GET['id']; 
    
    // Gunakan nama kolom yang benar: 'id_reklame'
    $query_hapus = "DELETE FROM tb_reklame WHERE id_reklame = '$id_yang_mau_dihapus'";
    
    $hapus = mysqli_query($koneksi, $query_hapus);

    if ($hapus) {
        header("location:admin.php?pesan=hapus_berhasil");
        exit();
    } else {
        // Jika gagal, ini akan memberi tahu kolom mana yang salah
        echo "Gagal Hapus: " . mysqli_error($koneksi);
    }
}



    // --- 3. LOGIKA UPDATE DATA ---
    elseif ($aksi == "update") {
        $id_reklame    = $_POST['id_reklame'];
        $nama_pemilik  = $_POST['nama_pemilik'];
        $jenis_reklame = $_POST['jenis_reklame'];
        $lokasi        = $_POST['lokasi'];
        $latitude      = $_POST['latitude'];
        $longitude     = $_POST['longitude'];
        // --- TAMBAHKAN KODE INI DI SINI ---
    $latitude  = str_replace(',', '.', $latitude);
    $longitude = str_replace(',', '.', $longitude);
    $nilai_pajak = str_replace('.', '', $_POST['nilai_pajak']);
        $panjang       = $_POST['panjang'];
        $lebar         = $_POST['lebar'];
        $nilai_pajak   = $_POST['nilai_pajak'];
        $periode_pajak = $_POST['periode_pajak'];

        $query_update = "UPDATE tb_reklame SET 
                        nama_pemilik='$nama_pemilik', 
                        jenis_reklame='$jenis_reklame', 
                        lokasi='$lokasi', 
                        latitude='$latitude', 
                        longitude='$longitude', 
                        panjang='$panjang', 
                        lebar='$lebar', 
                        nilai_pajak='$nilai_pajak',
                        periode_pajak='$periode_pajak'
                        WHERE id_reklame='$id_reklame'";
        
        $update = mysqli_query($koneksi, $query_update);

        if ($update) {
            header("location:admin.php?pesan=update_berhasil");
        } else {
            echo "Gagal Update: " . mysqli_error($koneksi);
        }
        // Menghapus titik secara otomatis sebelum masuk ke database
$nilai_pajak = str_replace('.', '', $_POST['nilai_pajak']);
    }
}
?>