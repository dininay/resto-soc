<?php
include "../koneksi.php";

// Ambil total existing store dari tabel land dengan status_land = 'Aktif'
$sql_existing_store = "SELECT COUNT(*) as total_existing_store FROM land WHERE status_land = 'Aktif'";
$result_existing_store = $conn->query($sql_existing_store);
$total_existing_store = 0;
if ($result_existing_store->num_rows > 0) {
    $row_existing_store = $result_existing_store->fetch_assoc();
    $total_existing_store = $row_existing_store['total_existing_store'];
}
// Filter kode_lahan yang memiliki status_kom = 'Approve' di tabel resto
$sql_filter_approve_kom = "SELECT COUNT(*) as total_approve_kom FROM resto WHERE status_kom = 'Approve'";
$result_filter_approve_kom = $conn->query($sql_filter_approve_kom);
$total_approve_kom = 0;
if ($result_filter_approve_kom->num_rows > 0) {
    $row_filter_approve_kom = $result_filter_approve_kom->fetch_assoc();
    $total_approve_kom = $row_filter_approve_kom['total_approve_kom'];
}

// Jumlah total store yang tidak memiliki kode_lahan dengan status_kom = 'Approve'
$total_existing_store_without_approve_kom = $total_existing_store - $total_approve_kom;

// Ambil total ready to go dari tabel resto
$sql_ready_to_go = "SELECT COUNT(*) as total_ready_to_go FROM resto";
$result_ready_to_go = $conn->query($sql_ready_to_go);
$total_ready_to_go = 0;
if ($result_ready_to_go->num_rows > 0) {
    $row_ready_to_go = $result_ready_to_go->fetch_assoc();
    $total_ready_to_go = $row_ready_to_go['total_ready_to_go'];
}

// Count data for Hold (In Process) from hold_project table
$sql_hold = "SELECT COUNT(*) as hold_count FROM hold_project WHERE status_hold = 'In Process'";
$result_hold = $conn->query($sql_hold);
$hold_count = 0;
if ($result_hold->num_rows > 0) {
    $row_hold = $result_hold->fetch_assoc();
    $hold_count = $row_hold['hold_count'];
}

// Count data for In Progress (Aktif) from land table
$sql_in_progress = "SELECT COUNT(*) as in_progress_count FROM land WHERE status_land = 'Aktif'";
$result_in_progress = $conn->query($sql_in_progress);
$in_progress_count = 0;
if ($result_in_progress->num_rows > 0) {
    $row_in_progress = $result_in_progress->fetch_assoc();
    $in_progress_count = $row_in_progress['in_progress_count'];
}

// Count data for In Preparation (Approve) from resto table
$sql_in_preparation = "SELECT COUNT(*) as in_preparation_count FROM resto WHERE status_kom = 'Approve'";
$result_in_preparation = $conn->query($sql_in_preparation);
$in_preparation_count = 0;
if ($result_in_preparation->num_rows > 0) {
    $row_in_preparation = $result_in_preparation->fetch_assoc();
    $in_preparation_count = $row_in_preparation['in_preparation_count'];
}

