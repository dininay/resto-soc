<?php
// Koneksi ke database
include "../../koneksi.php";

// Periksa apakah ada data yang akan dihapus yang dikirim melalui permintaan POST
if (isset($_POST['id'])) {
    // Ambil ID data yang akan dihapus dari permintaan POST
    $id = $_POST['id'];

    // Persiapkan statement SQL untuk menghapus data berdasarkan ID
    $sql = "DELETE FROM dokumen_loacd WHERE id = $id";

    // Jalankan statement SQL untuk menghapus data
    if ($conn->query($sql) === TRUE) {
        // Kirim tanggapan jika penghapusan berhasil
                header("Location: /Resto/dashboard/datatables-loa-cd.php");
                exit();
    } else {
        // Kirim tanggapan jika terjadi kesalahan saat penghapusan
        echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
    }
} else {
    // Kirim pesan jika tidak ada ID data yang dikirim melalui permintaan POST
    echo json_encode(array("message" => "ID tidak valid."));
}

// Tutup koneksi database
$conn->close();
?>
