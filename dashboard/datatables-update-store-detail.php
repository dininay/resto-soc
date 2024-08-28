<?php

include "../koneksi.php";

if (isset($_GET['kode_lahan'])) {
    // Securely get and sanitize the kode_lahan parameter
    $kode_lahan = $conn->real_escape_string($_GET['kode_lahan']);

    // Query to fetch store details based on kode_lahan
    $sql = "SELECT 
        summary_soc.*,
        resto.gostore_date,
        equipment.sla_steqp,
        land.kode_lahan,
        land.nama_lahan,
        land.lokasi,
        land.maps,
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
        socdate_sdg.lamp_sumberair,
        socdate_sdg.lamp_filterair,
        socdate_sdg.lamp_ujilab,
        socdate_sdg.lampwo_reqipal,
        socdate_sdg.lamp_slo,
        socdate_sdg.lamp_nidi,
        sdg_desain.lamp_permit,
        sdg_desain.lamp_pbg,
        dokumen_loacd.kode_store,
        socdate_academy.*
        FROM resto
        LEFT JOIN land ON resto.kode_lahan = land.kode_lahan
        LEFT JOIN summary_soc ON resto.kode_lahan = summary_soc.kode_lahan
        LEFT JOIN equipment ON resto.kode_lahan = equipment.kode_lahan
        LEFT JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
        LEFT JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
        LEFT JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
        LEFT JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
        LEFT JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
        LEFT JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
        LEFT JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
        LEFT JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
        LEFT JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
        LEFT JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
        LEFT JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
        WHERE summary_soc.kode_lahan = '$kode_lahan'
        GROUP BY summary_soc.kode_lahan";

    $result = $conn->query($sql);
    // Check if data is found
    if ($result && $result->num_rows > 0) {
        // Fetch store details
        $row = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "Parameter kode_lahan tidak ditemukan.";
}
$nama_lahan = $row ['nama_lahan'];
$lokasi = $row ['lokasi'];

$sql_chartteam = "SELECT 
    land.kode_lahan,
    land.nama_lahan,
    land.*,
    re.*,
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
WHERE land.kode_lahan = '$kode_lahan'
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
$no = 1;

// Initialize arrays to store dates and re_dates
$dates = [];
$reDates = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Store each status_date and re_date in the respective arrays
        $dates[] = $row['status_date'];
        $reDates[] = $row['re_date'];
    }
} else {
    echo "<th>No Dates Found</th>";
}


// Query untuk mengambil data dari tabel land
$sql = "SELECT 
            land.kode_lahan AS land_kode_lahan,
            land.nama_lahan,
            land.lokasi,
            land.*,
            re.*,
            dokumen_loacd.*,
            sdg_desain.*,
            sdg_rab.*,
            draft.*,
            procurement.*,
            resto.*,
            socdate_hr.*,
            socdate_it.*,
            socdate_ir.*,
            socdate_scm.*,
            socdate_fat.*,
            socdate_marketing.*,
            socdate_academy.*,
            socdate_legal.*,
            socdate_sdg.*,
            sdg_pk.*,
            soc_sdg.*,
            summary_soc.*,
            sign.*,
            equipment.*,
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
        LEFT JOIN re ON re.kode_lahan = land.kode_lahan
        LEFT JOIN dokumen_loacd ON dokumen_loacd.kode_lahan = land.kode_lahan
        LEFT JOIN sdg_desain ON sdg_desain.kode_lahan = land.kode_lahan
        LEFT JOIN equipment ON equipment.kode_lahan = land.kode_lahan
        LEFT JOIN sdg_rab ON sdg_rab.kode_lahan = land.kode_lahan
        LEFT JOIN draft ON draft.kode_lahan = land.kode_lahan
        LEFT JOIN procurement ON procurement.kode_lahan = land.kode_lahan
        LEFT JOIN resto ON resto.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_hr ON socdate_hr.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_marketing ON socdate_marketing.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_it ON socdate_it.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_ir ON socdate_ir.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_legal ON socdate_legal.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_scm ON socdate_scm.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_fat ON socdate_fat.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_academy ON socdate_academy.kode_lahan = land.kode_lahan
        LEFT JOIN socdate_sdg ON socdate_sdg.kode_lahan = land.kode_lahan
        LEFT JOIN sdg_pk ON sdg_pk.kode_lahan = land.kode_lahan
        LEFT JOIN soc_sdg ON soc_sdg.kode_lahan = land.kode_lahan
        LEFT JOIN summary_soc ON summary_soc.kode_lahan = land.kode_lahan
        LEFT JOIN sign ON sign.kode_lahan = land.kode_lahan
        WHERE land.kode_lahan = '$kode_lahan'
        GROUP BY land.kode_lahan";
$result = $conn->query($sql);


$total_remarks = ['good' => 0, 'poor' => 0, 'failed' => 0];
$total_remarks_2 = ['good' => 0, 'poor' => 0, 'failed' => 0];


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
    $start_date = $start_date ?: $today->format('d M y');
    $end_date = $end_date ?: $today->format('d M y');
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