// Ambil data dari tabel resto
$sql = "SELECT 
resto.*,
land.nama_lahan,
land.lokasi,
dokumen_loacd.kode_store,
summary_soc.status_go,
summary_soc.go_fix,
summary_soc.rto_act,
summary_soc.jam_ops,
summary_soc.project_sales,
summary_soc.crew_needed,
socdate_it.akun_gis,
        socdate_academy.kpt_1,
        socdate_academy.kpt_2,
        socdate_academy.kpt_3,
        socdate_fat.lamp_qris,
        socdate_fat.lamp_st,
        socdate_hr.tm,
        socdate_hr.lamp_tm,
        socdate_hr.ff_1,
        socdate_hr.ff_2,
        socdate_hr.ff_3,
        socdate_hr.lamp_ff1,
        socdate_hr.lamp_ff2,
        socdate_hr.lamp_ff3,
        socdate_hr.hot,
        socdate_hr.lamp_hot,
        socdate_ir.lamp_rabcs,
        socdate_ir.lamp_rabsecurity,
        socdate_it.kode_dvr,
        socdate_it.web_report,
        socdate_it.akun_gis,
        socdate_it.lamp_internet,
        socdate_it.lamp_cctv,
        socdate_it.lamp_printer,
        socdate_it.lamp_sound,
        socdate_it.lamp_config,
        socdate_legal.mou_parkirsampah,
        socdate_marketing.gmaps,
        socdate_marketing.lamp_gmaps,
        socdate_marketing.id_m_shopee,
        socdate_marketing.id_m_gojek,
        socdate_marketing.id_m_grab,
        socdate_marketing.email_resto,
        socdate_marketing.lamp_merchant,
        socdate_scm.lamp_sj,
        socdate_sdg.no_listrik,
        socdate_sdg.lamp_listrik,
        socdate_sdg.lamp_ka,
        socdate_sdg.lamp_ipal,
        socdate_sdg.lamp_eqp,
        socdate_sdg.lamp_ba,
        sdg_desain.lamp_permit,
        sdg_desain.lamp_pbg,
        re.start_date AS re_start_date,
        dokumen_loacd.start_date AS dlc_start_date,
        dokumen_loacd.end_date AS dlc_end_date,
        draft.start_date AS draft_start_date,
        draft.end_date AS draft_end_date,
        sdg_desain.end_date AS sdgd_end_date,
        obs_sdg.obs_date,
        obs_sdg.obslegal_date,
        sdg_rab.start_date AS rab_start_date,
        procurement.start_date AS procure_start_date,
            (CASE WHEN socdate_fat.lamp_qris IS NOT NULL AND socdate_fat.lamp_st IS NOT NULL THEN 100 ELSE 0 END) AS fat,
            (CASE WHEN socdate_academy.kpt_1 IS NOT NULL AND socdate_academy.kpt_2 IS NOT NULL AND socdate_academy.kpt_3 IS NOT NULL THEN 100 ELSE 0 END) AS academy,
            (CASE WHEN socdate_hr.tm IS NOT NULL AND socdate_hr.lamp_tm IS NOT NULL AND socdate_hr.ff_1 IS NOT NULL AND socdate_hr.lamp_ff1 IS NOT NULL AND socdate_hr.ff_2 IS NOT NULL AND socdate_hr.lamp_ff2 IS NOT NULL AND socdate_hr.ff_3 IS NOT NULL AND socdate_hr.lamp_ff3 IS NOT NULL AND socdate_hr.hot IS NOT NULL AND socdate_hr.lamp_hot IS NOT NULL THEN 100 ELSE 0 END) AS hr,
            (CASE WHEN socdate_ir.lamp_rabcs IS NOT NULL AND socdate_ir.lamp_rabsecurity IS NOT NULL THEN 100 ELSE 0 END) AS ir,
            (CASE WHEN socdate_it.kode_dvr IS NOT NULL AND socdate_it.web_report IS NOT NULL AND socdate_it.akun_gis IS NOT NULL AND socdate_it.lamp_internet IS NOT NULL AND socdate_it.lamp_cctv IS NOT NULL AND socdate_it.lamp_printer IS NOT NULL AND socdate_it.lamp_sound IS NOT NULL AND socdate_it.lamp_config IS NOT NULL THEN 100 ELSE 0 END) AS it,
            (CASE WHEN socdate_legal.mou_parkirsampah IS NOT NULL AND sdg_desain.lamp_pbg IS NOT NULL AND sdg_desain.lamp_permit IS NOT NULL THEN 100 ELSE 0 END) AS legal,
            (CASE WHEN socdate_marketing.gmaps IS NOT NULL AND socdate_marketing.lamp_gmaps IS NOT NULL AND socdate_marketing.id_m_shopee IS NOT NULL AND socdate_marketing.id_m_gojek IS NOT NULL AND socdate_marketing.id_m_grab IS NOT NULL AND socdate_marketing.email_resto IS NOT NULL AND socdate_marketing.lamp_merchant IS NOT NULL THEN 100 ELSE 0 END) AS marketing,
            (CASE WHEN socdate_scm.lamp_sj IS NOT NULL THEN 100 ELSE 0 END) AS scm,
            (CASE WHEN socdate_sdg.no_listrik IS NOT NULL AND socdate_sdg.lamp_listrik IS NOT NULL AND socdate_sdg.lamp_ka IS NOT NULL AND socdate_sdg.lamp_ipal IS NOT NULL AND socdate_sdg.lamp_eqp IS NOT NULL AND socdate_sdg.lamp_ba IS NOT NULL THEN 100 ELSE 0 END) AS sdg
        FROM land
        JOIN resto ON land.kode_lahan = resto.kode_lahan
        JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
        LEFT JOIN re ON land.kode_lahan = re.kode_lahan
        LEFT JOIN draft ON land.kode_lahan = draft.kode_lahan
        LEFT JOIN obs_sdg ON land.kode_lahan = obs_sdg.kode_lahan
        LEFT JOIN sdg_rab ON land.kode_lahan = sdg_rab.kode_lahan
        LEFT JOIN procurement ON land.kode_lahan = procurement.kode_lahan
        LEFT JOIN summary_soc ON land.kode_lahan = summary_soc.kode_lahan
        INNER JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
        INNER JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
        INNER JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
        INNER JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
        INNER JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
        INNER JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
        INNER JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
        INNER JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
        INNER JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
        INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan";
        
$result = $conn->query($sql);


$schedule = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
}

