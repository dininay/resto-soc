<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil kode lahan dari permintaan POST
$kode_lahan = $_POST["kode_lahan"];

// Query untuk mengambil data dari tabel land berdasarkan kode lahan yang diberikan
$sql = "SELECT * FROM re WHERE kode_lahan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_lahan);
$stmt->execute();
$result = $stmt->get_result();

// Buat array untuk menyimpan data tanah
$land_data = array();

// Periksa apakah query berhasil dieksekusi
if ($result->num_rows > 0) {
    // Ambil data hasil query
    $data = $result->fetch_assoc();
    // Masukkan data ke dalam array
    $land_data['status_approvowner'] = $data['status_approvowner'];
    $land_data['status_approvlegal'] = $data['status_approvlegal'];
    $land_data['status_approvnego'] = $data['status_approvnego'];
    $land_data['lamp_land'] = $data['lamp_land'];
}

// Mengembalikan data dalam format JSON
echo json_encode($land_data);
?>
