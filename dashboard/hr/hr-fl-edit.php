<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $fl = $_POST['fl'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_fl = "";

    if(isset($_FILES["lamp_fl"])) {
        $lamp_fl_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_fl']['name'] as $key => $filename) {
            $file_flp = $_FILES['lamp_fl']['flp_name'][$key];
            $file_name = $_FILES['lamp_fl']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_flp, $target_file)) {
                $lamp_fl_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_fl = implode(",", $lamp_fl_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_hr SET lamp_fl = '$lamp_fl', fl = '$fl' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-hr-fl.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
