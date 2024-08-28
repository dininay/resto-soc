<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $email = $_POST['email'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_qris = "";

    if(isset($_FILES["lamp_qris"])) {
        $lamp_qris_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_qris']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_qris']['tmp_name'][$key];
            $file_name = $_FILES['lamp_qris']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_qris_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_qris = implode(",", $lamp_qris_paths);
    }

    // // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_st = "";

    if(isset($_FILES["lamp_st"])) {
        $lamp_st_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_st']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_st']['tmp_name'][$key];
            $file_name = $_FILES['lamp_st']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_st_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_st = implode(",", $lamp_st_paths);
    }

    $atm_bank = "";

    if(isset($_FILES["atm_bank"])) {
        $atm_bank_paths = array();

        // Loop through each file
        foreach($_FILES['atm_bank']['name'] as $key => $filename) {
            $file_tmp = $_FILES['atm_bank']['tmp_name'][$key];
            $file_name = $_FILES['atm_bank']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $atm_bank_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $atm_bank = implode(",", $atm_bank_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_fat SET lamp_qris = '$lamp_qris', lamp_st = '$lamp_st', atm_bank = '$atm_bank', email = '$email' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-fat.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
