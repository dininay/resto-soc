<?php
// Koneksi ke database
include "../koneksi.php";

if (isset($_GET['kode_lahan'])) {
    $kode_lahan = $_GET['kode_lahan'];

    // Query untuk mendapatkan data dari tabel note_spk berdasarkan kode_lahan
    $sql = "SELECT catatan_proc, proc_date, catatan_spkfat, taf_date FROM note_spk WHERE kode_lahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_lahan);
    $stmt->execute();
    $stmt->bind_result($catatan_proc, $proc_date, $catatan_spkfat, $taf_date);

    // Fetch data and check if any result
    if ($stmt->fetch()) {
        echo json_encode([
            'catatan_proc' => $catatan_proc,
            'proc_date' => $proc_date,
            'catatan_spkfat' => $catatan_spkfat,
            'taf_date' => $taf_date
        ]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid kode_lahan']);
}
?>