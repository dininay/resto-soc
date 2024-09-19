<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_lahan = $_POST["kode_lahan"];
    $id = $_POST['id'];
    $gostore_date = $_POST['gostore_date'];
    $nama_lokasi = $_POST['nama_lahan'];
    $status_gostore = "Approve";
    $sql_sla = "SELECT sla FROM master_slacons WHERE divisi = 'RTO'";
    $result_sla = $conn->query($sql_sla);

    if ($result_sla->num_rows > 0) {
        $row_sla = $result_sla->fetch_assoc();
        $sla = $row_sla['sla'];
        
        // Hitung rto_date sebagai gostore_date - sla
        $rto_date = date('Y-m-d', strtotime($gostore_date . " - $sla days"));

    // Update data di database
    $sql = "UPDATE resto SET gostore_date='$gostore_date', approved_by = 'Last Updated by BoD', status_gostore = '$status_gostore', rto_date = '$rto_date' WHERE kode_lahan = '$kode_lahan'";
    $sql_land = "UPDATE land SET nama_lahan='$nama_lokasi' WHERE kode_lahan = '$kode_lahan'";

    if ($conn->query($sql) === TRUE) {
            if ($conn->query($sql_land) === TRUE) {
                
                $departments = [
                    'Legal',
                    'Real Estate',
                    'BoD',
                    'Negotiator',
                    'SDG-Design',
                    'SDG-QS',
                    'Procurement',
                    'SDG-Project',
                    'SDG-Equipment',
                    'HR',
                    'Academy',
                    'IT',
                    'Marketing',
                    'TAF'
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
                            $mail->Subject = 'Notification: New GO Date Scheduled by BoD Resto SOC Ticket';
                            $mail->Body = '
                                        <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                                        <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                                            <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                                            <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                                                <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Real Estate,</h2>
                                                <p>We would like to inform you that a new GO Date Scheduled by BoD Resto SOC Ticket has been created in the Resto Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
                                                Your prompt attention to this matter is greatly appreciated.</p>
                                                <p></p>
                                                <p>Have a good day!</p>
                                            </div>
                                        </div>
                                    </div>';
                                    $mail->AltBody = 'Dear Real Estate,'
                                                . 'We would like to inform you that a new GO Date Scheduled by BoD Resto SOC Ticket has been created in the Resto Process. This needs your attention, please log in to the SOC application to review the details at your earliest convenience.
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
                
                header("Location: " . $base_url . "/datatables-gostore.php");
            } else {
                echo "Error: " . $sql_land . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: SLA for RTO division not found.";
    }
}

// Menutup koneksi database
$conn->close();
?>
