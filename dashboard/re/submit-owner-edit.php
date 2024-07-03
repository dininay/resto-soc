<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Periksa apakah semua data ada
    if (isset($_POST['id']) && isset($_POST['status_approvre']) && isset($_POST['kode_lahan']) && isset($_POST['nama_lahan']) && isset($_POST['lokasi']) && isset($_POST['luas_area']) && isset($_POST['lamp_land'])) {
        // Ambil nilai dari formulir
        $id = $_POST['id'];
        $status_approvre = $_POST['status_approvre'];
        $kode_lahan = $_POST['kode_lahan'];
        $nama_lahan = $_POST['nama_lahan'];
        $lokasi = $_POST['lokasi'];
        $luas_area = $_POST['luas_area'];
        $lamp_land = $_POST['lamp_land'];
        $status_approvowner = "In Process";

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Update data di tabel land
            $sql_update = "UPDATE land SET status_approvre = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $status_approvre, $id);
            $stmt_update->execute();

            // Insert data ke tabel re
            $sql_insert = "INSERT INTO re (kode_lahan, nama_lahan, lokasi, luas_area, lamp_land, status_approvowner) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssss", $kode_lahan, $nama_lahan, $lokasi, $luas_area, $lamp_land, $status_approvowner);
            $stmt_insert->execute();

            // Komit transaksi
            $conn->commit();

            // Redirect setelah berhasil
            header("Location:" . $base_url . "/datatables-submit-to-owner.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Data tidak lengkap.";
    }
}

// Menutup koneksi database
$conn->close();
?>