<?php

session_start();


// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login
    header("location: /index.php?pesan=belum_login");
    exit;
}

include '../koneksi.php';
$user_level = $_SESSION['level']; // Ganti dengan level pengguna yang sesuai

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
?>
<style>
    .nav-item.active a {
    background-color: #f0f0f0; /* Warna latar belakang untuk item aktif */
    color: #333; /* Warna teks untuk item aktif */
}

.nav-item.active a .nav-icon {
    color: #333; /* Warna ikon untuk item aktif */
}
.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-success {
    background-color: #28a745; /* Hijau */
    color: #fff;
}
</style>
       <div class="side-content-wrap">
            <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
                <ul class="navigation-left">
                    <!-- Dashboard -->
                    <?php if (isset($_SESSION['level'])) : ?>
                    <li class="nav-item <?php echo (strpos($current_page, 'index') !== false ||$current_page == 'datatables-update-store' || $current_page == 'datatables-rto' || $current_page == 'datatables-mom'  || $current_page == 'datatables-soc-date-act' || $current_page == 'datatables-soc-date-hold' || strpos($current_page, 'datatables-tracking-calendar') !== false
                    || $current_page == 'datatables-soc-date'|| strpos($current_page, 'datatables-update-store-detail') !== false) ? 'active' : ''; ?>" data-item="dashboard">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Bar-Chart"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>
                    <!-- RE Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Real-Estate") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-land-sourcing' || $current_page == 'datatables-potensi-masalah-re' || $current_page == 'datatables-validasi-lahan' || $current_page == 'datatables-draft-sewa-legal'  || $current_page == 'datatables-loa-cd' || $current_page == 'datatables-validasi-data'|| $current_page == 'datatables-bussiness-planning'
                    || $current_page == 'datatables-submit-to-owner') ? 'active' : ''; ?>" data-item="uikits">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Library"></i>
                            <span class="nav-text">RE</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>
                    <!-- Owner Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "BoD") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-approval-owner' || $current_page == 'datatables-doc-confirm' || $current_page == 'datatables-gostore') ? 'active' : ''; ?>" data-item="owner">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Tag-2"></i>
                            <span class="nav-text">BoD</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Legal Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Legal") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-validasi-lahan-legal' || $current_page == 'datatables-potensi-masalah-legal' || $current_page == 'datatables-checkval-legal'|| $current_page == 'datatables-sign-psm-legal'|| $current_page == 'datatables-sp-submit-legal'|| $current_page == 'datatables-release-doc-legal' || $current_page == 'datatables-design-legal' || $current_page == 'datatables-valdoc-legal'|| $current_page == 'datatables-validasi-sp' || $current_page == 'datatables-obstacle-legal'|| $current_page == 'datatables-wovl'|| $current_page == 'datatables-wovd'|| $current_page == 'datatables-spk-legal'|| $current_page == 'datatables-mou-parkir'  || $current_page == 'datatables-resto-name' || $current_page == 'datatables-kondisi-lahan') ? 'active' : ''; ?>" data-item="extrakits">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Suitcase"></i>
                            <span class="nav-text">Legal</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Negosiator Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Negotiator") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-doc-confirm-negosiator' || $current_page == 'datatables-dealing-draft-negosiator'|| $current_page == 'datatables-validasi-negosiator'|| $current_page == 'datatables-gostore-nego') ? 'active' : ''; ?>" data-item="apps">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Computer-Secure"></i>
                            <span class="nav-text">Negotiator</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SDG Design Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SDG-Design") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-checkval-negosiator' || $current_page == 'datatables-design'  || $current_page == 'datatables-submit-wo' || $current_page == 'datatables-urugan'
                    || $current_page == 'datatables-land-survey' || $current_page == 'datatables-formval-release-design' || 
                    $current_page == 'datatables-obstacle-sdg') ? 'active' : ''; ?>" data-item="forms">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-File-Clipboard-File--Text"></i>
                            <span class="nav-text">SDG Design</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SDG QS Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SDG-QS") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-data-picture' || $current_page == 'datatables-potensi-masalah-sdg-qs' || $current_page == 'datatables-rab' || $current_page == 'datatables-rab-urugan' || $current_page == 'datatables-rab-tambahkurang'|| $current_page == 'datatables-validation-rab') ? 'active' : ''; ?>" data-item="sessions">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="nav-text">SDG QS</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Procurement Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Procurement") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-checkval-rab-from-sdg' || $current_page == 'datatables-checkval-wo-from-sdg' || $current_page == 'datatables-spkfa-procurement' || $current_page == 'datatables-checkval-rab-urugan' || $current_page == 'datatables-tender-urugan' ||  $current_page == 'datatables-procurement' || $current_page == 'datatables-vendor' || $current_page == 'datatables-spkipal-procurement' || $current_page == 'datatables-tambahkurang'||$current_page == 'datatables-final-spkcons'||
                    $current_page == 'datatables-tender'|| $current_page == 'datatables-spk-sdgpk'|| $current_page == 'datatables-eqpdev-procur'  || $current_page == 'datatables-issue-urgent-procur' || strpos($current_page, 'datatables-data-procurutensil')!== false) ? 'active' : ''; ?>" data-item="others">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Double-Tap"></i>
                            <span class="nav-text">Procurement</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SDG PK Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SDG-Project") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-kom-sdgpk' || $current_page == 'datatables-potensi-masalah-sdg-project' || $current_page == 'datatables-monitoring-op'|| $current_page == 'datatables-construction-act-vendor'|| $current_page == 'datatables-kom-schedule'|| strpos($current_page, 'sdgpk-rto-edit-form') !== false || strpos($current_page, 'sdgpk-rto-ipal-edit-form') !== false || strpos($current_page, 'sdgpk-rto-listrik-edit-form') !== false || $current_page == 'datatables-wo-tambahkurang'||
                    $current_page == 'datatables-st-konstruksi'|| $current_page == 'datatables-sdgpk-rto' || $current_page == 'datatables-sdgpk-rto-listrik' || $current_page == 'datatables-sdgpk-rto-ipal' || $current_page == 'datatables-sdgpk-issue') ? 'active' : ''; ?>" data-item="datatables">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-File-Horizontal-Text"></i>
                            <span class="nav-text">SDG PK</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SDG EQP Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SDG-Equipment") : ?>
                    <li class="nav-item <?php echo ( $current_page == 'datatables-st-eqp'  || $current_page == 'datatables-sdgpk-eqp-rto'  || $current_page == 'datatables-eqp-delivery' || $current_page == 'datatables-eqp-site' || $current_page == 'datatables-wo-eqp'  || strpos($current_page, 'eqp-edit-form') !== false  || strpos($current_page, 'eqpdev-edit-form') !== false  || strpos($current_page, 'eqpsite-edit-form') !== false
                     || strpos($current_page, 'eqp-wo-edit-form') !== false) ? 'active' : ''; ?>" data-item="eqp">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Bell1"></i>
                            <span class="nav-text">SDG EQP</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Operation Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Operation") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-soc-date-achieve' || $current_page == 'datatables-update-info' || $current_page == 'datatables-validation-monitoring' || $current_page == 'datatables-soc' || $current_page == 'datatables-soc-summary' || $current_page == 'datatables-mom-pmo' ) ? 'active' : ''; ?>" data-item="demos">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Safe-Box1"></i>
                            <span class="nav-text">PMO</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- HR Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "HR") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-hr-qs' || $current_page == 'datatables-hr-fl' || $current_page == 'datatables-hr-fulfillment' || $current_page == 'datatables-data-ff2' || $current_page == 'datatables-data-ff3' || strpos($current_page, 'datatables-data-ff1')!== false  || strpos($current_page, 'datatables-data-ff2')!== false  || strpos($current_page, 'datatables-data-ff3')!== false || $current_page == 'datatables-hr-fulfillment-2' || $current_page == 'datatables-hr-fulfillment-3' 
                    || $current_page == 'datatables-hr-hot'  || strpos($current_page, 'datatables-data-qs')!== false  || strpos($current_page, 'datatables-data-fl')!== false || $current_page == 'datatables-ir' || strpos($current_page, 'ir-edit-form') !== false  || $current_page == 'datatables-issue-urgent-hrga' || strpos($current_page, 'datatables-data-hrgautensil')!== false) ? 'active' : ''; ?>" data-item="hr">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                            <span class="nav-text">HR</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- HR Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Academy") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-hr-kpt'|| $current_page == 'datatables-aca-qs' || $current_page == 'datatables-aca-fl'|| strpos($current_page, 'datatables-data-kpt1')!== false || strpos($current_page, 'datatables-data-kpt2')!== false || strpos($current_page, 'datatables-data-kpt3')!== false || strpos($current_page, 'datatables-data-acaqs')!== false || strpos($current_page, 'datatables-data-acafl')!== false || $current_page == 'datatables-hr-kpt-2' || $current_page == 'datatables-hr-kpt-3') ? 'active' : ''; ?>" data-item="academy">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Loading-3"></i>
                            <span class="nav-text">Academy</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SCM Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SCM") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-scm' || $current_page == 'datatables-scm-ipal' || strpos($current_page, 'datatables-data-scmutensil')!== false ) ? 'active' : ''; ?>" data-item="scm">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Receipt-4"></i>
                            <span class="nav-text">SCM</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- IT Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "IT") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-it' || $current_page == 'datatables-it-config' || $current_page == 'datatables-issue-urgent-it' || strpos($current_page, 'datatables-data-itutensil')!== false) ? 'active' : ''; ?>" data-item="it">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Width-Window"></i>
                            <span class="nav-text">IT</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Marketing Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Marketing") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-marketing') ? 'active' : ''; ?>" data-item="marketing">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Medal-2"></i>
                            <span class="nav-text">Marketing</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- FAT Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "TAF") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-fat' || $current_page == 'datatables-review-rab-urugan' || $current_page == 'datatables-tender-fat' || $current_page == 'datatables-spk-fat' || $current_page == 'datatables-sign-psm-fat' || $current_page == 'datatables-tafpay-listrikair' || $current_page == 'datatables-tafpay-listrik' || $current_page =='datatables-review-wo-from-sdg' || $current_page == 'datatables-review-spkfa-procurement' || $current_page == 'datatables-review-rab-from-sdg' || $current_page == 'datatables-review-spkipal-procurement' || $current_page == 'datatables-review-spkeqp' || $current_page == 'datatables-kode-store-taf') ? 'active' : ''; ?>" data-item="fat">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Cursor-Click"></i>
                            <span class="nav-text">FAT</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- IR Head -->
                    <!-- <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "IR") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-ir') ? 'active' : ''; ?>" data-item="ir">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                            <span class="nav-text">IR</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?> -->
                </ul>
            </div>
            <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
                <i class="sidebar-close i-Close" (click)="toggelSidebar()"></i>
                <header>
                    
                </header>
                <!-- Submenu Dashboards -->
                <div class="submenu-area" data-parent="dashboard">
                    <header>
                        <h6><i class="nav-icon i-Bar-Chart"></i> Dashboards</h6>
                        <p>Dashboard Admin</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo $current_page == 'index' ? 'active' : ''; ?>">
                            <a href="../dashboard/index.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">Home</span>
                            </a>
                        </li>		
                        <li class="nav-item <?php echo $current_page == 'datatables-rto' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-rto.php">
                                <i class="nav-icon i-Clock-3"></i>
                                <span class="item-name mr-2">RTO</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mom' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-mom.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name mr-2">MOM</span>
                            </a>
                        </li>			
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">In Preparation Tracking</span>
                            </a>
                        </li>			
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-act' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-act.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">In Progress Tracking</span>
                            </a>
                        </li>		
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-hold' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-hold.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name mr-2">Hold Project Tracking</span>
                            </a>
                        </li>	
                        <li class="nav-item <?php echo $current_page == 'datatables-update-store' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-update-store.php">
                                <i class="nav-icon i-Over-Time"></i>
                                <span class="item-name mr-2">Update Per Store</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tracking-calendar' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-tracking-calendar.php">
                                <i class="nav-icon i-Loading-3"></i>
                                <span class="item-name mr-2">Calendar Tracking All Dept</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- RE -->
                <div class="submenu-area" data-parent="uikits">
                    <header>
                        <h6><i class="nav-icon i-Library"></i> Real Estate</h6>
                        <p>Real Estate Division</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo $current_page == 'datatables-bussiness-planning' ? 'active' : ''; ?>">
                            <a href="datatables-bussiness-planning.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">Bussiness Planning</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-land-sourcing' ? 'active' : ''; ?>">
                            <a href="datatables-land-sourcing.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">Land Sourcing</span>
                            <span class="badge <?php echo ($countland > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countland > 0 ? $countland : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-submit-to-owner' ? 'active' : ''; ?>">
                            <a href="datatables-submit-to-owner.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name mr-2">BoD Approval</span>
                            </a>
                        </li>					 -->
                        <li class="nav-item <?php echo $current_page == 'datatables-validasi-lahan' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-lahan.php">
                                <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                                <span class="item-name mr-2">Validation Land to Legal</span>
                            <span class="badge <?php echo ($countvlre > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countvlre > 0 ? $countvlre : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-loa-cd' ? 'active' : ''; ?>">
                            <a href="datatables-loa-cd.php">
                                <i class="nav-icon i-Cursor-Click"></i>
                                <span class="item-name mr-2">LoA & CD</span>
                            <span class="badge <?php echo ($countloacd > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countloacd > 0 ? $countloacd : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-validasi-data' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-data.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">Validasi Data to Legal</span>
                            <span class="badge <?php echo ($countvdre > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countvdre > 0 ? $countvdre : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-draft-sewa-legal' ? 'active' : ''; ?>">
                            <a href="datatables-draft-sewa-legal.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name mr-2">Final PSM & Table Sewa</span>
                            <span class="badge <?php echo ($countfinalpsm > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countfinalpsm > 0 ? $countfinalpsm : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-potensi-masalah-re' ? 'active' : ''; ?>">
                            <a href="datatables-potensi-masalah-re.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">List Potential Problems</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Owner -->
                <div class="submenu-area" data-parent="owner">
                    <header>
                        <h6><i class="nav-icon i-Tag-2"></i> BoD</h6>
                        <p>BoD Division</p>
                    </header>
                    <ul class="childNav">
						<li class="nav-item <?php echo $current_page == 'datatables-approval-owner' ? 'active' : ''; ?>">
                            <a href="datatables-approval-owner.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Bod Approval Land</span>
                            <span class="badge <?php echo ($counthrowner > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrowner > 0 ? $counthrowner : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-doc-confirm' ? 'active' : ''; ?>">
                            <a href="datatables-doc-confirm.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name mr-2">Approval Table Sewa & PSM</span>
                            <span class="badge <?php echo ($counthrbod > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrbod > 0 ? $counthrbod : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-gostore' ? 'active' : ''; ?>">
                            <a href="datatables-gostore.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Data GO Store</span>
                            <span class="badge <?php echo ($counthrgo > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrgo > 0 ? $counthrgo : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Legal -->
                <div class="submenu-area" data-parent="extrakits">
                    <header>
                        <h6><i class="nav-icon i-Suitcase"></i> Legal</h6>
                        <p>Legal Division</p>
                    </header>
                    <ul class="childNav">
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-validasi-lahan-legal' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-lahan-legal.php">
                                <i class="nav-icon i-Crop-2"></i>
                                <span class="item-name mr-2">Doc Approval from BoD</span>
                            </a>
                        </li>    -->
                        <li class="nav-item <?php echo $current_page == 'datatables-wovl' ? 'active' : ''; ?>">
                            <a href="datatables-wovl.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">Validate Land</span>
                            <span class="badge <?php echo ($countvllegal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countvllegal > 0 ? $countvllegal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-release-doc-legal' ? 'active' : ''; ?>">
                            <a href="datatables-release-doc-legal.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">Release Doc Legal Validation</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-wovd' ? 'active' : ''; ?>">
                            <a href="datatables-wovd.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name mr-2">Add WO VD Legal</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-legal' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-legal.php">
                                <i class="nav-icon i-Loading-3"></i>
                                <span class="item-name mr-2">Validasi Doc Legal (VD)</span>
                            <span class="badge <?php echo ($countvdlegal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countvdlegal > 0 ? $countvdlegal : '0'; ?>
                            </span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-sign-psm-legal' ? 'active' : ''; ?>">
                            <a href="datatables-sign-psm-legal.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Draft PSM Legal</span>
                            <span class="badge <?php echo ($countpsmlegal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countpsmlegal > 0 ? $countpsmlegal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-kondisi-lahan' ? 'active' : ''; ?>">
                            <a href="datatables-kondisi-lahan.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name mr-2">Pengondisian Lahan</span>
                            <span class="badge <?php echo ($countkondisilahan > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countkondisilahan > 0 ? $countkondisilahan : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-potensi-masalah-legal' ? 'active' : ''; ?>">
                            <a href="datatables-potensi-masalah-legal.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">List Potential Problems</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-design-legal' ? 'active' : ''; ?>">
                            <a href="datatables-design-legal.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name mr-2">Completion Obstacle from SDG Design</span>
                            <span class="badge <?php echo ($countdesignlegal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countdesignlegal > 0 ? $countdesignlegal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-valdoc-legal' ? 'active' : ''; ?>">
                            <a href="datatables-valdoc-legal.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">Validasi All Data</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-sp-submit-legal' ? 'active' : ''; ?>">
                            <a href="datatables-sp-submit-legal.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">Surat Permit & PBG Legal Submit</span>
                            <span class="badge <?php echo ($countpermitlegal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countpermitlegal > 0 ? $countpermitlegal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-validasi-sp' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-sp.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">Validation Permit & PBG Legal</span>
                            </a>
                        </li> -->
						<!-- <li class="nav-item <?php echo $current_page == 'datatables-obstacle-legal' ? 'active' : ''; ?>">
                            <a href="datatables-obstacle-legal.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Obstacle Land from SDG Design</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-spk-legal' ? 'active' : ''; ?>">
                            <a href="datatables-spk-legal.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Lampiran Izin Konstruksi</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-resto-name' ? 'active' : ''; ?>">
                            <a href="datatables-resto-name.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name mr-2">Resto Name</span>
                            <span class="badge <?php echo ($countrestoname > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countrestoname > 0 ? $countrestoname : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mou-parkir' ? 'active' : ''; ?>">
                            <a href="datatables-mou-parkir.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name mr-2">MOU Parkir & Sampah</span>
                            <span class="badge <?php echo ($countmouparkir > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countmouparkir > 0 ? $countmouparkir : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Negosiator -->
                <div class="submenu-area" data-parent="apps">
                    <header>
                        <h6><i class="nav-icon i-Computer-Secure"></i> Negotiator</h6>
                        <p>Negotiator Division</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo $current_page == 'datatables-doc-confirm-negosiator' ? 'active' : ''; ?>">
                            <a href="datatables-doc-confirm-negosiator.php">
                                <i class="nav-icon i-Add-File"></i>
                                <span class="item-name mr-2">Commercial Negotiation</span>
                            <span class="badge <?php echo ($countnego > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countnego > 0 ? $countnego : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-gostore-nego' ? 'active' : ''; ?>">
                            <a href="datatables-gostore-nego.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Data Go Store</span>
                            </a>
                        </li> -->
                        <!--<li class="nav-item <?php echo $current_page == 'datatables-validasi-negosiator' ? 'active' : ''; ?>">-->
                        <!--    <a href="datatables-validasi-negosiator.php">-->
                        <!--        <i class="nav-icon i-Speach-Bubble-3"></i>-->
                        <!--        <span class="item-name mr-2">Sign Final Doc</span>-->
                        <!--    </a>-->
                        <!--</li>-->
                    </ul>
                </div>
                <!-- SDG Design -->
                <div class="submenu-area" data-parent="forms">
                    <header>
                        <h6><i class="nav-icon i-File-Clipboard-File--Text"></i> SDG Design</h6>
                        <p>SDG Design Division</p>
                    </header>
                    <ul class="childNav">
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-checkval-negosiator' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-negosiator.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">Validation Data Dealing Draft Sewa</span>
                            </a>
                        </li> -->
						<!-- <li class="nav-item <?php echo $current_page == 'datatables-land-survey' ? 'active' : ''; ?>">
                            <a href="datatables-land-survey.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Land Survey</span>
                            </a>
                        </li> -->
						<li class="nav-item <?php echo $current_page == 'datatables-submit-wo' ? 'active' : ''; ?>">
                            <a href="datatables-submit-wo.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Submit WO Design</span>
                            <span class="badge <?php echo ($countwodesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countwodesign > 0 ? $countwodesign : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-obstacle-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-obstacle-sdg.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">Land Survey & Layouting</span>
                            <span class="badge <?php echo ($countsurveylayout > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countsurveylayout > 0 ? $countsurveylayout : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-urugan.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">Urugan</span>
                            <span class="badge <?php echo ($counturugan > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counturugan > 0 ? $counturugan : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-design' ? 'active' : ''; ?>">
                            <a href="datatables-design.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name mr-2">Design</span>
                            <span class="badge <?php echo ($countdesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countdesign > 0 ? $countdesign : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-formval-release-design' ? 'active' : ''; ?>">
                            <a href="datatables-formval-release-design.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name mr-2">Form Validation Release Design</span>
                            </a>
                        </li> -->
                    </ul>
                </div>
                <!-- SDG QS -->
                <div class="submenu-area" data-parent="sessions">
                    <header>
                        <h6><i class="nav-icon i-Administrator"></i> SDG QS</h6>
                        <p>SDG QS Division</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo $current_page == 'datatables-rab-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-rab-urugan.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">RAB Creation - Urugan</span>
                            <span class="badge <?php echo ($countraburugan > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countraburugan > 0 ? $countraburugan : '0'; ?>
                            </span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-rab' ? 'active' : ''; ?>">
                            <a href="datatables-rab.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">RAB Creation - Constraction</span>
                            <span class="badge <?php echo ($countrabdesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countrabdesign > 0 ? $countrabdesign : '0'; ?>
                            </span>
                            </a>
                        </li>
                            <li class="nav-item <?php echo $current_page == 'datatables-rab-tambahkurang' ? 'active' : ''; ?>">
                                <a href="datatables-rab-tambahkurang.php">
                                    <i class="nav-icon i-Width-Window"></i>
                                    <span class="item-name mr-2">RAB Job Add Less</span>
                                <span class="badge <?php echo ($countrabjobadd > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countrabjobadd > 0 ? $countrabjobadd : '0'; ?>
                                </span>
                                </a>
                            </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-potensi-masalah-sdg-qs' ? 'active' : ''; ?>">
                            <a href="datatables-potensi-masalah-sdg-qs.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">List Potential Problems</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-validation-rab' ? 'active' : ''; ?>">
                            <a href="datatables-validation-rab.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name mr-2">Validation Data RAB to Procurement</span>
                            </a>
                        </li> -->
                    </ul>
                </div>
                <!-- Procurement -->
                <div class="submenu-area" data-parent="others">
                    <header>
                        <h6><i class="i-Double-Tap"></i> Procurement</h6>
                        <p>Procurement Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-wo-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-wo-from-sdg.php">
                                <i class="nav-icon i-Add-File"></i>
                                <span class="item-name mr-2">SPK Design</span>
                            <span class="badge <?php echo ($countspkdesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkdesign > 0 ? $countspkdesign : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tender-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-tender-urugan.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Tender Urugan Process</span>
                            <span class="badge <?php echo ($counttenderurugan > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttenderurugan > 0 ? $counttenderurugan : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-rab-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-rab-urugan.php">
                                <i class="nav-icon i-Loading-3"></i>
                                <span class="item-name mr-2">SPK for RAB Urugan</span>
                            <span class="badge <?php echo ($countspkurugan > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkurugan > 0 ? $countspkurugan : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tender' ? 'active' : ''; ?>">
                            <a href="datatables-tender.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name mr-2">Tender Construction Process</span>
                            <span class="badge <?php echo ($counttendercons > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttendercons > 0 ? $counttendercons : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-rab-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-rab-from-sdg.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name mr-2">SPK for RAB Construction</span>
                            <span class="badge <?php echo ($countspkcons > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkcons > 0 ? $countspkcons : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-final-spkcons' ? 'active' : ''; ?>">
                            <a href="datatables-final-spkcons.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name mr-2">Final SPK Construction</span>
                            <span class="badge <?php echo ($countfinalspkcons > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countfinalspkcons > 0 ? $countfinalspkcons : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tambahkurang' ? 'active' : ''; ?>">
                                <a href="datatables-tambahkurang.php">
                                    <i class="nav-icon i-Width-Window"></i>
                                    <span class="item-name mr-2">SPK Job Add Less</span>
                                <span class="badge <?php echo ($countjobadd > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countjobadd > 0 ? $countjobadd : '0'; ?>
                                </span>
                                </a>
                            </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spkfa-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-spkfa-procurement.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">SPK MEP - Filter Air</span>
                            <span class="badge <?php echo ($countspkfa > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkfa > 0 ? $countspkfa : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spkipal-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-spkipal-procurement.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">SPK MEP - IPAL</span>
                            <span class="badge <?php echo ($countspkipal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkipal > 0 ? $countspkipal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-vendor' ? 'active' : ''; ?>">
                            <a href="datatables-vendor.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">Data Vendor</span>
                            <span class="badge <?php echo ($countrabdesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countrabdesign > 0 ? $countrabdesign : '0'; ?>
                            </span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-procurement.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Data Procurement</span>
                            <span class="badge <?php echo ($countrabdesign > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countrabdesign > 0 ? $countrabdesign : '0'; ?>
                            </span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-eqpdev-procur' ? 'active' : ''; ?>">
                            <a href="datatables-eqpdev-procur.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">SPK Equipment</span>
                            <span class="badge <?php echo ($countspkeqp > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countspkeqp > 0 ? $countspkeqp : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spk-sdgpk' ? 'active' : ''; ?>">
                            <a href="datatables-spk-sdgpk.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">SPK List</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-issue-urgent-procur' || strpos($current_page, 'datatables-data-procurutensil')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-issue-urgent-procur.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Issue Urgent</span>
                                <span class="badge <?php echo ($countissueprocur > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countissueprocur > 0 ? $countissueprocur : '0'; ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- SDG PK -->
                <div class="submenu-area" data-parent="datatables">
                    <header>
                        <h6><i class="i-File-Horizontal-Text"></i> SDG Project Construction</h6>
                        <p>SDG Project Construction Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-kom-schedule' ? 'active' : ''; ?>">
                            <a href="datatables-kom-schedule.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">Scheduling Kick Off Meeting Construction</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-potensi-masalah-sdg-project' ? 'active' : ''; ?>">
                            <a href="datatables-potensi-masalah-sdg-project.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">List Potential Problems</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-kom-sdgpk' ? 'active' : ''; ?>">
                            <a href="datatables-kom-sdgpk.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">Kick Off Meeting Construction</span>
                            <span class="badge <?php echo ($countkom > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countkom > 0 ? $countkom : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-monitoring-op' ? 'active' : ''; ?>">
                            <a href="datatables-monitoring-op.php">
                                <i class="nav-icon i-File-Horizontal"></i>
                                <span class="item-name mr-2">Construction Monitoring per Week</span>
                            <span class="badge <?php echo ($countcons > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countcons > 0 ? $countcons : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-construction-act-vendor' ? 'active' : ''; ?>">
                            <a href="datatables-construction-act-vendor.php">
                                <i class="nav-icon i-Male"></i>
                                <span class="item-name mr-2">Construction Activity by Vendor per Month</span>
                            </a>
                        </li>
                            <li class="nav-item <?php echo $current_page == 'datatables-wo-tambahkurang' ? 'active' : ''; ?>">
                                <a href="datatables-wo-tambahkurang.php">
                                    <i class="nav-icon i-Width-Window"></i>
                                    <span class="item-name mr-2">WO Job Add Less</span>
                                <span class="badge <?php echo ($countwojobadd > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countwojobadd > 0 ? $countwojobadd : '0'; ?>
                                </span>
                                </a>
                            </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-sdgpk-rto' || strpos($current_page, 'sdgpk-rto-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name mr-2">MEP - Sumber Air</span>
                            <span class="badge <?php echo ($countmepsa > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countmepsa > 0 ? $countmepsa : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-sdgpk-rto-listrik' || strpos($current_page, 'sdgpk-rto-listrik-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto-listrik.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name mr-2">MEP - Listrik Evaluation</span>
                            <span class="badge <?php echo ($countmeplistrik > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countmeplistrik > 0 ? $countmeplistrik : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-sdgpk-rto-ipal' || strpos($current_page, 'sdgpk-rto-ipal-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto-ipal.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name mr-2">MEP - IPAL Evaluation</span>
                            <span class="badge <?php echo ($countmepipal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countmepipal > 0 ? $countmepipal : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-st-konstruksi' ? 'active' : ''; ?>">
                            <a href="datatables-st-konstruksi.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">ST Kontraktor</span>
                            <span class="badge <?php echo ($countstkons > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countstkons > 0 ? $countstkons : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-sdgpk-issue' ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-issue.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name mr-2">Issue List</span>
                            <span class="badge <?php echo ($countissue > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countissue > 0 ? $countissue : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- SDG EQP -->
                <div class="submenu-area" data-parent="eqp">
                    <header>
                        <h6><i class=" i-Bell1"></i> SDG Equipment</h6>
                        <p>SDG Equipment Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo ($current_page == 'datatables-wo-eqp' || strpos($current_page, 'eqp-wo-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-wo-eqp.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">Submit WO Equipment</span>
                            <span class="badge <?php echo ($countwoeqp > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countwoeqp > 0 ? $countwoeqp : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-eqp-delivery' || strpos($current_page, 'eqpdev-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-eqp-delivery.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">Equipment Delivery</span>
                            <span class="badge <?php echo ($counteqpdev > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counteqpdev > 0 ? $counteqpdev : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-eqp-site' || strpos($current_page, 'eqpsite-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-eqp-site.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">Equipment On Site</span>
                            <span class="badge <?php echo ($counteqpsite > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counteqpsite > 0 ? $counteqpsite : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-st-eqp' || strpos($current_page, 'eqp-edit-form') !== false) ? 'active' : ''; ?>">
                            <a href="datatables-st-eqp.php">
                                <i class="nav-icon i-Crop-2"></i>
                                <span class="item-name mr-2">ST Equipment</span>
                            <span class="badge <?php echo ($counteqp > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counteqp > 0 ? $counteqp : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Operation -->
                <div class="submenu-area" data-parent="demos">
                    <header>
                        <h6><i class="nav-icon i-Safe-Box1"></i> PMO</h6>
                        <p>PMO Division</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo $current_page == 'datatables-soc' ? 'active' : ''; ?>">
                            <a href="datatables-soc.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">Entry SOC</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-soc-summary' ? 'active' : ''; ?>">
                            <a href="datatables-soc-summary.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Summary SOC Progress</span>
                            </a>
                        </li>	
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-achieve' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-achieve.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">All Achievement Division</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-update-info' ? 'active' : ''; ?>">
                            <a href="datatables-update-info.php">
                                <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                                <span class="item-name mr-2">Update Information All Division - Construction Progress</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mom-pmo' ? 'active' : ''; ?>">
                            <a href="datatables-mom-pmo.php">
                                <i class="nav-icon i-Cursor-Click"></i>
                                <span class="item-name mr-2">MoM Entry</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- HR -->
                <div class="submenu-area" data-parent="hr">
                    <header>
                        <h6><i class="i-Double-Tap"></i> HR GA</h6>
                        <p>HR GA Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                    <li class="nav-item <?php echo ($current_page == 'datatables-hr-qs' || strpos($current_page, 'datatables-data-qs') !== false) ? 'active' : ''; ?>">
                        <a href="datatables-hr-qs.php">
                            <i class="nav-icon i-Tag-2"></i>
                            <span class="item-name mr-2">Data QC</span>
                            <span class="badge <?php echo ($counthrqc > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrqc > 0 ? $counthrqc : '0'; ?>
                            </span>
                        </a>
                    </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-hr-fl' || strpos($current_page, 'datatables-data-fl')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-fl.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name mr-2">Data FL</span>
                            <span class="badge <?php echo ($counthrfl > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrfl > 0 ? $counthrfl : '0'; ?>
                            </span>
                            </a>
                        </li>
						<li class="nav-item <?php echo ($current_page == 'datatables-hr-fulfillment' || strpos($current_page, 'datatables-data-ff1')!== false)  ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Fulfillment Batch 1</span>
                            <span class="badge <?php echo ($counthrff1 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrff1 > 0 ? $counthrff1 : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-data-ff1'  ? 'active' : ''; ?>">
                            <a href="datatables-data-ff1.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">Data Crew FF Batch 1</span>
                            </a>
                        </li> -->
						<li class="nav-item <?php echo ($current_page == 'datatables-hr-fulfillment-2' || strpos($current_page, 'datatables-data-ff2')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment-2.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Fulfillment Batch 2</span>
                            <span class="badge <?php echo ($counthrff2 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrff2 > 0 ? $counthrff2 : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-data-ff2' ? 'active' : ''; ?>">
                            <a href="datatables-data-ff2.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">Data Crew FF Batch 2</span>
                            </a>
                        </li> -->
						<li class="nav-item <?php echo ($current_page == 'datatables-hr-fulfillment-3' || strpos($current_page, 'datatables-data-ff3')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment-3.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">Fulfillment Batch 3</span>
                            <span class="badge <?php echo ($counthrff3 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrff3 > 0 ? $counthrff3 : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-data-ff3' ? 'active' : ''; ?>">
                            <a href="datatables-data-ff3.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name mr-2">Data Crew FF Batch 3</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-hot' ? 'active' : ''; ?>">
                            <a href="datatables-hr-hot.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name mr-2">Hand Over Training</span>
                            <span class="badge <?php echo ($counthrhot > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counthrhot > 0 ? $counthrhot : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-ir' ? 'active' : ''; ?>">
                            <a href="datatables-ir.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name mr-2">Pengamanan Reguler</span>
                            <span class="badge <?php echo ($countir > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countir > 0 ? $countir : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-issue-urgent-hrga' || strpos($current_page, 'datatables-data-hrgautensil')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-issue-urgent-hrga.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Issue Urgent</span>
                                <span class="badge <?php echo ($countissuehrga > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countissuehrga > 0 ? $countissuehrga : '0'; ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Legal -->
                <div class="submenu-area" data-parent="academy">
                    <header>
                        <h6><i class="nav-icon i-Suitcase"></i> Academy </h6>
                        <p>Academy Division</p>
                    </header>
                    <ul class="childNav">
                        <li class="nav-item <?php echo ($current_page == 'datatables-aca-qs' || strpos($current_page, 'datatables-data-acaqs')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-aca-qs.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">Data QC from HR</span>
                            <span class="badge <?php echo ($countacaqc > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countacaqc > 0 ? $countacaqc : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-aca-fl' || strpos($current_page, 'datatables-data-acafl')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-aca-fl.php">
                                <i class="nav-icon i-Crop-2"></i>
                                <span class="item-name mr-2">Data FL from HR</span>
                            <span class="badge <?php echo ($countacafl > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countacafl > 0 ? $countacafl : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-hr-kpt' || strpos($current_page, 'datatables-data-kpt1')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Completion Training Rate Crew Batch 1</span>
                            <span class="badge <?php echo ($countkpt1 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countkpt1 > 0 ? $countkpt1 : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-hr-kpt-2' || strpos($current_page, 'datatables-data-kpt2')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt-2.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Completion Training Rate Crew Batch 2</span>
                            <span class="badge <?php echo ($countkpt2 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countkpt2 > 0 ? $countkpt2 : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-hr-kpt-3' || strpos($current_page, 'datatables-data-kpt3')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt-3.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Completion Training Rate Crew Batch 3</span>
                            <span class="badge <?php echo ($countkpt3 > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countkpt3 > 0 ? $countkpt3 : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- SCM -->
                <div class="submenu-area" data-parent="scm">
                    <header>
                        <h6><i class="i-Double-Tap"></i> SCM</h6>
                        <p>SCM Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo ($current_page == 'datatables-scm'  || strpos($current_page, 'datatables-data-scmutensil')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-scm.php">
                                <i class="nav-icon i-File-Horizontal"></i>
                                <span class="item-name mr-2">SJ Utensil, Dry Stock, Frozen Stock</span>
                            <span class="badge <?php echo ($countutensil > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countutensil > 0 ? $countutensil : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-scm-ipal' ? 'active' : ''; ?>">
                            <a href="datatables-scm-ipal.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">EHS IPAL</span>
                            <span class="badge <?php echo ($countscmipal > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countscmipal > 0 ? $countscmipal : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- IT -->
                <div class="submenu-area" data-parent="it">
                    <header>
                        <h6><i class="i-Double-Tap"></i> IT</h6>
                        <p>IT Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo $current_page == 'datatables-it-config' ? 'active' : ''; ?>">
                            <a href="datatables-it-config.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name mr-2">IT Configuration</span>
                            <span class="badge <?php echo ($countitconfig > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countitconfig > 0 ? $countitconfig : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-it' ? 'active' : ''; ?>">
                            <a href="datatables-it.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">Hardware Installation</span>
                            <span class="badge <?php echo ($countit > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countit > 0 ? $countit : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo ($current_page == 'datatables-issue-urgent-it' || strpos($current_page, 'datatables-data-itutensil')!== false) ? 'active' : ''; ?>">
                            <a href="datatables-issue-urgent-it.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Issue Urgent</span>
                                <span class="badge <?php echo ($countissueit > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                    <?php echo $countissueit > 0 ? $countissueit : '0'; ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Marketing -->
                <div class="submenu-area" data-parent="marketing">
                    <header>
                        <h6><i class="i-Double-Tap"></i> Marketing</h6>
                        <p>Marketing Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo $current_page == 'datatables-marketing' ? 'active' : ''; ?>">
                            <a href="datatables-marketing.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name mr-2">Marketing & Online Registration</span>
                            <span class="badge <?php echo ($countmarketing > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $countmarketing > 0 ? $countmarketing : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- FAT -->
                <div class="submenu-area" data-parent="fat">
                    <header>
                        <h6><i class="i-Double-Tap"></i> TAF</h6>
                        <p>TAF Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-review-wo-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-review-wo-from-sdg.php">
                                <i class="nav-icon i-Add-File"></i>
                                <span class="item-name mr-2">Review SPK Design</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-rab-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-review-rab-urugan.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name mr-2">Review SPK for RAB Urugan</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-review-rab-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-review-rab-from-sdg.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name mr-2">Review SPK for RAB Construction</span>
                            <span class="badge <?php echo ($counttafrab > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafrab > 0 ? $counttafrab : '0'; ?>
                            </span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-sign-psm-fat' ? 'active' : ''; ?>">
                            <a href="datatables-sign-psm-fat.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name mr-2">PSM Review</span>
                            <span class="badge <?php echo ($counttafpsm > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafpsm > 0 ? $counttafpsm : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-kode-store-taf' ? 'active' : ''; ?>">
                            <a href="datatables-kode-store-taf.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name mr-2">Penetapan Kode Store</span>
                            <span class="badge <?php echo ($counttafkode > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafkode > 0 ? $counttafkode : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-review-spkfa-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-review-spkfa-procurement.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name mr-2">Review SPK MEP - Filter Air</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-spkipal-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-review-spkipal-procurement.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name mr-2">Review SPK MEP - IPAL</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-spkeqp' ? 'active' : ''; ?>">
                            <a href="<?= $base_url . '/datatables-review-spkeqp.php'?>">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Review SPK Equipment</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spk-fat' ? 'active' : ''; ?>">
                            <a href="datatables-spk-fat.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name mr-2">List Data SPK</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-fat' ? 'active' : ''; ?>">
                            <a href="datatables-fat.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name mr-2">File QRIS & ST</span>
                            <span class="badge <?php echo ($counttafqris > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafqris > 0 ? $counttafqris : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tafpay-listrikair' ? 'active' : ''; ?>">
                            <a href="datatables-tafpay-listrikair.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name mr-2">Payment - Air PDAM</span>
                            <span class="badge <?php echo ($counttafpay > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafpay > 0 ? $counttafpay : '0'; ?>
                            </span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-listrik' ? 'active' : ''; ?>">
                            <a href="datatables-listrik.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name mr-2">Payment - Listrik</span>
                            <span class="badge <?php echo ($counttafpaylistrik > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo $counttafpaylistrik > 0 ? $counttafpaylistrik : '0'; ?>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- IR -->
                <!-- <div class="submenu-area" data-parent="ir">
                    <header>
                        <h6><i class="i-Double-Tap"></i> IR</h6>
                        <p>IR Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                    </ul>
                </div> -->
            </div>
        </div>

        <script>

document.querySelectorAll('.nav-item a').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
        this.parentElement.classList.add('active');
    });
});

</script>
