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
re.sla_date as re_sla_date,
re.slavl_date,
dokumen_loacd.slavd_date,
dokumen_loacd.slavdlegal_date,
dokumen_loacd.sla_tafkode,
dokumen_loacd.slaloa_date,
re.slavllegal_date,
draft.slapsm_date,
draft.slafatpsm_date,
draft.slabod_date,
draft.sladraftre_date,
draft.sla_kondisilahan,
sdg_desain.sla_spkwo,
sdg_desain.sla_survey,
sdg_desain.sla_obslegal,
sdg_desain.sla_urugan,
sdg_desain.sla_date as sdgdesain_sla_date,
equipment.*,
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
        socdate_sdg.sumber_air,
        socdate_sdg.kesesuaian_ujilab,
        socdate_sdg.filter_air,
        socdate_sdg.lamp_filterair,
        socdate_sdg.lamp_ujilab,
        socdate_sdg.debit_airsumur,
        socdate_sdg.debit_airpdam,
        socdate_sdg.id_pdam,
        socdate_sdg.sumber_listrik,
        socdate_sdg.form_pengajuanlistrik,
        socdate_sdg.hasil_va,
        socdate_sdg.id_pln,
        socdate_sdg.lampwo_reqipal,
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
            (CASE WHEN socdate_sdg.sumber_air IS NOT NULL AND socdate_sdg.kesesuaian_ujilab IS NOT NULL AND socdate_sdg.filter_air IS NOT NULL AND socdate_sdg.debit_airsumur IS NOT NULL AND socdate_sdg.debit_airpdam IS NOT NULL AND socdate_sdg.id_pdam IS NOT NULL AND socdate_sdg.sumber_listrik IS NOT NULL AND socdate_sdg.form_pengajuanlistrik IS NOT NULL AND socdate_sdg.hasil_va IS NOT NULL AND socdate_sdg.id_pln IS NOT NULL AND socdate_sdg.lampwo_reqipal IS NOT NULL THEN 100 ELSE 0 END) AS sdg
        FROM land
        JOIN resto ON land.kode_lahan = resto.kode_lahan
        JOIN equipment ON land.kode_lahan = equipment.kode_lahan
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
        INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
        GROUP BY land.kode_lahan";
        
$result = $conn->query($sql);


$schedule = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
}

// Get selected month and year from the form
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Prepare SQL query with date filtering
$sql_chartteam = "
SELECT 
    AVG(CASE WHEN YEAR(socdate_fat.fat_date) = $selectedYear AND MONTH(socdate_fat.fat_date) = $selectedMonth THEN 100 ELSE 0 END) AS FAT,
    AVG(CASE WHEN (YEAR(socdate_academy.kpt_date1) = $selectedYear AND MONTH(socdate_academy.kpt_date1) = $selectedMonth)
        OR (YEAR(socdate_academy.kpt_date2) = $selectedYear AND MONTH(socdate_academy.kpt_date2) = $selectedMonth)
        OR (YEAR(socdate_academy.kpt_date3) = $selectedYear AND MONTH(socdate_academy.kpt_date3) = $selectedMonth) THEN 100 ELSE 0 END) AS Academy,
    AVG(CASE WHEN (YEAR(socdate_hr.ff1_date) = $selectedYear AND MONTH(socdate_hr.ff1_date) = $selectedMonth)
        OR (YEAR(socdate_hr.ff2_date) = $selectedYear AND MONTH(socdate_hr.ff2_date) = $selectedMonth)
        OR (YEAR(socdate_hr.ff3_date) = $selectedYear AND MONTH(socdate_hr.ff3_date) = $selectedMonth)
        OR (YEAR(socdate_hr.hot_date) = $selectedYear AND MONTH(socdate_hr.hot_date) = $selectedMonth) THEN 100 ELSE 0 END) AS HR,
    AVG(CASE WHEN YEAR(socdate_ir.ir_date) = $selectedYear AND MONTH(socdate_ir.ir_date) = $selectedMonth THEN 100 ELSE 0 END) AS IR,
    AVG(CASE WHEN YEAR(socdate_it.it_date) = $selectedYear AND MONTH(socdate_it.it_date) = $selectedMonth THEN 100 ELSE 0 END) AS IT,
    AVG(CASE WHEN YEAR(socdate_legal.sampahparkir_date) = $selectedYear AND MONTH(socdate_legal.sampahparkir_date) = $selectedMonth THEN 100 ELSE 0 END) AS Legal,
    AVG(CASE WHEN YEAR(socdate_marketing.marketing_date) = $selectedYear AND MONTH(socdate_marketing.marketing_date) = $selectedMonth THEN 100 ELSE 0 END) AS Marketing,
    AVG(CASE WHEN YEAR(socdate_scm.sj_date) = $selectedYear AND MONTH(socdate_scm.sj_date) = $selectedMonth THEN 100 ELSE 0 END) AS SCM,
    AVG(CASE WHEN YEAR(socdate_sdg.sdgsumber_date) = $selectedYear AND MONTH(socdate_sdg.sdgsumber_date) = $selectedMonth THEN 100 ELSE 0 END) AS SDG
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
GROUP BY land.kode_lahan";

