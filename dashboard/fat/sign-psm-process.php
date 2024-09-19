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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// Koneksi ke database
include "../../koneksi.php";

// Set timezone ke Jakarta
date_default_timezone_set('Asia/Jakarta');

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["kode_lahan"]) && isset($_POST["confirm_fatpsm"])) {
    $id = $_POST["id"];
    $kode_lahan = $_POST["kode_lahan"];
    $confirm_fatpsm = $_POST["confirm_fatpsm"];
    
    // $catatan_psmfat_array = isset($_POST["catatan_psmfat"]) ? $_POST["catatan_psmfat"] : [];
    // print_r($catatan_psmfat_array);
    // $catatan_psmfat = json_encode($catatan_psmfat_array);
    // echo $catatan_psmfat;
    
    // Pastikan $_POST['catatan_psmfat'] adalah array
    // $catatan_psmfat = implode(';', array_map('htmlspecialchars', $_POST['catatan_psmfat']));

    $fat_date = date("Y-m-d H:i:s");
    $psmfat_date = null;
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
        // Ambil psmfat_date dan start_konstruksi dari tabel draft
        $sql_get_dates = "SELECT psmfat_date FROM draft WHERE id = ?";
        $stmt_get_dates = $conn->prepare($sql_get_dates);
        $stmt_get_dates->bind_param("i", $id);
        $stmt_get_dates->execute();
        $stmt_get_dates->bind_result($existing_psmfat_date);
        $stmt_get_dates->fetch();
        $stmt_get_dates->close();

        $sql_get_resto = "SELECT start_konstruksi FROM resto WHERE kode_lahan = ?";
        $stmt_get_resto = $conn->prepare($sql_get_resto);
        $stmt_get_resto->bind_param("s", $kode_lahan);
        $stmt_get_resto->execute();
        $stmt_get_resto->bind_result($existing_start_konstruksi);
        $stmt_get_resto->fetch();
        $stmt_get_resto->close();

        // Tentukan nilai start_konstruksi
        if ($confirm_fatpsm == 'Approve') {
            $psmfat_date = date("Y-m-d H:i:s");
            $new_start_konstruksi = $psmfat_date;

            // Periksa dan sesuaikan start_konstruksi
            if ($existing_psmfat_date > $existing_start_konstruksi) {
                $new_start_konstruksi = date('Y-m-d H:i:s', strtotime($psmfat_date . ' +1 day'));
            } else {
                $new_start_konstruksi = $existing_start_konstruksi;
            }

            // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
            $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'Sign'";
            $result_sla_steqp = $conn->query($sql_sla_steqp);
            if ($result_sla_steqp->num_rows > 0) {
                $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                $hari_sla_steqp = $row_sla_steqp['sla'];
                $slabod_date = date("Y-m-d", strtotime($psmfat_date . ' + ' . $hari_sla_steqp . ' days'));
            } else {
                $conn->rollback();
                echo "Error: Data SLA tidak ditemukan untuk divisi Table-Sewa.";
                exit;
            }
            $confirm_bod = "In Process";
            $confirm_nego = "Approve";
            
                $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();
                
            // Periksa apakah ada lampiran
            $catatan_psmfat = "";
            if (isset($_FILES["catatan_psmfat"])) {
                $catatan_psmfat_paths = array();

                // Loop setiap file yang diunggah
                foreach ($_FILES['catatan_psmfat']['name'] as $key => $filename) {
                    $file_tmp = $_FILES['catatan_psmfat']['tmp_name'][$key];
                    $file_name = $_FILES['catatan_psmfat']['name'][$key];
                    $target_dir = "../uploads/";
                    $target_file = $target_dir . basename($file_name);

                    // Cek apakah file berhasil diupload
                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $catatan_psmfat_paths[] = $file_name;
                        echo "File berhasil diunggah: $target_file<br>";

                        // Cek apakah file Excel dan impor data
                        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                        if ($file_extension == 'xlsx' || $file_extension == 'xls') {
                            $spreadsheet = IOFactory::load($target_file);
                            $sheet = $spreadsheet->getActiveSheet();
                            $highestRow = $sheet->getHighestRow();

                            // Loop setiap baris di file Excel
                            for ($row = 2; $row <= $highestRow; $row++) {
                                $pasal_page = $sheet->getCell('A' . $row)->getValue();
                                $catatan_psmfat = $sheet->getCell('B' . $row)->getValue();
                                $remarks = $sheet->getCell('C' . $row)->getValue();

                                // Insert data ke database
                                $sql_insert = "INSERT INTO note_psm (kode_lahan, pasal_page, catatan_psmfat, remarks, fat_date) VALUES (?, ?, ?, ?, ?)";
                                $stmt_insert = $conn->prepare($sql_insert);
                                $stmt_insert->bind_param("sssss", $kode_lahan, $pasal_page, $catatan_psmfat, $remarks, $fat_date);

                                if ($stmt_insert->execute()) {
                                    echo "Data berhasil disimpan untuk baris $row<br>";
                                } else {
                                    echo "Gagal menyimpan data pada baris $row: " . $stmt_insert->error . "<br>";
                                }
                            }
                        }
                    } else {
                        echo "Gagal mengunggah file " . htmlspecialchars($file_name) . "<br>";
                    }
                }

                // Gabungkan semua file lampiran ke satu string
                $catatan_psmfat = implode(",", $catatan_psmfat_paths);
            }
            
                // Query untuk memperbarui status confirm_fatpsm di tabel draft
                $sql_update = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ?, psmfat_date = ?, confirm_bod = ?, slabod_date = ?, confirm_nego = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssssssi", $confirm_fatpsm, $catatan_psmfat, $psmfat_date, $confirm_bod, $slabod_date, $confirm_nego, $id);
                $stmt_update->execute();

            // Query untuk memperbarui status confirm_fatpsm di tabel draft
            $sql_resto = "UPDATE resto SET start_konstruksi = ? WHERE kode_lahan = ?";
            $stmt_resto = $conn->prepare($sql_resto);
            $stmt_resto->bind_param("ss", $new_start_konstruksi, $id);
            $stmt_resto->execute();

            if ($stmt_resto->affected_rows > 0) {
                echo "Status berhasil diperbarui.";
            } else {
                echo "Gagal memperbarui status.";
            }

            // Ambil kode_lahan dari tabel draft
            $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
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

            $queryIR = "SELECT email FROM user WHERE level IN ('Legal')";
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
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Legal Team,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Legal Team,'
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
            
            $queryIR = "SELECT email FROM user WHERE level IN ('BoD')";
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
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Bu @Arie,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Bu @Arie,'
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
        } elseif ($confirm_fatpsm == 'Pending') {
            // Ambil kode_lahan dari tabel draft
            $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->close();

            // Query untuk memperbarui confirm_fatpsm, psmfat_date di tabel draft dan memasukkan data ke dalam tabel hold_project
            $sql_update_re = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ?, psmfat_date = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("sssi", $confirm_fatpsm, $catatan_psmfat, $psmfat_date, $id);
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
        } elseif ($confirm_fatpsm == 'In Revision') {
            $psmfat_date = date("Y-m-d H:i:s");
            // Ambil kode_lahan dari tabel draft
            $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Cek apakah file diunggah
            if (isset($_FILES["catatan_psmfat"])) {
                $file_tmp = $_FILES['catatan_psmfat']['tmp_name'];
                $file_name = $_FILES['catatan_psmfat']['name'];
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($file_name);

                // Cek apakah file berhasil diunggah
                if (move_uploaded_file($file_tmp, $target_file)) {
                    echo "File berhasil diunggah: $target_file<br>";

                    // Cek apakah file adalah Excel
                    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                    if ($file_extension == 'xlsx' || $file_extension == 'xls') {
                        // Load spreadsheet file
                        $spreadsheet = IOFactory::load($target_file);
                        $sheet = $spreadsheet->getActiveSheet();
                        $highestRow = $sheet->getHighestRow();

                        // Loop setiap baris mulai dari baris ke-2
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $pasal_page = $sheet->getCell('A' . $row)->getValue();
                            $catatan_psmfatisi = $sheet->getCell('B' . $row)->getValue();
                            $remarks = $sheet->getCell('C' . $row)->getValue(); // Jika ada kolom tambahan

                            // Pastikan kolom tidak kosong sebelum insert
                            if (!empty($pasal_page) && !empty($catatan_psmfatisi)) {
                                // Insert data ke database
                                $sql_insert = "INSERT INTO note_psm (kode_lahan, pasal_page, catatan_psmfat, remarks) VALUES (?, ?, ?, ?)";
                                $stmt_insert = $conn->prepare($sql_insert);
                                $stmt_insert->bind_param("ssss", $kode_lahan, $pasal_page, $catatan_psmfatisi, $remarks);

                                // Cek apakah insert berhasil
                                if ($stmt_insert->execute()) {
                                    echo "Data berhasil disimpan untuk baris $row<br>";
                                } else {
                                    echo "Gagal menyimpan data pada baris $row: " . $stmt_insert->error . "<br>";
                                }
                            } else {
                                echo "Baris $row tidak memiliki data yang cukup untuk dimasukkan.<br>";
                            }
                        }
                    } else {
                        echo "File bukan file Excel.<br>";
                    }
                } else {
                    echo "Gagal mengunggah file " . htmlspecialchars($file_name) . "<br>";
                }
            }

            // Query untuk memperbarui submit_legal dan catatan_owner di tabel draft
            $sql_update_pending = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ?, confirm_nego = ?, psmfat_date = ? WHERE id = ?";
            $stmt_update_pending = $conn->prepare($sql_update_pending);
            $confirm_nego = "In Revision";
            $stmt_update_pending->bind_param("ssssi", $confirm_fatpsm, $catatan_psmfat, $confirm_nego, $psmfat_date, $id);
            $stmt_update_pending->execute();
                
            $queryIR = "SELECT email FROM user WHERE level IN ('Legal')";
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
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Legal Team,</h2>
                                                <p>We would like to inform you that a new Active Resto SOC Ticket has been created in the Revition Draft Table Sewa & PSM By TAF Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Legal Team,'
                                                . 'We would like to inform you that a new Active Resto SOC Ticket has been created in the Revition Draft Table Sewa & PSM By TAF Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
            
            // $catatan_psmfat_array = json_encode(explode(';', $catatan_psmfat));
            // array_pop($catatan_psmfat_array);
            // $catatan_psmfat_json = json_encode($catatan_psmfat_array);
            // $catatan_psmfat_json = json_encode($catatan_psmfat_string);
           if (!empty($catatan_psmfat)) {
                $catatan_psmfat_array = explode(';', $catatan_psmfat);
            
                $catatan_psmfat_filtered = array_filter($catatan_psmfat_array, function($value) {
                    return trim($value) !== ''; 
                });
            
                $catatan_psmfat_json = json_encode($catatan_psmfat_filtered);
            } else {
                $catatan_psmfat_json = json_encode([]);
            }
            
                $sql = "UPDATE draft SET confirm_fatpsm = ?, catatan_psmfat = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $confirm_fatpsm, $catatan_psmfat_json, $id);
        
                $stmt->execute();
                 if ($stmt->affected_rows > 0) {
                echo "<script>
                        alert('Status berhasil diperbarui.');
                        window.location.href = window.location.href;
                     </script>";
            } else {
                echo "Error: Gagal memperbarui status. Tidak ada perubahan dilakukan.";
            }
            
            // Check if update was successful
           
        }

        // Komit transaksi
        $conn->commit();
        // Redirect ke halaman datatables-sign-psm-fat.php
        // header("Location: ../datatables-sign-psm-fat.php");
        // exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
