<?php

// Set timezone to Jakarta
date_default_timezone_set('Asia/Jakarta');

// Include PHPMailer library files manually
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.8.1/src/Exception.php';
require 'PHPMailer-6.8.1/src/PHPMailer.php';
require 'PHPMailer-6.8.1/src/SMTP.php';


include "koneksi.php";

// Fungsi untuk menampilkan submenu sesuai dengan level pengguna
function displaySubMenu($submenu_name, $user_level) {
    // Tentukan daftar submenu berdasarkan level pengguna
    $submenus = array(
        "Legal Dept" => array("Doc Validation from RE", "Chacklist Validasi Data", "Drafting Akta Sewa"),
        "RE Dept" => array("Land Sourcing", "Approval by owner", "Document Confirmation", "Validation Land to Legal", "LoA & CD", "Validasi Data to Legal"),
        "Others" => array("Not Found", "User Profile", "Blank Page")
    );

    // Jika level pengguna adalah admin, tampilkan semua submenu
    if ($user_level === "Admin") {
        return $submenus[$submenu_name];
    } elseif (array_key_exists($submenu_name, $submenus)) {
        // Jika submenu tersedia untuk level pengguna yang sesuai, tampilkan submenu tersebut
        return $submenus[$submenu_name];
    } else {
        // Jika tidak ada submenu yang sesuai, kembalikan array kosong
        return array();
    }
}

$current_page = basename($_SERVER['REQUEST_URI'], ".php");

function getCountByStatus($conn, $table, $statusField, $statusValues) {
    // Construct a dynamic list of placeholders for the SQL query
    $placeholders = implode(',', array_fill(0, count($statusValues), '?'));
    
    // Prepare the SQL query to handle multiple status values
    $sql = "SELECT COUNT(*) AS count FROM $table WHERE $statusField IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // Bind the parameters dynamically
    $stmt->bind_param(str_repeat('s', count($statusValues)), ...$statusValues);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'];
}


// Set status value for different queries
$statusValues = ["In Process", "In Revision", "In Design Revision", "", "Yes", "Pending In IT", "Pending In Procurement", "Pending In HRGA"];

// Khusus untuk countissueit, hanya menghitung status "Pending In IT"
$countissueitStatus = ["Pending In IT"];
$countissueprocurStatus = ["Pending In Procurement"];
$countissuehrgaStatus = ["Pending In HRGA"];


