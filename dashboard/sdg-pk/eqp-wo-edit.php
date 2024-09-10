<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    
    $lamp_woeqp = "";

    if (isset($_FILES['lamp_woeqp']) && $_FILES['lamp_woeqp']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $existing_files = isset($_POST['existing_files']) ? explode(",", $_POST['existing_files']) : array();
        $new_files = array();

        foreach ($_FILES['lamp_woeqp']['name'] as $key => $filename) {
            if ($filename) {
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($filename);

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['lamp_woeqp']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename);
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_woeqp']['name'][$key] . "<br>";
                }
            }
        }

        $all_files = array_merge($existing_files, $new_files);
        $lamp_woeqp = implode(",", array_filter($all_files));
    } else {
        $lamp_woeqp = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }
    // $lamp_woeqp = "";

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_woeqp']) && $_FILES['lamp_woeqp']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_woeqp']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_woeqp']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_woeqp']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_woeqp = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_woeqp = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE equipment SET lamp_woeqp = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("si", $lamp_woeqp, $id);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-wo-eqp.php");
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