// Ambil data dari tabel resto
$sql_chartteam = "SELECT 
resto.*,
land.nama_lahan,
dokumen_loacd.kode_store,
summary_soc.status_go,
summary_soc.go_fix,
summary_soc.rto_act,
        socdate_academy.kpt_1,
        socdate_academy.kpt_2,
        socdate_academy.kpt_3,
        socdate_fat.lamp_qris,
        socdate_fat.lamp_st,
        socdate_hr.tm,
        socdate_hr.lamp_tm,
        socdate_hr.ff_1,
        socdate_hr.ff_2,
        socdate_hr.ff_3,
        socdate_hr.lamp_ff1,
        socdate_hr.lamp_ff2,
        socdate_hr.lamp_ff3,
        socdate_hr.hot,
        socdate_hr.lamp_hot,
        socdate_ir.lamp_rabcs,
        socdate_ir.lamp_rabsecurity,
        socdate_it.kode_dvr,
        socdate_it.web_report,
        socdate_it.akun_gis,
        socdate_it.lamp_internet,
        socdate_it.lamp_cctv,
        socdate_it.lamp_printer,
        socdate_it.lamp_sound,
        socdate_it.lamp_config,
        socdate_legal.mou_parkirsampah,
        socdate_marketing.gmaps,
        socdate_marketing.lamp_gmaps,
        socdate_marketing.id_m_shopee,
        socdate_marketing.id_m_gojek,
        socdate_marketing.id_m_grab,
        socdate_marketing.email_resto,
        socdate_marketing.lamp_merchant,
        socdate_scm.lamp_sj,
        socdate_sdg.no_listrik,
        socdate_sdg.lamp_listrik,
        socdate_sdg.lamp_ka,
        socdate_sdg.lamp_ipal,
        socdate_sdg.lamp_eqp,
        socdate_sdg.lamp_ba,
        sdg_desain.lamp_permit,
        sdg_desain.lamp_pbg, 
        AVG(CASE WHEN socdate_fat.lamp_qris IS NOT NULL AND socdate_fat.lamp_st IS NOT NULL THEN 100 ELSE 0 END) AS FAT,
        AVG(CASE WHEN socdate_academy.kpt_1 IS NOT NULL AND socdate_academy.kpt_2 IS NOT NULL AND socdate_academy.kpt_3 IS NOT NULL THEN 100 ELSE 0 END) AS ACADEMY,
        AVG(CASE WHEN socdate_hr.tm IS NOT NULL AND socdate_hr.lamp_tm IS NOT NULL AND socdate_hr.ff_1 IS NOT NULL AND socdate_hr.lamp_ff1 IS NOT NULL AND socdate_hr.ff_2 IS NOT NULL AND socdate_hr.lamp_ff2 IS NOT NULL AND socdate_hr.ff_3 IS NOT NULL AND socdate_hr.lamp_ff3 IS NOT NULL AND socdate_hr.hot IS NOT NULL AND socdate_hr.lamp_hot IS NOT NULL THEN 100 ELSE 0 END) AS HR,
        AVG(CASE WHEN socdate_ir.lamp_rabcs IS NOT NULL AND socdate_ir.lamp_rabsecurity IS NOT NULL THEN 100 ELSE 0 END) AS IR,
        AVG(CASE WHEN socdate_it.kode_dvr IS NOT NULL AND socdate_it.web_report IS NOT NULL AND socdate_it.akun_gis IS NOT NULL AND socdate_it.lamp_internet IS NOT NULL AND socdate_it.lamp_cctv IS NOT NULL AND socdate_it.lamp_printer IS NOT NULL AND socdate_it.lamp_sound IS NOT NULL AND socdate_it.lamp_config IS NOT NULL THEN 100 ELSE 0 END) AS IT,
        AVG(CASE WHEN socdate_legal.mou_parkirsampah IS NOT NULL AND sdg_desain.lamp_pbg IS NOT NULL AND sdg_desain.lamp_permit IS NOT NULL THEN 100 ELSE 0 END) AS LEGAL,
        AVG(CASE WHEN socdate_marketing.gmaps IS NOT NULL AND socdate_marketing.lamp_gmaps IS NOT NULL AND socdate_marketing.id_m_shopee IS NOT NULL AND socdate_marketing.id_m_gojek IS NOT NULL AND socdate_marketing.id_m_grab IS NOT NULL AND socdate_marketing.email_resto IS NOT NULL AND socdate_marketing.lamp_merchant IS NOT NULL THEN 100 ELSE 0 END) AS MARKETING,
        AVG(CASE WHEN socdate_scm.lamp_sj IS NOT NULL THEN 100 ELSE 0 END) AS SCM,
        AVG(CASE WHEN socdate_sdg.no_listrik IS NOT NULL AND socdate_sdg.lamp_listrik IS NOT NULL AND socdate_sdg.lamp_ka IS NOT NULL AND socdate_sdg.lamp_ipal IS NOT NULL AND socdate_sdg.lamp_eqp IS NOT NULL AND socdate_sdg.lamp_ba IS NOT NULL THEN 100 ELSE 0 END) AS SDG
        FROM land
        JOIN resto ON land.kode_lahan = resto.kode_lahan
        JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
        LEFT JOIN summary_soc ON land.kode_lahan = summary_soc.kode_lahan
        INNER JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
        INNER JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
        INNER JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
        INNER JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
        INNER JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
        INNER JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
        INNER JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
        INNER JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
        INNER JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
        INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan";
        
$result_chartteam = $conn->query($sql_chartteam);

$departmentData = [
    'FAT' => 0,
    'Academy' => 0,
    'HR' => 0,
    'IR' => 0,
    'IT' => 0,
    'Legal' => 0,
    'Marketing' => 0,
    'SCM' => 0,
    'SDG' => 0
];

if ($result_chartteam->num_rows > 0) {
    while ($row = $result_chartteam->fetch_assoc()) {
        $departmentData = [
            'FAT' => round($row['FAT'], 2),
            'Academy' => round($row['ACADEMY'], 2),
            'HR' => round($row['HR'], 2),
            'IR' => round($row['IR'], 2),
            'IT' => round($row['IT'], 2),
            'Legal' => round($row['LEGAL'], 2),
            'Marketing' => round($row['MARKETING'], 2),
            'SCM' => round($row['SCM'], 2),
            'SDG' => round($row['SDG'], 2)
        ];
    }
}

$departmentDataJSON = json_encode($departmentData);
// Array untuk menyimpan data $fix per kode_lahan
$kodeLahans = [];
$fixValues = [];

// Proses hasil query
foreach ($schedule as $row) {
    $kodeLahan = $row['kode_lahan'];
    $fix = ($row['fat'] + $row['academy'] + $row['hr'] + $row['ir'] + $row['it'] + $row['legal'] + $row['marketing'] + $row['scm'] + $row['sdg']) / 9;

    // Masukkan ke dalam array
    $kodeLahans[] = $kodeLahan;
    $fixValues[] = round($fix, 2); // Pembulatan 2 angka di belakang koma
}

