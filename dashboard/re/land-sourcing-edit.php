<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_lahan = $_POST["kode_lahan"];
        $nama_lahan = $_POST["nama_lahan"];
        $lokasi = $_POST["lokasi"];
        $nama_pemilik = $_POST["nama_pemilik"];
        $alamat_pemilik = $_POST["alamat_pemilik"];
        $no_tlp = $_POST["no_tlp"];
        $luas_area = $_POST["luas_area"];
    $id = $_POST['id'];

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if(isset($_FILES['lamp_land']) && $_FILES['lamp_land']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = explode(", ", $_POST['existing_files']); // Ambil nama file sebelumnya
    //     $new_files = array();
        
    //     // Simpan file-file baru yang diunggah
    //     foreach($_FILES['lamp_land']['name'] as $key => $filename) {
    //         $target_dir = "uploads/";
    //         $target_file = $target_dir . basename($_FILES['lamp_land']['name'][$key]);
    //         if (move_uploaded_file($_FILES['lamp_land']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $target_file;
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_land = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_land = $_POST['existing_files'];
    // }
    $lamp_land = "";

    if(isset($_FILES["lamp_land"])) {
        $lamp_land_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_land']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_land']['tmp_name'][$key];
            $file_name = $_FILES['lamp_land']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_land_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_land = implode(",", $lamp_land_paths);
    }
    // Update data di database
    $sql = "UPDATE land SET kode_lahan = '$kode_lahan', nama_lahan = '$nama_lahan', lokasi = '$lokasi', nama_pemilik = '$nama_pemilik',
     alamat_pemilik = '$alamat_pemilik', no_tlp = '$no_tlp', luas_area = '$luas_area', lamp_land = '$lamp_land' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-land-sourcing.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
