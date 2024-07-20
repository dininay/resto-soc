<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $start_konstruksi = $_POST['start_konstruksi']; // Assuming this value is posted from form
    $status_consact = "In Process";
    $lamp_kom = "";

    // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    if (isset($_FILES['lamp_kom']) && $_FILES['lamp_kom']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
        $new_files = array();

        // Simpan file-file baru yang diunggah
        foreach ($_FILES['lamp_kom']['name'] as $key => $filename) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Buat direktori jika belum ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['lamp_kom']['tmp_name'][$key], $target_file)) {
                $new_files[] = $filename;
            } else {
                echo "Failed to upload file: " . $_FILES['lamp_kom']['name'][$key] . "<br>";
            }
        }

        // Gabungkan file-file baru dengan file-file sebelumnya
        $lamp_kom = implode(", ", array_merge($existing_files, $new_files));
    } else {
        // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
        $lamp_kom = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE resto SET lamp_kom = ?, start_konstruksi = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssi", $lamp_kom, $start_konstruksi, $id);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location:  " . $base_url . "/datatables-kom-sdgpk.php");
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