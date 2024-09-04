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

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $submit_wo = $_POST["submit_wo"];
    $wo_date = date("Y-m-d");

    $lamp_wo = "";

    if (isset($_FILES["lamp_wo"])) {
        $lamp_wo_paths = array();

        // Loop melalui setiap file
        foreach($_FILES['lamp_wo']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_wo']['tmp_name'][$key];
            $file_name = $_FILES['lamp_wo']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Coba pindahkan file yang diunggah ke direktori target
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_wo_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua path file menjadi string yang dipisahkan koma
        $lamp_wo = implode(",", $lamp_wo_paths);
    }

    // Inisialisasi variabel untuk status_spkwo dan sla_spkwo
    $status_spkwo = "";
    $sla_spkwo = "";

    if ($submit_wo == "Yes") {
        $status_spkwo = "In Process";

        // Ambil SLA dari tabel master_sla untuk divisi SPK
        $sql_sla_spk = "SELECT sla FROM master_sla WHERE divisi = 'SPK'";
        $result_sla_spk = $conn->query($sql_sla_spk);
        if ($result_sla_spk->num_rows > 0) {
            $row_sla_spk = $result_sla_spk->fetch_assoc();
            $hari_sla_spk = $row_sla_spk['sla'];

            // Hitung sla_spkwo berdasarkan wo_date + SLA dari divisi SPK
            $sla_spkwo = date("Y-m-d", strtotime($wo_date . ' + ' . $hari_sla_spk . ' days'));
        } else {
            echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
            exit;
        }

        // Update data di database
        $sql = "UPDATE sdg_desain 
                SET submit_wo = '$submit_wo', lamp_wo = '$lamp_wo', wo_date = '$wo_date', status_spkwo = '$status_spkwo', sla_spkwo = '$sla_spkwo' 
                WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
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

                    // Email content
                    $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                                    $mail->Body    = '
                                    <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                        <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                            <img src="cid:header_image" alt="Header Image" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                                            <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                                            <p>You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                                            <p>Thank you for your prompt attention to this matter.</p>
                                            <p></p>
                                            <p>Best regards,</p>
                                            <p>Resto - SOC</p>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Team,'
                                                . 'You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
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
        } else {
            // Update data di database
            $sql = "UPDATE sdg_desain 
                    SET submit_wo = '$submit_wo', lamp_wo = '$lamp_wo', wo_date = '$wo_date', status_spkwo = '$status_spkwo', sla_spkwo = '$sla_spkwo' 
                    WHERE id = '$id'";
        }
    
    header("Location: " . $base_url . "/datatables-submit-wo.php");
    exit();
    
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}

// Menutup koneksi database
$conn->close();
?>