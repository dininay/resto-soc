<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$nama_lahan = $_POST["nama_lahan"];
$lokasi = $_POST["lokasi"];
$lamp_land = $_POST["lamp_land"];
$lamp_loacd = $_POST["lamp_loacd"];
$lamp_draf = $_POST["lamp_draf"];
$jadwal_psm = $_POST["jadwal_psm"];
$catatan_nego = $_POST["catatan_nego"];

$confirm_nego = "In Process"; 

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO negosiator (kode_lahan, nama_lahan, lokasi, lamp_land, lamp_loacd, jadwal_psm, lamp_draf, catatan_nego, confirm_nego) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $kode_lahan, $nama_lahan, $lokasi, $lamp_land, $lamp_loacd, $jadwal_psm, $lamp_draf, $catatan_nego, $confirm_nego);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-dealing-draft-negosiator.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
