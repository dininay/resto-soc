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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_eqpdev"])) {
    $id = $_POST["id"];
    $status_eqpdev = $_POST["status_eqpdev"];
    $eqpdev_date = date("Y-m-d H:i:s");

    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if(isset($_FILES["kronologi"])) {
        foreach($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_dir . $target_file)) {
                $kronologi_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $kronologi = implode(",", $kronologi_paths);
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
    }
    
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_finallegal berdasarkan id
        $sql_update = "UPDATE equipment SET status_eqpdev = ?, eqpdev_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $status_eqpdev, $eqpdev_date,$id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_finallegal diubah menjadi Approve
            if ($status_eqpdev == 'Done') {
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_kom = "UPDATE equipment SET status_eqpdev = ?, eqpdev_date = ? WHERE id = ?";
                $stmt_update_kom = $conn->prepare($sql_update_kom);
                $stmt_update_kom->bind_param("ssi", $status_eqpdev, $eqpdev_date,$id);
                $stmt_update_kom->execute();

                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM resto WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("s", $kode_lahan);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
                
            $departments = [
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
                        $mail->Subject = 'Notification: New Information Equipment Delivery by SDG Resto SOC Ticket';
                        $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear '. $department .' Team,</h2>
                                                <p>We would like to inform you that a new Information Equipment Delivery by SDG Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear '. $department .' Team,'
                                                . 'We would like to inform you that a new Information Equipment Delivery by SDG Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
            } elseif ($status_eqpdev == 'Pending') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM resto WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
    
                // Query untuk memperbarui draft_legal, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE equipment SET status_eqpdev = ?, eqpdev_date = ?, status_eqpdevprocur = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $status_eqpdev, $eqpdev_date, $status_eqpdevprocur, $id);
                $stmt_update_re->execute();
    
                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");
    
                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $stmt_hold->execute();
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_vl di tabel re
                $sql = "UPDATE equipment SET status_eqpdev = ?, eqpdev_date = ?, status_eqpdevprocur = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $status_eqpdev, $eqpdev_date, $status_eqpdevprocur, $id);
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
            echo "Status dan data berhasil diperbarui.";
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        header("Location: ../datatables-eqp-delivery.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}