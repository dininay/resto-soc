<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $kode_lahan = $_POST['kode_lahan'];
    $status_wojobadd = "In Process";
    $status_rabjobadd = "In Process";
    $lamp_wojobadd = "";

    if(isset($_FILES["lamp_wojobadd"])) {
        $lamp_wojobadd_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_wojobadd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_wojobadd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_wojobadd']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_wojobadd_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_wojobadd = implode(",", $lamp_wojobadd_paths);
    }
    
    $defect_date = date('Y-m-d');
    // Update data di database untuk tabel resto
    $sql1 = "INSERT INTO jobadd (kode_lahan, lamp_wojobadd, status_wojobadd, status_rabjobadd) VALUES (?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssss", $kode_lahan, $lamp_wojobadd, $status_wojobadd, $status_rabjobadd);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-wo-tambahkurang.php");
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