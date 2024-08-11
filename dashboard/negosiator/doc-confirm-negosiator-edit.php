<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai catatan_legal dari formulir
    $catatan_nego = $_POST['catatan_nego'];
    $status_approvnego = $_POST['status_approvnego'];
    $id = $_POST['id'];

    // Update kolom status_approvlegal dan catatan_legal di tabel re
    $sql = "UPDATE re SET status_approvnego = ?, catatan_nego = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status_approvnego, $catatan_nego, $id);

    if ($stmt->execute()) {
        // Jika status_approvlegal diubah menjadi "Approve", perbarui tgl_end_date
        if ($status_approvnego == 'Approve') {
            // Perbarui tgl_end_date dengan tanggal saat ini
            $tgl_end_date = date('Y-m-d');
            
            // Update kolom tgl_end_date di tabel re
            $sql_update_tgl_end_date = "UPDATE re SET nego_date = ? WHERE id = ?";
            $stmt_update_tgl_end_date = $conn->prepare($sql_update_tgl_end_date);
            $stmt_update_tgl_end_date->bind_param("si", $tgl_end_date, $id);

            if ($stmt_update_tgl_end_date->execute()) {
                // Redirect ke halaman datatable-validasi-lahan-legal
                header("Location: " . $base_url . "/datatables-doc-confirm-negosiator.php");
                exit();
            } else {
                echo "Error updating tgl_nego_date: " . $stmt_update_tgl_end_date->error;
            }

            // Tutup statement untuk update tgl_end_date
            $stmt_update_tgl_end_date->close();
        } else {
            // Redirect ke halaman datatable-validasi-lahan-legal
            header("Location: " . $base_url . "/datatables-doc-confirm-negosiator.php");
            exit();
        }
    } else {
        echo "Error updating status_approvnego and catatan_nego: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi database
$conn->close();
?>
