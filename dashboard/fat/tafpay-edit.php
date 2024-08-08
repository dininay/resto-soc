<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_paylistrik = "";

    if(isset($_FILES["lamp_paylistrik"])) {
        $lamp_paylistrik_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_paylistrik']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_paylistrik']['tmp_name'][$key];
            $file_name = $_FILES['lamp_paylistrik']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_paylistrik_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_paylistrik = implode(",", $lamp_paylistrik_paths);
    }
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_paypdam = "";

    if(isset($_FILES["lamp_paypdam"])) {
        $lamp_paypdam_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_paypdam']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_paypdam']['tmp_name'][$key];
            $file_name = $_FILES['lamp_paypdam']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_paypdam_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_paypdam = implode(",", $lamp_paypdam_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET lamp_paylistrik = '$lamp_paylistrik', lamp_paypdam = '$lamp_paypdam' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-tafpay-listrikair.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
