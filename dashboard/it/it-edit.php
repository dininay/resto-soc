<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
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
                $lamp_printer_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_printer = implode(",", $lamp_printer_paths);
    }
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    if(isset($_FILES["lamp_cctv"])) {
        // Simpan lampiran ke folder tertentu
        $lamp_cctv = array();
        $total_files = count($_FILES['lamp_cctv']['name']);
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES['lamp_cctv']['tmp_name'][$i];
            $file_name = $_FILES['lamp_cctv']['name'][$i];
            $file_path = "../uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_path);
            $lamp_cctv[] = $file_path;
        }
        $lamp_cctv = implode(",", $lamp_cctv);
    } else {
        $lamp_cctv = "";
    }
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    if(isset($_FILES["lamp_sound"])) {
        // Simpan lampiran ke folder tertentu
        $lamp_sound = array();
        $total_files = count($_FILES['lamp_sound']['name']);
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES['lamp_sound']['tmp_name'][$i];
            $file_name = $_FILES['lamp_sound']['name'][$i];
            $file_path = "../uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_path);
            $lamp_sound[] = $file_path;
        }
        $lamp_sound = implode(",", $lamp_sound);
    } else {
        $lamp_sound = "";
    }
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    if(isset($_FILES["lamp_internet"])) {
        // Simpan lampiran ke folder tertentu
        $lamp_internet = array();
        $total_files = count($_FILES['lamp_internet']['name']);
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES['lamp_internet']['tmp_name'][$i];
            $file_name = $_FILES['lamp_internet']['name'][$i];
            $file_path = "../uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_path);
            $lamp_internet[] = $file_path;
        }
        $lamp_internet = implode(",", $lamp_internet);
    } else {
        $lamp_internet = "";
    }

    // Update data di database
    $sql = "UPDATE socdate_it SET lamp_printer = '$lamp_printer', lamp_cctv = '$lamp_cctv', lamp_sound = '$lamp_sound', lamp_internet = '$lamp_internet' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-it.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
