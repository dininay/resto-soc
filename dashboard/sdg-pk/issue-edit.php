<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $tanggal_retensi = $_POST['tanggal_retensi'];
    $lamp_badefect = "";

    if(isset($_FILES["lamp_badefect"])) {
        $lamp_badefect_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_badefect']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_badefect']['tmp_name'][$key];
            $file_name = $_FILES['lamp_badefect']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_badefect_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_badefect = implode(",", $lamp_badefect_paths);
    }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE issue SET lamp_badefect = ?, tanggal_retensi = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssi", $lamp_badefect, $tanggal_retensi, $id);

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