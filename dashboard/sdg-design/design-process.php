<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$lamp_land = $_POST["lamp_land"];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
if(isset($_FILES["lamp_desainplan"])) {
    // Simpan lampiran ke folder tertentu
    $lamp_desainplan = array();
    $total_files = count($_FILES['lamp_desainplan']['name']);
    for($i = 0; $i < $total_files; $i++) {
        $file_tmp = $_FILES['lamp_desainplan']['tmp_name'][$i];
        $file_name = $_FILES['lamp_desainplan']['name'][$i];
        $file_path = "../uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $lamp_desainplan[] = $file_path;
    }
    $lamp_desainplan = implode(",", $lamp_desainplan);
} else {
    $lamp_desainplan = "";
}
$catatan_sdgdesain = $_POST["catatan_sdgdesain"];

$submit_legal = "In Process"; 

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO sdg_desain (kode_lahan, lamp_land, lamp_desainplan, catatan_sdgdesain, submit_legal) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $kode_lahan, $lamp_land, $lamp_desainplan, $catatan_sdgdesain, $submit_legal);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-design.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
