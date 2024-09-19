<?php
if (isset($_GET['kode_lahan'])) {
    $kode_lahan = $_GET['kode_lahan'];
    $folderPath = "uploads/" . $kode_lahan;

    if (is_dir($folderPath)) {
        $zip = new ZipArchive();
        $zipFileName = $kode_lahan . ".zip";
        $zipFilePath = "downloads/" . $zipFileName;

        // Pastikan direktori downloads ada
        if (!is_dir('downloads')) {
            mkdir('downloads', 0777, true);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            // Buka folder kode_lahan dan tambahkan hanya file ke zip (tanpa subfolder)
            $files = scandir($folderPath);

            foreach ($files as $file) {
                $filePath = $folderPath . '/' . $file;
                if (is_file($filePath)) {
                    // Tambahkan file langsung ke zip dengan nama file saja
                    $zip->addFile($filePath, $file);
                }
            }
            $zip->close();

            // Setelah zip selesai dibuat, kirim header untuk memulai unduhan
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
            header('Content-Length: ' . filesize($zipFilePath));

            // Kirim file ke output buffer
            readfile($zipFilePath);

            // Hapus file zip setelah diunduh
            unlink($zipFilePath);
        } else {
            echo "Gagal membuat file zip.";
        }
    } else {
        echo "Folder tidak ditemukan.";
    }
} else {
    echo "Kode lahan tidak disertakan.";
}
?>
