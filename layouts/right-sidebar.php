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

?>
<style>
    .nav-item.active a {
    background-color: #f0f0f0; /* Warna latar belakang untuk item aktif */
    color: #333; /* Warna teks untuk item aktif */
}

.nav-item.active a .nav-icon {
    color: #333; /* Warna ikon untuk item aktif */
}

</style>
       <div class="side-content-wrap">
            <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
                <ul class="navigation-left">
                    <!-- Dashboard -->
                    <?php if (isset($_SESSION['level'])) : ?>
                    <li class="nav-item <?php echo (strpos($current_page, 'index') !== false ||$current_page == 'datatables-update-store' || $current_page == 'datatables-rto' || $current_page == 'datatables-mom'  || $current_page == 'datatables-soc-date-act' || $current_page == 'datatables-soc-date-hold'
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
                    <li class="nav-item <?php echo ($current_page == 'datatables-land-sourcing' || $current_page == 'datatables-validasi-lahan' || $current_page == 'datatables-draft-sewa-legal'  || $current_page == 'datatables-loa-cd' || $current_page == 'datatables-validasi-data'|| $current_page == 'datatables-bussiness-planning'
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
                    <li class="nav-item <?php echo ($current_page == 'datatables-validasi-lahan-legal' || $current_page == 'datatables-checkval-legal'|| $current_page == 'datatables-sign-psm-legal'|| $current_page == 'datatables-sp-submit-legal'|| $current_page == 'datatables-release-doc-legal' || $current_page == 'datatables-design-legal' || $current_page == 'datatables-valdoc-legal'|| $current_page == 'datatables-validasi-sp' || $current_page == 'datatables-obstacle-legal'|| $current_page == 'datatables-wovl'|| $current_page == 'datatables-wovd'|| $current_page == 'datatables-spk-legal'|| $current_page == 'datatables-mou-parkir'  || $current_page == 'datatables-resto-name') ? 'active' : ''; ?>" data-item="extrakits">
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
                    <li class="nav-item <?php echo ($current_page == 'datatables-data-picture' || $current_page == 'datatables-rab' || $current_page == 'datatables-rab-urugan' || $current_page == 'datatables-validation-rab') ? 'active' : ''; ?>" data-item="sessions">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="nav-text">SDG QS</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- Procurement Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Procurement") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-checkval-rab-from-sdg' || $current_page == 'datatables-checkval-wo-from-sdg' || $current_page == 'datatables-spkfa-procurement' || $current_page == 'datatables-checkval-rab-urugan' || $current_page == 'datatables-tender-urugan' ||  $current_page == 'datatables-procurement' || $current_page == 'datatables-vendor' || $current_page == 'datatables-spkipal-procurement' || 
                    $current_page == 'datatables-tender'|| $current_page == 'datatables-spk-sdgpk'|| $current_page == 'datatables-eqpdev-procur') ? 'active' : ''; ?>" data-item="others">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Double-Tap"></i>
                            <span class="nav-text">Procurement</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SDG PK Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SDG-Project") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-kom-sdgpk' || $current_page == 'datatables-monitoring-op'|| $current_page == 'datatables-construction-act-vendor'|| $current_page == 'datatables-kom-schedule'|| 
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
                    <li class="nav-item <?php echo ( $current_page == 'datatables-st-eqp'  || $current_page == 'datatables-sdgpk-eqp-rto'  || $current_page == 'datatables-eqp-delivery' || $current_page == 'datatables-eqp-site' || $current_page == 'datatables-wo-eqp'
                    ) ? 'active' : ''; ?>" data-item="eqp">
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
                    <li class="nav-item <?php echo ($current_page == 'datatables-hr-qs' || $current_page == 'datatables-hr-fl' || $current_page == 'datatables-hr-fulfillment'
                    || $current_page == 'datatables-hr-fulfillment-2' || $current_page == 'datatables-hr-fulfillment-3' 
                    || $current_page == 'datatables-hr-hot') ? 'active' : ''; ?>" data-item="hr">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                            <span class="nav-text">HR</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- HR Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "Academy") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-hr-kpt'
                    || $current_page == 'datatables-hr-kpt-2' || $current_page == 'datatables-hr-kpt-3') ? 'active' : ''; ?>" data-item="academy">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Loading-3"></i>
                            <span class="nav-text">Academy</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- SCM Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "SCM") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-scm' || $current_page == 'datatables-scm-ipal') ? 'active' : ''; ?>" data-item="scm">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Receipt-4"></i>
                            <span class="nav-text">SCM</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- IT Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "IT") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-it' || $current_page == 'datatables-it-config') ? 'active' : ''; ?>" data-item="it">
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
                    <li class="nav-item <?php echo ($current_page == 'datatables-fat' || $current_page == 'datatables-review-rab-urugan' || $current_page == 'datatables-tender-fat' || $current_page == 'datatables-spk-fat' || $current_page == 'datatables-sign-psm-fat' || $current_page == 'datatables-tafpay-listrikair' || $current_page =='datatables-review-wo-from-sdg' || $current_page == 'datatables-review-spkfa-procurement' || $current_page == 'datatables-review-rab-from-sdg' || $current_page == 'datatables-review-spkipal-procurement' || $current_page == 'datatables-review-spkeqp' || $current_page == 'datatables-kode-store-taf') ? 'active' : ''; ?>" data-item="fat">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Cursor-Click"></i>
                            <span class="nav-text">FAT</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>

                    <!-- IR Head -->
                    <?php if ($_SESSION['level'] === "Admin" || $_SESSION['level'] === "IR") : ?>
                    <li class="nav-item <?php echo ($current_page == 'datatables-ir') ? 'active' : ''; ?>" data-item="ir">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                            <span class="nav-text">IR</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php endif; ?>
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
                                <span class="item-name">Home</span>
                            </a>
                        </li>		
                        <li class="nav-item <?php echo $current_page == 'datatables-rto' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-rto.php">
                                <i class="nav-icon i-Clock-3"></i>
                                <span class="item-name">RTO</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mom' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-mom.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name">MOM</span>
                            </a>
                        </li>			
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name">In Progress Tracking</span>
                            </a>
                        </li>			
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-act' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-act.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">In Preparation Tracking</span>
                            </a>
                        </li>		
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-hold' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-hold.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name">Hold Project Tracking</span>
                            </a>
                        </li>	
                        <li class="nav-item <?php echo $current_page == 'datatables-update-store' ? 'active' : ''; ?>">
                            <a href="../dashboard/datatables-update-store.php">
                                <i class="nav-icon i-Over-Time"></i>
                                <span class="item-name">Update Per Store</span>
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
                                <span class="item-name">Bussiness Planning</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-land-sourcing' ? 'active' : ''; ?>">
                            <a href="datatables-land-sourcing.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name">Land Sourcing</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-submit-to-owner' ? 'active' : ''; ?>">
                            <a href="datatables-submit-to-owner.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name">BoD Approval</span>
                            </a>
                        </li>					 -->
                        <li class="nav-item <?php echo $current_page == 'datatables-validasi-lahan' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-lahan.php">
                                <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                                <span class="item-name">Validation Land to Legal</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-loa-cd' ? 'active' : ''; ?>">
                            <a href="datatables-loa-cd.php">
                                <i class="nav-icon i-Cursor-Click"></i>
                                <span class="item-name">LoA & CD</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-validasi-data' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-data.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name">Validasi Data to Legal</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-draft-sewa-legal' ? 'active' : ''; ?>">
                            <a href="datatables-draft-sewa-legal.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name">Final PSM & Table Sewa</span>
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
                                <span class="item-name">Bod Approval Land</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-doc-confirm' ? 'active' : ''; ?>">
                            <a href="datatables-doc-confirm.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name">Approval Draft Table Sewa</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-gostore' ? 'active' : ''; ?>">
                            <a href="datatables-gostore.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Data GO Store</span>
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
                                <span class="item-name">Doc Approval from BoD</span>
                            </a>
                        </li>    -->
                        <li class="nav-item <?php echo $current_page == 'datatables-wovl' ? 'active' : ''; ?>">
                            <a href="datatables-wovl.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name">Validate Land</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-release-doc-legal' ? 'active' : ''; ?>">
                            <a href="datatables-release-doc-legal.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name">Release Doc Legal Validation</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-wovd' ? 'active' : ''; ?>">
                            <a href="datatables-wovd.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name">Add WO VD Legal</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-legal' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-legal.php">
                                <i class="nav-icon i-Loading-3"></i>
                                <span class="item-name">Validasi Doc Legal (VD)</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-sign-psm-legal' ? 'active' : ''; ?>">
                            <a href="datatables-sign-psm-legal.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Draft PSM Legal</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-draft-sewa-legal' ? 'active' : ''; ?>">
                            <a href="datatables-draft-sewa-legal.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name">PSM Review</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-design-legal' ? 'active' : ''; ?>">
                            <a href="datatables-design-legal.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name">Completion Obstacle from SDG Design</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-valdoc-legal' ? 'active' : ''; ?>">
                            <a href="datatables-valdoc-legal.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name">Validasi All Data</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-sp-submit-legal' ? 'active' : ''; ?>">
                            <a href="datatables-sp-submit-legal.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name">Surat Permit & PBG Legal Submit</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-validasi-sp' ? 'active' : ''; ?>">
                            <a href="datatables-validasi-sp.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">Validation Permit & PBG Legal</span>
                            </a>
                        </li> -->
						<!-- <li class="nav-item <?php echo $current_page == 'datatables-obstacle-legal' ? 'active' : ''; ?>">
                            <a href="datatables-obstacle-legal.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">Obstacle Land from SDG Design</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-spk-legal' ? 'active' : ''; ?>">
                            <a href="datatables-spk-legal.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Lampiran Izin Konstruksi</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-resto-name' ? 'active' : ''; ?>">
                            <a href="datatables-resto-name.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name">Resto Name</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mou-parkir' ? 'active' : ''; ?>">
                            <a href="datatables-mou-parkir.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name">MOU Parkir & Sampah</span>
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
                                <span class="item-name">Doc Receipt Confirm Owner & Legal Confirm</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-gostore-nego' ? 'active' : ''; ?>">
                            <a href="datatables-gostore-nego.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">Data Go Store</span>
                            </a>
                        </li> -->
                        <!--<li class="nav-item <?php echo $current_page == 'datatables-validasi-negosiator' ? 'active' : ''; ?>">-->
                        <!--    <a href="datatables-validasi-negosiator.php">-->
                        <!--        <i class="nav-icon i-Speach-Bubble-3"></i>-->
                        <!--        <span class="item-name">Sign Final Doc</span>-->
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
                                <span class="item-name">Validation Data Dealing Draft Sewa</span>
                            </a>
                        </li> -->
						<!-- <li class="nav-item <?php echo $current_page == 'datatables-land-survey' ? 'active' : ''; ?>">
                            <a href="datatables-land-survey.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">Land Survey</span>
                            </a>
                        </li> -->
						<li class="nav-item <?php echo $current_page == 'datatables-submit-wo' ? 'active' : ''; ?>">
                            <a href="datatables-submit-wo.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">Submit WO Design</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-obstacle-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-obstacle-sdg.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name">Land Survey & Layouting</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-urugan.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name">Urugan</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-design' ? 'active' : ''; ?>">
                            <a href="datatables-design.php">
                                <i class="nav-icon i-Split-Vertical"></i>
                                <span class="item-name">Design</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-formval-release-design' ? 'active' : ''; ?>">
                            <a href="datatables-formval-release-design.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name">Form Validation Release Design</span>
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
                                <span class="item-name">RAB Creation - Urugan</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-rab' ? 'active' : ''; ?>">
                            <a href="datatables-rab.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">RAB Creation - Constraction</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-validation-rab' ? 'active' : ''; ?>">
                            <a href="datatables-validation-rab.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name">Validation Data RAB to Procurement</span>
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
                                <span class="item-name">SPK Design</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tender-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-tender-urugan.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Tender Urugan Process</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-rab-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-rab-urugan.php">
                                <i class="nav-icon i-Loading-3"></i>
                                <span class="item-name">SPK for RAB Urugan</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tender' ? 'active' : ''; ?>">
                            <a href="datatables-tender.php">
                                <i class="nav-icon i-Error-404-Window"></i>
                                <span class="item-name">Tender Construction Process</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-checkval-rab-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-checkval-rab-from-sdg.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name">SPK for RAB Construction</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spkfa-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-spkfa-procurement.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">SPK MEP - Filter Air</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spkipal-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-spkipal-procurement.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">SPK MEP - IPAL</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-vendor' ? 'active' : ''; ?>">
                            <a href="datatables-vendor.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name">Data Vendor</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php echo $current_page == 'datatables-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-procurement.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Data Procurement</span>
                            </a>
                        </li> -->
                        <li class="nav-item <?php echo $current_page == 'datatables-eqpdev-procur' ? 'active' : ''; ?>">
                            <a href="datatables-eqpdev-procur.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">SPK Equipment</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spk-sdgpk' ? 'active' : ''; ?>">
                            <a href="datatables-spk-sdgpk.php">
                                <i class="nav-icon i-Receipt-4"></i>
                                <span class="item-name">SPK List</span>
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
                        <li class="nav-item <?php echo $current_page == 'datatables-kom-schedule' ? 'active' : ''; ?>">
                            <a href="datatables-kom-schedule.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name">Scheduling Kick Off Meeting Construction</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-kom-sdgpk' ? 'active' : ''; ?>">
                            <a href="datatables-kom-sdgpk.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">Result Kick Off Meeting Construction</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-construction-act-vendor' ? 'active' : ''; ?>">
                            <a href="datatables-construction-act-vendor.php">
                                <i class="nav-icon i-Male"></i>
                                <span class="item-name">Construction Activity by Vendor per Month</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-monitoring-op' ? 'active' : ''; ?>">
                            <a href="datatables-monitoring-op.php">
                                <i class="nav-icon i-File-Horizontal"></i>
                                <span class="item-name">Construction Monitoring per Week</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-st-konstruksi' ? 'active' : ''; ?>">
                            <a href="datatables-st-konstruksi.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name">ST Kontraktor</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-sdgpk-issue' ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-issue.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name">Issue List</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-sdgpk-rto' ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name">MEP - Sumber Evaluation</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-sdgpk-rto-listrik' ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto-listrik.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name">MEP - Listrik Evaluation</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-sdgpk-rto-ipal' ? 'active' : ''; ?>">
                            <a href="datatables-sdgpk-rto-ipal.php">
                                <i class="nav-icon i-Clock-4"></i>
                                <span class="item-name">MEP - IPAL Evaluation</span>
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
                        <li class="nav-item <?php echo $current_page == 'datatables-st-eqp' ? 'active' : ''; ?>">
                            <a href="datatables-st-eqp.php">
                                <i class="nav-icon i-Crop-2"></i>
                                <span class="item-name">ST Equipment</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-wo-eqp' ? 'active' : ''; ?>">
                            <a href="datatables-wo-eqp.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name">Submit WO Equipment</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-eqp-delivery' ? 'active' : ''; ?>">
                            <a href="datatables-eqp-delivery.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name">Equipment Delivery</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-eqp-site' ? 'active' : ''; ?>">
                            <a href="datatables-eqp-site.php">
                                <i class="nav-icon i-Bell1"></i>
                                <span class="item-name">Equipment On Site</span>
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
                                <span class="item-name">Entry SOC</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-soc-summary' ? 'active' : ''; ?>">
                            <a href="datatables-soc-summary.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Summary SOC Progress</span>
                            </a>
                        </li>	
                        <li class="nav-item <?php echo $current_page == 'datatables-soc-date-achieve' ? 'active' : ''; ?>">
                            <a href="datatables-soc-date-achieve.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name">All Achievement Division</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-update-info' ? 'active' : ''; ?>">
                            <a href="datatables-update-info.php">
                                <i class="nav-icon i-Split-Horizontal-2-Window"></i>
                                <span class="item-name">Update Information All Division - Construction Progress</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-mom-pmo' ? 'active' : ''; ?>">
                            <a href="datatables-mom-pmo.php">
                                <i class="nav-icon i-Cursor-Click"></i>
                                <span class="item-name">MoM Entry</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- HR -->
                <div class="submenu-area" data-parent="hr">
                    <header>
                        <h6><i class="i-Double-Tap"></i> HR</h6>
                        <p>HR Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-qs' ? 'active' : ''; ?>">
                            <a href="datatables-hr-qs.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name">Data QC</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-fl' ? 'active' : ''; ?>">
                            <a href="datatables-hr-fl.php">
                                <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                                <span class="item-name">Data FL</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-hr-fulfillment' ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Fulfillment Batch 1</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-hr-fulfillment-2' ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment-2.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Fulfillment Batch 2</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-hr-fulfillment-3' ? 'active' : ''; ?>">
                            <a href="datatables-hr-fulfillment-3.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">Fulfillment Batch 3</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-hot' ? 'active' : ''; ?>">
                            <a href="datatables-hr-hot.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name">Hand Over Training</span>
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
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-kpt' ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Completion Training Rate Batch 1</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-kpt-2' ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt-2.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Completion Training Rate Batch 2</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-hr-kpt-3' ? 'active' : ''; ?>">
                            <a href="datatables-hr-kpt-3.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Completion Training Rate Batch 3</span>
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
                        <li class="nav-item <?php echo $current_page == 'datatables-scm' ? 'active' : ''; ?>">
                            <a href="datatables-scm.php">
                                <i class="nav-icon i-File-Horizontal"></i>
                                <span class="item-name">SJ Utensil, Dry Stock, Frozen Stock</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-scm-ipal' ? 'active' : ''; ?>">
                            <a href="datatables-scm-ipal.php">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">MEP IPAL</span>
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
                                <span class="item-name">IT Configuration</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-it' ? 'active' : ''; ?>">
                            <a href="datatables-it.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">Hardware Installation</span>
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
                                <span class="item-name">Marketing & Online Registration</span>
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
                        <li class="nav-item <?php echo $current_page == 'datatables-kode-store-taf' ? 'active' : ''; ?>">
                            <a href="datatables-kode-store-taf.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name">Penetapan Kode Store</span>
                            </a>
                        </li>
						<li class="nav-item <?php echo $current_page == 'datatables-sign-psm-fat' ? 'active' : ''; ?>">
                            <a href="datatables-sign-psm-fat.php">
                                <i class="nav-icon i-Checked-User"></i>
                                <span class="item-name">PSM Review</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-wo-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-review-wo-from-sdg.php">
                                <i class="nav-icon i-Add-File"></i>
                                <span class="item-name">Review SPK Design</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-rab-urugan' ? 'active' : ''; ?>">
                            <a href="datatables-review-rab-urugan.php">
                                <i class="nav-icon i-Loading-2"></i>
                                <span class="item-name">Review SPK for RAB Urugan</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-rab-from-sdg' ? 'active' : ''; ?>">
                            <a href="datatables-review-rab-from-sdg.php">
                                <i class="nav-icon i-Tag-2"></i>
                                <span class="item-name">Review SPK for RAB Construction</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-spkfa-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-review-spkfa-procurement.php">
                                <i class="nav-icon i-Email"></i>
                                <span class="item-name">Review SPK MEP - Filter Air</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-spkipal-procurement' ? 'active' : ''; ?>">
                            <a href="datatables-review-spkipal-procurement.php">
                                <i class="nav-icon i-Speach-Bubble-3"></i>
                                <span class="item-name">Review SPK MEP - IPAL</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-review-spkeqp' ? 'active' : ''; ?>">
                            <a href="<?= $base_url . '/datatables-review-spkeqp.php'?>">
                                <i class="nav-icon i-Pen-2"></i>
                                <span class="item-name">Review SPK Equipment</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-spk-fat' ? 'active' : ''; ?>">
                            <a href="datatables-spk-fat.php">
                                <i class="nav-icon i-Line-Chart-2"></i>
                                <span class="item-name">List Data SPK</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-fat' ? 'active' : ''; ?>">
                            <a href="datatables-fat.php">
                                <i class="nav-icon i-Close-Window"></i>
                                <span class="item-name">File QRIS & ST</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $current_page == 'datatables-tafpay-listrikair' ? 'active' : ''; ?>">
                            <a href="datatables-tafpay-listrikair.php">
                                <i class="nav-icon i-Width-Window"></i>
                                <span class="item-name">Payment - Listrik & Air PDAM</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- IR -->
                <div class="submenu-area" data-parent="ir">
                    <header>
                        <h6><i class="i-Double-Tap"></i> IR</h6>
                        <p>IR Division</p>
                    </header>
                    <ul class="childNav" data-parent="">
                        <li class="nav-item <?php echo $current_page == 'datatables-ir' ? 'active' : ''; ?>">
                            <a href="datatables-ir.php">
                                <i class="nav-icon i-Medal-2"></i>
                                <span class="item-name">Pengamanan Reguler</span>
                            </a>
                        </li>
                    </ul>
                </div>
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
