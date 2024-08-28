<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $catatan_obslegal = $_POST['catatan_obslegal'];
    $lamp_legal = "";

    if (isset($_FILES['lamp_legal']) && $_FILES['lamp_legal']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $existing_files = isset($_POST['existing_files']) ? explode(",", $_POST['existing_files']) : array();
        $new_files = array();

        foreach ($_FILES['lamp_legal']['name'] as $key => $filename) {
            if ($filename) {
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($filename);

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['lamp_legal']['tmp_name'][$key], $target_file)) {
                    $new_files[] = trim($filename);
                } else {
                    echo "Failed to upload file: " . $_FILES['lamp_legal']['name'][$key] . "<br>";
                }
            }
        }

        $all_files = array_merge($existing_files, $new_files);
        $lamp_legal = implode(",", array_filter($all_files));
    } else {
        $lamp_legal = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    }

    // Update data di database
    $sql = "UPDATE sdg_desain SET catatan_obslegal = '$catatan_obslegal', lamp_legal = '$lamp_legal' WHERE id = '$id'";
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
