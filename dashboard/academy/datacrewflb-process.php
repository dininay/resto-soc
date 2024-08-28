<?php
// Koneksi ke database
include "../../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["onboarding_date"]) && isset($_POST["status_last"])) {
    $id = $_POST["id"];
    $onboarding_date = $_POST["onboarding_date"];
    $status_last = $_POST["status_last"];
    $acafl_date = date("Y-m-d H:i:s");

    $issue_detail = isset($_POST["issue_detail"]) ? $_POST["issue_detail"] : null;
    $pic = isset($_POST["pic"]) ? $_POST["pic"] : null;
    $action_plan = isset($_POST["action_plan"]) ? $_POST["action_plan"] : null;
    $kronologi_paths = array();
    if (isset($_FILES["kronologi"])) {
        foreach ($_FILES['kronologi']['name'] as $key => $filename) {
            $file_tmp = $_FILES['kronologi']['tmp_name'][$key];
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($filename);

            if (move_uploaded_file($file_tmp, $target_dir . $target_file)) {
                $kronologi_paths[] = $filename;
            } else {
                echo "Gagal mengunggah file " . $filename . "<br>";
            }
        }

        $kronologi = implode(",", $kronologi_paths);
    } else {
        $kronologi = null;
    }

    $conn->begin_transaction();

    try {
        // Ambil status sebelumnya
        $sql_get_prev_status = "SELECT status_last, kode_lahan FROM crewfl WHERE id = ?";
        $stmt_get_prev_status = $conn->prepare($sql_get_prev_status);
        $stmt_get_prev_status->bind_param("i", $id);
        $stmt_get_prev_status->execute();
        $stmt_get_prev_status->bind_result($prev_status_last, $kode_lahan);
        $stmt_get_prev_status->fetch();
        $stmt_get_prev_status->close();

        // Update status dan data terkait
        $sql_update = "UPDATE crewfl SET status_last = ?, acafl_date = ?, onboarding_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $status_last, $acafl_date, $onboarding_date, $id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            if ($status_last == 'Done' || $status_last == 'Resign') {
                if ($kode_lahan) {
                    $sql_check_hold = "SELECT kode_lahan FROM hold_project WHERE kode_lahan = ?";
                    $stmt_check_hold = $conn->prepare($sql_check_hold);
                    $stmt_check_hold->bind_param("s", $kode_lahan);
                    $stmt_check_hold->execute();
                    $stmt_check_hold->store_result();

                    if ($stmt_check_hold->num_rows > 0) {
                        $status_hold = 'Done';
                        $sql_update_hold = "UPDATE hold_project SET status_hold = ? WHERE kode_lahan = ?";
                        $stmt_update_hold = $conn->prepare($sql_update_hold);
                        $stmt_update_hold->bind_param("ss", $status_hold, $kode_lahan);
                        $stmt_update_hold->execute();
                    }
                    $stmt_check_hold->close();
                    
                    // Tambah atau kurangi angka 1 ke kolom crew_onboardflaca di tabel socdate_hraca
                    if ($prev_status_last == 'Done' && $status_last == 'Resign') {
                        // Decrease crew_onboardflaca by 1
                        $sql_update_crew_onboard = "UPDATE socdate_hraca SET crew_onboardflaca = COALESCE(crew_onboardflaca, 0) - 1 WHERE kode_lahan = ?";
                        $stmt_update_crew_onboard = $conn->prepare($sql_update_crew_onboard);
                        $stmt_update_crew_onboard->bind_param("s", $kode_lahan);
                        $stmt_update_crew_onboard->execute();
        
                        // Increase crew_resignflaca by 1
                        $sql_update_crew_resign = "UPDATE socdate_hraca SET crew_resignflaca = COALESCE(crew_resignflaca, 0) + 1 WHERE kode_lahan = ?";
                        $stmt_update_crew_resign = $conn->prepare($sql_update_crew_resign);
                        $stmt_update_crew_resign->bind_param("s", $kode_lahan);
                        $stmt_update_crew_resign->execute();
                    } else {
                        // Handle other status transitions
                        $sql_update_crew_onboard = "UPDATE socdate_hraca SET crew_onboardflaca = COALESCE(crew_onboardflaca, 0) + 1, crew_allonboardtmaca = COALESCE(crew_allonboardtmaca, 0) + 1 WHERE kode_lahan = ?";
                        $stmt_update_crew_onboard = $conn->prepare($sql_update_crew_onboard);
                        $stmt_update_crew_onboard->bind_param("s", $kode_lahan);
                        $stmt_update_crew_onboard->execute();
                    }
                    // Calculate mortality_tmaca based on crew_resignflaca / crew_allonboardtmaca
                    $sql_calculate_mortality = "UPDATE socdate_hraca SET mortality_flaca = CASE WHEN crew_allonboardflaca > 0 THEN (crew_resignflaca / crew_allonboardflaca) * 100 ELSE 0 END WHERE kode_lahan = ?";
                    $stmt_calculate_mortality = $conn->prepare($sql_calculate_mortality);
                    $stmt_calculate_mortality->bind_param("s", $kode_lahan);
                    $stmt_calculate_mortality->execute();
                    // Step 1: Retrieve SLA for hrga_tm division
                    $sql_get_sla = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_tm'";
                    $result_get_sla = $conn->query($sql_get_sla);
                    $row_sla = $result_get_sla->fetch_assoc();
                    $sla = $row_sla['sla'];

                    // Step 2: Retrieve the actual completion date
                    $sql_get_sla_flaca = "SELECT sla_flaca FROM socdate_hraca WHERE kode_lahan = ?";
                    $stmt_get_sla_flaca = $conn->prepare($sql_get_sla_flaca);
                    $stmt_get_sla_flaca->bind_param("s", $kode_lahan);
                    $stmt_get_sla_flaca->execute();
                    $result_get_sla_flaca = $stmt_get_sla_flaca->get_result();
                    $row_sla_flaca = $result_get_sla_flaca->fetch_assoc();
                    $sla_flaca = $row_sla_flaca['sla_flaca'];

                    // Step 3: Calculate days passed and completion rate
                    $jml_terlewat = 0;
                    if ($sla_flaca && strtotime($sla_flaca) <= strtotime(date('Y-m-d'))) {
                        $jml_terlewat = (strtotime(date('Y-m-d')) - strtotime($sla_flaca)) / (60 * 60 * 24);
                    }

                    // Calculate completion rate as a percentage
                    $completion_rate = (($sla - $jml_terlewat) / $sla) * 100;

                    // Ensure completion rate is between 0 and 100
                    $completion_rate = max(0, min(100, $completion_rate));

                    // Update completion_rate in the database
                    $sql_update_comprate = "UPDATE crewfl SET comprate = ? WHERE id = ?";
                    $stmt_update_comprate = $conn->prepare($sql_update_comprate);
                    $stmt_update_comprate->bind_param("di", $completion_rate, $id);
                    $stmt_update_comprate->execute();

                    $sql_get_comprates = "SELECT comprate FROM crewfl WHERE status_oje = 'Lolos'";
                    $result_get_comprates = $conn->query($sql_get_comprates);

                    // Akumulasi nilai completion rate
                    $total_comprate = 0;
                    $count = 0;

                    while ($row_comprate = $result_get_comprates->fetch_assoc()) {
                        $total_comprate += $row_comprate['comprate'];
                        $count++;
                    }

                    // Hitung rata-rata completion rate
                    $average_comprate = ($count > 0) ? $total_comprate / $count : 0;

                    // Update rata-rata completion rate di tabel yang sesuai
                    $sql_update_comprate = "UPDATE socdate_hraca SET comprate_flaca = ? WHERE kode_lahan = ?";
                    $stmt_update_comprate = $conn->prepare($sql_update_comprate);
                    $stmt_update_comprate->bind_param("ds", $average_comprate, $kode_lahan);
                    $stmt_update_comprate->execute();
                }
            } elseif ($status_last == 'Pending') {
                $sql_get_kode_lahan = "SELECT kode_lahan FROM crewfl WHERE id = ?";
                $stmt_get_kode_lahan = $conn->prepare($sql_get_kode_lahan);
                $stmt_get_kode_lahan->bind_param("i", $id);
                $stmt_get_kode_lahan->execute();
                $stmt_get_kode_lahan->bind_result($kode_lahan);
                $stmt_get_kode_lahan->fetch();
                $stmt_get_kode_lahan->close();

                if ($kode_lahan) {
                    $sql_update_re = "UPDATE crewfl SET status_last = ?, acafl_date = ? WHERE id = ?";
                    $stmt_update_re = $conn->prepare($sql_update_re);
                    $stmt_update_re->bind_param("ssi", $status_last, $acafl_date, $id);
                    $stmt_update_re->execute();

                    $status_hold = "In Process";
                    $due_date = date("Y-m-d H:i:s");

                    $sql_hold = "INSERT INTO hold_project (kode_lahan, issue_detail, pic, action_plan, due_date, status_hold, kronologi) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt_hold = $conn->prepare($sql_hold);
                    $stmt_hold->bind_param("sssssss", $kode_lahan, $issue_detail, $pic, $action_plan, $due_date, $status_hold, $kronologi);
                    $stmt_hold->execute();
                }
            } else {
                $sql = "UPDATE crewfl SET status_last = ?, acafl_date = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $status_last, $acafl_date, $id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "<script>
                            alert('Status berhasil diperbarui.');
                            window.location.href = window.location.href;
                         </script>";
                } else {
                    echo "Error: Gagal memperbarui status. Tidak ada perubahan dilakukan.";
                }
            }
        }

        $conn->commit();
        
        $sql_get_kode = "SELECT kode_lahan FROM crewfl WHERE id = ?";
        $stmt_get_kode = $conn->prepare($sql_get_kode);
        $stmt_get_kode->bind_param("i", $id);
        $stmt_get_kode->execute();
        $stmt_get_kode->bind_result($kode_lahan);
        $stmt_get_kode->fetch();
        $stmt_get_kode->close();

        if (isset($kode_lahan) && !empty($kode_lahan)) {
            header("Location: ../datatables-data-acafl.php?id=" . urlencode($kode_lahan));
            exit;
        } else {
            echo "Error: Kode lahan tidak ditemukan.";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}