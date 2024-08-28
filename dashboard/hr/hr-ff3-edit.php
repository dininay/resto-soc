<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $ff_3 = $_POST['ff_3'];
    $persen_ff3 = $_POST['persen_ff3'];
    $lamp_ff3 = "";

    // Periksa apakah ada file yang diunggah
    if(isset($_FILES["lamp_ff3"])) {
        $lamp_ff3_paths = array();

        // Loop melalui setiap file yang diunggah
        foreach($_FILES['lamp_ff3']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ff3']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ff3']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Coba pindahkan file yang diunggah ke direktori target
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ff3_paths[] = $file_name;

                // Periksa apakah file Excel dan jika iya, import data ke tabel crew
                if (pathinfo($file_name, PATHINFO_EXTENSION) == 'xlsx' || pathinfo($file_name, PATHINFO_EXTENSION) == 'xls') {
                    $spreadsheet = IOFactory::load($target_file);
                    $sheet = $spreadsheet->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();

                    // Looping through each row in the excel sheet
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $no = $sheet->getCell('A'.$row)->getValue();
                        $nama = $sheet->getCell('B'.$row)->getValue();
                        $gender = $sheet->getCell('C'.$row)->getValue();
                        $ttl = $sheet->getCell('D'.$row)->getValue();
                        if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCell('D'.$row))) {
                            $ttl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($ttl)->format('Y-m-d');
                        }
                        $alamat = $sheet->getCell('E'.$row)->getValue();
                        $usia = $sheet->getCell('F'.$row)->getValue();

                        // Insert data ke tabel crew
                        $status_lolos = "In Process";
                        $sql_insert = "INSERT INTO crew3 (kode_lahan, no, nama, gender, ttl, alamat, usia, status_lolos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("sissssss", $kode_lahan, $no, $nama, $gender, $ttl, $alamat, $usia, $status_lolos);
                        $stmt_insert->execute();
                    }
                }
            } else {
                echo "Gagal mengunggah file " . htmlspecialchars($file_name) . "<br>";
            }
        }

        // Gabungkan semua path file menjadi string yang dipisahkan oleh koma
        $lamp_ff3 = implode(",", $lamp_ff3_paths);
    }

    // Update data di database untuk tabel socdate_hr
    $sql = "UPDATE socdate_hr SET lamp_ff3 = ?, persen_ff3 = ?, ff_3 = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $lamp_ff3, $persen_ff3, $ff_3, $id);

    if ($stmt->execute()) {
        header("Location: " . $base_url . "/datatables-hr-fulfillment-3.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Menutup statement
    $stmt->close();
}

// Menutup koneksi database
$conn->close();
?>