// Execute query and process results
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
            'Academy' => round($row['Academy'], 2),
            'HR' => round($row['HR'], 2),
            'IR' => round($row['IR'], 2),
            'IT' => round($row['IT'], 2),
            'Legal' => round($row['Legal'], 2),
            'Marketing' => round($row['Marketing'], 2),
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
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : 'all';

// Initialize status data
$statusData = [
    'accelerated' => 0,
    'delayed' => 0,
    'on schedule' => 0,
];

// Prepare the SQL query based on selected month
$sql_status_go = "
SELECT 
    COUNT(CASE WHEN s.status_go = 'Accelerated' THEN 1 END) as accelerated_count,
    COUNT(CASE WHEN s.status_go = 'Delayed' THEN 1 END) as delayed_count,
    COUNT(CASE WHEN s.status_go = 'On Schedule' THEN 1 END) as on_schedule_count
FROM summary_soc s
JOIN land l ON s.kode_lahan = l.kode_lahan";

// Add filter for selected month if not 'all'
if ($selectedMonth != 'all') {
    $sql_status_go .= " WHERE MONTH(STR_TO_DATE(s.go_fix, '%Y-%m-%d')) = $selectedMonth";
}

$result_status_go = $conn->query($sql_status_go);

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


$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Initialize data array
$fixValues = array();

// Prepare the SQL query with date range filter
$sql_fix_values = "
SELECT COALESCE(NULLIF(s.go_fix, ''), r.gostore_date) as fix_date
FROM summary_soc s
LEFT JOIN resto r ON s.kode_lahan = r.kode_lahan
WHERE 1 = 1";

if ($startDate && $endDate) {
    $sql_fix_values .= " AND (COALESCE(NULLIF(s.go_fix, ''), r.gostore_date) BETWEEN '$startDate' AND '$endDate')";
}

$result_fix_values = $conn->query($sql_fix_values);

