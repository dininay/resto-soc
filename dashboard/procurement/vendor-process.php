<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$kode_lokasi = $_POST["city"];
$kode_vendor = $_POST["kode_vendor"];
$nama = $_POST["nama"];
$alamat = $_POST["alamat"];
$nohp = $_POST["nohp"];
$detail = $_POST["detail"];
$status_lokasi = "On Planning";
$lamp_vendor = "";

    if(isset($_FILES["lamp_vendor"])) {
        $lamp_vendor_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_vendor']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_vendor']['tmp_name'][$key];
            $file_name = $_FILES['lamp_vendor']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_vendor_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_vendor = implode(",", $lamp_vendor_paths);
    }

if(isset($_FILES["lamp_profil"])) {
    // Simpan lampiran ke folder tertentu
    $lamp_profil = array();
    $total_files = count($_FILES['lamp_profil']['name']);
    for($i = 0; $i < $total_files; $i++) {
        $file_tmp = $_FILES['lamp_profil']['tmp_name'][$i];
        $file_name = $_FILES['lamp_profil']['name'][$i];
        $file_path = "../uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $lamp_profil[] = $file_path;
    }
    $lamp_profil = implode(",", $lamp_profil);
} else {
    $lamp_profil = "";
}

// Menghasilkan singkatan kota
$singkatan_kota = strtoupper(substr($kode_lokasi, 0, 3));

// Ambil kode_vendor terakhir untuk kota tersebut
$sql_get_last_vendor = "SELECT kode_vendor FROM vendor WHERE kode_vendor LIKE ? ORDER BY kode_vendor DESC LIMIT 1";
$stmt_get_last_vendor = $conn->prepare($sql_get_last_vendor);
$search_param = $singkatan_kota . '%';
$stmt_get_last_vendor->bind_param("s", $search_param);
$stmt_get_last_vendor->execute();
$stmt_get_last_vendor->bind_result($last_kode_vendor);
$stmt_get_last_vendor->fetch();
$stmt_get_last_vendor->close();

if ($last_kode_vendor) {
    // Ekstrak nomor urut dan tambahkan 1
    $last_number = intval(substr($last_kode_vendor, -3));
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
} else {
    // Jika tidak ada kode_vendor sebelumnya, mulai dari 001
    $new_number = '001';
}

// Gabungkan singkatan kota dan nomor urut untuk membuat kode_vendor baru
$kode_vendor = $singkatan_kota . $new_number;

// Query untuk menyimpan data ke dalam tabel land
$sql = "INSERT INTO vendor (city, kode_vendor, nama, alamat, nohp, detail, lamp_vendor, lamp_profil, status_lokasi) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $kode_lokasi, $kode_vendor, $nama, $alamat, $nohp, $detail, $lamp_vendor, $lamp_profil, $status_lokasi);

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: /Resto/dashboard/datatables-vendor.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
