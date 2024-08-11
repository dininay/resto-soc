<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "tracking_resto");

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$base_url = "/resto-soc/dashboard";
?>