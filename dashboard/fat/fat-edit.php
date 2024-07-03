<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
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
                $lamp_qris_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_qris = implode(",", $lamp_qris_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    if(isset($_FILES["lamp_st"])) {
        // Simpan lampiran ke folder tertentu
        $lamp_st = array();
        $total_files = count($_FILES['lamp_st']['name']);
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES['lamp_st']['tmp_name'][$i];
            $file_name = $_FILES['lamp_st']['name'][$i];
            $file_path = "../uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_path);
            $lamp_st[] = $file_path;
        }
        $lamp_st = implode(",", $lamp_st);
    } else {
        $lamp_st = "";
    }

    // Update data di database
    $sql = "UPDATE socdate_fat SET lamp_qris = '$lamp_qris', lamp_st = '$lamp_st' WHERE id = '$id'";
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
