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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_tafpay"])) {
    $id = $_POST["id"];
    $status_tafpay = $_POST["status_tafpay"];
    var_dump($id); // Debugging untuk memastikan ID diterima
    var_dump($status_tafpay); // Debugging untuk memastikan status diterima
    $tafpay_date = null;
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
        $tafpay_date = date("Y-m-d H:i:s");

        // Jika status_tafpay diubah menjadi Proceed
        if ($status_tafpay == 'Paid') {
                $sql_get_kode_lahan = "SELECT status_procurspkwofa FROM socdate_sdg WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($status_procurspkwofa);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                if ($status_procurspkwofa === "Approve" && $status_tafpay === "Paid") {
                    $status_sdgsumber = "Done";
                } else {
                    $status_sdgsumber = "Proceed";
                }
            var_dump($status_tafpay); // Debugging untuk memastikan status Proceed masuk

            // Query untuk memperbarui status_tafpay dan obstacle
            $sql = "UPDATE socdate_sdg SET status_tafpay = ?, tafpay_date = ?, status_sdgsumber = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $status_tafpay, $tafpay_date, $status_sdgsumber, $id);
            
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
            $queryIR = "SELECT email FROM user WHERE level IN ('SDG-Project')";
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
                    $mail->Subject = 'Notification: 1 New Information Of Payment by TAF Resto SOC Ticket';
                                    $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear SDG-Project Team,</h2>
                                                <p>We would like to inform you that a new Information Of Payment by TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear SDG-Project Team,'
                                                . 'We would like to inform you that a new Information Of Payment by TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
        } elseif ($status_tafpay == 'Pending') {
            // Ambil kode_lahan dari tabel socdate_sdg
            $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_sdg WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();
            
            $sql = "UPDATE socdate_sdg SET status_tafpay = ?, tafpay_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status_tafpay, $tafpay_date, $id);

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
            // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_tafpay
            $sql = "UPDATE socdate_sdg SET status_tafpay = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status_tafpay, $id);

            // Eksekusi query
            if ($stmt->execute() === TRUE) {
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        
        header("Location:  " . $base_url . "/datatables-tafpay-listrikair.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
