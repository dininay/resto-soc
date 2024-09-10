<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_cctv = "";

    if(isset($_FILES["lamp_cctv"])) {
        $lamp_cctv_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_cctv']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_cctv']['tmp_name'][$key];
            $file_name = $_FILES['lamp_cctv']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_cctv_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_cctv = implode(",", $lamp_cctv_paths);
    }
    
    // // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    // $lamp_sound = "";

    // if(isset($_FILES["lamp_sound"])) {
    //     $lamp_sound_paths = array();

    //     // Loop through each file
    //     foreach($_FILES['lamp_sound']['name'] as $key => $filename) {
    //         $file_tmp = $_FILES['lamp_sound']['tmp_name'][$key];
    //         $file_name = $_FILES['lamp_sound']['name'][$key];
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($file_name);

    //         // Attempt to move the uploaded file to the target directory
    //         if (move_uploaded_file($file_tmp, $target_file)) {
    //             $lamp_sound_paths[] = $file_name;
    //         } else {
    //             echo "Gagal mengunggah file " . $file_name . "<br>";
    //         }
    //     }

    //     // Join all file paths into a comma-separated string
    //     $lamp_sound = implode(",", $lamp_sound_paths);
    // }
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_internet = "";

    if(isset($_FILES["lamp_internet"])) {
        $lamp_internet_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_internet']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_internet']['tmp_name'][$key];
            $file_name = $_FILES['lamp_internet']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_internet_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_internet = implode(",", $lamp_internet_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_it SET lamp_cctv = '$lamp_cctv', lamp_internet = '$lamp_internet' WHERE id = '$id'";
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
