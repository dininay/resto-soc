<?php
include "../../koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city_id = $_POST["city_id"];

    $sql_vendor = "SELECT kode_vendor, nama FROM vendor WHERE city = ?";
    $stmt_vendor = $conn->prepare($sql_vendor);
    $stmt_vendor->bind_param("s", $city_id);
    $stmt_vendor->execute();
    $result_vendor = $stmt_vendor->get_result();

    if ($result_vendor->num_rows > 0) {
        while($row = $result_vendor->fetch_assoc()) {
            echo "<option value='" . $row["kode_vendor"] . "'>" . $row["nama"] . "</option>";
        }
    } else {
        echo "<option value=''>Tidak ada kode vendor tersedia</option>";
    }

    $stmt_vendor->close();
}
$conn->close();
?>
