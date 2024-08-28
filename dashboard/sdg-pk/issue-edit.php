<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $pic = $_POST['pic'];
    $target_done = $_POST['target_done'];
    $issue = $_POST['issue'];
    $lamp_defect = "";

    if(isset($_FILES["lamp_defect"])) {
        $lamp_defect_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_defect']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_defect']['tmp_name'][$key];
            $file_name = $_FILES['lamp_defect']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_defect_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_defect = implode(",", $lamp_defect_paths);
    }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE issue SET lamp_defect = ?, pic = ?, target_done = ?, issue = ?, status_defect = ?, defect_date = ? WHERE kode_lahan = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("sssssss", $lamp_defect, $pic, $target_done, $issue, $status_defect, $defect_date, $kode_lahan);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-sdgpk-issue.php");
        exit();
    } else {
        echo "Error: " . $stmt1->error;
    }

    // Close statements
    $stmt1->close();
}

// Menutup koneksi database
$conn->close();
?>