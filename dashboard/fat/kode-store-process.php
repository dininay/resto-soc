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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])  && isset($_POST["status_tafkode"])) {
    $id = $_POST["id"];
    $status_tafkode = $_POST["status_tafkode"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;
    $tafkode_date = date("Y-m-d");

    // Periksa apakah file kronologi ada dalam $_FILES
    if (isset($_FILES["kronologi"])) {
        $kronologi_paths = array();
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
    }
    
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_tafkode berdasarkan id
        $sql_update = "UPDATE dokumen_loacd SET status_tafkode = ?, tafkode_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $tafkode_date = date("Y-m-d");
        $stmt_update->bind_param("ssi", $status_tafkode, $tafkode_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_tafkode diubah menjadi 'Approve'
            if ($status_tafkode == 'Done') {
                $tafkode_date = date("Y-m-d");
                $status_woeqp = "Approve";
                $status_eqpdevprocur = "Approve";
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel dokumen_loacd
                $sql_update_pending = "UPDATE dokumen_loacd SET status_tafkode = ?, tafkode_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssi", $status_tafkode, $tafkode_date, $id);
                $stmt_update_pending->execute();
                
                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = (SELECT kode_lahan FROM dokumen_loacd WHERE id = ?)";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("i", $id);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = (SELECT kode_lahan FROM dokumen_loacd WHERE id = ?)";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("si", $status_hold, $id);
                    $stmt_update_hold->execute();
                }
                
            $queryIR = "SELECT email FROM user WHERE level IN ('PMO')";
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
                    $mail->Subject = 'Notification: 1 New Information Store Code Has Been Created by TAF Resto SOC Ticket';
                                    $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear PMO Team,</h2>
                                                <p>We would like to inform you that a new Information Store Code Has Been Created by TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear PMO Team,'
                                                . 'We would like to inform you that a new Information Store Code Has Been Created by TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
                     
        //         $queryIR = "SELECT email FROM user WHERE level IN ('Real Estate','BoD','Legal','Negotiator','SDG-Design','SDG-QS','SDG-Project','SDG-Equipment','Procurement','HR','Academy','IT','SCM','Marketing','IR','PMO','MnR')";
        //         $resultIR = mysqli_query($conn, $queryIR);

        //         if ($resultIR && mysqli_num_rows($resultIR) > 0) {
        //             while ($rowIR = mysqli_fetch_assoc($resultIR)) {
        //                 if (!empty($rowIR['email'])) {
        //                     $toEmails[] = $rowIR['email'];
        //                 }
        //             }
        //         }
        //         var_dump($toEmails);
        //         if (!empty($toEmails)) {

        //             try {
        //                 // SMTP configuration
        //                 $mail = new PHPMailer(true);
        //                 $mail->isSMTP();
        //                 // $mail->SMTPDebug = 2;
        //                 $mail->SMTPAuth = true;
        //                 $mail->SMTPSecure = 'ssl';
        //                 $mail->Host = 'miegacoan.co.id';
        //                 $mail->Port = 465;
        //                 $mail->Username = 'resto-soc@miegacoan.co.id';
        //                 $mail->Password = '9)5X]*hjB4sh';
        //                 $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        //                 $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');

        //                 foreach ($toEmails as $toEmail) {
        //                 $mail->addAddress($toEmail);
        //             }
        // $imagePath = '../../assets/images/logo-email.png';
        // $mail->addEmbeddedImage($imagePath, 'embedded_image', 'logo-email.png', 'base64', 'image/png');

        //                 // Email content
        //                 $mail->Subject = 'Notification: 1 New Information from TAF Resto SOC Ticket';
        //                                 $mail->Body    = '
        //                                 <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
        //                                 <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
        //                                     <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
        //                                     <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
        //                                         <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
        //                                         <p>You have 1 New Information from IT Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
        //                                         <p>Thank you for your prompt attention to this matter.</p>
        //                                         <p></p>
        //                                         <p>Have a good day!</p>
        //                                     </div>
        //                                 </div>
        //                                 </div>';
        //                                 $mail->AltBody = 'Dear Team,'
        //                                             . 'You have 1 New Information from IT Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
        //                                             . 'Thank you for your prompt attention to this matter.'
        //                                             . 'Best regards,'
        //                                             . 'Have a good day!';

        //                 // Send email
        //                 if ($mail->send()) {
        //                     echo "Email sent successfully!<br>";
        //                 } else {
        //                     echo "Failed to send email. Error: {$mail->ErrorInfo}<br>";
        //                 }

        //             } catch (Exception $e) {
        //                 echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        //             }
        //         } else {
        //             echo "No email found for the selected resto or IR users.";
        //         }
                // try {
                //     // Pengaturan server SMTP
                //     $mail->isSMTP();
                //     $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
                //     $mail->SMTPAuth = true;
                //     $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
                //     $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
                //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                //     $mail->Port = 2525;
                    
                //     // Pengaturan pengirim dan penerima
                //     $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');
                
                //     // Query untuk mendapatkan email pengguna dengan level tertentu
                //     $sql = "SELECT email FROM user WHERE level IN ('Real Estate','BoD','Legal','Negotiator','SDG-Design','SDG-QS','SDG-Project','SDG-Equipment','Procurement','HR','Academy','IT','SCM','Marketing','IR','PMO','MnR')";
                //     $result = $conn->query($sql);
                
                //     if ($result->num_rows > 0) {
                //         $emails = [];
                
                //         while ($row = $result->fetch_assoc()) {
                //             $email = $row['email'];
                //             // Validasi format email sebelum menambahkannya ke array penerima
                //             if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //                 $emails[] = $email;
                //             } else {
                //                 echo "Invalid email format: " . $email;
                //             }
                //         }
                
                //         // Tambahkan penerima dalam batch untuk mengurangi waktu pengiriman
                //         $batchSize = 10;
                //         $totalEmails = count($emails);

                //         for ($i = 0; $i < $totalEmails; $i += $batchSize) {
                //             $batchEmails = array_slice($emails, $i, $batchSize);

                //             try {
                //                 // Reset penerima email untuk setiap batch
                //                 $mail->clearAddresses();

                //                 foreach ($batchEmails as $email) {
                //                     $mail->addAddress($email); // Tambahkan setiap penerima email
                //                 }

                //                 // Konten email
                //                 $mail->isHTML(true);
                //                 $mail->Subject = 'Notification: A New Store Code Has Been Assigned Resto SOC';
                //                 $mail->Body = '
                //                 <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                //                     <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                           // <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                //                         <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                //                         <p>A new store code has been assigned in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                //                         <p>Thank you for your prompt attention to this matter.</p>
                //                         <p>Best regards,</p>
                //                         <p>Resto - SOC</p>
                //                     </div>
                //                 </div>';
                //                 $mail->AltBody = 'Dear Team,'
                //                             . 'A new store code has been assigned in the Resto SOC system. Please log in to the SOC application to review the details.'
                //                             . 'Thank you for your prompt attention to this matter.'
                //                             . 'Best regards,'
                //                             . 'Resto - SOC';

                //                 // Kirim email
                //                 $mail->send();
                //                 echo "Batch sent successfully.<br>";
                //             } catch (Exception $e) {
                //                 echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
                //             }

                //             // Untuk menghindari rate limiting, berikan jeda antara pengiriman batch
                //             sleep(5); // Jeda selama 5 detik
                //         }
                //     } else {
                //         echo "No emails found.";
                //     }

                // } catch (Exception $e) {
                //     echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
                // }
            } elseif ($status_tafkode == 'Pending') {
                // Ambil kode_lahan dari tabel dokumen_loacd
                $sql_get_kode_lahan = "SELECT kode_lahan FROM dokumen_loacd WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel dokumen_loacd
                $sql_update_pending = "UPDATE dokumen_loacd SET status_tafkode = ?, tafkode_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssi", $status_tafkode, $tafkode_date, $id);
                $stmt_update_pending->execute();

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
                
                    // // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
                    // $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'Review PSM TAF'";
                    // $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
                    // if ($result_sla_stkonstruksi->num_rows > 0) {
                    //     $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                    //     $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];
                    //     $sla_fat = date("Y-m-d", strtotime($tafkode_date . ' + ' . $hari_sla_stkonstruksi . ' days'));
                    // } else {
                    //     $conn->rollback();
                    //     echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                    //     exit;
                    // }
                    // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_tafkode
                    $sql_update_other = "UPDATE dokumen_loacd SET status_tafkode = ?, tafkode_date = ? WHERE id = ?";
                    $stmt_update_other = $conn->prepare($sql_update_other);
                    $stmt_update_other->bind_param("ssi", $status_tafkode, $tafkode_date, $id);
    

                // Eksekusi query
                if ($stmt_update_other->execute() === TRUE) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: " . $sql_update_other . "<br>" . $conn->error;
                }
            }
            // Komit transaksi
            $conn->commit();
            echo "Status dan data berhasil diperbarui.";
            // Redirect ke halaman datatables-tender.php
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        header("Location: ../datatables-kode-store-taf.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>