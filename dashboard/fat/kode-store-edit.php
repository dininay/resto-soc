<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $kode_store = $_POST['kode_store'];
    
    // Update data di tabel dokumen_loacd
    $sql_update_dokumen_loacd = "UPDATE dokumen_loacd SET kode_store = ? WHERE kode_lahan = ?";
    $stmt_update_dokumen_loacd = $conn->prepare($sql_update_dokumen_loacd);
    $stmt_update_dokumen_loacd->bind_param("ss", $kode_store, $kode_lahan);

    if (!$stmt_update_dokumen_loacd->execute()) {
        echo "Error updating dokumen_loacd: " . $stmt_update_dokumen_loacd->error;
    }

    header("Location: ../datatables-kode-store-taf.php");
    exit; // Pastikan tidak ada output lain setelah header redirect
    // Menutup prepared statements
    $stmt_update_dokumen_loacd->close();
    $stmt_update_draft->close();
}

// Menutup koneksi database
$conn->close();
?>
