<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {

// Ambil data dari form
$notes = $_POST["notes"];
$date = $_POST["date"];
$updated_by = $_POST["updated_by"];
$status = $_POST["status"];
$file = "";

    if(isset($_FILES["file"])) {
        $file_paths = array();

        // Loop through each file
        foreach($_FILES['file']['name'] as $key => $filename) {
            $file_tmp = $_FILES['file']['tmp_name'][$key];
            $file_name = $_FILES['file']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $file_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $file = implode(",", $file_paths);
    }

    // Update kolom status_approvlegal dan catatan_legal di tabel re
    $sql = "INSERT INTO mom (notes, date, updated_by, status, file) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $notes, $date, $updated_by, $status, $file);

    if ($stmt->execute()) {
        // Redirect ke halaman datatable-land-sourcing
        header("Location: " . $base_url . "/datatables-mom-pmo.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi database
$conn->close();
?>
