<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_rabcs = "";

    if(isset($_FILES["lamp_rabcs"])) {
        $lamp_rabcs_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_rabcs']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_rabcs']['tmp_name'][$key];
            $file_name = $_FILES['lamp_rabcs']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_rabcs_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_rabcs = implode(",", $lamp_rabcs_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    if(isset($_FILES["lamp_rabsecurity"])) {
        // Simpan lampiran ke folder tertentu
        $lamp_rabsecurity = array();
        $total_files = count($_FILES['lamp_rabsecurity']['name']);
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES['lamp_rabsecurity']['tmp_name'][$i];
            $file_name = $_FILES['lamp_rabsecurity']['name'][$i];
            $file_path = "../uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_path);
            $lamp_rabsecurity[] = $file_path;
        }
        $lamp_rabsecurity = implode(",", $lamp_rabsecurity);
    } else {
        $lamp_rabsecurity = "";
    }

    // Update data di database
    $sql = "UPDATE socdate_ir SET lamp_rabcs = '$lamp_rabcs', lamp_rabsecurity = '$lamp_rabsecurity' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-ir.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
