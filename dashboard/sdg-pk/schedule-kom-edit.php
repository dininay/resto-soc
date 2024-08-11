<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $sla_kom = $_POST['sla_kom'];
    $start_slakom = $_POST['start_slakom'];
    
    // Cek status_schedule saat ini
    $checkSql = "SELECT status_schedule FROM resto WHERE id = '$id'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentStatus = $row['status_schedule'];

        // Periksa apakah status_schedule saat ini adalah 'Approve'
        if ($currentStatus === 'Approve') {
            $status_schedule = 'Approve Update';
        }
    }

    // Update data di database
    $updateSql = "UPDATE resto SET start_slakom = '$start_slakom', sla_kom = '$sla_kom', status_schedule = '$status_schedule' WHERE id = '$id'";

    if ($conn->query($updateSql) === TRUE) {
        header("Location:" . $base_url . "/datatables-kom-schedule.php");
        exit();
    } else {
        echo "Error: " . $updateSql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
