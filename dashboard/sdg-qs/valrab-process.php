<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_sdgqs"]) && isset($_POST["catatan_sdgqs"])) {
    $id = $_POST["id"];
    $confirm_sdgqs = $_POST["confirm_sdgqs"];
    $catatan_sdgqs = $_POST["catatan_sdgqs"];
    $start_date = null;

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        $start_date = date("Y-m-d H:i:s");
        // Query untuk memperbarui confirm_sdgqs berdasarkan id
        $sql_update = "UPDATE sdg_rab SET confirm_sdgqs = ?, catatan_sdgqs = ?, start_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $confirm_sdgqs, $catatan_sdgqs, $start_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika valdoc_legal diubah menjadi Approve
            if ($confirm_sdgqs == 'Approve') {

                $sql_rab = "SELECT kode_lahan, lamp_rab, confirm_sdgqs FROM sdg_rab WHERE id = ?";
                $stmt_rab = $conn->prepare($sql_rab);
                $stmt_rab->bind_param("i", $id);
                $stmt_rab->execute();
                $result_rab = $stmt_rab->get_result();
                if ($row = $result_rab->fetch_assoc()) {
                    $sql_select_sla_qs = "SELECT sla FROM master_sla WHERE divisi = 'Tender'";
                    $result_sla_qs = $conn->query($sql_select_sla_qs);
                    
                    if ($row_sla_qs = $result_sla_qs->fetch_assoc()) {
                        $sla_days_qs = $row_sla_qs['sla'];
                        $end_date_obj = new DateTime($start_date);
                        $end_date_obj->modify("+$sla_days_qs days");
                        $sla_date = $end_date_obj->format("Y-m-d");

                        // Masukkan data ke tabel 
                        $sql_insert = "INSERT INTO procurement (kode_lahan, status_approvsdg, status_approvprocurement, sla_date) VALUES (?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $status_approvprocurement = 'In Process';
                        $stmt_insert->bind_param("ssss", $row['kode_lahan'], $row['confirm_sdgqs'], $status_approvprocurement, $sla_date);
                        $stmt_insert->execute();
                    } else {
                        $conn->rollback();
                        echo "Error: SLA not found for divisi: Procurement.";
                        exit;
                    }
                } else {
                    // Rollback transaksi jika terjadi kesalahan pada select
                    $conn->rollback();
                    echo "Error: Data not found for id: $id.";
                    exit;
                }
            }
            // Komit transaksi
            $conn->commit();
            echo "Status dan data berhasil diperbarui.";
            echo "Status, obstacle, dan submit_legal berhasil diperbarui.";
            // // Redirect ke halaman datatables-checkval-legal.php
            header("Location: datatables-validation-rab.php");
            exit; // Pastikan tidak ada output lain setelah header redirect
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}