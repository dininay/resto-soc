<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_lahan = $_POST['kode_lahan'];
    
    $week_1 = floatval($_POST['week_1']);
    $week_2 = floatval($_POST['week_2']);
    $week_3 = floatval($_POST['week_3']);
    $week_4 = floatval($_POST['week_4']);
    $week_5 = floatval($_POST['week_5']);
    $week_6 = floatval($_POST['week_6']);
    $week_7 = floatval($_POST['week_7']);
    $week_8 = floatval($_POST['week_8']);
    $week_9 = floatval($_POST['week_9']);
    $week_10 = floatval($_POST['week_10']);
    $week_11 = floatval($_POST['week_11']);
    $week_12 = floatval($_POST['week_12']);
    $week_13 = floatval($_POST['week_13']);
    $week_14 = floatval($_POST['week_14']);
    $week_15 = floatval($_POST['week_15']);

    $lamp_monitoring = "";

    if (isset($_FILES['lamp_monitoring']) && $_FILES['lamp_monitoring']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $existing_files = isset($_POST['existing_files']) ? explode(",", $_POST['existing_files']) : array();
        $new_files = array();

        foreach ($_FILES['lamp_monitoring']['name'] as $key => $filename) {
            if ($filename) {
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($filename);

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['lamp_monitoring']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename);
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_monitoring']['name'][$key] . "<br>";
                }
            }
        }

        $all_files = array_merge($existing_files, $new_files);
        $lamp_monitoring = implode(",", array_filter($all_files));
    } else {
        $lamp_monitoring = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }

    // Update data di tabel konstruksi
    $sql = "UPDATE konstruksi SET week_1 = ?, week_2 = ?, week_3 = ?, week_4 = ?, week_5 = ?, week_6 = ?, week_7 = ?, week_8 = ?, week_9 = ?, week_10 = ?, week_11 = ?, week_12 = ?, week_13 = ?, week_14 = ?, week_15 = ?, lamp_monitoring = ? WHERE kode_lahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddddddsssddddss", $week_1, $week_2, $week_3, $week_4, $week_5, $week_6, $week_7, $week_8, $week_9, $week_10, $week_11, $week_12, $week_13, $week_14, $week_15, $lamp_monitoring, $kode_lahan);

    if ($stmt->execute()) {
        // Update tabel sdg_pk berdasarkan kode_lahan
        $month_1 = $week_1 + $week_2 + $week_3 + $week_4 + $week_5;
        $month_2 = $week_6 + $week_7 + $week_8 + $week_9 + $week_10;
        $month_3 = $week_11 + $week_12 + $week_13 + $week_14 + $week_15;

        $sql_update_sdg_pk = "UPDATE sdg_pk SET month_1 = ?, month_2 = ?, month_3 = ? WHERE kode_lahan = ?";
        $stmt_update = $conn->prepare($sql_update_sdg_pk);
        $stmt_update->bind_param("ddds", $month_1, $month_2, $month_3, $kode_lahan);
        $stmt_update->execute();

        // Redirect ke halaman data tabel setelah selesai
        header("Location:  " . $base_url . "/datatables-monitoring-op.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