// Konversi array PHP ke JSON untuk digunakan di dalam JavaScript
$kodeLahansJSON = json_encode($kodeLahans);
$fixValuesJSON = json_encode($fixValues);
// echo "<pre>";
// print_r($schedule);
// echo "</pre>";

// Ambil data status_go dari tabel summary_soc
$sql_status_go = "
SELECT 
    COUNT(CASE WHEN status_go = 'Accelerated' THEN 1 END) as accelerated_count,
    COUNT(CASE WHEN status_go = 'Delayed' THEN 1 END) as delayed_count,
    COUNT(CASE WHEN status_go = 'On Schedule' THEN 1 END) as on_schedule_count
FROM summary_soc";
$result_status_go = $conn->query($sql_status_go);

$statusData = [
    'accelerated' => 0,
    'delayed' => 0,
    'on schedule' => 0
];

if ($result_status_go->num_rows > 0) {
    $row_status_go = $result_status_go->fetch_assoc();
    $statusData['accelerated'] = $row_status_go['accelerated_count'];
    $statusData['delayed'] = $row_status_go['delayed_count'];
    $statusData['on schedule'] = $row_status_go['on_schedule_count'];
}

// Ambil total existing store (In Progress) per bulan dari tabel land
$sql_existing_store = "SELECT COUNT(*) as total, MONTH(status_date) as month 
                      FROM land 
                      WHERE status_land = 'Aktif' 
                      GROUP BY MONTH(status_date)";
$result_existing_store = $conn->query($sql_existing_store);
$existing_store_data = array();
while ($row = $result_existing_store->fetch_assoc()) {
    $existing_store_data[$row['month']] = $row['total'];
}

// Ambil total ready to go store (In Preparation) per bulan dari tabel resto
$sql_ready_to_go = "SELECT COUNT(*) as total, MONTH(kom_date) as month 
                   FROM resto 
                   WHERE status_kom = 'Approve' 
                   GROUP BY MONTH(kom_date)";
$result_ready_to_go = $conn->query($sql_ready_to_go);
$ready_to_go_data = array();
while ($row = $result_ready_to_go->fetch_assoc()) {
    $ready_to_go_data[$row['month']] = $row['total'];
}

// Buat chart data untuk ditampilkan
$months = range(1, 12); // Bulan dari 1 sampai 12
$existing_store_chart_data = array();
$ready_to_go_chart_data = array();

foreach ($months as $month) {
    // Existing store (In Progress)
    if (isset($existing_store_data[$month])) {
        $existing_store_chart_data[] = $existing_store_data[$month];
    } else {
        $existing_store_chart_data[] = 0;
    }

    // Ready to go store (In Preparation)
    if (isset($ready_to_go_data[$month])) {
        $ready_to_go_chart_data[] = $ready_to_go_data[$month];
    } else {
        $ready_to_go_chart_data[] = 0;
    }
}


