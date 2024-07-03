<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
        $id = $_POST['id'];
        $sla_kom = $_POST['sla_kom'];
        $lamp_spk = "";
    // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // Proses file yang diunggah
    if (isset($_FILES['lamp_spk']) && $_FILES['lamp_spk']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
        $new_files = array();

        // Simpan file-file baru yang diunggah
        foreach ($_FILES['lamp_spk']['name'] as $key => $filename) {
            $target_dir = "uploads/";
            $target_file = basename($filename);

            // Buat direktori jika belum ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['lamp_spk']['tmp_name'][$key], $target_file)) {
                $new_files[] = $target_file;
            } else {
                echo "Failed to upload file: " . $_FILES['lamp_spk']['name'][$key] . "<br>";
            }
        }

        // Gabungkan file-file baru dengan file-file sebelumnya
        $lamp_spk = implode(", ", array_merge($existing_files, $new_files));
    } else {
        // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
        $lamp_spk = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }
    // if(isset($_FILES["lamp_vendor"])) {
    //     // Simpan lampiran ke folder tertentu
    //     $lamp_vendor = array();
    //     $total_files = count($_FILES['lamp_vendor']['name']);
    //     for($i = 0; $i < $total_files; $i++) {
    //         $file_tmp = $_FILES['lamp_vendor']['tmp_name'][$i];
    //         $file_name = $_FILES['lamp_vendor']['name'][$i];
    //         $file_path = "../uploads/" . $file_name;
    //         move_uploaded_file($file_tmp, $file_path);
    //         $lamp_land[] = $file_path;
    //     }
    //     $lamp_vendor = implode(",", $lamp_vendor);
    // } else {
    //     $lamp_vendor = "";
    // }
    // Update data di database
    $sql = "UPDATE resto SET lamp_spk = '$lamp_spk', sla_kom = '$sla_kom' WHERE id = '$id'";

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
