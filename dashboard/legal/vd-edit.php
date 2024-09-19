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
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    // $kode_store = $_POST['kode_store'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $sql_get_kode_lahan = "SELECT kode_lahan FROM dokumen_loacd WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_vd = "";
    if (isset($_FILES["lamp_vd"])) {
        $lamp_vd_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_vd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_vd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_vd']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_vd_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_vd = implode(",", $lamp_vd_paths);
    }
        $end_date = date("Y-m-d H:i:s");
        // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
        $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'VD'";
        $result_sla_steqp = $conn->query($sql_sla_steqp);
        if ($result_sla_steqp->num_rows > 0) {
            $row_sla_steqp = $result_sla_steqp->fetch_assoc();
            $hari_sla_steqp = $row_sla_steqp['sla'];
            $slavdlegal_date = date("Y-m-d", strtotime($end_date . ' + ' . $hari_sla_steqp . ' days'));
        } else {
            $conn->rollback();
            echo "Error: Data SLA tidak ditemukan untuk divisi VD.";
            exit;
        }
        $status_approvlegalvd = "In Process";

        // Update data di database
        $sql = "UPDATE dokumen_loacd SET lamp_vd = '$lamp_vd', end_date = '$end_date', slavdlegal_date = '$slavdlegal_date', status_approvlegalvd = '$status_approvlegalvd' WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            $queryIR = "SELECT email FROM user WHERE level = 'Legal'";
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
            header("Location: " . $base_url . "/datatables-validasi-data.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
    }

// Menutup koneksi database
$conn->close();
?>