while ($row = $result_fix_values->fetch_assoc()) {
    $fixValues[] = $row['fix_date'];
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
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
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
                    <h1 class="mr-2">Calendar Tracking</h1>
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li>Calendar Tracking</li>
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
                <div class="row" style="width: 80%; margin: 0 auto;">
    <div class="card mb-3">
        <div class="card-body p-2">
            <div class="card-title border-bottom d-flex align-items-center m-0 p-3">
                <span>Monthly Schedule - <?php echo date('F Y'); ?></span>
                <span class="flex-grow-1"></span>
                <div class="p-3 justify-content-center">
                    <form method="get" action="" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="month">Select Month:</label>
                            <select name="month" id="month" class="form-control ml-2">
                                <?php
                                for ($m = 1; $m <= 12; $m++) {
                                    $month = date('F', mktime(0, 0, 0, $m, 1));
                                    $selected = ($m == (isset($_GET['month']) ? $_GET['month'] : date('m'))) ? 'selected' : '';
                                    echo "<option value='$m' $selected>$month</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label for="year" style="font-size: 12px;">Select Year:</label>
                            <select name="year" id="year" class="form-control ml-2">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
                                    $selected = ($y == (isset($_GET['year']) ? $_GET['year'] : $currentYear)) ? 'selected' : '';
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
                <span class="badge badge-pill badge-warning">Updated daily</span>
            </div>
            <div class="d-flex flex-wrap p-2">
                <?php
                // Get selected month and year from the form, or use current month and year as default
                $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
                $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                
                // Get the number of days in the selected month
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
                
                // Loop through each day of the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = new DateTime("$selectedYear-$selectedMonth-$day");
                    $dateString = $date->format('Y-m-d');
                    
                    // Count the number of events for the current day
                    $eventCount = 0;
                    $eventContent = '';
                    foreach ($schedule as $event) {
                        if ($event['re_sla_date'] == $dateString ||
                            $event['slavl_date'] == $dateString ||
                            $event['sla_spk'] == $dateString ||
                            $event['sla_kom'] == $dateString ||
                            $event['sla_steqp'] == $dateString ||
                            $event['sla_stkonstruksi'] == $dateString ||
                            $event['gostore_date'] == $dateString ||
                            $event['rto_act'] == $dateString) {
                            
                            // Increment event count
                            $eventCount++;

                            // Add event to the content
                            if ($event['re_sla_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline BoD</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slavl_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline VL RE</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slavllegal_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline VL Legal</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slavd_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline VD RE</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slavdlegal_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline VD Legal</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slaloa_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline LOA CD RE</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_tafkode'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline TAF Kode Store</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slapsm_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Draft PSM Legal</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slafatpsm_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Draft PSM TAF</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['slabod_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline BoD PSM Table Sewa</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sladraftre_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Uploading PSM RE</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_kondisilahan'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Pengondisian Lahan Legal</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_spkwo'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline SPK WO Design by Procurement</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_survey'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Survey & Layouting SDG-Design</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_obslegal'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Obstacle Legal</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_urugan'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Urugan SDG-Design</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sdgdesain_sla_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline Desain Konstruksi SDG-Design</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_spk'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Deadline SPK</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_kom'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>Kick Off Meeting</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_steqp'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>ST Equipment</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['sla_stkonstruksi'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>ST Kontraktor</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['gostore_date'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>GO</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                            if ($event['rto_act'] == $dateString) {
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>RTO</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['nama_lahan']}</p>";
                                $eventContent .= "<p class='text-truncate' style='font-size: 10px; margin: 0;'>{$event['kode_store']}</p>";
                            }
                        }
                    }
                    
                    // Display the card
                    echo "<div class='col-6 col-sm-4 col-md-2 mb-2'>";
                    echo "<div class='card border p-2' style='height: 100px; max-width: 100%; overflow: hidden;' data-toggle='modal' data-target='#eventModal' data-date='$dateString' data-events='$eventContent'>";
                    echo "<p class='m-0' style='font-size: 12px;'>{$date->format('d')}</p>";
                    echo "<p class='m-0 text-muted' style='font-size: 10px;'>$eventCount Events</p>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for detailed events -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Deadline Schedule on <span id="modalDate"></span></h5>
                <div class="col-6">
                    <ul></ul>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalEvents">
                <!-- Event details will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
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
        var date = new Date(value);
        var now = new Date();
        var timeDiff = now - date;
        var daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if (daysDiff > 30) {
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
    ]; // Convert to percentage

    // Option untuk chart
    var option = {
        title: {
            text: ''
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function (params) {
                var result = params.map(param => {
                    return param.seriesName + ' ' + param.name + ': ' + (param.value.toFixed(2)) + '%';
                }).join('<br/>');
                return result;
            }
        },
        xAxis: {
            type: 'category',
            data: departments
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value}%' // Display yAxis labels as percentage
            }
        },
        series: [{
            name: 'Performance',
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

<script>
        $(document).ready(function() {
            // Hancurkan DataTable jika sudah ada
            if ($.fn.DataTable.isDataTable('#zero_configuration_table')) {
                $('#zero_configuration_table').DataTable().destroy();
            }

            // Inisialisasi DataTable
            $('#zero_configuration_table').DataTable({
                scrollX: true, // Menambahkan scroll horizontal
                fixedColumns: {
                    leftColumns: 3 // Jumlah kolom yang ingin di-fix
                }
            });
        });
    </script>
    <script>
    // Assuming you have a function to render your chart with statusData
    renderChart(statusData);
</script>
<script>
    // Assuming you have a function to render your chart with the provided data
    renderChart({
        existingStore: <?php echo json_encode($existing_store_chart_data); ?>,
        readyToGoStore: <?php echo json_encode($ready_to_go_chart_data); ?>
    });
</script>
<script>
    // jQuery script to handle the click event on the card and show the modal with event details
    $(document).ready(function() {
        $('.card[data-toggle="modal"]').on('click', function() {
            var date = $(this).data('date');
            var events = $(this).data('events');
            
            $('#modalDate').text(date);
            $('#modalEvents').html(events);
        });
    });
</script>
</body>

</html>