<?php
// Koneksi ke database
include "../../koneksi.php";
// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_fat"])) {
    $id = $_POST["id"];
    $status_fat = $_POST["status_fat"];
    $fat_date = date("Y-m-d H:i:s");
    $lamp_signedtaf = null;

    // Periksa apakah file lamp_signedtaf ada dalam $_FILES
    if (isset($_FILES["lamp_signedtaf"])) {
        $lamp_signedtaf_paths = array();
        foreach ($_FILES['lamp_signedtaf']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_signedtaf']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_signedtaf_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_signedtaf = implode(",", $lamp_signedtaf_paths);
    }
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
        // Query untuk memperbarui status_fat dan fat_date berdasarkan id
        $sql_update = "UPDATE resto SET status_fat = ?, fat_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $status_fat, $fat_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_spk diubah menjadi Approve
            if ($status_fat == 'Signed') {
                $spk_date = date("Y-m-d H:i:s");
                $status_legalizin = "In Process";
                $status_gostore = 'In Process';
                $status_spkwo = "Done";
                $status_approvprocurement = "Done";
                $status_procurspkwofa = "Done";
                $status_spkwoipal = "Done";
                $status_eqpdevprocur = "Done";
                $status_eqpsite = "In Process";
                $status_eqpdev = "Done";

                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_kom = "UPDATE resto SET status_schedule = 'In Process', lamp_signedtaf = ?, status_fat = ?, fat_date = ?, status_spk = 'Signed', spk_date = ?, status_legalizin = ?, status_gostore = ? WHERE id = ?";
                $stmt_update_kom = $conn->prepare($sql_update_kom);
                $stmt_update_kom->bind_param("ssssssi", $lamp_signedtaf, $status_fat, $fat_date, $spk_date, $status_legalizin, $status_gostore, $id);
                $stmt_update_kom->execute();
                
                $sql_get_kode_lahan = "SELECT kode_lahan FROM resto WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_design = "UPDATE sdg_desain SET status_spkwo = ? WHERE kode_lahan = ?";
                $stmt_update_design = $conn->prepare($sql_update_design);
                $stmt_update_design->bind_param("ss", $status_spkwo,  $kode_lahan);
                $stmt_update_design->execute();
                
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_procur = "UPDATE procurement SET status_approvprocurement = ? WHERE kode_lahan = ?";
                $stmt_update_procur = $conn->prepare($sql_update_procur);
                $stmt_update_procur->bind_param("ss", $status_approvprocurement,  $kode_lahan);
                $stmt_update_procur->execute();
                
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_soc = "UPDATE socdate_sdg SET status_procurspkwofa = ?, status_spkwoipal = ? WHERE kode_lahan = ?";
                $stmt_update_soc = $conn->prepare($sql_update_soc);
                $stmt_update_soc->bind_param("sss", $status_procurspkwofa, $status_spkwoipal, $kode_lahan);
                $stmt_update_soc->execute();
                
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_eqp = "UPDATE equipment SET status_eqpdevprocur = ? , status_eqpsite = ?, status_eqpdev = ? WHERE kode_lahan = ?";
                $stmt_update_eqp = $conn->prepare($sql_update_eqp);
                $stmt_update_eqp->bind_param("ssss", $status_eqpdevprocur, $status_eqpsite, $status_eqpdev, $kode_lahan);
                $stmt_update_eqp->execute();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_approvprocurement = 'Signed' WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("i", $id);
                $stmt_update_pending->execute();
                
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
            } elseif ($status_fat == 'Pending') {
                // Ambil kode_lahan dari tabel procurement
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE resto SET status_fat = ?, fat_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssi", $status_fat, $fat_date, $id);
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
            } elseif ($status_fat == 'In Revision') {
                // Ambil kode_lahan dari tabel procurement
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_approvprocurement = 'In Revision' WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("i", $id);
                $stmt_update_pending->execute();
                
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_kom = "UPDATE resto SET status_fat = ?, fat_date = ?, status_spk = 'In Revision' WHERE id = ?";
                $stmt_update_kom = $conn->prepare($sql_update_kom);
                $stmt_update_kom->bind_param("ssi", $status_fat, $fat_date, $id);
                $stmt_update_kom->execute();

                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_approvprocurement
                $sql_update_other = "UPDATE resto SET status_fat = ?, fat_date = ? WHERE id = ?";
                $stmt_update_other = $conn->prepare($sql_update_other);
                $stmt_update_other->bind_param("ssi", $status_fat, $fat_date, $id);

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
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $stmt_update->error;
        }
        // Redirect ke halaman datatables-spk-fat.php
        header("Location: ../datatables-spk-fat.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}