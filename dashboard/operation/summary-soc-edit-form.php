<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Periksa apakah ada data yang dikirimkan melalui URL (ID)
if (isset($_GET['id'])) {
    // Ambil id dari URL
    $id = $_GET['id'];

    // Query untuk mendapatkan data resep berdasarkan ID
    $result = $conn->query("SELECT 
        summary_soc.*,
        resto.gostore_date,
        land.kode_lahan,
        land.nama_lahan,
        land.lokasi,
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
        socdate_scm.lamp_sj,
        sdg_desain.lamp_permit,
        sdg_desain.lamp_pbg,
        sdg_pk.month_1,
        sdg_pk.month_2,
        sdg_pk.month_3,
        dokumen_loacd.kode_store
        FROM resto
        INNER JOIN land ON resto.kode_lahan = land.kode_lahan
        INNER JOIN summary_soc ON resto.kode_lahan = summary_soc.kode_lahan
        JOIN sdg_pk ON resto.kode_lahan = sdg_pk.kode_lahan
        INNER JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
        INNER JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
        INNER JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
        INNER JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
        INNER JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
        INNER JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
        INNER JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
        INNER JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
        INNER JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
        INNER JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
        INNER JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
        WHERE summary_soc.id = '$id'");

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        // Ambil data resep
        $row = $result->fetch_assoc();
        $status_go = $row['status_go'];
    } else {
        echo "Data tidak ditemukan.";
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
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<body class="text-left">
    <div class="app-admin-wrap layout-sidebar-compact sidebar-dark-purple sidenav-open clearfix">
		<?php
			include '../../layouts/right-sidebar-data.php';
		?>

        <!--=============== Left side End ================-->
        <div class="main-content-wrap d-flex flex-column">
			<?php
			include '../../layouts/top-sidebar.php';
		?>

			<!-- ============ Body content start ============= -->
            <div class="main-content">
                <div class="breadcrumb">
                    <h1>Summary SOC</h1>
                    <ul>
                        <li><a href="href">Form</a></li>
                        <li>Summary SOC</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-5">
                            <div class="card-body">
                            <form method="post" action="summary-soc-edit.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="kode_lahan">Inventory Code</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="kode_lahan" name="kode_lahan" type="text" placeholder="" value="<?php echo $row['kode_lahan']; ?>" readonly/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="go_fix">GO Fix<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="go_fix" name="go_fix" type="date" placeholder="" value="<?php echo $row['go_fix']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="rto_act">RTO Actual<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="rto_act" name="rto_act" type="date" placeholder="" value="<?php echo $row['rto_act']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="type_kitchen">Type Kitchen<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="type_kitchen" name="type_kitchen">
                                            <option value="">Pilih</option>
                                            <option value="double" <?php echo (isset($row['type_kitchen']) && $row['type_kitchen'] == 'Double') ? 'selected' : ''; ?>>Double</option>
                                            <option value="triple" <?php echo (isset($row['type_kitchen']) && $row['type_kitchen'] == 'Triple') ? 'selected' : ''; ?>>Triple</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="jam_ops">Jam Operasional<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="jam_ops" name="jam_ops" type="text" placeholder="ex : 8-22" value="<?php echo $row['jam_ops']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="project_sales">Project Sales<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="project_sales" name="project_sales" type="text" oninput="formatRupiah(this)" placeholder="Masukkan Angka Saja" value="<?php echo $row['project_sales']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="crew_needed">Crew Needed<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="crew_needed" name="crew_needed" type="number" placeholder="ex : 50" value="<?php echo $row['crew_needed']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="spk_release">SPK Release<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="spk_release" name="spk_release" type="date" placeholder="" value="<?php echo $row['spk_release']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="gocons_progress">RTO Score<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="gocons_progress" name="gocons_progress" type="text" placeholder="" value="
                                            
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
                                                        $fix = number_format($total, 2);
                                                        echo $fix; 
                                            ?>%" readonly/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="rto_score">GO Construction Progress<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="rto_score" name="rto_score" type="text" placeholder="" value="
                                                    <?php 
                                                    if (!empty($row['month_1']) && !empty($row['month_2']) && !empty($row['month_3'])) {
                                                        $total = $row['month_1'] + $row['month_2'] + $row['month_3']; 
                                                        echo $total . "%";
                                                    }
                                            ?>%" readonly/>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="status_go">Status GO<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="status_go" name="status_go">
                                            <?php echo $options; ?>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <button class="btn btn-primary" type="submit">Simpan</button>
                                    </div>
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
                </div>

				<!-- end of main-content -->
                <!-- Footer Start -->
                <div class="flex-grow-1"></div>
                <!-- fotter end -->
            </div>
        </div>
    </div><!-- ============ Search UI Start ============= -->
    <div class="search-ui">
        <div class="search-header">
            <img src="../../dist-assets/images/logo.png" alt="" class="logo">
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
                        <img src="../../dist-assets/images/products/headphone-1.jpg" alt="">
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
                        <img src="../../dist-assets/images/products/headphone-2.jpg" alt="">
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
                        <img src="../../dist-assets/images/products/headphone-3.jpg" alt="">
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
                        <img src="../../dist-assets/images/products/headphone-4.jpg" alt="">
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
    <script src="../../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../dist-assets/js/scripts/script.min.js"></script>
    <script src="../../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../../dist-assets/js/scripts/customizer.script.min.js"></script>
    
    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = 'Rp. ' + rupiah;
        }
    </script>
</body>

</html>