function getStatusRemarks($inProgressPercentage, $inPreparationPercentage) {
    if ($inProgressPercentage == 100 && $inPreparationPercentage == 100) {
        return 'Good';
    } elseif ($inProgressPercentage <= 50 || $inPreparationPercentage <= 50) {
        return 'Bad';
    } else {
        return 'Bad';
    }
}

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'Good':
            return 'success';
        case 'Bad':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard Resto | Mie Gacoan</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
    <style>
        .schedule-card {
            margin-bottom: 20px;
        }
        .schedule-card .card-body {
            padding: 20px;
        }
        .schedule-card .card-title {
            margin-bottom: 20px;
        }
    </style>
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
                    <h1 class="mr-2">Home</h1>
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li>Home</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row justify-content-center">
                    <!-- ICON BG-->
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body">
                                <i class="i-Add-User mr-3"></i>
                                <h5 class="text-muted mt-2 mb-2">Total Existing Store</h5>
                                <div class="content">
                                    <p class="text-primary text-24 line-height-1 mb-2"><?php echo $total_existing_store_without_approve_kom; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body">
                                <i class="i-Financial mr-3"></i>
                                <h5 class="text-muted mt-2 mb-2">Total Ready To Go Store</h5>
                                <div class="content">
                                    <p class="text-primary text-24 line-height-1 mb-2"><?php echo $total_ready_to_go; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Checkout-Basket"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Orders</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">80</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Money-2"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Expense</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">$1200</p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="card mb-4">
                        <div class="card-body p-0">
                                <div class="card-title border-bottom d-flex align-items-center m-0 p-3">
                                    <span>Monthly Schedule - <?php echo date('F Y'); ?></span>
                                    <span class="flex-grow-1"></span>
                                    <span class="badge badge-pill badge-warning">Updated daily</span>
                                </div>
                                <div class="d-flex flex-wrap p-3">
                                    <?php
                                    // Get the current month and year
                                    $currentMonth = date('m');
                                    $currentYear = date('Y');
                                    // Get the number of days in the current month
                                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
                                    
                                    // Loop through each day of the month
                                    for ($day = 1; $day <= $daysInMonth; $day++) {
                                        // Create a DateTime object for the current day
                                        $date = new DateTime("$currentYear-$currentMonth-$day");
                                        $dateString = $date->format('Y-m-d');
                                        
                                        echo "<div class='col-md-2 col-sm-4 mb-3'>";
                                        echo "<div class='card border p-3'>";
                                        echo "<h6>{$date->format('d')}</h6>"; // Display only the day of the month

                                        // Check if there's any event for this date
                                        $eventFound = false;

                                        // Check for sla_spk
                                        foreach ($schedule as $event) {
                                            if ($event['sla_spk'] == $dateString) {
                                                echo "<h6>Deadline SPK</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        // Check for sla_kom
                                        foreach ($schedule as $event) {
                                            if ($event['sla_kom'] == $dateString) {
                                                echo "<h6>Kick Off Meeting</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        // Check for sla_steqp
                                        foreach ($schedule as $event) {
                                            if ($event['sla_steqp'] == $dateString) {
                                                echo "<h6>ST EQuipment</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        // Check for sla_stkonstruksi
                                        foreach ($schedule as $event) {
                                            if ($event['sla_stkonstruksi'] == $dateString) {
                                                echo "<h6>ST Kontraktor</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        // Check for gostore_date
                                        foreach ($schedule as $event) {
                                            if ($event['gostore_date'] == $dateString) {
                                                echo "<h6>GO</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        // Check for gostore_date
                                        foreach ($schedule as $event) {
                                            if ($event['rto_act'] == $dateString) {
                                                echo "<h6>RTO</h6>";
                                                echo "<h6>Nama : {$event['nama_lahan']}</h6>";
                                                echo "<h6>Kode Store : {$event['kode_store']}</h6>";
                                                $eventFound = true;
                                                break;
                                            }
                                        }

                                        if (!$eventFound) {
                                            echo "<p></p>";
                                            echo "<p></p>";
                                            echo "<p></p>";
                                            echo "<p></p>";
                                            echo "<p></p>";
                                        }
                                        
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card o-hidden mb-4">
                                <div class="card-header d-flex align-items-center border-0">
                                    <h3 class="w-50 float-left card-title m-0">Summary SOC</h3>
                                    <div class="dropdown dropleft text-right w-50 float-right">
                                        <!-- <button class="btn bg-gray-100" id="dropdownMenuButton1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon i-Gear-2"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <a class="dropdown-item" href="#">Add new user</a>
                                            <a class="dropdown-item" href="#">View All users</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div> -->
                                    </div>
                                </div>
                                <div>
                                    <div class="table-responsive">
                                        <table class="table text-center" id="user_table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Store</th>
                                                    <th scope="col">Number Store (GIS)</th>
                                                    <th scope="col">Kode Store</th>
                                                    <th scope="col">GO Date</th>
                                                    <th scope="col">Bussiness Hour</th>
                                                    <th scope="col">Crew Needed</th>
                                                    <th scope="col">Address</th>
                                                    <th scope="col">Project Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $noUrut = 1;
                                                foreach ($schedule as $event) {
                                                    echo "<tr>";
                                                    echo "<th scope='row'>{$noUrut}</th>";
                                                    echo "<td>{$event['kode_lahan']}</td>";
                                                    echo "<td>{$event['akun_gis']}</td>";
                                                    echo "<td>{$event['kode_store']}</td>";
                                                    echo "<td>{$event['go_fix']}</td>";
                                                    echo "<td>{$event['jam_ops']}</td>";
                                                    echo "<td>{$event['crew_needed']}</td>";
                                                    echo "<td>{$event['lokasi']}</td>";
                                                    echo "<td>{$event['project_sales']}</td>";

                                                    echo "</tr>";
                                                    $noUrut++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card o-hidden mb-4">
                                <div class="card-header d-flex align-items-center border-0">
                                    <h3 class="w-50 float-left card-title m-0">Store by Revision Date</h3>
                                    <div class="dropdown dropleft text-right w-50 float-right">
                                        <!-- <button class="btn bg-gray-100" id="dropdownMenuButton1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon i-Gear-2"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <a class="dropdown-item" href="#">Add new user</a>
                                            <a class="dropdown-item" href="#">View All users</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div> -->
                                    </div>
                                </div>
                                <div>
                                    <div class="table-responsive">
                                        <table class="table text-center" id="user_table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No Urut</th>
                                                    <th scope="col">ID Lahan</th>
                                                    <th scope="col">Nama</th>
                                                    <th scope="col">Kode Store</th>
                                                    <th scope="col">Tanggal GO Awal</th>
                                                    <th scope="col">Revision Date</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $noUrut = 1;
                                                foreach ($schedule as $event) {
                                                    echo "<tr>";
                                                    echo "<th scope='row'>{$noUrut}</th>";
                                                    echo "<td>{$event['kode_lahan']}</td>";
                                                    echo "<td>{$event['nama_lahan']}</td>";
                                                    echo "<td>{$event['kode_store']}</td>";
                                                    echo "<td>{$event['gostore_date']}</td>";
                                                    echo "<td>{$event['go_fix']}</td>";
                                                    echo "<td>{$event['status_go']}</td>";
                                                    echo "</tr>";
                                                    $noUrut++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Status GO Overview</div>
                                    <div id="echartGo" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Filter Date Range</div>
                                    <div id="storeChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">New Store Phase</div>
                                    <div id="echartPhase" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">New Store Rank</div>
                                    <div id="storeRank" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Performance Acheivement New Store</div>
                                    <div id="achieveChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Department Performance</div>
                                    <div id="teamChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card o-hidden mb-4">
                                <div class="card-header d-flex align-items-center border-0">
                                    <h3 class="w-50 float-left card-title m-0">Projects</h3>
                                    <div class="dropdown dropleft text-right w-50 float-right">
                                        <!-- <button class="btn bg-gray-100" id="dropdownMenuButton1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon i-Gear-2"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <a class="dropdown-item" href="#">Add new user</a>
                                            <a class="dropdown-item" href="#">View All users</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div> -->
                                    </div>
                                </div>
                                <div>
                                    <div class="table-responsive">
                                        <table class="table text-center" id="user_table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Projects</th>
                                                    <th scope="col">Grand Opening Date</th>
                                                    <th scope="col">In Progress</th>
                                                    <th scope="col">In Preparation</th>
                                                    <th scope="col">Total</th>
                                                    <th scope="col">Final Performance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $noUrut = 1;
                                                foreach ($schedule as $event) {
                                                    echo "<tr>";
                                                    echo "<th scope='row'>{$noUrut}</th>";
                                                    echo "<td>{$event['kode_lahan']}</td>";
                                                    // Check and set Grand Opening Date
                                                    $grandOpeningDate = !empty($event['go_fix']) ? $event['go_fix'] : $event['gostore_date'];
                                                    echo "<td>{$grandOpeningDate}</td>";
                                                    
                                                    // Calculate In Progress Percentage
                                                    $inProgressFields = [
                                                        're_date', 're_start_date', 'nego_date', 'vl_date',
                                                        'dlc_start_date', 'dlc_end_date', 'draft_start_date',
                                                        'draft_end_date', 'sdgd_start_date', 'sdgd_end_date',
                                                        'obs_date', 'obslegal_date', 'rab_start_date', 'procure_start_date'
                                                    ];
                                                    $inProgressCount = count($inProgressFields);
                                                    $inProgressComplete = 0;

                                                    foreach ($inProgressFields as $field) {
                                                        if (!empty($event[$field])) {
                                                            $inProgressComplete += 100; // Set value to 100 if not null
                                                        }
                                                    }
                                                    $inProgressPercentage = ($inProgressComplete / ($inProgressCount * 100)) * 100;
                                                    $inProgressPercentage = number_format($inProgressPercentage, 2);

                                                    echo "<td>{$inProgressPercentage}%</td>";

                                                    // Calculate In Preparation Percentage
                                                    $inPreparationFields = [
                                                        'spk_date', 'fat_date', 'kom_date', 'steqp_date', 'stkonstruksi_date'
                                                    ];
                                                    $inPreparationCount = count($inPreparationFields);
                                                    $inPreparationComplete = 0;

                                                    foreach ($inPreparationFields as $field) {
                                                        if (!empty($event[$field])) {
                                                            $inPreparationComplete += 100; // Set value to 100 if not null
                                                        }
                                                    }
                                                    $inPreparationPercentage = ($inPreparationComplete / ($inPreparationCount * 100)) * 100;
                                                    $inPreparationPercentage = number_format($inPreparationPercentage, 2);

                                                    echo "<td>{$inPreparationPercentage}%</td>";

                                                    // Calculate Total and Final Performance
                                                    $totalPercentage = ($inProgressPercentage + $inPreparationPercentage) / 2;
                                                    $totalPercentage = number_format($totalPercentage, 2);
                                                    echo "<td>{$totalPercentage}%</td>";

                                                    $finalPerformance = getStatusRemarks($inProgressPercentage, $inPreparationPercentage);
                                                    $badge_color = getStatusBadgeColor($finalPerformance);

                                                    echo "<td>
                                                            <span class='badge rounded-pill badge-{$badge_color}'>
                                                                {$finalPerformance}
                                                            </span>
                                                        </td>";

                                                    echo "</tr>";
                                                    $noUrut++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <!-- <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="card card-chart-bottom o-hidden mb-4">
                                        <div class="card-body">
                                            <div class="text-muted">Last Month Sales</div>
                                            <p class="mb-4 text-primary text-24">$40250</p>
                                        </div>
                                        <div id="echart1" style="height: 260px;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card card-chart-bottom o-hidden mb-4">
                                        <div class="card-body">
                                            <div class="text-muted">Last Week Sales</div>
                                            <p class="mb-4 text-warning text-24">$10250</p>
                                        </div>
                                        <div id="echart2" style="height: 260px;"></div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header d-flex align-items-center border-0">
                                            <h3 class="w-50 float-left card-title m-0">New Users</h3>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                                <button class="btn bg-gray-100" id="dropdownMenuButton1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon i-Gear-2"></i></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1"><a class="dropdown-item" href="#">Add new user</a><a class="dropdown-item" href="#">View All users</a><a class="dropdown-item" href="#">Something else here</a></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="table-responsive">
                                                <table class="table text-center" id="user_table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Avatar</th>
                                                            <th scope="col">Email</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>Smith Doe</td>
                                                            <td><img class="rounded-circle m-0 avatar-sm-table" src="../dist-assets/images/faces/1.jpg" alt="" /></td>
                                                            <td>Smith@gmail.com</td>
                                                            <td><span class="badge badge-success">Active</span></td>
                                                            <td><a class="text-success mr-2" href="#"><i class="nav-icon i-Pen-2 font-weight-bold"></i></a><a class="text-danger mr-2" href="#"><i class="nav-icon i-Close-Window font-weight-bold"></i></a></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>Jhon Doe</td>
                                                            <td><img class="rounded-circle m-0 avatar-sm-table" src="../dist-assets/images/faces/1.jpg" alt="" /></td>
                                                            <td>Jhon@gmail.com</td>
                                                            <td><span class="badge badge-info">Pending</span></td>
                                                            <td><a class="text-success mr-2" href="#"><i class="nav-icon i-Pen-2 font-weight-bold"></i></a><a class="text-danger mr-2" href="#"><i class="nav-icon i-Close-Window font-weight-bold"></i></a></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>Alex</td>
                                                            <td><img class="rounded-circle m-0 avatar-sm-table" src="../dist-assets/images/faces/1.jpg" alt="" /></td>
                                                            <td>Otto@gmail.com</td>
                                                            <td><span class="badge badge-warning">Not Active</span></td>
                                                            <td><a class="text-success mr-2" href="#"><i class="nav-icon i-Pen-2 font-weight-bold"></i></a><a class="text-danger mr-2" href="#"><i class="nav-icon i-Close-Window font-weight-bold"></i></a></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>Mathew Doe</td>
                                                            <td><img class="rounded-circle m-0 avatar-sm-table" src="../dist-assets/images/faces/1.jpg" alt="" /></td>
                                                            <td>Mathew@gmail.com</td>
                                                            <td><span class="badge badge-success">Active</span></td>
                                                            <td><a class="text-success mr-2" href="#"><i class="nav-icon i-Pen-2 font-weight-bold"></i></a><a class="text-danger mr-2" href="#"><i class="nav-icon i-Close-Window font-weight-bold"></i></a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <!-- <div class="col-lg-6 col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Top Selling Products</div>
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3"><img class="avatar-lg mb-3 mb-sm-0 rounded mr-sm-3" src="../dist-assets/images/products/headphone-4.jpg" alt="" />
                                        <div class="flex-grow-1">
                                            <h5><a href="">Wireless Headphone E23</a></h5>
                                            <p class="m-0 text-small text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                                            <p class="text-small text-danger m-0">$450
                                                <del class="text-muted">$500</del>
                                            </p>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary mt-3 mb-3 m-sm-0 btn-rounded btn-sm">
                                                View
                                                details
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3"><img class="avatar-lg mb-3 mb-sm-0 rounded mr-sm-3" src="../dist-assets/images/products/headphone-2.jpg" alt="" />
                                        <div class="flex-grow-1">
                                            <h5><a href="">Wireless Headphone Y902</a></h5>
                                            <p class="m-0 text-small text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                                            <p class="text-small text-danger m-0">$550
                                                <del class="text-muted">$600</del>
                                            </p>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary mt-3 mb-3 m-sm-0 btn-sm btn-rounded">
                                                View
                                                details
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3"><img class="avatar-lg mb-3 mb-sm-0 rounded mr-sm-3" src="../dist-assets/images/products/headphone-3.jpg" alt="" />
                                        <div class="flex-grow-1">
                                            <h5><a href="">Wireless Headphone E09</a></h5>
                                            <p class="m-0 text-small text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                                            <p class="text-small text-danger m-0">$250
                                                <del class="text-muted">$300</del>
                                            </p>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary mt-3 mb-3 m-sm-0 btn-sm btn-rounded">
                                                View
                                                details
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3"><img class="avatar-lg mb-3 mb-sm-0 rounded mr-sm-3" src="../dist-assets/images/products/headphone-4.jpg" alt="" />
                                        <div class="flex-grow-1">
                                            <h5><a href="">Wireless Headphone X89</a></h5>
                                            <p class="m-0 text-small text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                                            <p class="text-small text-danger m-0">$450
                                                <del class="text-muted">$500</del>
                                            </p>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary mt-3 mb-3 m-sm-0 btn-sm btn-rounded">
                                                View
                                                details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                        </div> -->
                        <!-- <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body p-0">
                                    <h5 class="card-title m-0 p-3">Last 20 Day Leads</h5>
                                    <div id="echart3" style="height: 360px;"></div>
                                </div>
                            </div>
                        </div> -->
                    </div><!-- end of main-content -->
                <!-- Footer Start -->
                <div class="flex-grow-1"></div>
                <!-- <div class="app-footer">
                    <div class="row">
                        <div class="col-md-9">
                            <p><strong>Gull - Laravel + Bootstrap 4 admin template</strong></p>
                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero quis beatae officia saepe perferendis voluptatum minima eveniet voluptates dolorum, temporibus nisi maxime nesciunt totam repudiandae commodi sequi dolor quibusdam
                                <sunt></sunt>
                            </p>
                        </div>
                    </div>
                    <div class="footer-bottom border-top pt-3 d-flex flex-column flex-sm-row align-items-center">
                        <a class="btn btn-primary text-white btn-rounded" href="https://themeforest.net/item/gull-bootstrap-laravel-admin-dashboard-template/23101970" target="_blank">Buy Gull HTML</a>
                        <span class="flex-grow-1"></span>
                        <div class="d-flex align-items-center">
                            <img class="logo" src="../dist-assets/images/logo.png" alt="">
                            <div>
                                <p class="m-0">&copy; 2018 Gull HTML</p>
                                <p class="m-0">All rights reserved</p>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- fotter end -->
            </div>
        </div>
    </div><!-- ============ Search UI Start ============= -->
    <div class="search-ui">
        <div class="search-header">
            <img src="dist-assets/images/logo.png" alt="" class="logo">
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
    <script src="../dist-assets/js/plugins/echarts.min.js"></script>
    <script src="../dist-assets/js/scripts/echart.options.min.js"></script>
    <script src="../dist-assets/js/scripts/dashboard.v1.script.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script>
        // Mengirim data status_go ke JavaScript
        var statusData = <?php echo json_encode($statusData); ?>;

        // Inisialisasi ECharts
        var echartElemPie = document.getElementById("echartGo");

        if (echartElemPie) {
            var echartPie = echarts.init(echartElemPie);
            echartPie.setOption({
                title: {
                    text: '',
                    left: 'center'
                },
                color: ["#DCA47C", "#405D72", "#758694"],
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [
                    {
                        name: "Status GO Overview",
                        type: "pie",
                        radius: "50%",
                        center: ["50%", "50%"],
                        data: [
                            {
                                value: statusData.accelerated,
                                name: "Accelerated",
                            },
                            {
                                value: statusData.delayed,
                                name: "Delayed",
                            },
                            {
                                value: statusData['on schedule'],
                                name: "On Schedule",
                            }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)",
                            },
                        },
                    },
                ],
            });

            // Resize chart on window resize
            window.addEventListener("resize", function () {
                setTimeout(function () {
                    echartPie.resize();
                }, 500);
            });
        }
    </script>

    <script>
        // Sample data (replace with actual data)
        var phaseData = {
            'Hold': <?php echo $hold_count; ?>,
            'In Progress': <?php echo $total_existing_store_without_approve_kom; ?>,
            'In Preparation': <?php echo $in_preparation_count; ?>
        };

        // Inisialisasi ECharts untuk echartPhase
        var echartElemPhase = document.getElementById("echartPhase");

        if (echartElemPhase) {
            var echartPhase = echarts.init(echartElemPhase);
            echartPhase.setOption({
                title: {
                    text: '',
                    left: 'center'
                },
                color: ["#DCA47C", "#405D72", "#758694"],
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [
                    {
                        name: 'Phase',
                        type: 'pie',
                        radius: '50%',
                        data: [
                            { value: phaseData['Hold'], name: 'Hold' },
                            { value: phaseData['In Progress'], name: 'In Progress' },
                            { value: phaseData['In Preparation'], name: 'In Preparation' }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            });

            // Resize chart on window resize
            window.addEventListener("resize", function () {
                setTimeout(function () {
                    echartPhase.resize();
                }, 500);
            });
        }
    </script>
    <script>
        // Inisialisasi chart menggunakan ECharts
        var storeChart = echarts.init(document.getElementById('storeChart'));

        // Data untuk chart
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var existingData = <?php echo json_encode($existing_store_chart_data); ?>;
        var readyData = <?php echo json_encode($ready_to_go_chart_data); ?>;

        // Option untuk chart
        var option = {
            title: {
                text: ''
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['Existing Store (In Progress)', 'Ready To Go Store (In Preparation)'],
                align: 'left'
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: months
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Existing Store (In Progress)',
                    type: 'bar',
                    stack: 'total',
                    itemStyle: {
                        color: '#DCA47C' // Warna untuk Existing Store (In Progress)
                    },
                    data: existingData
                },
                {
                    name: 'Ready To Go Store (In Preparation)',
                    type: 'bar',
                    stack: 'total',
                    itemStyle: {
                        color: '#405D72' // Warna untuk Ready To Go Store (In Preparation)
                    },
                    data: readyData
                }
            ]
        };

        // Gunakan setOption untuk mengatur data dan opsi ke chart
        storeChart.setOption(option);
    </script>


    <script>
        var storeChart = echarts.init(document.getElementById('storeRank'));

        var option = {
            title: {
                text: ''
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            xAxis: {
                type: 'category',
                data: <?php echo $kodeLahansJSON; ?>,
                axisLabel: {
                    rotate: 45 // Rotasi label sumbu X agar lebih mudah dibaca
                }
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    formatter: '{value}%' // Format nilai sumbu Y dengan persen
                }
            },
            series: [{
                name: 'Store Rank',
                type: 'bar',
                data: <?php echo $fixValuesJSON; ?>,
                itemStyle: {
                    color: '#405D72' // Warna untuk bar chart
                }
            }]
        };

        storeChart.setOption(option);
    </script>

    <script>
        // Sample PHP data converted to JavaScript
        var fixValues = <?php echo json_encode($fixValues); ?>;

        // Categorize data into Good and Bad
        var goodCount = 0;
        var badCount = 0;

        fixValues.forEach(function(value) {
            if (value > 30) {
                goodCount++;
            } else {
                badCount++;
            }
        });

        // Initialize chart
        var achieveChart = echarts.init(document.getElementById('achieveChart'));

        // Chart options
        var option = {
            title: {
                text: '',
                left: 'center'
            },
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left'
            },
            series: [
                {
                    name: 'Achievement',
                    type: 'pie',
                    radius: '50%',
                    data: [
                        { value: goodCount, name: 'Good', itemStyle: { color: '#405D72' } },
                        { value: badCount, name: 'Bad', itemStyle: { color: '#758694' } }
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // Set options
        achieveChart.setOption(option);
    </script>

    <script>
        // Mengirim data department ke JavaScript
        var departmentData = <?php echo $departmentDataJSON; ?>;

        // Inisialisasi chart menggunakan ECharts
        var teamChart = echarts.init(document.getElementById('teamChart'));

        // Data untuk chart
        var departments = ['FAT', 'Academy', 'HR', 'IR', 'IT', 'Legal', 'Marketing', 'SCM', 'SDG'];
        var values = [
            departmentData.FAT,
            departmentData.Academy,
            departmentData.HR,
            departmentData.IR,
            departmentData.IT,
            departmentData.Legal,
            departmentData.Marketing,
            departmentData.SCM,
            departmentData.SDG
        ];

        // Option untuk chart
        var option = {
            title: {
                text: ''
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            xAxis: {
                type: 'category',
                data: departments
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                data: values,
                type: 'bar',
                itemStyle: {
                    color: function(params) {
                        var colors = ['#DCA47C', '#758694'];
                        return colors[params.dataIndex % colors.length];
                    }
                }
            }]
        };

        // Gunakan setOption untuk mengatur data dan opsi ke chart
        teamChart.setOption(option);

        // Resize chart on window resize
        window.addEventListener("resize", function () {
            setTimeout(function () {
                teamChart.resize();
            }, 500);
        });
    </script>
</body>

</html>