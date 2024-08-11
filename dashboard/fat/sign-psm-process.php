<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["kode_lahan"]) && isset($_POST["confirm_fatpsm"]) && isset($_POST["catatan_psmfat"])) {
    $id = $_POST["id"];
    $kode_lahan = $_POST["kode_lahan"];
    $confirm_fatpsm = $_POST["confirm_fatpsm"];
    $catatan_psmfat = $_POST["catatan_psmfat"];
    $psmfat_date = null;
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    
    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if (isset($_FILES["kronologi"])) {
        foreach ($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
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
        // Ambil psmfat_date dan start_konstruksi dari tabel draft
        $sql_get_dates = "SELECT psmfat_date FROM draft WHERE id = ?";
        $stmt_get_dates = $conn->prepare($sql_get_dates);
        $stmt_get_dates->bind_param("i", $id);
        $stmt_get_dates->execute();
        $stmt_get_dates->bind_result($existing_psmfat_date);
        $stmt_get_dates->fetch();
        $stmt_get_dates->close();

        $sql_get_resto = "SELECT start_konstruksi FROM resto WHERE kode_lahan = ?";
        $stmt_get_resto = $conn->prepare($sql_get_resto);
        $stmt_get_resto->bind_param("s", $kode_lahan);
        $stmt_get_resto->execute();
        $stmt_get_resto->bind_result($existing_start_konstruksi);
        $stmt_get_resto->fetch();
        $stmt_get_resto->close();

        // Tentukan nilai start_konstruksi
        if ($confirm_fatpsm == 'Approve') {
            $psmfat_date = date("Y-m-d H:i:s");
            $new_start_konstruksi = $psmfat_date;

            // Periksa dan sesuaikan start_konstruksi
            if ($existing_psmfat_date > $existing_start_konstruksi) {
                $new_start_konstruksi = date('Y-m-d H:i:s', strtotime($psmfat_date . ' +1 day'));
            } else {
                $new_start_konstruksi = $existing_start_konstruksi;
            }

            // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
            $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'Sign'";
            $result_sla_steqp = $conn->query($sql_sla_steqp);
            if ($result_sla_steqp->num_rows > 0) {
                $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                $hari_sla_steqp = $row_sla_steqp['sla'];
                $slabod_date = date("Y-m-d", strtotime($psmfat_date . ' + ' . $hari_sla_steqp . ' days'));
            } else {
                $conn->rollback();
                echo "Error: Data SLA tidak ditemukan untuk divisi Table-Sewa.";
                exit;
            }
            $confirm_bod = "In Process";
            $confirm_nego = "Approve";
            // Query untuk memperbarui status confirm_fatpsm di tabel draft
            $sql_update = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ?, psmfat_date = ?, confirm_bod = ?, slabod_date = ?, confirm_nego = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssssssi", $confirm_fatpsm, $catatan_psmfat, $psmfat_date, $confirm_bod, $slabod_date, $confirm_nego, $id);
            $stmt_update->execute();
            // Query untuk memperbarui status confirm_fatpsm di tabel draft
            $sql_resto = "UPDATE resto SET start_konstruksi = ? WHERE kode_lahan = ?";
            $stmt_resto = $conn->prepare($sql_resto);
            $stmt_resto->bind_param("ss", $new_start_konstruksi, $id);
            $stmt_resto->execute();

            if ($stmt_resto->affected_rows > 0) {
                echo "Status berhasil diperbarui.";
            } else {
                echo "Gagal memperbarui status.";
            }

            // Ambil kode_lahan dari tabel draft
            $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();

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
        } elseif ($confirm_fatpsm == 'Pending') {
            // Ambil kode_lahan dari tabel draft
            $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();

            // Query untuk memperbarui confirm_fatpsm, psmfat_date di tabel draft dan memasukkan data ke dalam tabel hold_project
            $sql_update_re = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ?, psmfat_date = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("sssi", $confirm_fatpsm, $catatan_psmfat, $psmfat_date, $id);
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
        } else {
            // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel draft
            $sql = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $confirm_fatpsm, $catatan_psmfat, $id);
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
        // Redirect ke halaman datatables-sign-psm-fat.php
        header("Location: ../datatables-sign-psm-fat.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
