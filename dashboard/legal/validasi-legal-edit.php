<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai catatan_legal dari formulir
    $catatan_legal = $_POST['catatan_legal'];
    $status_approvlegal = $_POST['status_approvlegal'];
    $id = $_POST['id'];

    // Update kolom status_approvlegal dan catatan_legal di tabel re
    $sql = "UPDATE re SET status_approvlegal = ?, catatan_legal = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status_approvlegal, $catatan_legal, $id);

    if ($stmt->execute()) {
        // Jika status_approvlegal diubah menjadi "Approve", perbarui tgl_end_date
        if ($status_approvlegal == 'Approve') {
            // Perbarui tgl_end_date dengan tanggal saat ini
            $tgl_end_date = date('Y-m-d');
            
            // Hitung slanego_date (7 hari setelah end_date)
            $slanego_date = date('Y-m-d', strtotime($tgl_end_date . ' +7 days'));
            
            // Update kolom tgl_end_date di tabel re
            $sql_update_tgl_end_date = "UPDATE re SET end_date = ?, slanego_date = ? WHERE id = ?";
            $stmt_update_tgl_end_date = $conn->prepare($sql_update_tgl_end_date);
            $stmt_update_tgl_end_date->bind_param("ssi", $tgl_end_date, $slanego_date, $id);

            if ($stmt_update_tgl_end_date->execute()) {
                // Redirect ke halaman datatable-validasi-lahan-legal
                header("Location: " . $base_url . "/datatables-validasi-lahan-legal.php");
                exit();
            } else {
                echo "Error updating tgl_end_date: " . $stmt_update_tgl_end_date->error;
            }

            // Tutup statement untuk update tgl_end_date
            $stmt_update_tgl_end_date->close();
        } else {
            // Redirect ke halaman datatable-validasi-lahan-legal
            header("Location: " . $base_url . "/datatables-validasi-lahan-legal.php");
            exit();
        }
    } else {
        echo "Error updating status_approvlegal and catatan_legal: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi database
$conn->close();
?>