if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
        // ... (lanjutkan untuk semua scoring yang diperlukan)

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
        addToTotal($total_remarks, $remarks1, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks2, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks3, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks4, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks5, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks6, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks7, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks8, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks9, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks10, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks11, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks12, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks13, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks14, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks15, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks16, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks17, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks18, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks19, $row['kode_lahan']);
        addToTotal($total_remarks, $remarks20, $row['kode_lahan']);
        // ... (lanjutkan untuk semua remarks yang diperlukan)

        $dev_ff1 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['ff_1'])) {
            $dev_ff1 = (int)$row['ff_1'] - 100;
        }
        $dev_ff2 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['ff_2'])) {
            $dev_ff2 = (int)$row['ff_2'] - 100;
        }
        $dev_ff3 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['ff_3'])) {
            $dev_ff3 = (int)$row['ff_3'] - 100;
        }
        $dev_kpt1 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['kpt_1'])) {
            $dev_kpt1 = (int)$row['kpt_1'] - 100;
        }
        $dev_kpt2 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['kpt_2'])) {
            $dev_kpt2 = (int)$row['kpt_2'] - 100;
        }
        $dev_kpt3 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['kpt_3'])) {
            $dev_kpt3 = (int)$row['kpt_3'] - 100;
        }
        $dev_scm = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_sj'])) {
            $dev_scm = (int)$row['lamp_sj'] - 100;
        }
        $dev_it = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_config'])) {
            $dev_it = (int)$row['lamp_config'] - 100;
        }
        $dev_it2 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_printer'])) {
            $dev_it2 = (int)$row['lamp_printer'] - 100;
        }
        $dev_it3 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_cctv'])) {
            $dev_it3 = (int)$row['lamp_cctv'] - 100;
        }
        $dev_it4 = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_internet'])) {
            $dev_it4 = (int)$row['lamp_internet'] - 100;
        }
        $dev_marketing = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_merchant'])) {
            $dev_marketing = (int)$row['lamp_merchant'] - 100;
        }
        $dev_taf = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_qris'])) {
            $dev_taf = (int)$row['lamp_qris'] - 100;
        }
        $dev_ho = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_qris'])) {
            $dev_ho = (int)$row['lamp_qris'] - 100;
        }
        $dev_rto = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_qris'])) {
            $dev_rto = (int)$row['lamp_qris'] - 100;
        }
        $dev_go = 0; // default jika tidak ada nilai yang valid
        if (is_numeric($row['lamp_qris'])) {
            $dev_go = (int)$row['lamp_qris'] - 100;
        }

        // Hitung scoring dan remarks untuk masing-masing data
        $month_1 = !empty($row['month_1']) ? $row['month_1'] : 0;
        $month_2 = !empty($row['month_2']) ? $row['month_2'] : 0;
        $month_3 = !empty($row['month_3']) ? $row['month_3'] : 0;

        $scoring1_2 = ($month_1 + $month_2 + $month_3) - 100;
        $scoring2_2 = (!empty($row['lamp_steqp']) ? 100 : 0) - 100;
        $scoring3_2 = $row['bangunan_mural']-100;
        $scoring4_2 = ($row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 5 - 100;
        $scoring5_2 = (!empty($row['lamp_tm']) ? 100 : 0) - 100; 
        $scoring6_2 = $dev_ff1;
        $scoring7_2 = $dev_ff2;
        $scoring8_2 = $dev_ff3;
        $scoring9_2 = $dev_kpt1;
        $scoring10_2 = $dev_kpt2;
        $scoring11_2 = $dev_kpt3;
        $scoring12_2 = $dev_scm;
        $scoring13_2 = $dev_scm;
        $scoring14_2 = $dev_scm;
        $scoring15_2 = $dev_it;
        $scoring16_2 = $dev_it;
        $scoring17_2 = $dev_it2;
        $scoring18_2 = $dev_it3;
        $scoring19_2 = $dev_it4;
        $scoring20_2 = $dev_marketing;
        $scoring21_2 = $dev_marketing;
        $scoring22_2 = $dev_marketing;
        $scoring23_2 = $dev_taf;
        $scoring24_2 = $dev_taf;
        $scoring25_2 = $dev_ho;
        $scoring26_2 = $dev_rto;
        $scoring27_2 = $dev_go;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
        // ... (lanjutkan untuk semua scoring yang diperlukan)

        // Tentukan remarks untuk masing-masing scoring
        $remark1_2 = getRemarks($scoring1_2);
        $remark2_2 = getRemarks($scoring2_2);
        $remark3_2 = getRemarks($scoring3_2);
        $remark4_2 = getRemarks($scoring4_2);
        $remark5_2 = getRemarks($scoring5_2);
        $remark6_2 = getRemarks($scoring6_2);
        $remark7_2 = getRemarks($scoring7_2);
        $remark8_2 = getRemarks($scoring8_2);
        $remark9_2 = getRemarks($scoring9_2);
        $remark10_2 = getRemarks($scoring10_2);
        $remark11_2 = getRemarks($scoring11_2);
        $remark12_2 = getRemarks($scoring12_2);
        $remark13_2 = getRemarks($scoring13_2);
        $remark14_2 = getRemarks($scoring14_2);
        $remark15_2 = getRemarks($scoring15_2);
        $remark16_2 = getRemarks($scoring16_2);
        $remark17_2 = getRemarks($scoring17_2);
        $remark18_2 = getRemarks($scoring18_2);
        $remark19_2 = getRemarks($scoring19_2);
        $remark20_2 = getRemarks($scoring20_2);
        $remark21_2 = getRemarks($scoring21_2);
        $remark22_2 = getRemarks($scoring22_2);
        $remark23_2 = getRemarks($scoring23_2);
        $remark24_2 = getRemarks($scoring24_2);
        $remark25_2 = getRemarks($scoring25_2);
        $remark26_2 = getRemarks($scoring26_2);
        $remark27_2 = getRemarks($scoring27_2);
        // ... (lanjutkan untuk semua remark yang diperlukan)

        // Tambahkan ke total remark berdasarkan kode_lahan
        addToTotal($total_remarks_2, $remark1_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark2_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark3_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark4_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark5_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark6_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark7_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark8_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark9_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark10_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark11_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark12_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark13_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark14_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark15_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark16_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark17_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark18_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark19_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark20_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark21_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark22_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark23_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark24_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark25_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark26_2, $row['kode_lahan']);
        addToTotal($total_remarks_2, $remark27_2, $row['kode_lahan']);

        // Tambahkan data ke dalam array $data jika diperlukan
        $data[] = $row;
        $dates[] = $row;
    }
} else {
    echo "0 results";
}

if (isset($total_remarks[$kode_lahan])) {
    $good_count = $total_remarks[$kode_lahan]['good'];
    $poor_count = $total_remarks[$kode_lahan]['poor'];
    $failed_count = $total_remarks[$kode_lahan]['failed'];

    // echo "Good: $good_count<br>";
    // echo "Poor: $poor_count<br>";
    // echo "Failed: $failed_count<br>";
} else {
    echo "Kode lahan '$kode_lahan' tidak ditemukan atau tidak memiliki data remarks.";
}
$statusData = json_encode([
    'good' => $good_count,
    'poor' => $poor_count,
    'failed' => $failed_count
]);



if (isset($total_remarks_2[$kode_lahan])) {
    $good_count2 = $total_remarks_2[$kode_lahan]['good'];
    $poor_count2 = $total_remarks_2[$kode_lahan]['poor'];
    $failed_count2 = $total_remarks_2[$kode_lahan]['failed'];

    // echo "Good: $good_count<br>";
    // echo "Poor: $poor_count<br>";
    // echo "Failed: $failed_count<br>";
} else {
    echo "Kode lahan '$kode_lahan' tidak ditemukan atau tidak memiliki data remarks.";
}
$statusData2 = json_encode([
    'good2' => $good_count2,
    'poor2' => $poor_count2,
    'failed2' => $failed_count2
]);

// Asumsi koneksi ke database sudah ada di $conn
$phaseQuery = "SELECT 
    land.status_date, 
    land.re_date, 
    re.start_date AS re_start_date,
    re.vl_date AS vl_date,
    re.nego_date AS nego_date,
    dokumen_loacd.start_date AS loa_start_date,
    dokumen_loacd.end_date AS loa_end_date,
    draft.start_date AS draft_start_date,
    sdg_desain.start_date AS ded_start_date,
    sdg_desain.end_date AS ded_end_date,
    draft.end_date AS draft_end_date,
    sdg_rab.start_date AS qs_start_date,
    procurement.start_date AS procur_start_date,
    resto.*,
    equipment.*,
    summary_soc.*,
    sdg_pk.consact_date
