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
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_defect"]) && isset($_POST["tanggal_retensi"]) ) {
    $id = $_POST["id"];
    $status_defect = $_POST["status_defect"];
    $tanggal_retensi = $_POST["tanggal_retensi"];
    $defect_date = null;

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Jika status_approvlegalvd diubah menjadi Approve
        if ($status_defect == 'Done') {
            $defect_date = date("Y-m-d H:i:s");
            
                $lamp_badefect = "";

                if(isset($_FILES["lamp_badefect"])) {
                    $lamp_badefect_paths = array();

                    // Loop through each file
                    foreach($_FILES['lamp_badefect']['name'] as $key => $filename) {
                        $file_tmp = $_FILES['lamp_badefect']['tmp_name'][$key];
                        $file_name = $_FILES['lamp_badefect']['name'][$key];
                        $target_dir = "../uploads/";
                        $target_file = $target_dir . basename($file_name);

                        // Attempt to move the uploaded file to the target directory
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $lamp_badefect_paths[] = $file_name;
                        } else {
                            echo "Gagal mengunggah file " . $file_name . "<br>";
                        }
                    }

                    // Join all file paths into a comma-separated string
                    $lamp_badefect = implode(",", $lamp_badefect_paths);
                }

                    // Query untuk memperbarui status status_defect di tabel draft
                    $sql_update = "UPDATE issue SET status_defect = ?, defect_date = ?, tanggal_retensi = ?, lamp_badefect = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ssssi", $status_defect, $defect_date, $tanggal_retensi, $lamp_badefect, $id);
                    $stmt_update->execute();

                    if ($stmt_update->affected_rows > 0) {
                        echo "Status berhasil diperbarui.";
                    } else {
                        echo "Gagal memperbarui status.";
                    }
                    // Komit transaksi
                    $conn->commit();
                    echo "Status berhasil diperbarui.";
                    
                    $departments = [
                        'SDG-Project',
                        'PMO'
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
                                $mail->Subject = 'Notification: New Information Issues Done by SDG Project Resto SOC Ticket';
                                $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear '. $department .' Team,</h2>
                                                <p>We would like to inform you that a new Information Issues Done by SDG Project Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear '. $department .' Team,'
                                                . 'We would like to inform you that a new Information Issues Done by SDG Project Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
                
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
                $sql = "UPDATE issue SET status_defect = ?, defect_date = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $status_defect, $defect_date, $id);
                $stmt->execute();
    
                // Check if update was successful
                if ($stmt->affected_rows > 0) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: Gagal memperbarui status. Tidak ada perubahan dilakukan.";
                }
            }

        // Komit transaksi
        $conn->commit();
        // Redirect ke halaman datatables-checkval-legal.php
        header("Location: ../datatables-sdgpk-issue.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}