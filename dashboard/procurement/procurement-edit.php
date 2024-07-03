<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $nama_vendor = $_POST['nama_vendor'];
    // $lamp_vendor = $_POST['lamp_vendor'];
    $id = $_POST['id'];

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
// if(isset($_FILES["lamp_splegal"])) {
//     // Simpan lampiran ke folder tertentu
//     $lamp_splegal = array();
//     $total_files = count($_FILES['lamp_splegal']['name']);
//     for($i = 0; $i < $total_files; $i++) {
//         $file_tmp = $_FILES['lamp_splegal']['tmp_name'][$i];
//         $file_name = $_FILES['lamp_splegal']['name'][$i];
//         $file_path = "../uploads/" . $file_name;
//         move_uploaded_file($file_tmp, $file_path);
//         $lamp_splegal[] = $file_path;
//     }
//     $lamp_splegal = implode(",", $lamp_splegal);
// } else {
//     $lamp_splegal = "";
// }

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if(isset($_FILES['lamp_loacd']) && $_FILES['lamp_loacd']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = explode(", ", $_POST['existing_files']); // Ambil nama file sebelumnya
    //     $new_files = array();
        
    //     // Simpan file-file baru yang diunggah
    //     foreach($_FILES['lamp_loacd']['name'] as $key => $filename) {
    //         $target_dir = "uploads/";
    //         $target_file = $target_dir . basename($_FILES['lamp_loacd']['name'][$key]);
    //         if (move_uploaded_file($_FILES['lamp_loacd']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $target_file;
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_land = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_land = $_POST['existing_files'];
    // }

    // Update data di database
    $sql = "UPDATE procurement SET nama_vendor = '$nama_vendor' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-procurement.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
