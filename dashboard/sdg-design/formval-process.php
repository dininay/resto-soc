<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm_sdgdesain"]) && isset($_POST["catatan_sdgdesain"])) {
    $id = $_POST["id"];
    $confirm_sdgdesain = $_POST["confirm_sdgdesain"];
    $catatan_sdgdesain = $_POST["catatan_sdgdesain"];
    $start_date = null;
    $slalegal_date = null;

    if ($confirm_sdgdesain == 'Approve') {
        $start_date = date("Y-m-d H:i:s");
    } else {
        $start_date = date("Y-m-d H:i:s");

    // Ambil start_date dari database
    $sql_select_start_date = "SELECT start_date FROM sdg_desain WHERE id = ?";
    $stmt_select_start_date = $conn->prepare($sql_select_start_date);
    $stmt_select_start_date->bind_param("i", $id);
    $stmt_select_start_date->execute();
    $result_start_date = $stmt_select_start_date->get_result();
    
    if ($row = $result_start_date->fetch_assoc()) {
        $start_date = $row['start_date'];
    } else {
        $conn->rollback();
        echo "Error: Data not found for id: $id.";
        exit;
    }

    // // Ambil jumlah SLA dari tabel master_sla berdasarkan divisi "Legal"
    // $sql_select_sla = "SELECT sla FROM master_sla WHERE divisi = 'Legal'";
    // $result_sla = $conn->query($sql_select_sla);
    
    // if ($row_sla = $result_sla->fetch_assoc()) {
    //     $sla_days = $row_sla['sla'];
    //     $start_date_obj = new DateTime($start_date);
    //     $start_date_obj->modify("+$sla_days days");
    //     $slaLegal_date = $start_date_obj->format("Y-m-d");
    // } else {
    //     $conn->rollback();
    //     echo "Error: SLA not found for divisi: Legal.";
    //     exit;
    // }
}

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui confirm_sdgdesain dan obstacle
        $sql = "UPDATE sdg_desain SET confirm_sdgdesain = ?, catatan_sdgdesain = ?, start_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $confirm_sdgdesain, $catatan_sdgdesain, $start_date, $id);

        // Eksekusi query
        if ($stmt->execute() === TRUE) {
            // Jika submit_legal diubah menjadi Approve
            if ($confirm_sdgdesain == 'Approve') {
                // Ambil data dari tabel sdg_desain berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan, start_date FROM sdg_desain WHERE id = ?";
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
                        $end_date_obj = new DateTime($row['start_date']);
                        $end_date_obj->modify("+$sla_days_qs days");
                        $sla_date = $end_date_obj->format("Y-m-d");

                        // Masukkan data ke tabel sdg_rab
                        $sql_insert = "INSERT INTO sdg_rab (kode_lahan, confirm_sdgqs, sla_date) VALUES (?,?,?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        // Tambahkan 'In Process' untuk kolom confirm_sdgqs
                        $confirm_sdgqs = 'In Process';
                        $stmt_insert->bind_param("sss", $row['kode_lahan'], $confirm_sdgqs, $sla_date);
                        
                        $stmt_insert->execute();
                    } else {
                        $conn->rollback();
                        echo "Error: SLA not found for divisi: SDG-QS.";
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
            echo "Status, obstacle, dan submit_legal berhasil diperbarui.";
            // Redirect ke halaman datatables-checkval-legal.php
            // header("Location: datatables-formval-release-design.php");
            // exit; // Pastikan tidak ada output lain setelah header redirect
        } else {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}