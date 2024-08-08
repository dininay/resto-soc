<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
        $id = $_POST['id'];
        // $sla_kom = $_POST['sla_kom'];
        $lamp_spk = "";

    if(isset($_FILES["lamp_spk"])) {
        $lamp_spk_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_spk']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_spk']['tmp_name'][$key];
            $file_name = $_FILES['lamp_spk']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_spk_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_spk = implode(",", $lamp_spk_paths);
    }
    // $lamp_spk = "";

    // if(isset($_FILES["lamp_spk"])) {
    //     $lamp_spk_paths = array();

    //     // Loop through each file
    //     foreach($_FILES['lamp_spk']['name'] as $key => $filename) {
    //         $file_tmp = $_FILES['lamp_spk']['tmp_name'][$key];
    //         $file_name = $_FILES['lamp_spk']['name'][$key];
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($file_name);

    //         // Attempt to move the uploaded file to the target directory
    //         if (move_uploaded_file($file_tmp, $target_file)) {
    //             $lamp_spk_paths[] = $file_name;
    //         } else {
    //             echo "Gagal mengunggah file " . $file_name . "<br>";
    //         }
    //     }

    //     // Join all file paths into a comma-separated string
    //     $lamp_spk = implode(",", $lamp_spk_paths);
    // }
    // if(isset($_FILES["lamp_vendor"])) {
    //     // Simpan lampiran ke folder tertentu
    //     $lamp_vendor = array();
    //     $total_files = count($_FILES['lamp_vendor']['name']);
    //     for($i = 0; $i < $total_files; $i++) {
    //         $file_tmp = $_FILES['lamp_vendor']['tmp_name'][$i];
    //         $file_name = $_FILES['lamp_vendor']['name'][$i];
    //         $file_path = "../uploads/" . $file_name;
    //         move_uploaded_file($file_tmp, $file_path);
    //         $lamp_spk[] = $file_path;
    //     }
    //     $lamp_vendor = implode(",", $lamp_vendor);
    // } else {
    //     $lamp_vendor = "";
    // }
    // Update data di database
    $sql = "UPDATE resto SET lamp_spk = '$lamp_spk' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location:" . $base_url . "/datatables-spk-sdgpk.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
