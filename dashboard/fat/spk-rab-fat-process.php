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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])  && isset($_POST["status_spkfat"]) && isset($_POST["catatan_spkfat"]) && is_array($_POST['catatan_spkfat'])) {
    $id = $_POST["id"];
    $status_spkfat = $_POST["status_spkfat"];
    $catatan_spkfat = implode(';', array_map('htmlspecialchars', $_POST['catatan_spkfat']));
    $taf_date = date("Y-m-d H:i:s");
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;
    $spkfat_date = date("Y-m-d");

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
        // Query untuk memperbarui status_spkfat berdasarkan id
        $sql_update = "UPDATE procurement SET status_spkfat = ?, catatan_spkfat = ?, spkfat_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $spkfat_date = date("Y-m-d");
        $stmt_update->bind_param("sssi", $status_spkfat, $catatan_spkfat, $spkfat_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_spkfat diubah menjadi 'Approve'
            if ($status_spkfat == 'Done Review') {
                $spkfat_date = date("Y-m-d");
                $status_approvprocurement = "Approve";
                
                    $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                    $stmt_get_kode_lahan->bind_param("i", $id);
                    $stmt_get_kode_lahan->execute();
                    $stmt_get_kode_lahan->bind_result($kode_lahan);
                    $stmt_get_kode_lahan->fetch();
                    $stmt_get_kode_lahan->free_result();

                    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
                    $lamp_spkconsdone = "";
                    if (isset($_FILES["lamp_spkconsdone"])) {
                        $lamp_spkconsdone_paths = array();

                        // Path ke direktori "uploads"
                        $target_dir = "../uploads/" . $kode_lahan . "/";

                        // Cek apakah folder dengan nama kode_lahan sudah ada
                        if (!is_dir($target_dir)) {
                            // Jika folder belum ada, buat folder baru
                            mkdir($target_dir, 0777, true);
                        }

                        // Loop untuk menangani setiap file yang diunggah
                        foreach ($_FILES['lamp_spkconsdone']['name'] as $key => $filename) {
                            $file_tmp = $_FILES['lamp_spkconsdone']['tmp_name'][$key];
                            $file_name = $_FILES['lamp_spkconsdone']['name'][$key];
                            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

                            // Pindahkan file yang diunggah ke target folder
                            if (move_uploaded_file($file_tmp, $target_file)) {
                                $lamp_spkconsdone_paths[] = $file_name; // Simpan nama file
                            } else {
                                echo "Gagal mengunggah file " . $file_name . "<br>";
                            }
                        }

                        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
                        $lamp_spkconsdone = implode(",", $lamp_spkconsdone_paths);
                    }

                    $sql_sla = "SELECT sla FROM master_sla WHERE divisi = 'SPK'";
                    $result_sla = $conn->query($sql_sla);
                    if ($result_sla->num_rows > 0) {
                        $row_sla = $result_sla->fetch_assoc();
                        $hari_sla = $row_sla['sla'];
                        echo "SLA days: $hari_sla<br>";

                        // Tentukan sla_spk
                        $sla_kom = date("Y-m-d", strtotime("$end_date + $hari_sla days"));
                        echo "SLA SPK: $sla_spk<br>";
                    } else {
                        // Rollback transaksi jika data SLA tidak ditemukan
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                    }

                    $sql_slafinal = "SELECT sla FROM master_sla WHERE divisi = 'SPK-Tender'";
                    $result_slafinal = $conn->query($sql_slafinal);
                    if ($result_slafinal->num_rows > 0) {
                        $row_slafinal = $result_slafinal->fetch_assoc();
                        $hari_slafinal = $row_slafinal['sla'];
                        echo "SLA days: $hari_slafinal<br>";

                        // Tentukan sla_spk
                        $sla_finalspk = date("Y-m-d", strtotime("$spkfat_date + $hari_slafinal days"));
                        echo "SLA SPK: $sla_finalspk<br>";
                    } else {
                        // Rollback transaksi jika data SLA tidak ditemukan
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                    }
                    $status_finalspk = "In Process";
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_spkfat = ?, catatan_spkfat = ?, spkfat_date = ?, status_approvprocurement = ?, lamp_spkconsdone = ?, status_finalspk = ?, sla_finalspk = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssssssi", $status_spkfat, $catatan_spkfat, $spkfat_date, $status_approvprocurement, $lamp_spkconsdone, $status_finalspk, $sla_finalspk, $id);
                $stmt_update_pending->execute();

                $status_kom = "In Process";
                $sql_update_pending = "UPDATE resto SET status_kom = ?, sla_kom = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssi", $status_kom, $sla_kom, $id);
                $stmt_update_pending->execute();
            
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
                    // Melanjutkan ke proses insert jika kode_lahan tidak kosong
                    $sql_insert = "INSERT INTO note_spk (kode_lahan, catatan_spkfat, taf_date) VALUES (?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sss", $kode_lahan, $catatan_spkfat, $taf_date);
                    $stmt_insert->execute();
                
                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("i", $id);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("si", $status_hold, $id);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
                     
                $queryIR = "SELECT email FROM user WHERE level IN ('Procurement')";
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
                                                    <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Procurement Team,</h2>
                                                    <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                    Your prompt attention to this matter is greatly appreciated.</p>
                                                    <p></p>
                                                    <p>Have a good day!</p>
                                                </div>
                                            </div>
                                        </div>';
                                        $mail->AltBody = 'Dear Procurement Team,'
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
                 
                $queryIR = "SELECT email FROM user WHERE level IN ('SDG-QS')";
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
                        $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                                        $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                                <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear SDG-QS Team,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created in the Review SPK Construction by TAF Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>';
                                        $mail->AltBody = 'Dear SDG-QS Team,'
                                                    . 'We would like to inform you that a new Active Resto SOC Ticket has been created in the Review SPK Construction by TAF Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
            } elseif ($status_spkfat == 'Pending') {
                // Ambil kode_lahan dari tabel procurement
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel procurement
                $sql_update_pending = "UPDATE procurement SET status_spkfat = ?, catatan_spkfat = ?, spkfat_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $status_spkfat, $catatan_spkfat, $spkfat_date, $id);
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
            } elseif ($status_spkfat == 'In Revision') {
                $spkfat_date = date("Y-m-d H:i:s");
                // Ambil kode_lahan dari tabel sdg_rab
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_pending = "UPDATE procurement SET status_spkfat = ?, catatan_spkfat = ?, status_approvprocurement = ?, spkfat_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $status_approvprocurement = "In Revision";
                $stmt_update_pending->bind_param("ssssi", $status_spkfat, $catatan_spkfat, $status_approvprocurement, $spkfat_date, $id);
                $stmt_update_pending->execute();
            
                // Periksa apakah kode_lahan ada di tabel hold_project
                
                $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
                    // Melanjutkan ke proses insert jika kode_lahan tidak kosong
                    $sql_insert = "INSERT INTO note_spk (kode_lahan, catatan_spkfat, taf_date) VALUES (?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sss", $kode_lahan, $catatan_spkfat, $taf_date);
                    var_dump($kode_lahan);
                    $stmt_insert->execute();
                    if ($stmt_insert->execute()) {
                        echo "Data berhasil dimasukkan.";
                    } else {
                        echo "Gagal memasukkan data: " . $stmt_insert->error;
                    }
                  
                    $queryIR = "SELECT email FROM user WHERE level IN ('Procurement')";
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
                            $mail->Subject = 'Notification: 1 New Active Revision By TAF Resto SOC Ticket';
                                            $mail->Body    = '
                                                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                                <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                                    <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                                    <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                        <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Procurement Team,</h2>
                                                        <p>We would like to inform you that a new Active Revision By TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                        Your prompt attention to this matter is greatly appreciated.</p>
                                                        <p></p>
                                                        <p>Have a good day!</p>
                                                    </div>
                                                </div>
                                            </div>';
                                            $mail->AltBody = 'Dear Procurement Team,'
                                                        . 'We would like to inform you that a new Active Revision By TAF Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
                echo "Status berhasil diperbarui dan data ditahan.";
            } else {
                
                    // // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
                    // $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'Review PSM TAF'";
                    // $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
                    // if ($result_sla_stkonstruksi->num_rows > 0) {
                    //     $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                    //     $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];
                    //     $sla_fat = date("Y-m-d", strtotime($spkfat_date . ' + ' . $hari_sla_stkonstruksi . ' days'));
                    // } else {
                    //     $conn->rollback();
                    //     echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                    //     exit;
                    // }
                    // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_spkfat
                    $sql_update_other = "UPDATE procurement SET status_spkfat = ?, catatan_spkfat = ?, spkfat_date = ? WHERE id = ?";
                    $stmt_update_other = $conn->prepare($sql_update_other);
                    $stmt_update_other->bind_param("sssi", $status_spkfat, $catatan_spkfat, $spkfat_date, $id);
    

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
        header("Location: ../datatables-review-rab-from-sdg.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>