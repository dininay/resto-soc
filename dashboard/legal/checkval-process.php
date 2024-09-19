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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_approvlegalvd"]) && isset($_POST["catatan_vd"])) {
    $id = $_POST["id"];
    $status_approvlegalvd = $_POST["status_approvlegalvd"];
    $catatan_vd = $_POST["catatan_vd"];
    
    // Periksa apakah file kronologi ada dalam $_FILES
    $sql_get_kode_lahan = "SELECT kode_lahan FROM dokumen_loacd WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_vdsign = "";
    if (isset($_FILES["lamp_vdsign"])) {
        $lamp_vdsign_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_vdsign']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_vdsign']['tmp_name'][$key];
            $file_name = $_FILES['lamp_vdsign']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_vdsign_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_vdsign = implode(",", $lamp_vdsign_paths);
    } else {
        $lamp_vdsign = null; // Set kronologi to null if no files were uploaded
    }
    $vdlegal_date = null;
    $sla_date = null;
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
        if ($status_approvlegalvd == 'Approve') {
            $vdlegal_date = date("Y-m-d H:i:s");
            // Hitung sla_permit berdasarkan divisi legal_permit
            // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = 'fl' (Field)
            $sql_sla_fl = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_fl'";
            $result_sla_fl = $conn->query($sql_sla_fl);
            if ($result_sla_fl->num_rows > 0) {
                $row_sla_fl = $result_sla_fl->fetch_assoc();
                $hari_sla_fl = $row_sla_fl['sla'];

                // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = 'tm' (Team)
                $sql_sla_tm = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_tm'";
                $result_sla_tm = $conn->query($sql_sla_tm);
                if ($result_sla_tm->num_rows > 0) {
                    $row_sla_tm = $result_sla_tm->fetch_assoc();
                    $hari_sla_tm = $row_sla_tm['sla'];

                    // Hitung SLA berdasarkan hari ini + jumlah hari SLA dari fl dan tm
                    $sla_fl = date("Y-m-d H:i:s", strtotime("+$hari_sla_fl days"));
                    $sla_tm = date("Y-m-d H:i:s", strtotime("+$hari_sla_tm days"));
                    $sla_flaca = date("Y-m-d H:i:s", strtotime("+$hari_sla_tm days"));
                    $sla_tmaca = date("Y-m-d H:i:s", strtotime("+$hari_sla_tm days"));
                    $status_fl = "In Process";
                    $status_tm = "In Process";
                    $status_tmaca = "In Process";
                    $status_flaca = "In Process";

                    // Ambil data dari dokumen_loacd berdasarkan ID yang diedit
                    $sql_select = "SELECT * FROM dokumen_loacd WHERE id = ?";
                    $stmt_select = $conn->prepare($sql_select);
                    $stmt_select->bind_param("i", $id);
                    $stmt_select->execute();
                    $result_select = $stmt_select->get_result();
                    $row = $result_select->fetch_assoc();

                    $kode_lahan = $row['kode_lahan'];

                    // Query untuk memperbarui status_approvlegalvd
                    $sql_insert = "INSERT INTO socdate_hr SET kode_lahan = ?, status_fl = ?, status_tm = ?, sla_tm = ?, sla_fl = ?";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sssss", $kode_lahan, $status_fl, $status_tm, $sla_tm, $sla_fl);
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Error updating status_approvlegalvd: " . $stmt_insert->error);
                    }

                    // Query untuk memperbarui status_approvlegalvd
                    $sql_insert = "INSERT INTO socdate_hraca SET kode_lahan = ?, status_flaca = ?, status_tmaca = ?, sla_tmaca = ?, sla_flaca = ?";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sssss", $kode_lahan, $status_flaca, $status_tmaca, $sla_tmaca, $sla_flaca);
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Error updating status_approvlegalvd: " . $stmt_insert->error);
                    }

                    // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
                    $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'Legal'";
                    $result_sla_steqp = $conn->query($sql_sla_steqp);
                    if ($result_sla_steqp->num_rows > 0) {
                        $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                        $hari_sla_steqp = $row_sla_steqp['sla'];
                        $sla_tafkode = date("Y-m-d", strtotime($vdlegal_date . ' + ' . $hari_sla_steqp . ' days'));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi FAT-Sewa.";
                        exit;
                    }
                    $status_tafkode = "In Process";
                    // Query untuk memperbarui status_approvlegalvd
                    $sql_update = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, vdlegal_date = ?, lamp_vdsign = ?, status_tafkode = ?, sla_tafkode = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ssssssi", $status_approvlegalvd, $catatan_vd, $vdlegal_date, $lamp_vdsign, $status_tafkode, $sla_tafkode, $id);
                    if (!$stmt_update->execute()) {
                        throw new Exception("Error updating status_approvlegalvd: " . $stmt_update->error);
                    }

                    // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
                    $sql_select_sla_negosiator = "SELECT sla FROM master_sla WHERE divisi = 'Table Sewa'";
                    $result_select_sla_negosiator = $conn->query($sql_select_sla_negosiator);
                    if (!$result_select_sla_negosiator) {
                        throw new Exception("Error retrieving SLA Negotiator: " . $conn->error);
                    }

                    if ($result_select_sla_negosiator->num_rows > 0) {
                        $row_sla_negosiator = $result_select_sla_negosiator->fetch_assoc();
                        $sla_negosiator_days = $row_sla_negosiator['sla'];

                        // Tambahkan jumlah hari SLA Negotiator ke vdlegal_date untuk mendapatkan sla_date
                        $sla_date = date('Y-m-d H:i:s', strtotime($vdlegal_date . ' + ' . $sla_negosiator_days . ' days'));

                        // Masukkan data ke tabel draft
                        $draft_legal = "In Process";
                        $sql_insert_draft = "INSERT INTO draft (kode_lahan, slalegal_date, draft_legal) VALUES (?, ?, ?)";
                        $stmt_insert_draft = $conn->prepare($sql_insert_draft);
                        $stmt_insert_draft->bind_param("sss", $kode_lahan, $sla_date, $draft_legal);
                        if (!$stmt_insert_draft->execute()) {
                            throw new Exception("Error inserting draft: " . $stmt_insert_draft->error);
                        }

                        // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
                        $sql_select_sla_psm = "SELECT sla FROM master_sla WHERE divisi = 'Final PSM'";
                        $result_select_sla_psm = $conn->query($sql_select_sla_psm);
                        if (!$result_select_sla_psm) {
                            throw new Exception("Error retrieving SLA Negotiator: " . $conn->error);
                        }

                        if ($result_select_sla_psm->num_rows > 0) {
                            $row_sla_psm = $result_select_sla_psm->fetch_assoc();
                            $sla_psm_days = $row_sla_psm['sla'];

                            // Tambahkan jumlah hari SLA Negotiator ke vdlegal_date untuk mendapatkan sla_date
                            $slapsm_date = date('Y-m-d H:i:s', strtotime($vdlegal_date . ' + ' . $sla_psm_days . ' days'));

                            // Masukkan juga perintah untuk mengupdate status_confirm_nego di tabel draft menjadi "In Process"
                            $sql_update_confirm_nego = "UPDATE draft SET confirm_nego = 'In Process', slapsm_date = ? WHERE kode_lahan = ?";
                            $stmt_update_confirm_nego = $conn->prepare($sql_update_confirm_nego);
                            $stmt_update_confirm_nego->bind_param("ss", $slapsm_date, $kode_lahan);
                            if (!$stmt_update_confirm_nego->execute()) {
                                throw new Exception("Error updating confirm_nego: " . $stmt_update_confirm_nego->error);
                            }
                        } else {
                            throw new Exception("Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.");
                        }
                    } else {
                        throw new Exception("Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.");
                    }
                    
                
                    $sql_get_kode_lahan = "SELECT kode_lahan FROM draft WHERE id = ?";
                    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                    $stmt_get_kode_lahan->bind_param("i", $id);
                    $stmt_get_kode_lahan->execute();
                    $stmt_get_kode_lahan->bind_result($kode_lahan);
                    $stmt_get_kode_lahan->fetch();
                    $stmt_get_kode_lahan->close();
                    
                    $status_gostore = "In Process";
                    $sql_update = "UPDATE resto SET status_gostore = ? WHERE kode_lahan = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ss", $status_gostore, $kode_lahan);
                    $stmt_update->execute();

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

            } else {
                $conn->rollback();
                echo "Error: Data SLA tidak ditemukan untuk divisi fl.";
            }
            } else {
                $conn->rollback();
                echo "Error: Data SLA tidak ditemukan untuk divisi tm.";
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
                    $mail->Subject = 'Notification: 1 New Active (Go Store Date) Resto SOC Ticket';
                                        $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear @Bu Arie,</h2>
                                                <p>We would like to inform you that a new Active (Go Store Date Scheduling) Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear @Bu Arie,'
                                                . 'We would like to inform you that a new Active (Go Store Date Scheduling) Resto SOC Ticket has been created. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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

                $departments = [
                    'TAF',
                    'HR',
                    'Academy',
                    'Legal'
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
        } elseif ($status_approvlegalvd == 'Pending') {
            $vdlegal_date = date("Y-m-d H:i:s");

            // Query untuk memperbarui status_approvlegalvd, catatan_vd, dan vdlegal_date di tabel dokumen_loacd
            $sql_update_re = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, vdlegal_date = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $vdlegal_date, $id);
            if (!$stmt_update_re->execute()) {
                throw new Exception("Error updating status_approvlegalvd: " . $stmt_update_re->error);
            }

            // Periksa apakah kode_lahan ada di tabel hold_project
            $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
            $stmt_check_hold = $conn->prepare($sql_check_hold);
            $stmt_check_hold->bind_param("s", $kode_lahan);
            $stmt_check_hold->execute();
            $stmt_check_hold->store_result();

            if ($stmt_check_hold->num_rows > 0) {
                // Jika kode_lahan ada di hold_project, update status_hold menjadi 'In Process'
                $status_hold = 'In Process';
                $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                $stmt_update_hold = $conn->prepare($sql_update_hold);
                $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                $stmt_update_hold->execute();
            }
        } elseif ($status_approvlegalvd == 'In Revision') {
            
            // Ambil kode_lahan dari tabel re
            $sql_get_kode_lahan = "SELECT kode_lahan FROM dokumen_loacd WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->bind_result($kode_lahan);
            $stmt_get_kode_lahan->fetch();
            $stmt_get_kode_lahan->free_result();

            // Query untuk memperbarui status_approvlegalvd, vllegal_date di tabel re dan memasukkan data ke dalam tabel hold_project
            $sql_update_re = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, vdlegal_date = ?, lamp_vdsign = ? WHERE id = ?";
            $stmt_update_re = $conn->prepare($sql_update_re);
            $stmt_update_re->bind_param("ssssi", $status_approvlegalvd, $catatan_vd, $vdlegal_date, $lamp_vdsign, $id);
            $stmt_update_re->execute();
            
            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui dan data ditahan.";
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
                    $mail->Subject = 'Notification: 1 New Active Revision VD from Legal Resto SOC Ticket';
                                        $mail->Body    = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                                <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Real Estate Team,</h2>
                                                <p>We would like to inform you that a new Active Revision VD from Legal Resto SOC Ticket has been created in the payment for electricity & water Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>';
                                        $mail->AltBody = 'Dear Real Estate Team,'
                                                    . 'We would like to inform you that a new Active Revision VD from Legal Resto SOC Ticket has been created in the payment for electricity & water Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
        } elseif ($status_approvlegalvd == 'Reject') {
            // Ambil kode lahan sebelum menghapus dari tabel re
            $sql = "SELECT kode_lahan FROM dokumen_loacd WHERE kode_lahan = ?";
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
    
                // Komit transaksi
                $conn->commit();
                echo "Data berhasil dihapus dan status berhasil diperbarui.";
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $conn->rollback();
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_approvlegalvd di tabel re
            $sql = "UPDATE dokumen_loacd SET status_approvlegalvd = ?, catatan_vd = ?, vdlegal_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $status_approvlegalvd, $catatan_vd, $vdlegal_date, $id);
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

        // Commit transaksi jika semua perintah berhasil dieksekusi
        $conn->commit();
        // Redirect ke halaman datatables-checkval-legal.php
        header("Location: ../datatables-checkval-legal.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika ada kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
