<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $ff_3 = $_POST['ff_3'];
    $persenff_3 = $_POST['persenff_3'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_ff3 = "";

    if(isset($_FILES["lamp_ff3"])) {
        $lamp_ff3_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_ff3']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_ff3']['tmp_name'][$key];
            $file_name = $_FILES['lamp_ff3']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_ff3_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_ff3 = implode(",", $lamp_ff3_paths);
    }

    // Update data di database
    $sql = "UPDATE socdate_hr SET lamp_ff3 = '$lamp_ff3', persen_ff3 = '$persen_ff3', ff_3 = '$ff_3' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-hr-fulfillment-3.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
