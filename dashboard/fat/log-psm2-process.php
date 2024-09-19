<?php
// Koneksi ke database
include "../../koneksi.php";

// Set timezone ke Jakarta
date_default_timezone_set('Asia/Jakarta');

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["kode_lahan"])) {
    $id = $_POST["id"];
    $kode_lahan = $_POST["kode_lahan"];
    $fat2_date = date("Y-m-d H:i:s");

    if ($_POST['review_fat2'] == "Done") {
        $review_fat2 = "Done";
    } else {
        $review_fat2 = isset($_POST['manual_note']) ? $_POST['manual_note'] : "";
    }

    $sql_update = "UPDATE note_psm SET review_fat2 = ?, fat2_date = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssi", $review_fat2, $fat2_date, $id);
    $stmt_update->execute();
    // Mulai transaksi
    $conn->begin_transaction();
    
    // Redirect ke halaman datatables-checkval-legal.php
    if (isset($kode_lahan) && !empty($kode_lahan)) {
        header("Location: ../log-note-psm.php?id=" . urlencode($kode_lahan));
        exit;
    } else {
        echo "Error: Kode lahan tidak ditemukan.";
    }

}