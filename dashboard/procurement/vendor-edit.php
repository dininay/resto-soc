<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_vendor = $_POST["kode_vendor"];
        $nama = $_POST["nama"];
        $alamat = $_POST["alamat"];
        $nohp = $_POST["nohp"];
        $detail = $_POST["detail"];
        $id = $_POST['id'];
        
        // Inisialisasi variabel untuk lampiran
    $lamp_profil = "";
    $lamp_vendor = "";

    // Menggabungkan file-file baru dengan file-file sebelumnya, jika ada
    // Proses file yang diunggah untuk lampiran profil
    if (isset($_POST['ganti_lampirancp']) && $_POST['ganti_lampirancp'] === 'ya') {
        $new_files_profil = array();

        // Simpan file-file baru yang diunggah
        if (isset($_FILES['lamp_profil']) && $_FILES['lamp_profil']['error'][0] != UPLOAD_ERR_NO_FILE) {
            foreach ($_FILES['lamp_profil']['name'] as $key => $filename) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($filename);

                // Buat direktori jika belum ada
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['lamp_profil']['tmp_name'][$key], $target_file)) {
                    $new_files_profil[] = $filename;
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_profil']['name'][$key] . "<br>";
                }
            }
        }

        // Gabungkan file-file baru dengan file-file sebelumnya untuk lampiran profil
        $existing_files_profil = isset($_POST['existing_files_profil']) ? explode(", ", $_POST['existing_files_profil']) : array();
        $lamp_profil = implode(", ", array_merge($existing_files_profil, $new_files_profil));
    } else {
        // Jika tidak ada perubahan pada lampiran profil, gunakan yang sudah ada
        $lamp_profil = isset($_POST['existing_files_profil']) ? $_POST['existing_files_profil'] : "";
    }

    // Menggabungkan file-file baru dengan file-file sebelumnya, jika ada
    // Proses file yang diunggah untuk lampiran vendor
    if (isset($_POST['ganti_lampiran']) && $_POST['ganti_lampiran'] === 'ya') {
        $new_files_vendor = array();

        // Simpan file-file baru yang diunggah
        if (isset($_FILES['lamp_vendor']) && $_FILES['lamp_vendor']['error'][0] != UPLOAD_ERR_NO_FILE) {
            foreach ($_FILES['lamp_vendor']['name'] as $key => $filename) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($filename);

                // Buat direktori jika belum ada
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['lamp_vendor']['tmp_name'][$key], $target_file)) {
                    $new_files_vendor[] = $filename;
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_vendor']['name'][$key] . "<br>";
                }
            }
        }

        // Gabungkan file-file baru dengan file-file sebelumnya untuk lampiran vendor
        $existing_files_vendor = isset($_POST['existing_files_vendor']) ? explode(", ", $_POST['existing_files_vendor']) : array();
        $lamp_vendor = implode(", ", array_merge($existing_files_vendor, $new_files_vendor));
    } else {
        // Jika tidak ada perubahan pada lampiran vendor, gunakan yang sudah ada
        $lamp_vendor = isset($_POST['existing_files_vendor']) ? $_POST['existing_files_vendor'] : "";
    }

    // Update data di database
    $sql = "UPDATE vendor SET kode_vendor = ?, nama = ?, alamat = ?, nohp = ?, detail = ?, lamp_profil = ?, lamp_vendor = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $kode_vendor, $nama, $alamat, $nohp, $detail, $lamp_profil, $lamp_vendor, $id);

    if ($stmt->execute()) {
        header("Location: " . $base_url . "/datatables-vendor.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>