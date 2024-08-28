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

    $status_land = $_POST['status_land'];
    $status_approvre = 'In Process';

    // Ambil tanggal hari ini untuk status_date
    $status_date = date('Y-m-d');

    // Ambil nilai sla dari tabel master_sla
    $sql_sla = "SELECT sla FROM master_sla WHERE divisi = 'RE'";
    $result_sla = $conn->query($sql_sla);

    if ($result_sla && $result_sla->num_rows > 0) {
        // Ambil nilai sla dari hasil query
        $row_sla = $result_sla->fetch_assoc();
        $sla = $row_sla['sla'];

        // Hitung tanggal deadline dengan menambahkan status_date dengan sla dari master_sla
        $deadline = date('Y-m-d', strtotime($status_date . ' + ' . $sla . ' days'));

        // Update data di database
        $sql_update = "UPDATE land SET status_land = '$status_land', status_approvre = '$status_approvre', status_date = '$status_date', sla = '$deadline' WHERE id = '$id'";

        if ($conn->query($sql_update) === TRUE) {
            // Mengirim email notifikasi
            $sql_land = "SELECT city, kode_lahan, nama_lahan FROM land WHERE id = '$id'";
            $result_land = $conn->query($sql_land);

            if ($result_land && $result_land->num_rows > 0) {
                $row_land = $result_land->fetch_assoc();
                $city = $row_land['city'];
                $kode_lahan = $row_land['kode_lahan'];
                $nama_lahan = $row_land['nama_lahan'];
                try {
                    // Pengaturan server SMTP
                    $mail->isSMTP();
                    $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
                    $mail->Password = 'c60c92868ce0f8';          // Ganti dengan password email Anda
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 2525;
                
                    // Pengaturan pengirim dan penerima
                    $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');
                    
                    // Query untuk mendapatkan email pengguna dengan level "Real Estate"
                    $sql = "SELECT email FROM user WHERE level = 'Real Estate'";
                    $result = $conn->query($sql);
                
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $mail->addAddress($row['email']); // Tambahkan setiap penerima email

                            // Konten email
                            $mail->isHTML(true);
                            $mail->Subject = 'Notifikasi Entry Lahan Baru';
                            $mail->Body    = '
                            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                    <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Lahan Baru Berhasil Diaktifkan!</h2>
                                    <p style="margin-bottom: 8px;"><strong>Kota:</strong> ' . $city . '</p>
                                    <p style="margin-bottom: 8px;"><strong>Inventory Code:</strong> ' . $kode_lahan . '</p>
                                    <p style="margin-bottom: 8px;"><strong>Nama Lahan:</strong> ' . $nama_lahan . '</p>
                                    <p style="margin-bottom: 8px;"><strong>Status:</strong> ' . $status_land . '</p>
                                    <p>Segera lengkapi data pada Aplikasi SOC - Land Sourcing untuk diserahkan kepada BoD.</p>
                                </div>
                            </div>';
                            $mail->AltBody = 'Telah dilakukan entry data lahan baru: ' .
                                            'Kota: ' . $city . ', ' .
                                            'Inventory Code: ' . $kode_lahan . ', ' .
                                            'Nama Lahan: ' . $nama_lahan . ', ' .
                                            'Status: ' . $status_land . ', ' .
                                            '. Segera lengkapi data pada Aplikasi SOC - Land Sourcing untuk diserahkan kepada BoD.';

                            // Kirim email
                            $mail->send();
                            $mail->clearAddresses(); // Hapus semua penerima sebelum loop berikutnya
                            }
                        }
                        header("Location: " . $base_url . "/datatables-bussiness-planning.php");
                        exit();
            
                } catch (Exception $e) {
                    echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "Error: Lahan tidak ditemukan.";
            }
        } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } else {
    echo "Error: Failed to retrieve sla from master_sla.";
    }
}

// Menutup koneksi database
$conn->close();
?>
