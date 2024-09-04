<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.8.1/src/Exception.php';
require 'PHPMailer-6.8.1/src/PHPMailer.php';
require 'PHPMailer-6.8.1/src/SMTP.php';
require 'vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);

include "koneksi.php";

// Query untuk menghitung jumlah tiket per divisi untuk hari ini
// Tanggal hari ini
$date = date('Y-m-d');

// Array untuk menyimpan jumlah tiket per divisi
$ticketCounts = [];

// Query untuk divisi Negotiator
$sql_nego = "SELECT COUNT(*) as jumlah_tiket FROM re WHERE status_approvnego = 'In Process' AND DATE(tanggal) = '$date'";
$result_nego = $conn->query($sql_nego);
$ticketCounts['Negotiator'] = $result_nego->fetch_assoc()['jumlah_tiket'];

// Query untuk divisi Real Estate
$sql_real_estate_land = "SELECT COUNT(*) as jumlah_tiket FROM land WHERE status_approvre = 'In Process' AND DATE(tanggal) = '$date'";
$result_real_estate_land = $conn->query($sql_real_estate_land);
$ticketCounts['Real Estate'] = $result_real_estate_land->fetch_assoc()['jumlah_tiket'];

$sql_real_estate_re = "SELECT COUNT(*) as jumlah_tiket FROM re WHERE status_vl = 'In Process' AND DATE(tanggal) = '$date'";
$result_real_estate_re = $conn->query($sql_real_estate_re);
$ticketCounts['Real Estate'] += $result_real_estate_re->fetch_assoc()['jumlah_tiket'];

// Ambil email berdasarkan level divisi tertentu
$queryIR = "SELECT email FROM user WHERE level IN ('Procurement','SDG-QS')";
$resultIR = mysqli_query($conn, $queryIR);

$toEmails = [];
if ($resultIR && mysqli_num_rows($resultIR) > 0) {
    while ($rowIR = mysqli_fetch_assoc($resultIR)) {
        if (!empty($rowIR['email'])) {
            $toEmails[] = $rowIR['email'];
        }
    }
}

if (!empty($toEmails)) {
    try {
        // Konfigurasi SMTP
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        // $mail->Host = 'miegacoan.co.id';
        // $mail->SMTPAuth = true;
        // $mail->Username = 'resto-soc@miegacoan.co.id';
        // $mail->Password = '9)5X]*hjB4sh';
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->Port = 465;
            $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
            $mail->SMTPAuth = true;
            $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
            $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;
        $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');

        // Tambahkan penerima dari array $toEmails
        foreach ($toEmails as $toEmail) {
            $mail->addAddress($toEmail);
        }

        // Buat konten email berdasarkan jumlah tiket
        $bodyContent = '
            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                            <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                    <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                    <p>You have the following ticket updates for today:</p>
                    <ul>';
        foreach ($ticketCounts as $divisi => $jumlah_tiket) {
            $bodyContent .= "<li>Divisi $divisi: $jumlah_tiket tiket</li>";
        }
        $bodyContent .= '</ul>
                    <p>Please log in to the SOC application to review the details.</p>
                    <p>Thank you for your prompt attention to this matter.</p>
                    <p>Best regards,</p>
                    <p>Resto - SOC</p>
                </div>
            </div>';

        // Set konten email
        $mail->Subject = 'Notification: Daily Ticket Update';
        $mail->Body    = $bodyContent;
        $mail->AltBody = 'Dear Team, You have new ticket updates. Please log in to the SOC application for details. Thank you. Best regards, Resto - SOC';

        // Kirim email
        if ($mail->send()) {
            echo "Email sent successfully!<br>";
        } else {
            echo "Failed to send email. Error: {$mail->ErrorInfo}<br>";
        }

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No email found for the selected users.";
}

$conn->close();
?>
