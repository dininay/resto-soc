<?php
// mengaktifkan session pada php
session_start();

// menghubungkan php dengan koneksi database
include 'koneksi.php';

// menangkap data yang dikirim dari form login
$username = $_POST['username'];
$password = md5($_POST['password']);

// menyeleksi data user dengan username dan password yang sesuai
$login = mysqli_query($conn, "select * from user where username='$username' and password='$password'");
// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// cek apakah username dan password ditemukan pada database
if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);
    
    // buat session login dan level
    $_SESSION['username'] = $username;
    $_SESSION['level'] = $data['level'];

    // alihkan ke halaman dashboard berdasarkan level pengguna
    switch ($_SESSION['level']) {
        case "Admin":
            header("location:dashboard/index.php");
            break;
        case "Re":
            header("location:dashboard/index.php");
            break;
        case "Legal":
            header("location:dashboard/index.php");
            break;
        default:
            header("location:index.php?pesan=gagal");
    }
} else {
    header("location:index.php?pesan=gagal");
}
?>
