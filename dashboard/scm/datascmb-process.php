<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_utensil"]) ) {
    $id = $_POST["id"];
    $status_utensil = $_POST["status_utensil"];
    $qty_date = date("Y-m-d H:i:s");

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
        if ($status_utensil == 'Receive In Store') {

            $sql_update = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt_update->execute();

            if ($stmt_update->affected_rows > 0) {
                $sql_get_kode_lahan = "SELECT kode_lahan FROM utensil WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("s", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->close();

                if ($kode_lahan) {
                    $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                    $stmt_check_hold = $conn->prepare($sql_check_hold);
                    $stmt_check_hold->bind_param("s", $kode_lahan);
                    $stmt_check_hold->execute();
                    $stmt_check_hold->store_result();

                    if ($stmt_check_hold->num_rows > 0) {
                        $status_hold = 'Done';
                        $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                        $stmt_update_hold = $conn->prepare($sql_update_hold);
                        $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                        $stmt_update_hold->execute();
                    }
                    $stmt_check_hold->close();
                }
            }
        } elseif ($status_utensil == 'Pending') {
            $sql_get_kode_lahan = "SELECT kode_lahan FROM utensil WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("s", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();

            if ($kode_lahan) {
                $sql_update_re = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("ssi", $status_utensil, $qty_date, $id);
                $stmt_update_re->execute();

                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");

                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $stmt_hold->execute();
            }
        } elseif ($status_utensil == 'Progress Utensil Receive') {
            // Jika status id diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
            $sql = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt->execute();
        } elseif ($status_utensil == 'Pending In Procurement') {
            // Jika status id diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
            $sql = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt->execute();
        } elseif ($status_utensil == 'Pending In IT') {
            // Jika status id diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
            $sql = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt->execute();
        } elseif ($status_utensil == 'Pending In HRGA') {
            // Jika status id diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
            $sql = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt->execute();
        } else {
            // Jika status id diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
            $sql = "UPDATE utensil SET status_utensil = ?, qty_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_utensil, $qty_date, $id);
            $stmt->execute();

            // Check if update was successful
            if ($stmt->affected_rows > 0) {
                echo "<script>
                        alert('Status berhasil diperbarui.');
                        window.location.href = window.location.href;
                     </script>";
            } else {
                echo "Error: Gagal memperbarui status. id ada perubahan dilakukan.";
            }
        }

        $conn->commit();
        
        $sql_get_kode = "SELECT kode_lahan FROM utensil WHERE id = ?";
        $stmt_get_kode = $conn->prepare($sql_get_kode);
        $stmt_get_kode->bind_param("i", $id);
        $stmt_get_kode->execute();
        $stmt_get_kode->bind_result($kode_lahan);
        $stmt_get_kode->fetch();
        $stmt_get_kode->close();

        if (isset($kode_lahan) && !empty($kode_lahan)) {
            header("Location: ../datatables-data-scmutensil.php?id=" . urlencode($kode_lahan));
            exit;
        } else {
            echo "Error: Kode lahan id ditemukan.";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}