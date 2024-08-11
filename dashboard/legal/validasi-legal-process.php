<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["status_approvlegal"]) && isset($_POST["catatan_legal"])) {
    $kode_lahan = $_POST["kode_lahan"];
    $status_approvlegal = $_POST["status_approvlegal"];
    $catatan_legal = $_POST["catatan_legal"];
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
        // Debug: cetak path file yang dihasilkan
        echo "Kronologi paths: " . $kronologi . "<br>";
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
        // Debug: cetak pesan tidak ada file diunggah
        echo "Tidak ada file kronologi yang diunggah.<br>";
    }

    // Inisialisasi variabel untuk status_approvlegal
    $status_approvnego = null;
    $end_date = null;
    $nego_date = null;

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        if ($status_approvlegal == 'Approve') {
            $status_approvnego = 'In Process';
            $status_vl = 'In Process';
            $end_date = date("Y-m-d H:i:s");

            // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = VL
            $sql_select_sla_vl = "SELECT sla FROM master_sla WHERE divisi = 'VL'";
            $result_select_sla_vl = $conn->query($sql_select_sla_vl);

            if ($result_select_sla_vl && $result_select_sla_vl->num_rows > 0) {
                $row_sla_vl = $result_select_sla_vl->fetch_assoc();
                $sla_vl_days = $row_sla_vl['sla'];

                // Tambahkan jumlah hari SLA VL ke end_date untuk mendapatkan vl_date
                $vl_date = date('Y-m-d H:i:s', strtotime($end_date . ' + ' . $sla_vl_days . ' days'));

                // Query untuk memperbarui status_approvlegal, status_approvnego, status_vl, end_date, dan vl_date
                $sql = "UPDATE re SET status_approvlegal = ?, catatan_legal = ?, status_approvnego = ?, end_date = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $status_approvlegal, $catatan_legal, $status_approvnego, $end_date, $kode_lahan);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error: Tkode_lahanak dapat mengambil data SLA VL dari tabel master_sla.";
            }
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
        } elseif ($status_approvlegal == 'Reject') {
            // Ambil kode lahan sebelum menghapus dari tabel re
            $sql_select_kode_lahan = "SELECT kode_lahan FROM re WHERE kode_lahan = ?";
            $stmt_select_kode_lahan = $conn->prepare($sql_select_kode_lahan);
            $stmt_select_kode_lahan->bind_param("i", $kode_lahan);
            $stmt_select_kode_lahan->execute();
            $stmt_select_kode_lahan->bind_result($kode_lahan);
            $stmt_select_kode_lahan->fetch();
            $stmt_select_kode_lahan->close();

            // Hapus data dari tabel re berdasarkan kode_lahan
            $sql_delete_re = "DELETE FROM re WHERE kode_lahan = ?";
            $stmt_delete_re = $conn->prepare($sql_delete_re);
            $stmt_delete_re->bind_param("s", $kode_lahan);
            $stmt_delete_re->execute();
            $stmt_delete_re->close();

            // Perbarui status_land menjadi Reject pada tabel land berdasarkan kode lahan
            $sql_update_land = "UPDATE land SET status_land = 'Reject', status_approve = 'In Process' WHERE kode_lahan = ?";
            $stmt_update_land = $conn->prepare($sql_update_land);
            $stmt_update_land->bind_param("s", $kode_lahan);
            $stmt_update_land->execute();
            $stmt_update_land->close();
        } elseif ($status_approvlegal == 'Pending') {
            // Mulai transaksi
            $conn->begin_transaction();
    
            try {
                // Query untuk memperbarui status_approvowner dan catatan_owner di tabel re
                $sql = "UPDATE re SET status_approvlegal = ?, catatan_legal = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $status_approvlegal, $catatan_legal, $kode_lahan);
                $stmt->execute();
    
                var_dump($kode_lahan);
    
                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");
    
                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $stmt_hold->execute();
    
                var_dump($kronologi);
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $conn->rollback();
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Query untuk memperbarui status_approvlegal
            $sql = "UPDATE re SET status_approvlegal = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status_approvlegal, $kode_lahan);
            $stmt->execute();
            $stmt->close();
        }

        // Komit transaksi
        $conn->commit();
        echo "Status berhasil diperbarui.";
            // Redirect ke halaman datatables-kom-sdgpk.php
    header("Location: ../datatables-validasi-lahan-legal.php");
    exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>