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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_eqpdevprocur"])) {
    $id = $_POST["id"];
    $status_eqpdevprocur = $_POST["status_eqpdevprocur"];
    $eqpdevprocur_date = date("Y-m-d H:i:s");

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
        $status_eqptaf = "In Process";
        // Query untuk memperbarui status_finallegal berdasarkan id
        $sql_update = "UPDATE equipment SET status_eqpdevprocur = ?, eqpdevprocur_date = ?, status_eqptaf = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $status_eqpdevprocur, $eqpdevprocur_date, $status_eqptaf, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_finallegal diubah menjadi Approve
            if ($status_eqpdevprocur == 'Approve') {
                // $status_eqptaf = "In Process";
                $eqpdevprocur_date = date("Y-m-d H:i:s");

                // // Ambil SLA dari tabel master_sla untuk divisi SPK
                // $sql_sla_spk = "SELECT sla FROM master_sla WHERE divisi = 'SPK-FAT'";
                // $result_sla_spk = $conn->query($sql_sla_spk);
                // if ($result_sla_spk->num_rows > 0) {
                //     $row_sla_spk = $result_sla_spk->fetch_assoc();
                //     $hari_sla_spk = $row_sla_spk['sla'];

                //     // Hitung sla_spkwo berdasarkan wo_date + SLA dari divisi SPK
                //     $sla_eqptaf = date("Y-m-d", strtotime($eqpdevprocur_date . ' + ' . $hari_sla_spk . ' days'));
                // } else {
                //     echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                //     exit;
                // }
                // Update status_kom di tabel resto menjadi "In Process"
                $sql_update_kom = "UPDATE equipment SET status_eqpdevprocur = ?, eqpdevprocur_date = ? WHERE id = ?";
                $stmt_update_kom = $conn->prepare($sql_update_kom);
                $stmt_update_kom->bind_param("ssi", $status_eqpdevprocur, $eqpdevprocur_date, $id);
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
                
                $queryIR = "SELECT email FROM user WHERE level IN ('SDG-Equipment')";
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
                        $mail->Subject = 'Notification: 1 New SPK Done by Procurement Resto SOC Ticket';
                                        $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                                                <p>You have 1 New SPK Done by Procurement Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                                                <p>Thank you for your prompt attention to this matter.</p>
                                                <p></p>
                                                <p>Best regards,</p>
                                                <p>Resto - SOC</p>
                                            </div>
                                        </div>';
                                        $mail->AltBody = 'Dear Team,'
                                                    . 'You have 1 New SPK Done by Procurement Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
                                                    . 'Thank you for your prompt attention to this matter.'
                                                    . 'Best regards,'
                                                    . 'Resto - SOC';

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
            } elseif ($status_eqpdevprocur == 'Pending') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM resto WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
    
                // Query untuk memperbarui draft_legal, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE equipment SET status_eqpdevprocur = ?, eqpdevprocur_date = ?, status_eqpsite = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $status_eqpdevprocur, $eqpdevprocur_date, $status_eqpsite, $id);
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
                $sql = "UPDATE equipment SET status_eqpdevprocur = ?, eqpdevprocur_date = ?, status_eqpsite = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $status_eqpdevprocur, $eqpdevprocur_date, $status_eqpsite, $id);
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
        header("Location: ../datatables-eqpdev-procur.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}