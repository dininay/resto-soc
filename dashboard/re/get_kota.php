<?php
    include "../../koneksi.php";

if(isset($_POST['provinsi'])) {
    $provinsi = $_POST['provinsi'];

    // Query untuk mengambil data kota berdasarkan provinsi
    $sql = "SELECT City FROM master_city WHERE Provinsi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $provinsi);
    $stmt->execute();
    $result = $stmt->get_result();

    $kota = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $kota[] = $row['City'];
        }
    }

    // Mengembalikan data dalam format JSON
    echo json_encode($kota);
}
?>
