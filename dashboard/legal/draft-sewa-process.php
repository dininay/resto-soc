<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lahan = $_POST["kode_lahan"];
$nama_lahan = $_POST["nama_lahan"];
$lokasi = $_POST["lokasi"];
$lamp_land = $_POST["lamp_land"];
$lamp_loacd = $_POST["lamp_loacd"];
$catatan = $_POST["catatan_legal"];
$jadwal_psm = $_POST["jadwal_psm"];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
if(isset($_FILES["lamp_draf"])) {
    // Simpan lampiran ke folder tertentu
    $lamp_draf = array();
    $total_files = count($_FILES['lamp_draf']['name']);
    for($i = 0; $i < $total_files; $i++) {
        $file_tmp = $_FILES['lamp_draf']['tmp_name'][$i];
        $file_name = $_FILES['lamp_draf']['name'][$i];
        $file_path = "../uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $lamp_draf[] = $file_path;
    }
    $lamp_draf = implode(",", $lamp_draf);
} else {
    $lamp_draf = "";
}

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO draft (kode_lahan, nama_lahan, lokasi, lamp_land, lamp_loacd, catatan_legal, jadwal_psm, lamp_draf) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $kode_lahan, $nama_lahan, $lokasi, $lamp_land, $lamp_loacd, $catatan, $jadwal_psm, $lamp_draf);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-draft-sewa-legal.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
