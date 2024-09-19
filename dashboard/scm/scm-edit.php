<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];

    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_sj = "";
    if (isset($_FILES["lamp_sj"])) {
        $lamp_sj_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_sj']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_sj']['tmp_name'][$key];
            $file_name = $_FILES['lamp_sj']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_sj_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_sj = implode(",", $lamp_sj_paths);
    }
    $lamp_utensil = "";

if (isset($_FILES["lamp_utensil"])) {
    $lamp_utensil_paths = array();

    foreach ($_FILES['lamp_utensil']['name'] as $key => $filename) {
        $file_flp = $_FILES['lamp_utensil']['tmp_name'][$key];
        $file_name = $_FILES['lamp_utensil']['name'][$key];
        $target_dir = "../uploads/" . $kode_lahan . "/";
        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file_name);

        if (move_uploaded_file($file_flp, $target_file)) {
            $lamp_utensil_paths[] = $file_name;

            if (pathinfo($file_name, PATHINFO_EXTENSION) == 'xlsx' || pathinfo($file_name, PATHINFO_EXTENSION) == 'xls') {
                $spreadsheet = IOFactory::load($target_file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = min(150, $sheet->getHighestRow());

                for ($row = 3; $row <= $highestRow; $row++) {
                    // Ambil item_name dari kolom C
                        $item_name = $sheet->getCell('C'.$row)->getCalculatedValue();

                        //Query untuk mendapatkan kode_utensil berdasarkan item_name
                        $stmt = $conn->prepare("SELECT kode_utensil FROM list_utensil WHERE item_name = ?");
                        $stmt->bind_param("s", $item_name);
                        $stmt->execute();
                        $stmt->bind_result($kode_utensil);
                        $stmt->fetch();
                        $stmt->close();

                    // if ($kode_utensil) {
                        $qty_target = $sheet->getCell('F'.$row)->getCalculatedValue();
                        $qty_arrival = $sheet->getCell('G'.$row)->getCalculatedValue();
                        $mandatory = $sheet->getCell('H'.$row)->getCalculatedValue();
                        $pic = $sheet->getCell('D'.$row)->getCalculatedValue();
                        $satuan = $sheet->getCell('E'.$row)->getCalculatedValue();

                        // Insert data ke tabel utensil
                        $status_utensil = "In Process";
                        $sql_insert = "INSERT INTO utensil (kode_lahan, kode_utensil, item_name, qty_target, pic, satuan, mandatory, status_utensil) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("ssssssss", $kode_lahan, $kode_utensil, $item_name, $qty_target, $pic, $satuan, $mandatory, $status_utensil);
                        $stmt_insert->execute();
                    // } else {
                    //     echo "Tidak ditemukan kode_utensil untuk item_name: " . htmlspecialchars($item_name) . "<br>";
                    // }
                }
            }
        } else {
            echo "Gagal mengunggah file " . htmlspecialchars($file_name) . "<br>";
        }
    }

    $lamp_utensil = implode(",", $lamp_utensil_paths);
}

    // Update data di database
    $sql = "UPDATE socdate_scm SET lamp_sj = '$lamp_sj', lamp_utensil = '$lamp_utensil' WHERE id = '$id'";
    // var_dump($sql);
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $base_url . "/datatables-scm.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi database
$conn->close();
?>
