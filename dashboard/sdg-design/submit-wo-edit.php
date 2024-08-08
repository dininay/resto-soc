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

    if(isset($_FILES["lamp_wo"])) {
        $lamp_wo_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_wo']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_wo']['tmp_name'][$key];
            $file_name = $_FILES['lamp_wo']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_wo_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_wo = implode(",", $lamp_wo_paths);
    }


    // // Ambil SLA dari tabel master_sla dengan divisi = Design
    // $sql_select_sla_sdgd = "SELECT sla FROM master_sla WHERE divisi = 'Design'";
    // $result_select_sla_sdgd = $conn->query($sql_select_sla_sdgd);

    // if ($result_select_sla_sdgd && $result_select_sla_sdgd->num_rows > 0) {
    //     $row_sla_sdgd = $result_select_sla_sdgd->fetch_assoc();
    //     $sla_sdgd = $row_sla_sdgd['sla'];
    // } else {
    //     throw new Exception("Tidak dapat mengambil data SLA dari tabel master_sla.");
    // }
    
    // $sla_date = date('Y-m-d', strtotime($wo_date . ' + ' . $sla_sdgd . ' days'));

    // Update data di database
    $sql = "UPDATE sdg_desain SET submit_wo = '$submit_wo', lamp_wo = '$lamp_wo', wo_date = '$wo_date' WHERE id = '$id'";
    var_dump($sql);
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