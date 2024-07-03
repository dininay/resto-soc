<?php
// Koneksi ke database
include "../koneksi.php";
// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_land"])) {
    $id = $_POST["id"];
    $status_land = $_POST["status_land"];
    
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_finallegal berdasarkan id
        $sql_update = "UPDATE resto SET status_land = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $status_land, $id);
        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
// Query untuk mengambil data dari tabel land
$sql = "SELECT 
land.kode_lahan,
land.*,
resto.*,
soc_sdg.*,
sdg_pk.*,
socdate_hr.*,
draft.*,
socdate_academy.*,
socdate_scm.*,
socdate_it.*,
socdate_marketing.*,
socdate_fat.*,
sign.*,
summary_soc.*,
            land.kode_lahan AS land_kode_lahan,
            draft.end_date AS draft_end_date
FROM land
JOIN resto ON resto.kode_lahan = land.kode_lahan
JOIN soc_sdg ON soc_sdg.kode_lahan = land.kode_lahan
LEFT JOIN sdg_pk ON sdg_pk.kode_lahan = land.kode_lahan
LEFT JOIN socdate_hr ON socdate_hr.kode_lahan = land.kode_lahan
LEFT JOIN draft ON draft.kode_lahan = land.kode_lahan
LEFT JOIN socdate_academy ON socdate_academy.kode_lahan = land.kode_lahan
LEFT JOIN socdate_scm ON socdate_scm.kode_lahan = land.kode_lahan
LEFT JOIN socdate_it ON socdate_it.kode_lahan = land.kode_lahan
LEFT JOIN socdate_marketing ON socdate_marketing.kode_lahan = land.kode_lahan
LEFT JOIN socdate_fat ON socdate_fat.kode_lahan = land.kode_lahan
LEFT JOIN sign ON sign.kode_lahan = land.kode_lahan
LEFT JOIN summary_soc ON summary_soc.kode_lahan = land.kode_lahan
WHERE resto.status_kom = 'Approve'";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

// Query untuk mengambil nilai SLA dari master_sla
$sla_query = "SELECT sla, divisi FROM master_sla";
$sla_result = $conn->query($sla_query);

$master_sla = [];
if ($sla_result->num_rows > 0) {
    while ($row = $sla_result->fetch_assoc()) {
        $master_sla[$row['divisi']] = $row['sla'];
    }
} else {
    echo "0 results";
}

// Query untuk mengambil nilai SLA dari master_sla
$slacons_query = "SELECT sla, divisi FROM master_slacons";
$slacons_result = $conn->query($slacons_query);

$master_slacons = [];
if ($slacons_result->num_rows > 0) {
    while ($row = $slacons_result->fetch_assoc()) {
        $master_slacons[$row['divisi']] = $row['sla'];
    }
} else {
    echo "0 results";
}

// Fungsi untuk menghitung scoring
function calculateScoring($start_date, $end_date, $sla) {
    $today = new DateTime();
    $start_date = $start_date ?: $today->format('Y-m-d');
    $end_date = $end_date ?: $today->format('Y-m-d');
    $sla_days = $sla ?: 0;

    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);

    $date_diff = $end_date_obj->diff($start_date_obj)->days + 1;

    if ($sla_days != 0) {
        if ($date_diff > $sla_days) {
            $scoring = -((($date_diff - $sla_days) / $sla_days) * 100);
        } else {
            $scoring = ((($sla_days - $date_diff) / $sla_days) * 100);
        }
    } else {
        $scoring = 0;
    }

    return round($scoring, 2);
}

// Fungsi untuk menentukan remarks
function getRemarks($scoring) {
    if ($scoring >= 0) {
        return "good";
    } elseif ($scoring >= -30) {
        return "poor";
    } elseif ($scoring >= -50) {
        return "bad";
    } else {
        return "failed";
    }
}