$statusQueries = [
    //re
    'countland' => ['table' => 'land', 'field' => 'status_approvre'],
    'countvlre' => ['table' => 're', 'field' => 'status_vl'],
    'countloacd' => ['table' => 'dokumen_loacd', 'field' => 'status_approvloacd'],
    'countvdre' => ['table' => 'dokumen_loacd', 'field' => 'status_approvlegalvd'],
    'countfinalpsm' => ['table' => 'draft', 'field' => 'confirm_re'],

    //bod
    'counthrowner' => ['table' => 're', 'field' => 'status_approvowner'],
    'counthrbod' => ['table' => 'draft', 'field' => 'confirm_bod'],
    'counthrgo' => ['table' => 'resto', 'field' => 'status_gostore'],

    //legal
    'countvllegal' => ['table' => 're', 'field' => 'status_vl'],
    'countvdlegal' => ['table' => 'dokumen_loacd', 'field' => 'status_approvlegalvd'],
    'countpsmlegal' => ['table' => 'draft', 'field' => 'confirm_nego'],
    'countkondisilahan' => ['table' => 'draft', 'field' => 'status_kondisilahan'],
    'countdesignlegal' => ['table' => 'sdg_desain', 'field' => 'status_obslegal'],
    'countpermitlegal' => ['table' => 'sdg_desain', 'field' => 'submit_legal'],
    'countmouparkir' => ['table' => 'socdate_legal', 'field' => 'status_legal'],
    'countrestoname' => ['table' => 'resto', 'field' => 'status_land'],

    //negosiator
    'countnego' => ['table' => 're', 'field' => 'status_approvnego'],

    //sdg-design
    'countsurveylayout' => ['table' => 'sdg_desain', 'field' => 'status_obssdg'],
    'countwodesign' => ['table' => 'sdg_desain', 'field' => 'submit_wo'],
    'counturugan' => ['table' => 'sdg_desain', 'field' => 'confirm_sdgurugan'],
    'countdesign' => ['table' => 'sdg_desain', 'field' => 'confirm_sdgdesain'],

    //sdg-qs
    'countraburugan' => ['table' => 'sdg_rab', 'field' => 'confirm_qsurugan'],
    'countrabdesign' => ['table' => 'sdg_rab', 'field' => 'confirm_sdgqs'],
    'countrabjobadd' => ['table' => 'jobadd', 'field' => 'status_rabjobadd'],

    //procurement
    'countspkdesign' => ['table' => 'sdg_desain', 'field' => 'status_spkwo'],
    'countspkurugan' => ['table' => 'procurement', 'field' => 'status_procururugan'],
    'counttenderurugan' => ['table' => 'procurement', 'field' => 'status_tenderurugan'],
    'countspkcons' => ['table' => 'procurement', 'field' => 'status_approvprocurement'],
    'counttendercons' => ['table' => 'procurement', 'field' => 'status_tender'],
    'countspkfa' => ['table' => 'socdate_sdg', 'field' => 'status_procurspkwofa'],
    'countspkipal' => ['table' => 'socdate_sdg', 'field' => 'status_spkwoipal'],
    'countspkeqp' => ['table' => 'equipment', 'field' => 'status_eqpdevprocur'],
    'countjobadd' => ['table' => 'jobadd', 'field' => 'status_jobadd'],
    'countfinalspkcons' => ['table' => 'procurement', 'field' => 'status_finalspk'],
    'countissueprocur' => ['table' => 'utensil', 'field' => 'status_utensil', 'statusValues' => $countissueprocurStatus],

    //sdg-pk
    'countkom' => ['table' => 'resto', 'field' => 'status_kom'],
    'countcons' => ['table' => 'sdg_pk', 'field' => 'status_consact'],
    'countmepsa' => ['table' => 'socdate_sdg', 'field' => 'status_sdgsumber'],
    'countmeplistrik' => ['table' => 'socdate_sdg', 'field' => 'status_sdglistrik'],
    'countmepipal' => ['table' => 'socdate_sdg', 'field' => 'status_sdgipal'],
    'countstkons' => ['table' => 'resto', 'field' => 'status_stkonstruksi'],
    'countissue' => ['table' => 'issue', 'field' => 'status_defect'],
    'countwojobadd' => ['table' => 'jobadd', 'field' => 'status_wojobadd'],

    //equipment
    'counteqp' => ['table' => 'equipment', 'field' => 'status_steqp'],
    'countwoeqp' => ['table' => 'equipment', 'field' => 'status_woeqp'],
    'counteqpdev' => ['table' => 'equipment', 'field' => 'status_eqpdev'],
    'counteqpsite' => ['table' => 'equipment', 'field' => 'status_eqpsite'],

    //hr
    'counthrqc' => ['table' => 'socdate_hr', 'field' => 'status_tm'],
    'counthrfl' => ['table' => 'socdate_hr', 'field' => 'status_fl'],
    'counthrff1' => ['table' => 'socdate_hr', 'field' => 'status_ff1'],
    'counthrff2' => ['table' => 'socdate_hr', 'field' => 'status_ff2'],
    'counthrff3' => ['table' => 'socdate_hr', 'field' => 'status_ff3'],
    'counthrhot' => ['table' => 'socdate_hr', 'field' => 'status_hot'],
    'countissuehrga' => ['table' => 'utensil', 'field' => 'status_utensil', 'statusValues' => $countissuehrgaStatus],

    //academy
    'countacaqc' => ['table' => 'socdate_hraca', 'field' => 'status_tmaca'],
    'countacafl' => ['table' => 'socdate_hraca', 'field' => 'status_flaca'],
    'countkpt1' => ['table' => 'socdate_academy', 'field' => 'status_kpt1'],
    'countkpt2' => ['table' => 'socdate_academy', 'field' => 'status_kpt2'],
    'countkpt3' => ['table' => 'socdate_academy', 'field' => 'status_kpt3'],

    //scm
    'countutensil' => ['table' => 'socdate_scm', 'field' => 'status_scm'],
    'countscmipal' => ['table' => 'socdate_sdg', 'field' => 'status_scmipal'],

    //it
    'countit' => ['table' => 'socdate_it', 'field' => 'status_it'],
    'countitconfig' => ['table' => 'socdate_it', 'field' => 'status_itconfig'],
    'countissueit' => ['table' => 'utensil', 'field' => 'status_utensil', 'statusValues' => $countissueitStatus],

    //marketing
    'countmarketing' => ['table' => 'socdate_marketing', 'field' => 'status_marketing'],

    //taf
    'counttafkode' => ['table' => 'dokumen_loacd', 'field' => 'status_tafkode'],
    'counttafpsm' => ['table' => 'draft', 'field' => 'confirm_fatpsm'],
    'counttafrab' => ['table' => 'procurement', 'field' => 'status_spkfat'],
    'counttafqris' => ['table' => 'socdate_fat', 'field' => 'status_fat'],
    'counttafpay' => ['table' => 'socdate_sdg', 'field' => 'status_tafpay'],
    'counttafpaylistrik' => ['table' => 'socdate_sdg', 'field' => 'status_tafpaylistrik'],

    //ir
    'countir' => ['table' => 'socdate_ir', 'field' => 'status_ir'],


    // Tambahkan query lainnya di sini
];
$results = [];

