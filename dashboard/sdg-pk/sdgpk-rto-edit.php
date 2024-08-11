<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $sumber_air = $_POST['sumber_air']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_sumberair = "";

    if(isset($_FILES["lamp_sumberair"])) {
        $lamp_sumberair_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_sumberair']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_sumberair']['tmp_name'][$key];
            $file_name = $_FILES['lamp_sumberair']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_sumberair_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_sumberair = implode(",", $lamp_sumberair_paths);
    }
    $kesesuaian_ujilab = $_POST['kesesuaian_ujilab']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_ujilab = null;

    // Periksa apakah file lamp_ujilab ada dalam $_FILES
    if (isset($_FILES["lamp_ujilab"])) {
        $lamp_ujilab_paths = array();
        foreach ($_FILES['lamp_ujilab']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ujilab']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ujilab_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_ujilab = implode(",", $lamp_ujilab_paths);
    }
    $filter_air = $_POST['filter_air']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_filterair = null;

    // Periksa apakah file lamp_filterair ada dalam $_FILES
    if (isset($_FILES["lamp_filterair"])) {
        $lamp_filterair_paths = array();
        foreach ($_FILES['lamp_filterair']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_filterair']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_filterair_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_filterair = implode(",", $lamp_filterair_paths);
    }
    $debit_airsumur = $_POST['debit_airsumur']; 
    $debit_airpdam = $_POST['debit_airpdam']; 
    $id_pdam = $_POST['id_pdam']; 
    $status_sdgsumber = "Proceed";
    $status_procurspkwofa = "In Process";
    $sla_spkwofa = "";
    $slaQuery = "SELECT SUM(sla) as total_days FROM master_slacons WHERE divisi = 'spk-procur'";
    $result = $conn->query($slaQuery);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_days = $row['total_days'];
        $current_date = new DateTime();
        $current_date->modify("+$total_days days");
        $sla_spkwofa = $current_date->format('Y-m-d');
    }
    $status_tafpay = "In Process";
    $sla_tafpay = "";
    $slaQuerys = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'payment-taf'";
    $results = $conn->query($slaQuerys);
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_tafpay = $current_date->format('Y-m-d');
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET sumber_air = '$sumber_air', lamp_sumberair = '$lamp_sumberair', kesesuaian_ujilab = '$kesesuaian_ujilab', lamp_ujilab = '$lamp_ujilab', filter_air = '$filter_air', lamp_filterair = '$lamp_filterair', debit_airsumur = '$debit_airsumur', debit_airpdam = '$debit_airpdam', id_pdam = '$id_pdam', status_sdgsumber = '$status_sdgsumber', status_procurspkwofa = '$status_procurspkwofa', status_tafpay = '$status_tafpay', sla_spkwofa = '$sla_spkwofa', sla_tafpay = '$sla_tafpay' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-sdgpk-rto.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
