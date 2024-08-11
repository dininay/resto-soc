<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_approvnego"]) && isset($_POST["catatan_nego"]) && isset($_POST["deal_sewa"]) && isset($_POST["masa_berlaku"])) {
    $id = $_POST["id"];
    $status_approvnego = $_POST["status_approvnego"];
    $catatan_nego = $_POST["catatan_nego"];
    $deal_sewa = $_POST["deal_sewa"];
    $masa_berlaku = $_POST["masa_berlaku"];
    $nego_date = null;
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

    // Inisialisasi variabel untuk tanggal negosiasi
    $nego_date = null;

    
    try {
        $sql_get_kode_lahan = "SELECT kode_lahan FROM re WHERE id = ?";
        $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
        $stmt_get_kode_lahan->bind_param("i", $id);
        $stmt_get_kode_lahan->execute();
        $stmt_get_kode_lahan->bind_result($kode_lahan);
        $stmt_get_kode_lahan->fetch();
        $stmt_get_kode_lahan->free_result();

        // Jika kode_lahan tidak ditemukan, lemparkan exception
        if (empty($kode_lahan)) {
            throw new Exception("Kode lahan tidak ditemukan untuk id: $id.");
        }
        // Jika status_approvnego diubah menjadi Approve, ambil tanggal saat ini dan SLA dari divisi terkait
        if ($status_approvnego == 'Approve') {
            $nego_date = date("Y-m-d H:i:s");

            // Ambil SLA dari tabel master_sla dengan divisi = LOA-CD
            $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'LOA-CD'";
            $result_select_sla = $conn->query($sql_select_sla);

            if ($result_select_sla && $result_select_sla->num_rows > 0) {
                $row_sla = $result_select_sla->fetch_assoc();
                $sla = $row_sla['sla'];
            } else {
                throw new Exception("Tidak dapat mengambil data SLA dari tabel master_sla.");
            }

            // Ambil SLA dari tabel master_sla dengan divisi = Design
            $sql_select_sla_sdgd = "SELECT sla FROM master_sla WHERE divisi = 'Layouting'";
            $result_select_sla_sdgd = $conn->query($sql_select_sla_sdgd);

            if ($result_select_sla_sdgd && $result_select_sla_sdgd->num_rows > 0) {
                $row_sla_sdgd = $result_select_sla_sdgd->fetch_assoc();
                $sla_sdgd = $row_sla_sdgd['sla'];
            } else {
                throw new Exception("Tidak dapat mengambil data SLA dari tabel master_sla.");
            }
            
            $nego_date = date("Y-m-d H:i:s");
            // Query untuk memperbarui status_approvnego, catatan_nego, dan nego_date pada tabel re
            $sql = "UPDATE re SET status_approvnego = ?, catatan_nego = ?, nego_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $status_approvnego, $catatan_nego, $nego_date, $id);

            // Eksekusi query
            if ($stmt->execute() === TRUE) {
                // Jika status diubah menjadi Approve, tambahkan data ke tabel dokumen_loacd dan sdg_desain
                $sla_date = date('Y-m-d', strtotime($nego_date . ' + ' . $sla . ' days'));
                $sla_survey = date('Y-m-d', strtotime($nego_date . ' + ' . $sla_sdgd . ' days'));

                // Query untuk mengambil data yang diperbarui
                $sql_select = "SELECT * FROM re WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                $updated_row = $result_select->fetch_assoc();

                // Variabel tambahan untuk dokumen_loacd
                $status_approvloacd = "In Process";
                $confirm_sdgdesain = "In Process";
                $status_obssdg = "In Process";
                $confirm_layout = "In Process";

                // Insert data ke tabel dokumen_loacd
                $sql_dokumen = "INSERT INTO dokumen_loacd (kode_lahan, status_approvloacd, slaloa_date, masa_berlaku, deal_sewa) 
                                VALUES (?, ?, ?, ?, ?)";
                $stmt_dokumen = $conn->prepare($sql_dokumen);
                $stmt_dokumen->bind_param("sssss", $updated_row['kode_lahan'], $status_approvloacd, $sla_date, $masa_berlaku, $deal_sewa);
                $stmt_dokumen->execute();

                // Insert data ke tabel sdg_desain
                $sql_sdgd = "INSERT INTO sdg_desain (kode_lahan, confirm_sdgdesain, status_obssdg, sla_survey) 
                             VALUES (?, ?, ?, ?)";
                $stmt_sdgd = $conn->prepare($sql_sdgd);
                $stmt_sdgd->bind_param("ssss", $updated_row['kode_lahan'], $confirm_sdgdesain, $status_obssdg, $sla_survey);
                $stmt_sdgd->execute();

                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_resto = "INSERT INTO resto (kode_lahan) VALUES (?)";
                $stmt_resto = $conn->prepare($sql_resto);
                $stmt_resto->bind_param("s", $updated_row['kode_lahan']);
                $stmt_resto->execute();

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("s", $updated_row['kode_lahan']);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("ss", $status_hold, $updated_row['kode_lahan']);
                    $stmt_update_hold->execute();
                }
            } else {
                throw new Exception("Tidak dapat memperbarui status pada tabel re.");
            }
        } elseif ($status_approvnego == 'Pending') {
            $nego_date = date("Y-m-d H:i:s");
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM re WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Query untuk memperbarui status_approvnego dan catatan_nego di tabel re
            $sql = "UPDATE re SET status_approvnego = ?, catatan_nego = ?, nego_date = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $status_approvnego, $catatan_nego, $nego_date, $kode_lahan);
            $stmt->execute();

            $status_hold = "In Process";
            $due_date = date("Y-m-d H:i:s");

                // Query untuk memasukkan data ke dalam tabel hold_project
            $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_hold = $conn->prepare($sql_hold);
            $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
            $stmt_hold->execute();
        } else {
            // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_approvnego
            $sql = "UPDATE re SET status_approvnego = ?, catatan_nego = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_approvnego, $catatan_nego, $id);
            $stmt->execute();
        }

        // Komit transaksi
        $conn->commit();
              
        // Redirect ke halaman datatables-doc-confirm-negosiator.php
        header("Location: ../datatables-doc-confirm-negosiator.php");
        exit; // Pastikan tidak ada output lain setelah header redirect

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>