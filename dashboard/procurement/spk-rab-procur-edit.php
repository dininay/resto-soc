<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'];
            // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_spkrabcons = "";

    if(isset($_FILES["lamp_spkrabcons"])) {
        $lamp_spkrabcons_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_spkrabcons']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_spkrabcons']['tmp_name'][$key];
            $file_name = $_FILES['lamp_spkrabcons']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_spkrabcons_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_spkrabcons = implode(",", $lamp_spkrabcons_paths);
    }
    $status_approvprocurement = "In Process";
    // Update data di database
    $sql = "UPDATE procurement SET lamp_spkrabcons = '$lamp_spkrabcons', status_approvprocurement = '$status_approvprocurement' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-checkval-rab-from-sdg.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
