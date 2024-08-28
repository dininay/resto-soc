<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_spk"])) {
    $id = $_POST["id"];
    $status_spk = $_POST["status_spk"];
    $spk_date = date("Y-m-d H:i:s");
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;

    // Periksa apakah file kronologi ada dalam $_FILES
    if (isset($_FILES["kronologi"])) {
        $kronologi_paths = array();
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
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        $status_fat = "In Process";
        $status_legalizin = "In Process";

        if ($status_spk == 'Done') {
            // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "SPK-FAT"
            $sql_select_sla_fat = "SELECT sla FROM master_sla WHERE divisi = 'SPK-FAT'";
            $result_sla_fat = $conn->query($sql_select_sla_fat);
            
            if ($row_sla_fat = $result_sla_fat->fetch_assoc()) {
                $sla_days_fat = $row_sla_fat['sla'];

                // Hitung sla_fat dari spk_date
                $sla_fat_obj = new DateTime($spk_date);
                $days_added = 0;

                while ($days_added < $sla_days_fat) {
                    $sla_fat_obj->modify("+1 day");
                    // Periksa apakah hari ini adalah hari kerja (bukan Sabtu atau Minggu)
                    if ($sla_fat_obj->format('N') < 6) { // 1 (Senin) sampai 5 (Jumat) adalah hari kerja
                        $days_added++;
                    }
                }

                $sla_fat = $sla_fat_obj->format("Y-m-d");

                // Query untuk memperbarui status_spk dan spk_date berdasarkan id
                $sql_update = "UPDATE resto SET status_spk = ?, spk_date = ?, status_legalizin = ?, status_gostore = ?, status_fat = ?, status_land = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $status_gostore = 'In Process';
                $status_land = 'In Process';
                $stmt_update->bind_param("ssssssi", $status_spk, $spk_date, $status_legalizin, $status_gostore, $status_fat, $status_land, $id);
                $stmt_update->execute();

                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_kom = "UPDATE resto SET status_kom = 'In Process' WHERE id = ?";
                $stmt_update_kom = $conn->prepare($sql_update_kom);
                $stmt_update_kom->bind_param("i", $id);
                $stmt_update_kom->execute();

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("i", $id);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("si", $status_hold, $id);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
            } else {
                echo "SLA untuk divisi SPK-FAT tidak ditemukan.";
            }
        } elseif ($status_spk == 'Pending') {
            // Ambil kode_lahan dari tabel procurement
            $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Query untuk memperbarui status_spk dan spk_date berdasarkan id
            $sql_update = "UPDATE resto SET status_spk = ?, spk_date = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $status_spk, $spk_date, $id);
            $stmt_update->execute();

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
        } elseif ($status_spk == 'In Review by TAF') {
            // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
            $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'Review PSM TAF'";
            $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
            if ($result_sla_stkonstruksi->num_rows > 0) {
                $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];

                // Hitung SLA hanya pada hari kerja
                $sla_fat_obj = new DateTime($spk_date);
                $days_added = 0;

                while ($days_added < $hari_sla_stkonstruksi) {
                    $sla_fat_obj->modify("+1 day");
                    // Periksa apakah hari ini adalah hari kerja (bukan Sabtu atau Minggu)
                    if ($sla_fat_obj->format('N') < 6) { // 1 (Senin) sampai 5 (Jumat) adalah hari kerja
                        $days_added++;
                    }
                }

                $sla_fat = $sla_fat_obj->format("Y-m-d");

                $status_fat = "In Process";
                // Query untuk memperbarui status_spk dan spk_date berdasarkan id
                $sql_update = "UPDATE resto SET status_spk = ?, spk_date = ?, sla_fat = ?, status_fat = ?, status_legalizin = ?, status_gostore = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $status_gostore = 'In Process';
                $stmt_update->bind_param("ssssssi", $status_spk, $spk_date, $sla_fat, $status_fat, $status_legalizin, $status_gostore, $id);
                $stmt_update->execute();

                // Komit transaksi
                $conn->commit();
                echo "<script>
                        alert('Status berhasil diperbarui.');
                        window.location.href = window.location.href;
                     </script>";
            } else {
                $conn->rollback();
                echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                exit;
            }
        }
        
        // Redirect ke halaman datatables-spk.php setelah proses selesai
        header("Location: ../datatables-spk-sdgpk.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
