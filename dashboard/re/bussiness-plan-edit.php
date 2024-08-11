<?php
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
            header("Location: " . $base_url . "/datatables-bussiness-planning.php");
            exit();
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
