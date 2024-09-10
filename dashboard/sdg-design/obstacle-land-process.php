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

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_obssdg"])) {
    $id = $_POST["id"];
    $status_obssdg = $_POST["status_obssdg"];
    $obs_date = date("Y-m-d H:i:s");
    $layout_date = date("Y-m-d H:i:s");
    $confirm_sdgdesain = "In Process";
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;


    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if(isset($_FILES["kronologi"])) {
        foreach($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $kronologi_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $kronologi = implode(",", $kronologi_paths);
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        if ($status_obssdg == 'Done') {
            $start_date = date("Y-m-d H:i:s");
            $obs_date = date("Y-m-d H:i:s");
        
            // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
            $sql_select_sla_negosiator = "SELECT sla FROM master_sla WHERE divisi = 'Design'";
            $result_select_sla_negosiator = $conn->query($sql_select_sla_negosiator);

            if ($result_select_sla_negosiator && $result_select_sla_negosiator->num_rows > 0) {
                $row_sla_negosiator = $result_select_sla_negosiator->fetch_assoc();
                $sla_negosiator_days = $row_sla_negosiator['sla'];

                // Tambahkan jumlah hari SLA Negotiator ke end_date untuk mendapatkan nego_date
                $slavd_date = date('Y-m-d H:i:s', strtotime($start_date . ' + ' . $sla_negosiator_days . ' days'));

                // Query untuk memperbarui status_$status_obssdg dan status_approvlegalvd
                $sql = "UPDATE sdg_desain SET status_obssdg = ?, obs_date = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $status_obssdg, $obs_date, $id);
                $stmt->execute();
                
                // Ambil kode_lahan dari tabel sdg_desain
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("s", $kode_lahan);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                    $stmt_update_hold->execute();
                }
                $sql_get_kode_lahan = "SELECT kode_lahan, obstacle FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan, $obstacle);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->close();

                if ($obstacle == 'Yes') {
                    $queryIR = "SELECT email FROM user WHERE level IN ('Legal')";
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
                }
            } else {
                echo "Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.";
            }
            $conn->commit();
            echo "Status berhasil diperbarui.";

        } elseif ($status_obssdg == 'Pending') {
            // Mulai transaksi
            $conn->begin_transaction();

            try {
                // Ambil kode_lahan dari tabel sdg_desain
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_desain
                $sql = "UPDATE sdg_desain SET status_obssdg = ?, obs_date = ?, sla_date = ?, confirm_sdgdesain = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $status_obssdg, $obs_date, $slavd_date, $confirm_sdgdesain, $kode_lahan);
                $stmt->execute();

                $status_hold = "In Process";
                $due_date = date("Y-m-d H:i:s");

                // Query untuk memasukkan data ke dalam tabel hold_project
                $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_hold = $conn->prepare($sql_hold);
                $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                $stmt_hold->execute();
                
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $conn->rollback();
                echo "Error: " . $e->getMessage();
            }
        } else {
                $obs_date = date("Y-m-d H:i:s");
                $start_date = date("Y-m-d H:i:s");
                $confirm_sdgdesain = "In Process";
                $status_obslegal = "In Process";
                // Ambil kode_lahan dari tabel sdg_desain
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Ambil kode_lahan dari tabel sdg_desain
                $sql_get_kode_lahan = "SELECT slaloa_date FROM dokumen_loacd WHERE kode_lahan = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("s", $kode_lahan);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($slaloa_date);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

        
                // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = Negotiator
                $sql_select_sla_negosiator = "SELECT sla FROM master_sla WHERE divisi = 'Design'";
                $result_select_sla_negosiator = $conn->query($sql_select_sla_negosiator);
    
                if ($result_select_sla_negosiator && $result_select_sla_negosiator->num_rows > 0) {
                    $row_sla_negosiator = $result_select_sla_negosiator->fetch_assoc();
                    $sla_negosiator_days = $row_sla_negosiator['sla'];
    
                    // Tambahkan jumlah hari SLA Negotiator ke end_date untuk mendapatkan nego_date
                    $slavd_date = date('Y-m-d H:i:s', strtotime($start_date . ' + ' . $sla_negosiator_days . ' days'));
                    
                    // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_$status_obssdg
                    $sql = "UPDATE sdg_desain SET status_obssdg = ?, obs_date = ?, sla_date = ?, confirm_sdgdesain = ?, status_obslegal = ?, sla_obslegal = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssi", $status_obssdg, $obs_date, $slavd_date, $confirm_sdgdesain, $status_obslegal, $slaloa_date, $id);
                    var_dump($status_obssdg);
                    var_dump($obs_date);
                    var_dump($slavd_date);
                    var_dump($confirm_sdgdesain);
                    var_dump($status_obslegal);
                    var_dump($slaloa_date);
                    
                } else {
                    echo "Error: Tidak dapat mengambil data SLA Negotiator dari tabel master_sla.";
                }
            
            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui.";
            
            // Eksekusi query
            if ($stmt->execute() === TRUE) {
                echo "";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    
// Redirect ke halaman datatables-approval-owner.php
header("Location: ../datatables-obstacle-sdg.php");
exit;

} else {
    echo "Data tidak lengkap atau tidak ada pengiriman data dari formulir.";
}

// Menutup koneksi database
$conn->close();
?>
