<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_ff2"]) && isset($_POST["catatan_ff2"]) ) {
    $id = $_POST["id"];
    $status_ff2 = $_POST["status_ff2"];
    $catatan_ff2 = $_POST["catatan_ff2"];
    $ff2_date = null;

    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if(isset($_FILES["kronologi"])) {
        foreach($_FILES['kronologi']['name'] as $key => $filename) {
            $file_ff2p = $_FILES['kronologi']['ff2p_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_ff2p, $target_dir . $target_file)) {
                $kronologi_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $kronologi = implode(",", $kronologi_paths);
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Jika status_approvlegalvd diubah menjadi Approve
        if ($status_ff2 == 'Done') {
            $ff2_date = date("Y-m-d H:i:s");

            // Query untuk memperbarui status status_ff2 di tabel draft
            $sql_update = "UPDATE socdate_hr SET status_ff2 = ?, ff2_date = ?, catatan_ff2 = ? WHERE id = ?";
            $sff2t_update = $conn->prepare($sql_update);
            $sff2t_update->bind_param("sssi", $status_ff2, $ff2_date, $catatan_ff2, $id);
            $sff2t_update->execute();

            if ($sff2t_update->affected_rows > 0) {
                echo "Status berhasil diperbarui.";
            } else {
                echo "Gagal memperbarui status.";
            }
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_hr WHERE id = ?";
            $sff2t_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $sff2t_get_kode_lahan->bind_param("i", $id);
            $sff2t_get_kode_lahan->execute();
            $sff2t_get_kode_lahan->bind_result($kode_lahan);
            $sff2t_get_kode_lahan->fetch();
            $sff2t_get_kode_lahan->free_result();

            // Periksa apakah kode_lahan ada di tabel hold_project
            $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
            $sff2t_check_hold = $conn->prepare($sql_check_hold);
            $sff2t_check_hold->bind_param("s", $kode_lahan);
            $sff2t_check_hold->execute();
            $sff2t_check_hold->store_result();

            if ($sff2t_check_hold->num_rows > 0) {
                // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                $status_hold = 'Done';
                $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                $sff2t_update_hold = $conn->prepare($sql_update_hold);
                $sff2t_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                $sff2t_update_hold->execute();
            }
            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui.";
        } elseif ($status_ff2 == 'Pending') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                $sff2t_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $sff2t_get_kode_lahan->bind_param("i", $id);
                $sff2t_get_kode_lahan->execute();
                $sff2t_get_kode_lahan->bind_result($kode_lahan);
                $sff2t_get_kode_lahan->fetch();
                $sff2t_get_kode_lahan->free_result();
    
                // Query untuk memperbarui status_ff2, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE socdate_hr SET status_ff2 = ?, ff2_date = ?, catatan_ff2 = ? WHERE id = ?";
                $sff2t_update_re = $conn->prepare($sql_update_re);
                $sff2t_update_re->bind_param("sssi", $status_ff2, $ff2_date, $catatan_ff2, $id);
                $sff2t_update_re->execute();
    
                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");
    
                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $sff2t_hold = $conn->prepare($sql_hold);
                $sff2t_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $sff2t_hold->execute();
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
                $sql = "UPDATE socdate_hr SET status_ff2 = ?, ff2_date = ?, catatan_ff2 = ? WHERE id = ?";
                $sff2t = $conn->prepare($sql);
                $sff2t->bind_param("sssi", $status_ff2, $ff2_date, $catatan_ff2, $id);
                $sff2t->execute();
    
                // Check if update was successful
                if ($sff2t->affected_rows > 0) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: Gagal memperbarui status. Tidak ada perubahan dilakukan.";
                }
            }

        // Komit transaksi
        $conn->commit();
        // Redirect ke halaman datatables-checkval-legal.php
        header("Location: ../datatables-hr-fulfillment-2.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->geff2essage();
    }
}