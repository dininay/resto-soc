<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai tgl_berlaku dan penanggungjawab dari formulir
    $id = $_POST['id'];
    $kode_lahan = $_POST['kode_lahan'];
    $kode_store = $_POST['kode_store'];
    
    // Periksa apakah kunci 'lampiran' ada dalam $_FILES
    $lamp_signpsm = "";

    if(isset($_FILES["lamp_signpsm"])) {
        $lamp_signpsm_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_signpsm']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_signpsm']['tmp_name'][$key];
            $file_name = $_FILES['lamp_signpsm']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_signpsm_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_signpsm = implode(",", $lamp_signpsm_paths);
    }

    $lamp_draf = "";

    if(isset($_FILES["lamp_draf"])) {
        $lamp_draf_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_draf']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_draf']['tmp_name'][$key];
            $file_name = $_FILES['lamp_draf']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_draf_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_draf = implode(",", $lamp_draf_paths);
    }

    // Update data di tabel dokumen_loacd
    $sql_update_dokumen_loacd = "UPDATE dokumen_loacd SET kode_store = ? WHERE kode_lahan = ?";
    $stmt_update_dokumen_loacd = $conn->prepare($sql_update_dokumen_loacd);
    $stmt_update_dokumen_loacd->bind_param("ss", $kode_store, $kode_lahan);

    if (!$stmt_update_dokumen_loacd->execute()) {
        echo "Error updating dokumen_loacd: " . $stmt_update_dokumen_loacd->error;
    }

    // Update data di tabel draft
    $sql_update_draft = "UPDATE draft SET lamp_signpsm = ?, lamp_draf = ? WHERE id = ?";
    $stmt_update_draft = $conn->prepare($sql_update_draft);
    $stmt_update_draft->bind_param("ssi", $lamp_signpsm, $lamp_draf, $id);

    if (!$stmt_update_draft->execute()) {
        echo "Error updating draft: " . $stmt_update_draft->error;
    } else {
        // Redirect to another page after successful update
        // header("Location: " . $base_url . "/datatables-sign-psm-legal.php");
        // exit();
        echo "<script>
                alert('Data berhasil diperbarui.');
                window.location.href = window.location.href;
             </script>";
    }
    
    header("Location: ../datatables-sign-psm-legal.php");
    exit; // Pastikan tidak ada output lain setelah header redirect
    // Menutup prepared statements
    $stmt_update_dokumen_loacd->close();
    $stmt_update_draft->close();
}

// Menutup koneksi database
$conn->close();
?>
