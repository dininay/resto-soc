<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lampwo_reqipal = "";

    if(isset($_FILES["lampwo_reqipal"])) {
        $lampwo_reqipal_paths = array();

        // Loop through each file
        foreach($_FILES['lampwo_reqipal']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lampwo_reqipal']['tmp_name'][$key];
            $file_name = $_FILES['lampwo_reqipal']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lampwo_reqipal_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lampwo_reqipal = implode(",", $lampwo_reqipal_paths);
    }

    $status_sdgipal = "Proceed";
    $status_scmipal = "Not Yet";
    $sla_scmipal = "";
    $slaQuerys = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'spk-procur'";
    $results = $conn->query($slaQuerys);
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_scmipal = $current_date->format('Y-m-d');
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET lampwo_reqipal = '$lampwo_reqipal', status_sdgipal = '$status_sdgipal', status_scmipal = '$status_scmipal', sla_scmipal = '$sla_scmipal' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-sdgpk-rto-ipal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
