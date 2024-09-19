<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    
    $lamp_eqpdev = "";
    if (isset($_FILES["lamp_eqpdev"])) {
        $lamp_eqpdev_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_eqpdev']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_eqpdev']['tmp_name'][$key];
            $file_name = $_FILES['lamp_eqpdev']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_eqpdev_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_eqpdev = implode(",", $lamp_eqpdev_paths);
    }
    // $lamp_eqpdev = "";

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_eqpdev']) && $_FILES['lamp_eqpdev']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_eqpdev']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_eqpdev']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_eqpdev']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_eqpdev = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_eqpdev = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE equipment SET lamp_eqpdev = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("si", $lamp_eqpdev, $id);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-eqp-delivery.php");
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