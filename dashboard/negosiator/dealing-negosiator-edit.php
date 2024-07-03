<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];

    $confirm_nego = $_POST['confirm_nego'];
    $catatan_nego = $_POST['catatan_nego'];
    $jadwal_psm = $_POST['jadwal_psm'];

    // Update data di database
    $sql = "UPDATE negosiator SET confirm_nego = '$confirm_nego', catatan_nego = '$catatan_nego', jadwal_psm = '$jadwal_psm' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-dealing-draft-negosiator.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
