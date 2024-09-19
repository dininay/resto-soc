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
    // Ambil nilai dari formulir
    $kode_lahan = $_POST['kode_lahan'];
    $status_wojobadd = "In Process";
    $status_rabjobadd = "In Process";
    $lamp_wojobadd = "";

    if(isset($_FILES["lamp_wojobadd"])) {
        $lamp_wojobadd_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_wojobadd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_wojobadd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_wojobadd']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_wojobadd_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_wojobadd = implode(",", $lamp_wojobadd_paths);
    }
    
    $defect_date = date('Y-m-d');
    // Update data di database untuk tabel resto
    $sql1 = "INSERT INTO jobadd (kode_lahan, lamp_wojobadd, status_wojobadd, status_rabjobadd) VALUES (?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssss", $kode_lahan, $lamp_wojobadd, $status_wojobadd, $status_rabjobadd);

    // Execute both queries
    if ($stmt1->execute()) {
        $departments = [
            'SDG-QS'
        ];
        
        // Loop through each department
        foreach ($departments as $department) {
            // Query to get emails for the current department
            $query = "SELECT email FROM user WHERE level = '$department'";
            $result = mysqli_query($conn, $query);
        
            $toEmails = [];
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!empty($row['email'])) {
                        $toEmails[] = $row['email'];
                    }
                }
            }
        
            if (!empty($toEmails)) {
                try {
                    // SMTP configuration
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'ssl';
                    $mail->Host = 'miegacoan.co.id';
                    $mail->Port = 465;
                    $mail->Username = 'resto-soc@miegacoan.co.id';
                    $mail->Password = '9)5X]*hjB4sh';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');
        
                    // Add recipients
                    foreach ($toEmails as $toEmail) {
                        $mail->addAddress($toEmail);
                    }
        
                    // Add embedded image
                    $imagePath = '../../assets/images/logo-email.png';
                    $mail->addEmbeddedImage($imagePath, 'embedded_image', 'logo-email.png', 'base64', 'image/png');
        
                    // Email content with personalized greeting
                    $mail->Subject = 'Notification: New Active RAB Job Add Less Resto SOC Ticket';
                    $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear '. $department .' Team,</h2>
                                                <p>We would like to inform you that a new Active RAB Job Add Less Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear '. $department .' Team,'
                                                . 'We would like to inform you that a new Active RAB Job Add Less Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.'
                                                . 'Have a good day!';
        
                    // Send email
                    if ($mail->send()) {
                        echo "Email sent successfully to $department team!<br>";
                    } else {
                        echo "Failed to send email to $department team. Error: {$mail->ErrorInfo}<br>";
                    }
                } catch (Exception $e) {
                    echo "Message could not be sent to $department team. Mailer Error: {$mail->ErrorInfo}<br>";
                }
            } else {
                echo "No email found for the $department team.<br>";
            }
        }
        header("Location: " . $base_url . "/datatables-wo-tambahkurang.php");
        exit();
    } else {
        echo "Error: " . $stmt1->error;
    }

    // Close statements
    $stmt1->close();
}

// Menutup koneksi database
$conn->close();
?>