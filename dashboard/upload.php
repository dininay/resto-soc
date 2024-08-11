<?php
// Pastikan file diunggah dengan benar
if (!empty($_FILES['file']['name'])) {
    // Tentukan direktori tempat menyimpan file yang diunggah
    $uploadDirectory = "uploads/";

    // Buat direktori jika belum ada
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // File yang diunggah
    $file = $_FILES['file'];

    // Tentukan nama file baru (misalnya, timestamp saat ini ditambahkan untuk mencegah nama file yang sama)
    $fileName = time() . '_' . $file['name'];

    // Pindahkan file yang diunggah ke direktori upload
    if (move_uploaded_file($file['tmp_name'], $uploadDirectory . $fileName)) {
        // Berhasil menyimpan file, lakukan tindakan lain yang diperlukan (misalnya, menyimpan nama file di database)
        echo "File berhasil diunggah.";
    } else {
        // Gagal menyimpan file, tindakan darurat (misalnya, tampilkan pesan kesalahan)
        echo "Gagal mengunggah file.";
    }
} else {
    // File tidak diunggah, tindakan darurat (misalnya, tampilkan pesan bahwa tidak ada file yang dipilih)
    echo "Tidak ada file yang dipilih.";
}
?>
