<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $sumber_listrik = $_POST['sumber_listrik']; 
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $form_pengajuanlistrik_paths = array();
    if(isset($_FILES["form_pengajuanlistrik"])) {
        foreach($_FILES['form_pengajuanlistrik']['name'] as $key => $filename) {
            $file_tmp = $_FILES['form_pengajuanlistrik']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_dir . $target_file)) {
                $form_pengajuanlistrik_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $form_pengajuanlistrik = implode(",", $form_pengajuanlistrik_paths);
    } else {
        $kronologi = null; // Set kronologi to null if no files were uploaded
    }

    $lamp_slo = "";

    if(isset($_FILES["lamp_slo"])) {
        $lamp_slo_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_slo']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_slo']['tmp_name'][$key];
            $file_name = $_FILES['lamp_slo']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_slo_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_slo = implode(",", $lamp_slo_paths);
    }

    $lamp_nidi = "";

    if(isset($_FILES["lamp_nidi"])) {
        $lamp_nidi_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_nidi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_nidi']['tmp_name'][$key];
            $file_name = $_FILES['lamp_nidi']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_nidi_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_nidi = implode(",", $lamp_nidi_paths);
    }

    $hasil_va = $_POST['hasil_va']; 
    $pasanglistrik_date = isset($_POST["pasanglistrik_date"]) ? $_POST["pasanglistrik_date"] : null;
    $id_pln = $_POST['id_pln']; 

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
    $sql = "UPDATE socdate_sdg SET sumber_listrik = '$sumber_listrik', pasanglistrik_date = '$pasanglistrik_date', form_pengajuanlistrik = '$form_pengajuanlistrik', lamp_slo = '$lamp_slo', lamp_nidi = '$lamp_nidi', hasil_va = '$hasil_va', id_pln = '$id_pln', status_sdglistrik = '$status_sdglistrik', status_tafpay = '$status_tafpay', sla_tafpay = '$sla_tafpay' WHERE id = '$id'";
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
