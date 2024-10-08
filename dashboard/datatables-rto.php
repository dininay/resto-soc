<?php
// Koneksi ke database tracking_resto
include "../koneksi.php";

    // Query untuk mengambil data dari tabel land
$sql = "
SELECT 
    soc_fat.*, 
    soc_hrga.*, 
    soc_it.*, 
    soc_legal.*, 
    soc_marketing.*, 
    soc_rto.*, 
    soc_sdg.*, 
    note_ba.*, 
    note_legal.*,
    doc_legal.*,
    sign.*,
    land.kode_lahan, 
    land.nama_lahan, 
    land.lokasi, 
    resto.nama_store,
    socdate_it.catatan_it,
    socdate_it.catatan_config,
    socdate_marketing.catatan_marketing,
    socdate_legal.catatan_legal,
    socdate_sdg.catatan_sdg,
    socdate_fat.catatan_fat,
    socdate_hr.catatan_tm,
    socdate_hr.catatan_hot,
    socdate_hr.catatan_ff1,
    socdate_hr.catatan_ff2,
    socdate_hr.catatan_ff3
FROM soc_fat 
INNER JOIN soc_hrga ON soc_fat.kode_lahan = soc_hrga.kode_lahan
INNER JOIN soc_it ON soc_fat.kode_lahan = soc_it.kode_lahan
INNER JOIN soc_legal ON soc_fat.kode_lahan = soc_legal.kode_lahan
INNER JOIN soc_marketing ON soc_fat.kode_lahan = soc_marketing.kode_lahan
INNER JOIN soc_rto ON soc_fat.kode_lahan = soc_rto.kode_lahan
INNER JOIN soc_sdg ON soc_fat.kode_lahan = soc_sdg.kode_lahan
INNER JOIN note_ba ON soc_fat.kode_lahan = note_ba.kode_lahan
INNER JOIN note_legal ON soc_fat.kode_lahan = note_legal.kode_lahan
INNER JOIN doc_legal ON note_legal.kode_lahan = doc_legal.kode_lahan
INNER JOIN sign ON soc_fat.kode_lahan = sign.kode_lahan
INNER JOIN land ON soc_fat.kode_lahan = land.kode_lahan
INNER JOIN resto ON soc_fat.kode_lahan = resto.kode_lahan
INNER JOIN socdate_it ON soc_fat.kode_lahan = socdate_it.kode_lahan
INNER JOIN socdate_legal ON soc_fat.kode_lahan = socdate_legal.kode_lahan
INNER JOIN socdate_marketing ON soc_fat.kode_lahan = socdate_marketing.kode_lahan
INNER JOIN socdate_fat ON soc_fat.kode_lahan = socdate_fat.kode_lahan
INNER JOIN socdate_sdg ON soc_fat.kode_lahan = socdate_sdg.kode_lahan
INNER JOIN socdate_hr ON soc_fat.kode_lahan = socdate_hr.kode_lahan
GROUP BY land.kode_lahan";
$result = $conn->query($sql);

$data = [];

// Initialize counters for categories
$suksesRTO = 0;
$failedRTOProceedGO = 0;
$failedRTOFailedGO = 0;

