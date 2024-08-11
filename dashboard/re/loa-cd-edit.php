<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $catatan = $_POST['catatan'];
    $id = $_POST['id'];

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_loacd = "";

    if(isset($_FILES["lamp_loacd"])) {
        $lamp_loacd_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_loacd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_loacd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_loacd']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_loacd_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_loacd = implode(",", $lamp_loacd_paths);
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
    //     $lamp_loacd = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_loacd = $_POST['existing_files'];
    // }

    // Update data di database
    $sql = "UPDATE dokumen_loacd SET  lamp_loacd = '$lamp_loacd', catatan = '$catatan' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-loa-cd.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
