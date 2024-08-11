<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$nama_lahan = $_POST["nama_lahan"];
$lokasi = $_POST["lokasi"];
$luas_area = $_POST["luas_area"];
$lamp_land = $_POST["lamp_land"];
// // Periksa apakah kunci 'lampiran' ada dalam $_FILES
// if(isset($_FILES["lamp_land"])) {
//     // Simpan lampiran ke folder tertentu
//     $lamp_land = array();
//     $total_files = count($_FILES['lamp_land']['name']);
//     for($i = 0; $i < $total_files; $i++) {
//         $file_tmp = $_FILES['lamp_land']['tmp_name'][$i];
//         $file_name = $_FILES['lamp_land']['name'][$i];
//         $file_path = "../uploads/" . $file_name;
//         move_uploaded_file($file_tmp, $file_path);
//         $lamp_land[] = $file_path;
//     }
//     $lamp_land = implode(",", $lamp_land);
// } else {
//     $lamp_land = "";
// }
$catatan_owner = $_POST["catatan_owner"];
$status_approvowner = "In Process";

$sla_date = date('Y-m-d', strtotime("+5 days"));
$slalegal_date = date('Y-m-d', strtotime($sla_date . ' +4 days'));
$slanego_date = date('Y-m-d', strtotime($sla_date . ' +3 days'));

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO re (kode_lahan, nama_lahan, lokasi, luas_area, lamp_land, catatan_owner, status_approvowner, sla_date, slalegal_date, slanego_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $kode_lahan, $nama_lahan, $lokasi, $luas_area, $lamp_land, $catatan_owner, $status_approvowner, $sla_date, $slalegal_date, $slanego_date);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-approval-owner.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
