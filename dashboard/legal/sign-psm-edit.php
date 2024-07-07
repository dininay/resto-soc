<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
$lamp_signpsm = "";

    if(isset($_FILES["lamp_signpsm"])) {
        $lamp_signpsm_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_signpsm']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_signpsm']['tmp_name'][$key];
            $file_name = $_FILES['lamp_signpsm']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_signpsm_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_signpsm = implode(",", $lamp_signpsm_paths);
    }

    // Update data di database
    $sql = "UPDATE draft SET lamp_signpsm = '$lamp_signpsm' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-sign-psm-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