// Fungsi untuk menentukan warna badge berdasarkan remarks
function getBadgeColor($remarks) {
    switch ($remarks) {
        case 'good':
            return 'success';
        case 'poor':
            return 'warning';
        case 'bad':
            return 'orange';
        case 'failed':
            return 'danger';
        default:
            return 'secondary';
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en" dir="">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard Resto | Mie Gacoan<</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/datatables.min.css" rel="stylesheet"  />
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/feather-icon.css">
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="text-left">
    <div class="app-admin-wrap layout-sidebar-compact sidebar-dark-purple sidenav-open clearfix">
        <?php
			include '../layouts/right-sidebar.php';
		?>
        <!--=============== Left side End ================-->
        <div class="main-content-wrap d-flex flex-column">
            <?php
			include '../layouts/top-sidebar.php';
		?>
			<!-- ============ Body content start ============= -->
            <div class="main-content">
                <div class="breadcrumb">
                    <h1>In Progress Tracking Date</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
									<!-- <p><a class="btn btn-primary btn-icon m-1" href="o/sp-submit-form.php">+ add SOC </a></p> -->
									<p>
									  <span class="flex-grow-1"></span></p>
								</div>
                                <p>
							  <div class="table-responsive">
                              <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                                <tr>
                                                    <th rowspan="5" class="sticky" style="background-color: #6c757d; color: white;">Proses Phase</th>
                                                    <th rowspan="5" class="sticky" style="background-color: #6c757d; color: white;">Detail Process</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="sticky" style="background-color: #6c757d; color: white;">Store</th>
                                                    <?php foreach ($data as $row): ?>
                                                    <th colspan="9" style="background-color: #6c757d; color: white;"><?= $row['land_kode_lahan'] ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <th colspan="" rowspan="" class="sticky" style="background-color: #6c757d; color: white;">Target GO</th>
                                                    <?php foreach ($data as $row): ?>
                                                    <th colspan="9" style="background-color: #6c757d; color: white;"><?= $row['gostore_date'] ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <th colspan="" rowspan="2" class="sticky" style="background-color: #6c757d; color: white;">PIC</th>
                                                <?php foreach ($data as $row): ?>
                                                <th colspan="2" style="background-color: #6c757d; color: white;">Plan</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">SLA (d)</th>
                                                <th colspan="4" style="background-color: #6c757d; color: white;">Actual</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Scoring</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Remarks</th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($data as $row): ?>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">End</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Progress</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Deviasi</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">End</th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Construction</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Construction Work (only update progress)</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">SDG-Project</td>
                                                <?php foreach ($data as $row): ?>
                                                <?php
                                                // Mendefinisikan variabel untuk SLA
                                                $sla_cons_date = !empty($row['start_konstruksi']) ? date('Y-m-d', strtotime($row['start_konstruksi'] . ' +' . ($master_sla['Konstruksi'] ?? 0) . ' days')) : '';
                                                $cons = $row['month_1'] + $row['month_2'] + $row['month_3'];
                                                $deviasi_cons = $cons - 100;
                                                ?>
                                                <td><?= $row['start_konstruksi'] ?></td>
                                                <td><?= $sla_cons_date ?></td>
                                                <td><?= $master_sla['Konstruksi'] ?? 'N/A' ?></td>
                                                <td><?= $row['start_konstruksi'] ?></td>
                                                <td><?= $cons ?>%</td>
                                                <td><?= $deviasi_cons ?>%</td>
                                                <td><?= $row['stkonstruksi_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($deviasi_cons);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $deviasi_cons ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">Equipment</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">ST Equipment</td>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">SDG-Equipment</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php 
                                                        $start_steqp_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date'] . ' -' . 11 . ' days')) : '';
                                                        // Menghitung sla_steqp_date berdasarkan start_steqp_date ditambah 2 hari
                                                        $sla_steqp_date = !empty($start_steqp_date) ? date('Y-m-d', strtotime($start_steqp_date . ' +' . 2 . ' days')) : '';
                                                        $steqp = !empty($row['lamp_steqp']) ? 100 : 0; 
                                                        $dev_steqp = $steqp - 100 ;
                                                    ?>
                                                <td><?= $start_steqp_date ?></td>
                                                <td><?= $sla_steqp_date ?></td>
                                                <td>2</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <td><?= $steqp ?>%</td>
                                                <td><?= $dev_steqp?>%</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_steqp);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_steqp ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Pemasangan Pylon Sign/Totem</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                           
                                                    $start_pylon_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date'] . ' -' . 21 . ' days')) : '';
                                                    $sla_pylon_date = !empty($start_pylon_date) ? date('Y-m-d', strtotime($start_pylon_date . ' +' . 1 . ' days')) : '';
                                                    $bangunan_mural = $row['bangunan_mural']-100;
                                                    ?>
                                                <td><?= $start_pylon_date ?></td>
                                                <td><?= $sla_pylon_date?></td>
                                                <td>1</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <td><?= $row['bangunan_mural'] ?>%</td>
                                                <td><?= $bangunan_mural ?>%</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($bangunan_mural);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $bangunan_mural ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Fulfillment Equipment</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                           
                                                    $start_ffeqp_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date'] . ' -' . 4 . ' days')) : '';
                                                    $sla_ffeqp_date = !empty($start_ffeqp_date) ? date('Y-m-d', strtotime($start_ffeqp_date . ' +' . 1 . ' days')) : '';
                                                    // Menghitung hasil penjumlahan dari beberapa kolom
                                                    $total = ($row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) /5;
                                                    $scoring = ($row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 5 - 100;
                                                    ?>
                                                <td><?= $start_ffeqp_date ?></td>
                                                <td><?= $sla_ffeqp_date?></td>
                                                <td>1</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <td><?= $total ?>%</td>
                                                <td><?= $scoring ?>%</td>
                                                <td><?= $row['steqp_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $scoring ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="4"  class="sticky" style="background-color: #b6c7aa; color: white;">Recruitment</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">TM (start from lahan masuk, after VD)</td>
                                                <td rowspan="4"  class="sticky" style="background-color: #b6c7aa; color: white;">HRGA</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php 
                                                        $start_tm_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date'] . ' -' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : '';
                                                        // Menghitung sla_tm_date berdasarkan start_tm_date ditambah 2 hari
                                                        $sla_tm_date = !empty($row['draft_end_date']) ? date('Y-m-d', strtotime($row['draft_end_date'] . ' +' . $master_slacons['hrga_tm'] . ' days')) : '';
                                                        $tm = !empty($row['lamp_tm']) ? 100 : 0; 
                                                        $dev_tm = $tm - 100 ;
                                                    ?>
                                                <td><?= $row['draft_end_date'] ?></td>
                                                <td><?= $sla_tm_date ?></td>
                                                <td><?= $master_slacons['hrga_tm']?></td>
                                                <td><?= $row['draft_end_date'] ?></td>
                                                <td><?= $tm ?>%</td>
                                                <td><?= $dev_tm?>%</td>
                                                <td><?= $row['tm_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_tm);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_tm ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Crew Batch 1</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_ff1_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $ff1 = isset($row['ff_1']) ? $row['ff_1'] : 0;
                                                    $dev_ff1 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_1'])) {
                                                        $dev_ff1 = (int)$row['ff_1'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_ff1_date?></td>
                                                <td><?= $master_slacons['hrga_ff1'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $ff1 ?>%</td>
                                                <td><?= $dev_ff1 ?>%</td>
                                                <td><?= $row['ff1_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_ff1);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_ff1 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Crew Batch 2</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_ff2_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff2'] . ' days')) : '';
                                                    $sla_ff2_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff2'] . ' days')) : '';
                                                    // $ff2 = !empty($row['lamp_ff2']) ? 100 : 0; 
                                                    $ff2 = isset($row['ff_2']) ? $row['ff_2'] : 0;
                                                    $dev_ff2 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_2'])) {
                                                        $dev_ff2 = (int)$row['ff_2'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_ff2_date?></td>
                                                <td><?= $master_slacons['hrga_ff2'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $ff2 ?>%</td>
                                                <td><?= $dev_ff2 ?>%</td>
                                                <td><?= $row['ff2_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_ff2);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_ff2 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Crew Batch 3</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_ff3_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    $sla_ff3_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    // $ff3 = !empty($row['lamp_ff3']) ? 100 : 0; 
                                                    $ff3 = isset($row['ff_3']) ? $row['ff_3'] : 0;
                                                    $dev_ff3 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_3'])) {
                                                        $dev_ff3 = (int)$row['ff_3'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_ff3_date?></td>
                                                <td><?= $master_slacons['hrga_ff3'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $ff3 ?>%</td>
                                                <td><?= $dev_ff3 ?>%</td>
                                                <td><?= $row['ff3_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_ff3);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_ff3 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">Training (Completion Rate)</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Training Batch 1</td>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">Academy</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_kpt1_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $kpt1 = isset($row['kpt_1']) ? $row['kpt_1'] : 0;
                                                    $dev_kpt1 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_1'])) {
                                                        $dev_kpt1 = (int)$row['kpt_1'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_kpt1_date?></td>
                                                <td><?= $master_slacons['kpt1'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $kpt1 ?>%</td>
                                                <td><?= $dev_kpt1 ?>%</td>
                                                <td><?= $row['kpt_date1'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_kpt1);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_kpt1 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Training Batch 2</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_kpt2_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt2'] . ' days')) : '';
                                                    $sla_kpt2_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt2'] . ' days')) : '';
                                                    // $kpt2 = !empty($row['lamp_kpt2']) ? 100 : 0; 
                                                    $kpt2 = isset($row['kpt_2']) ? $row['kpt_2'] : 0;
                                                    $dev_kpt2 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_2'])) {
                                                        $dev_kpt2 = (int)$row['kpt_2'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_kpt2_date?></td>
                                                <td><?= $master_slacons['kpt2'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $kpt2 ?>%</td>
                                                <td><?= $dev_kpt2 ?>%</td>
                                                <td><?= $row['kpt_date2'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_kpt2);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_kpt2 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Training Batch 3</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_kpt3_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    $sla_kpt3_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    // $kpt3 = !empty($row['lamp_kpt3']) ? 100 : 0; 
                                                    $kpt3 = isset($row['kpt_3']) ? $row['kpt_3'] : 0;
                                                    $dev_kpt3 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_3'])) {
                                                        $dev_kpt3 = (int)$row['kpt_3'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_kpt3_date?></td>
                                                <td><?= $master_slacons['kpt3'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $kpt3 ?>%</td>
                                                <td><?= $dev_kpt3 ?>%</td>
                                                <td><?= $row['kpt_date3'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_kpt3);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_kpt3 ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>            
                                            <tr>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">SCM</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Utensils</td>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">SCM</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_scm);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_scm ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Dry Stock</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_scm);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_scm ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Frozen Stock</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('Y-m-d', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_scm);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_scm ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>             
                                            <tr>
                                                <td rowspan="5"  class="sticky" style="background-color: #b6c7aa; color: white;">IT Configuration</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Configuration System (Desktop, Cloud, Back Office, Server, Web Report, GIS)</td>
                                                <td rowspan="5"  class="sticky" style="background-color: #b6c7aa; color: white;">IT</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('Y-m-d', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_config']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_config'])) {
                                                        $dev_it = (int)$row['lamp_config'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_it);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_it ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Instalasi POS & Kitchen Print Out</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('Y-m-d', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_printer']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_printer'])) {
                                                        $dev_it = (int)$row['lamp_printer'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_it);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_it ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Testing POS & Kitchen Print Out</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('Y-m-d', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_printer']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_printer'])) {
                                                        $dev_it = (int)$row['lamp_printer'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_it);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_it ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">CCTV, Sound</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('Y-m-d', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_printer']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_printer'])) {
                                                        $dev_it = (int)$row['lamp_printer'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_it);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_it ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Internet Customer, Manager & Kasir</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('Y-m-d', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_internet']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_internet'])) {
                                                        $dev_it = (int)$row['lamp_internet'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= $row['sj_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_it);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_it ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">Marketing & Online Registration</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Registration Merchant</td>
                                                <td rowspan="3"  class="sticky" style="background-color: #b6c7aa; color: white;">Marketing</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('Y-m-d', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $marketing = isset($row['lamp_merchant']) ? 100 : 0;
                                                    $dev_marketing = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_merchant'])) {
                                                        $dev_marketing = (int)$row['lamp_merchant'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $sla_marketing_date?></td>
                                                <td>1</td>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $marketing ?>%</td>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td><?= $row['merchant_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_marketing);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Content</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('Y-m-d', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $marketing = isset($row['lamp_merchant']) ? 100 : 0;
                                                    $dev_marketing = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_merchant'])) {
                                                        $dev_marketing = (int)$row['lamp_merchant'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $sla_marketing_date?></td>
                                                <td>1</td>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $marketing ?>%</td>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td><?= $row['merchant_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_marketing);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Promo</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('Y-m-d', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $marketing = isset($row['lamp_merchant']) ? 100 : 0;
                                                    $dev_marketing = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_merchant'])) {
                                                        $dev_marketing = (int)$row['lamp_merchant'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $sla_marketing_date?></td>
                                                <td>1</td>
                                                <td><?= $start_marketing_date ?></td>
                                                <td><?= $marketing ?>%</td>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td><?= $row['merchant_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_marketing);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_marketing ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"  class="sticky" style="background-color: #b6c7aa; color: white;">Payment</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">QRIS EDC</td>
                                                <td rowspan="2"  class="sticky" style="background-color: #b6c7aa; color: white;">TAF</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_taf_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_taf_date = !empty($start_taf_date ) ? date('Y-m-d', strtotime($start_taf_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $taf = isset($row['lamp_qris']) ? 100 : 0;
                                                    $dev_taf = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_qris'])) {
                                                        $dev_taf = (int)$row['lamp_qris'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_taf_date ?></td>
                                                <td><?= $sla_taf_date?></td>
                                                <td>1</td>
                                                <td><?= $start_taf_date ?></td>
                                                <td><?= $taf ?>%</td>
                                                <td><?= $dev_taf ?>%</td>
                                                <td><?= $row['merchant_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_taf);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_taf ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">ATM / Rek</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_taf_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_taf_date = !empty($start_taf_date ) ? date('Y-m-d', strtotime($start_taf_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $taf = isset($row['lamp_qris']) ? 100 : 0;
                                                    $dev_taf = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_qris'])) {
                                                        $dev_taf = (int)$row['lamp_qris'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_taf_date ?></td>
                                                <td><?= $sla_taf_date?></td>
                                                <td>1</td>
                                                <td><?= $start_taf_date ?></td>
                                                <td><?= $taf ?>%</td>
                                                <td><?= $dev_taf ?>%</td>
                                                <td><?= $row['merchant_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_taf);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_taf ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">Handover</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Handover Contractor</td>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">SDG-Project</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_ho_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_ho_date = !empty($start_ho_date ) ? date('Y-m-d', strtotime($start_ho_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $ho = isset($row['lamp_qris']) ? 100 : 0;
                                                    $dev_ho = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_qris'])) {
                                                        $dev_ho = (int)$row['lamp_qris'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_ho_date ?></td>
                                                <td><?= $sla_ho_date?></td>
                                                <td>1</td>
                                                <td><?= $start_ho_date ?></td>
                                                <td><?= $ho ?>%</td>
                                                <td><?= $dev_ho ?>%</td>
                                                <td><?= $row['soc_date'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_ho);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_ho ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">RTO</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">RTO Checklist</td>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">Ops</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_rto_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_rto_date = !empty($start_rto_date ) ? date('Y-m-d', strtotime($start_rto_date  . ' +' . 1 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $rto = isset($row['lamp_qris']) ? 100 : 0;
                                                    $dev_rto = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_qris'])) {
                                                        $dev_rto = (int)$row['lamp_qris'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_rto_date ?></td>
                                                <td><?= $sla_rto_date?></td>
                                                <td>1</td>
                                                <td><?= $start_rto_date ?></td>
                                                <td><?= $rto ?>%</td>
                                                <td><?= $dev_rto ?>%</td>
                                                <td><?= $row['rto_act'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_rto);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_rto ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">GO</td>
                                                <td  class="sticky" style="background-color: #b6c7aa; color: white;">Grand Opening</td>
                                                <td rowspan="1"  class="sticky" style="background-color: #b6c7aa; color: white;">Ops</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php    
                                                    $start_go_date = !empty($row['gostore_date'] ) ? date('Y-m-d', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $sla_go_date = !empty($start_go_date ) ? date('Y-m-d', strtotime($start_go_date  . ' +' . 2 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $go = isset($row['lamp_qris']) ? 100 : 0;
                                                    $dev_go = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_qris'])) {
                                                        $dev_go = (int)$row['lamp_qris'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_go_date ?></td>
                                                <td><?= $sla_go_date?></td>
                                                <td>1</td>
                                                <td><?= $start_go_date ?></td>
                                                <td><?= $go ?>%</td>
                                                <td><?= $dev_go ?>%</td>
                                                <td><?= $row['go_fix'] ?></td>
                                                <?php 
                                                $remarks = getRemarks($dev_go);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td><?= $dev_go ?>%</td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- Modal Konfirmasi Hapus -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Data</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus data ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <form method="POST" action="draft-sewa-delete.php">
                                                        <input type="hidden" name="id" id="delete" value="">
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                </div>
                <!-- end of row-->
                <!-- end of main-content -->
                <!-- Footer Start -->
                <div class="flex-grow-1"></div>
                <!-- fotter end -->
            </div>
        </div>
    </div><!-- ============ Search UI Start ============= -->
    <div class="search-ui">
        <div class="search-header">
            <img src="../dist-assets/images/logo.png" alt="" class="logo">
            <button class="search-close btn btn-icon bg-transparent float-right mt-2">
                <i class="i-Close-Window text-22 text-muted"></i>
            </button>
        </div>
        <input type="text" placeholder="Type here" class="search-input" autofocus>
        <div class="search-title">
            <span class="text-muted">Search results</span>
        </div>
        <div class="search-results list-horizontal">
            <div class="list-item col-md-12 p-0">
                <div class="card o-hidden flex-row mb-4 d-flex">
                    <div class="list-thumb d-flex">
                        <!-- TUMBNAIL -->
                        <img src="../dist-assets/images/products/headphone-1.jpg" alt="">
                    </div>
                    <div class="flex-grow-1 pl-2 d-flex">
                        <div class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center flex-lg-row">
                            <!-- OTHER DATA -->
                            <a href="" class="w-40 w-sm-100">
                                <div class="item-title">Headphone 1</div>
                            </a>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">Gadget</p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">$300
                                <del class="text-secondary">$400</del>
                            </p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100 d-none d-lg-block item-badges">
                                <span class="badge badge-danger">Sale</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list-item col-md-12 p-0">
                <div class="card o-hidden flex-row mb-4 d-flex">
                    <div class="list-thumb d-flex">
                        <!-- TUMBNAIL -->
                        <img src="../dist-assets/images/products/headphone-2.jpg" alt="">
                    </div>
                    <div class="flex-grow-1 pl-2 d-flex">
                        <div class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center flex-lg-row">
                            <!-- OTHER DATA -->
                            <a href="" class="w-40 w-sm-100">
                                <div class="item-title">Headphone 1</div>
                            </a>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">Gadget</p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">$300
                                <del class="text-secondary">$400</del>
                            </p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100 d-none d-lg-block item-badges">
                                <span class="badge badge-primary">New</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list-item col-md-12 p-0">
                <div class="card o-hidden flex-row mb-4 d-flex">
                    <div class="list-thumb d-flex">
                        <!-- TUMBNAIL -->
                        <img src="../dist-assets/images/products/headphone-3.jpg" alt="">
                    </div>
                    <div class="flex-grow-1 pl-2 d-flex">
                        <div class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center flex-lg-row">
                            <!-- OTHER DATA -->
                            <a href="" class="w-40 w-sm-100">
                                <div class="item-title">Headphone 1</div>
                            </a>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">Gadget</p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">$300
                                <del class="text-secondary">$400</del>
                            </p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100 d-none d-lg-block item-badges">
                                <span class="badge badge-primary">New</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list-item col-md-12 p-0">
                <div class="card o-hidden flex-row mb-4 d-flex">
                    <div class="list-thumb d-flex">
                        <!-- TUMBNAIL -->
                        <img src="../dist-assets/images/products/headphone-4.jpg" alt="">
                    </div>
                    <div class="flex-grow-1 pl-2 d-flex">
                        <div class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center flex-lg-row">
                            <!-- OTHER DATA -->
                            <a href="" class="w-40 w-sm-100">
                                <div class="item-title">Headphone 1</div>
                            </a>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">Gadget</p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100">$300
                                <del class="text-secondary">$400</del>
                            </p>
                            <p class="m-0 text-muted text-small w-15 w-sm-100 d-none d-lg-block item-badges">
                                <span class="badge badge-primary">New</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PAGINATION CONTROL -->
        <div class="col-md-12 mt-5 text-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination d-inline-flex">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- ============ Search UI End ============= -->
    <div class="customizer">
        <div class="handle"><i class="i-Gear spin"></i></div>
        <div class="customizer-body" data-perfect-scrollbar="" data-suppress-scroll-x="true">
            <div class="accordion" id="accordionCustomizer">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <p class="mb-0">Sidebar Colors</p>
                    </div>
                    <div class="collapse show" id="collapseOne" aria-labelledby="headingOne" data-parent="#accordionCustomizer">
                        <div class="card-body">
                            <div class="colors sidebar-colors"><a class="color gradient-purple-indigo" data-sidebar-class="sidebar-gradient-purple-indigo"><i class="i-Eye"></i></a><a class="color gradient-black-blue" data-sidebar-class="sidebar-gradient-black-blue"><i class="i-Eye"></i></a><a class="color gradient-black-gray" data-sidebar-class="sidebar-gradient-black-gray"><i class="i-Eye"></i></a><a class="color gradient-steel-gray" data-sidebar-class="sidebar-gradient-steel-gray"><i class="i-Eye"></i></a><a class="color dark-purple active" data-sidebar-class="sidebar-dark-purple"><i class="i-Eye"></i></a><a class="color slate-gray" data-sidebar-class="sidebar-slate-gray"><i class="i-Eye"></i></a><a class="color midnight-blue" data-sidebar-class="sidebar-midnight-blue"><i class="i-Eye"></i></a><a class="color blue" data-sidebar-class="sidebar-blue"><i class="i-Eye"></i></a><a class="color indigo" data-sidebar-class="sidebar-indigo"><i class="i-Eye"></i></a><a class="color pink" data-sidebar-class="sidebar-pink"><i class="i-Eye"></i></a><a class="color red" data-sidebar-class="sidebar-red"><i class="i-Eye"></i></a><a class="color purple" data-sidebar-class="sidebar-purple"><i class="i-Eye"></i></a></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <p class="mb-0">RTL</p>
                    </div>
                    <div class="collapse show" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordionCustomizer">
                        <div class="card-body">
                            <label class="checkbox checkbox-primary">
                                <input id="rtl-checkbox" type="checkbox" /><span>Enable RTL</span><span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../dist-assets/js/scripts/script.min.js"></script>
    <script src="../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../dist-assets/js/scripts/customizer.script.min.js"></script>
    <script src="../dist-assets/js/plugins/datatables.min.js"></script>
    <script src="../dist-assets/js/scripts/datatables.script.min.js"></script>
	<script src="../dist-assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="../dist-assets/js/icons/feather-icon/feather-icon.js"></script>
    <script>
        // Fungsi untuk mengatur id data yang akan dihapus ke dalam modal
        function setDelete(element) {
            var id = element.id;
            document.getElementById('delete').value = id;
        }
    </script>
    <script>
$(document).ready(function() {
    $(".edit-btn").click(function() {
        // Sembunyikan semua form yang terbuka
        $(".status-form").hide();
        // Tampilkan form di samping tombol edit yang diklik
        $(this).next(".status-form").show();
    });
});
</script>
</body>

</html>