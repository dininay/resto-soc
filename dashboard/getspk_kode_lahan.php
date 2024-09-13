<?php
// Koneksi ke database
include "../koneksi.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Query untuk mendapatkan kode_lahan berdasarkan id
    $sql = "SELECT kode_lahan FROM procurement WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($kode_lahan);
    
    // Fetch data and check if any result
    if ($stmt->fetch()) {
        echo json_encode([
            'kode_lahan' => $kode_lahan
        ]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid id']);
}
?>