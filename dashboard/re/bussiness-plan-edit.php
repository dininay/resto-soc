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
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];

    $status_land = $_POST['status_land'];
    $status_approvre = 'In Process';

    // Ambil tanggal hari ini untuk status_date
    $status_date = date('Y-m-d');

    // Ambil nilai sla dari tabel master_sla
    $sql_sla = "SELECT sla FROM master_sla WHERE divisi = 'RE'";
    $result_sla = $conn->query($sql_sla);

    if ($result_sla && $result_sla->num_rows > 0) {
        // Ambil nilai sla dari hasil query
        $row_sla = $result_sla->fetch_assoc();
        $sla = $row_sla['sla'];

        // Hitung tanggal deadline dengan menambahkan status_date dengan sla dari master_sla
        $deadline = date('Y-m-d', strtotime($status_date . ' + ' . $sla . ' days'));

        // Update data di database
        $sql_update = "UPDATE land SET status_land = '$status_land', status_approvre = '$status_approvre', status_date = '$status_date', sla = '$deadline' WHERE id = '$id'";

        if ($conn->query($sql_update) === TRUE) {
            // Mengirim email notifikasi
            $sql_land = "SELECT city, kode_lahan, nama_lahan FROM land WHERE id = '$id'";
            $result_land = $conn->query($sql_land);

            if ($result_land && $result_land->num_rows > 0) {
                $row_land = $result_land->fetch_assoc();
                $city = $row_land['city'];
                $kode_lahan = $row_land['kode_lahan'];
                $nama_lahan = $row_land['nama_lahan'];

                
                $queryIR = "SELECT email FROM user WHERE level IN ('Real Estate')";
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
                        
                        $imagePath = '../../assets/images/logo-email.png';
                        $mail->addEmbeddedImage($imagePath, 'embedded_image', 'logo-email.png', 'base64', 'image/png');

                        // Email content
                        $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                                            $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Real Estate,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Real Estate,'
                                                . 'We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.'
                                                . 'Have a good day!';

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
                header("Location: " . $base_url . "/datatables-bussiness-planning.php");
                exit();
            
            } else {
                echo "Error: Lahan tidak ditemukan.";
            }
        } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } else {
    echo "Error: Failed to retrieve sla from master_sla.";
    }
}

// Menutup koneksi database
$conn->close();
?>
