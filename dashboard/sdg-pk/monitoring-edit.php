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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_lahan = $_POST['kode_lahan'];
    
    $week_1 = floatval($_POST['week_1']);
    $week_2 = floatval($_POST['week_2']);
    $week_3 = floatval($_POST['week_3']);
    $week_4 = floatval($_POST['week_4']);
    $week_5 = floatval($_POST['week_5']);
    $week_6 = floatval($_POST['week_6']);
    $week_7 = floatval($_POST['week_7']);
    $week_8 = floatval($_POST['week_8']);
    $week_9 = floatval($_POST['week_9']);
    $week_10 = floatval($_POST['week_10']);
    $week_11 = floatval($_POST['week_11']);
    $week_12 = floatval($_POST['week_12']);
    $week_13 = floatval($_POST['week_13']);
    $week_14 = floatval($_POST['week_14']);
    $week_15 = floatval($_POST['week_15']);

    $lamp_monitoring = "";

    if (isset($_FILES['lamp_monitoring']) && $_FILES['lamp_monitoring']['error'][0] != UPLOAD_ERR_NO_FILE) {
        // Jika ada file yang diunggah
        $existing_files = isset($_POST['existing_files']) ? explode(",", $_POST['existing_files']) : array();
        $new_files = array();

        // Path ke direktori "uploads" dengan kode_lahan
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_monitoring']['name'] as $key => $filename) {
            if ($filename) {
                $target_file = $target_dir . basename($filename); // Simpan di folder kode_lahan

                if (move_uploaded_file($_FILES['lamp_monitoring']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename); // Simpan nama file yang berhasil diunggah
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_monitoring']['name'][$key] . "<br>";
                }
            }
        }

        // Gabungkan file yang sudah ada dan file yang baru diunggah
        $all_files = array_merge($existing_files, $new_files);
        $lamp_monitoring = implode(",", array_filter($all_files)); // Gabungkan nama file menjadi string
    } else {
        // Jika tidak ada file baru, gunakan file yang sudah ada
        $lamp_monitoring = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }

    // Update data di tabel konstruksi
    $sql = "UPDATE konstruksi SET week_1 = ?, week_2 = ?, week_3 = ?, week_4 = ?, week_5 = ?, week_6 = ?, week_7 = ?, week_8 = ?, week_9 = ?, week_10 = ?, week_11 = ?, week_12 = ?, week_13 = ?, week_14 = ?, week_15 = ?, lamp_monitoring = ? WHERE kode_lahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddddddsssddddss", $week_1, $week_2, $week_3, $week_4, $week_5, $week_6, $week_7, $week_8, $week_9, $week_10, $week_11, $week_12, $week_13, $week_14, $week_15, $lamp_monitoring, $kode_lahan);

    if ($stmt->execute()) {
        // Update tabel sdg_pk berdasarkan kode_lahan
        $month_1 = $week_1 + $week_2 + $week_3 + $week_4 + $week_5;
        $month_2 = $week_6 + $week_7 + $week_8 + $week_9 + $week_10;
        $month_3 = $week_11 + $week_12 + $week_13 + $week_14 + $week_15;

        $sql_update_sdg_pk = "UPDATE sdg_pk SET month_1 = ?, month_2 = ?, month_3 = ? WHERE kode_lahan = ?";
        $stmt_update = $conn->prepare($sql_update_sdg_pk);
        $stmt_update->bind_param("ddds", $month_1, $month_2, $month_3, $kode_lahan);
        $stmt_update->execute();

        // Redirect ke halaman data tabel setelah selesai
        header("Location:  " . $base_url . "/datatables-monitoring-op.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $departments = [
        'PMO',
        'SDG-Project'
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
                $mail->Subject = 'Notification: New Update List Progress Construction by SDG-Project Resto SOC Ticket';
                $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear '. $department .' Team,</h2>
                                                <p>We would like to inform you that a new Update List Progress COnstruction by SDG Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear '. $department .' Team,'
                                                . 'We would like to inform you that a new Update List Progress COnstruction by SDG Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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

    $stmt->close();
}

$conn->close();
?>
