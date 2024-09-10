<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $eta_date = $_POST['eta_date'];
    $status_utensil = "In Process";
    $eta_by = "Last Updated by Procurement";
    // Update data di database
    $sql = "UPDATE utensil SET eta_date = '$eta_date', eta_by = '$eta_by', status_utensil = '$status_utensil' WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        $sql_get_kode = "SELECT kode_lahan FROM utensil WHERE id = ?";
        $stmt_get_kode = $conn->prepare($sql_get_kode);
        $stmt_get_kode->bind_param("i", $id);
        $stmt_get_kode->execute();
        $stmt_get_kode->bind_result($kode_lahan);
        $stmt_get_kode->fetch();
        $stmt_get_kode->close();

        if (isset($kode_lahan) && !empty($kode_lahan)) {
            header("Location: " . $base_url . "/datatables-data-procurutensil.php?id=" . urlencode($kode_lahan));
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
