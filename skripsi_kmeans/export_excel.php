<?php 
// 1. MENYALAKAN PELACAK EROR (Agar aman dan tidak blank putih)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Cek login
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit;
}
include 'koneksi.php'; 

// 2. KUNCI UTAMA: Perintah Header agar Browser Otomatis Mendownload Halaman ini sebagai File Excel (.xls)
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keseluruhan_Pajak_Reklame.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Hitung Jumlah Total per Kelompok untuk Ringkasan di Bagian Atas Excel
$count_tinggi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Tinggi'"));
$count_sedang = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Sedang'"));
$count_rendah = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_cluster WHERE label_cluster='Potensi Rendah'"));
$grand_total  = $count_tinggi + $count_sedang + $count_rendah;
?>

<table border="0">
    <tr>
        <th colspan="7" style="font-size: 14px; font-weight: bold; text-align: center;">BADAN PENGELOLA KEUANGAN DAN PENDAPATAN DAERAH</th>
    </tr>
    <tr>
        <th colspan="7" style="font-size: 14px; font-weight: bold; text-align: center;">KABUPATEN SIMALUNGUN</th>
    </tr>
    <tr>
        <td colspan="7" style="font-size: 11px; text-align: center; color: #555;">Wilayah Studi Evaluasi Potensi Pajak Reklame: Kecamatan Bandar</td>
    </tr>
    <tr>
        <th colspan="7" style="font-size: 12px; font-weight: bold; text-align: center; padding-top: 15px;">LAPORAN HASIL ANALISIS K-MEANS CLUSTERING ZONASI POTENSI PAJAK REKLAME</th>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>

<table border="1" cellpadding="5">
    <tr style="background-color: #dcdde1; font-weight: bold;">
        <th colspan="2" style="text-align: left;">RINGKASAN TOTAL HASIL KLASTERISASI</th>
    </tr>
    <tr>
        <td>Potensi Tinggi (C1)</td>
        <td><?php echo $count_tinggi; ?> Objek Reklame</td>
    </tr>
    <tr>
        <td>Potensi Sedang (C2)</td>
        <td><?php echo $count_sedang; ?> Objek Reklame</td>
    </tr>
    <tr>
        <td>Potensi Rendah (C3)</td>
        <td><?php echo $count_rendah; ?> Objek Reklame</td>
    </tr>
    <tr style="font-weight: bold; background-color: #f5f6fa;">
        <td>TOTAL KESELURUHAN DATA OBJEK PAJAK</td>
        <td><?php echo $grand_total; ?> Objek Reklame</td>
    </tr>
</table>

<br />

<?php 
// Urutan Kelompok Sesuai Aturan Dosen: Tinggi -> Sedang -> Rendah
$susunan_cluster = [
    ['label' => 'Potensi Tinggi', 'warna_kolom' => '#ff7675', 'kunci' => 'Potensi Tinggi'],
    ['label' => 'Potensi Sedang', 'warna_kolom' => '#ffeaa7', 'kunci' => 'Potensi Sedang'],
    ['label' => 'Potensi Rendah', 'warna_kolom' => '#55efc4', 'kunci' => 'Potensi Rendah']
];

foreach($susunan_cluster as $cls) {
?>
    <table border="0">
        <tr>
            <td colspan="7" style="background-color: <?php echo $cls['warna_kolom']; ?>; font-weight: bold; font-size: 12px; text-transform: uppercase;">
                KELOMPOK CLUSTER: <?php echo $cls['label']; ?>
            </td>
        </tr>
    </table>

    <table border="1" cellpadding="5">
        <thead>
            <tr style="background-color: #f8f9fa; font-weight: bold; text-align: center;">
                <th width="5%">No</th>
                <th>Nama Pemilik</th>
                <th>Jenis Reklame</th>
                <th>Lokasi Objek</th>
                <th>Dimensi (P x L)</th>
                <th>Nilai Pajak (Rp)</th>
                <th>Periode Pajak</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Query menarik rincian data dengan LEFT JOIN agar aman
            $sql_rincian = "SELECT tb_reklame.*, tb_cluster.label_cluster 
                            FROM tb_reklame 
                            LEFT JOIN tb_cluster ON tb_reklame.id_reklame = tb_cluster.id_reklame 
                            WHERE tb_cluster.label_cluster = '".$cls['kunci']."'
                            ORDER BY tb_reklame.id_reklame DESC";
                            
            $eksekusi = mysqli_query($koneksi, $sql_rincian);
            $nomor = 1;
            $subtotal_pajak = 0;
            $jumlah_baris = mysqli_num_rows($eksekusi);

            if($jumlah_baris == 0){
                echo "<tr><td colspan='7' style='text-align:center; color: #7f8c8d;'>Belum ada data pada klaster ini.</td></tr>";
            } else {
                while($row = mysqli_fetch_array($eksekusi)){
                    $subtotal_pajak += $row['nilai_pajak'];
            ?>
                <tr>
                    <td style="text-align: center;"><?php echo $nomor++; ?></td>
                    <td style="font-weight: bold;"><?php echo $d['nama_pemilik'] ?? $row['nama_pemilik']; ?></td>
                    <td><?php echo $row['jenis_reklame']; ?></td>
                    <td><?php echo $row['lokasi']; ?></td>
                    <td style="text-align: center;"><?php echo $row['panjang']; ?>m x <?php echo $row['lebar']; ?>m</td>
                    <td style="text-align: right;"><?php echo $row['nilai_pajak']; ?></td>
                    <td style="text-align: center;"><?php echo $row['periode_pajak']; ?></td>
                </tr>
            <?php 
                }
            }
            ?>
            <tr style="font-weight: bold; background-color: #f5f6fa;">
                <td colspan="5" style="text-align: right;">JUMLAH DATA KELOMPOK <?php echo strtoupper($cls['kunci']); ?> :</td>
                <td colspan="2" style="text-align: center; color: red;"><?php echo $jumlah_baris; ?> Objek</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f5f6fa;">
                <td colspan="5" style="text-align: right;">SUBTOTAL POTENSI REALISASI PAJAK :</td>
                <td style="text-align: right; color: blue;"><?php echo $subtotal_pajak; ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br />
<?php 
} 
?>