// Hitung jumlah untuk setiap status field
foreach ($statusQueries as $key => $query) {
    $statusForQuery = isset($query['statusValues']) ? $query['statusValues'] : $statusValues;
    $results[$key] = getCountByStatus($conn, $query['table'], $query['field'], $statusForQuery);
}

// Tutup koneksi
$conn->close();

// Variabel hasil
//re
$countland = isset($results['countland']) ? $results['countland'] : 0;
$countvlre = isset($results['countvlre']) ? $results['countvlre'] : 0;
$countloacd = isset($results['countloacd']) ? $results['countloacd'] : 0;
$countvdre = isset($results['countvdre']) ? $results['countvdre'] : 0;
$countfinalpsm = isset($results['countfinalpsm']) ? $results['countfinalpsm'] : 0;
//owner bod
$counthrowner = isset($results['counthrowner']) ? $results['counthrowner'] : 0;
$counthrbod = isset($results['counthrbod']) ? $results['counthrbod'] : 0;
$counthrgo = isset($results['counthrgo']) ? $results['counthrgo'] : 0;
//legal
$countvllegal = isset($results['countvllegal']) ? $results['countvllegal'] : 0;
$countvdlegal = isset($results['countvdlegal']) ? $results['countvdlegal'] : 0;
$countpsmlegal = isset($results['countpsmlegal']) ? $results['countpsmlegal'] : 0;
$countkondisilahan = isset($results['countkondisilahan']) ? $results['countkondisilahan'] : 0;
$countdesignlegal = isset($results['countdesignlegal']) ? $results['countdesignlegal'] : 0;
$countpermitlegal = isset($results['countpermitlegal']) ? $results['countpermitlegal'] : 0;
$countmouparkir = isset($results['countmouparkir']) ? $results['countmouparkir'] : 0;
$countrestoname = isset($results['countrestoname']) ? $results['countrestoname'] : 0;
//negotiator
$countnego = isset($results['countnego']) ? $results['countnego'] : 0;
//sdg-design
$countsurveylayout = isset($results['countsurveylayout']) ? $results['countsurveylayout'] : 0;
$countwodesign = isset($results['countwodesign']) ? $results['countwodesign'] : 0;
$counturugan = isset($results['counturugan']) ? $results['counturugan'] : 0;
$countdesign = isset($results['countdesign']) ? $results['countdesign'] : 0;
//sdg-rab
$countraburugan = isset($results['countraburugan']) ? $results['countraburugan'] : 0;
$countrabdesign = isset($results['countrabdesign']) ? $results['countrabdesign'] : 0;
$countrabjobadd = isset($results['countrabjobadd']) ? $results['countrabjobadd'] : 0;
//procurement
$countspkdesign = isset($results['countspkdesign']) ? $results['countspkdesign'] : 0;
$countspkurugan = isset($results['countspkurugan']) ? $results['countspkurugan'] : 0;
$counttenderurugan = isset($results['counttenderurugan']) ? $results['counttenderurugan'] : 0;
$countspkcons = isset($results['countspkcons']) ? $results['countspkcons'] : 0;
$counttendercons = isset($results['counttendercons']) ? $results['counttendercons'] : 0;
$countspkfa = isset($results['countspkfa']) ? $results['countspkfa'] : 0;
$countspkipal = isset($results['countspkipal']) ? $results['countspkipal'] : 0;
$countspkeqp = isset($results['countspkeqp']) ? $results['countspkeqp'] : 0;
$countjobadd = isset($results['countjobadd']) ? $results['countjobadd'] : 0;
$countfinalspkcons = isset($results['countfinalspkcons']) ? $results['countfinalspkcons'] : 0;
$countissueprocur = isset($results['countissueprocur']) ? $results['countissueprocur'] : 0;
//sdg-pk
$countkom = isset($results['countkom']) ? $results['countkom'] : 0;
$countcons = isset($results['countcons']) ? $results['countcons'] : 0;
$countmepsa = isset($results['countmepsa']) ? $results['countmepsa'] : 0;
$countmeplistrik = isset($results['countmeplistrik']) ? $results['countmeplistrik'] : 0;
$countmepipal = isset($results['countmepipal']) ? $results['countmepipal'] : 0;
$countstkons = isset($results['countstkons']) ? $results['countstkons'] : 0;
$countissue = isset($results['countissue']) ? $results['countissue'] : 0;
$countwojobadd = isset($results['countwojobadd']) ? $results['countwojobadd'] : 0;
//equipment
$counteqp = isset($results['counteqp']) ? $results['counteqp'] : 0;
$countwoeqp = isset($results['countwoeqp']) ? $results['countwoeqp'] : 0;
$counteqpdev = isset($results['counteqpdev']) ? $results['counteqpdev'] : 0;
$counteqpsite = isset($results['counteqpsite']) ? $results['counteqpsite'] : 0;
//hr
$counthrff1 = isset($results['counthrff1']) ? $results['counthrff1'] : 0;
$counthrff2 = isset($results['counthrff2']) ? $results['counthrff2'] : 0;
$counthrff3 = isset($results['counthrff3']) ? $results['counthrff3'] : 0;
$counthrfl = isset($results['counthrfl']) ? $results['counthrfl'] : 0;
$counthrqc = isset($results['counthrqc']) ? $results['counthrqc'] : 0;
$counthrhot = isset($results['counthrhot']) ? $results['counthrhot'] : 0;
$countissuehrga = isset($results['countissuehrga']) ? $results['countissuehrga'] : 0;
//academy
$countkpt1 = isset($results['countkpt1']) ? $results['countkpt1'] : 0;
$countkpt2 = isset($results['countkpt2']) ? $results['countkpt2'] : 0;
$countkpt3 = isset($results['countkpt3']) ? $results['countkpt3'] : 0;
$countacafl = isset($results['countacafl']) ? $results['countacafl'] : 0;
$countacaqc = isset($results['countacaqc']) ? $results['countacaqc'] : 0;
//scm
$countutensil = isset($results['countutensil']) ? $results['countutensil'] : 0;
$countscmipal = isset($results['countscmipal']) ? $results['countscmipal'] : 0;
//it
$countit = isset($results['countit']) ? $results['countit'] : 0;
$countitconfig = isset($results['countitconfig']) ? $results['countitconfig'] : 0;
$countissueit = isset($results['countissueit']) ? $results['countissueit'] : 0;
//marketing
$countmarketing = isset($results['countmarketing']) ? $results['countmarketing'] : 0;
//taf
$counttafkode = isset($results['counttafkode']) ? $results['counttafkode'] : 0;
$counttafpsm = isset($results['counttafpsm']) ? $results['counttafpsm'] : 0;
$counttafrab = isset($results['counttafrab']) ? $results['counttafrab'] : 0;
$counttafqris = isset($results['counttafqris']) ? $results['counttafqris'] : 0;
$counttafpay = isset($results['counttafpay']) ? $results['counttafpay'] : 0;
$counttafpaylistrik = isset($results['counttafpaylistrik']) ? $results['counttafpaylistrik'] : 0;
//ir
$countir = isset($results['countir']) ? $results['countir'] : 0;

