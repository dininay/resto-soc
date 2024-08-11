<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil kode lahan dari permintaan POST
$kode_lahan = $_POST["kode_lahan"];

// Query untuk mengambil data dari tabel land berdasarkan kode lahan yang diberikan
// Query untuk mengambil data dari tabel land dan resto berdasarkan kode lahan yang diberikan
$sql = "SELECT l.nama_lahan, l.lokasi, r.nama_store 
        FROM land l 
        INNER JOIN resto r ON l.kode_lahan = r.kode_lahan 
        WHERE l.kode_lahan = ?";
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
    $land_data['nama_lahan'] = $data['nama_lahan'];
    $land_data['lokasi'] = $data['lokasi'];
    $land_data['nama_store'] = $data['nama_store'];
}

// Mengembalikan data dalam format JSON
echo json_encode($land_data);
?>
