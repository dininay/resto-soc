<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $gmaps = $_POST['gmaps'];
    $id_m_gojek = $_POST['id_m_gojek'];
    $id_m_shopee = $_POST['id_m_shopee'];
    $id_m_grab = $_POST['id_m_grab'];
    $email_resto = $_POST['email_resto'];
    $lamp_content = $_POST['lamp_content'];
    $issue_marketing = isset($_POST["issue_marketing"]) ? $_POST["issue_marketing"] : null;
    $note_issuemarketing = isset($_POST["note_issuemarketing"]) ? $_POST["note_issuemarketing"] : null;
    $catatan_marketing = $_POST['catatan_marketing'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    
    $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_marketing WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_gmaps = "";
    if (isset($_FILES["lamp_gmaps"])) {
        $lamp_gmaps_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_gmaps']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_gmaps']['tmp_name'][$key];
            $file_name = $_FILES['lamp_gmaps']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_gmaps_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_gmaps = implode(",", $lamp_gmaps_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_merchant = "";
    if (isset($_FILES["lamp_merchant"])) {
        $lamp_merchant_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_merchant']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_merchant']['tmp_name'][$key];
            $file_name = $_FILES['lamp_merchant']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_merchant_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_merchant = implode(",", $lamp_merchant_paths);
    }
    // Update data di database
    $sql = "UPDATE socdate_marketing SET lamp_gmaps = '$lamp_gmaps', gmaps = '$gmaps', id_m_gojek = '$id_m_gojek', id_m_grab = '$id_m_grab', id_m_shopee = '$id_m_shopee', email_resto = '$email_resto', lamp_merchant = '$lamp_merchant', lamp_content = '$lamp_content', issue_marketing = '$issue_marketing', note_issuemarketing = '$note_issuemarketing', catatan_marketing  = '$catatan_marketing ' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:" . $base_url . "/datatables-marketing.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