//re
$totalRe = 0;
if (isset($results['countland'])) {
    $totalRe += $results['countland'];
}
if (isset($results['countvlre'])) {
    $totalRe += $results['countvlre'];
}
if (isset($results['countloacd'])) {
    $totalRe += $results['countloacd'];
}
if (isset($results['countvdre'])) {
    $totalRe += $results['countvdre'];
}
if (isset($results['countfinalpsm'])) {
    $totalRe += $results['countfinalpsm'];
}

//legal
$totalLegal = 0;
if (isset($results['countvllegal'])) {
    $totalLegal += $results['countvllegal'];
}
if (isset($results['countvdlegal'])) {
    $totalLegal += $results['countvdlegal'];
}
if (isset($results['countpsmlegal'])) {
    $totalLegal += $results['countpsmlegal'];
}
if (isset($results['countkondisilahan'])) {
    $totalLegal += $results['countkondisilahan'];
}
if (isset($results['countdesignlegal'])) {
    $totalLegal += $results['countdesignlegal'];
}
if (isset($results['countpermitlegal'])) {
    $totalLegal += $results['countpermitlegal'];
}
if (isset($results['countmouparkir'])) {
    $totalLegal += $results['countmouparkir'];
}
if (isset($results['countrestoname'])) {
    $totalLegal += $results['countrestoname'];
}

