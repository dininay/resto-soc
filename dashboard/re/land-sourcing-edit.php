<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil nilai dari formulir
    $kode_lahan = $_POST["kode_lahan"];
    $nama_lahan = $_POST["nama_lahan"];
    $lokasi = $_POST["lokasi"];
    $nama_pemilik = $_POST["nama_pemilik"];
    $alamat_pemilik = $_POST["alamat_pemilik"];
    $no_tlp = $_POST["no_tlp"];
    $luas_area = $_POST["luas_area"];
    $maps = $_POST["maps"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $harga_sewa = $_POST["harga_sewa"];
    $mintahun_sewa = $_POST["mintahun_sewa"];
    $status_approvre = "Approve";
    $id = $_POST['id'];

    $lamp_land = "";
    if(isset($_FILES["lamp_land"])) {
        $lamp_land_paths = array();

        // Loop through each file
        foreach($_FILES['lamp_land']['name'] as $key => $filename) {
            $file_tmp = $_FILES['lamp_land']['tmp_name'][$key];
            $file_name = $_FILES['lamp_land']['name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file_name);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $lamp_land_paths[] = $file_name;
            } else {
                echo "Gagal mengunggah file " . $file_name . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $lamp_land = implode(",", $lamp_land_paths);
    }

    // Update data di tabel land
    $sql_update_land = "UPDATE land SET kode_lahan = ?, nama_lahan = ?, lokasi = ?, nama_pemilik = ?, alamat_pemilik = ?, no_tlp = ?, luas_area = ?, lamp_land = ?, maps = ?, latitude = ?, longitude = ?, harga_sewa = ?, mintahun_sewa = ?, status_approvre = ? WHERE id = ?";
    $stmt_update_land = $conn->prepare($sql_update_land);
    $stmt_update_land->bind_param("ssssssssssssssi", $kode_lahan, $nama_lahan, $lokasi, $nama_pemilik, $alamat_pemilik, $no_tlp, $luas_area, $lamp_land, $maps, $latitude, $longitude, $harga_sewa, $mintahun_sewa, $status_approvre, $id);

    if ($stmt_update_land->execute() === TRUE) {
        // Cek jika kode_lahan sudah ada di tabel re
        $sql_check_re = "SELECT kode_lahan FROM re WHERE kode_lahan = ?";
        $stmt_check_re = $conn->prepare($sql_check_re);
        $stmt_check_re->bind_param("s", $kode_lahan);
        $stmt_check_re->execute();
        $stmt_check_re->store_result();

        if ($stmt_check_re->num_rows === 0) {
            // Jika kode_lahan tidak ada di tabel re dan status_approvre adalah 'Approve'
            if ($status_approvre == 'Approve') {
                // Ambil data dari tabel land
                $sql_select = "SELECT kode_lahan, re_date FROM land WHERE id = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("i", $id);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                
                if ($row = $result_select->fetch_assoc()) {
                    $sql_select_sla_qs = "SELECT sla FROM master_sla WHERE divisi = 'Owner Surveyor'";
                    $result_sla_qs = $conn->query($sql_select_sla_qs);

                    if ($row_sla_qs = $result_sla_qs->fetch_assoc()) {
                        $sla_days_qs = $row_sla_qs['sla'];
                        $end_date_obj = new DateTime($row['re_date']);
                        $end_date_obj->modify("+$sla_days_qs days");
                        $sla_bod_date = $end_date_obj->format("Y-m-d");
                        $sla_vl_date = $end_date_obj->format("Y-m-d");

                        $sql_insert_re = "INSERT INTO re (kode_lahan, status_approvowner, sla_date, status_vl, slavl_date) VALUES (?,?,?,?,?)";
                        $stmt_insert_re = $conn->prepare($sql_insert_re);
                        $status_approvowner = 'In Process';
                        $status_vl = 'In Process';
                        $stmt_insert_re->bind_param("sssss", $row['kode_lahan'], $status_approvowner, $sla_bod_date, $status_vl, $sla_vl_date);
                        $stmt_insert_re->execute();
                    }

                    $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                    $stmt_check_hold = $conn->prepare($sql_check_hold);
                    $stmt_check_hold->bind_param("s", $row['kode_lahan']);
                    $stmt_check_hold->execute();
                    $stmt_check_hold->store_result();

                    if ($stmt_check_hold->num_rows > 0) {
                        $status_hold = 'Done';
                        $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                        $stmt_update_hold = $conn->prepare($sql_update_hold);
                        $stmt_update_hold->bind_param("ss", $status_hold, $row['kode_lahan']);
                        $stmt_update_hold->execute();
                    }
                }
            }
        } elseif ($status_approvre == 'Pending') {
            $status_hold = "In Process";
            $due_date = date("Y-m-d H:i:s");

            $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_hold = $conn->prepare($sql_hold);
            $stmt_hold->bind_param("sssssss", $kode_lahan, $_POST["issue_detail"], $_POST["pic"], $_POST["action_plan"], $due_date, $status_hold, $_POST["kronologi"]);
            $stmt_hold->execute();
        } elseif ($status_approvre == 'Reject') {
            $sql_reject_land = "UPDATE land SET status_land = 'Reject' WHERE id = ?";
            $stmt_reject_land = $conn->prepare($sql_reject_land);
            $stmt_reject_land->bind_param("i", $id);
            $stmt_reject_land->execute();
        }
        
        echo "<script>
                alert('Status berhasil diperbarui.');
                window.location.href = window.location.href;
             </script>";
    } else {
        echo "Error: " . $stmt_update_land->error;
    }
    header("Location: ../datatables-land-sourcing.php");
exit;
    $stmt_update_land->close();
    $conn->close();
}
?>