FROM land
JOIN re ON land.kode_lahan = re.kode_lahan
JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
JOIN draft ON land.kode_lahan = draft.kode_lahan
JOIN equipment ON land.kode_lahan = equipment.kode_lahan
JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
JOIN sdg_rab ON land.kode_lahan = sdg_rab.kode_lahan
JOIN procurement ON land.kode_lahan = procurement.kode_lahan
JOIN resto ON land.kode_lahan = resto.kode_lahan
JOIN summary_soc ON land.kode_lahan = summary_soc.kode_lahan
JOIN sdg_pk ON land.kode_lahan = sdg_pk.kode_lahan
WHERE land.kode_lahan = '$kode_lahan'
GROUP BY land.kode_lahan";
$phaseResult = $conn->query($phaseQuery);

// $dates = [];
$phases = [];
$currentDate = date('Y-m-d'); // Tanggal hari ini

while ($row = $phaseResult->fetch_assoc()) {
    // Menambahkan tanggal ke array $dates
    $dates[] = ['status_date' => $row['status_date']];

    // RE Submit Lahan
    $phases[] = [
        'start' => $row['status_date'],
        'end' => $row['re_date'] ?: $currentDate,
        'phase_name' => 'RE Submit Lahan'
    ];

    // BoD Validation
    $phases[] = [
        'start' => $row['re_date'] ?: $currentDate,
        'end' => $row['re_start_date'] ?: $currentDate,
        'phase_name' => 'BoD Validation'
    ];

    // Negosiasi
    $phases[] = [
        'start' => $row['re_start_date'] ?: $currentDate,
        'end' => $row['nego_date'] ?: $currentDate,
        'phase_name' => 'Negosiasi'
    ];

    // Validasi Lahan
    $phases[] = [
        'start' => $row['re_date'] ?: $currentDate,
        'end' => $row['vl_date'] ?: $currentDate,
        'phase_name' => 'Validasi Lahan'
    ];

    // LOA CD
    $phases[] = [
        'start' => $row['vl_date'] ?: $currentDate,
        'end' => $row['loa_start_date'] ?: $currentDate,
        'phase_name' => 'LOA CD'
    ];

    // Validasi Data
    $phases[] = [
        'start' => $row['vl_date'] ?: $currentDate,
        'end' => $row['loa_end_date'] ?: $currentDate,
        'phase_name' => 'Validasi Data'
    ];

    // Draft Sewa
    $phases[] = [
        'start' => $row['loa_end_date'] ?: $currentDate,
        'end' => $row['draft_start_date'] ?: $currentDate,
        'phase_name' => 'Draft Sewa'
    ];

    // TTD Sewa
    $phases[] = [
        'start' => $row['draft_start_date'] ?: $currentDate,
        'end' => $row['draft_end_date'] ?: $currentDate,
        'phase_name' => 'TTD Sewa'
    ];

    // DED
    $phases[] = [
        'start' => $row['nego_date'] ?: $currentDate,
        'end' => $row['ded_start_date'] ?: $currentDate,
        'phase_name' => 'DED'
    ];

    // Permit
    $phases[] = [
        'start' => $row['ded_start_date'] ?: $currentDate,
        'end' => $row['ded_end_date'] ?: $currentDate,
        'phase_name' => 'Permit'
    ];

    // QS
    $phases[] = [
        'start' => $row['ded_start_date'] ?: $currentDate,
        'end' => $row['qs_start_date'] ?: $currentDate,
        'phase_name' => 'QS'
    ];

    // Tender
    $phases[] = [
        'start' => $row['qs_start_date'] ?: $currentDate,
        'end' => $row['procur_start_date'] ?: $currentDate,
        'phase_name' => 'Tender'
    ];

    // SPK
    $phases[] = [
        'start' => $row['procur_start_date'] ?: $currentDate,
        'end' => $row['spk_date'] ?: $currentDate,
        'phase_name' => 'SPK'
    ];

    // Kick Off Meeting
    $phases[] = [
        'start' => $row['spk_date'] ?: $currentDate,
        'end' => $row['kom_date'] ?: $currentDate,
        'phase_name' => 'Kick Off Meeting'
    ];

    // Konstruksi
    $phases[] = [
        'start' => $row['start_konstruksi'] ?: $currentDate,
        'end' => $row['consact_date'] ?: $currentDate,
        'phase_name' => 'Konstruksi'
    ];

    // ST Equipment
    $phases[] = [
        'start' => $row['start_konstruksi'] ?: $currentDate,
        'end' => $row['steqp_date'] ?: $currentDate,
        'phase_name' => 'ST Equipment'
    ];

    // ST Kontraktor
    $phases[] = [
        'start' => $row['steqp_date'] ?: $currentDate,
        'end' => $row['stkonstruksi_date'] ?: $currentDate,
        'phase_name' => 'ST Kontraktor'
    ];

    // RTO
    $phases[] = [
        'start' => $row['stkonstruksi_date'] ?: $currentDate,
        'end' => $row['rto_act'] ?: $currentDate,
        'phase_name' => 'RTO'
    ];

    // GO
    $phases[] = [
        'start' => $row['rto_act'] ?: $currentDate,
        'end' => $row['go_fix'] ?: $currentDate,
        'phase_name' => 'GO'
    ];
}

// Konversi $phases ke JSON untuk digunakan di JavaScript
$phasesJSON = json_encode($phases);
// Tentukan tanggal mulai dari data, gunakan tanggal pertama dari data jika ada
$startDate = isset($dates[0]['status_date']) ? $dates[0]['status_date'] : date('d M y');

// Fungsi untuk menghasilkan tanggal-tanggal untuk timeline
function generateTimeline($startDate, $days) {
    $timeline = [];
    for ($i = 0; $i < $days; $i++) {
        $timeline[] = date('d M y', strtotime($startDate . " +$i days"));
    }
    return $timeline;
}



