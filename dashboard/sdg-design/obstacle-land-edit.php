<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $obstacle = $_POST["obstacle"];
    $urugan = $_POST["urugan"];
    $note = $_POST["note"];
    $obs_detail = $_POST["obs_detail"];
    $potensi_masalah = $_POST["potensi_masalah"];
    $obs_date = date("Y-m-d");

    // Tentukan status berdasarkan nilai obstacle
    if ($obstacle == 'Yes') {
        $status_obslegal = 'In Process';
        $status_obssdg = 'Diajukan';
    } else if ($obstacle == 'Tidak') {
        $status_obslegal = 'Not Obstacle';
        $status_obssdg = 'Not Obstacle';
    } else {
        $status_obslegal = 'Not Obstacle';
        $status_obssdg = 'Not Obstacle';
    }

    if ($confirm_sdgurugan == 'Yes'){
        $confirm_sdgurugan = "In Process";
        $sql_select_sla_sdgd = "SELECT sla FROM master_sla WHERE divisi = 'Urugan'";
        $result_select_sla_sdgd = $conn->query($sql_select_sla_sdgd);
    
        if ($result_select_sla_sdgd && $result_select_sla_sdgd->num_rows > 0) {
            $row_sla_sdgd = $result_select_sla_sdgd->fetch_assoc();
            $sla_sdgd = $row_sla_sdgd['sla'];
        } else {
            throw new Exception("Tidak dapat mengambil data SLA dari tabel master_sla.");
        }
        
        $sla_urugan = date('Y-m-d', strtotime($obs_date . ' + ' . $sla_sdgd . ' days'));
        
    } else if ($confirm_sdgurugan == 'No'){
        $confirm_sdgurugan = Null;
    } else {
        $confirm_sdgurugan = Null;
    }
    $note_survey = $_POST["note_survey"];

    $sql_get_kode_lahan = "SELECT kode_lahan FROM sdg_desain WHERE id = ?";
    $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
    $stmt_get_kode_lahan->bind_param("i", $id);
    $stmt_get_kode_lahan->execute();
    $stmt_get_kode_lahan->bind_result($kode_lahan);
    $stmt_get_kode_lahan->fetch();
    $stmt_get_kode_lahan->free_result();

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_survey = "";
    if (isset($_FILES["lamp_survey"])) {
        $lamp_survey_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_survey']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_survey']['tmp_name'][$key];
            $file_name = $_FILES['lamp_survey']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_survey_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_survey = implode(",", $lamp_survey_paths);
    }

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_layouting = "";
    if (isset($_FILES["lamp_layouting"])) {
        $lamp_layouting_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_layouting']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_layouting']['tmp_name'][$key];
            $file_name = $_FILES['lamp_layouting']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_layouting_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_layouting = implode(",", $lamp_layouting_paths);
    }

    // Ambil SLA dari tabel master_sla dengan divisi = Design
    // Update data di database
    $sql = "UPDATE sdg_desain SET status_obslegal = '$status_obslegal', obstacle = '$obstacle', potensi_masalah = '$potensi_masalah', urugan = '$urugan', confirm_sdgurugan = '$confirm_sdgurugan', note = '$note', obs_detail = '$obs_detail', note_survey = '$note_survey', lamp_layouting = '$lamp_layouting', lamp_survey = '$lamp_survey', obs_date = '$obs_date' WHERE id = '$id'";
    var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-obstacle-sdg.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>