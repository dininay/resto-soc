<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer-6.8.1/src/Exception.php';
require '../../PHPMailer-6.8.1/src/PHPMailer.php';
require '../../PHPMailer-6.8.1/src/SMTP.php';
require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $ff_1 = $_POST['ff_1'];
    $persen_ff1 = $_POST['persen_ff1'];
    $lamp_ff1 = "";

    // Periksa apakah ada file yang diunggah
    if(isset($_FILES["lamp_ff1"])) {
        $lamp_ff1_paths = array();

        // Loop melalui setiap file yang diunggah
        foreach($_FILES['lamp_ff1']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ff1']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ff1']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Coba pindahkan file yang diunggah ke direktori target
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ff1_paths[] = $file_name;

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
                        $sql_insert = "INSERT INTO crew (kode_lahan, no, nama, gender, ttl, alamat, usia, status_lolos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
        $lamp_ff1 = implode(",", $lamp_ff1_paths);
    }

    // Update data di database untuk tabel socdate_hr
    $sql = "UPDATE socdate_hr SET lamp_ff1 = ?, persen_ff1 = ?, ff_1 = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $lamp_ff1, $persen_ff1, $ff_1, $id);

    if ($stmt->execute()) {
        
        $queryIR = "SELECT email FROM user WHERE level IN ('Academy')";
        $resultIR = mysqli_query($conn, $queryIR);

        if ($resultIR && mysqli_num_rows($resultIR) > 0) {
            while ($rowIR = mysqli_fetch_assoc($resultIR)) {
                if (!empty($rowIR['email'])) {
                    $toEmails[] = $rowIR['email'];
                }
            }
        }
        var_dump($toEmails);
        if (!empty($toEmails)) {

            try {
                // SMTP configuration
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                // $mail->SMTPDebug = 2;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = 'miegacoan.co.id';
                $mail->Port = 465;
                $mail->Username = 'resto-soc@miegacoan.co.id';
                $mail->Password = '9)5X]*hjB4sh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');

                foreach ($toEmails as $toEmail) {
                    $mail->addAddress($toEmail);
                }

                // Email content
                $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                                $mail->Body    = '
                                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                    <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                        <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                                        <p>You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                                        <p>Thank you for your prompt attention to this matter.</p>
                                        <p></p>
                                        <p>Best regards,</p>
                                        <p>Resto - SOC</p>
                                    </div>
                                </div>';
                                $mail->AltBody = 'Dear Team,'
                                            . 'You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
                                            . 'Thank you for your prompt attention to this matter.'
                                            . 'Best regards,'
                                            . 'Resto - SOC';

                // Send email
                if ($mail->send()) {
                    echo "Email sent successfully!<br>";
                } else {
                    echo "Failed to send email. Error: {$mail->ErrorInfo}<br>";
                }

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No email found for the selected resto or IR users.";
        }
        header("Location: " . $base_url . "/datatables-hr-fulfillment.php");
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