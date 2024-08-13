<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_qsurugan"]) && isset($_POST["catatan_qsurugan"])) {
    $id = $_POST["id"];
    $confirm_qsurugan = $_POST["confirm_qsurugan"];
    $catatan_qsurugan = $_POST["catatan_qsurugan"];
    $qsurugan_date = date("Y-m-d H:i:s");
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
        // Query untuk memperbarui confirm_qsurugan berdasarkan id
        $sql_update = "UPDATE sdg_rab SET confirm_qsurugan = ?, catatan_qsurugan = ?, qsurugan_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $confirm_qsurugan, $catatan_qsurugan, $qsurugan_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika confirm_qsurugan diubah menjadi Approve
            if ($confirm_qsurugan == 'Approve') {
                // Ambil data dari tabel sdg_rab berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                if ($row = $result_select->fetch_assoc()) {
                    // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "Tender"
                    $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'Tender'";
                    $result_sla = $conn->query($sql_select_sla);

                    if ($row_sla = $result_sla->fetch_assoc()) {
                        $sla_days = $row_sla['sla'];
                        $end_date_obj = new DateTime($qsurugan_date);
                        $end_date_obj->modify("+$sla_days days");
                        $sla_spkrab = $end_date_obj->format("Y-m-d");
                        $slatenderurugan_date = $end_date_obj->format("Y-m-d");
                        $sla_spkurugan = $end_date_obj->format("Y-m-d");

                        // Masukkan data ke tabel procurement
                        $sql_insert = "INSERT INTO procurement (kode_lahan, status_procururugan, sla_spkurugan, status_tenderurugan, slatenderurugan_date) VALUES (?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $status_approvprocurement = 'In Process';
                        $status_procururugan = 'In Process';
                        $status_tenderurugan = 'In Process';
                        $stmt_insert->bind_param("sssss", $row['kode_lahan'], $status_procururugan, $sla_spkurugan, $status_tenderurugan, $slatenderurugan_date);
                        $stmt_insert->execute();
                    } else {
                        $conn->rollback();
                        echo "Error: SLA not found for divisi: Tender.";
                        exit;
                    }
                } else {
                    // Rollback transaksi jika terjadi kesalahan pada select
                    $conn->rollback();
                    echo "Error: Data not found for id: $id.";
                    exit;
                }

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("s", $row['kode_lahan']);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("ss", $status_hold, $row['kode_lahan']);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
            } elseif ($confirm_qsurugan == 'Pending') {
                // Ambil kode_lahan dari tabel sdg_rab
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_pending = "UPDATE sdg_rab SET confirm_qsurugan = ?, catatan_qsurugan = ?, qsurugan_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $confirm_qsurugan, $catatan_qsurugan, $qsurugan_date, $id);
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
            } elseif ($confirm_qsurugan == 'In Design Revision') {
                // Ambil kode_lahan dari tabel sdg_rab
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_pending = "UPDATE sdg_rab SET confirm_qsurugan = ?, catatan_qsurugan = ?, qsurugan_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $confirm_qsurugan, $catatan_qsurugan, $qsurugan_date, $id);
                $stmt_update_pending->execute();

                $confirm_qsurugan = "In Design Revision";
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_design = "UPDATE sdg_desain SET confirm_qsurugan = ? WHERE kode_lahan = ?";
                $stmt_update_design = $conn->prepare($sql_update_design);
                $stmt_update_design->bind_param("ss", $confirm_qsurugan, $kode_lahan);
                $stmt_update_design->execute();

                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_$status_obssdg
                $sql_update_other = "UPDATE sdg_rab SET confirm_qsurugan = ?, catatan_qsurugan = ?, qsurugan_date = ? WHERE id = ?";
                $stmt_update_other = $conn->prepare($sql_update_other);
                $stmt_update_other->bind_param("sssi", $confirm_qsurugan, $catatan_qsurugan, $qsurugan_date, $id);

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
        } else {
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        // Redirect ke halaman datatables-approval-owner.php
        header("Location: ../datatables-rab-urugan.php");
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>