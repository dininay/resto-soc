<?php
// Include PHPMailer library files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Hanya jika menggunakan Composer

// Inisialisasi PHPMailer
$mail = new PHPMailer(true);
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_kom"])) {
    $id = $_POST["id"];
    var_dump($id);
    $status_kom = $_POST["status_kom"];
    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    
    $submit_legal = null;
    $obstacle = null;
    $kronologi = null;

    // Periksa apakah file kronologi ada dalam $_FILES
    if (isset($_FILES["kronologi"])) {
        $kronologi_paths = array();
        foreach ($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                $kronologi_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        // Join all file paths into a comma-separated string
        $kronologi = implode(",", $kronologi_paths);
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        $start_konstruksi = date("Y-m-d H:i:s");

        if ($status_kom == 'Done') {
            // Query untuk memperbarui status_kom dan kom_date di tabel resto
            $sql_update = "UPDATE resto SET status_kom = ?, kom_date = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $status_kom, $start_konstruksi, $id);

            // Eksekusi query update
            if ($stmt_update->execute() === TRUE) {
                // Ambil data dari tabel resto berdasarkan id yang diedit
                $sql_get_data = "SELECT kode_lahan, start_konstruksi, gostore_date FROM resto WHERE id = ?";
                $stmt_get_data = $conn->prepare($sql_get_data);
                $stmt_get_data->bind_param("i", $id);
                $stmt_get_data->execute();
                $stmt_get_data->store_result();

                if ($stmt_get_data->num_rows > 0) {
                    $stmt_get_data->bind_result($kode_lahan, $start_konstruksi, $gostore_date);
                    $stmt_get_data->fetch();

                    // Ambil SLA dari tabel master_sla untuk divisi ST-EQP
                    $sql_sla_steqp = "SELECT sla FROM master_sla WHERE divisi = 'ST-EQP'";
                    $result_sla_steqp = $conn->query($sql_sla_steqp);
                    if ($result_sla_steqp->num_rows > 0) {
                        $row_sla_steqp = $result_sla_steqp->fetch_assoc();
                        $hari_sla_steqp = $row_sla_steqp['sla'];
                        $sla_steqp = date("Y-m-d", strtotime("$start_konstruksi + $hari_sla_steqp days"));
                        $sla_eqpdev = date("Y-m-d", strtotime("$start_konstruksi + $hari_sla_steqp days"));
                        $sla_eqpdevprocur = date("Y-m-d", strtotime("$start_konstruksi + $hari_sla_steqp days"));
                        $sla_eqpsite = date("Y-m-d", strtotime("$start_konstruksi + $hari_sla_steqp days"));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi ST-EQP.";
                        exit;
                    }

                    // Ambil SLA dari tabel master_sla untuk divisi ST-Konstruksi
                    $sql_sla_stkonstruksi = "SELECT sla FROM master_sla WHERE divisi = 'ST-Konstruksi'";
                    $result_sla_stkonstruksi = $conn->query($sql_sla_stkonstruksi);
                    if ($result_sla_stkonstruksi->num_rows > 0) {
                        $row_sla_stkonstruksi = $result_sla_stkonstruksi->fetch_assoc();
                        $hari_sla_stkonstruksi = $row_sla_stkonstruksi['sla'];
                        $sla_stkonstruksi = date("Y-m-d", strtotime("$start_konstruksi + $hari_sla_stkonstruksi days"));
                    } else {
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi ST-Konstruksi.";
                        exit;
                    }

                    // Update sla_steqp, sla_stkonstruksi, status_steqp, status_stkonstruksi, status_land, status_gostore di tabel resto
                    $sql_update_resto = "UPDATE resto SET status_stkonstruksi = ?, sla_stkonstruksi = ? WHERE id = ?";
                    $stmt_update_resto = $conn->prepare($sql_update_resto);
                    $status_stkonstruksi = "In Process";
                    $stmt_update_resto->bind_param("ssi", $status_stkonstruksi, $sla_stkonstruksi, $id);

                    // Execute update for resto
                    if ($stmt_update_resto->execute() === TRUE) {
                        // Insert into equipment table
                        $sql_update_eqp = "INSERT INTO equipment (kode_lahan, sla_steqp, status_steqp, status_eqpdev, sla_eqpdev, sla_eqpdevprocur, sla_eqpsite, status_woeqp, status_eqpsite) VALUES (?,?,?,?,?,?,?,?,?)";
                        $stmt_update_eqp = $conn->prepare($sql_update_eqp);
                        $status_steqp = "In Process";
                        $status_eqpdev = "In Process";
                        $status_eqpsite = "In Process";
                        $status_woeqp = "In Process";
                        $stmt_update_eqp->bind_param("sssssssss", $kode_lahan, $sla_steqp, $status_steqp, $status_eqpdev, $sla_eqpdev, $sla_eqpdevprocur, $sla_eqpsite, $status_woeqp, $status_eqpsite);
                        $stmt_update_eqp->execute();

                        // Insert into summary_soc table
                        $status_defect = "Not Yet";
                        $sql_issue = "INSERT INTO issue (kode_lahan, status_defect) VALUES (?,?)";
                        $stmt_issue = $conn->prepare($sql_issue);
                        $stmt_issue->bind_param("ss", $kode_lahan, $status_defect);
                        $stmt_issue->execute();

                        // Insert into summary_soc table
                        $sql_summary = "INSERT INTO summary_soc (kode_lahan) VALUES (?)";
                        $stmt_summary = $conn->prepare($sql_summary);
                        $stmt_summary->bind_param("s", $kode_lahan);
                        $stmt_summary->execute();
                    }

                    // Eksekusi query update
                    if ($stmt_summary->execute() === TRUE) {
                        // Ambil SLA dari tabel master_sla untuk divisi Konstruksi
                        $sql_sla_konstruksi = "SELECT sla FROM master_sla WHERE divisi = 'Konstruksi'";
                        $result_sla_konstruksi = $conn->query($sql_sla_konstruksi);

                        if ($result_sla_konstruksi->num_rows > 0) {
                            $row_sla_konstruksi = $result_sla_konstruksi->fetch_assoc();
                            $sla_days = $row_sla_konstruksi['sla'];

                            // Hitung sla_consact sebagai start_konstruksi ditambah dengan SLA Konstruksi
                            $start_konstruksi_date = new DateTime($start_konstruksi);
                            $start_konstruksi_date->modify("+$sla_days days");
                            $sla_consact = $start_konstruksi_date->format('Y-m-d');

                            // Insert data ke tabel sdg_pk
                            $sql_insert_sdg_pk = "UPDATE sdg_pk set status_consact = ? WHERE kode_lahan = ?";
                            $stmt_insert_sdg_pk = $conn->prepare($sql_insert_sdg_pk);
                            $status_consact = "In Process";
                            $stmt_insert_sdg_pk->bind_param("ss", $status_consact, $kode_lahan);

                            if ($stmt_insert_sdg_pk->execute() === TRUE) {
                            $sql_insert_konstruksi = "INSERT INTO konstruksi (kode_lahan) VALUES (?)";
                            $stmt_insert_konstruksi = $conn->prepare($sql_insert_konstruksi);
                            $stmt_insert_konstruksi->bind_param("s", $kode_lahan);

                                // Eksekusi query insert sdg_pk
                                if ($stmt_insert_konstruksi->execute() === TRUE) {
                                    // Hitung sla_fat sebagai gostore_date dikurangi dengan SLA RTO dan SLA FAT
                                    $sql_sla_rto = "SELECT sla FROM master_slacons WHERE divisi = 'rto'";
                                    $result_sla_rto = $conn->query($sql_sla_rto);
                                    if ($result_sla_rto->num_rows > 0) {
                                        $row_sla_rto = $result_sla_rto->fetch_assoc();
                                        $hari_sla_rto = $row_sla_rto['sla'];

                                        $sql_sla_fat = "SELECT sla FROM master_slacons WHERE divisi = 'fat'";
                                        $result_sla_fat = $conn->query($sql_sla_fat);
                                        if ($result_sla_fat->num_rows > 0) {
                                            $row_sla_fat = $result_sla_fat->fetch_assoc();
                                            $hari_sla_fat = $row_sla_fat['sla'];

                                            $sla_fat_days = $hari_sla_rto + $hari_sla_fat;
                                            $sla_fat = date("Y-m-d", strtotime("$gostore_date - $sla_fat_days days"));

                                            // Insert data ke tabel socdate_fat
                                            $sql_insert_socdate_fat = "INSERT INTO socdate_fat (kode_lahan, status_fat, sla_fat) VALUES (?, ?, ?)";
                                            $stmt_insert_socdate_fat = $conn->prepare($sql_insert_socdate_fat);
                                            $status_fat = "In Process";
                                            $stmt_insert_socdate_fat->bind_param("sss", $kode_lahan, $status_fat, $sla_fat);

                                            // Eksekusi query insert socdate_fat
                                            if ($stmt_insert_socdate_fat->execute() === TRUE) {
                                            // Hitung sla_it sebagai start_konstruksi ditambah dengan SLA IT
                                                $sql_sla_it = "SELECT sla FROM master_slacons WHERE divisi = 'it'";
                                                $result_sla_it = $conn->query($sql_sla_it);
                                                if ($result_sla_it->num_rows > 0) {
                                                    $row_sla_it = $result_sla_it->fetch_assoc();
                                                    $hari_sla_it = $row_sla_it['sla'];
                                                    $sla_it_days = $hari_sla_rto + $hari_sla_it;
                                                    $sla_it = date("Y-m-d", strtotime("$gostore_date - $sla_it_days days"));

                                                    // Hitung sla_itconfig sebagai start_konstruksi ditambah dengan SLA IT Config
                                                    $sql_sla_itconfig = "SELECT sla FROM master_slacons WHERE divisi = 'it_config'";
                                                    $result_sla_itconfig = $conn->query($sql_sla_itconfig);
                                                    if ($result_sla_itconfig->num_rows > 0) {
                                                        $row_sla_itconfig = $result_sla_itconfig->fetch_assoc();
                                                        $hari_sla_itconfig = $row_sla_itconfig['sla'];
                                                        $sla_itconfig_days = $hari_sla_rto + $hari_sla_itconfig;
                                                        $sla_itconfig = date("Y-m-d", strtotime("$gostore_date - $sla_itconfig_days days"));

                                                        // Insert data ke tabel socdate_it dan socdate_itconfig
                                                        $sql_insert_socdate_it = "INSERT INTO socdate_it (kode_lahan, status_it, status_itconfig, sla_it, sla_itconfig) VALUES (?, ?, ?, ?, ?)";
                                                        $stmt_insert_socdate_it = $conn->prepare($sql_insert_socdate_it);
                                                        $status_it = "In Process";
                                                        $status_itconfig = "In Process";
                                                        $stmt_insert_socdate_it->bind_param("sssss", $kode_lahan, $status_it, $status_itconfig, $sla_it, $sla_itconfig);

                                                            // Eksekusi query insert socdate_itconfig
                                                            if ($stmt_insert_socdate_it->execute() === TRUE) {
                                                                // Hitung sla_legal, status_legal, sla_permit, status_permit berdasarkan divisi legal dan legal_permit
                                                                $sql_sla_legal = "SELECT sla FROM master_slacons WHERE divisi = 'legal'";
                                                                $result_sla_legal = $conn->query($sql_sla_legal);
                                                                if ($result_sla_legal->num_rows > 0) {
                                                                    $row_sla_legal = $result_sla_legal->fetch_assoc();
                                                                    $hari_sla_legal = $row_sla_legal['sla'];
                                                                    $sla_legal_days = $hari_sla_rto + $hari_sla_legal;
                                                                    $sla_legal = date("Y-m-d", strtotime("$gostore_date - $sla_legal_days days"));
                                                                    $status_legal = "In Process";

                                                                    // // Hitung sla_permit berdasarkan divisi legal_permit
                                                                    // $sql_sla_permit = "SELECT sla FROM master_slacons WHERE divisi = 'legal_permit'";
                                                                    // $result_sla_permit = $conn->query($sql_sla_permit);
                                                                    // if ($result_sla_permit->num_rows > 0) {
                                                                    //     $row_sla_permit = $result_sla_permit->fetch_assoc();
                                                                    //     $hari_sla_permit = $row_sla_permit['sla'];
                                                                    //     $sla_permit_days = $hari_sla_rto + $hari_sla_permit;
                                                                    //     $sla_permit = date("Y-m-d", strtotime("$gostore_date - $sla_permit_days days"));
                                                                    //     $status_permit = "In Process";

                                                                        // Insert data ke tabel socdate_legal
                                                                        $sql_insert_socdate_legal = "INSERT INTO socdate_legal (kode_lahan, status_legal, sla_legal) VALUES (?, ?, ?)";
                                                                        $stmt_insert_socdate_legal = $conn->prepare($sql_insert_socdate_legal);
                                                                        $stmt_insert_socdate_legal->bind_param("sss", $kode_lahan, $status_legal, $sla_legal);

                                                                        // Eksekusi query insert socdate_legal
                                                                        if ($stmt_insert_socdate_legal->execute() === TRUE) {
                                                                            // Hitung sla_scm sebagai start_konstruksi ditambah dengan SLA SCM
                                                                            $sql_sla_scm = "SELECT sla FROM master_slacons WHERE divisi = 'scm'";
                                                                            $result_sla_scm = $conn->query($sql_sla_scm);
                                                                            if ($result_sla_scm->num_rows > 0) {
                                                                                $row_sla_scm = $result_sla_scm->fetch_assoc();
                                                                                $hari_sla_scm = $row_sla_scm['sla'];
                                                                                $sla_scm_days = $hari_sla_rto + $hari_sla_scm;
                                                                                $sla_scm = date("Y-m-d", strtotime("$gostore_date - $sla_scm_days days"));

                                                                                // Insert data ke tabel socdate_scm
                                                                                $sql_insert_socdate_scm = "INSERT INTO socdate_scm (kode_lahan, status_scm, sla_scm) VALUES (?, ?, ?)";
                                                                                $stmt_insert_socdate_scm = $conn->prepare($sql_insert_socdate_scm);
                                                                                $status_scm = "In Process";
                                                                                $stmt_insert_socdate_scm->bind_param("sss", $kode_lahan, $status_scm, $sla_scm);

                                                                                // Eksekusi query insert socdate_legal
                                                                                if ($stmt_insert_socdate_scm->execute() === TRUE) {
                                                                                    // Hitung sla_scm sebagai start_konstruksi ditambah dengan SLA SCM
                                                                                    $sql_sla_marketing = "SELECT sla FROM master_slacons WHERE divisi = 'marketing'";
                                                                                    $result_sla_marketing = $conn->query($sql_sla_marketing);
                                                                                    if ($result_sla_marketing->num_rows > 0) {
                                                                                        $row_sla_marketing = $result_sla_marketing->fetch_assoc();
                                                                                        $hari_sla_marketing = $row_sla_marketing['sla'];
                                                                                        $sla_marketing_days = $hari_sla_rto + $hari_sla_marketing;
                                                                                        $sla_marketing = date("Y-m-d", strtotime("$gostore_date - $sla_marketing_days days"));

                                                                                        // Insert data ke tabel socdate_marketing
                                                                                        $sql_insert_socdate_marketing = "INSERT INTO socdate_marketing (kode_lahan, status_marketing, sla_marketing) VALUES (?, ?, ?)";
                                                                                        $stmt_insert_socdate_marketing = $conn->prepare($sql_insert_socdate_marketing);
                                                                                        $status_marketing = "In Process";
                                                                                        $stmt_insert_socdate_marketing->bind_param("sss", $kode_lahan, $status_marketing, $sla_marketing);

                                                                                        // Eksekusi query insert socdate_scm
                                                                                        if ($stmt_insert_socdate_marketing->execute() === TRUE) {
                                                                                            // Hitung sla_sdg sebagai start_konstruksi ditambah dengan SLA SDG
                                                                                            $sql_sla_sdg = "SELECT sla FROM master_slacons WHERE divisi = 'mep'";
                                                                                            $result_sla_sdg = $conn->query($sql_sla_sdg);
                                                                                            if ($result_sla_sdg->num_rows > 0) {
                                                                                                $row_sla_sdg = $result_sla_sdg->fetch_assoc();
                                                                                                $hari_sla_sdg = $row_sla_sdg['sla'];
                                                                                                $sla_sdg_days = $hari_sla_rto + $hari_sla_sdg;
                                                                                                $sla_sdg = date("Y-m-d", strtotime("$gostore_date - $sla_sdg_days days"));

                                                                                                // Insert data ke tabel socdate_sdg
                                                                                                $sql_insert_socdate_sdg = "INSERT INTO socdate_sdg (kode_lahan, status_sdgsumber, status_sdglistrik, status_sdgipal, sla_mep) VALUES (?, ?, ?, ?, ?)";
                                                                                                $stmt_insert_socdate_sdg = $conn->prepare($sql_insert_socdate_sdg);
                                                                                                $status_sdgsumber = "In Process";
                                                                                                $status_sdglistrik = "In Process";
                                                                                                $status_sdgipal = "In Process";
                                                                                                $stmt_insert_socdate_sdg->bind_param("sssss", $kode_lahan, $status_sdgsumber, $status_sdglistrik, $status_sdgipal, $sla_sdg);

                                                                                                // Eksekusi query insert socdate_scm
                                                                                                if ($stmt_insert_socdate_sdg->execute() === TRUE) {
                                                                                                    // Hitung sla_sdg sebagai start_konstruksi ditambah dengan SLA SDG
                                                                                                    $sql_sla_ir = "SELECT sla FROM master_slacons WHERE divisi = 'ir'";
                                                                                                    $result_sla_ir = $conn->query($sql_sla_ir);
                                                                                                    if ($result_sla_ir->num_rows > 0) {
                                                                                                        $row_sla_ir = $result_sla_ir->fetch_assoc();
                                                                                                        $hari_sla_ir = $row_sla_ir['sla'];
                                                                                                        $sla_ir_days = $hari_sla_rto + $hari_sla_ir;
                                                                                                        $sla_ir = date("Y-m-d", strtotime("$gostore_date - $sla_ir_days days"));

                                                                                                        // Insert data ke tabel socdate_ir
                                                                                                        $sql_insert_socdate_ir = "INSERT INTO socdate_ir (kode_lahan, status_ir, sla_ir) VALUES (?, ?, ?)";
                                                                                                        $stmt_insert_socdate_ir = $conn->prepare($sql_insert_socdate_ir);
                                                                                                        $status_ir = "In Process";
                                                                                                        $stmt_insert_socdate_ir->bind_param("sss", $kode_lahan, $status_ir, $sla_ir);

                                                                                                        // Eksekusi query insert socdate_itconfig
                                                                                                        if ($stmt_insert_socdate_ir->execute() === TRUE) {
                                                                                                            // Hitung sla_legal, status_legal, sla_permit, status_permit berdasarkan divisi legal dan legal_permit
                                                                                                            $sql_sla_ff1 = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_ff1'";
                                                                                                            $result_sla_ff1 = $conn->query($sql_sla_ff1);
                                                                                                            if ($result_sla_ff1->num_rows > 0) {
                                                                                                                $row_sla_ff1 = $result_sla_ff1->fetch_assoc();
                                                                                                                $hari_sla_ff1 = $row_sla_ff1['sla'];
                                                                                                                $sla_ff1_days = $hari_sla_rto + $hari_sla_ff1;
                                                                                                                $sla_ff1 = date("Y-m-d", strtotime("$gostore_date - $sla_ff1_days days"));
                                                                                                                $status_ff1 = "In Process";

                                                                                                                // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                $sql_sla_ff2 = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_ff2'";
                                                                                                                $result_sla_ff2 = $conn->query($sql_sla_ff2);
                                                                                                                if ($result_sla_ff2->num_rows > 0) {
                                                                                                                    $row_sla_ff2 = $result_sla_ff2->fetch_assoc();
                                                                                                                    $hari_sla_ff2 = $row_sla_ff2['sla'];
                                                                                                                    $sla_ff2_days = $hari_sla_rto + $hari_sla_ff2;
                                                                                                                    $sla_ff2 = date("Y-m-d", strtotime("$gostore_date - $sla_ff2_days days"));
                                                                                                                    $status_ff2 = "In Process";

                                                                                                                    // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                    $sql_sla_ff3 = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_ff3'";
                                                                                                                    $result_sla_ff3 = $conn->query($sql_sla_ff3);
                                                                                                                    if ($result_sla_ff3->num_rows > 0) {
                                                                                                                        $row_sla_ff3 = $result_sla_ff3->fetch_assoc();
                                                                                                                        $hari_sla_ff3 = $row_sla_ff3['sla'];
                                                                                                                        $sla_ff3_days = $hari_sla_rto + $hari_sla_ff3;
                                                                                                                        $sla_ff3 = date("Y-m-d", strtotime("$gostore_date - $sla_ff3_days days"));
                                                                                                                        $status_ff3 = "In Process";

                                                                                                                        // // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                        // $sql_sla_tm = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_tm'";
                                                                                                                        // $result_sla_tm = $conn->query($sql_sla_tm);
                                                                                                                        // if ($result_sla_tm->num_rows > 0) {
                                                                                                                        //     $row_sla_tm = $result_sla_tm->fetch_assoc();
                                                                                                                        //     $hari_sla_tm = $row_sla_tm['sla'];
                                                                                                                        //     $sla_tm_days = $hari_sla_rto + $hari_sla_tm;
                                                                                                                        //     $sla_tm = date("Y-m-d", strtotime("$gostore_date - $sla_tm_days days"));
                                                                                                                        //     $status_tm = "In Process";

                                                                                                                        //     // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                        //     $sql_sla_fl = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_fl'";
                                                                                                                        //     $result_sla_fl = $conn->query($sql_sla_fl);
                                                                                                                        //     if ($result_sla_fl->num_rows > 0) {
                                                                                                                        //         $row_sla_fl = $result_sla_fl->fetch_assoc();
                                                                                                                        //         $hari_sla_fl = $row_sla_fl['sla'];
                                                                                                                        //         $sla_fl_days = $hari_sla_rto + $hari_sla_fl;
                                                                                                                        //         $sla_fl = date("Y-m-d", strtotime("$gostore_date - $sla_fl_days days"));
                                                                                                                        //         $status_fl = "In Process";

                                                                                                                                // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                                $sql_sla_hot = "SELECT sla FROM master_slacons WHERE divisi = 'hot'";
                                                                                                                                $result_sla_hot = $conn->query($sql_sla_hot);
                                                                                                                                if ($result_sla_hot->num_rows > 0) {
                                                                                                                                    $row_sla_hot = $result_sla_hot->fetch_assoc();
                                                                                                                                    $hari_sla_hot = $row_sla_hot['sla'];
                                                                                                                                    $sla_hot_days = $hari_sla_rto + $hari_sla_hot;
                                                                                                                                    $sla_hot = date("Y-m-d", strtotime("$gostore_date - $sla_hot_days days"));
                                                                                                                                    $status_hot = "In Process";

                                                                                                                                        // Insert data ke tabel socdate_legal
                                                                                                                                        $sql_insert_socdate_hr = "UPDATE socdate_hr SET status_ff1 = ?, status_ff2 = ?, status_ff3 = ?, status_hot = ?, sla_ff1 = ?, sla_ff2 = ?, sla_ff3 = ?, sla_hot = ? WHERE kode_lahan = ?";
                                                                                                                                        $stmt_insert_socdate_hr = $conn->prepare($sql_insert_socdate_hr);
                                                                                                                                        $stmt_insert_socdate_hr->bind_param("sssssssss", $status_ff1, $status_ff2, $status_ff3, $status_hot, $sla_ff1, $sla_ff2, $sla_ff3, $sla_hot, $kode_lahan);
                                                                                                                                        
                                                                                                                                        // Eksekusi query insert socdate_itconfig
                                                                                                                                        if ($stmt_insert_socdate_hr->execute() === TRUE) {
                                                                                                                                            // Hitung sla_legal, status_legal, sla_permit, status_permit berdasarkan divisi legal dan legal_permit
                                                                                                                                            $sql_sla_kpt1 = "SELECT sla FROM master_slacons WHERE divisi = 'kpt1'";
                                                                                                                                            $result_sla_kpt1 = $conn->query($sql_sla_kpt1);
                                                                                                                                            if ($result_sla_kpt1->num_rows > 0) {
                                                                                                                                                $row_sla_kpt1 = $result_sla_kpt1->fetch_assoc();
                                                                                                                                                $hari_sla_kpt1 = $row_sla_kpt1['sla'];
                                                                                                                                                $sla_kpt1_days = $hari_sla_rto + $hari_sla_kpt1;
                                                                                                                                                $sla_kpt1 = date("Y-m-d", strtotime("$gostore_date - $sla_kpt1_days days"));
                                                                                                                                                $status_kpt1 = "In Process";

                                                                                                                                                // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                                                $sql_sla_kpt2 = "SELECT sla FROM master_slacons WHERE divisi = 'kpt2'";
                                                                                                                                                $result_sla_kpt2 = $conn->query($sql_sla_kpt2);
                                                                                                                                                if ($result_sla_kpt2->num_rows > 0) {
                                                                                                                                                    $row_sla_kpt2 = $result_sla_kpt2->fetch_assoc();
                                                                                                                                                    $hari_sla_kpt2 = $row_sla_kpt2['sla'];
                                                                                                                                                    $sla_kpt2_days = $hari_sla_rto + $hari_sla_kpt2;
                                                                                                                                                    $sla_kpt2 = date("Y-m-d", strtotime("$gostore_date - $sla_kpt2_days days"));
                                                                                                                                                    $status_kpt2 = "In Process";

                                                                                                                                                    // Hitung sla_permit berdasarkan divisi legal_permit
                                                                                                                                                    $sql_sla_kpt3 = "SELECT sla FROM master_slacons WHERE divisi = 'kpt3'";
                                                                                                                                                    $result_sla_kpt3 = $conn->query($sql_sla_kpt3);
                                                                                                                                                    if ($result_sla_kpt3->num_rows > 0) {
                                                                                                                                                        $row_sla_kpt3 = $result_sla_kpt3->fetch_assoc();
                                                                                                                                                        $hari_sla_kpt3 = $row_sla_kpt3['sla'];
                                                                                                                                                        $sla_kpt3_days = $hari_sla_rto + $hari_sla_kpt3;
                                                                                                                                                        $sla_kpt3 = date("Y-m-d", strtotime("$gostore_date - $sla_kpt3_days days"));
                                                                                                                                                        $status_kpt3 = "In Process";

                                                                                                                                                     
                                                                                                                                                        // Insert data ke tabel socdate_legal
                                                                                                                                                        $sql_insert_socdate_academy = "INSERT INTO socdate_academy (kode_lahan, status_kpt1, status_kpt2, status_kpt3, sla_kpt1, sla_kpt2, sla_kpt3) VALUES (?, ?, ?, ?, ?, ?, ?)";
                                                                                                                                                        $stmt_insert_socdate_academy = $conn->prepare($sql_insert_socdate_academy);
                                                                                                                                                        $stmt_insert_socdate_academy->bind_param("sssssss", $kode_lahan, $status_kpt1, $status_kpt2, $status_kpt3, $sla_kpt1, $sla_kpt2, $sla_kpt3);
                                                                                                                                                        
                                                                                                                                                        if ($stmt_insert_socdate_academy->execute() === TRUE) {
                                                                                                                                                        $conn->commit();
                                                                                                                                                        echo "Status dan data berhasil diperbarui.";
                                                                                                                                                        } else {
                                                                                                                                                            $conn->rollback();
                                                                                                                                                            echo "Error: " . "$stmt_insert_socdate_academy->error";
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $conn->rollback();
                                                                                                                                                        echo "Error: Data SLA tidak ditemukan untuk divisi kpt3.";
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $conn->rollback();
                                                                                                                                                    echo "Error: Data SLA tidak ditemukan untuk divisi kpt2.";
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $conn->rollback();
                                                                                                                                                echo "Error: Data SLA tidak ditemukan untuk divisi kpt1.";
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            $conn->rollback();
                                                                                                                                            echo "Error: " . "$stmt_insert_socdate_hr->error";
                                                                                                                                        }
                                                                                                                                } else {
                                                                                                                                    $conn->rollback();
                                                                                                                                    echo "Error: Data SLA tidak ditemukan untuk divisi hot.";
                                                                                                                                }
                                                                                                                            // } else {
                                                                                                                            //     $conn->rollback();
                                                                                                                            //     echo "Error: Data SLA tidak ditemukan untuk divisi fl.";
                                                                                                                            // }
                                                                                                                            // } else {
                                                                                                                            //     $conn->rollback();
                                                                                                                            //     echo "Error: Data SLA tidak ditemukan untuk divisi tm.";
                                                                                                                            // }
                                                                                                                    } else {
                                                                                                                        $conn->rollback();
                                                                                                                        echo "Error: Data SLA tidak ditemukan untuk divisi ff3.";
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $conn->rollback();
                                                                                                                    echo "Error: Data SLA tidak ditemukan untuk divisi ff2.";
                                                                                                                }
                                                                                                            } else {
                                                                                                                $conn->rollback();
                                                                                                                echo "Error: Data SLA tidak ditemukan untuk divisi ff1.";
                                                                                                            }
                                                                                                        } else {
                                                                                                            $conn->rollback();
                                                                                                            echo "Error: " . $stmt_insert_socdate_ir->error;
                                                                                                        }
                                                                                                    } else {
                                                                                                        $conn->rollback();
                                                                                                        echo "Error: Data SLA tidak ditemukan untuk divisi ir.";
                                                                                                    }
                                                                                                } else {
                                                                                                    $conn->rollback();
                                                                                                    echo "Error: " . $stmt_insert_socdate_sdg->error;
                                                                                                }
                                                                                            } else {
                                                                                                $conn->rollback();
                                                                                                echo "Error: Data SLA tidak ditemukan untuk divisi SDG.";
                                                                                            }
                                                                                        } else {
                                                                                            $conn->rollback();
                                                                                            echo "Error: " . $stmt_insert_socdate_marketing->error;
                                                                                        }
                                                                                    } else {
                                                                                        $conn->rollback();
                                                                                        echo "Error: Data SLA tidak ditemukan untuk divisi Marketing.";
                                                                                    }
                                                                                } else {
                                                                                    $conn->rollback();
                                                                                    echo "Error: " . $stmt_insert_socdate_scm->error;
                                                                                }
                                                                            } else {
                                                                                $conn->rollback();
                                                                                echo "Error: Data SLA tidak ditemukan untuk divisi SCM.";
                                                                            }
                                                                        } else {
                                                                            $conn->rollback();
                                                                            echo "Error: " . $stmt_insert_socdate_legal->error;
                                                                        }
                                                                    // } else {
                                                                    //     $conn->rollback();
                                                                    //     echo "Error: Data SLA tidak ditemukan untuk divisi Legal Permit.";
                                                                    // }
                                                                } else {
                                                                    $conn->rollback();
                                                                    echo "Error: Data SLA tidak ditemukan untuk divisi Legal.";
                                                                }
                                                        } else {
                                                            $conn->rollback();
                                                            echo "Error: " . $stmt_insert_socdate_it->error;
                                                        }
                                                    } else {
                                                        $conn->rollback();
                                                        echo "Error: Data SLA tidak ditemukan untuk divisi IT Config.";
                                                    }
                                                } else {
                                                    $conn->rollback();
                                                    echo "Error: Data SLA tidak ditemukan untuk divisi IT.";
                                                }
                                                } else {
                                                    $conn->rollback();
                                                    echo "Error: " . $stmt_insert_socdate_fat->error;
                                                }
                                            } else {
                                                $conn->rollback();
                                                echo "Error: Data SLA tidak ditemukan untuk divisi FAT.";
                                            }
                                        } else {
                                            $conn->rollback();
                                            echo "Error: Data SLA tidak ditemukan untuk divisi RTO.";
                                        }
                                    } else {
                                        $conn->rollback();
                                        echo "Error: " . $stmt_insert_konstruksi->error;
                                    }
                                } else {
                                    $conn->rollback();
                                    echo "Error: " . $stmt_insert_sdg_pk->error;
                                }
                            } else {
                                $conn->rollback();
                                echo "Error: Data SLA tidak ditemukan untuk divisi Konstruksi.";
                            }
                        } else {
                            $conn->rollback();
                            echo "Error: " . $stmt_summary->error;
                        }
                    } else {
                        $conn->rollback();
                        echo "Error: Kode lahan tidak ditemukan untuk id $id.";
                    }
                    
                    // Periksa apakah kode_lahan ada di tabel hold_project
                    $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                    $stmt_check_hold = $conn->prepare($sql_check_hold);
                    $stmt_check_hold->bind_param("i", $id);
                    $stmt_check_hold->execute();
                    $stmt_check_hold->store_result();

                    if ($stmt_check_hold->num_rows > 0) {
                        // Jika kode_lahan ada di hold_project, update status_hold menjadi 'Done'
                        $status_hold = 'Done';
                        $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = (SELECT kode_lahan FROM procurement WHERE id = ?)";
                        $stmt_update_hold = $conn->prepare($sql_update_hold);
                        $stmt_update_hold->bind_param("si", $status_hold, $id);
                        $stmt_update_hold->execute();
                    }

                    // Komit transaksi
                    $conn->commit();
                    echo "Status berhasil diperbarui.";
                } else {
                    // Rollback jika query update gagal
                    $conn->rollback();
                    echo "Error: " . $stmt_update->error;
                }
            
                try {
                    // Pengaturan server SMTP
                    $mail->isSMTP();
                    $mail->Host = 'sandbox.smtp.mailtrap.io';  // Ganti dengan SMTP server Anda
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ff811f556f5d12'; // Ganti dengan email Anda
                    $mail->Password = 'c60c92868ce0f8'; // Ganti dengan password email Anda
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 2525;
                    
                    // Pengaturan pengirim dan penerima
                    $mail->setFrom('resto-soc@gacoan.com', 'Resto SOC');
            
                    // Query untuk mendapatkan email pengguna dengan level "Real Estate"
                    $sql = "SELECT email FROM user WHERE level IN ('SDG-Project','SDG-Equipment','PMO','HR','Academy','SCM','IT','Marketing','TAF','IR')";
                    $result = $conn->query($sql);
            
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $email = $row['email'];
                    
                            // Validasi format email sebelum menambahkannya sebagai penerima
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $mail->addAddress($email); // Tambahkan setiap penerima email
                                
                                // Konten email
                                $mail->isHTML(true);
                                $mail->Subject = 'Notification: 1 New Active Resto SOC Ticket';
                                $mail->Body    = '
                                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                    <div style="background-color: #f7f7f7; padding: 20px; border-radius: 8px;">
                                        <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear Team,</h2>
                                        <p>You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.</p>
                                        <p>Thank you for your prompt attention to this matter.</p>
                                        <p></p>
                                        <p>Best regards,</p>
                                        <p>Resto - SOC</p>
                                    </div>
                                </div>';
                                $mail->AltBody = 'Dear Team,'
                                               . 'You have 1 New Active Resto SOC Ticket in the Resto SOC system. Please log in to the SOC application to review the details.'
                                               . 'Thank you for your prompt attention to this matter.'
                                               . 'Best regards,'
                                               . 'Resto - SOC';
                    
                                // Kirim email
                                $mail->send();
                                $mail->clearAddresses(); // Hapus semua penerima sebelum loop berikutnya
                            } else {
                                echo "Invalid email format: " . $email;
                            }
                            }
                        } else {
                            echo "No emails found.";
                        }
            
                    } catch (Exception $e) {
                        echo "Email tidak dapat dikirim. Error: {$mail->ErrorInfo}";
                    }

        } elseif ($status_kom == 'Pending') {
            // Ambil kode_lahan dari tabel procurement
            $sql_get_kode_lahan = "SELECT kode_lahan FROM procurement WHERE id = ?";
            $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
            $stmt_get_kode_lahan->bind_param("i", $id);
            $stmt_get_kode_lahan->execute();
            $stmt_get_kode_lahan->store_result();

            if ($stmt_get_kode_lahan->num_rows > 0) {
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();

                // Query untuk memperbarui status_kom dan kom_date di tabel resto
                $sql_update_pending = "UPDATE resto SET status_kom = ?, kom_date = ? WHERE id = ?";
                $stmt_update_pending = $conn->prepare($sql_update_pending);
                $stmt_update_pending->bind_param("ssi", $status_kom, $start_konstruksi, $id);

                // Eksekusi query update
                if ($stmt_update_pending->execute() === TRUE) {
                    $status_hold = "In Process";
                    $due_date = date("Y-m-d H:i:s");

                    // Query untuk memasukkan data ke dalam tabel hold_project
                    $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt_hold = $conn->prepare($sql_hold);
                    $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                    $stmt_hold->execute();

                    // Komit transaksi
                    $conn->commit();
                    echo "Status berhasil diperbarui dan data ditahan.";
                } else {
                    // Rollback jika query update gagal
                    $conn->rollback();
                    echo "Error: " . $stmt_update_pending->error;
                }
            } else {
                $conn->rollback();
                echo "Error: Kode lahan tidak ditemukan untuk id $id.";
                exit;
            }
        } else {
            // Jika status tidak diubah menjadi Approve atau Pending, hanya perlu memperbarui status_kom dan kom_date di tabel resto
            $sql_update_other = "UPDATE resto SET status_kom = ?, kom_date = ? WHERE id = ?";
            $stmt_update_other = $conn->prepare($sql_update_other);
            $stmt_update_other->bind_param("ssi", $status_kom, $start_konstruksi, $id);

            // Eksekusi query
            if ($stmt_update_other->execute() === TRUE) {
                $conn->commit();
                echo "Status berhasil diperbarui.";
            } else {
                // Rollback jika query update gagal
                $conn->rollback();
                echo "Error: " . $stmt_update_other->error;
            }
        }
        header("Location: ../datatables-kom-sdgpk.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
