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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["status_approvowner"]) && isset($_POST["catatan_owner"])) {
    $kode_lahan = $_POST["kode_lahan"];
    var_dump($kode_lahan);
    $status_approvowner = $_POST["status_approvowner"];
    $catatan_owner = $_POST["catatan_owner"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;

    // Inisialisasi variabel untuk status_approvlegal
    $status_approvnego = null;
    $start_date = null;
    
    // Jika status_approvowner diubah menjadi Approve, ubah status_approvlegal menjadi In Process
    if ($status_approvowner == 'Approve') {
        $status_approvnego = 'In Process';
        $status_vl = 'In Process';
        $start_date = date("Y-m-d H:i:s");

        // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = VL
            $sql_select_sla_vl = "SELECT sla FROM master_sla WHERE divisi = 'Negosiator'";
            $result_select_sla_vl = $conn->query($sql_select_sla_vl);

            if ($result_select_sla_vl && $result_select_sla_vl->num_rows > 0) {
                $row_sla_vl = $result_select_sla_vl->fetch_assoc();
                $sla_vl_days = $row_sla_vl['sla'];

                // Tambahkan jumlah hari SLA VL ke end_date untuk mendapatkan vl_date
                $slanego_date = date('Y-m-d H:i:s', strtotime($start_date . ' + ' . $sla_vl_days . ' days'));

                // // Query untuk memperbarui status_approvlegal, status_approvnego, status_vl, end_date, dan vl_date
                // $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ?, status_approvnego = ?, start_date = ? WHERE kode_lahan = ?";
                // $stmt = $conn->prepare($sql);
                // $stmt->bind_param("sssss", $status_approvowner, $catatan_owner, $status_approvnego, $start_date, $kode_lahan);
                // $stmt->execute();
                // $stmt->close();
            } else {
                echo "Error: Tkode_lahanak dapat mengambil data SLA VL dari tabel master_sla.";
            }

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Query untuk memperbarui status_approvowner, catatan_owner, status_approvlegal, start_date, slalegal_date, status_vl, dan slavl_date
            $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ?, status_approvnego = ?, start_date = ?, slanego_date = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $status_approvowner, $catatan_owner, $status_approvnego, $start_date, $slanego_date, $kode_lahan);
            $stmt->execute();

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
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
                $queryIR = "SELECT email FROM user WHERE level = 'Negotiator'";
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
                    $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                    $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Negotiator @Bu Lily,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created in the Approval Land Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Negotiator @Bu Lily,'
                                                . 'We would like to inform you that a new Active Resto SOC Ticket has been created in the Approval Land Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
                    echo "No email found for the selected resto or RE users.";
                }
        // try {
        //     // Pengaturan server SMTP
        //     // $mail->SMTPKeepAlive = true;
        //     $mail->isSMTP();
        //     $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
        //     $mail->SMTPAuth = true;
        //     $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
        //     $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
        //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //     $mail->Port = 2525;
            
        //     // Pengaturan pengirim dan penerima
        //     $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');

        //     // Query untuk mendapatkan email pengguna dengan level "Real Estate"
        //     $sql = "SELECT email FROM user WHERE level IN ('Negotiator')";
        //     $result = $conn->query($sql);

        //     if ($result->num_rows > 0) {
        //         while($row = $result->fetch_assoc()) {
        //             $mail->addAddress($row['email']); // Tambahkan setiap penerima email

        //             // Konten email
        //             $mail->isHTML(true);
        //             $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
        //             $mail->Body    = '
        //             <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
        //                 <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                            // <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
        //                     <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
        //                     <p>You have 1 new active ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
        //                     <p>Thank you for your prompt attention to this matter.</p>
        //                     <p></p>
        //                     <p>Best regards,</p>
        //                     <p>Resto - SOC</p>
        //                 </div>
        //             </div>';
        //             $mail->AltBody = 'Dear Team,'
        //                            . 'You have 1 new active ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
        //                            . 'Thank you for your prompt attention to this matter.'
        //                            . 'Best regards,'
        //                            . 'Resto - SOC';

        //             // Kirim email
        //             $mail->send();
        //             $mail->clearAddresses(); // Hapus semua penerima sebelum loop berikutnya
        //         }
        //     }
        //     $mail->smtpClose(); 
        //     // Redirect setelah email dikirim
        //     header("Location: " . $base_url . "/datatables-land-sourcing.php");
        //     exit();

        // } catch (Exception $e) {
        //     echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
        // }
    } elseif ($status_approvowner == 'Reject') {
        // Ambil kode lahan sebelum menghapus dari tabel re
        $sql = "SELECT kode_lahan FROM re WHERE kode_lahan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $kode_lahan);
        $stmt->execute();
        $stmt->bind_result($kode_lahan);
        $stmt->fetch();
        $stmt->close();

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Hapus data dari tabel re berdasarkan kode_lahan
            $sql = "DELETE FROM re WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $kode_lahan);
            $stmt->execute();

            // Perbarui status_land menjadi Reject pada tabel land berdasarkan kode lahan
            $sql = "UPDATE land SET status_land = 'Reject' WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $kode_lahan);
            $stmt->execute();

            $queryIR = "SELECT email FROM user WHERE level IN ('Real Estate')";
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
                $mail->Subject = 'Notification: 1 New Reject Land by BoD Resto SOC Ticket';
                                    $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Real Estate,</h2>
                                                <p>We would like to inform you that a new Reject Land by BoD Resto SOC Ticket has been created in the Approval Land Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Real Estate,'
                                                . 'We would like to inform you that a new Reject Land by BoD Resto SOC Ticket has been created in the Approval Land Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
            echo "Data berhasil dihapus dan status berhasil diperbarui.";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
        
    } elseif ($status_approvowner == 'Pending') {
        // Mulai transaksi
        $conn->begin_transaction();

        try {
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
            // Query untuk memperbarui status_approvowner dan catatan_owner di tabel re
            $sql = "UPDATE re SET status_approvowner = ?, catatan_owner = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $status_approvowner, $catatan_owner, $kode_lahan);
            $stmt->execute();

            var_dump($kode_lahan);

            $status_hold = "In Process";
            $due_date = date("Y-m-d H:i:s");

            // Query untuk memasukkan data ke dalam tabel hold_project
            $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_hold = $conn->prepare($sql_hold);
            $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
            $stmt_hold->execute();

            var_dump($kode_lahan);
            var_dump($status_approvowner);
            var_dump($kronologi);
            
            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui dan data ditahan.";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_approvowner
        // $sql = "UPDATE re SET status_approvowner = ? WHERE kode_lahan = ?";
        // $stmt = $conn->prepare($sql);
        // $stmt->bind_param("ss", $status_approvowner, $kode_lahan);

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
    // Redirect ke halaman datatables-approval-owner.php
    header("Location: ../datatables-approval-owner.php");
    exit;
}
?>