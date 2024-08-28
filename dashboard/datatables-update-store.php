<?php

include "../koneksi.php";

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
        sdg_pk.month_1,
        sdg_pk.month_2,
        sdg_pk.month_3,
        dokumen_loacd.kode_store
        FROM resto
         JOIN land ON resto.kode_lahan = land.kode_lahan
         JOIN summary_soc ON resto.kode_lahan = summary_soc.kode_lahan
         JOIN sdg_pk ON resto.kode_lahan = sdg_pk.kode_lahan
         JOIN equipment ON resto.kode_lahan = equipment.kode_lahan
         JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
         JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
         JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
         JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
         JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
         JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
         JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
         JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
         JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
         JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
         JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
         GROUP BY summary_soc.kode_lahan";
$result = $conn->query($sql);

// Inisialisasi variabel $data dengan array kosong
$data = [];

// Periksa apakah query mengembalikan hasil yang valid
if ($result && $result->num_rows > 0) {
    // Ambil data dan masukkan ke dalam array $data
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
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

$sql_labels = "
    SELECT 
        l.kode_lahan,
        CASE
            WHEN l.status_land = 'Aktif' AND r.status_kom  != 'Approve' THEN 'In Preparation'
            WHEN r.status_kom = 'Approve' AND h.status_hold != 'In Process' THEN 'In Progress'
            WHEN h.status_hold = 'In Process' THEN 'Hold Project'
            ELSE 'In Preparation'
        END as label
    FROM land l
    LEFT JOIN resto r ON l.kode_lahan = r.kode_lahan
    LEFT JOIN hold_project h ON l.kode_lahan = h.kode_lahan";

$result_labels = $conn->query($sql_labels);

// Initialize an array to store labels
$labels = [];

// Check if there are results and store them in the array
if ($result_labels->num_rows > 0) {
    while ($row_labels = $result_labels->fetch_assoc()) {
        $labels[$row_labels['kode_lahan']] = $row_labels['label'];
    }
}
$no = 1;
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
                    <h1 class="mr-2">Update Per Store</h1>
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li>Update Per Store</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                
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
                                        <h3 class="w-50 float-left card-title m-0">Update Per Store</h3>
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
                                                        <th scope="col">Kode Store</th>
                                                        <th scope="col">Nama</th>
                                                        <th scope="col">Alamat / Maps</th>
                                                        <th scope="col">Date GO</th>
                                                        <th scope="col">Project Status</th>
                                                        <th scope="col">% Complete</th>
                                                        <th scope="col">Bussiness Hour</th>
                                                        <th scope="col">Type Kitchen</th>
                                                        <th scope="col">Crew Needed</th>
                                                        <th scope="col">Project Sales</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($data as $row): ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $no++; ?></th>
                                                        <td><?php echo $row ['kode_store']?></td>
                                                        <td><?php echo $row ['nama_lahan']?></td>
                                                        <td class="small-column">
                                                            <?php if (!empty($row['maps'])): ?>
                                                                <a href="<?= htmlspecialchars($row['maps']) ?>" target="_blank" title="View Map">
                                                                    <i class="fas fa-map-marker-alt"></i> <!-- Ikon peta Font Awesome -->
                                                                </a>
                                                            <?php else: ?>
                                                                <!-- Jika data kosong, Anda bisa menampilkan pesan atau membiarkannya kosong -->
                                                                <!-- Misalnya, menampilkan pesan "No link" atau membiarkannya kosong -->
                                                                <!-- <span>No link</span> -->
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $row ['gostore_date']?></td>
                                                        
                                                        <td>
                                                            <?php
                                                            // Display the label if it exists for the current kode_lahan
                                                            $kode_lahan = $row['kode_lahan'];
                                                            echo isset($labels[$kode_lahan]) ? htmlspecialchars($labels[$kode_lahan]) : 'In Preparation';
                                                            ?>
                                                        </td>
                                                                
                                                        <!-- <td><img class="rounded-circle m-0 avatar-sm-table" src="../dist-assets/images/faces/1.jpg" alt="" /></td> -->
                                                        
                                                        <td>
                                                            <?php
                                                                $fat = (( !is_null($row['lamp_qris']) ? 100 : 0 ) + ( !is_null($row['lamp_st']) ? 100 : 0 )) / 2;

                                                                $academy = (( !is_null($row['kpt_1']) ? 100 : 0 ) + ( !is_null($row['kpt_2']) ? 100 : 0 ) + ( !is_null($row['kpt_3']) ? 100 : 0 )) / 3;

                                                                $hr = (( !is_null($row['tm']) ? 100 : 0 ) + ( !is_null($row['lamp_tm']) ? 100 : 0 ) + ( !is_null($row['ff_1']) ? 100 : 0 ) + ( !is_null($row['lamp_ff1']) ? 100 : 0 ) + ( !is_null($row['ff_2']) ? 100 : 0 ) + ( !is_null($row['lamp_ff2']) ? 100 : 0 ) + ( !is_null($row['ff_3']) ? 100 : 0 ) + ( !is_null($row['lamp_ff3']) ? 100 : 0 ) + ( !is_null($row['hot']) ? 100 : 0 ) + ( !is_null($row['lamp_hot']) ? 100 : 0 )) / 10;

                                                                $ir = (( !is_null($row['lamp_rabcs']) ? 100 : 0 ) + ( !is_null($row['lamp_rabsecurity']) ? 100 : 0 )) / 2;

                                                                $it = (( !is_null($row['kode_dvr']) ? 100 : 0 ) + ( !is_null($row['web_report']) ? 100 : 0 ) + ( !is_null($row['akun_gis']) ? 100 : 0 ) + ( !is_null($row['lamp_internet']) ? 100 : 0 ) + ( !is_null($row['lamp_cctv']) ? 100 : 0 ) + ( !is_null($row['lamp_config']) ? 100 : 0 ) + ( !is_null($row['lamp_printer']) ? 100 : 0 ) + ( !is_null($row['lamp_sound']) ? 100 : 0 )) / 8;

                                                                $legal = (( !is_null($row['mou_parkirsampah']) ? 100 : 0 ) + ( !is_null($row['lamp_pbg']) ? 100 : 0 ) + ( !is_null($row['lamp_permit']) ? 100 : 0 )) / 3;

                                                                $marketing = (( !is_null($row['gmaps']) ? 100 : 0 ) + ( !is_null($row['lamp_gmaps']) ? 100 : 0 ) + ( !is_null($row['id_m_shopee']) ? 100 : 0 ) + ( !is_null($row['id_m_gojek']) ? 100 : 0 ) + ( !is_null($row['id_m_grab']) ? 100 : 0 ) + ( !is_null($row['email_resto']) ? 100 : 0 ) + ( !is_null($row['lamp_merchant']) ? 100 : 0 )) / 7;

                                                                $scm = (( !is_null($row['lamp_sj']) ? 100 : 0 ));

                                                                $sdg = (( !is_null($row['lamp_ujilab']) ? 100 : 0 ) + ( !is_null($row['lamp_sumberair']) ? 100 : 0 ) + ( !is_null($row['lamp_filterair']) ? 100 : 0 ) + ( !is_null($row['lamp_slo']) ? 100 : 0 ) + ( !is_null($row['lamp_nidi']) ? 100 : 0 ) + ( !is_null($row['lampwo_reqipal']) ? 100 : 0 )) / 6;
        
                                                                $total = ($fat + $academy + $hr + $ir + $it + $legal + $marketing + $scm + $sdg) / 9;
                                                                $fix = round($total, 2);
                                                                echo $fix; 
                                                            ?>%
                                                        </td>
                                                        <td><?php echo $row ['jam_ops']?></td>
                                                        <td><?php echo $row ['type_kitchen']?></td>
                                                        <td><?php echo $row ['crew_needed']?></td>
                                                        <td><?php echo $row ['project_sales']?></td>
                                                        <td>
                                                            <button class="btn btn-outline-primary mt-3 mb-3 m-sm-0 btn-sm btn-rounded" onclick="window.location.href='datatables-update-store-detail.php?kode_lahan=<?= $row['kode_lahan'] ?>'">
                                                                View details
                                                            </button>
                                                        </td>
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
</body>

</html>