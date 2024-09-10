<?php
// Aktifkan debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer-6.8.1/src/Exception.php';
require '../../PHPMailer-6.8.1/src/PHPMailer.php';
require '../../PHPMailer-6.8.1/src/SMTP.php';
require '../../vendor/autoload.php'; // Jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Periksa apakah ada data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $ff_3 = $_POST['ff_3'];
    $persen_ff3 = $_POST['persen_ff3'];

    // Periksa apakah ada lampiran
    $lamp_ff3 = "";
    if (isset($_FILES["lamp_ff3"])) {
        $lamp_ff3_paths = array();

        // Loop setiap file yang diunggah
        foreach ($_FILES['lamp_ff3']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ff3']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ff3']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Cek apakah file berhasil diupload
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ff3_paths[] = $file_name;
                echo "File berhasil diunggah: $target_file<br>";

                // Cek apakah file Excel dan impor data
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                if ($file_extension == 'xlsx' || $file_extension == 'xls') {
                    $spreadsheet = IOFactory::load($target_file);
                    $sheet = $spreadsheet->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();

                    // Loop setiap baris di file Excel
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $no = $sheet->getCell('A' . $row)->getValue();
                        $nama = $sheet->getCell('B' . $row)->getValue();
                        $gender = $sheet->getCell('C' . $row)->getValue();
                        $ttl = $sheet->getCell('D' . $row)->getValue();
                        if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCell('D' . $row))) {
                            $ttl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($ttl)->format('Y-m-d');
                        }
                        $alamat = $sheet->getCell('E' . $row)->getValue();
                        $usia = $sheet->getCell('F' . $row)->getValue();

                        // Insert data ke database
                        $status_lolos = "In Process";
                        $sql_insert = "INSERT INTO crew3 (kode_lahan, no, nama, gender, ttl, alamat, usia, status_lolos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("sissssss", $kode_lahan, $no, $nama, $gender, $ttl, $alamat, $usia, $status_lolos);

                        if ($stmt_insert->execute()) {
                            echo "Data berhasil disimpan untuk baris $row<br>";
                        } else {
                            echo "Gagal menyimpan data pada baris $row: " . $stmt_insert->error . "<br>";
                        }
                    }
                }
            } else {
                echo "Gagal mengunggah file " . htmlspecialchars($file_name) . "<br>";
            }
        }

        // Gabungkan semua file lampiran ke satu string
        $lamp_ff3 = implode(",", $lamp_ff3_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_hr SET lamp_ff3 = ?, ff_3 = ?, persen_ff3 = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $lamp_ff3, $ff_3, $persen_ff3, $id);

    if ($stmt->execute()) {
        echo "Data berhasil diperbarui di database.<br>";
        
        // Proses pengiriman email
        $queryIR = "SELECT email FROM user WHERE level IN ('Academy')";
        $resultIR = mysqli_query($conn, $queryIR);
        $toEmails = array();
        if ($resultIR && mysqli_num_rows($resultIR) > 0) {
            while ($rowIR = mysqli_fetch_assoc($resultIR)) {
                if (!empty($rowIR['email'])) {
                    $toEmails[] = $rowIR['email'];
                }
            }
        }

        // Cek apakah ada email yang ditemukan
        if (!empty($toEmails)) {
            try {
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Host = 'miegacoan.co.id';
                $mail->Port = 465;
                $mail->Username = 'resto-soc@miegacoan.co.id';
                $mail->Password = '9)5X]*hjB4sh';
                $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');

                foreach ($toEmails as $toEmail) {
                    $mail->addAddress($toEmail);
                }

                $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                $mail->Body = 'You have 1 New Active Resto SOC Ticket in the Resto SOC system.';
                $mail->AltBody = 'You have 1 New Active Resto SOC Ticket in the Resto SOC system.';

                $mail->send();
                echo "Email berhasil dikirim!<br>";

            } catch (Exception $e) {
                echo "Gagal mengirim email: {$mail->ErrorInfo}<br>";
            }
        } else {
            echo "Tidak ada email ditemukan.<br>";
        }

        header("Location: " . $base_url . "/datatables-hr-fulfillment-3.php");
        exit();
    } else {
        echo "Gagal memperbarui database: " . $stmt->error . "<br>";
    }
}

$conn->close();
?>