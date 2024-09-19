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
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $sumber_listrik = $_POST['sumber_listrik']; 

    // Definisikan direktori unggahan berdasarkan kode_lahan
    $target_dir = "../uploads/" . $kode_lahan . "/";
    // Cek apakah folder dengan nama kode_lahan sudah ada
    if (!is_dir($target_dir)) {
        // Jika folder belum ada, buat folder baru
        mkdir($target_dir, 0777, true);
    }
    $maxFileSize = 5 * 1024 * 1024; // Maksimal ukuran file 5MB
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Tipe file yang diizinkan

    // Fungsi untuk menangani unggahan file
    function upload_files($file_key, $target_dir, $allowedTypes, $maxFileSize) {
        $uploaded_paths = array();
        if (isset($_FILES[$file_key])) {
            foreach ($_FILES[$file_key]['name'] as $key => $filename) {
                $file_tmp = $_FILES[$file_key]['tmp_name'][$key];
                $file_size = $_FILES[$file_key]['size'][$key];
                $file_type = $_FILES[$file_key]['type'][$key];
                $target_file = $target_dir . basename($filename);
                
                // Validasi ukuran dan tipe file
                if ($file_size <= $maxFileSize && in_array($file_type, $allowedTypes)) {
                    if ($_FILES[$file_key]['error'][$key] === UPLOAD_ERR_OK) {
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $uploaded_paths[] = $filename;
                        } else {
                            echo "Gagal memindahkan file " . $filename . "<br>";
                        }
                    } else {
                        echo "Error mengunggah file " . $filename . ": " . $_FILES[$file_key]['error'][$key] . "<br>";
                    }
                } else {
                    echo "File " . $filename . " terlalu besar atau tipe file tidak diperbolehkan.<br>";
                }
            }
        }
        return implode(",", $uploaded_paths); // Mengembalikan string nama file yang dipisahkan dengan koma
    }

    // Proses unggah file
    $form_pengajuanlistrik = upload_files("form_pengajuanlistrik", $target_dir, $allowedTypes, $maxFileSize);
    $lamp_slo = upload_files("lamp_slo", $target_dir, $allowedTypes, $maxFileSize);
    $lamp_nidi = upload_files("lamp_nidi", $target_dir, $allowedTypes, $maxFileSize);
    $form_wolistrik = upload_files("form_wolistrik", $target_dir, $allowedTypes, $maxFileSize);

    // Ambil nilai dari form lainnya
    $hasil_va = isset($_POST["hasil_va"]) ? $_POST["hasil_va"] : null;
    $pasanglistrik_date = isset($_POST["pasanglistrik_date"]) ? $_POST["pasanglistrik_date"] : null;
    $id_pln = isset($_POST["id_pln"]) ? $_POST["id_pln"] : null;
    $id_regpln = isset($_POST["id_regpln"]) ? $_POST["id_regpln"] : null;

    // Status default
    $status_sdglistrik = "Proceed";
    $status_tafpaylistrik = "In Process";
    $sla_tafpaylistrik = "";

    // Hitung SLA berdasarkan query
    $slaQuery = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'payment-taf'";
    $results = $conn->query($slaQuery);
    if ($results && $results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_tafpaylistrik = $current_date->format('Y-m-d');
    }

    // Query berdasarkan sumber listrik
    if ($sumber_listrik == "Penyambungan Daya Baru PLN") {
        $sql = "UPDATE socdate_sdg 
                SET sumber_listrik = '$sumber_listrik', 
                    pasanglistrik_date = '$pasanglistrik_date', 
                    id_regpln = '$id_regpln', 
                    form_pengajuanlistrik = '$form_pengajuanlistrik', 
                    status_sdglistrik = '$status_sdglistrik', 
                    status_tafpaylistrik = '$status_tafpaylistrik', 
                    sla_tafpaylistrik = '$sla_tafpaylistrik' 
                WHERE id = '$id'";
    } elseif ($sumber_listrik == "Tambah Listrik Existing") {
        $sql = "UPDATE socdate_sdg 
                SET sumber_listrik = '$sumber_listrik', 
                    form_wolistrik = '$form_wolistrik', 
                    lamp_slo = '$lamp_slo', 
                    lamp_nidi = '$lamp_nidi', 
                    hasil_va = '$hasil_va', 
                    id_pln = '$id_pln', 
                    status_sdglistrik = '$status_sdglistrik', 
                    status_tafpaylistrik = '$status_tafpaylistrik', 
                    sla_tafpaylistrik = '$sla_tafpaylistrik' 
                WHERE id = '$id'";
    } else {
        $sql = "UPDATE socdate_sdg 
                SET sumber_listrik = '$sumber_listrik' 
                WHERE id = '$id'";
    }

    // Jalankan query dan periksa apakah berhasil
    if ($conn->query($sql) === TRUE) {
        $departments = [
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
        header("Location: " . $base_url . "/datatables-sdgpk-rto-listrik.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>