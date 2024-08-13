<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])  && isset($_POST["status_tenderurugan"])&& isset($_POST["catatan_tenderurugan"])) {
    $id = $_POST["id"];
    $status_tenderurugan = $_POST["status_tenderurugan"];
    $catatan_tenderurugan = $_POST["catatan_tenderurugan"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;
    $tenderurugan_date = date("Y-m-d");

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
        // Query untuk memperbarui status_tenderurugan berdasarkan id
        $sql_update = "UPDATE procurement SET status_tenderurugan = ?, catatan_tenderurugan = ?, tenderurugan_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $tenderurugan_date = date("Y-m-d");
        $stmt_update->bind_param("sssi", $status_tenderurugan, $catatan_tenderurugan, $tenderurugan_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_tenderurugan diubah menjadi 'Approve'
            if ($status_tenderurugan == 'Done') {
                // Tentukan tenderurugan_date
                $sql_subquery = "SELECT nama_vendor, tenderurugan_date FROM procurement WHERE id = ?";
                $stmt_subquery = $conn->prepare($sql_subquery);
                $stmt_subquery->bind_param("i", $id);
                $stmt_subquery->execute();
                $result_subquery = $stmt_subquery->get_result();
                
                if ($result_subquery->num_rows > 0) {
                    $row_subquery = $result_subquery->fetch_assoc();
                    $nama_vendor = $row_subquery['nama_vendor'];
                    $tenderurugan_date = $row_subquery['tenderurugan_date'];
                    // Perbarui status_lokasi menjadi 'Aktif' berdasarkan nama_vendor yang diperoleh dari subquery
                    $sql_update_status = "UPDATE vendor SET status_lokasi = 'Aktif' WHERE kode_vendor = ?";
                    $stmt_update_status = $conn->prepare($sql_update_status);
                    $stmt_update_status->bind_param("s", $nama_vendor);
                    $stmt_update_status->execute();

                    // Ambil jumlah hari dari tabel master_sla berdasarkan divisi SPK
                    $sql_sla = "SELECT sla FROM master_sla WHERE divisi = 'SPK'";
                    $result_sla = $conn->query($sql_sla);
                    if ($result_sla->num_rows > 0) {
                        $row_sla = $result_sla->fetch_assoc();
                        $hari_sla = $row_sla['sla'];
                        echo "SLA days: $hari_sla<br>";

                        // Tentukan sla_spk
                        $sla_spk = date("Y-m-d", strtotime("$tenderurugan_date + $hari_sla days"));
                        $sla_fattender = date("Y-m-d", strtotime("$tenderurugan_date + $hari_sla days"));
                        echo "SLA SPK: $sla_spk<br>";
                    } else {
                        // Rollback transaksi jika data SLA tidak ditemukan
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                    }

                    
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_tenderurugan = ?, catatan_tenderurugan = ?, tenderurugan_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $status_tenderurugan, $catatan_tenderurugan, $tenderurugan_date, $id);
                $stmt_update_pending->execute();
                    
                        // Update sla_spk dan status_spk di tabel resto
                        $sql_update_spk = "UPDATE resto SET sla_spk = ?, status_scheduleurugan = 'In Process' WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                        $stmt_update_spk = $conn->prepare($sql_update_spk);
                        $stmt_update_spk->bind_param("si", $sla_spk, $id);
                        $stmt_update_spk->execute();
                }
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
            } elseif ($status_tenderurugan == 'Pending') {
                // Ambil kode_lahan dari tabel procurement
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_tenderurugan = ?, catatan_tenderurugan = ?, tenderurugan_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $status_tenderurugan, $catatan_tenderurugan, $tenderurugan_date, $id);
                $stmt_update_pending->execute();

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
                
                    // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
                    $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'Review PSM TAF'";
                    $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
                    if ($result_sla_stkonstruksi->num_rows > 0) {
                        $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                        $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];
                        $sla_fat = date("Y-m-d", strtotime($tenderurugan_date . ' + ' . $hari_sla_stkonstruksi . ' days'));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                        exit;
                    }
                    // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_tenderurugan
                    $sql_update_other = "UPDATE procurement SET status_tenderurugan = ?, catatan_tenderurugan = ?, tenderurugan_date = ? WHERE id = ?";
                    $stmt_update_other = $conn->prepare($sql_update_other);
                    $stmt_update_other->bind_param("sssi", $status_tenderurugan, $catatan_tenderurugan, $tenderurugan_date, $id);
                
                        // Update sla_spk dan status_spk di tabel resto
                        $sql_update_spk = "UPDATE resto SET status_fat = 'In Process', sla_fat WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                        $stmt_update_spk = $conn->prepare($sql_update_spk);
                        $stmt_update_spk->bind_param("si", $sla_fat, $id);
                        $stmt_update_spk->execute();

                // Eksekusi query
                if ($stmt_update_other->execute() === TRUE) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: " . $sql_update_other . "<br>" . $conn->error;
                }
            }
            // Komit transaksi
            $conn->commit();
            echo "Status dan data berhasil diperbarui.";
            // Redirect ke halaman datatables-tender.php
            header("Location: ../datatables-tender-process.php");
            exit; // Pastikan tidak ada output lain setelah header redirect
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>