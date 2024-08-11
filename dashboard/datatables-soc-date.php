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
            // Commit transaksi jika berhasil
            $conn->commit();
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

// Tangani input form untuk memfilter data
$filtered_kode_lahan = isset($_POST['kode_lahan']) ? $_POST['kode_lahan'] : '';

// Query untuk mengambil data dari tabel land
$sql = "SELECT 
            land.kode_lahan AS land_kode_lahan,
            land.*,
            re.*,
            dokumen_loacd.*,
            sdg_desain.*,
            sdg_rab.*,
            draft.*,
            procurement.*,
            resto.*,
            socdate_hr.*,
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
            draft.psmfat_date AS draft_fat_date,
            draft.slafatpsm_date AS draft_slafat_date,
            procurement.start_date AS procurement_start_date,
            procurement.sla_date AS procurement_sla_date
        FROM land
        INNER JOIN re ON re.kode_lahan = land.kode_lahan
        INNER JOIN dokumen_loacd ON dokumen_loacd.kode_lahan = land.kode_lahan
        INNER JOIN sdg_desain ON sdg_desain.kode_lahan = land.kode_lahan
        INNER JOIN sdg_rab ON sdg_rab.kode_lahan = land.kode_lahan
        INNER JOIN draft ON draft.kode_lahan = land.kode_lahan
        INNER JOIN procurement ON procurement.kode_lahan = land.kode_lahan
        INNER JOIN resto ON resto.kode_lahan = land.kode_lahan
        INNER JOIN socdate_hr ON socdate_hr.kode_lahan = land.kode_lahan
        WHERE land.status_land = 'Aktif' 
        GROUP BY land.kode_lahan";
$result = $conn->query($sql);

$total_remarks = [];

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

// Query untuk mengambil nilai SLA dari master_slacons
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

$data = [];
$good_totals = [];

// Fungsi untuk menentukan remarks berdasarkan scoring
function getRemarks($scoring) {
    if ($scoring >= 0) {
        return "good";
    } elseif ($scoring >= -30) {
        return "poor";
    } elseif ($scoring >= -50) {
        return "failed";
    } else {
        return "failed"; // Kembalikan "failed" untuk skenario lain yang tidak tertangani
    }
}