// Process each row and categorize based on total score
while ($row = $result->fetch_assoc()) {
    
    $data[] = $row;
    $total1 = rtrim(50 * number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2) / 100, '0') . (number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)[strlen(number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)) - 1] == '.' ? '0' : '');
    $total2 = rtrim(25 * number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2) / 100, '0') . (number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)[strlen(number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)) - 1] == '.' ? '0' : '');
    $total3 = rtrim(6 * number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2) / 100, '0') . (number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)[strlen(number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)) - 1] == '.' ? '0' : '');
    $total4 = rtrim(8 * number_format(($row['security'] + $row['cs']) / 2, 2) / 100, '0') . (number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)[strlen(number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');
    $total5 = rtrim(5 * number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2) / 100, '0') . (number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)[strlen(number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)) - 1] == '.' ? '0' : '');
    $total6 = rtrim(6 * number_format(($row['qris'] + $row['edc']) / 2, 2) / 100, '0') . (number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)[strlen(number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');
    $total = $total1 + $total2 + $total3 + $total4 + $total5 + $total6;

    // Categorize based on total score
    if ($total > 94) {
        $suksesRTO++;
    } elseif ($total > 75 && $total <= 94) {
        $failedRTOProceedGO++;
    } else {
        $failedRTOFailedGO++;
    }
}

$no = 1;

$schedule = [];
// $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
// $selectedMonth = isset($_POST['month']) ? $_POST['month'] : 'all';
// $selectedDivision = isset($_POST['division']) ? $_POST['division'] : 'all';

// $sql_chartteam = "
// SELECT 
//     AVG(CASE WHEN YEAR(socdate_fat.fat_date) = $selectedYear AND MONTH(socdate_fat.fat_date) = $selectedMonth THEN 100 ELSE 0 END) AS FAT,
//     AVG(CASE WHEN (YEAR(socdate_academy.kpt_date1) = $selectedYear AND MONTH(socdate_academy.kpt_date1) = $selectedMonth)
//         OR (YEAR(socdate_academy.kpt_date2) = $selectedYear AND MONTH(socdate_academy.kpt_date2) = $selectedMonth)
//         OR (YEAR(socdate_academy.kpt_date3) = $selectedYear AND MONTH(socdate_academy.kpt_date3) = $selectedMonth) THEN 100 ELSE 0 END) AS Academy,
//     AVG(CASE WHEN (YEAR(socdate_hr.ff1_date) = $selectedYear AND MONTH(socdate_hr.ff1_date) = $selectedMonth)
//         OR (YEAR(socdate_hr.ff2_date) = $selectedYear AND MONTH(socdate_hr.ff2_date) = $selectedMonth)
//         OR (YEAR(socdate_hr.ff3_date) = $selectedYear AND MONTH(socdate_hr.ff3_date) = $selectedMonth)
//         OR (YEAR(socdate_hr.hot_date) = $selectedYear AND MONTH(socdate_hr.hot_date) = $selectedMonth) THEN 100 ELSE 0 END) AS HR,
//     AVG(CASE WHEN YEAR(socdate_ir.ir_date) = $selectedYear AND MONTH(socdate_ir.ir_date) = $selectedMonth THEN 100 ELSE 0 END) AS IR,
//     AVG(CASE WHEN YEAR(socdate_it.it_date) = $selectedYear AND MONTH(socdate_it.it_date) = $selectedMonth
//         OR YEAR(socdate_it.config_date) = $selectedYear AND MONTH(socdate_it.config_date) = $selectedMonth THEN 100 ELSE 0 END) AS IT,
//     AVG(CASE WHEN (YEAR(socdate_legal.permit_date) = $selectedYear AND MONTH(socdate_legal.permit_date) = $selectedMonth)
//         OR (YEAR(socdate_legal.sampahparkir_date) = $selectedYear AND MONTH(socdate_legal.sampahparkir_date) = $selectedMonth) THEN 100 ELSE 0 END) AS Legal,
//     AVG(CASE WHEN YEAR(socdate_marketing.marketing_date) = $selectedYear AND MONTH(socdate_marketing.marketing_date) = $selectedMonth THEN 100 ELSE 0 END) AS Marketing,
//     AVG(CASE WHEN YEAR(socdate_scm.sj_date) = $selectedYear AND MONTH(socdate_scm.sj_date) = $selectedMonth THEN 100 ELSE 0 END) AS SCM,
//     AVG(CASE WHEN (YEAR(socdate_sdg.sdgsumber_date) = $selectedYear AND MONTH(socdate_sdg.sdgsumber_date) = $selectedMonth)
//         OR (YEAR(socdate_sdg.sdglistrik_date) = $selectedYear AND MONTH(socdate_sdg.sdglistrik_date) = $selectedMonth)
//         OR (YEAR(socdate_sdg.sdgipal_date) = $selectedYear AND MONTH(socdate_sdg.sdgipal_date) = $selectedMonth) THEN 100 ELSE 0 END) AS SDG
// FROM land
// JOIN resto ON land.kode_lahan = resto.kode_lahan
// JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
// LEFT JOIN summary_soc ON land.kode_lahan = summary_soc.kode_lahan
// INNER JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
// INNER JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
// INNER JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
// INNER JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
// INNER JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
// INNER JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
// INNER JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
// INNER JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
// INNER JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
// GROUP BY land.kode_lahan";


// $columns = [
//     'socdate_academy' => ['kpt_date1', 'kpt_date2', 'kpt_date3'],
//     'socdate_fat' => ['fat_date'],
//     'socdate_hr' => ['tm_date', 'hot_date', 'ff1_date', 'ff2_date', 'ff3_date'],
//     'socdate_ir' => ['ir_date'],
//     'socdate_it' => ['it_date', 'config_date'],
//     'socdate_legal' => ['permit_date', 'sampahparkir_date'],
//     'socdate_marketing' => ['marketing_date'],
//     'socdate_scm' => ['sj_date'],
//     'socdate_sdg' => ['sdgsumber_date', 'sdglistrik_date', 'sdgipal_date'],
// ];

// $whereConditions = [];
// if ($selectedMonth !== 'all') {
//     $whereConditions[] = "MONTH({$selectedDivision}) = $selectedMonth";
// }
// if ($selectedDivision !== 'all') {
//     $whereConditions[] = "{$selectedDivision} IS NOT NULL AND {$selectedDivision} <> '0000-00-00'";
// }

// $sql_chartteam_parts = [];
// foreach ($columns as $table => $fields) {
//     foreach ($fields as $field) {
//         $sql_chartteam_parts[] = "
//             SELECT MONTH($field) AS bulan, '$table' AS table_name, '$field' AS field_name 
//             FROM $table 
//             WHERE YEAR($field) = $selectedYear 
//             AND $field IS NOT NULL AND $field <> '0000-00-00'";
//     }
// }

// $sql_chartteam = implode(' UNION ALL ', $sql_chartteam_parts);
// if (!empty($whereConditions)) {
//     $sql_chartteam .= " AND " . implode(' AND ', $whereConditions);
// }

// // Execute the query
// $result_chartteam = $conn->query($sql_chartteam);

// $averageScores = array_fill(1, 12, 0);
// $countScores = array_fill(1, 12, 0);

// if ($result_chartteam->num_rows > 0) {
//     while ($row = $result_chartteam->fetch_assoc()) {
//         $bulan = intval($row['bulan']);
//         $averageScores[$bulan] += 1;
//     }
// }

// $totalColumns = count($columns) * 3; // Adjust if you expect different numbers of fields per division
// foreach ($averageScores as $bulan => $totalTerisi) {
//     $averageScores[$bulan] = round(($totalTerisi / $totalColumns) * 100, 2);
// }

// $bulanNames = [
//     1 => 'Jan',
//     2 => 'Feb',
//     3 => 'Mar',
//     4 => 'Apr',
//     5 => 'May',
//     6 => 'Jun',
//     7 => 'Jul',
//     8 => 'Aug',
//     9 => 'Sep',
//     10 => 'Oct',
//     11 => 'Nov',
//     12 => 'Dec'
// ];

// $months = array_values($bulanNames);
// $averageScoresJSON = json_encode(array_values($averageScores)); 

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
?>

<!DOCTYPE html>
<html lang="en" dir="">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard Resto | Mie Gacoan</title>
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.3.3/dist/echarts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.2/echarts.min.js"></script>
    <style>
        .filter-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: center;
        }
        .filter-container select {
            padding: 5px 10px;
            font-size: 16px;
        }
        #teamChart {
            width: 100%;
            height: 500px;
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
                    <h1 class="mr-2">RTO Score</h1>
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li>RTO Score</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <!-- ICON BG-->
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Add-User"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">New Leads</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">205</p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Financial"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Sales</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">$4021</p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Checkout-Basket"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Orders</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">80</p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
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
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">RTO Score</div>
                                <div id="echartRto" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                        <!-- <div class="col-lg-8 col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Average Score All Dept vs. Month</div>
                                    <div class="row">
                                    <div class="col-md-4">
                                        <label for="monthFilter">Filter by Month:</label>
                                        <select id="monthFilter" class="form-control">
                                            <option value="all">All Months</option>
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="divisionFilter">Filter by Division:</label>
                                        <select id="divisionFilter" class="form-control">
                                            <option value="all">All Divisions</option>
                                            <option value="socdate_academy">Academy</option>
                                            <option value="socdate_fat">FAT</option>
                                            <option value="socdate_hr">HR</option>
                                            <option value="socdate_ir">IR</option>
                                            <option value="socdate_it">IT</option>
                                            <option value="socdate_legal">Legal</option>
                                            <option value="socdate_marketing">Marketing</option>
                                            <option value="socdate_scm">SCM</option>
                                            <option value="socdate_sdg">SDG</option>
                                        </select>
                                    </div> 
                                </div>
                                    <div id="teamChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div> -->
                        
                        <div class="col-lg-8 col-md-8">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-title">Department Tracking</div>
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
                                            <label for="year">Select Year:</label>
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
                                    <div id="deptChart" style="height: 270px; "></div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                <div class="row">
                    <div class="col-lg-12 col-md-12">
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card o-hidden mb-4">
                                    <div class="card-header d-flex align-items-center border-0">
                                        <h3 class="w-50 float-left card-title m-0">Note Feedback RTO</h3>
                                        <!-- <div class="dropdown dropleft text-right w-50 float-right">
                                            <button class="btn bg-gray-100" id="dropdownMenuButton1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon i-Gear-2"></i></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1"><a class="dropdown-item" href="#">Add new user</a><a class="dropdown-item" href="#">View All users</a><a class="dropdown-item" href="#">Something else here</a></div>
                                        </div> -->
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table text-center" id="user_table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">No</th>
                                                        <th scope="col">Inventory Code</th>
                                                        <th scope="col">SDG</th>
                                                        <th scope="col">Legal</th>
                                                        <th scope="col">IT</th>
                                                        <th scope="col">HRGA</th>
                                                        <th scope="col">Marketing</th>
                                                        <th scope="col">TAF</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($data as $row): ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $no++ ?></th>
                                                        <td><?php echo $row ['kode_lahan']?></td>
                                                        <td><?php echo $row ['catatan_sdg']?></td>
                                                        <td><?php echo $row ['catatan_legal']?></td>
                                                        <td><?php echo nl2br($row['catatan_it'] . "\n" . $row['catatan_config']) ?></td>
                                                        <td><?php echo nl2br($row['catatan_tm'] . "\n" . $row['catatan_hot'] . "\n" . $row['catatan_ff1'] . "\n" . $row['catatan_ff2'] . "\n" . $row['catatan_ff3']) ?></td>
                                                        <td><?php echo $row ['catatan_marketing']?></td>
                                                        <td><?php echo $row ['catatan_fat']?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <div class="card-title border-bottom d-flex align-items-center m-0 p-3"><span>User activity</span><span class="flex-grow-1"></span><span class="badge badge-pill badge-warning">Updated daily</span></div>
                                <div class="d-flex border-bottom justify-content-between p-3">
                                    <div class="flex-grow-1"><span class="text-small text-muted">Pages / Visit</span>
                                        <h5 class="m-0">2065</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">New user</span>
                                        <h5 class="m-0">465</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">Last week</span>
                                        <h5 class="m-0">23456</h5>
                                    </div>
                                </div>
                                <div class="d-flex border-bottom justify-content-between p-3">
                                    <div class="flex-grow-1"><span class="text-small text-muted">Pages / Visit</span>
                                        <h5 class="m-0">1829</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">New user</span>
                                        <h5 class="m-0">735</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">Last week</span>
                                        <h5 class="m-0">92565</h5>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between p-3">
                                    <div class="flex-grow-1"><span class="text-small text-muted">Pages / Visit</span>
                                        <h5 class="m-0">3165</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">New user</span>
                                        <h5 class="m-0">165</h5>
                                    </div>
                                    <div class="flex-grow-1"><span class="text-small text-muted">Last week</span>
                                        <h5 class="m-0">32165</h5>
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
    <script>
        var rtoData = {
            'Sukses RTO': <?php echo $suksesRTO; ?>,
            'Failed RTO - Proceed GO': <?php echo $failedRTOProceedGO; ?>,
            'Failed RTO - Failed GO': <?php echo $failedRTOFailedGO; ?>
        };

        // Initialize ECharts for echartRto
        var echartElemRto = document.getElementById("echartRto");

        if (echartElemRto) {
            var echartRto = echarts.init(echartElemRto);
            echartRto.setOption({
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
                    left: 'left',
                    padding: [250, 0, 0, 0] // Adjust padding here to add space between legend and chart
                },
                series: [
                    {
                        name: 'RTO Score',
                        type: 'pie',
                        radius: '50%',
                        data: [
                            { value: rtoData['Sukses RTO'], name: 'Sukses RTO' },
                            { value: rtoData['Failed RTO - Proceed GO'], name: 'Failed RTO - Proceed GO' },
                            { value: rtoData['Failed RTO - Failed GO'], name: 'Failed RTO - Failed GO' }
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
                    echartRto.resize();
                }, 500);
            });
        }
    </script>
    <script>
    // Mengirim data department ke JavaScript
    var departmentData = <?php echo $departmentDataJSON; ?>;

    // Inisialisasi chart menggunakan ECharts
    var teamChart = echarts.init(document.getElementById('deptChart'));

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
            data: departments,
            axisLabel: {
                fontSize: 10, // Reduce the font size to 10px
                rotate: 45,  // Optionally rotate the labels to make them more readable
            }
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
    var months = <?php echo json_encode($months); ?>;
    var averageScores = <?php echo $averageScoresJSON; ?>;

    var teamChart = echarts.init(document.getElementById('teamChart'));

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
                var tooltipContent = params[0].name + '<br/>';
                params.forEach(function (item) {
                    tooltipContent += item.seriesName + ': ' + item.data + '%<br/>';
                });
                return tooltipContent;
            }
        },
        xAxis: {
            type: 'category',
            data: months
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value} %'
            }
        },
        series: [{
            name: 'Average Score',
            type: 'bar',
            data: averageScores,
            itemStyle: {
                color: '#4CAF50'
            }
        }]
    };

    teamChart.setOption(option);

    window.addEventListener("resize", function () {
        setTimeout(function () {
            teamChart.resize();
        }, 500);
    });
    document.getElementById('monthFilter').addEventListener('change', updateChart);
    document.getElementById('divisionFilter').addEventListener('change', updateChart);

    function updateChart() {
        var selectedMonth = document.getElementById('monthFilter').value;
        var selectedDivision = document.getElementById('divisionFilter').value;

        var filteredMonths = [];
        var filteredScores = [];

        for (var i = 0; i < months.length; i++) {
            var month = i + 1;
            if (selectedMonth === 'all' || selectedMonth == month) {
                if (selectedDivision === 'all' || divisionMatches(selectedDivision, month)) {
                    filteredMonths.push(months[i]);
                    filteredScores.push(averageScores[i]);
                }
            }
        }

        teamChart.setOption({
            xAxis: {
                data: filteredMonths
            },
            series: [{
                data: filteredScores
            }]
        });
    }

    function divisionMatches(division, month) {
        // Implementasikan logika untuk memeriksa apakah data untuk bulan dan divisi yang dipilih tersedia.
        // Return true jika data cocok, false jika tidak.
        // Anda bisa memodifikasi kode di bawah sesuai kebutuhan Anda
        // Misalnya: return true jika ada data untuk division dan month
        return true; // Placeholder, ganti dengan logika yang sesuai
    }

    // Memanggil fungsi untuk pertama kali saat halaman dimuat
    updateChart();
</script>   
</body>

</html>