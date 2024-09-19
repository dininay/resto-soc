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
    $eta_date = $_POST['eta_date'];
    $status_utensil = "In Process";
    $eta_by = "Last Updated by HRGA";
    // Update data di database
    $sql = "UPDATE utensil SET eta_date = '$eta_date', eta_by = '$eta_by', status_utensil = '$status_utensil' WHERE id = '$id'";
    
    
    $queryIR = "SELECT email FROM user WHERE level IN ('SCM')";
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
            $mail->Subject = 'Notification: 1 New Active ETA Update by HRGA Resto SOC Ticket';
                            $mail->Body    = '
                                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                    <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                    <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                        <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear SCM Team,</h2>
                                        <p>We would like to inform you that a new Active ETA Update by HRGA Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                        Your prompt attention to this matter is greatly appreciated.</p>
                                        <p></p>
                                        <p>Have a good day!</p>
                                    </div>
                                </div>
                            </div>';
                            $mail->AltBody = 'Dear SCM Team,'
                                        . 'We would like to inform you that a new Active ETA Update by HRGA Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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

    if ($conn->query($sql) === TRUE) {
        $sql_get_kode = "SELECT kode_lahan FROM utensil WHERE id = ?";
        $stmt_get_kode = $conn->prepare($sql_get_kode);
        $stmt_get_kode->bind_param("i", $id);
        $stmt_get_kode->execute();
        $stmt_get_kode->bind_result($kode_lahan);
        $stmt_get_kode->fetch();
        $stmt_get_kode->close();

        if (isset($kode_lahan) && !empty($kode_lahan)) {
            header("Location: " . $base_url . "/datatables-data-hrgautensil.php?id=" . urlencode($kode_lahan));
            exit;
        } else {
            echo "Error: Kode lahan tidak ditemukan.";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    
}

// Menutup koneksi database
$conn->close();
?>
