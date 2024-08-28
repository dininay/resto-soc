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
                
                try {
                    // Pengaturan server SMTP
                    $mail->isSMTP();
                    $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
                    $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 2525;
                    
                    // Pengaturan pengirim dan penerima
                    $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');
            
                    // Query untuk mendapatkan email pengguna dengan level "Real Estate"
                    $sql = "SELECT email FROM user WHERE level IN ('SDG-Project')";
                    $result = $conn->query($sql);
            
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $email = $row['email'];
                    
                            // Validasi format email sebelum menambahkannya sebagai penerima
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $mail->addAddress($email); // Tambahkan setiap penerima email
                                
                                // Konten email
                                $mail->isHTML(true);
                                $mail->Subject = 'Notification: 1 New Go Store Date Scheduled by BoD Resto SOC Ticket';
                                $mail->Body    = '
                                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                    <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                        <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                                        <p>You have 1 New Go Store Date Scheduled by BoD Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                                        <p>Thank you for your prompt attention to this matter.</p>
                                        <p></p>
                                        <p>Best regards,</p>
                                        <p>Resto - SOC</p>
                                    </div>
                                </div>';
                                $mail->AltBody = 'Dear Team,'
                                               . 'You have 1 New Go Store Date Scheduled by BoD Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
                                               . 'Thank you for your prompt attention to this matter.'
                                               . 'Best regards,'
                                               . 'Resto - SOC';
                    
                                // Kirim email
                                $mail->send();
                                $mail->clearAddresses(); // Hapus semua penerima sebelum loop berikutnya
                            } else {
                                echo "Invalid email format: " . $email;
                            }
                            }
                        } else {
                            echo "No emails found.";
                        }
            
                    } catch (Exception $e) {
                        echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
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
