<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    
    $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_rab = "";
    if (isset($_FILES["lamp_rab"])) {
        $lamp_rab_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_rab']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_rab']['tmp_name'][$key];
            $file_name = $_FILES['lamp_rab']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_rab_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_rab = implode(",", $lamp_rab_paths);
    }

    // Update data di database
    $sql = "UPDATE sdg_rab SET keterangan = '$keterangan', jumlah = '$jumlah', lamp_rab = '$lamp_rab' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-rab.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
