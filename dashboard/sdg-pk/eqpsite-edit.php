<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $defect_eqpsite = $_POST['defect_eqpsite'];
    $progress_eqpsite = $_POST['progress_eqpsite'];
    $notedefect_eqpsite = isset($_POST["notedefect_eqpsite"]) ? $_POST["notedefect_eqpsite"] : null;
    $lamp_eqpsite = "";

    if(isset($_FILES["lamp_eqpsite"])) {
        $lamp_eqpsite_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_eqpsite']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_eqpsite']['tmp_name'][$key];
            $file_name = $_FILES['lamp_eqpsite']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_eqpsite_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_eqpsite = implode(",", $lamp_eqpsite_paths);
    }
    // $lamp_eqpsite = "";

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_eqpsite']) && $_FILES['lamp_eqpsite']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_eqpsite']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_eqpsite']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_eqpsite']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_eqpsite = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_eqpsite = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE equipment SET lamp_eqpsite = ?, progress_eqpsite = ?, defect_eqpsite = ?, notedefect_eqpsite = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssssi", $lamp_eqpsite, $progress_eqpsite, $defect_eqpsite, $notedefect_eqpsite, $id);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-eqp-site.php");
        exit();
    } else {
        echo "Error: " . $stmt1->error;
    }

    // Close statements
    $stmt1->close();
    $stmt2->close();
}

// Menutup koneksi database
$conn->close();
?>