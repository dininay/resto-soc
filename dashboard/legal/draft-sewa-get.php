<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil kode lahan dari permintaan POST
$kode_lahan = $_POST["kode_lahan"];

// Query untuk mengambil data dari tabel land berdasarkan kode lahan yang diberikan
$sql = "SELECT re.nama_lahan, re.lokasi, re.luas_area, dokumen_loacd.lamp_land, dokumen_loacd.lamp_loacd
        FROM re
        LEFT JOIN dokumen_loacd ON re.kode_lahan = dokumen_loacd.kode_lahan
        WHERE re.kode_lahan = ?";
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
    $land_data['luas_area'] = $data['luas_area'];
    $land_data['lamp_land'] = $data['lamp_land'];
    $land_data['lamp_loacd'] = $data['lamp_loacd'];
}

// Mengembalikan data dalam format JSON
echo json_encode($land_data);
?>
