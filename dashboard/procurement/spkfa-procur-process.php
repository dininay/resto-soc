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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])  && isset($_POST["status_procurspkwofa"])&& isset($_POST["catatan_spkwofa"])) {
    $id = $_POST["id"];
    $status_procurspkwofa = $_POST["status_procurspkwofa"];
    $catatan_spkwofa = $_POST["catatan_spkwofa"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;
    $spkwofa_date = date("Y-m-d");

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
        // Query untuk memperbarui status_procurspkwofa berdasarkan id
        $sql_update = "UPDATE socdate_sdg SET status_procurspkwofa = ?, catatan_spkwofa = ?, spkwofa_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $spkwofa_date = date("Y-m-d");
        $stmt_update->bind_param("sssi", $status_procurspkwofa, $catatan_spkwofa, $spkwofa_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_procurspkwofa diubah menjadi 'Approve'
            if ($status_procurspkwofa == 'Approve') {
                $spkwofa_date = date("Y-m-d");

                // // Tentukan spkwoipal_date
                // // Ambil SLA dari tabel master_sla untuk divisi SPK
                // $sql_sla_spk = "SELECT sla FROM master_sla WHERE divisi = 'SPK-FAT'";
                // $result_sla_spk = $conn->query($sql_sla_spk);
                // if ($result_sla_spk->num_rows > 0) {
                //     $row_sla_spk = $result_sla_spk->fetch_assoc();
                //     $hari_sla_spk = $row_sla_spk['sla'];

                //     // Hitung sla_spkwo berdasarkan wo_date + SLA dari divisi SPK
                //     $sla_spkfataf = date("Y-m-d", strtotime($spkwofa_date . ' + ' . $hari_sla_spk . ' days'));
                // } else {
                //     echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                //     exit;
                // }
                // $status_spkfataf = "In Process";
                // Tentukan spkwofa_date
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel socdate_sdg
                
                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_get_kode_lahan = "SELECT status_tafpay FROM socdate_sdg WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($status_tafpay);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                if ($status_procurspkwofa === "Approve" && $status_tafpay === "Paid") {
                    $status_sdgsumber = "Done";
                } else {
                    $status_sdgsumber = "Proceed";
                }

                $status_sdgsumber = "Done";
                $sql_update_pending = "UPDATE socdate_sdg SET status_procurspkwofa = ?, catatan_spkwofa = ?, spkwofa_date = ?, status_sdgsumber = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssssi", $status_procurspkwofa, $catatan_spkwofa, $spkwofa_date, $status_sdgsumber, $id);
                $stmt_update_pending->execute();
                    
                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = (SELECT kode_lahan FROM socdate_sdg WHERE id = ?)";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("i", $id);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = (SELECT kode_lahan FROM socdate_sdg WHERE id = ?)";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("si", $status_hold, $id);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
                
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
                        
                        $imagePath = '../../assets/images/logo-email.png';
                        $mail->addEmbeddedImage($imagePath, 'embedded_image', 'logo-email.png', 'base64', 'image/png');

                        // Email content
                        $mail->Subject = 'Notification: 1 New SPK Sumber Air Release Resto SOC Ticket';
                                            $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear SDG-Project,</h2>
                                                <p>We would like to inform you that a new SPK Sumber Air Release Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear SDG-Project,'
                                                . 'We would like to inform you that a new SPK Sumber Air Release Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
            } elseif ($status_procurspkwofa == 'Pending') {
                // Ambil kode_lahan dari tabel socdate_sdg
                $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_sdg WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel socdate_sdg
                $sql_update_pending = "UPDATE socdate_sdg SET status_procurspkwofa = ?, catatan_spkwofa = ?, spkwofa_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $status_procurspkwofa, $catatan_spkwofa, $spkwofa_date, $id);
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
                
                    // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
                    $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'SPK-FAT'";
                    $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
                    if ($result_sla_stkonstruksi->num_rows > 0) {
                        $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                        $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];
                        $sla_fat = date("Y-m-d", strtotime($spkwofa_date . ' + ' . $hari_sla_stkonstruksi . ' days'));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                        exit;
                    }
                    // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_procurspkwofa
                    $sql_update_other = "UPDATE socdate_sdg SET status_procurspkwofa = ?, catatan_spkwofa = ?, spkwofa_date = ? WHERE id = ?";
                    $stmt_update_other = $conn->prepare($sql_update_other);
                    $stmt_update_other->bind_param("sssi", $status_procurspkwofa, $catatan_spkwofa, $spkwofa_date, $id);
                
                        // Update sla_spk dan status_spk di tabel resto
                        // $sql_update_spk = "UPDATE procurement SET status_fattender = 'In Process', sla_fattender WHERE kode_lahan = (SELECT kode_lahan FROM socdate_sdg WHERE id = ?)";
                        // $stmt_update_spk = $conn->prepare($sql_update_spk);
                        // $stmt_update_spk->bind_param("si", $sla_fat, $id);
                        // $stmt_update_spk->execute();

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
        header("Location: ../datatables-spkfa-procurement.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>