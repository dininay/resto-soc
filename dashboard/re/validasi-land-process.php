<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$status_approvlegal = "In Process";
$status_approvnego = "In Process";
$kode_lahan = $_POST['kode_lahan'];

// Query untuk mendapatkan start_date dari tabel re
$get_start_date_sql = "SELECT start_date FROM re WHERE kode_lahan = '$kode_lahan'";
$result = $conn->query($get_start_date_sql);

if ($result->num_rows > 0) {
    // Ambil start_date dari hasil query
    $row = $result->fetch_assoc();
    $start_date = $row["start_date"];

    // Hitung slalegal_date (9 hari setelah start_date)
    $slalegal_date = date('Y-m-d', strtotime($start_date . ' +9 days'));

    // Query untuk melakukan pembaruan pada tabel re
    $update_sql = "UPDATE re SET status_approvlegal = '$status_approvlegal', status_approvnego = '$status_approvnego', slalegal_date = '$slalegal_date' WHERE kode_lahan = '$kode_lahan'";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect ke halaman datatable-validasi-lahan
        header("Location: " . $base_url . "/datatables-validasi-lahan.php");
        exit();
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
} else {
    echo "Tidak ada record dengan kode lahan tersebut.";
}
}
?>