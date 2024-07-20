<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form
$city = $_POST["city"];
$nama_lahan = $_POST["nama_lahan"];
$status_land = "On Planning";
$bp_date = date('Y-m-d');

// Ambil singkatan kota (Ct) dari tabel master_city
$sql = "SELECT Ct FROM master_city WHERE City = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $city);
$stmt->execute();
$stmt->bind_result($city_code);
$stmt->fetch();
$stmt->close();

if ($city_code) {
    // Temukan nilai terakhir dari kode_lahan untuk kota yang dipilih
    $sql = "SELECT kode_lahan FROM land WHERE kode_lahan LIKE ? ORDER BY kode_lahan DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $city_code_pattern = $city_code . '-%';
    $stmt->bind_param("s", $city_code_pattern);
    $stmt->execute();
    $stmt->bind_result($last_kode_lahan);
    $stmt->fetch();
    $stmt->close();

    // Hitung urutan berikutnya
    if ($last_kode_lahan) {
        $last_sequence = (int)substr($last_kode_lahan, strrpos($last_kode_lahan, '-') + 1);
        $new_sequence = $last_sequence + 1;
    } else {
        $new_sequence = 1;
    }

    // Bentuk kode_lahan yang baru
    $kode_lahan = $city_code . '-' . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);

    // Query untuk menyimpan data ke dalam tabel land
    $sql = "INSERT INTO land (city, kode_lahan, nama_lahan, status_land, bp_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $city, $kode_lahan, $nama_lahan, $status_land, $bp_date);

    if ($stmt->execute()) {
        // Redirect ke halaman datatable-land-sourcing
        header("Location: " . $base_url . "/datatables-bussiness-planning.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
} else {
    echo "Error: City not found.";
}
?>
