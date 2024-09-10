<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'];
            // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_rabjobadd = "";

    if(isset($_FILES["lamp_rabjobadd"])) {
        $lamp_rabjobadd_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_rabjobadd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_rabjobadd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_rabjobadd']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_rabjobadd_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_rabjobadd = implode(",", $lamp_rabjobadd_paths);
    }
    // Update data di database
    $sql = "UPDATE jobadd SET lamp_rabjobadd = '$lamp_rabjobadd' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-rab-tambahkurang.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
