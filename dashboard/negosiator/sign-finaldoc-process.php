<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_negovaldoc"]) && isset($_POST["catatan_negovaldoc"])) {
    $id = $_POST["id"];
    $confirm_negovaldoc = $_POST["confirm_negovaldoc"];
    $catatan_negovaldoc = $_POST["catatan_negovaldoc"];
    $negovaldoc_date = null;
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if(isset($_FILES["kronologi"])) {
        foreach($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_dir . $target_file)) {
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
        if ($confirm_negovaldoc == 'Done') {
            $negovaldoc_date = date("Y-m-d H:i:s");
                        // Query untuk memperbarui status confirm_negovaldoc di tabel draft
                        $sql_update = "UPDATE draft SET confirm_negovaldoc = ?, catatan_negovaldoc = ?, negovaldoc_date = ? WHERE id = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param("sssi", $confirm_negovaldoc, $catatan_negovaldoc, $negovaldoc_date, $id);
                        $stmt_update->execute();

                        if ($stmt_update->affected_rows > 0) {
                            echo "Status berhasil diperbarui.";
                        } else {
                            echo "Gagal memperbarui status.";
                        }
                        // Ambil kode_lahan dari tabel re
                        $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                        $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                        $stmt_get_kode_lahan->bind_param("i", $id);
                        $stmt_get_kode_lahan->execute();
                        $stmt_get_kode_lahan->bind_result($kode_lahan);
                        $stmt_get_kode_lahan->fetch();
                        $stmt_get_kode_lahan->free_result();

                        // Periksa apakah kode_lahan ada di tabel hold_project
                        $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                        $stmt_check_hold = $conn->prepare($sql_check_hold);
                        $stmt_check_hold->bind_param("s", $kode_lahan);
                        $stmt_check_hold->execute();
                        $stmt_check_hold->store_result();

                        if ($stmt_check_hold->num_rows > 0) {
                            // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                            $status_hold = 'Done';
                            $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                            $stmt_update_hold = $conn->prepare($sql_update_hold);
                            $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                            $stmt_update_hold->execute();
                        }
                        // Komit transaksi
                        $conn->commit();
                        echo "Status berhasil diperbarui.";
        } elseif ($confirm_negovaldoc == 'Pending') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
    
                // Query untuk memperbarui confirm_negovaldoc, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE draft SET confirm_negovaldoc = ?, catatan_negovaldoc = ?, negovaldoc_date = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $confirm_negovaldoc, $catatan_negovaldoc, $negovaldoc_date, $id);
                $stmt_update_re->execute();
    
                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");
    
                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $stmt_hold->execute();
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } elseif ($confirm_negovaldoc == 'Reject') {
                // Ambil kode lahan sebelum menghapus dari tabel re
                $sql = "SELECT kode_lahan FROM draft WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $kode_lahan);
                $stmt->execute();
                $stmt->bind_result($kode_lahan);
                $stmt->fetch();
                $stmt->close();
        
                // Mulai transaksi
                $conn->begin_transaction();
        
                try {
                    // Hapus data dari tabel re berdasarkan kode_lahan
                    $sql = "DELETE FROM re WHERE kode_lahan = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $kode_lahan);
                    $stmt->execute();
                    
                    // Hapus data dari tabel re berdasarkan kode_lahan
                    $sql = "DELETE FROM draft WHERE kode_lahan = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $kode_lahan);
                    $stmt->execute();
                    
                    // Hapus data dari tabel re berdasarkan kode_lahan
                    $sql = "DELETE FROM dokumen_loacd WHERE kode_lahan = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $kode_lahan);
                    $stmt->execute();
        
                    // Perbarui status_land menjadi Reject pada tabel land berdasarkan kode lahan
                    $sql = "UPDATE land SET status_land = 'Reject' WHERE kode_lahan = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $kode_lahan);
                    $stmt->execute();
        
                    // Komit transaksi
                    $conn->commit();
                    echo "Data berhasil dihapus dan status berhasil diperbarui.";
                } catch (Exception $e) {
                    // Rollback transaksi jika terjadi kesalahan
                    $conn->rollback();
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
                $sql = "UPDATE draft SET confirm_negovaldoc = ?, catatan_negovaldoc = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $confirm_negovaldoc, $catatan_negovaldoc, $id);
                $stmt->execute();
    
                // Check if update was successful
                if ($stmt->affected_rows > 0) {
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
        header("Location: ../datatables-validasi-negosiator.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}