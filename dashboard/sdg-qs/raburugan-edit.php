<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $ket_urugan = $_POST['ket_urugan'];
    $jumlah_urugan = $_POST['jumlah_urugan'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_raburugan = "";

    if(isset($_FILES["lamp_raburugan"])) {
        $lamp_raburugan_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_raburugan']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_raburugan']['tmp_name'][$key];
            $file_name = $_FILES['lamp_raburugan']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_raburugan_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_raburugan = implode(",", $lamp_raburugan_paths);
    }

    // Update data di database
    $sql = "UPDATE sdg_rab SET ket_urugan = '$ket_urugan', jumlah_urugan = '$jumlah_urugan', lamp_raburugan = '$lamp_raburugan' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-rab-urugan.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
