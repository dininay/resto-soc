<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
// Koneksi ke database
include "../../koneksi.php";

echo $_POST["id"];

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_sdgurugan"]) && isset($_POST["catatan_sdgurugan"])) {
    $id = $_POST["id"];
    var_dump($id); // Debugging untuk memastikan ID diterima
    $confirm_sdgurugan = $_POST["confirm_sdgurugan"];
    var_dump($confirm_sdgurugan);
    $catatan_sdgurugan = $_POST["catatan_sdgurugan"];
    $urugan_date = null;
    $slalegal_date = null;
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $submit_legal = null;
    $obstacle = null;

    // Periksa apakah file kronologi ada dalam $_FILES
    $kronologi_paths = array();
    if (isset($_FILES["kronologi"])) {
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
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
    }

    if ($confirm_sdgurugan == 'Approve') {
        $submit_legal = 'In Process';
        $urugan_date = date("Y-m-d H:i:s");
    } else {
        $urugan_date = date("Y-m-d H:i:s");
    }

        // Ambil urugan_date dari database
    $sql_select_urugan_date = "SELECT urugan_date FROM sdg_desain WHERE id = ?";
    $stmt_select_urugan_date = $conn->prepare($sql_select_urugan_date);
    $stmt_select_urugan_date->bind_param("i", $id);
    $stmt_select_urugan_date->execute();
    $result_urugan_date = $stmt_select_urugan_date->get_result();

    if ($row = $result_urugan_date->fetch_assoc()) {
        $urugan_date = $row['urugan_date'];
    } else {
        $conn->rollback();
        echo "Error: Data not found for id: $id.";
        exit;
    }

    // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "Legal"
    $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'Legal'";
    $result_sla = $conn->query($sql_select_sla);

    if ($row_sla = $result_sla->fetch_assoc()) {
        $sla_days = $row_sla['sla'];
        $urugan_date_obj = new DateTime($urugan_date);
        $urugan_date_obj->modify("+$sla_days days");
        $slalegal_date = $urugan_date_obj->format("Y-m-d");
    } else {
        $conn->rollback();
        echo "Error: SLA not found for divisi: Legal.";
        exit;
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        $urugan_date = date("Y-m-d H:i:s");
        // Query untuk memperbarui confirm_sdgurugan dan obstacle
        $sql = "UPDATE sdg_desain SET confirm_sdgurugan = ?, catatan_sdgurugan = ?,  urugan_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $confirm_sdgurugan, $catatan_sdgurugan,  $urugan_date, $id);

        // Eksekusi query
        if ($stmt->execute() === TRUE) {
            // Jika submit_legal diubah menjadi Approve
            if ($confirm_sdgurugan == 'Approve') {
                // Ambil data dari tabel sdg_desain berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan, end_date FROM sdg_desain WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                if ($row = $result_select->fetch_assoc()) {
                    // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "SDG-QS"
                    $sql_select_sla_qs = "SELECT sla FROM master_sla WHERE divisi = 'QS'";
                    $result_sla_qs = $conn->query($sql_select_sla_qs);

                    if ($row_sla_qs = $result_sla_qs->fetch_assoc()) {
                        $sla_days_qs = $row_sla_qs['sla'];
                        $end_date_obj = new DateTime($row['end_date']);
                        $end_date_obj->modify("+$sla_days_qs days");
                        $sla_date = $end_date_obj->format("Y-m-d");
                        $slaurugan_date = $end_date_obj->format("Y-m-d");

                        // Cek apakah kode_lahan sudah ada di tabel sdg_rab
                        $sql_check = "SELECT kode_lahan FROM sdg_rab WHERE kode_lahan = ?";
                        $stmt_check = $conn->prepare($sql_check);
                        $stmt_check->bind_param("s", $row['kode_lahan']);
                        $stmt_check->execute();
                        $stmt_check->store_result();

                        if ($stmt_check->num_rows > 0) {
                            // Jika ada, lakukan update
                            $sql_update = "UPDATE sdg_rab SET 
                                        confirm_sdgqs = ?, 
                                        sla_date = ?, 
                                        confirm_qsurugan = ?, 
                                        slaurugan_date = ? 
                                        WHERE kode_lahan = ?";
                            $confirm_qsurugan = "In Process";
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->bind_param("sssss", $confirm_sdgqs, $sla_date, $confirm_qsurugan, $slaurugan_date, $row['kode_lahan']);
                            $stmt_update->execute();
                        } else {
                            // Jika tidak ada, lakukan insert
                            $sql_insert = "INSERT INTO sdg_rab (kode_lahan, confirm_sdgqs, sla_date, confirm_qsurugan, slaurugan_date) 
                                        VALUES (?, ?, ?, ?, ?)";
                            $stmt_insert = $conn->prepare($sql_insert);
                            $confirm_qsurugan = "In Process";
                            $stmt_insert->bind_param("sssss", $row['kode_lahan'], $confirm_sdgqs, $sla_date, $confirm_qsurugan, $slaurugan_date);
                            $stmt_insert->execute();
                        }

                        $stmt_check->close();
                    } 
                    
                    // Ambil kode_lahan dari tabel re
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
                    // Komit transaksi
                    $conn->commit();
                    echo "Status berhasil diperbarui.";
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
                    $sql = "SELECT email FROM user WHERE level IN ('SDG-QS')";
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
            } elseif ($confirm_sdgurugan == 'Pending') {
                // Ambil kode_lahan dari tabel sdg_desain
                $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->free_result();

                // Query untuk memperbarui submit_legal dan catatan_owner di tabel sdg_desain
                $sql = "UPDATE sdg_desain SET confirm_sdgurugan = ?, catatan_sdgurugan = ?, obstacle = ?, submit_legal = ?, urugan_date = ?, slalegal_date = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $confirm_sdgurugan, $catatan_sdgurugan, $obstacle, $submit_legal, $urugan_date, $slalegal_date, $kode_lahan);
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
            } else {
                // Jika status tidak diubah menjadi Approve, Reject, atau Pending, hanya perlu memperbarui status_$status_obssdg
                $sql = "UPDATE sdg_desain SET confirm_sdgurugan = ?, catatan_sdgurugan = ?, obstacle = ?, submit_legal = ?, urugan_date = ?, slalegal_date = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $confirm_sdgurugan, $catatan_sdgurugan, $obstacle, $submit_legal, $urugan_date, $slalegal_date, $kode_lahan);

                // Eksekusi query
                if ($stmt->execute() === TRUE) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        // Redirect ke halaman datatables-approval-owner.php
header("Location: ../datatables-urugan.php");
exit;

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>