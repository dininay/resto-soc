<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $no_listrik = $_POST['no_listrik']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_listrik = "";

    if(isset($_FILES["lamp_listrik"])) {
        $lamp_listrik_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_listrik']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_listrik']['tmp_name'][$key];
            $file_name = $_FILES['lamp_listrik']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_listrik_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_listrik = implode(",", $lamp_listrik_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_ipal = "";

    if(isset($_FILES["lamp_ipal"])) {
        $lamp_ipal_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_ipal']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ipal']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ipal']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ipal_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_ipal = implode(",", $lamp_ipal_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_ka = "";

    if(isset($_FILES["lamp_ka"])) {
        $lamp_ka_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_ka']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ka']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ka']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ka_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_ka = implode(",", $lamp_ka_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET no_listrik = '$no_listrik', lamp_listrik = '$lamp_listrik', lamp_ipal = '$lamp_ipal', lamp_ka = '$lamp_ka' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-sdgpk-rto.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