//bod
$totalBod = 0;
if (isset($results['counthrowner'])) {
    $totalBod += $results['counthrowner'];
}
if (isset($results['counthrbod'])) {
    $totalBod += $results['counthrbod'];
}
if (isset($results['counthrgo'])) {
    $totalBod += $results['counthrgo'];
}

//negotiator
$totalNegotiator = 0;
if (isset($results['countnego'])) {
    $totalNegotiator += $results['countnego'];
}

//sdg-design
$totalSdgDesign = 0;
if (isset($results['countsurveylayout'])) {
    $totalSdgDesign += $results['countsurveylayout'];
}
if (isset($results['countwodesign'])) {
    $totalSdgDesign += $results['countwodesign'];
}
if (isset($results['counturugan'])) {
    $totalSdgDesign += $results['counturugan'];
}
if (isset($results['countdesign'])) {
    $totalSdgDesign += $results['countdesign'];
}

//sdg-qs
$totalSdgQs = 0;
if (isset($results['countraburugan'])) {
    $totalSdgQs += $results['countraburugan'];
}
if (isset($results['countrabdesign'])) {
    $totalSdgQs += $results['countrabdesign'];
}
if (isset($results['countrabjobadd'])) {
    $totalSdgQs += $results['countrabjobadd'];
}

