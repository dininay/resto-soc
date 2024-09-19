<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $tipe_ipal = $_POST['tipe_ipal'];
    $note_ipalscm = $_POST['note_ipalscm'];
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    
    $sql_get_kode_lahan = "SELECT kode_lahan FROM socdate_sdg WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_woipal = "";
    if (isset($_FILES["lamp_woipal"])) {
        $lamp_woipal_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_woipal']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_woipal']['tmp_name'][$key];
            $file_name = $_FILES['lamp_woipal']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_woipal_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_woipal = implode(",", $lamp_woipal_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_hbm = "";
    if (isset($_FILES["lamp_hbm"])) {
        $lamp_hbm_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_hbm']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_hbm']['tmp_name'][$key];
            $file_name = $_FILES['lamp_hbm']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_hbm_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_hbm = implode(",", $lamp_hbm_paths);
    }

    $status_spkwoipal = "In Process";
    $sla_spkwoipal = "";
    $slaQuerys = "SELECT SUM(sla) as total_day FROM master_slacons WHERE divisi = 'spk-procur'";
    $results = $conn->query($slaQuerys);
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $total_day = $row['total_day'];
        $current_date = new DateTime();
        $current_date->modify("+$total_day days");
        $sla_spkwoipal = $current_date->format('Y-m-d');
    }

    // Update data di database
    $sql = "UPDATE socdate_sdg SET lamp_woipal = '$lamp_woipal', lamp_hbm = '$lamp_hbm', tipe_ipal = '$tipe_ipal', note_ipalscm = '$note_ipalscm', status_spkwoipal = '$status_spkwoipal', sla_spkwoipal = '$sla_spkwoipal' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location:  " . $base_url . "/datatables-scm-ipal.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