// Fungsi untuk menentukan warna badge berdasarkan remarks
function getBadgeColor($remarks) {
    switch ($remarks) {
        case 'good':
            return 'success';
        case 'poor':
            return 'warning';
        case 'failed':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Fungsi untuk menambahkan nilai ke total remarks
function addToTotal(&$total_array, $remarks, $kode_lahan) {
    if (!isset($total_array[$kode_lahan])) {
        $total_array[$kode_lahan] = ['good' => 0, 'poor' => 0, 'failed' => 0];
    }

    switch ($remarks) {
        case 'good':
            $total_array[$kode_lahan]['good']++;
            break;
        case 'poor':
            $total_array[$kode_lahan]['poor']++;
            break;
        case 'failed':
            $total_array[$kode_lahan]['failed']++;
            break;
        default:
            // Tambahkan logika jika terdapat remarks lainnya, jika diperlukan
            break;
    }
}
$totals_per_kode_lahan = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $kode_lahan = $row['kode_lahan'];
        // Hitung scoring dan remarks untuk masing-masing data
        $scoring1 = calculateScoring($row['status_date'], $row['re_date'], $master_sla['RE']);
        $scoring2 = calculateScoring($row['re_date'], $row['re_start_date'], $master_sla['Owner Surveyor']);
        $scoring3 = calculateScoring($row['re_start_date'], $row['vl_date'], $master_sla['VL']);
        $scoring4 = calculateScoring($row['end_date'], $row['nego_date'], $master_sla['Negosiator']);
        $scoring5 = calculateScoring($row['nego_date'], $row['dokumen_loacd_start_date'], $master_sla['LOA-CD']);
        $scoring6 = calculateScoring($row['vl_date'], $row['dokumen_loacd_end_date'], $master_sla['VD']);
        $scoring7 = calculateScoring($row['nego_date'], $row['survey_date'], $master_sla['Land Survey']);
        $scoring8 = calculateScoring($row['survey_date'], $row['layout_date'], $master_sla['Layouting']);
        $scoring9 = calculateScoring($row['layout_date'], $row['sdg_desain_start_date'], $master_sla['Design']);
        $scoring10 = calculateScoring($row['sdg_desain_start_date'], $row['sdg_qs_start_date'], $master_sla['QS']);
        $scoring11 = calculateScoring($row['dokumen_loacd_end_date'], $row['draft_start_date'], $master_sla['Draft-Sewa']);
        $scoring12 = calculateScoring($row['draft_start_date'], $row['draft_fat_date'], $master_sla['FAT-Sewa']);
        $scoring13 = calculateScoring($row['draft_start_date'], $row['draft_end_date'], $master_sla['TTD-Sewa']);
        $scoring14 = calculateScoring($row['sdg_desain_start_date'], $row['sdg_desain_submit_date'], $master_sla['Legal']);
        $scoring15 = calculateScoring($row['sdg_desain_start_date'], $row['procurement_start_date'], $master_sla['Tender']);
        $scoring16 = calculateScoring($row['procurement_start_date'], $row['spk_date'], $master_sla['SPK']);
        $scoring17 = calculateScoring($row['spk_date'], $row['fat_date'], $master_sla['SPK-FAT']);
        $scoring18 = calculateScoring($row['fat_date'], $row['kom_date'], $master_sla['KOM']);
        $scoring19 = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
        $scoring20 = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
        // Tentukan remarks untuk masing-masing scoring
        $remarks1 = getRemarks($scoring1);
        $remarks2 = getRemarks($scoring2);
        $remarks3 = getRemarks($scoring3);
        $remarks4 = getRemarks($scoring4);
        $remarks5 = getRemarks($scoring5);
        $remarks6 = getRemarks($scoring6);
        $remarks7 = getRemarks($scoring7);
        $remarks8 = getRemarks($scoring8);
        $remarks9 = getRemarks($scoring9);
        $remarks10 = getRemarks($scoring10);
        $remarks11 = getRemarks($scoring11);
        $remarks12 = getRemarks($scoring12);
        $remarks13 = getRemarks($scoring13);
        $remarks14 = getRemarks($scoring14);
        $remarks15 = getRemarks($scoring15);
        $remarks16 = getRemarks($scoring16);
        $remarks17 = getRemarks($scoring17);
        $remarks18 = getRemarks($scoring18);
        $remarks19 = getRemarks($scoring19);
        $remarks20 = getRemarks($scoring20);
        // ... (lanjutkan untuk semua remarks yang diperlukan)

        // Tambahkan ke total remarks berdasarkan kode_lahan
        addToTotal($totals_per_kode_lahan, $remarks1, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks2, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks3, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks4, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks5, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks6, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks7, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks8, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks9, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks10, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks11, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks12, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks13, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks14, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks15, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks16, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks17, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks18, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks19, $row['kode_lahan']);
        addToTotal($totals_per_kode_lahan, $remarks20, $row['kode_lahan']);
        // ... (lanjutkan untuk semua remarks yang diperlukan)

        // Tambahkan data ke dalam array $data jika diperlukan
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$statusDataList = [];

// Loop untuk setiap kode lahan yang ada dalam $totals_per_kode_lahan
foreach ($totals_per_kode_lahan as $kode_lahan => $totals) {
    // Ambil jumlah good, poor, dan failed dari totals_per_kode_lahan
    $good_count = $totals['good'];
    $poor_count = $totals['poor'];
    $failed_count = $totals['failed'];

    // Buat statusData untuk kode lahan saat ini
    $statusData = json_encode([
        'good' => $good_count ?? 0, // Default value jika tidak ada data
        'poor' => $poor_count ?? 0, // Default value jika tidak ada data
        'failed' => $failed_count ?? 0, // Default value jika tidak ada data
    ]);

    // Tambahkan statusData ke dalam list untuk digunakan dalam script JavaScript
    $statusDataList[$kode_lahan] = $statusData;
}

$sql_chartteam = "SELECT 
    land.kode_lahan,
    land.nama_lahan,
    AVG(CASE WHEN land.status_date IS NOT NULL AND land.re_date IS NOT NULL AND dokumen_loacd.start_date IS NOT NULL THEN 100 ELSE 0 END) AS RE,
    AVG(CASE WHEN re.start_date IS NOT NULL AND resto.gostore_date IS NOT NULL THEN 100 ELSE 0 END) AS BoD,
    AVG(CASE WHEN re.vl_date IS NOT NULL AND dokumen_loacd.end_date IS NOT NULL AND draft.start_date IS NOT NULL AND draft.end_date IS NOT NULL THEN 100 ELSE 0 END) AS Legal,
    AVG(CASE WHEN re.nego_date IS NOT NULL AND resto.gostore_date IS NOT NULL THEN 100 ELSE 0 END) AS Negotiator,
    AVG(CASE WHEN sdg_desain.survey_date IS NOT NULL AND resto.gostore_date IS NOT NULL AND sdg_desain.obs_date IS NOT NULL AND sdg_desain.start_date IS NOT NULL THEN 100 ELSE 0 END) AS Design,
    AVG(CASE WHEN sdg_rab.start_date IS NOT NULL THEN 100 ELSE 0 END) AS QS,
    AVG(CASE WHEN procurement.start_date IS NOT NULL THEN 100 ELSE 0 END) AS Procurement
FROM 
    land
    JOIN resto ON land.kode_lahan = resto.kode_lahan
    JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
    INNER JOIN re ON land.kode_lahan = re.kode_lahan
    INNER JOIN draft ON land.kode_lahan = draft.kode_lahan
    INNER JOIN procurement ON land.kode_lahan = procurement.kode_lahan
    INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
    INNER JOIN sdg_rab ON land.kode_lahan = sdg_rab.kode_lahan
GROUP BY 
    land.kode_lahan, land.nama_lahan";

$result_chartteam = $conn->query($sql_chartteam);

// Array untuk menyimpan data $fix per kode_lahan
$kodeLahans = [];
$fixValues = [];

// Proses hasil query
if ($result_chartteam->num_rows > 0) {
    while ($row = $result_chartteam->fetch_assoc()) {
        $kodeLahan = $row['kode_lahan'];
        $fix = ($row['RE'] + $row['BoD'] + $row['Legal'] + $row['Negotiator'] + $row['Design'] + $row['QS'] + $row['Procurement']) / 7;

        // Masukkan ke dalam array
        $kodeLahans[] = $kodeLahan;
        $fixValues[] = round($fix, 2); // Pembulatan 2 angka di belakang koma
    }
}

// Konversi array PHP ke JSON untuk digunakan di dalam JavaScript
$kodeLahansJSON = json_encode($kodeLahans);
$fixValuesJSON = json_encode($fixValues);

$sql_chartteam = "SELECT 
    land.kode_lahan,
    land.nama_lahan,
    AVG(CASE WHEN land.status_date IS NOT NULL AND land.re_date IS NOT NULL AND dokumen_loacd.start_date IS NOT NULL THEN 100 ELSE 0 END) AS RE,
    AVG(CASE WHEN re.start_date IS NOT NULL AND resto.gostore_date IS NOT NULL THEN 100 ELSE 0 END) AS BoD,
    AVG(CASE WHEN re.vl_date IS NOT NULL AND dokumen_loacd.end_date IS NOT NULL AND draft.start_date IS NOT NULL AND draft.end_date IS NOT NULL THEN 100 ELSE 0 END) AS Legal,
    AVG(CASE WHEN re.nego_date IS NOT NULL AND resto.gostore_date IS NOT NULL THEN 100 ELSE 0 END) AS Negotiator,
    AVG(CASE WHEN sdg_desain.survey_date IS NOT NULL AND resto.gostore_date IS NOT NULL AND sdg_desain.obs_date IS NOT NULL AND sdg_desain.start_date IS NOT NULL THEN 100 ELSE 0 END) AS Design,
    AVG(CASE WHEN sdg_rab.start_date IS NOT NULL THEN 100 ELSE 0 END) AS QS,
    AVG(CASE WHEN procurement.start_date IS NOT NULL THEN 100 ELSE 0 END) AS Procurement
FROM 
    land
    JOIN resto ON land.kode_lahan = resto.kode_lahan
    JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
    INNER JOIN re ON land.kode_lahan = re.kode_lahan
    INNER JOIN draft ON land.kode_lahan = draft.kode_lahan
    INNER JOIN procurement ON land.kode_lahan = procurement.kode_lahan
    INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
    INNER JOIN sdg_rab ON land.kode_lahan = sdg_rab.kode_lahan
GROUP BY 
    land.kode_lahan, land.nama_lahan";

$result_chartteam = $conn->query($sql_chartteam);

// Array untuk menyimpan data departemen
$departmentData = [
    'RE' => 0,
    'BoD' => 0,
    'Legal' => 0,
    'Negotiator' => 0,
    'Design' => 0,
    'QS' => 0,
    'Procurement' => 0
];

if ($result_chartteam->num_rows > 0) {
    while ($row = $result_chartteam->fetch_assoc()) {
        // Menghitung nilai rata-rata untuk setiap departemen
        $departmentData['RE'] += round($row['RE'], 2);
        $departmentData['BoD'] += round($row['BoD'], 2);
        $departmentData['Legal'] += round($row['Legal'], 2);
        $departmentData['Negotiator'] += round($row['Negotiator'], 2);
        $departmentData['Design'] += round($row['Design'], 2);
        $departmentData['QS'] += round($row['QS'], 2);
        $departmentData['Procurement'] += round($row['Procurement'], 2);
    }
    // Pembagian rata-rata untuk mendapatkan nilai akhir
    foreach ($departmentData as $key => $value) {
        $departmentData[$key] = $value / $result_chartteam->num_rows;
    }
}

$departmentDataJSON = json_encode($departmentData);



$conn->close();
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
    <link href="../dist-assets/css/plugins/datatables.min.css" rel="stylesheet"  />
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/feather-icon.css">
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>

    <style>
        table.dataTable {
            border-collapse: collapse!important;
        }
        table {
            font-size: 10px; /* Sesuaikan dengan ukuran font yang diinginkan */
        }

        /* Mengatur padding sel tabel */
        table th, table td {
            padding: 4px; /* Sesuaikan dengan padding yang diinginkan */
        }
        table {
            width: 50%; /* Sesuaikan dengan lebar yang diinginkan */
            margin: 0 auto; /* Memusatkan tabel jika diperlukan */
        }
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 50px; /* Sesuaikan dengan lebar kolom yang diinginkan */
        }
        table {
            border-collapse: collapse;
            border-spacing: 0; /* Menghilangkan jarak antara sel */
        }
        
        table th, table td {
            border: 1px solid #ddd; /* Atur border sel sesuai kebutuhan */
            margin: 0; /* Menghilangkan margin sel */
        }
        #zero_configuration_table {
            font-size: 5px;
            width: 50%;
            margin: 0 auto;
        }

        #zero_configuration_table th, #zero_configuration_table td {
            padding: 4px;
        }

        /* Mengatur ukuran font tabel */
        #zero_configuration_table {
            font-size: 10px; /* Sesuaikan ukuran font */
            width: 50%; /* Sesuaikan lebar tabel */
            margin: 0 auto; /* Memusatkan tabel */
            border-collapse: collapse; /* Menghilangkan jarak antara sel */
            text-align: center; /* Agar teks di dalam kolom rata tengah */
            vertical-align: middle; 
        }

        /* Mengatur padding sel tabel */
        #zero_configuration_table th, #zero_configuration_table td {
            padding: 3px; /* Sesuaikan padding sel */
            border: 1px solid #ddd; /* Mengatur border sel */
            text-align: center; /* Menyelaraskan teks ke tengah */
        }
        #zero_configuration_table th, #zero_configuration_table td {
            width: 100%; /* Atur lebar sesuai kebutuhan */
        }

    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

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
                                <div class="row justify-content-center">
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                                        <div class="card-body">
                                            <i class="i-Add-User mr-3"></i>
                                            <h5 class="text-muted mt-2 mb-2">Total Store In Preparation</h5>
                                            <div class="content">
                                                <p class="text-primary text-24 line-height-1 mb-2"><?php echo $total_existing_store_without_approve_kom; ?></p>
                                            </div>
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
                                                <div class="card-title">Status In Preparation</div>
                                                <div id="echartGo" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="card-title">Department Tracking</div>
                                                <div id="teamChart" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

							  <div class="table-responsive">
                                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                                <tr>
                                                    <th rowspan="4" class="sticky-col" style="background-color: #6c757d; color: white;">Proses Phase</th>
                                                    <th rowspan="4" class="sticky-col" style="background-color: #6c757d; color: white;">Activities</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="sticky-col" style="background-color: #6c757d; color: white;">Store</th>
                                                    <?php foreach ($data as $row): ?>
                                                    <th colspan="11" style="background-color: #6c757d; color: white;"><?= $row['kode_store'] ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" rowspan="3" class="sticky-col" style="background-color: #6c757d; color: white;">PIC</th>
                                                <?php foreach ($data as $row): ?>
                                                <th colspan="4" style="background-color: #6c757d; color: white;">Plan</th>
                                                <th rowspan="3" colspan="1" style="background-color: #6c757d; color: white;">SLA (d)</th>
                                                <th colspan="4" style="background-color: #6c757d; color: white;">Actual</th>
                                                <th rowspan="3" colspan="1" style="background-color: #6c757d; color: white;">Scoring</th>
                                                <th rowspan="3" colspan="1" style="background-color: #6c757d; color: white;">Remarks</th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($data as $row): ?>
                                                <th rowspan="3" colspan="2" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" colspan="2" style="background-color: #6c757d; color: white;">End</th>
                                                <th rowspan="3" colspan="2" style="background-color: #6c757d; color: white;">Start</th>
                                                <th rowspan="3" colspan="2" style="background-color: #6c757d; color: white;">End</th>
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
                                                $sla_re_date = !empty($row['status_date']) ? date('d M y', strtotime($row['status_date'] . ' +' . ($master_sla['RE'] ?? 0) . ' days')) : '';
                                                
                                                ?>
                                                <td colspan="2"><?= date('d M y', strtotime($row['status_date'])) ?></td>
                                                <td colspan="2"><?= $sla_re_date ?></td>
                                                <td><?= $master_sla['RE'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['status_date']) && !empty($row['status_date']) ? date('d M y', strtotime($row['status_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['re_date']) && !empty($row['re_date']) ? date('d M y', strtotime($row['re_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['status_date'], $row['re_date'], $master_sla['RE']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);    
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';         
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_re_date = !empty($row['status_date']) ? date('d M y', strtotime($row['status_date'] . ' +' . ($master_sla['RE'] ?? 0) . ' days')) : '';
                                                                                       
                                                    $sla_bod_date = $sla_re_date != 'N/A' ? date('d M y', strtotime($sla_re_date . ' +' . ($master_sla['Owner Surveyor'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_re_date ?></td>
                                                <td colspan="2"><?= $sla_bod_date ?></td>
                                                <td><?= $master_sla['Owner Surveyor'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['re_date']) && !empty($row['re_date']) ? date('d M y', strtotime($row['re_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['re_start_date']) && !empty($row['re_start_date']) ? date('d M y', strtotime($row['re_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_date'], $row['re_start_date'], $master_sla['Owner Surveyor']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);  
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                   
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_legal_date = $sla_bod_date != 'N/A' ? date('d M y', strtotime($sla_bod_date . ' +' . ($master_sla['Legal'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_bod_date ?></td>
                                                <td colspan="2"><?= $sla_legal_date?></td>
                                                <td><?= $master_sla['VL'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['re_start_date']) && !empty($row['re_start_date']) ? date('d M y', strtotime($row['re_start_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['vl_date']) && !empty($row['vl_date']) ? date('d M y', strtotime($row['vl_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_start_date'], $row['vl_date'], $master_sla['VL']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);     
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                  
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_nego_date = $sla_legal_date != 'N/A' ? date('d M y', strtotime($sla_legal_date . ' +' . ($master_sla['Negosiator'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_legal_date?></td>
                                                <td colspan="2"><?= $sla_nego_date?></td>
                                                <td><?= $master_sla['Negosiator'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['end_date']) && !empty($row['end_date']) ? date('d M y', strtotime($row['end_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['end_date'], $row['nego_date'], $master_sla['Negosiator']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);           
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';  
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_loa_date = $sla_nego_date != 'N/A' ? date('d M y', strtotime($sla_nego_date . ' +' . ($master_sla['LOA-CD'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_nego_date ?></td>
                                                <td colspan="2"><?= $sla_loa_date?></td>
                                                <td><?= $master_sla['LOA-CD'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['dokumen_loacd_start_date']) && !empty($row['dokumen_loacd_start_date']) ? date('d M y', strtotime($row['dokumen_loacd_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['dokumen_loacd_start_date'], $master_sla['LOA-CD']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                 
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';       
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_vd_date = $sla_loa_date != 'N/A' ? date('d M y', strtotime($sla_loa_date . ' +' . ($master_sla['VD'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_loa_date ?></td>
                                                <td colspan="2"><?= $sla_vd_date?></td>
                                                <td><?= $master_sla['VD'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['vl_date']) && !empty($row['vl_date']) ? date('d M y', strtotime($row['vl_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['dokumen_loacd_end_date']) && !empty($row['dokumen_loacd_end_date']) ? date('d M y', strtotime($row['dokumen_loacd_end_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['vl_date'], $row['dokumen_loacd_end_date'], $master_sla['VD']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);       
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                     
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_survey_date = $sla_nego_date != 'N/A' ? date('d M y', strtotime($sla_nego_date . ' +' . ($master_sla['Land Survey'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_nego_date ?></td>
                                                <td colspan="2"><?= $sla_survey_date?></td>
                                                <td><?= $master_sla['Land Survey'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['survey_date']) && !empty($row['survey_date']) ? date('d M y', strtotime($row['survey_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['survey_date'], $master_sla['Land Survey']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);    
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                      
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_layout_date = $sla_survey_date != 'N/A' ? date('d M y', strtotime($sla_survey_date . ' +' . ($master_sla['Layouting'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_survey_date ?></td>
                                                <td colspan="2"><?= $sla_layout_date?></td>
                                                <td><?= $master_sla['Layouting'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['survey_date']) && !empty($row['survey_date']) ? date('d M y', strtotime($row['survey_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['layout_date']) && !empty($row['layout_date']) ? date('d M y', strtotime($row['layout_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['survey_date'], $row['layout_date'], $master_sla['Layouting']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);          
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                  
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_sdgded_date = $sla_layout_date != 'N/A' ? date('d M y', strtotime($sla_layout_date . ' +' . ($master_sla['Design'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2" colspan="2"><?= $sla_layout_date ?></td>
                                                <td rowspan="2" colspan="2"><?= $sla_sdgded_date?></td>
                                                <td rowspan="2"><?= $master_sla['Design'] ?? 'N/A' ?></td>
                                                <td rowspan="2" colspan="2"><?= isset($row['layout_date']) && !empty($row['layout_date']) ? date('d M y', strtotime($row['layout_date'])) : '0' ?></td>
                                                <td rowspan="2" colspan="2"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['layout_date'], $row['sdg_desain_start_date'], $master_sla['Design']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);            
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                     
                                                ?>
                                                <td rowspan="2"><?= $display_scoring ?></td>
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
                                                    $sla_rab_date = $sla_sdgded_date != 'N/A' ? date('d M y', strtotime($sla_sdgded_date . ' +' . ($master_sla['QS'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td rowspan="2" colspan="2" style="background-color: white"><?= $sla_sdgded_date ?></td>
                                                <td rowspan="2" colspan="2" style="background-color: white"><?= $sla_rab_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['QS'] ?? 'N/A' ?></td>
                                                <td rowspan="2" colspan="2" style="background-color: white"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td rowspan="2" colspan="2" style="background-color: white"><?= isset($row['sdg_qs_start_date']) && !empty($row['sdg_qs_start_date']) ? date('d M y', strtotime($row['sdg_qs_start_date'])) : '0' ?></td>
                                                
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_qs_start_date'], $master_sla['QS']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                   
                                                ?>
                                                <td rowspan="2"><?= $display_scoring ?></td>
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
                                                    $sla_draft_date = $sla_vd_date != 'N/A' ? date('d M y', strtotime($sla_vd_date . ' +' . ($master_sla['Draft-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_vd_date ?></td>
                                                <td colspan="2"><?= $sla_draft_date?></td>
                                                <td><?= $master_sla['Draft-Sewa'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['dokumen_loacd_end_date']) && !empty($row['dokumen_loacd_end_date']) ? date('d M y', strtotime($row['dokumen_loacd_end_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['dokumen_loacd_end_date'], $row['draft_start_date'], $master_sla['Draft-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                         
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';               
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_fat_date = $sla_draft_date != 'N/A' ? date('d M y', strtotime($sla_draft_date . ' +' . ($master_sla['FAT-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_draft_date ?></td>
                                                <td colspan="2"><?= $sla_fat_date?></td>
                                                <td><?= $master_sla['FAT-Sewa'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['draft_fat_date']) && !empty($row['draft_fat_date']) ? date('d M y', strtotime($row['draft_fat_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_fat_date'], $master_sla['FAT-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                         
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                  
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_psm_date = $sla_fat_date != 'N/A' ? date('d M y', strtotime($sla_fat_date . ' +' . ($master_sla['TTD-Sewa'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_fat_date ?></td>
                                                <td colspan="2"><?= $sla_psm_date?></td>
                                                <td><?= $master_sla['TTD-Sewa'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['draft_end_date']) && !empty($row['draft_end_date']) ? date('d M y', strtotime($row['draft_end_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_end_date'], $master_sla['TTD-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                    
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                     
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_permit_date = $sla_psm_date != 'N/A' ? date('d M y', strtotime($sla_psm_date . ' +' . ($master_sla['Legal'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_psm_date ?></td>
                                                <td colspan="2"><?= $sla_permit_date?></td>
                                                <td><?= $master_sla['Legal'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['sdg_desain_submit_date']) && !empty($row['sdg_desain_submit_date']) ? date('d M y', strtotime($row['sdg_desain_submit_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_desain_submit_date'], $master_sla['Legal']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);          
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                  
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_tender_date = $sla_rab_date != 'N/A' ? date('d M y', strtotime($sla_rab_date . ' +' . ($master_sla['Tender'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2" rowspan="2"><?= $sla_rab_date ?></td>
                                                <td colspan="2" rowspan="2"><?= $sla_tender_date?></td>
                                                <td rowspan="2"><?= $master_sla['Tender'] ?? 'N/A' ?></td>
                                                <td rowspan="2" colspan="2"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td rowspan="2" colspan="2"><?= isset($row['procurement_start_date']) && !empty($row['procurement_start_date']) ? date('d M y', strtotime($row['procurement_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['procurement_start_date'], $master_sla['Tender']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                         
                                                ?>
                                                <td rowspan="2"><?= $display_scoring ?></td>
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
                                                    $sla_spk_date = $sla_tender_date != 'N/A' ? date('d M y', strtotime($sla_tender_date . ' +' . ($master_sla['SPK'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2" rowspan="2" style="background-color: white"><?= $sla_tender_date ?></td>
                                                <td colspan="2" rowspan="2" style="background-color: white"><?= $sla_spk_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['SPK'] ?? 'N/A' ?></td>
                                                <td colspan="2" rowspan="2"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td colspan="2" rowspan="2"><?= isset($row['spk_date']) && !empty($row['spk_date']) ? date('d M y', strtotime($row['spk_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['procurement_start_date'], $row['spk_date'], $master_sla['SPK']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                       
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                     
                                                ?>
                                                <td rowspan="2"><?= $display_scoring ?></td>
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
                                                    $sla_spkfat_date = $sla_spk_date != 'N/A' ? date('d M y', strtotime($sla_spk_date . ' +' . ($master_sla['SPK-FAT'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_spk_date ?></td>
                                                <td colspan="2"><?= $sla_spkfat_date?></td>
                                                <td><?= $master_sla['SPK-FAT'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['spk_date']) && !empty($row['spk_date']) ? date('d M y', strtotime($row['spk_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['fat_date']) && !empty($row['fat_date']) ? date('d M y', strtotime($row['fat_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['spk_date'], $row['fat_date'], $master_sla['SPK-FAT']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                     
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                      
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_kom_date = $sla_spk_date != 'N/A' ? date('d M y', strtotime($sla_spk_date . ' +' . ($master_sla['KOM'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_spk_date ?></td>
                                                <td colspan="2"><?= $sla_kom_date?></td>
                                                <td><?= $master_sla['KOM'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['fat_date']) && !empty($row['fat_date']) ? date('d M y', strtotime($row['fat_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['fat_date'], $row['kom_date'], $master_sla['KOM']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                      
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('d M y', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_kom_date ?></td>
                                                <td colspan="2"><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['ff3_date']) && !empty($row['ff3_date']) ? date('d M y', strtotime($row['ff3_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                        
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('d M y', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><?= $sla_kom_date ?></td>
                                                <td colspan="2"><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td colspan="2"><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td colspan="2"><?= isset($row['ff3_date']) && !empty($row['ff3_date']) ? date('d M y', strtotime($row['ff3_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);      
                                                $display_scoring = $scoring < -200 ? '-200%' : round($scoring) . '%';                   
                                                ?>
                                                <td><?= $display_scoring ?></td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $remarks ?>
                                                    </span>
                                                </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                            <?php foreach ($data as $row): ?>
                                                <td class="sticky" rowspan="1" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('Y-m-d', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('') ?>"><?= $row ['kode_lahan'] ?></span></td>
                                                <?php
                                                // Ambil total remarks untuk kode lahan saat ini
                                                $good_count = isset($totals_per_kode_lahan[$row['kode_lahan']]['good']) ? $totals_per_kode_lahan[$row['kode_lahan']]['good'] : 0;
                                                $poor_count = isset($totals_per_kode_lahan[$row['kode_lahan']]['poor']) ? $totals_per_kode_lahan[$row['kode_lahan']]['poor'] : 0;
                                                $failed_count = isset($totals_per_kode_lahan[$row['kode_lahan']]['failed']) ? $totals_per_kode_lahan[$row['kode_lahan']]['failed'] : 0;
                                                ?>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('good') ?>">Total Good</span></td>
                                                <td colspan="1"><span class="badge badge-<?= getBadgeColor('good') ?>"><?= $good_count ?></span></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('poor') ?>">Total Poor</span></td>
                                                <td colspan="1"><span class="badge badge-<?= getBadgeColor('poor') ?>"><?= $poor_count ?></span></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('failed') ?>">Total Failed</span></td>
                                                <td colspan="1"><span class="badge badge-<?= getBadgeColor('failed') ?>"><?= $failed_count ?></span></td>
                                                <?php endforeach; ?>
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

<script>
    // Mengambil data kodeLahans dan fixValues dari PHP
    var kodeLahans = <?php echo $kodeLahansJSON; ?>;
    var fixValues = <?php echo $fixValuesJSON; ?>;

    // Gabungkan kodeLahans dan fixValues ke dalam satu array untuk pengurutan
    var combinedData = kodeLahans.map(function(e, i) {
        return { kodeLahan: e, fixValue: fixValues[i] };
    });

    // Urutkan data berdasarkan fixValues dari yang terbesar ke yang terkecil
    combinedData.sort(function(a, b) {
        return b.fixValue - a.fixValue;
    });

    // Pisahkan kembali data yang sudah diurutkan
    kodeLahans = combinedData.map(function(e) { return e.kodeLahan; });
    fixValues = combinedData.map(function(e) { return e.fixValue; });

    // Inisialisasi chart menggunakan ECharts
    var storeRankChart = echarts.init(document.getElementById('storeRank'));

    // Opsi untuk chart
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
                var value = params[0].value.toFixed(2);
                return params[0].name + ': ' + value + '%';
            }
        },
        xAxis: {
            type: 'category',
            data: kodeLahans,
            axisLabel: {
                rotate: 45, // Rotate labels if needed
                interval: 0 // Display all labels
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value} %'
            }
        },
        series: [
            {
                name: 'Score',
                type: 'bar',
                data: fixValues,
                itemStyle: {
                    color: function(params) {
                        var colors = ['#405D72', '#758694']; // Warna untuk bar
                        return colors[params.dataIndex % colors.length];
                    }
                }
            }
        ]
    };

    // Gunakan setOption untuk mengatur data dan opsi ke chart
    storeRankChart.setOption(option);

    // Resize chart on window resize
    window.addEventListener("resize", function () {
        setTimeout(function () {
            storeRankChart.resize();
        }, 500);
    });
</script>

<script>
    // Mengirim data department ke JavaScript
    var departmentData = <?php echo $departmentDataJSON; ?>;

    // Inisialisasi chart menggunakan ECharts
    var teamChart = echarts.init(document.getElementById('teamChart'));

    // Data untuk chart
    var departments = ['RE', 'BoD', 'Legal', 'Negotiator', 'Design', 'QS', 'Procurement'];
    var values = [
        departmentData.RE,
        departmentData.BoD,
        departmentData.Legal,
        departmentData.Negotiator,
        departmentData.Design,
        departmentData.QS,
        departmentData.Procurement
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
            },
            formatter: function (params) {
                var value = params[0].value.toFixed(2);
                return params[0].name + ': ' + value + '%';
            }
        },
        xAxis: {
            type: 'category',
            data: departments
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value} %'
            }
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
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script>
        // Convert the PHP statusData to JavaScript object
        var statusData = <?php echo $statusData; ?>;

        // Initialize ECharts
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
                        name: "Status In Preparation",
                        type: "pie",
                        radius: "50%",
                        center: ["50%", "50%"],
                        data: [
                            {
                                value: statusData.good,
                                name: "Good",
                            },
                            {
                                value: statusData.poor,
                                name: "Poor",
                            },
                            {
                                value: statusData.failed,
                                name: "Failed",
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
    $(document).ready(function() {
        if (!$.fn.dataTable.isDataTable('#zero_configuration_table')) {
            $('#zero_configuration_table').DataTable({
                scrollX: true,
                fixedColumns: {
                    leftColumns: 3 // Adjust this number to the number of columns you want to freeze
                }
            });
        }
    });
    </script>

</body>

</html>