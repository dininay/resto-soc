<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_approvnego"]) && isset($_POST["catatan_nego"])) {
    $id = $_POST["id"];
    $status_approvnego = $_POST["status_approvnego"];
    $catatan_nego = $_POST["catatan_nego"];

    // Inisialisasi variabel untuk tanggal negosiasi
    $nego_date = null;

    // Jika status_approvnego diubah menjadi Approve, ambil tanggal saat ini dan SLA dari divisi terkait
    if ($status_approvnego == 'Approve') {
        $nego_date = date("Y-m-d H:i:s");

        // Ambil SLA dari tabel master_sla dengan divisi = LOA-CD
        $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'LOA-CD'";
        $result_select_sla = $conn->query($sql_select_sla);

        if ($result_select_sla && $result_select_sla->num_rows > 0) {
            $row_sla = $result_select_sla->fetch_assoc();
            $sla = $row_sla['sla'];
        } else {
            echo "Error: Tidak dapat mengambil data SLA dari tabel master_sla.";
            exit();
        }

        // Ambil SLA dari tabel master_sla dengan divisi = Design
        $sql_select_sla_sdgd = "SELECT sla FROM master_sla WHERE divisi = 'Land Survey'";
        $result_select_sla_sdgd = $conn->query($sql_select_sla_sdgd);

        if ($result_select_sla_sdgd && $result_select_sla_sdgd->num_rows > 0) {
            $row_sla_sdgd = $result_select_sla_sdgd->fetch_assoc();
            $sla_sdgd = $row_sla_sdgd['sla'];
        } else {
            echo "Error: Tidak dapat mengambil data SLA dari tabel master_sla.";
            exit();
        }
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_approvnego, catatan_nego, dan nego_date pada tabel re
        $sql = "UPDATE re SET status_approvnego = ?, catatan_nego = ?, nego_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $status_approvnego, $catatan_nego, $nego_date, $id);

        // Eksekusi query untuk memperbarui status_approvnego dan nego_date
        if ($stmt->execute() === TRUE) {
            // Jika status diubah menjadi Approve, tambahkan data ke tabel dokumen_loacd dan sdg_desain
            if ($status_approvnego == 'Approve') {
                // Hitung sla_date dan sla_date_sdgd
                $sla_date = date('Y-m-d', strtotime($nego_date . ' + ' . $sla . ' days'));
                $sla_survey = date('Y-m-d', strtotime($nego_date . ' + ' . $sla_sdgd . ' days'));

                // Query untuk mengambil data yang diperbarui
                $sql_select = "SELECT * FROM re WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                $updated_row = $result_select->fetch_assoc();

                // Variabel tambahan untuk dokumen_loacd
                $status_approvloacd = "In Process";
                $confirm_sdgdesain = "In Process";
                $confirm_survey = "In Process";
                $confirm_layout = "In Process";

                // Insert data ke tabel dokumen_loacd
                $sql_dokumen = "INSERT INTO dokumen_loacd (kode_lahan, status_approvowner, status_approvlegal, status_approvnego, status_approvloacd, slaloa_date) 
                                VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_dokumen = $conn->prepare($sql_dokumen);
                $stmt_dokumen->bind_param("ssssss", $updated_row['kode_lahan'], $updated_row['status_approvowner'], $updated_row['status_approvlegal'], $updated_row['status_approvnego'], $status_approvloacd, $sla_date);
                $stmt_dokumen->execute();

                // Insert data ke tabel sdg_desain
                $sql_sdgd = "INSERT INTO sdg_desain (kode_lahan, confirm_sdgdesain, confirm_survey, confirm_layout, sla_survey) 
                             VALUES (?, ?, ?)";
                $stmt_sdgd = $conn->prepare($sql_sdgd);
                $stmt_sdgd->bind_param("sss", $updated_row['kode_lahan'], $confirm_sdgdesain, $confirm_survey, $confirm_layout, $sla_survey);
                $stmt_sdgd->execute();
            }

            // Komit transaksi
            $conn->commit();
            echo "Status berhasil diperbarui.";
        } else {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Redirect ke halaman datatables-doc-confirm-negosiator.php
        header("Location: ../datatables-doc-confirm-negosiator.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}