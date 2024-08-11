<?php
include "../../koneksi.php";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Query untuk mendapatkan kode_lahan berdasarkan ID
    $query = "SELECT kode_lahan FROM resto WHERE id = '$id'";
    $result = $conn->query($query);
    $kode_lahan = '';

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kode_lahan = $row['kode_lahan'];
    }

    // Query untuk mengecek gostore_date di tabel resto
    $query = "SELECT gostore_date FROM resto WHERE id = '$id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (empty($row['gostore_date'])) {
            echo 'empty';
        } else {
            echo 'not_empty';
        }
    } else {
        echo 'not_found';
    }
}
?>
