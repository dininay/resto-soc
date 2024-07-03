<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["status_approvlegal"]) && isset($_POST["catatan_legal"])) {
    $kode_lahan = $_POST["kode_lahan"];
    $status_approvlegal = $_POST["status_approvlegal"];
    $catatan_legal = $_POST["catatan_legal"];

    // Inisialisasi variabel untuk status_approvlegal
    $status_approvnego = null;
    $end_date = null;
    $nego_date = null;

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        if ($status_approvlegal == 'Approve') {
            $status_approvnego = 'In Process';
            $status_vl = 'In Process';
            $end_date = date("Y-m-d H:i:s");

            // Ambil jumlah hari SLA dari tabel master_sla berdasarkan divisi = VL
            $sql_select_sla_vl = "SELECT sla FROM master_sla WHERE divisi = 'VL'";
            $result_select_sla_vl = $conn->query($sql_select_sla_vl);

            if ($result_select_sla_vl && $result_select_sla_vl->num_rows > 0) {
                $row_sla_vl = $result_select_sla_vl->fetch_assoc();
                $sla_vl_days = $row_sla_vl['sla'];

                // Tambahkan jumlah hari SLA VL ke end_date untuk mendapatkan vl_date
                $vl_date = date('Y-m-d H:i:s', strtotime($end_date . ' + ' . $sla_vl_days . ' days'));

                // Query untuk memperbarui status_approvlegal, status_approvnego, status_vl, end_date, dan vl_date
                $sql = "UPDATE re SET status_approvlegal = ?, catatan_legal = ?, status_approvnego = ?, status_vl = ?, end_date = ?, slavl_date = ? WHERE kode_lahan = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $status_approvlegal, $catatan_legal, $status_approvnego, $status_vl, $end_date, $vl_date, $kode_lahan);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error: Tidak dapat mengambil data SLA VL dari tabel master_sla.";
            }
        } elseif ($status_approvlegal == 'Reject') {
            // Ambil kode lahan sebelum menghapus dari tabel re
            $sql_select_kode_lahan = "SELECT kode_lahan FROM re WHERE kode_lahan = ?";
            $stmt_select_kode_lahan = $conn->prepare($sql_select_kode_lahan);
            $stmt_select_kode_lahan->bind_param("s", $id);
            $stmt_select_kode_lahan->execute();
            $stmt_select_kode_lahan->bind_result($kode_lahan);
            $stmt_select_kode_lahan->fetch();
            $stmt_select_kode_lahan->close();

            // Hapus data dari tabel re berdasarkan ID
            $sql_delete_re = "DELETE FROM re WHERE kode_lahan = ?";
            $stmt_delete_re = $conn->prepare($sql_delete_re);
            $stmt_delete_re->bind_param("s", $kode_lahan);
            $stmt_delete_re->execute();
            $stmt_delete_re->close();

            // Perbarui status_land menjadi Reject pada tabel land berdasarkan kode lahan
            $sql_update_land = "UPDATE land SET status_land = 'Reject', status_approve = 'In Process' WHERE kode_lahan = ?";
            $stmt_update_land = $conn->prepare($sql_update_land);
            $stmt_update_land->bind_param("s", $kode_lahan);
            $stmt_update_land->execute();
            $stmt_update_land->close();
        } else {
            // Query untuk memperbarui status_approvlegal
            $sql = "UPDATE re SET status_approvlegal = ? WHERE kode_lahan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $status_approvlegal, $kode_lahan);
            $stmt->execute();
            $stmt->close();
        }

        // Komit transaksi
        $conn->commit();
        echo "Status berhasil diperbarui.";
    //     // Redirect ke halaman datatables-kom-sdgpk.php
    header("Location: ../datatables-validasi-lahan-legal.php");
    exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>