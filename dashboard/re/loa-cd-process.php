<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$status_approvowner = $_POST["status_approvowner"];
$status_approvlegal = $_POST["status_approvlegal"];
$status_approvnego = $_POST["status_approvnego"];
$lamp_land = $_POST["lamp_land"];
$tgl_berlaku = $_POST["tgl_berlaku"];
$penanggungjawab = $_POST["penanggungjawab"];
$catatan = $_POST["catatan"];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
if(isset($_FILES["lamp_loacd"])) {
    // Simpan lampiran ke folder tertentu
    $lamp_loacd = array();
    $total_files = count($_FILES['lamp_loacd']['name']);
    for($i = 0; $i < $total_files; $i++) {
        $file_tmp = $_FILES['lamp_loacd']['tmp_name'][$i];
        $file_name = $_FILES['lamp_loacd']['name'][$i];
        $file_path = "../uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $lamp_loacd[] = $file_path;
    }
    $lamp_loacd = implode(",", $lamp_loacd);
} else {
    $lamp_loacd = "";
}

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO dokumen_loacd (kode_lahan, status_approvowner, status_approvlegal, status_approvnego, lamp_land, tgl_berlaku, penanggungjawab, catatan, lamp_loacd) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $kode_lahan, $status_approvowner, $status_approvlegal, $status_approvnego, $lamp_land, $tgl_berlaku, $penanggungjawab, $catatan, $lamp_loacd );

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-loa-cd.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
