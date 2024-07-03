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
re.*,
dokumen_loacd.*,
sdg_desain.*,
sdg_rab.*,
draft.*,
procurement.*,
resto.*,
socdate_hr.*,
            land.kode_lahan AS land_kode_lahan,
            re.start_date AS re_start_date,
            dokumen_loacd.start_date AS dokumen_loacd_start_date,
            dokumen_loacd.end_date AS dokumen_loacd_end_date,
            sdg_desain.start_date AS sdg_desain_start_date,
            sdg_desain.sla_date AS sdg_desain_sla_date,
            sdg_desain.submit_date AS sdg_desain_submit_date,
            sdg_desain.slalegal_date AS sdg_desain_slalegal_date,
            sdg_rab.start_date AS sdg_qs_start_date,
            sdg_rab.sla_date AS sdg_qs_sla_date,
            draft.start_date AS draft_start_date,
            draft.slalegal_date AS draft_slalegal_date,
            draft.sla_date AS draft_sla_date,
            draft.end_date AS draft_end_date,
            draft.fat_date AS draft_fat_date,
            draft.slafat_date AS draft_slafat_date,
            procurement.start_date AS procurement_start_date,
            procurement.sla_date AS procurement_sla_date
FROM land
LEFT JOIN re ON re.kode_lahan = land.kode_lahan
LEFT JOIN dokumen_loacd ON dokumen_loacd.kode_lahan = land.kode_lahan
LEFT JOIN sdg_desain ON sdg_desain.kode_lahan = land.kode_lahan
LEFT JOIN sdg_rab ON sdg_rab.kode_lahan = land.kode_lahan
LEFT JOIN draft ON draft.kode_lahan = land.kode_lahan
LEFT JOIN procurement ON procurement.kode_lahan = land.kode_lahan
LEFT JOIN resto ON resto.kode_lahan = land.kode_lahan
LEFT JOIN socdate_hr ON socdate_hr.kode_lahan = land.kode_lahan";
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
                    <h1>In Preparation Tracking Date</h1>
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
                                                    <th rowspan="4" class="sticky" style="background-color: #6c757d; color: white;">Proses Phase</th>
                                                    <th rowspan="4" class="sticky" style="background-color: #6c757d; color: white;">Activities</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="sticky" style="background-color: #6c757d; color: white;">Store</th>
                                                    <?php foreach ($data as $row): ?>
                                                    <th colspan="7" style="background-color: #6c757d; color: white;"><?= $row['land_kode_lahan'] ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" rowspan="3" class="sticky" style="background-color: #6c757d; color: white;">PIC</th>
                                                <?php foreach ($data as $row): ?>
                                                <th colspan="2" style="background-color: #6c757d; color: white;">Plan</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">SLA (d)</th>
                                                <th colspan="2" style="background-color: #6c757d; color: white;">Actual</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Scoring</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Remarks</th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($data as $row): ?>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">End</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" style="background-color: #6c757d; color: white;">End</th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Submit Lahan</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Submitting Lahan</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">RE</td>
                                                <?php foreach ($data as $row): ?>
                                                <?php
                                                // Mendefinisikan variabel untuk SLA
                                                $sla_re_date = !empty($row['status_date']) ? date('Y-m-d', strtotime($row['status_date'] . ' +' . ($master_sla['RE'] ?? 0) . ' days')) : '';
                                                ?>
                                                <td><?= $row['status_date'] ?></td>
                                                <td><?= $sla_re_date ?></td>
                                                <td><?= $master_sla['RE'] ?? 'N/A' ?></td>
                                                <td><?= $row['status_date'] ?></td>
                                                <td><?= $row['re_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['status_date'], $row['re_date'], $master_sla['RE']);
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
                                                <td rowspan="5" class="sticky" style="background-color: white;">Document Preparation</td>
                                                <td class="sticky" style="background-color: white;">Validation BoD</td>
                                                <td class="sticky" style="background-color: white;">BoD</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php       
                                                    $sla_re_date = !empty($row['status_date']) ? date('Y-m-d', strtotime($row['status_date'] . ' +' . ($master_sla['RE'] ?? 0) . ' days')) : '';
                                                                                       
                                                    $sla_bod_date = $sla_re_date != 'N/A' ? date('Y-m-d', strtotime($sla_re_date . ' +' . ($master_sla['Owner Surveyor'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_re_date ?></td>
                                                <td><?= $sla_bod_date ?></td>
                                                <td><?= $master_sla['Owner Surveyor'] ?? 'N/A' ?></td>
                                                <td><?= $row['re_date'] ?></td>
                                                <td><?= $row['re_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_date'], $row['re_start_date'], $master_sla['Owner Surveyor']);
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
                                                <td class="sticky" style="background-color: white;">Validation Land</td>
                                                <td class="sticky" style="background-color: white;">Legal</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_legal_date = $sla_bod_date != 'N/A' ? date('Y-m-d', strtotime($sla_bod_date . ' +' . ($master_sla['Legal'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_bod_date ?></td>
                                                <td><?= $sla_legal_date?></td>
                                                <td><?= $master_sla['VL'] ?? 'N/A' ?></td>
                                                <td><?= $row['re_start_date'] ?></td>
                                                <td><?= $row['vl_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_start_date'], $row['vl_date'], $master_sla['VL']);
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
                                                <td class="sticky" style="background-color: white;">Negotiation</td>
                                                <td class="sticky" style="background-color: white;">Owner</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_nego_date = $sla_legal_date != 'N/A' ? date('Y-m-d', strtotime($sla_legal_date . ' +' . ($master_sla['Negosiator'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_legal_date?></td>
                                                <td><?= $sla_nego_date?></td>
                                                <td><?= $master_sla['Negosiator'] ?? 'N/A' ?></td>
                                                <td><?= $row['end_date'] ?></td>
                                                <td><?= $row['nego_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['end_date'], $row['nego_date'], $master_sla['Negosiator']);
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
                                                <td class="sticky" style="background-color: white;">Collect Doc & LoA</td>
                                                <td class="sticky" style="background-color: white;">RE</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_loa_date = $sla_nego_date != 'N/A' ? date('Y-m-d', strtotime($sla_nego_date . ' +' . ($master_sla['LOA-CD'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_nego_date ?></td>
                                                <td><?= $sla_loa_date?></td>
                                                <td><?= $master_sla['LOA-CD'] ?? 'N/A' ?></td>
                                                <td><?= $row['nego_date'] ?></td>
                                                <td><?= $row['dokumen_loacd_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['dokumen_loacd_start_date'], $master_sla['LOA-CD']);
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
                                                <td class="sticky" style="background-color: white;">Validation Document (VD)</td>
                                                <td class="sticky" style="background-color: white;">Legal</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_vd_date = $sla_loa_date != 'N/A' ? date('Y-m-d', strtotime($sla_loa_date . ' +' . ($master_sla['VD'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_loa_date ?></td>
                                                <td><?= $sla_vd_date?></td>
                                                <td><?= $master_sla['VD'] ?? 'N/A' ?></td>
                                                <td><?= $row['vl_date'] ?></td>
                                                <td><?= $row['dokumen_loacd_end_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['vl_date'], $row['dokumen_loacd_end_date'], $master_sla['VD']);
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
                                                <td rowspan="4" style="background-color: #b6c7aa; color: white;">Design</td>
                                                <td style="background-color: #b6c7aa; color: white;">Survey</td>
                                                <td rowspan="4" style="background-color: #b6c7aa; color: white;">SDG-DED</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_survey_date = $sla_nego_date != 'N/A' ? date('Y-m-d', strtotime($sla_nego_date . ' +' . ($master_sla['Land Survey'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_nego_date ?></td>
                                                <td><?= $sla_survey_date?></td>
                                                <td><?= $master_sla['Land Survey'] ?? 'N/A' ?></td>
                                                <td><?= $row['nego_date'] ?></td>
                                                <td><?= $row['survey_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['survey_date'], $master_sla['Land Survey']);
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
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Setting Layout</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_layout_date = $sla_survey_date != 'N/A' ? date('Y-m-d', strtotime($sla_survey_date . ' +' . ($master_sla['Layouting'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_survey_date ?></td>
                                                <td><?= $sla_layout_date?></td>
                                                <td><?= $master_sla['Layouting'] ?? 'N/A' ?></td>
                                                <td><?= $row['survey_date'] ?></td>
                                                <td><?= $row['layout_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['survey_date'], $row['layout_date'], $master_sla['Layouting']);
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
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Design Urugan</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_sdgded_date = $sla_layout_date != 'N/A' ? date('Y-m-d', strtotime($sla_layout_date . ' +' . ($master_sla['Design'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2"><?= $sla_layout_date ?></td>
                                                <td rowspan="2"><?= $sla_sdgded_date?></td>
                                                <td rowspan="2"><?= $master_sla['Design'] ?? 'N/A' ?></td>
                                                <td rowspan="2"><?= $row['layout_date'] ?></td>
                                                <td rowspan="2"><?= $row['sdg_desain_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['layout_date'], $row['sdg_desain_start_date'], $master_sla['Design']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td rowspan="2"><?= $scoring ?>%</td>
                                                <td rowspan="2">
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">DED Construction</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2" class="sticky" style="background-color: white;">QS</td>
                                                <td class="sticky" style="background-color: white;">RAB Urugan</td>
                                                <td rowspan="2" class="sticky" style="background-color: white;">SDG-QS</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_rab_date = $sla_sdgded_date != 'N/A' ? date('Y-m-d', strtotime($sla_sdgded_date . ' +' . ($master_sla['QS'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2" style="background-color: white"><?= $sla_sdgded_date ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $sla_rab_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['QS'] ?? 'N/A' ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $row['sdg_desain_start_date'] ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $row['sdg_qs_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_qs_start_date'], $master_sla['QS']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td rowspan="2" style="background-color: white"><?= $scoring ?>%</td>
                                                <td rowspan="2" style="background-color: white">
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky">RAB Construction</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="3" class="sticky" style="background-color: #b6c7aa; color: white;">Draft Sewa (PSM)</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Draft Sewa (PSM)</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Legal</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_draft_date = $sla_vd_date != 'N/A' ? date('Y-m-d', strtotime($sla_vd_date . ' +' . ($master_sla['Draft-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_vd_date ?></td>
                                                <td><?= $sla_draft_date?></td>
                                                <td><?= $master_sla['Draft-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= $row['dokumen_loacd_end_date'] ?></td>
                                                <td><?= $row['draft_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['dokumen_loacd_end_date'], $row['draft_start_date'], $master_sla['Draft-Sewa']);
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
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Review</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">TAF</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_fat_date = $sla_draft_date != 'N/A' ? date('Y-m-d', strtotime($sla_draft_date . ' +' . ($master_sla['FAT-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_draft_date ?></td>
                                                <td><?= $sla_fat_date?></td>
                                                <td><?= $master_sla['FAT-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= $row['draft_start_date'] ?></td>
                                                <td><?= $row['draft_fat_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_fat_date'], $master_sla['FAT-Sewa']);
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
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">TTD</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">RE</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_psm_date = $sla_fat_date != 'N/A' ? date('Y-m-d', strtotime($sla_fat_date . ' +' . ($master_sla['TTD-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_fat_date ?></td>
                                                <td><?= $sla_psm_date?></td>
                                                <td><?= $master_sla['TTD-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= $row['draft_start_date'] ?></td>
                                                <td><?= $row['draft_end_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_end_date'], $master_sla['TTD-Sewa']);
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
                                                <td class="sticky" rowspan="1">Permit</td>
                                                <td class="sticky">Government Relation</td>
                                                <td class="sticky">Legal</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_permit_date = $sla_psm_date != 'N/A' ? date('Y-m-d', strtotime($sla_psm_date . ' +' . ($master_sla['Legal'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_psm_date ?></td>
                                                <td><?= $sla_permit_date?></td>
                                                <td><?= $master_sla['Legal'] ?? 'N/A' ?></td>
                                                <td><?= $row['sdg_desain_start_date'] ?></td>
                                                <td><?= $row['sdg_desain_submit_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_desain_submit_date'], $master_sla['Legal']);
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
                                                <td class="sticky" rowspan="2" style="background-color: #b6c7aa; color: white;">Tender</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Tender - Urugan</td>
                                                <td class="sticky" rowspan="2" style="background-color: #b6c7aa; color: white;">Procurement</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_tender_date = $sla_rab_date != 'N/A' ? date('Y-m-d', strtotime($sla_rab_date . ' +' . ($master_sla['Tender'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2"><?= $sla_rab_date ?></td>
                                                <td rowspan="2"><?= $sla_tender_date?></td>
                                                <td rowspan="2"><?= $master_sla['Tender'] ?? 'N/A' ?></td>
                                                <td rowspan="2"><?= $row['sdg_desain_start_date'] ?></td>
                                                <td rowspan="2"><?= $row['procurement_start_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['procurement_start_date'], $master_sla['Tender']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td rowspan="2"><?= $scoring ?>%</td>
                                                <td rowspan="2">
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Tender - Construction</td>
                                            </tr>
                                            <tr>
                                                <td class="sticky" rowspan="2" style="background-color: white">SPK</td>
                                                <td class="sticky" style="background-color: white">SPK - Urugan</td>
                                                <td class="sticky" rowspan="2" style="background-color: white">Procurement</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_spk_date = $sla_tender_date != 'N/A' ? date('Y-m-d', strtotime($sla_tender_date . ' +' . ($master_sla['SPK'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2" style="background-color: white"><?= $sla_tender_date ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $sla_spk_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['SPK'] ?? 'N/A' ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $row['procurement_start_date'] ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $row['spk_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['procurement_start_date'], $row['spk_date'], $master_sla['SPK']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                ?>
                                                <td rowspan="2" style="background-color: white"><?= $scoring ?>%</td>
                                                <td rowspan="2" style="background-color: white">
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky">SPK - Construction</td>
                                            </tr>
                                            <tr>
                                                <td class="sticky" rowspan="1" style="background-color: #b6c7aa; color: white;">Approval</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">SPK - Approval</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">TAF</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_spkfat_date = $sla_spk_date != 'N/A' ? date('Y-m-d', strtotime($sla_spk_date . ' +' . ($master_sla['SPK-FAT'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_spk_date ?></td>
                                                <td><?= $sla_spkfat_date?></td>
                                                <td><?= $master_sla['SPK-FAT'] ?? 'N/A' ?></td>
                                                <td><?= $row['spk_date'] ?></td>
                                                <td><?= $row['fat_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['spk_date'], $row['fat_date'], $master_sla['SPK-FAT']);
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
                                                <td class="sticky" rowspan="1">Kick Off</td>
                                                <td class="sticky">Kick Off Meeting with Vendor</td>
                                                <td class="sticky">SDG-Project</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_kom_date = $sla_spk_date != 'N/A' ? date('Y-m-d', strtotime($sla_spk_date . ' +' . ($master_sla['KOM'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_spk_date ?></td>
                                                <td><?= $sla_kom_date?></td>
                                                <td><?= $master_sla['KOM'] ?? 'N/A' ?></td>
                                                <td><?= $row['fat_date'] ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['fat_date'], $row['kom_date'], $master_sla['KOM']);
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
                                                <td class="sticky" rowspan="1" style="background-color: #b6c7aa; color: white;">Recruitment FL</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">Fulfillment FL</td>
                                                <td class="sticky" style="background-color: #b6c7aa; color: white;">HR</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('Y-m-d', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_kom_date ?></td>
                                                <td><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $row['ff3_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
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
                                                <td class="sticky" rowspan="1" style="background-color: white">Recruitment Qc</td>
                                                <td class="sticky" style="background-color: white">Fulfillment QC</td>
                                                <td class="sticky" style="background-color: white">HR</td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('Y-m-d', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td><?= $sla_kom_date ?></td>
                                                <td><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td><?= $row['kom_date'] ?></td>
                                                <td><?= $row['ff3_date'] ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
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