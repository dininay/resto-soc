<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    VAR_DUMP($id);

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_pbg = "";

    if(isset($_FILES["lamp_pbg"])) {
        $lamp_pbg_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_pbg']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_pbg']['tmp_name'][$key];
            $file_name = $_FILES['lamp_pbg']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_pbg_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_pbg = implode(",", $lamp_pbg_paths);
    }
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
$lamp_permit = "";

    if(isset($_FILES["lamp_permit"])) {
        $lamp_permit_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_permit']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_permit']['tmp_name'][$key];
            $file_name = $_FILES['lamp_permit']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_permit_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_permit = implode(",", $lamp_permit_paths);
    }

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if(isset($_FILES['lamp_loacd']) && $_FILES['lamp_loacd']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = explode(", ", $_POST['existing_files']); // Ambil nama file sebelumnya
    //     $new_files = array();
        
    //     // Simpan file-file baru yang diunggah
    //     foreach($_FILES['lamp_loacd']['name'] as $key => $filename) {
    //         $target_dir = "uploads/";
    //         $target_file = $target_dir . basename($_FILES['lamp_loacd']['name'][$key]);
    //         if (move_uploaded_file($_FILES['lamp_loacd']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $target_file;
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_pbg = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_pbg = $_POST['existing_files'];
    // }

    // Update data di database
    $sql = "UPDATE sdg_desain SET lamp_pbg = '$lamp_pbg', lamp_permit = '$lamp_permit' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-sp-submit-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
