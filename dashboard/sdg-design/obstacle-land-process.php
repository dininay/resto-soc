<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$obstacle = $_POST["obstacle"];
$note = $_POST["note"];
$obs_date = $_POST["obs_date"];
$status_obssdg = "Diajukan";

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO obs_sdg (kode_lahan, obstacle, note, obs_date, status_obssdg) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $kode_lahan, $obstacle, $note, $obs_date, $status_obssdg);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-obstacle-sdg.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
