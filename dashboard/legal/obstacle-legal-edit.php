<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $obslegal_date = $_POST["obslegal_date"];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_legal = "";

    if(isset($_FILES["lamp_legal"])) {
        $lamp_legal_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_legal']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_legal']['tmp_name'][$key];
            $file_name = $_FILES['lamp_legal']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_legal_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_legal = implode(",", $lamp_legal_paths);
    }

    // Update data di database
    $sql = "UPDATE sdg_desain SET lamp_legal = '$lamp_legal',  obslegal_date = '$obslegal_date' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-design-legal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
