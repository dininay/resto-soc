<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $sql = "SELECT email FROM user WHERE level IN ('Procurement')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $email = $row['email'];
        
                // Validasi format email sebelum menambahkannya sebagai penerima
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email); // Tambahkan setiap penerima email
                    
                    // Konten email
                    $mail->isHTML(true);
                    $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                    $mail->Body    = '
                    <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                        <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
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