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
    $id = $_POST['id'];
    // $kode_store = $_POST['kode_store'];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
$lamp_vd = "";

    if(isset($_FILES["lamp_vd"])) {
        $lamp_vd_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_vd']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_vd']['tmp_name'][$key];
            $file_name = $_FILES['lamp_vd']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_vd_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_vd = implode(",", $lamp_vd_paths);
    }
    $end_date = date("Y-m-d H:i:s");
    // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
    $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'VL'";
    $result_sla_steqp = $conn->query($sql_sla_steqp);
    if ($result_sla_steqp->num_rows > 0) {
        $row_sla_steqp = $result_sla_steqp->fetch_assoc();
        $hari_sla_steqp = $row_sla_steqp['sla'];
        $slavdlegal_date = date("Y-m-d", strtotime($end_date . ' + ' . $hari_sla_steqp . ' days'));
    } else {
        $conn->rollback();
        echo "Error: Data SLA tidak ditemukan untuk divisi VL.";
        exit;
    }
    $status_approvlegalvd = "In Process";

    // Update data di database
    $sql = "UPDATE dokumen_loacd SET lamp_vd = '$lamp_vd', end_date = '$end_date', slavdlegal_date = '$slavdlegal_date', status_approvlegalvd = '$status_approvlegalvd' WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-validasi-data.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
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
        $sql = "SELECT email FROM user WHERE level = 'Legal'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $email = $row['email'];
        
                // Validasi format email sebelum menambahkannya sebagai penerima
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email); // Tambahkan setiap penerima email
                    
                    // Konten email
                    $mail->isHTML(true);
                    $mail->Subject = 'Notification: 1 New Active VD Resto SOC Ticket';
                    $mail->Body    = '
                    <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                        <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                            <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                            <p>You have 1 New Active VD Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                            <p>Thank you for your prompt attention to this matter.</p>
                            <p></p>
                            <p>Best regards,</p>
                            <p>Resto - SOC</p>
                        </div>
                    </div>';
                    $mail->AltBody = 'Dear Team,'
                                   . 'You have 1 New Active VD Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
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
    
}

// Menutup koneksi database
$conn->close();
?>
