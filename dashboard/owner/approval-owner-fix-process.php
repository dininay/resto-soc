<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["status_approvowner"]) && isset($_POST["catatan_owner"])) {
    $kode_lahan = $_POST["kode_lahan"];
    var_dump($kode_lahan);
    $status_approvowner = $_POST["status_approvowner"];
    $catatan_owner = $_POST["catatan_owner"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;

    // Inisialisasi variabel untuk status_approvlegal
    $status_approvnego = null;
    $start_date = null;
    
    // Jika status_approvowner diubah menjadi Approve, ubah status_approvlegal menjadi In Process
    if ($status_approvowner == 'Approve') {
        $status_approvnego = 'In Process';
        $status_vl = 'In Process';
        $start_date = date("Y-m-d H:i:s");

        // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = VL
            $sql_select_sla_vl = "SELECT sla FROM master_sla WHERE divisi = 'Negosiator'";
            $result_select_sla_vl = $conn->query($sql_select_sla_vl);

            if ($result_select_sla_vl && $result_select_sla_vl->num_rows > 0) {
                $row_sla_vl = $result_select_sla_vl->fetch_assoc();
                $sla_vl_days = $row_sla_vl['sla'];

                // Tambahkan jumlah hari SLA VL ke end_date untuk mendapatkan vl_date
                $slanego_date = date('Y-m-d H:i:s', strtotime($start_date . ' + ' . $sla_vl_days . ' days'));

                // // Query untuk memperbarui status_approvlegal, status_approvnego, status_vl, end_date, dan vl_date
                // $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ?, status_approvnego = ?, start_date = ? WHERE kode_lahan = ?";
                // $stmt = $conn->prepare($sql);
                // $stmt->bind_param("sssss", $status_approvowner, $catatan_owner, $status_approvnego, $start_date, $kode_lahan);
                // $stmt->execute();
                // $stmt->close();
            } else {
                echo "Error: Tkode_lahanak dapat mengambil data SLA VL dari tabel master_sla.";
            }

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Query untuk memperbarui status_approvowner, catatan_owner, status_approvlegal, start_date, slalegal_date, status_vl, dan slavl_date
            $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ?, status_approvnego = ?, start_date = ?, slanego_date = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $status_approvowner, $catatan_owner, $status_approvnego, $start_date, $slanego_date, $kode_lahan);
            $stmt->execute();

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
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } elseif ($status_approvowner == 'Reject') {
        // Ambil kode lahan sebelum menghapus dari tabel re
        $sql = "SELECT kode_lahan FROM re WHERE kode_lahan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $kode_lahan);
        $stmt->execute();
        $stmt->bind_result($kode_lahan);
        $stmt->fetch();
        $stmt->close();

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Hapus data dari tabel re berdasarkan kode_lahan
            $sql = "DELETE FROM re WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $kode_lahan);
            $stmt->execute();

            // Perbarui status_land menjadi Reject pada tabel land berdasarkan kode lahan
            $sql = "UPDATE land SET status_land = 'Reject' WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $kode_lahan);
            $stmt->execute();

            // Komit transaksi
            $conn->commit();
            echo "Data berhasil dihapus dan status berhasil diperbarui.";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } elseif ($status_approvowner == 'Pending') {
        // Mulai transaksi
        $conn->begin_transaction();

        try {
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
            // Query untuk memperbarui status_approvowner dan catatan_owner di tabel re
            $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $status_approvowner, $catatan_owner, $kode_lahan);
            $stmt->execute();

            var_dump($kode_lahan);

            $status_hold = "In Process";
            $due_date = date("Y-m-d H:i:s");

            // Query untuk memasukkan data ke dalam tabel hold_project
            $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_hold = $conn->prepare($sql_hold);
            $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
            $stmt_hold->execute();

            var_dump($kode_lahan);
            var_dump($status_approvowner);
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
        // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_approvowner
        // $sql = "UPDATE re SET status_approvowner = ? WHERE kode_lahan = ?";
        // $stmt = $conn->prepare($sql);
        // $stmt->bind_param("ss", $status_approvowner, $kode_lahan);

        // Eksekusi query
        if ($stmt->execute() === TRUE) {
            echo "<script>
                    alert('Status berhasil diperbarui.');
                    window.location.href = window.location.href;
                 </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    // Redirect ke halaman datatables-approval-owner.php
    header("Location: ../datatables-approval-owner.php");
    exit;
}
?>