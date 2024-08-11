<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_lahan = $_POST["kode_lahan"];
    $id = $_POST['id'];
    $nama_lokasi = $_POST['nama_lahan'];
    // Update data di tabel land
    $sql_land = "UPDATE land SET nama_lahan='$nama_lokasi' WHERE kode_lahan = '$kode_lahan'";
    // Update data di tabel draft
    $sql_draft = "UPDATE draft SET nama_lahan='$nama_lokasi' WHERE kode_lahan = '$kode_lahan'";

    // Eksekusi query untuk tabel land
    if ($conn->query($sql_land) === TRUE) {
        // Eksekusi query untuk tabel draft
        if ($conn->query($sql_draft) === TRUE) {
            header("Location: " . $base_url . "/datatables-resto-name.php");
            exit();
        } else {
            echo "Error: " . $sql_draft . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_land . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>