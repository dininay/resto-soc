<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
$lamp_legalizin = "";

    if(isset($_FILES["lamp_legalizin"])) {
        $lamp_legalizin_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_legalizin']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_legalizin']['tmp_name'][$key];
            $file_name = $_FILES['lamp_legalizin']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_legalizin_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_legalizin = implode(",", $lamp_legalizin_paths);
    }

    // Update data di database
    $sql = "UPDATE resto SET lamp_legalizin = '$lamp_legalizin' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-spk-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
