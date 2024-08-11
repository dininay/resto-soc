<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $sumber_listrik = $_POST['sumber_listrik']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $form_pengajuanlistrik = "";

    if(isset($_FILES["form_pengajuanlistrik"])) {
        $form_pengajuanlistrik_paths = array();

        // Loop through each file
        foreach($_FILES['form_pengajuanlistrik']['name'] as $key => $filename) {
            $file_tmp = $_FILES['form_pengajuanlistrik']['tmp_name'][$key];
            $file_name = $_FILES['form_pengajuanlistrik']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $form_pengajuanlistrik_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $form_pengajuanlistrik = implode(",", $form_pengajuanlistrik_paths);
    }
    $note_sumberlistrik = isset($_POST["note_sumberlistrik"]) ? $_POST["note_sumberlistrik"] : null;

    $hasil_va = $_POST['hasil_va']; 
    $id_pln = $_POST['id_pln']; 
    $biaya_perkwh = $_POST['biaya_perkwh']; 

    $status_sdglistrik = "Proceed";
    $status_tafpay = "In Process";
    $sla_tafpay = "";
    $slaQuerys = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'spk-procur'";
    $results = $conn->query($slaQuerys);
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_tafpay = $current_date->format('Y-m-d');
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET sumber_listrik = '$sumber_listrik', form_pengajuanlistrik = '$form_pengajuanlistrik', note_sumberlistrik = '$note_sumberlistrik', hasil_va = '$hasil_va', id_pln = '$id_pln', biaya_perkwh = '$biaya_perkwh', status_sdglistrik = '$status_sdglistrik', status_tafpay = '$status_tafpay', sla_tafpay = '$sla_tafpay' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-sdgpk-rto-listrik.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
