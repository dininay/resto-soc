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

// Set timezone ke Jakarta
date_default_timezone_set('Asia/Jakarta');

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_nego"]) && isset($_POST["catatan_psm"]) && is_array($_POST['catatan_psm'])) {
    $id = $_POST["id"];
    $confirm_nego = $_POST["confirm_nego"];
    $catatan_psm = $_POST["catatan_psm"];

    $catatan_psm = implode(';', array_map('htmlspecialchars', $_POST['catatan_psm']));

    $end_date = null;
    $legal_date = date("Y-m-d H:i:s");
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
        // Jika status_approvlegalvd diubah menjadi Approve
        if ($confirm_nego == 'In Review By TAF') {
            $end_date = date("Y-m-d H:i:s");

            // Ambil confirm_fatpsm dari tabel draft berdasarkan id
            $sql_check_confirm = "SELECT confirm_fatpsm FROM draft WHERE id = ?";
            $stmt_check = $conn->prepare($sql_check_confirm);
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $result_confirm = $stmt_check->get_result();

            if ($result_confirm->num_rows > 0) {
                $row_confirm = $result_confirm->fetch_assoc();
                $confirm_fatpsm = $row_confirm['confirm_fatpsm'];
                
                // Jika confirm_fatpsm = 'In Revision'
                if ($confirm_fatpsm == 'In Revision') {
                    // Ambil SLA dari tabel master_sla untuk divisi FAT-Sewa-2
                    $sql_sla_sewa2 = "SELECT sla FROM master_sla WHERE divisi = 'FAT-Sewa-2'";
                    $result_sla_sewa2 = $conn->query($sql_sla_sewa2);
                    if ($result_sla_sewa2->num_rows > 0) {
                        $row_sla_sewa2 = $result_sla_sewa2->fetch_assoc();
                        $hari_sla_sewa2 = $row_sla_sewa2['sla'];

                        // Cek waktu saat ini
                        $current_time = date('H:i');

                        // Jika submit setelah jam 12:00 siang, tambahkan 1 hari ke SLA
                        if ($current_time > '12:00') {
                            $hari_sla_sewa2 += 1;
                        }

                        // Hitung tanggal SLA berdasarkan end_date
                        $slafatpsm_date = date("Y-m-d", strtotime($end_date . ' + ' . $hari_sla_sewa2 . ' days'));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi FAT-Sewa-2.";
                        exit;
                    }
                    
                    $confirm_fatpsm = "In Process";
                    // Perbarui status di tabel draft
                    $sql_update = "UPDATE draft SET confirm_nego = ?, catatan_psm = ?, end_date = ?, confirm_fatpsm = ?, slafatpsm_date = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("sssssi", $confirm_nego, $catatan_psm, $end_date, $confirm_fatpsm, $slafatpsm_date, $id);
                    $stmt_update->execute();

                    if ($stmt_update->affected_rows > 0) {
                        echo "Status berhasil diperbarui.";
                    } else {
                        echo "Gagal memperbarui status.";
                    }
                }
                // Jika confirm_fatpsm = NULL
                else if (is_null($confirm_fatpsm)) {
                    // Ambil SLA dari tabel master_sla untuk divisi FAT-Sewa
                    $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'FAT-Sewa'";
                    $result_sla_steqp = $conn->query($sql_sla_steqp);
                    if ($result_sla_steqp->num_rows > 0) {
                        $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                        $hari_sla_steqp = $row_sla_steqp['sla'];

                        // Cek waktu saat ini
                        $current_time = date('H:i');

                        // Jika submit setelah jam 12:00 siang, tambahkan 1 hari ke SLA
                        if ($current_time > '12:00') {
                            $hari_sla_steqp += 1;
                        }

                        // Hitung tanggal SLA berdasarkan end_date
                        $slafatpsm_date = date("Y-m-d", strtotime($end_date . ' + ' . $hari_sla_steqp . ' days'));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi FAT-Sewa.";
                        exit;
                    }
                    
                    $confirm_fatpsm = "In Process";
                    // Perbarui status di tabel draft
                    $sql_update = "UPDATE draft SET confirm_nego = ?, catatan_psm = ?, end_date = ?, confirm_fatpsm = ?, slafatpsm_date = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("sssssi", $confirm_nego, $catatan_psm, $end_date, $confirm_fatpsm, $slafatpsm_date, $id);
                    $stmt_update->execute();

                    if ($stmt_update->affected_rows > 0) {
                        echo "Status berhasil diperbarui.";
                    } else {
                        echo "Gagal memperbarui status.";
                    }
                } else {
                    echo "Tidak ada tindakan yang perlu diambil untuk confirm_fatpsm = $confirm_fatpsm.";
                    exit;
                }

            } else {
                echo "Error: ID tidak ditemukan di tabel draft.";
            }
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan, lamp_draf, lamp_signpsm FROM draft WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan, $lamp_draf, $lamp_signpsm);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();
            
            // Melanjutkan ke proses insert jika kode_lahan tidak kosong
            // $sql_insert = "INSERT INTO note_psm (kode_lahan, catatan_psmlegal, legal_date) VALUES (?, ?, ?)";
            // $stmt_insert = $conn->prepare($sql_insert);
            // $stmt_insert->bind_param("sss", $kode_lahan, $catatan_psm, $legal_date);
            // var_dump($kode_lahan);
            // $stmt_insert->execute();
            // if ($stmt_insert->execute()) {
            //     echo "Data berhasil dimasukkan.";
            // } else {
            //     echo "Gagal memasukkan data: " . $stmt_insert->error;
            // }

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
            $queryIR = "SELECT email FROM user WHERE level = 'TAF'";
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

                    // Lampirkan file dari folder cPanel
                    $file_dir = "../uploads/"; // Ganti dengan direktori file yang sesuai
                    if (!empty($lamp_draf)) {
                        $mail->addAttachment($file_dir . $lamp_draf);
                    }
                    if (!empty($lamp_signpsm)) {
                        $mail->addAttachment($file_dir . $lamp_signpsm);
                    }

                    // Email content
                    $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                    $mail->Body    = '
                                            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                            <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                                <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                                <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                    <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear TAF Team,</h2>
                                                    <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                    Your prompt attention to this matter is greatly appreciated.</p>
                                                    <p></p>
                                                    <p>Have a good day!</p>
                                                </div>
                                            </div>
                                        </div>';
                                        $mail->AltBody = 'Dear TAF Team,'
                                                    . 'We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
        } elseif ($confirm_nego == 'Pending') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
    
                // Query untuk memperbarui confirm_nego, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE draft SET confirm_nego = ?, catatan_psm = ?, end_date = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $confirm_nego, $catatan_psm, $end_date, $id);
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
            } elseif ($confirm_nego == 'In Revision') {
            
                // Ambil kode_lahan dari tabel re
                $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui status_vl, vl_date di tabel re dan memasukkan data ke dalam tabel hold_project
                $sql_update_re = "UPDATE draft SET confirm_nego = ?, catatan_psm = ?, end_date = ? WHERE id = ?";
                $stmt_update_re = $conn->prepare($sql_update_re);
                $stmt_update_re->bind_param("sssi", $confirm_nego, $catatan_psm, $end_date, $id);
                $stmt_update_re->execute();
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                if (!empty($catatan_psm)) {
                    $catatan_psm_array = explode(';', $catatan_psm);
                
                    $catatan_psm_filtered = array_filter($catatan_psm_array, function($value) {
                        return trim($value) !== ''; 
                    });
                
                    $catatan_psm_json = json_encode($catatan_psm_filtered);
                } else {
                    $catatan_psm_json = json_encode([]);
                }
                
                    $sql = "UPDATE draft SET confirm_nego = ?, catatan_psm = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $confirm_nego, $catatan_psm_json, $id);
            
                    $stmt->execute();
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
        header("Location: ../datatables-sign-psm-legal.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}