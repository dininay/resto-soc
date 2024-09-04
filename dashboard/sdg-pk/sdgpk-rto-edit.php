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
    $sumber_air = $_POST['sumber_air']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_sumberair = "";

    if(isset($_FILES["lamp_sumberair"])) {
        $lamp_sumberair_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_sumberair']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_sumberair']['tmp_name'][$key];
            $file_name = $_FILES['lamp_sumberair']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_sumberair_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_sumberair = implode(",", $lamp_sumberair_paths);
    }
    $kesesuaian_ujilab = $_POST['kesesuaian_ujilab']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_ujilab = null;

    // Periksa apakah file lamp_ujilab ada dalam $_FILES
    if (isset($_FILES["lamp_ujilab"])) {
        $lamp_ujilab_paths = array();
        foreach ($_FILES['lamp_ujilab']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ujilab']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ujilab_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_ujilab = implode(",", $lamp_ujilab_paths);
    }
    $filter_air = $_POST['filter_air']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_filterair = null;

    // Periksa apakah file lamp_filterair ada dalam $_FILES
    if (isset($_FILES["lamp_filterair"])) {
        $lamp_filterair_paths = array();
        foreach ($_FILES['lamp_filterair']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_filterair']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_filterair_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_filterair = implode(",", $lamp_filterair_paths);
    }
    $debit_airsumur = $_POST['debit_airsumur']; 
    $debit_airpdam = $_POST['debit_airpdam']; 
    $id_pdam = $_POST['id_pdam']; 
    $status_sdgsumber = "Proceed";
    $status_procurspkwofa = "In Process";
    $sla_spkwofa = "";
    $slaQuery = "SELECT SUM(sla) as total_days FROM master_slacons WHERE divisi = 'spk-procur'";
    $result = $conn->query($slaQuery);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_days = $row['total_days'];
        $current_date = new DateTime();
        $current_date->modify("+$total_days days");
        $sla_spkwofa = $current_date->format('Y-m-d');
    }
    $status_tafpay = "In Process";
    $sla_tafpay = "";
    $slaQuerys = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'payment-taf'";
    $results = $conn->query($slaQuerys);
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_tafpay = $current_date->format('Y-m-d');
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET sumber_air = '$sumber_air', lamp_sumberair = '$lamp_sumberair', kesesuaian_ujilab = '$kesesuaian_ujilab', lamp_ujilab = '$lamp_ujilab', filter_air = '$filter_air', lamp_filterair = '$lamp_filterair', debit_airsumur = '$debit_airsumur', debit_airpdam = '$debit_airpdam', id_pdam = '$id_pdam', status_sdgsumber = '$status_sdgsumber', status_procurspkwofa = '$status_procurspkwofa', status_tafpay = '$status_tafpay', sla_spkwofa = '$sla_spkwofa', sla_tafpay = '$sla_tafpay' WHERE id = '$id'";
    // var_dump($sql);
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
        header("Location:  " . $base_url . "/datatables-sdgpk-rto.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