//procurement
$totalProcur = 0;
if (isset($results['countspkdesign'])) {
    $totalProcur += $results['countspkdesign'];
}
if (isset($results['countspkurugan'])) {
    $totalProcur += $results['countspkurugan'];
}
if (isset($results['counttenderurugan'])) {
    $totalProcur += $results['counttenderurugan'];
}
if (isset($results['countspkcons'])) {
    $totalProcur += $results['countspkcons'];
}
if (isset($results['counttendercons'])) {
    $totalProcur += $results['counttendercons'];
}
if (isset($results['countspkfa'])) {
    $totalProcur += $results['countspkfa'];
}
if (isset($results['countspkipal'])) {
    $totalProcur += $results['countspkipal'];
}
if (isset($results['countspkeqp'])) {
    $totalProcur += $results['countspkeqp'];
}
if (isset($results['countjobadd'])) {
    $totalProcur += $results['countjobadd'];
}
if (isset($results['countfinalspkcons'])) {
    $totalProcur += $results['countfinalspkcons'];
}
if (isset($results['countissueprocur'])) {
    $totalProcur += $results['countissueprocur'];
}

//sdg-pk
$totalSdgProject = 0;
if (isset($results['countkom'])) {
    $totalSdgProject += $results['countkom'];
}
if (isset($results['countcons'])) {
    $totalSdgProject += $results['countcons'];
}
if (isset($results['countmepsa'])) {
    $totalSdgProject += $results['countmepsa'];
}
if (isset($results['countmeplistrik'])) {
    $totalSdgProject += $results['countmeplistrik'];
}
if (isset($results['countmepipal'])) {
    $totalSdgProject += $results['countmepipal'];
}
if (isset($results['countstkons'])) {
    $totalSdgProject += $results['countstkons'];
}
if (isset($results['countissue'])) {
    $totalSdgProject += $results['countissue'];
}
if (isset($results['countwojobadd'])) {
    $totalSdgProject += $results['countwojobadd'];
}

//equipment
$totalSdgEqp = 0;
if (isset($results['counteqp'])) {
    $totalSdgEqp += $results['counteqp'];
}
if (isset($results['countwoeqp'])) {
    $totalSdgEqp += $results['countwoeqp'];
}
if (isset($results['counteqpdev'])) {
    $totalSdgEqp += $results['counteqpdev'];
}
if (isset($results['counteqpsite'])) {
    $totalSdgEqp += $results['counteqpsite'];
}

//hr
$totalHr = 0;
if (isset($results['counthrff1'])) {
    $totalHr += $results['counthrff1'];
}
if (isset($results['counthrff2'])) {
    $totalHr += $results['counthrff2'];
}
if (isset($results['counthrff3'])) {
    $totalHr += $results['counthrff3'];
}
if (isset($results['counthrfl'])) {
    $totalHr += $results['counthrfl'];
}
if (isset($results['counthrqc'])) {
    $totalHr += $results['counthrqc'];
}
if (isset($results['counthrhot'])) {
    $totalHr += $results['counthrhot'];
}
if (isset($results['countissuehrga'])) {
    $totalHr += $results['countissuehrga'];
}
if (isset($results['countir'])) {
    $totalHr += $results['countir'];
}

//academy
$totalAcademy = 0;
if (isset($results['countkpt1'])) {
    $totalAcademy += $results['countkpt1'];
}
if (isset($results['countkpt2'])) {
    $totalAcademy += $results['countkpt2'];
}
if (isset($results['countkpt3'])) {
    $totalAcademy += $results['countkpt3'];
}
if (isset($results['countacafl'])) {
    $totalAcademy += $results['countacafl'];
}
if (isset($results['countacaqc'])) {
    $totalAcademy += $results['countacaqc'];
}

//academy
$totalScm = 0;
if (isset($results['countutensil'])) {
    $totalScm += $results['countutensil'];
}
if (isset($results['countscmipal'])) {
    $totalScm += $results['countscmipal'];
}

//it
$totalIt = 0;
if (isset($results['countit'])) {
    $totalIt += $results['countit'];
}
if (isset($results['countitconfig'])) {
    $totalIt += $results['countitconfig'];
}
if (isset($results['countissueit'])) {
    $totalIt += $results['countissueit'];
}

