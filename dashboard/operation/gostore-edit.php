<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_lahan = $_POST["kode_lahan"];
    $id = $_POST['id'];
    $gostore_date = $_POST['gostore_date'];
    $nama_lokasi = $_POST['nama_lahan'];
    // Update data di database
    $sql = "UPDATE resto SET gostore_date='$gostore_date', approved_by = 'Last Updated by PMO' WHERE kode_lahan = '$kode_lahan'";
    $sql_land = "UPDATE land SET nama_lahan='$nama_lokasi' WHERE kode_lahan = '$kode_lahan'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-gostore.php");
        if ($conn->query($sql_land) === TRUE) {
        } else {
            echo "Error: " . $sql_land . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
