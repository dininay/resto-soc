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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_sdgqs"]) && isset($_POST["catatan_sdgqs"])) {
    $id = $_POST["id"];
    $confirm_sdgqs = $_POST["confirm_sdgqs"];
    $catatan_sdgqs = $_POST["catatan_sdgqs"];
    $start_date = date("Y-m-d H:i:s");
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;

    // Periksa apakah file kronologi ada dalam $_FILES
    if (isset($_FILES["kronologi"])) {
        $kronologi_paths = array();
        foreach ($_FILES['kronologi']['name'] as $key => $filename) {
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
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui confirm_sdgqs berdasarkan id
        $sql_update = "UPDATE sdg_rab SET confirm_sdgqs = ?, catatan_sdgqs = ?, start_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $confirm_sdgqs, $catatan_sdgqs, $start_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika confirm_sdgqs diubah menjadi Approve
            if ($confirm_sdgqs == 'Approve') {
                // Ambil data dari tabel sdg_rab berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                if ($row = $result_select->fetch_assoc()) {
                    // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "Tender"
                    $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'Tender'";
                    $result_sla = $conn->query($sql_select_sla);

                    if ($row_sla = $result_sla->fetch_assoc()) {
                        $sla_days = $row_sla['sla'];
                        $end_date_obj = new DateTime($start_date);
                        $end_date_obj->modify("+$sla_days days");
                        $sla_spkrab = $end_date_obj->format("Y-m-d");
                        $sla_date = $end_date_obj->format("Y-m-d");

                        $sql_check = "SELECT kode_lahan FROM procurement WHERE kode_lahan = ?";
                        $stmt_check = $conn->prepare($sql_check);
                        $stmt_check->bind_param("s", $row['kode_lahan']);
                        $stmt_check->execute();
                        $stmt_check->store_result();

                        if ($stmt_check->num_rows > 0) {
                            // Jika ada, lakukan update
                            $sql_update = "UPDATE procurement SET 
                                        status_approvsdg = ?, 
                                        status_approvprocurement = ?, 
                                        sla_spkrab = ?, 
                                        status_tender = ?, 
                                        sla_date = ? 
                                        WHERE kode_lahan = ?";
                            $stmt_update = $conn->prepare($sql_update);
                            $status_approvprocurement = "In Process";
                            $status_tender = "In Process";
                            $stmt_update->bind_param("ssssss", $confirm_sdgqs, $status_approvprocurement, $sla_spkrab, $status_tender, $sla_date, $row['kode_lahan']);
                            $stmt_update->execute();
                        } else {
                            // Jika tidak ada, lakukan insert
                            $sql_insert = "INSERT INTO procurement (kode_lahan, status_approvsdg, status_approvprocurement, sla_spkrab, status_tender, sla_date) 
                                        VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt_insert = $conn->prepare($sql_insert);
                            $status_approvprocurement = "In Process";
                            $status_tender = "In Process";
                            $stmt_insert->bind_param("ssssss", $row['kode_lahan'], $confirm_sdgqs, $status_approvprocurement, $sla_spkrab, $status_tender, $sla_date);
                            $stmt_insert->execute();
                        }

                        $stmt_check->close();
                    } else {
                        $conn->rollback();
                        echo "Error: SLA not found for divisi: Tender.";
                        exit;
                    }
                } else {
                    // Rollback transaksi jika terjadi kesalahan pada select
                    $conn->rollback();
                    echo "Error: Data not found for id: $id.";
                    exit;
                }

                // Periksa apakah kode_lahan ada di tabel hold_project
                $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                $stmt_check_hold = $conn->prepare($sql_check_hold);
                $stmt_check_hold->bind_param("s", $row['kode_lahan']);
                $stmt_check_hold->execute();
                $stmt_check_hold->store_result();

                if ($stmt_check_hold->num_rows > 0) {
                    // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                    $status_hold = 'Done';
                    $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                    $stmt_update_hold = $conn->prepare($sql_update_hold);
                    $stmt_update_hold->bind_param("ss", $status_hold, $row['kode_lahan']);
                    $stmt_update_hold->execute();
                }
                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui.";
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
                
                // try {
                //     // Pengaturan server SMTP
                //     $mail->isSMTP();
                //     $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
                //     $mail->SMTPAuth = true;
                //     $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
                //     $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
                //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                //     $mail->Port = 2525;
                    
                //     // Pengaturan pengirim dan penerima
                //     $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');
            
                //     // Query untuk mendapatkan email pengguna dengan level "Real Estate"
                //     $sql = "SELECT email FROM user WHERE level IN ('Procurement')";
                //     $result = $conn->query($sql);
            
                //     if ($result->num_rows > 0) {
                //         while($row = $result->fetch_assoc()) {
                //             $email = $row['email'];
                    
                //             // Validasi format email sebelum menambahkannya sebagai penerima
                //             if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //                 $mail->addAddress($email); // Tambahkan setiap penerima email
                                
                //                 // Konten email
                //                 $mail->isHTML(true);
                //                 $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                //                 $mail->Body    = '
                //                 <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                //                     <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                //                         <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                //                         <p>You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                //                         <p>Thank you for your prompt attention to this matter.</p>
                //                         <p></p>
                //                         <p>Best regards,</p>
                //                         <p>Resto - SOC</p>
                //                     </div>
                //                 </div>';
                //                 $mail->AltBody = 'Dear Team,'
                //                                . 'You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
                //                                . 'Thank you for your prompt attention to this matter.'
                //                                . 'Best regards,'
                //                                . 'Resto - SOC';
                    
                //                 // Kirim email
                //                 $mail->send();
                //                 $mail->clearAddresses(); // Hapus semua penerima sebelum loop berikutnya
                //             } else {
                //                 echo "Invalid email format: " . $email;
                //             }
                //             }
                //         } else {
                //             echo "No emails found.";
                //         }
            
                //     } catch (Exception $e) {
                //         echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
                //     }
            } elseif ($confirm_sdgqs == 'Pending') {
                // Ambil kode_lahan dari tabel sdg_rab
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_pending = "UPDATE sdg_rab SET confirm_sdgqs = ?, catatan_sdgqs = ?, start_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $confirm_sdgqs, $catatan_sdgqs, $start_date, $id);
                $stmt_update_pending->execute();

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
            } elseif ($confirm_sdgqs == 'In Design Revision') {
                // Ambil kode_lahan dari tabel sdg_rab
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_rab WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_pending = "UPDATE sdg_rab SET confirm_sdgqs = ?, catatan_sdgqs = ?, start_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("sssi", $confirm_sdgqs, $catatan_sdgqs, $start_date, $id);
                $stmt_update_pending->execute();

                $confirm_sdgdesain = "In Design Revision";
                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_rab
                $sql_update_design = "UPDATE sdg_desain SET confirm_sdgdesain = ? WHERE kode_lahan = ?";
                $stmt_update_design = $conn->prepare($sql_update_design);
                $stmt_update_design->bind_param("ss", $confirm_sdgdesain, $kode_lahan);
                $stmt_update_design->execute();

                // Komit transaksi
                $conn->commit();
                echo "Status berhasil diperbarui dan data ditahan.";
                
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
                    $sql = "SELECT email FROM user WHERE level IN ('SDG-Design')";
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
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_$status_obssdg
                $sql_update_other = "UPDATE sdg_rab SET confirm_sdgqs = ?, catatan_sdgqs = ?, start_date = ? WHERE id = ?";
                $stmt_update_other = $conn->prepare($sql_update_other);
                $stmt_update_other->bind_param("sssi", $confirm_sdgqs, $catatan_sdgqs, $start_date, $id);

                // Eksekusi query
                if ($stmt_update_other->execute() === TRUE) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: " . $sql_update_other . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        // Redirect ke halaman datatables-approval-owner.php
        header("Location: ../datatables-rab.php");
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
