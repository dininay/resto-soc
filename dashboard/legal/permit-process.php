<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["submit_legal"])) {
    $kode_lahan = $_POST["kode_lahan"];
    var_dump($kode_lahan);
    $submit_legal = $_POST["submit_legal"];
    $submit_date = null;
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
        
        if ($submit_legal == 'Done') {
        // Query untuk memperbarui status_approvowner, catatan_owner, status_approvlegal, start_date, submit_date, status_vl, dan slavl_date
        $sql = "UPDATE sdg_desain SET submit_legal = ?, submit_date = ? WHERE kode_lahan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $submit_legal, $submit_date, $kode_lahan);
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
} elseif ($submit_legal == 'Pending') {
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui submit_legal dan catatan_owner di tabel re
        $sql = "UPDATE sdg_desain SET submit_legal = ?, submit_date = ? WHERE kode_lahan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $submit_legal, $submit_date, $kode_lahan);
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
        var_dump($submit_legal);
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
    // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui submit_legal
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
header("Location: ../datatables-sp-submit-legal.php");
exit;

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
}
?>