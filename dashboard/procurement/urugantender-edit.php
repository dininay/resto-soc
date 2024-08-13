<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $nama_vendorurugan = $_POST['nama_vendorurugan'];
    $alamat_vendorurugan = $_POST["alamat_vendorurugan"];
    $detail_vendorurugan = $_POST["detail_vendorurugan"];
    // $lamp_vendorurugan = $_POST['lamp_vendorurugan'];
    $id = $_POST['id'];

    // Inisialisasi variabel untuk lampiran
        // Periksa apakah kunci 'lampiran' ada dalam $_FILES
        $lamp_profilurugan = "";

        if(isset($_FILES["lamp_profilurugan"])) {
            $lamp_profilurugan_paths = array();
    
            // Loop through each file
            foreach($_FILES['lamp_profilurugan']['name'] as $key => $filename) {
                $file_tmp = $_FILES['lamp_profilurugan']['tmp_name'][$key];
                $file_name = $_FILES['lamp_profilurugan']['name'][$key];
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($file_name);
    
                // Attempt to move the uploaded file to the target directory
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $lamp_profilurugan_paths[] = $file_name;
                } else {
                    echo "Gagal mengunggah file " . $file_name . "<br>";
                }
            }
    
            // Join all file paths into a comma-separated string
            $lamp_profilurugan = implode(",", $lamp_profilurugan_paths);
        }

            // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_vendorurugan = "";

    if(isset($_FILES["lamp_vendorurugan"])) {
        $lamp_vendorurugan_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_vendorurugan']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_vendorurugan']['tmp_name'][$key];
            $file_name = $_FILES['lamp_vendorurugan']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_vendorurugan_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_vendorurugan = implode(",", $lamp_vendorurugan_paths);
    }

    // Menggabungkan file-file baru dengan file-file sebelumnya, jika ada
    // Proses file yang diunggah untuk lampiran profil
    // if (isset($_POST['ganti_lampirancp']) && $_POST['ganti_lampirancp'] === 'ya') {
    //     $new_files_profil = array();

    //     // Simpan file-file baru yang diunggah
    //     if (isset($_FILES['lamp_profilurugan']) && $_FILES['lamp_profilurugan']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //         foreach ($_FILES['lamp_profilurugan']['name'] as $key => $filename) {
    //             $target_dir = "uploads/";
    //             $target_file = $target_dir . basename($filename);

    //             // Buat direktori jika belum ada
    //             if (!is_dir($target_dir)) {
    //                 mkdir($target_dir, 0777, true);
    //             }

    //             if (move_uploaded_file($_FILES['lamp_profilurugan']['tmp_name'][$key], $target_file)) {
    //                 $new_files_profil[] = $filename;
    //             } else {
    //                 echo "Failed to upload file: " . $_FILES['lamp_profilurugan']['name'][$key] . "<br>";
    //             }
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya untuk lampiran profil
    //     $existing_files_profil = isset($_POST['existing_files_profil']) ? explode(", ", $_POST['existing_files_profil']) : array();
    //     $lamp_profilurugan = implode(", ", array_merge($existing_files_profil, $new_files_profil));
    // } else {
    //     // Jika tidak ada perubahan pada lampiran profil, gunakan yang sudah ada
    //     $lamp_profilurugan = isset($_POST['existing_files_profil']) ? $_POST['existing_files_profil'] : "";
    // }

    // Menggabungkan file-file baru dengan file-file sebelumnya, jika ada
    // Proses file yang diunggah untuk lampiran vendor
    // if (isset($_POST['ganti_lampiran']) && $_POST['ganti_lampiran'] === 'ya') {
    //     $new_files_vendor = array();

    //     // Simpan file-file baru yang diunggah
    //     if (isset($_FILES['lamp_vendorurugan']) && $_FILES['lamp_vendorurugan']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //         foreach ($_FILES['lamp_vendorurugan']['name'] as $key => $filename) {
    //             $target_dir = "uploads/";
    //             $target_file = $target_dir . basename($filename);

    //             // Buat direktori jika belum ada
    //             if (!is_dir($target_dir)) {
    //                 mkdir($target_dir, 0777, true);
    //             }

    //             if (move_uploaded_file($_FILES['lamp_vendorurugan']['tmp_name'][$key], $target_file)) {
    //                 $new_files_vendor[] = $filename;
    //             } else {
    //                 echo "Failed to upload file: " . $_FILES['lamp_vendorurugan']['name'][$key] . "<br>";
    //             }
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya untuk lampiran vendor
    //     $existing_files_vendor = isset($_POST['existing_files_vendor']) ? explode(", ", $_POST['existing_files_vendor']) : array();
    //     $lamp_vendorurugan = implode(", ", array_merge($existing_files_vendor, $new_files_vendor));
    // } else {
    //     // Jika tidak ada perubahan pada lampiran vendor, gunakan yang sudah ada
    //     $lamp_vendorurugan = isset($_POST['existing_files_vendor']) ? $_POST['existing_files_vendor'] : "";
    // }


    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
// if(isset($_FILES["lamp_splegal"])) {
//     // Simpan lampiran ke folder tertentu
//     $lamp_splegal = array();
//     $total_files = count($_FILES['lamp_splegal']['name']);
//     for($i = 0; $i < $total_files; $i++) {
//         $file_tmp = $_FILES['lamp_splegal']['tmp_name'][$i];
//         $file_name = $_FILES['lamp_splegal']['name'][$i];
//         $file_path = "../uploads/" . $file_name;
//         move_uploaded_file($file_tmp, $file_path);
//         $lamp_splegal[] = $file_path;
//     }
//     $lamp_splegal = implode(",", $lamp_splegal);
// } else {
//     $lamp_splegal = "";
// }

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
    //     $lamp_land = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_land = $_POST['existing_files'];
    // }

    // Update data di database
    $sql = "UPDATE procurement SET nama_vendorurugan = '$nama_vendorurugan', alamat_vendorurugan = '$alamat_vendorurugan', detail_vendorurugan = '$detail_vendorurugan', lamp_profilurugan = '$lamp_profilurugan', lamp_vendorurugan = '$lamp_vendorurugan' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-tender-urugan.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
