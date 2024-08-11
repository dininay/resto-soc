<?php
require_once '../../vendor/autoload.php'; // Sesuaikan path ini dengan path Dompdf di proyek Anda

use Dompdf\Dompdf;

// Periksa apakah ada parameter id yang dikirimkan
if(isset($_GET['id'])) {
    // Ambil ID konten yang ingin Anda ekspor ke PDF
    $id = $_GET['id'];

    // Mulai menghasilkan HTML yang akan diubah menjadi PDF
    $html = '<html><body>';
    $html .= '<div class="card-body printable" id="' . $id . '">';
    // Tambahkan konten yang ingin Anda ekspor ke PDF di sini, misalnya:
    $html .= '<h1>Contoh Ekspor PDF</h1>';
    $html .= '<p>Ini adalah contoh konten yang ingin Anda ekspor ke PDF.</p>';
    $html .= '</div>';
    $html .= '</body></html>';

    // Buat instance Dompdf
    $dompdf = new Dompdf();

    // Muat HTML ke Dompdf
    $dompdf->loadHtml($html);

    // Set pengaturan (opsional)
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Output PDF
    $dompdf->stream('output.pdf', ['Attachment' => false]);
} else {
    // Jika parameter id tidak ada, tampilkan pesan kesalahan
    echo "ID konten tidak ditemukan.";
}
?>
