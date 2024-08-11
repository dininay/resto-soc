<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $go_fix = $_POST["go_fix"];
    $rto_act = $_POST["rto_act"];
    $type_kitchen = $_POST["type_kitchen"];
    $jam_ops = $_POST["jam_ops"];
    $project_sales = $_POST["project_sales"];
    $crew_needed = $_POST["crew_needed"];
    $spk_release = $_POST["spk_release"];
    $gocons_progress = $_POST["gocons_progress"];
    $rto_score = $_POST["rto_score"];
    $status_go = $_POST["status_go"];
    // Update data di database
    $sql = "UPDATE summary_soc SET go_fix = '$go_fix', rto_act = '$rto_act', type_kitchen = '$type_kitchen', jam_ops = '$jam_ops', 
    project_sales = '$project_sales', crew_needed = '$crew_needed', spk_release = '$spk_release', gocons_progress = '$gocons_progress',
    rto_score = '$rto_score', status_go = '$status_go' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-soc-summary.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
