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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_scmipal"])) {
    $id = $_POST["id"];
    $status_scmipal = $_POST["status_scmipal"];
    var_dump($id); // Debugging untuk memastikan ID diterima
    var_dump($status_scmipal); // Debugging untuk memastikan status diterima
    $scmipal_date = null;
    $sla_spkwoipal = null;
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;

    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if (isset($_FILES["kronologi"])) {
        foreach ($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
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
        $scmipal_date = date("Y-m-d H:i:s");

        // Ambil jumlah SLA dari tabel master_slacons berdasarkan divisi 'wo-scm'
        $sql_select_sla = "SELECT sla FROM master_slacons WHERE divisi = 'spk-procur'";
        $result_sla = $conn->query($sql_select_sla);

        if ($row_sla = $result_sla->fetch_assoc()) {
            $sla_days = $row_sla['sla'];
            $start_date_obj = new DateTime($scmipal_date);
            $start_date_obj->modify("+$sla_days days");
            $sla_spkwoipal = $start_date_obj->format("Y-m-d");
        } else {
            $conn->rollback();
            echo "Error: SLA not found for divisi: wo-scm.";
            exit;
        }

        // Jika status_scmipal diubah menjadi Proceed
        if ($status_scmipal == 'In Process') {
            var_dump($status_scmipal); // Debugging untuk memastikan status Proceed masuk

            // Query untuk memperbarui status_scmipal dan obstacle
            $sql = "UPDATE socdate_sdg SET status_scmipal = ?, scmipal_date = ?, status_spkwoipal = 'In Process', sla_spkwoipal = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $status_scmipal, $scmipal_date, $sla_spkwoipal, $id);
            
            // Eksekusi query
            if ($stmt->execute()) {
                // Ambil kode_lahan dari tabel socdate_sdg
                $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_sdg WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->close();

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
            } else {
                $conn->rollback();
                echo "Error: " . $stmt->error;
            }
               
            $departments = [
                'Procurement'
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
                        $mail->Subject = 'Notification: New Active Resto SOC Ticket';
                        $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear '. $department .' Team,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear '. $department .' Team,'
                                                . 'We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
        } elseif ($status_scmipal == 'Pending') {
            // Ambil kode_lahan dari tabel socdate_sdg
            $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_sdg WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();
            
            $sql = "UPDATE socdate_sdg SET status_scmipal = ?, scmipal_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_scmipal, $scmipal_date, $id);

            if ($stmt->execute()) {
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
                $conn->rollback();
                echo "Error: " . $stmt->error;
            }
        } else {
            // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_scmipal
            $sql = "UPDATE socdate_sdg SET status_scmipal = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status_scmipal, $id);

            // Eksekusi query
            if ($stmt->execute() === TRUE) {
                echo "<script>
                        alert('Status berhasil diperbarui.');
                        window.location.href = window.location.href;
                     </script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        
        header("Location:  " . $base_url . "/datatables-scm-ipal.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
