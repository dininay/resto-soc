<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai catatan_owner dari formulir
    $id = $_POST['id'];
    $catatan_legal = $_POST['catatan_legal'];
    $submit_legal = $_POST['submit_legal'];

    // Update data di database
    $sql = "UPDATE sdg_desain SET submit_legal = '$submit_legal', catatan_legal = '$catatan_legal' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-design-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>