<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $submit_wo = $_POST["submit_wo"];
    $wo_date = date("Y-m-d");

    $lamp_wo = "";

    if (isset($_FILES["lamp_wo"])) {
        $lamp_wo_paths = array();

        // Loop melalui setiap file
        foreach($_FILES['lamp_wo']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_wo']['tmp_name'][$key];
            $file_name = $_FILES['lamp_wo']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Coba pindahkan file yang diunggah ke direktori target
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_wo_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua path file menjadi string yang dipisahkan koma
        $lamp_wo = implode(",", $lamp_wo_paths);
    }

    // Inisialisasi variabel untuk status_spkwo dan sla_spkwo
    $status_spkwo = "";
    $sla_spkwo = "";

    if ($submit_wo == "Yes") {
        $status_spkwo = "In Process";

        // Ambil SLA dari tabel master_sla untuk divisi SPK
        $sql_sla_spk = "SELECT sla FROM master_sla WHERE divisi = 'SPK'";
        $result_sla_spk = $conn->query($sql_sla_spk);
        if ($result_sla_spk->num_rows > 0) {
            $row_sla_spk = $result_sla_spk->fetch_assoc();
            $hari_sla_spk = $row_sla_spk['sla'];

            // Hitung sla_spkwo berdasarkan wo_date + SLA dari divisi SPK
            $sla_spkwo = date("Y-m-d", strtotime($wo_date . ' + ' . $hari_sla_spk . ' days'));
        } else {
            echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
            exit;
        }
    }

    // Update data di database
    $sql = "UPDATE sdg_desain 
            SET submit_wo = '$submit_wo', lamp_wo = '$lamp_wo', wo_date = '$wo_date', status_spkwo = '$status_spkwo', sla_spkwo = '$sla_spkwo' 
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-submit-wo.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>