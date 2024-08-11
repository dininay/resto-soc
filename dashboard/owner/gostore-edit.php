<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $kode_lahan = $_POST["kode_lahan"];
    $id = $_POST['id'];
    $gostore_date = $_POST['gostore_date'];
    $nama_lokasi = $_POST['nama_lahan'];
    $status_gostore = "Approve";
    $sql_sla = "SELECT sla FROM master_slacons WHERE divisi = 'RTO'";
    $result_sla = $conn->query($sql_sla);

    if ($result_sla->num_rows > 0) {
        $row_sla = $result_sla->fetch_assoc();
        $sla = $row_sla['sla'];
        
        // Hitung rto_date sebagai gostore_date - sla
        $rto_date = date('Y-m-d', strtotime($gostore_date . " - $sla days"));

    // Update data di database
    $sql = "UPDATE resto SET gostore_date='$gostore_date', approved_by = 'Last Updated by BoD', status_gostore = '$status_gostore', rto_date = '$rto_date' WHERE kode_lahan = '$kode_lahan'";
    $sql_land = "UPDATE land SET nama_lahan='$nama_lokasi' WHERE kode_lahan = '$kode_lahan'";

    if ($conn->query($sql) === TRUE) {
            if ($conn->query($sql_land) === TRUE) {
                header("Location: " . $base_url . "/datatables-gostore.php");
            } else {
                echo "Error: " . $sql_land . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: SLA for RTO division not found.";
    }
}

// Menutup koneksi database
$conn->close();
?>
