<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_approvlegalvd"]) && isset($_POST["catatan_vd"])) {
    $id = $_POST["id"];
    $status_approvlegalvd = $_POST["status_approvlegalvd"];
    $catatan_vd = $_POST["catatan_vd"];
     // Debugging: tampilkan nilai ID yang diterima
     echo "ID yang diterima: " . $id . "<br>";
    $end_date = null;
    $sla_date = null;
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
            
            // Ambil data dari dokumen_loacd berdasarkan ID yang diedit
            $sql_select = "SELECT * FROM dokumen_loacd WHERE id = ?";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bind_param("i", $id);
            $stmt_select->execute();
            $result_select = $stmt_select->get_result();
            $row = $result_select->fetch_assoc();

            $kode_lahan = $row['kode_lahan'];
            var_dump($kode_lahan);

            if ($status_approvlegalvd == 'Approve') {
                $end_date = date("Y-m-d H:i:s");

            // Query untuk memperbarui status_approvlegalvd
            $sql_update = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, end_date = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $end_date, $id);
            if (!$stmt_update->execute()) {
                throw new Exception("Error updating status_approvlegalvd: " . $stmt_update->error);
            }

            // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
            $sql_select_sla_negosiator = "SELECT sla FROM master_sla WHERE divisi = 'Table Sewa'";
            $result_select_sla_negosiator = $conn->query($sql_select_sla_negosiator);
            if (!$result_select_sla_negosiator) {
                throw new Exception("Error retrieving SLA Negotiator: " . $conn->error);
            }

            if ($result_select_sla_negosiator->num_rows > 0) {
                $row_sla_negosiator = $result_select_sla_negosiator->fetch_assoc();
                $sla_negosiator_days = $row_sla_negosiator['sla'];

                // Tambahkan jumlah hari SLA Negotiator ke end_date untuk mendapatkan sla_date
                $sla_date = date('Y-m-d H:i:s', strtotime($end_date . ' + ' . $sla_negosiator_days . ' days'));

                // Masukkan data ke tabel draft
                $draft_legal = "In Process";
                $sql_insert_draft = "INSERT INTO draft (kode_lahan, slalegal_date, draft_legal) VALUES (?, ?, ?)";
                $stmt_insert_draft = $conn->prepare($sql_insert_draft);
                $stmt_insert_draft->bind_param("sss", $kode_lahan, $sla_date, $draft_legal);
                if (!$stmt_insert_draft->execute()) {
                    throw new Exception("Error inserting draft: " . $stmt_insert_draft->error);
                }
                
                // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
                $sql_select_sla_psm = "SELECT sla FROM master_sla WHERE divisi = 'Final PSM'";
                $result_select_sla_psm = $conn->query($sql_select_sla_psm);
                if (!$result_select_sla_psm) {
                    throw new Exception("Error retrieving SLA Negotiator: " . $conn->error);
                }
                
                    if ($result_select_sla_psm->num_rows > 0) {
                        $row_sla_psm = $result_select_sla_psm->fetch_assoc();
                        $sla_psm_days = $row_sla_psm['sla'];

                        // Tambahkan jumlah hari SLA Negotiator ke end_date untuk mendapatkan sla_date
                        $slapsm_date = date('Y-m-d H:i:s', strtotime($end_date . ' + ' . $sla_psm_days . ' days'));
                        // Masukkan juga perintah untuk mengupdate status_confirm_nego di tabel draft menjadi "In Process"
                        $sql_update_confirm_nego = "UPDATE draft SET confirm_nego = 'In Process', slapsm_date WHERE kode_lahan = ?";
                        $stmt_update_confirm_nego = $conn->prepare($sql_update_confirm_nego);
                        $stmt_update_confirm_nego->bind_param("ss", $slapsm_date, $kode_lahan);
                        if (!$stmt_update_confirm_nego->execute()) {
                            throw new Exception("Error updating confirm_nego: " . $stmt_update_confirm_nego->error);
                        }
                    } else {
                        throw new Exception("Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.");
                    }
            } else {
                throw new Exception("Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.");
            }
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM re WHERE id = ?";
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
        }elseif ($status_approvlegalvd == 'Pending') {
            
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM re WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Query untuk memperbarui status_vl, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
            $sql_update_re = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, status_approvlegalvd = ?, end_date = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $end_date, $id);
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
        } elseif ($status_approvlegalvd == 'In Revision') {
            
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM re WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Query untuk memperbarui status_vl, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
            $sql_update_re = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, end_date = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $end_date, $id);
            $stmt_update_re->execute();
            
            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui dan data ditahan.";
        } else {

        // Query untuk memperbarui status_approvlegalvd
        $sql_update = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, end_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $end_date, $id);
        if (!$stmt_update->execute()) {
            throw new Exception("Error updating status_approvlegalvd: " . $stmt_update->error);
        }

        // Komit transaksi
        $conn->commit();
        echo "Status berhasil diperbarui.";
    }
        // Redirect ke halaman datatables-checkval-legal.php
        header("Location: ../datatables-checkval-legal.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>