<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kode_utensil = $_POST['kode_utensil'];
    $qty_target = $_POST['qty_target'];
    $qty_arrival = $_POST['qty_arrival'];
    // Update data di database
    $sql = "UPDATE utensil SET qty_target = '$qty_target', qty_arrival = '$qty_arrival', kode_utensil = '$kode_utensil' WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        $sql_get_kode = "SELECT kode_lahan FROM utensil WHERE id = ?";
        $stmt_get_kode = $conn->prepare($sql_get_kode);
        $stmt_get_kode->bind_param("i", $id);
        $stmt_get_kode->execute();
        $stmt_get_kode->bind_result($kode_lahan);
        $stmt_get_kode->fetch();
        $stmt_get_kode->close();

        if (isset($kode_lahan) && !empty($kode_lahan)) {
            header("Location: " . $base_url . "/datatables-data-scmutensil.php?id=" . urlencode($kode_lahan));
            exit;
        } else {
            echo "Error: Kode lahan tidak ditemukan.";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    
}

// Menutup koneksi database
$conn->close();
?>
