<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $nominal_spkeqp = $_POST['nominal_spkeqp'];
    $kode_lahan = $_POST['kode_lahan'];
    
    $lamp_spkeqpdev = "";

    if (isset($_FILES['lamp_spkeqpdev']) && $_FILES['lamp_spkeqpdev']['error'][0] != UPLOAD_ERR_NO_FILE) {
        // Jika ada file yang diunggah
        $existing_files = isset($_POST['existing_files']) ? explode(",", $_POST['existing_files']) : array();
        $new_files = array();

        // Path ke direktori "uploads" dengan kode_lahan
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_spkeqpdev']['name'] as $key => $filename) {
            if ($filename) {
                $target_file = $target_dir . basename($filename); // Simpan di folder kode_lahan

                if (move_uploaded_file($_FILES['lamp_spkeqpdev']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename); // Simpan nama file yang berhasil diunggah
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_spkeqpdev']['name'][$key] . "<br>";
                }
            }
        }

        // Gabungkan file yang sudah ada dan file yang baru diunggah
        $all_files = array_merge($existing_files, $new_files);
        $lamp_spkeqpdev = implode(",", array_filter($all_files)); // Gabungkan nama file menjadi string
    } else {
        // Jika tidak ada file baru, gunakan file yang sudah ada
        $lamp_spkeqpdev = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }
    // Update data di database untuk tabel resto
    $sql1 = "UPDATE equipment SET lamp_spkeqpdev = ?, nominal_spkeqp = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssi", $lamp_spkeqpdev, $nominal_spkeqp, $id);

    // Execute both queries
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-eqpdev-procur.php");
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