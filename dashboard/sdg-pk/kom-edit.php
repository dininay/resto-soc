<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $start_konstruksi = $_POST['start_konstruksi']; // Assuming this value is posted from form
    $sla_consact = $_POST['sla_consact']; // Assuming this value is posted from form
    $obstacle_kom = $_POST['obstacle_kom']; // Assuming this value is posted from form
    $note_kom = isset($_POST["note_kom"]) ? $_POST["note_kom"] : null;// Assuming this value is posted from form
    $pm_cons = $_POST['pm_cons'];
    $sm_cons = $_POST['sm_cons'];
    $pm_ppa = $_POST['pm_ppa'];
    $cons_manager = $_POST['cons_manager'];
    $site_inspect = $_POST['site_inspect'];
    $tgl_agendastkons = $_POST['tgl_agendastkons'];
    $status_consact = "In Process";
    $lamp_kom = "";
    if (isset($_FILES["lamp_kom"])) {
        $lamp_kom_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_kom']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_kom']['tmp_name'][$key];
            $file_name = $_FILES['lamp_kom']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_kom_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_kom = implode(",", $lamp_kom_paths);
    }
    $lamp_obskom = "";
    if (isset($_FILES["lamp_obskom"])) {
        $lamp_obskom_paths = array();

        // Path ke direktori "uploads"
        $target_dir = "../uploads/" . $kode_lahan . "/";

        // Cek apakah folder dengan nama kode_lahan sudah ada
        if (!is_dir($target_dir)) {
            // Jika folder belum ada, buat folder baru
            mkdir($target_dir, 0777, true);
        }

        // Loop untuk menangani setiap file yang diunggah
        foreach ($_FILES['lamp_obskom']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_obskom']['tmp_name'][$key];
            $file_name = $_FILES['lamp_obskom']['name'][$key];
            $target_file = $target_dir . basename($file_name); // Simpan di folder kode_lahan

            // Pindahkan file yang diunggah ke target folder
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_obskom_paths[] = $file_name; // Simpan nama file
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Gabungkan semua nama file menjadi satu string, dipisahkan koma
        $lamp_obskom = implode(",", $lamp_obskom_paths);
    }

    // $lamp_kom = "";

    // // Menggabungkan nama file baru dengan nama file sebelumnya, jika ada
    // if (isset($_FILES['lamp_kom']) && $_FILES['lamp_kom']['error'][0] != UPLOAD_ERR_NO_FILE) {
    //     $existing_files = isset($_POST['existing_files']) ? explode(", ", $_POST['existing_files']) : array(); // Ambil nama file sebelumnya
    //     $new_files = array();

    //     // Simpan file-file baru yang diunggah
    //     foreach ($_FILES['lamp_kom']['name'] as $key => $filename) {
    //         $target_dir = "../uploads/";
    //         $target_file = $target_dir . basename($filename);

    //         // Buat direktori jika belum ada
    //         if (!is_dir($target_dir)) {
    //             mkdir($target_dir, 0777, true);
    //         }

    //         if (move_uploaded_file($_FILES['lamp_kom']['tmp_name'][$key], $target_file)) {
    //             $new_files[] = $filename;
    //         } else {
    //             echo "Failed to upload file: " . $_FILES['lamp_kom']['name'][$key] . "<br>";
    //         }
    //     }

    //     // Gabungkan file-file baru dengan file-file sebelumnya
    //     $lamp_kom = implode(", ", array_merge($existing_files, $new_files));
    // } else {
    //     // Jika tidak ada file baru diunggah, gunakan file yang sudah ada
    //     $lamp_kom = isset($_POST['existing_files']) ? $_POST['existing_files'] : "";
    // }

    // Update data di database untuk tabel resto
    $sql1 = "UPDATE resto SET lamp_kom = ?, start_konstruksi = ?, obstacle_kom = ?, note_kom = ?, lamp_obskom = ?, pm_cons = ?, sm_cons = ?, pm_ppa = ?, cons_manager = ?, site_inspect = ?, tgl_agendastkons = ? WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("sssssssssssi",$lamp_kom, $start_konstruksi, $obstacle_kom, $note_kom, $lamp_obskom, $pm_cons, $sm_cons, $pm_ppa, $cons_manager, $site_inspect, $tgl_agendastkons, $id);
    
    // Check if kode_lahan already exists in sdg_pk
    $check_sql = "SELECT kode_lahan FROM sdg_pk WHERE kode_lahan = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $kode_lahan);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        // kode_lahan does not exist, proceed with insertion
        $sql2 = "INSERT INTO sdg_pk (kode_lahan, sla_consact) VALUES (?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $kode_lahan, $sla_consact);
        $stmt2->execute();
        $stmt2->close();
    } else {
        echo "Kode lahan already exists, skipping insertion.";
    }

    // Execute the update query for resto
    if ($stmt1->execute()) {
        header("Location: " . $base_url . "/datatables-kom-sdgpk.php");
        exit();
    } else {
        echo "Error: " . $stmt1->error;
    }

    // Close statements
    $stmt1->close();
    $stmt_check->close();
}

// Menutup koneksi database
$conn->close();
?>