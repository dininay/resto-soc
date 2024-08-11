<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kode_dvr = $_POST['kode_dvr'];
    $web_report = $_POST['web_report'];
    $akun_gis = $_POST['akun_gis'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_config = "";

    if(isset($_FILES["lamp_config"])) {
        $lamp_config_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_config']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_config']['tmp_name'][$key];
            $file_name = $_FILES['lamp_config']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_config_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_config = implode(",", $lamp_config_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_printer = "";

    if(isset($_FILES["lamp_printer"])) {
        $lamp_printer_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_printer']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_printer']['tmp_name'][$key];
            $file_name = $_FILES['lamp_printer']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_printer_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_printer = implode(",", $lamp_printer_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_it SET lamp_config = '$lamp_config', lamp_printer = '$lamp_printer', kode_dvr = '$kode_dvr', web_report = '$web_report', akun_gis = '$akun_gis' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-it-config.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