// Generate timeline untuk 180 hari ke depan
$timeline = generateTimeline($startDate, 180);
// Close the database connection
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
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.3.8/echarts.min.js"></script>
    <style>
        #phaseChart {
            width: 100%;
            height: 500px;
        }
        body {
        margin: 0;
        padding: 0;
        }
        .container {
            width: 100%;
            overflow: hidden;
        }

        .scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            width: 100%;
        }

        .timeline-header,
        .timeline-content {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .timeline-header {
            background: #f9f9f9;
            border-bottom: 2px solid #ccc;
            padding: 5px 0;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .timeline-header .phase,
        .timeline-header .phase-label,
        .timeline-header .day {
            padding: 10px;
            text-align: center;
            border-right: 1px solid #ccc;
            min-width: 100px; /* Width for day columns */
            box-sizing: border-box;
        }

        .timeline-header .phase {
            width: 50px; /* Width for phase number column */
            text-align: center;
        }

        .timeline-header .phase-label {
            width: 200px; /* Width for phase name column */
            text-align: left;
        }

        .timeline-content {
            width: max-content;
            display: flex;
            flex-direction: column;
        }

        .timeline-row {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ccc;
            position: relative;
            min-height: 60px; /* Height of each row */
        }

        .timeline-row:nth-child(even) {
            background-color: #f2f2f2;
        }

        .phase {
            width: 50px; 
            text-align: center;
            border-right: 1px solid #ccc;
        }

        .phase-label {
            width: 150px; 
            border-right: 1px solid #ccc;
            text-align: center;
        }

        .timeline {
            display: flex;
            flex-direction: row;
            width: max-content;
            position: relative;
        }

        .day {
            min-width: 100px; /* Width of each day cell */
            padding: 15px; /* Padding for better spacing */
            text-align: center;
            border-right: 1px solid #ccc;
            position: relative;
            box-sizing: border-box;
        }

        .bar {
            position: absolute;
            top: 30px; /* Adjusted top position for bars */
            height: 15px; /* Height of the bar */
            background-color: #4CAF50;
            z-index: 1;
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
                    <h1 class="mr-2">Detail Update per Store</h1>
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li>Detail Update per Store</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Add-User"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">New Leads</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">205</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Financial"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Sales</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">$4021</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
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
                    </div>
                </div> -->
                <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                                        <div class="card-body">
                                            <!-- <i class="i-Add-User mr-3"></i> -->
                                            <h5 class="text-muted mt-2 mb-2">Inventory Code</h5>
                                            <div class="content">
                                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $kode_lahan; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                                        <div class="card-body">
                                            <!-- <i class="i-Add-User mr-3"></i> -->
                                            <h5 class="text-muted mt-2 mb-2">Nama Lahan</h5>
                                            <div class="content">
                                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $nama_lahan; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                                        <div class="card-body">
                                            <!-- <i class="i-Add-User mr-3"></i> -->
                                            <h5 class="text-muted mt-2 mb-2">Lokasi</h5>
                                            <div class="content">
                                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $lokasi; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-8 col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="card-title">All Dept Tracking</div>
                                            <div id="teamChart" style="height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-4 col-sm-12">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="card-title">Status In Preparation</div>
                                                <div id="echartGo" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="col-lg-4 col-sm-12">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="card-title">Status In Progress</div>
                                                <div id="echartGo2" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="card-title">Project Phase Tracking</div>
                                            <div id="phaseChart" style="height: 500px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <!-- <div class="row">
                    <div class="container">
                        <div class="scroll-container">
                            <div class="timeline-header">
                                <div class="phase">No</div>
                                <div class="phase-label">Phase</div>
                                <?php foreach ($timeline as $day) : ?>
                                    <div class="day"><?php echo $day; ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="timeline-content">
                                <?php
                                $phaseNumber = 1;
                                foreach ($phases as $phase) {
                                    $phaseName = isset($phase['phase_name']) ? $phase['phase_name'] : 'Unknown Phase';

                                    $startDateIndex = array_search($phase['start'], $timeline);
                                    $endDateIndex = array_search($phase['end'], $timeline);

                                    // Calculate the indices for bars
                                    $startIndex = $startDateIndex !== false ? $startDateIndex : -1;
                                    $endIndex = $endDateIndex !== false ? $endDateIndex : -1;

                                    echo '<div class="timeline-row">';
                                    echo '<div class="phase">' . $phaseNumber . '</div>';
                                    echo '<div class="phase-label">' . $phaseName . '</div>';
                                    echo '<div class="timeline">';
                                    foreach ($timeline as $index => $day) {
                                        echo '<div class="day">';
                                        if ($startIndex != -1 && $endIndex != -1 && $index >= $startIndex && $index <= $endIndex) {
                                            $barWidth = ($endIndex - $startIndex + 1) * 100; // Adjust width if needed
                                            $barLeft = $startIndex * 100; // Adjust left position if needed
                                            echo '<div class="bar" style="width: ' . $barWidth . 'px; left: ' . $barLeft . 'px;"></div>';
                                        }
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    $phaseNumber++;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div> -->

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
                                                    <th colspan="7" style="background-color: #6c757d; color: white;"><?= $row['kode_store'] ?></th>
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
                                                $sla_re_date = !empty($row['status_date']) ? date('d M y', strtotime($row['status_date'] . ' +' . ($master_sla['RE'] ?? 0) . ' days')) : '';
                                                
                                                ?>
                                                <td><?= date('d M y', strtotime($row['status_date'])) ?></td>
                                                <td><?= $sla_re_date ?></td>
                                                <td><?= $master_sla['RE'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['status_date']) && !empty($row['status_date']) ? date('d M y', strtotime($row['status_date'])) : '0' ?></td>
                                                <td><?= isset($row['re_date']) && !empty($row['re_date']) ? date('d M y', strtotime($row['re_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['status_date'], $row['re_date'], $master_sla['RE']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);    
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';         
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
                                                <td><?= $sla_re_date ?></td>
                                                <td><?= $sla_bod_date ?></td>
                                                <td><?= $master_sla['Owner Surveyor'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['re_date']) && !empty($row['re_date']) ? date('d M y', strtotime($row['re_date'])) : '0' ?></td>
                                                <td><?= isset($row['re_start_date']) && !empty($row['re_start_date']) ? date('d M y', strtotime($row['re_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_date'], $row['re_start_date'], $master_sla['Owner Surveyor']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);  
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                   
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
                                                <td><?= $sla_bod_date ?></td>
                                                <td><?= $sla_legal_date?></td>
                                                <td><?= $master_sla['VL'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['re_start_date']) && !empty($row['re_start_date']) ? date('d M y', strtotime($row['re_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['vl_date']) && !empty($row['vl_date']) ? date('d M y', strtotime($row['vl_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['re_start_date'], $row['vl_date'], $master_sla['VL']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);     
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                  
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
                                                <td><?= $sla_legal_date?></td>
                                                <td><?= $sla_nego_date?></td>
                                                <td><?= $master_sla['Negosiator'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['end_date']) && !empty($row['end_date']) ? date('d M y', strtotime($row['end_date'])) : '0' ?></td>
                                                <td><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['end_date'], $row['nego_date'], $master_sla['Negosiator']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);           
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';  
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
                                                <td><?= $sla_nego_date ?></td>
                                                <td><?= $sla_loa_date?></td>
                                                <td><?= $master_sla['LOA-CD'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <td><?= isset($row['dokumen_loacd_start_date']) && !empty($row['dokumen_loacd_start_date']) ? date('d M y', strtotime($row['dokumen_loacd_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['dokumen_loacd_start_date'], $master_sla['LOA-CD']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                 
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';       
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
                                                <td><?= $sla_loa_date ?></td>
                                                <td><?= $sla_vd_date?></td>
                                                <td><?= $master_sla['VD'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['vl_date']) && !empty($row['vl_date']) ? date('d M y', strtotime($row['vl_date'])) : '0' ?></td>
                                                <td><?= isset($row['dokumen_loacd_end_date']) && !empty($row['dokumen_loacd_end_date']) ? date('d M y', strtotime($row['dokumen_loacd_end_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['vl_date'], $row['dokumen_loacd_end_date'], $master_sla['VD']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);       
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                     
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
                                                <td><?= $sla_nego_date ?></td>
                                                <td><?= $sla_survey_date?></td>
                                                <td><?= $master_sla['Land Survey'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['nego_date']) && !empty($row['nego_date']) ? date('d M y', strtotime($row['nego_date'])) : '0' ?></td>
                                                <td><?= isset($row['survey_date']) && !empty($row['survey_date']) ? date('d M y', strtotime($row['survey_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['nego_date'], $row['survey_date'], $master_sla['Land Survey']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);    
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                      
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
                                                <td><?= $sla_survey_date ?></td>
                                                <td><?= $sla_layout_date?></td>
                                                <td><?= $master_sla['Layouting'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['survey_date']) && !empty($row['survey_date']) ? date('d M y', strtotime($row['survey_date'])) : '0' ?></td>
                                                <td><?= isset($row['layout_date']) && !empty($row['layout_date']) ? date('d M y', strtotime($row['layout_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['survey_date'], $row['layout_date'], $master_sla['Layouting']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);          
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                  
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
                                                <td rowspan="2"><?= $sla_layout_date ?></td>
                                                <td rowspan="2"><?= $sla_sdgded_date?></td>
                                                <td rowspan="2"><?= $master_sla['Design'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['layout_date']) && !empty($row['layout_date']) ? date('d M y', strtotime($row['layout_date'])) : '0' ?></td>
                                                <td><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['layout_date'], $row['sdg_desain_start_date'], $master_sla['Design']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);            
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                     
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                <td rowspan="2" style="background-color: white"><?= $sla_sdgded_date ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $sla_rab_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['QS'] ?? 'N/A' ?></td>
                                                <td rowspan="2" style="background-color: white"><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td rowspan="2" style="background-color: white"><?= isset($row['sdg_qs_start_date']) && !empty($row['sdg_qs_start_date']) ? date('d M y', strtotime($row['sdg_qs_start_date'])) : '0' ?></td>
                                                
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_qs_start_date'], $master_sla['QS']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                        
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                   
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                <td><?= $sla_vd_date ?></td>
                                                <td><?= $sla_draft_date?></td>
                                                <td><?= $master_sla['Draft-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['dokumen_loacd_end_date']) && !empty($row['dokumen_loacd_end_date']) ? date('d M y', strtotime($row['dokumen_loacd_end_date'])) : '0' ?></td>
                                                <td><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['dokumen_loacd_end_date'], $row['draft_start_date'], $master_sla['Draft-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                         
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';               
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
                                                <td><?= $sla_draft_date ?></td>
                                                <td><?= $sla_fat_date?></td>
                                                <td><?= $master_sla['FAT-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['draft_fat_date']) && !empty($row['draft_fat_date']) ? date('d M y', strtotime($row['draft_fat_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_fat_date'], $master_sla['FAT-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                         
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                  
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
                                                <td><?= $sla_fat_date ?></td>
                                                <td><?= $sla_psm_date?></td>
                                                <td><?= $master_sla['TTD-Sewa'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['draft_start_date']) && !empty($row['draft_start_date']) ? date('d M y', strtotime($row['draft_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['draft_end_date']) && !empty($row['draft_end_date']) ? date('d M y', strtotime($row['draft_end_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['draft_start_date'], $row['draft_end_date'], $master_sla['TTD-Sewa']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                    
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                     
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
                                                <td><?= $sla_psm_date ?></td>
                                                <td><?= $sla_permit_date?></td>
                                                <td><?= $master_sla['Legal'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['sdg_desain_submit_date']) && !empty($row['sdg_desain_submit_date']) ? date('d M y', strtotime($row['sdg_desain_submit_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['sdg_desain_submit_date'], $master_sla['Legal']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);          
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                  
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
                                                <td rowspan="2"><?= $sla_rab_date ?></td>
                                                <td rowspan="2"><?= $sla_tender_date?></td>
                                                <td rowspan="2"><?= $master_sla['Tender'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['procurement_start_date']) && !empty($row['procurement_start_date']) ? date('d M y', strtotime($row['procurement_start_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['sdg_desain_start_date'], $row['procurement_start_date'], $master_sla['Tender']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                         
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                <td rowspan="2" style="background-color: white"><?= $sla_tender_date ?></td>
                                                <td rowspan="2" style="background-color: white"><?= $sla_spk_date?></td>
                                                <td rowspan="2" style="background-color: white"><?= $master_sla['SPK'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['sdg_desain_start_date']) && !empty($row['sdg_desain_start_date']) ? date('d M y', strtotime($row['sdg_desain_start_date'])) : '0' ?></td>
                                                <td><?= isset($row['spk_date']) && !empty($row['spk_date']) ? date('d M y', strtotime($row['spk_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['procurement_start_date'], $row['spk_date'], $master_sla['SPK']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                       
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                     
                                                ?>
                                                <td><?= $display_scoring ?></td>
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
                                                <td><?= $sla_spk_date ?></td>
                                                <td><?= $sla_spkfat_date?></td>
                                                <td><?= $master_sla['SPK-FAT'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['spk_date']) && !empty($row['spk_date']) ? date('d M y', strtotime($row['spk_date'])) : '0' ?></td>
                                                <td><?= isset($row['fat_date']) && !empty($row['fat_date']) ? date('d M y', strtotime($row['fat_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['spk_date'], $row['fat_date'], $master_sla['SPK-FAT']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);                     
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                      
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
                                                <td><?= $sla_spk_date ?></td>
                                                <td><?= $sla_kom_date?></td>
                                                <td><?= $master_sla['KOM'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['fat_date']) && !empty($row['fat_date']) ? date('d M y', strtotime($row['fat_date'])) : '0' ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['fat_date'], $row['kom_date'], $master_sla['KOM']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                      
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
                                                <td><?= $sla_kom_date ?></td>
                                                <td><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= isset($row['ff3_date']) && !empty($row['ff3_date']) ? date('d M y', strtotime($row['ff3_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);        
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                        
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
                                                <td><?= $sla_kom_date ?></td>
                                                <td><?= $sla_ff_date?></td>
                                                <td><?= $master_slacons['hrga_tm'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= isset($row['ff3_date']) && !empty($row['ff3_date']) ? date('d M y', strtotime($row['ff3_date'])) : '0' ?></td>
                                                <?php 
                                                $scoring = calculateScoring($row['kom_date'], $row['ff3_date'], $master_slacons['hrga_tm']);
                                                $remarks = getRemarks($scoring);
                                                $badge_color = getBadgeColor($remarks);      
                                                $display_scoring = $scoring < -200 ? '-200%++' : round($scoring) . '%';                   
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
                                                <td class="sticky" rowspan="1" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('d M y', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <?php
                                                $good_totals = [];
                                                foreach ($good_totals as $kode_lahan => $totals) {
                                                    $total_good = $totals['good'];
                                                    echo "Total Good for kode_lahan $kode_lahan: $total_good<br>";
                                                }
                                                ?>
                                                
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('good') ?>">Total Good</span></td>
                                                <td colspan="1"><?= $good_count ?></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('poor') ?>">Total Poor</span></td>
                                                <td colspan="1"><?= $poor_count ?></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('failed') ?>">Total Failed</span></td>
                                                <td colspan="1"><?= $failed_count ?></td>
                                                <td></td>
                                                <?php endforeach; ?>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                                <tr>
                                                    <th rowspan="5" class="sticky" style="background-color: #6c757d; color: white;">Proses Phase</th>
                                                    <th rowspan="5" class="sticky" style="background-color: #6c757d; color: white;">Detail Process</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="sticky" style="background-color: #6c757d; color: white;">Store</th>
                                                    <?php foreach ($data as $row): ?>
                                                    <th colspan="9" style="background-color: #6c757d; color: white;"><?= $row['kode_store'] ?></th>
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
                                                $sla_cons_date = !empty($row['start_konstruksi']) ? date('d M y', strtotime($row['start_konstruksi'] . ' +' . ($master_sla['Konstruksi'] ?? 0) . ' days')) : '';
                                                $month_1 = !empty($row['month_1']) ? $row['month_1'] : 0;
                                                $month_2 = !empty($row['month_2']) ? $row['month_2'] : 0;
                                                $month_3 = !empty($row['month_3']) ? $row['month_3'] : 0;

                                                $scoring1_2 = ($month_1 + $month_2 + $month_3) - 100;
                                                $cons = ($month_1 + $month_2 + $month_3) - 100;
                                                $deviasi_cons = $cons - 100;
                                                ?>
                                                <td><?= isset($row['start_konstruksi']) && !empty($row['start_konstruksi']) ? date('d M y', strtotime($row['start_konstruksi'])) : '0' ?></td>
                                                <td><?= $sla_cons_date ?></td>
                                                <td><?= $master_sla['Konstruksi'] ?? 'N/A' ?></td>
                                                <td><?= isset($row['start_konstruksi']) && !empty($row['start_konstruksi']) ? date('d M y', strtotime($row['start_konstruksi'])) : '0' ?></td>
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
                                                        $start_steqp_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date'] . ' -' . 11 . ' days')) : '';
                                                        // Menghitung sla_steqp_date berdasarkan start_steqp_date ditambah 2 hari
                                                        $sla_steqp_date = !empty($start_steqp_date) ? date('d M y', strtotime($start_steqp_date . ' +' . 2 . ' days')) : '';
                                                        $steqp = !empty($row['lamp_steqp']) ? 100 : 0; 
                                                        $dev_steqp = $steqp - 100 ;
                                                    ?>
                                                <td><?= $start_steqp_date ?></td>
                                                <td><?= $sla_steqp_date ?></td>
                                                <td>2</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '' ?></td>
                                                <td><?= $steqp ?>%</td>
                                                <td><?= $dev_steqp?>%</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '' ?></td>
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
                                                    $start_pylon_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date'] . ' -' . 21 . ' days')) : '';
                                                    $sla_pylon_date = !empty($start_pylon_date) ? date('d M y', strtotime($start_pylon_date . ' +' . 1 . ' days')) : '';
                                                    $bangunan_mural = $row['bangunan_mural']-100;
                                                    ?>
                                                <td><?= $start_pylon_date ?></td>
                                                <td><?= $sla_pylon_date?></td>
                                                <td>1</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '0' ?></td>
                                                <td><?= $row['bangunan_mural'] ?>%</td>
                                                <td><?= $bangunan_mural ?>%</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '0' ?></td>
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
                                                    $start_ffeqp_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date'] . ' -' . 4 . ' days')) : '';
                                                    $sla_ffeqp_date = !empty($start_ffeqp_date) ? date('d M y', strtotime($start_ffeqp_date . ' +' . 1 . ' days')) : '';
                                                    // Menghitung hasil penjumlahan dari beberapa kolom
                                                    $total = ($row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) /5;
                                                    $scoring = ($row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 5 - 100;
                                                    ?>
                                                <td><?= $start_ffeqp_date ?></td>
                                                <td><?= $sla_ffeqp_date?></td>
                                                <td>1</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '0' ?></td>
                                                <td><?= $total ?>%</td>
                                                <td><?= $scoring ?>%</td>
                                                <td><?= isset($row['steqp_date']) && !empty($row['steqp_date']) ? date('d M y', strtotime($row['steqp_date'])) : '0' ?></td>
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
                                                        $start_tm_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date'] . ' -' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : '';
                                                        // Menghitung sla_tm_date berdasarkan start_tm_date ditambah 2 hari
                                                        $sla_tm_date = !empty($row['draft_end_date']) ? date('d M y', strtotime($row['draft_end_date'] . ' +' . $master_slacons['hrga_tm'] . ' days')) : '';
                                                        $tm = !empty($row['lamp_tm']) ? 100 : 0; 
                                                        $dev_tm = $tm - 100 ;
                                                    ?>
                                                <td><?= isset($row['draft_end_date']) && !empty($row['draft_end_date']) ? date('d M y', strtotime($row['draft_end_date'])) : '0' ?></td>
                                                <td><?= $sla_tm_date ?></td>
                                                <td><?= $master_slacons['hrga_tm']?></td>
                                                <td><?= isset($row['draft_end_date']) && !empty($row['draft_end_date']) ? date('d M y', strtotime($row['draft_end_date'])) : '0' ?></td>
                                                <td><?= $tm ?>%</td>
                                                <td><?= $dev_tm?>%</td>
                                                <td><?= isset($row['tm_date']) && !empty($row['tm_date']) ? date('d M y', strtotime($row['tm_date'])) : '0' ?></td>
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
                                                    $sla_ff1_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $ff1 = isset($row['ff_1']) ? $row['ff_1'] : 0;
                                                    $dev_ff1 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_1'])) {
                                                        $dev_ff1 = (int)$row['ff_1'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_ff1_date?></td>
                                                <td><?= $master_slacons['hrga_ff1'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $ff1 ?>%</td>
                                                <td><?= $dev_ff1 ?>%</td>
                                                <td><?= isset($row['ff1_date']) && !empty($row['ff1_date']) ? date('d M y', strtotime($row['ff1_date'])) : '0' ?></td>
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
                                                    $start_ff2_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff2'] . ' days')) : '';
                                                    $sla_ff2_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff2'] . ' days')) : '';
                                                    // $ff2 = !empty($row['lamp_ff2']) ? 100 : 0; 
                                                    $ff2 = isset($row['ff_2']) ? $row['ff_2'] : 0;
                                                    $dev_ff2 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_2'])) {
                                                        $dev_ff2 = (int)$row['ff_2'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_ff2_date?></td>
                                                <td><?= $master_slacons['hrga_ff2'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $ff2 ?>%</td>
                                                <td><?= $dev_ff2 ?>%</td>
                                                <td><?= isset($row['ff2_date']) && !empty($row['ff2_date']) ? date('d M y', strtotime($row['ff2_date'])) : '0' ?></td>
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
                                                    $start_ff3_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    $sla_ff3_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['hrga_ff1'] . ' days')) : '';
                                                    // $ff3 = !empty($row['lamp_ff3']) ? 100 : 0; 
                                                    $ff3 = isset($row['ff_3']) ? $row['ff_3'] : 0;
                                                    $dev_ff3 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['ff_3'])) {
                                                        $dev_ff3 = (int)$row['ff_3'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_ff3_date?></td>
                                                <td><?= $master_slacons['hrga_ff3'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $ff3 ?>%</td>
                                                <td><?= $dev_ff3 ?>%</td>
                                                <td><?= isset($row['ff3_date']) && !empty($row['ff3_date']) ? date('d M y', strtotime($row['ff3_date'])) : '0' ?></td>
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
                                                    $sla_kpt1_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $kpt1 = isset($row['kpt_1']) ? $row['kpt_1'] : 0;
                                                    $dev_kpt1 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_1'])) {
                                                        $dev_kpt1 = (int)$row['kpt_1'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_kpt1_date?></td>
                                                <td><?= $master_slacons['kpt1'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $kpt1 ?>%</td>
                                                <td><?= $dev_kpt1 ?>%</td>
                                                <td><?= isset($row['kpt_date1']) && !empty($row['kpt_date1']) ? date('d M y', strtotime($row['kpt_date1'])) : '0' ?></td>
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
                                                    $start_kpt2_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt2'] . ' days')) : '';
                                                    $sla_kpt2_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt2'] . ' days')) : '';
                                                    // $kpt2 = !empty($row['lamp_kpt2']) ? 100 : 0; 
                                                    $kpt2 = isset($row['kpt_2']) ? $row['kpt_2'] : 0;
                                                    $dev_kpt2 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_2'])) {
                                                        $dev_kpt2 = (int)$row['kpt_2'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_kpt2_date?></td>
                                                <td><?= $master_slacons['kpt2'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $kpt2 ?>%</td>
                                                <td><?= $dev_kpt2 ?>%</td>
                                                <td><?= isset($row['kpt_date2']) && !empty($row['kpt_date2']) ? date('d M y', strtotime($row['kpt_date2'])) : '0' ?></td>
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
                                                    $start_kpt3_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    $sla_kpt3_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['kpt1'] . ' days')) : '';
                                                    // $kpt3 = !empty($row['lamp_kpt3']) ? 100 : 0; 
                                                    $kpt3 = isset($row['kpt_3']) ? $row['kpt_3'] : 0;
                                                    $dev_kpt3 = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['kpt_3'])) {
                                                        $dev_kpt3 = (int)$row['kpt_3'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_kpt3_date?></td>
                                                <td><?= $master_slacons['kpt3'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $kpt3 ?>%</td>
                                                <td><?= $dev_kpt3 ?>%</td>
                                                <td><?= isset($row['kpt_date3']) && !empty($row['kpt_date3']) ? date('d M y', strtotime($row['kpt_date3'])) : '0' ?></td>
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
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= isset($row['sj_date']) && !empty($row['sj_date']) ? date('d M y', strtotime($row['sj_date'])) : '0' ?></td>
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
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= isset($row['sj_date']) && !empty($row['sj_date']) ? date('d M y', strtotime($row['sj_date'])) : '0' ?></td>
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
                                                    $sla_scm_date = !empty($row['kom_date'] ) ? date('d M y', strtotime($row['kom_date']  . ' +' . $master_slacons['scm'] . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $scm = isset($row['lamp_sj']) ? 100 : 0;
                                                    $dev_scm = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_sj'])) {
                                                        $dev_scm = (int)$row['lamp_sj'] - 100;
                                                    }
                                                    ?>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $sla_scm_date?></td>
                                                <td><?= $master_slacons['scm'] ?></td>
                                                <td><?= isset($row['kom_date']) && !empty($row['kom_date']) ? date('d M y', strtotime($row['kom_date'])) : '0' ?></td>
                                                <td><?= $scm ?>%</td>
                                                <td><?= $dev_scm ?>%</td>
                                                <td><?= isset($row['sj_date']) && !empty($row['sj_date']) ? date('d M y', strtotime($row['sj_date'])) : '0' ?></td>
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
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('d M y', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
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
                                                <td><?= isset($row['it_date']) && !empty($row['it_date']) ? date('d M y', strtotime($row['it_date'])) : '0' ?></td>
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
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('d M y', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
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
                                                <td><?= isset($row['it_date']) && !empty($row['it_date']) ? date('d M y', strtotime($row['it_date'])) : '0' ?></td>
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
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('d M y', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
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
                                                <td><?= isset($row['it_date']) && !empty($row['it_date']) ? date('d M y', strtotime($row['it_date'])) : '0' ?></td>
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
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('d M y', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    // $ff1 = !empty($row['lamp_ff1']) ? 100 : 0; 
                                                    $it = isset($row['lamp_cctv']) ? 100 : 0;
                                                    $dev_it = 0; // default jika tidak ada nilai yang valid
                                                    if (is_numeric($row['lamp_cctv'])) {
                                                        $dev_it = (int)$row['lamp_cctv'] - 100;
                                                    }
                                                    ?>
                                                <td><?= $start_it_date ?></td>
                                                <td><?= $sla_it_date?></td>
                                                <td>1</td>
                                                <td><?= $start_act_it_date ?></td>
                                                <td><?= $it ?>%</td>
                                                <td><?= $dev_it ?>%</td>
                                                <td><?= isset($row['it_date']) && !empty($row['it_date']) ? date('d M y', strtotime($row['it_date'])) : '0' ?></td>
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
                                                    $sla_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $start_it_date = !empty($sla_it_date) ? date('d M y', strtotime($sla_it_date  . ' -' . 1 . ' days')) : '';
                                                    $start_act_it_date = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
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
                                                <td><?= isset($row['it_date']) && !empty($row['it_date']) ? date('d M y', strtotime($row['it_date'])) : '0' ?></td>
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
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('d M y', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['merchant_date']) && !empty($row['merchant_date']) ? date('d M y', strtotime($row['merchant_date'])) : '0' ?></td>
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
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('d M y', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['merchant_date']) && !empty($row['merchant_date']) ? date('d M y', strtotime($row['merchant_date'])) : '0' ?></td>
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
                                                    $start_marketing_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_marketing_date = !empty($start_marketing_date ) ? date('d M y', strtotime($start_marketing_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['merchant_date']) && !empty($row['merchant_date']) ? date('d M y', strtotime($row['merchant_date'])) : '0' ?></td>
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
                                                    $start_taf_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_taf_date = !empty($start_taf_date ) ? date('d M y', strtotime($start_taf_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['merchant_date']) && !empty($row['merchant_date']) ? date('d M y', strtotime($row['merchant_date'])) : '0' ?></td>
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
                                                    $start_taf_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_taf_date = !empty($start_taf_date ) ? date('d M y', strtotime($start_taf_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['merchant_date']) && !empty($row['merchant_date']) ? date('d M y', strtotime($row['merchant_date'])) : '0' ?></td>
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
                                                    $start_ho_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_ho_date = !empty($start_ho_date ) ? date('d M y', strtotime($start_ho_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['soc_date']) && !empty($row['soc_date']) ? date('d M y', strtotime($row['soc_date'])) : '0' ?></td>
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
                                                    $start_rto_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 3 . ' days')) : '';
                                                    $sla_rto_date = !empty($start_rto_date ) ? date('d M y', strtotime($start_rto_date  . ' +' . 1 . ' days')) : '';
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
                                                <td><?= isset($row['rto_act']) && !empty($row['rto_act']) ? date('d M y', strtotime($row['rto_act'])) : '0' ?></td>
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
                                                    $start_go_date = !empty($row['gostore_date'] ) ? date('d M y', strtotime($row['gostore_date']  . ' -' . 2 . ' days')) : '';
                                                    $sla_go_date = !empty($start_go_date ) ? date('d M y', strtotime($start_go_date  . ' +' . 2 . ' days')) : '';
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
                                                <td><?= isset($row['go_fix']) && !empty($row['go_fix']) ? date('d M y', strtotime($row['go_fix'])) : '0' ?></td>
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
                                            <tr>
                                                <td class="sticky" rowspan="1" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <td class="sticky" style="background-color: white"></td>
                                                <?php foreach ($data as $row): ?>
                                                    <?php                                              
                                                    $sla_ff_date = $sla_kom_date != 'N/A' ? date('d M y', strtotime($sla_kom_date . ' +' . ($master_slacons['hrga_tm'] ?? 0) . ' days')) : 'N/A';
                                                    ?>
                                                <?php
                                                $goodTotals = [];
                                                foreach ($goodTotals as $kode_lahan => $total) {
                                                    $totalGood = $total['good'];
                                                    echo "Total Good for kode_lahan $kode_lahan: $totalGood<br>";
                                                }
                                                ?>
                                                
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('good') ?>">Total Good</span></td>
                                                <td colspan="1"><?= $good_count2 ?></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('poor') ?>">Total Poor</span></td>
                                                <td colspan="1"><?= $poor_count2 ?></td>
                                                <td colspan="2"><span class="badge badge-<?= getBadgeColor('failed') ?>">Total Failed</span></td>
                                                <td colspan="1"><?= $failed_count2 ?></td>
                                                <td colspan="2"></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                <!-- <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="row">
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
                        </div>
                        <div class="row">
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
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
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
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <h5 class="card-title m-0 p-3">Last 20 Day Leads</h5>
                                <div id="echart3" style="height: 360px;"></div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- end of main-content -->
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
        // Convert the PHP statusData to JavaScript object
        var statusData2 = <?php echo $statusData2; ?>;

        // Initialize ECharts
        var echartElemPie = document.getElementById("echartGo2");

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
                        name: "Status In Progress",
                        type: "pie",
                        radius: "50%",
                        center: ["50%", "50%"],
                        data: [
                            {
                                value: statusData2.good2,
                                name: "Good",
                            },
                            {
                                value: statusData2.poor2,
                                name: "Poor",
                            },
                            {
                                value: statusData2.failed2,
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
    document.addEventListener('DOMContentLoaded', function() {
        // Data from PHP
        var phases = <?php echo $phasesJSON; ?>;
        
        // Configure chart
        var phaseChart = echarts.init(document.getElementById('phaseChart'));

        // Convert phase data to chart data format
        var chartData = phases.map(function(phase) {
            return {
                name: phase.phase_name,
                value: [new Date(phase.start).getTime(), new Date(phase.end).getTime()],
                itemStyle: {
                    color: '#69B578'
                }
            };
        });

        var option = {
            tooltip: {
                trigger: 'item',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function (params) {
                    var start = new Date(params.value[0]).toISOString().split('T')[0];
                    var end = new Date(params.value[1]).toISOString().split('T')[0];
                    return params.name + '<br>' + 'Start: ' + start + '<br>' + 'End: ' + end;
                }
            },
            xAxis: {
                type: 'time',
                axisLabel: {
                    formatter: function (value) {
                        var date = new Date(value);
                        return date.getDate() + '/' + (date.getMonth() + 1);
                    }
                }
            },
            yAxis: {
                type: 'category',
                data: [
                    'RE Submit Lahan', 'BoD Validation', 'Negosiasi', 'Validasi Lahan', 'LOA CD',
                    'Validasi Data', 'Draft Sewa', 'TTD Sewa', 'DED', 'Permit', 'QS', 'Tender',
                    'SPK', 'Kick Off Meeting', 'Konstruksi', 'ST Equipment', 'ST Kontraktor',
                    'RTO', 'GO'
                ]
            },
            series: [
                {
                    name: 'Phases',
                    type: 'bar',
                    barWidth: 20,
                    data: chartData
                },
                {
                    name: 'Phase Lines',
                    type: 'line',
                    data: chartData,
                    itemStyle: {
                        color: '#69B578'
                    },
                    lineStyle: {
                        width: 2,
                        type: 'solid'
                    },
                    markLine: {
                        data: chartData.map(function(phase) {
                            return {
                                name: phase.name,
                                xAxis: phase.value[0],
                                yAxis: phase.name
                            };
                        })
                    }
                }
            ]
        };
        
        // Render chart
        phaseChart.setOption(option);

        // Resize chart on window resize
        window.addEventListener("resize", function () {
            setTimeout(function () {
                phaseChart.resize();
            }, 500);
        });
    });
</script>
</body>

</html>