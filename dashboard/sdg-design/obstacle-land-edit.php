<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $obstacle = $_POST["obstacle"];
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

    // Periksa apakah kunci 'lamp_survey' ada dalam $_FILES
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
                $lamp_survey_paths[] = $target_file;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_survey = implode(",", $lamp_survey_paths);
    }

    // Update data di database
    $sql = "UPDATE sdg_desain SET obstacle = '$obstacle', note = '$note', obs_detail = '$obs_detail', lamp_survey = '$lamp_survey', status_obslegal = '$status_obslegal', status_obssdg = '$status_obssdg', obs_date = '$obs_date' WHERE id = '$id'";
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