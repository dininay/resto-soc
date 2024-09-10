<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_obslegal"]) && isset($_POST["catatan_obslegal"])) {
    $id = $_POST["id"];
    $status_obslegal = $_POST["status_obslegal"];
    $catatan_obslegal = $_POST["catatan_obslegal"];
    $end_date = date("Y-m-d H:i:s");
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

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_obslegal berdasarkan id
        $sql_update_sdg_desain = "UPDATE sdg_desain SET status_obslegal = ?, catatan_obslegal = ?, end_date = ? WHERE id = ?";
        $stmt_update_sdg_desain = $conn->prepare($sql_update_sdg_desain);
        $stmt_update_sdg_desain->bind_param("sssi", $status_obslegal, $catatan_obslegal, $end_date, $id);

        // Eksekusi query
        if ($stmt_update_sdg_desain->execute() === TRUE) {
            // Jika status_obslegal diubah menjadi Approve
            if ($status_obslegal == 'Done') {
                // Ambil data dari tabel sdg_desain berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan, end_date, lamp_desainplan FROM sdg_desain WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                if ($row = $result_select->fetch_assoc()) {
                    // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "SDG-QS"
                    $sql_select_sla_qs = "SELECT sla FROM master_sla WHERE divisi = 'QS'";
                    $result_sla_qs = $conn->query($sql_select_sla_qs);
                    
                    // Ambil kode_lahan dari tabel re
                    $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
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
                }
            } elseif ($status_obslegal == 'Pending') {
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
    
                // Query untuk memperbarui status_obslegal, end_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE sdg_desain SET status_obslegal = ?, catatan_obslegal = ?, end_date = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $status_obslegal, $catatan_obslegal, $end_date, $id);
                $stmt_update_re->execute();
    
                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");

                // Debugging: tampilkan data yang akan dimasukkan ke tabel hold_project
                echo "Data yang akan dimasukkan ke hold_project: <br>";
                echo "kode_lahan: " . $kode_lahan . "<br>";
                echo "issue_detail: " . $issue_detail . "<br>";
                echo "pic: " . $pic . "<br>";
                echo "action_plan: " . $action_plan . "<br>";
                echo "due_date: " . $due_date . "<br>";
                echo "status_hold: " . $status_hold . "<br>";
                echo "kronologi: " . $kronologi . "<br>";
    
                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                
                // Eksekusi query dan periksa keberhasilan
                if ($stmt_hold->execute() === TRUE) {
                    echo "Data berhasil dimasukkan ke tabel hold_project.";
                } else {
                    echo "Error: " . $sql_hold . "<br>" . $conn->error;
                }

                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } elseif ($status_obslegal == 'In Process') {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_obslegal di tabel re
                $sql = "UPDATE sdg_desain SET status_obslegal = ?, catatan_obslegal = ?, end_date = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $status_obslegal, $catatan_obslegal, $end_date, $id);
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
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_obslegal di tabel re
                $sql = "UPDATE sdg_desain SET status_obslegal = ?, catatan_obslegal = ?, end_date = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $status_obslegal, $catatan_obslegal, $end_date, $id);
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
        } else {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $sql_update_sdg_desain . "<br>" . $conn->error;
        }
        header("Location: ../datatables-design-legal.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
