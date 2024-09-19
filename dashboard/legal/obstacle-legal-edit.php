<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $catatan_obslegal = $_POST['catatan_obslegal'];
    
    $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    $lamp_legal = "";

    if (isset($_FILES['lamp_legal']) && $_FILES['lamp_legal']['error'][0] != UPLOAD_ERR_NO_FILE) {
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
        foreach ($_FILES['lamp_legal']['name'] as $key => $filename) {
            if ($filename) {
                $target_file = $target_dir . basename($filename); // Simpan di folder kode_lahan

                if (move_uploaded_file($_FILES['lamp_legal']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename); // Simpan nama file yang berhasil diunggah
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_legal']['name'][$key] . "<br>";
                }
            }
        }

        // Gabungkan file yang sudah ada dan file yang baru diunggah
        $all_files = array_merge($existing_files, $new_files);
        $lamp_legal = implode(",", array_filter($all_files)); // Gabungkan nama file menjadi string
    } else {
        // Jika tidak ada file baru, gunakan file yang sudah ada
        $lamp_legal = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }

    // Update data di database
    $sql = "UPDATE sdg_desain SET catatan_obslegal = '$catatan_obslegal', lamp_legal = '$lamp_legal' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-design-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
