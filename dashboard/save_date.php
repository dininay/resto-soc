<?php
// Koneksi ke database
include "../koneksi.php";

// Ambil data JSON yang dikirim
if (isset($_POST['data'])) {
    $jsonData = $_POST['data'];
    $data = json_decode($jsonData, true);

    // Ambil catatan dari data JSON
    if (isset($data['catatan_psmfat']) && is_array($data['catatan_psmfat'])) {
        $catatan_psmfat = implode(';', array_filter($data['catatan_psmfat']));
    } else {
        $catatan_psmfat = '';
    }

    // Ambil ID dan kode_lahan dari data JSON
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $kode_lahan = isset($data['kode_lahan']) ? $data['kode_lahan'] : '';

    // Siapkan query untuk menyimpan data
    $sql_update = "UPDATE draft SET catatan_psmfat = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $catatan_psmfat, $id);
    
    if ($stmt_update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
}
?>