//marketing
$totalMarketing = 0;
if (isset($results['countmarketing'])) {
    $totalMarketing += $results['countmarketing'];
}


//taf
$totalTaf = 0;
if (isset($results['counttafkode'])) {
    $totalTaf += $results['counttafkode'];
}
if (isset($results['counttafpsm'])) {
    $totalTaf += $results['counttafpsm'];
}
if (isset($results['counttafrab'])) {
    $totalTaf += $results['counttafrab'];
}
if (isset($results['counttafqris'])) {
    $totalTaf += $results['counttafqris'];
}
if (isset($results['counttafpay'])) {
    $totalTaf += $results['counttafpay'];
}
if (isset($results['counttafpaylistrik'])) {
    $totalTaf += $results['counttafpaylistrik'];
}

    // Data total tiket untuk masing-masing level
    $totalTickets = [
        'Legal' => $totalLegal,
        'Real Estate' => $totalRe,
        'BoD' => $totalBod,
        'Negotiator' => $totalNegotiator,
        'SDG-Design' => $totalSdgDesign,
        'SDG-QS' => $totalSdgQs,
        'Procurement' => $totalProcur,
        'SDG-Project' => $totalSdgProject,
        'SDG-Equipment' => $totalSdgEqp,
        'HR' => $totalHr,
        'Academy' => $totalAcademy,
        'IT' => $totalIt,
        'Marketing' => $totalMarketing,
        'TAF' => $totalTaf,
    ];

    // Query untuk email sesuai level
    $levels = array_keys($totalTickets);
    foreach ($levels as $level) {
        $query = "SELECT email FROM user WHERE level = '$level'";
        $result = mysqli_query($conn, $query);

        $toEmails = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (!empty($row['email'])) {
                    $toEmails[] = $row['email'];
                }
            }
        }

        if (!empty($toEmails)) {
            try {
                // SMTP configuration
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = 'miegacoan.co.id';
                $mail->Port = 465;
                $mail->Username = 'resto-soc@miegacoan.co.id';
                $mail->Password = '9)5X]*hjB4sh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->setFrom('resto-soc@miegacoan.co.id', 'Pesta Pora Abadi');

                foreach ($toEmails as $toEmail) {
                    $mail->addAddress($toEmail);
                }
                $imagePath = 'assets/images/logo-email.png';
                $mail->addEmbeddedImage($imagePath, 'embedded_image', 'logo-email.png', 'base64', 'image/png');

                // Update email body based on level
                $total = $totalTickets[$level];
                $mail->Body    = '
                <div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
                    <div style="background-color: #f7f7f7; border-radius: 8px; padding: 0; margin: 0; text-align: center;">
                        <img src="cid:embedded_image" alt="Header Image" style="display: block; width: 50%; height: auto; margin: 0 auto;">
                        <div style="padding: 20px; background-color: #f7f7f7; border-radius: 8px;">
                            <h2 style="font-size: 20px; color: #5cb85c; margin-bottom: 10px;">Dear ' . $level . ' Team,</h2>
                            <p>You have a total of <strong>' . $total . '</strong> active tickets in the Resto SOC system.</p>
                            <p>Please log in to the SOC application to review the details.</p>
                            <p>Thank you for your prompt attention to this matter.</p>
                            <p>Best regards,</p>
                        </div>
                    </div>
                </div>';

                // Update AltBody for plain text
                $mail->AltBody = 'Dear ' . $level . ' Team,'
                    . 'You have a total of ' . $total . ' active tickets in the Resto SOC system.'
                    . 'Please log in to the SOC application to review the details.'
                    . 'Thank you for your prompt attention to this matter.'
                    . 'Best regards,';

                // Send email
                if ($mail->send()) {
                    echo "Email sent successfully to $level!<br>";
                } else {
                    echo "Failed to send email to $level. Error: {$mail->ErrorInfo}<br>";
                }

            } catch (Exception $e) {
                echo "Message could not be sent to $level. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No email found for the $level users.";
        }
    }

$conn->close();
?>