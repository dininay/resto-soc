<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kpt_1 = $_POST['kpt_1'];
    $lamp_kpt1 = "";

    if(isset($_FILES["lamp_kpt1"])) {
        $lamp_kpt1_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_kpt1']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_kpt1']['tmp_name'][$key];
            $file_name = $_FILES['lamp_kpt1']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_kpt1_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_kpt1 = implode(",", $lamp_kpt1_paths);
    }
    // Update data di database
    $sql = "UPDATE socdate_academy SET lamp_kpt1 = '$lamp_kpt1', kpt_1 = '$kpt_1' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-hr-kpt.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
