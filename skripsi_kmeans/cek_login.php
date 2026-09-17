<?php 
// Mengaktifkan session php
session_start();
 
// Menghubungkan dengan koneksi
include 'koneksi.php';
 
// Menangkap data yang dikirim dari form
$username = $_POST['username'];
$password = md5($_POST['password']); // Enkripsi password inputan dengan MD5
 
// Menyeleksi data admin dengan username dan password yang sesuai
$data = mysqli_query($koneksi,"SELECT * FROM tb_admin WHERE username='$username' AND password='$password'");
 
// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($data);
 
if($cek > 0){
    // Jika ketemu, buat session login
    $_SESSION['username'] = $username;
    $_SESSION['status'] = "login";
    
    // Redirect ke halaman admin
    header("location:admin.php");
}else{
    // Jika salah, balikin ke login lagi
    header("location:login.php?pesan=gagal");
}
?>