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
    $kode_lahan = $_POST["kode_lahan"];
    $nama_lahan = $_POST["nama_lahan"];
    $lokasi = $_POST["lokasi"];
    $nama_pemilik = $_POST["nama_pemilik"];
    $alamat_pemilik = $_POST["alamat_pemilik"];
    $no_tlp = $_POST["no_tlp"];
    $luas_area = $_POST["luas_area"];
    $maps = $_POST["maps"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $harga_sewa = $_POST["harga_sewa"];
    $mintahun_sewa = $_POST["mintahun_sewa"];
    $status_approvre = "Approve";
    $id = $_POST['id'];
    $re_date = date('Y-m-d');

    $lamp_land = "";
    if (isset($_FILES["lamp_land"])) {
        $lamp_land_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_land']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_land']['tmp_name'][$key];
            $file_name = $_FILES['lamp_land']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_land_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_land = implode(",", $lamp_land_paths);
    }

    // Update data di tabel land
    $sql_update_land = "UPDATE land SET kode_lahan = ?, re_date = ?, nama_lahan = ?, lokasi = ?, nama_pemilik = ?, alamat_pemilik = ?, no_tlp = ?, luas_area = ?, lamp_land = ?, maps = ?, latitude = ?, longitude = ?, harga_sewa = ?, mintahun_sewa = ?, status_approvre = ? WHERE id = ?";
    $stmt_update_land = $conn->prepare($sql_update_land);
    $stmt_update_land->bind_param("sdsssssssssssssi", $kode_lahan, $re_date, $nama_lahan, $lokasi, $nama_pemilik, $alamat_pemilik, $no_tlp, $luas_area, $lamp_land, $maps, $latitude, $longitude, $harga_sewa, $mintahun_sewa, $status_approvre, $id);

    if ($stmt_update_land->execute() === TRUE) {
        // Cek jika kode_lahan sudah ada di tabel re
        $sql_check_re = "SELECT status_approvowner, status_vl FROM re WHERE kode_lahan = ?";
        $stmt_check_re = $conn->prepare($sql_check_re);
        $stmt_check_re->bind_param("s", $kode_lahan);
        $stmt_check_re->execute();
        $stmt_check_re->store_result();
        $stmt_check_re->bind_result($existing_status_approvowner, $existing_status_vl);
        $stmt_check_re->fetch();

        if ($stmt_check_re->num_rows === 0) {
            // Jika kode_lahan tidak ada di tabel re dan status_approvre adalah 'Approve'
            if ($status_approvre == 'Approve') {
                // Ambil data dari tabel land
                $sql_select = "SELECT kode_lahan, re_date FROM land WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                
                if ($row = $result_select->fetch_assoc()) {
                    $sql_select_sla_qs = "SELECT sla FROM master_sla WHERE divisi = 'Owner Surveyor'";
                    $result_sla_qs = $conn->query($sql_select_sla_qs);

                    if ($row_sla_qs = $result_sla_qs->fetch_assoc()) {
                        $sql_sla_vl = "SELECT sla FROM master_sla WHERE divisi = 'VL'";
                        $result_sla_vl = $conn->query($sql_sla_vl);
                        if ($result_sla_vl->num_rows > 0) {
                            $row_sla_vl = $result_sla_vl->fetch_assoc();
                            $hari_sla_vl = $row_sla_vl['sla'];
                            $slavl_date = date("Y-m-d", strtotime($re_date . ' + ' . $hari_sla_vl . ' days'));
                        } else {
                            $conn->rollback();
                            echo "Error: Data SLA tidak ditemukan untuk divisi VL";
                            exit;
                        }
                        $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'Owner Surveyor'";
                        $result_sla_steqp = $conn->query($sql_sla_steqp);
                        if ($result_sla_steqp->num_rows > 0) {
                            $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                            $hari_sla_steqp = $row_sla_steqp['sla'];
                            $sla_date = date("Y-m-d", strtotime($re_date . ' + ' . $hari_sla_steqp . ' days'));
                        } else {
                            $conn->rollback();
                            echo "Error: Data SLA tidak ditemukan untuk divisi BoD";
                            exit;
                        }

                        $status_approvowner = 'In Process';
                        $status_vl = 'In Process';

                        $sql_insert_re = "INSERT INTO re (kode_lahan, status_approvowner, sla_date, status_vl, slavl_date) VALUES (?,?,?,?,?)";
                        $stmt_insert_re = $conn->prepare($sql_insert_re);
                        $stmt_insert_re->bind_param("sssss", $row['kode_lahan'], $status_approvowner, $sla_date, $status_vl, $slavl_date);
                        $stmt_insert_re->execute();
                    }

                    $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                    $stmt_check_hold = $conn->prepare($sql_check_hold);
                    $stmt_check_hold->bind_param("s", $row['kode_lahan']);
                    $stmt_check_hold->execute();
                    $stmt_check_hold->store_result();

                    if ($stmt_check_hold->num_rows > 0) {
                        $status_hold = 'Done';
                        $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                        $stmt_update_hold = $conn->prepare($sql_update_hold);
                        $stmt_update_hold->bind_param("ss", $status_hold, $row['kode_lahan']);
                        $stmt_update_hold->execute();
                    }
                }
            }
            $departments = [
                'Real Estate',
                'BoD'
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
                // Redirect setelah email dikirim
                header("Location: " . $base_url . "/datatables-land-sourcing.php");
                exit();
        }
    } else {
        echo "Error: " . $stmt_update_land->error;
    }

    // Tutup koneksi dan statement
    $stmt_update_land->close();
    $conn->close();
}
?>