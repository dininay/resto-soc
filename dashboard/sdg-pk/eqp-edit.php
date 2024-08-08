<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $submit_gis = $_POST['submit_gis'];
    $lamp_steqp = "";

    if(isset($_FILES["lamp_steqp"])) {
        $lamp_steqp_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_steqp']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_steqp']['tmp_name'][$key];
            $file_name = $_FILES['lamp_steqp']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_steqp_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_steqp = implode(",", $lamp_steqp_paths);
    }
    $lamp_basteqp = "";

    if(isset($_FILES["lamp_basteqp"])) {
        $lamp_basteqp_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_basteqp']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_basteqp']['tmp_name'][$key];
            $file_name = $_FILES['lamp_basteqp']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_basteqp_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_basteqp = implode(",", $lamp_basteqp_paths);
    }
    // $lamp_steqp = "";
    // $lamp_basteqp = "";

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_steqp']) && $_FILES['lamp_steqp']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_steqp']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_steqp']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_steqp']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_steqp = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_steqp = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_basteqp']) && $_FILES['lamp_basteqp']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_basteqp']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_basteqp']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_basteqp']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_basteqp = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_basteqp = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE equipment SET lamp_steqp = ?, lamp_basteqp = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssi", $lamp_steqp, $lamp_basteqp, $id);

    $sql2 = "UPDATE resto SET submit_gis = ? WHERE kode_lahan = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ss", $submit_gis, $kode_lahan);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-st-eqp.php");
        exit();
    } else {
        echo "Error: " . $stmt1->error;
    }

    if ($stmt2->execute()) {
        header("Location: " . $base_url . "/datatables-st-eqp.php");
        exit();
    } else {
        echo "Error: " . $stmt2->error;
    }

    // Close statements
    $stmt1->close();
    $stmt2->close();
}

// Menutup koneksi database
$conn->close();
?>