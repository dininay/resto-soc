<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$city = $_POST["city"];
$kode_lahan = $_POST["kode_lahan"];
$nama_lahan = $_POST["nama_lahan"];
$status_land = "On Planning";

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO land (city, kode_lahan, nama_lahan, status_land) 
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $city, $kode_lahan, $nama_lahan, $status_land);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-bussiness-planning.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
