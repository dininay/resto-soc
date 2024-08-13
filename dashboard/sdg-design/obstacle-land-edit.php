<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $obstacle = $_POST["obstacle"];
    $urugan = $_POST["urugan"];
    $note = $_POST["note"];
    $obs_detail = $_POST["obs_detail"];
    $obs_date = date("Y-m-d");

    // Tentukan status berdasarkan nilai obstacle
    if ($obstacle == 'Yes') {
        $status_obslegal = 'In Process';
        $status_obssdg = 'Diajukan';
    } else if ($obstacle == 'Tidak') {
        $status_obslegal = 'Not Obstacle';
        $status_obssdg = 'Not Obstacle';
    } else {
        $status_obslegal = 'Not Obstacle';
        $status_obssdg = 'Not Obstacle';
    }
    $note_survey = $_POST["note_survey"];

    $lamp_survey = "";

    if(isset($_FILES["lamp_survey"])) {
        $lamp_survey_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_survey']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_survey']['tmp_name'][$key];
            $file_name = $_FILES['lamp_survey']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_survey_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_survey = implode(",", $lamp_survey_paths);
    }

    // Periksa apakah kunci 'lamp_survey' ada dalam $_FILES
    $lamp_layouting = "";

    if(isset($_FILES["lamp_layouting"])) {
        $lamp_layouting_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_layouting']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_layouting']['tmp_name'][$key];
            $file_name = $_FILES['lamp_layouting']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_layouting_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_layouting = implode(",", $lamp_layouting_paths);
    }

    // Ambil SLA dari tabel master_sla dengan divisi = Design
    $sql_select_sla_sdgd = "SELECT sla FROM master_sla WHERE divisi = 'Design'";
    $result_select_sla_sdgd = $conn->query($sql_select_sla_sdgd);

    if ($result_select_sla_sdgd && $result_select_sla_sdgd->num_rows > 0) {
        $row_sla_sdgd = $result_select_sla_sdgd->fetch_assoc();
        $sla_sdgd = $row_sla_sdgd['sla'];
    } else {
        throw new Exception("Tidak dapat mengambil data SLA dari tabel master_sla.");
    }
    
    $sla_date = date('Y-m-d', strtotime($obs_date . ' + ' . $sla_sdgd . ' days'));
    $confirm_sdgurugan = "In Process";
    // Update data di database
    $sql = "UPDATE sdg_desain SET obstacle = '$obstacle', urugan = '$urugan', confirm_sdgurugan = '$confirm_sdgurugan', note = '$note', obs_detail = '$obs_detail', note_survey = '$note_survey', lamp_layouting = '$lamp_layouting', lamp_survey = '$lamp_survey', obs_date = '$obs_date', sla_date = '$sla_date' WHERE id = '$id'";
    var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-obstacle-sdg.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>