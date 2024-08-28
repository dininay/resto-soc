<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

if(isset($_GET['kode_lahan'])) {
    // Ambil data gostore_date dari tabel resto
    $resto_id = $_GET['kode_lahan'];
    $resto_query = "SELECT gostore_date FROM resto WHERE kode_lahan = ?";
    $resto_stmt = $conn->prepare($resto_query);
    $resto_stmt->bind_param("s", $resto_id);
    $resto_stmt->execute();
    $resto_result = $resto_stmt->get_result();

    if ($resto_result->num_rows > 0) {
        $row_resto = $resto_result->fetch_assoc();
        $gostore_date = $row_resto['gostore_date'];

        // Debug: Output gostore_date
        echo "gostore_date: " . $gostore_date . "<br>";

        // Define divisions yang diinginkan
        $divisions = ['sdg'];
        $dates = [];

        foreach ($divisions as $division) {
            $sla_query = "SELECT sla FROM master_slacons WHERE divisi = ?";
            $sla_stmt = $conn->prepare($sla_query);
            $sla_stmt->bind_param("s", $division);
            $sla_stmt->execute();
            $sla_result = $sla_stmt->get_result();

            if ($sla_result->num_rows > 0) {
                $row_sla = $sla_result->fetch_assoc();
                $jumlah_sla = $row_sla['sla'];

                // Debug: Output jumlah_sla for division
                echo "Division: " . $division . " - sla: " . $jumlah_sla . "<br>";

                // Kurangi gostore_date dengan jumlah_sla untuk divisi sdg
                $dates[$division] = date('Y-m-d', strtotime($gostore_date . " -$jumlah_sla days"));
            } else {
                $dates[$division] = "No SLA data";
                echo "No SLA data for division: " . $division . "<br>";
            }
        }

        // Assign hasil perhitungan tanggal ke variabel yang sesuai
        $construction_date = $dates['sdg']; // Menggunakan 'sdg' karena hanya satu elemen dalam $divisions
        $debitair_date = $dates['sdg'];
        $kualitasair_date = $dates['sdg'];
        $listrik_date = $dates['sdg'];
        $ipal_date = $dates['sdg'];

        // Debug: Output calculated dates
        echo "construction_date: " . $construction_date . "<br>";
        echo "debitair_date: " . $debitair_date . "<br>";
        echo "kualitasair_date: " . $kualitasair_date . "<br>";
        echo "listrik_date: " . $listrik_date . "<br>";
        echo "ipal_date: " . $ipal_date . "<br>";
    } else {
        echo "No resto data found for kode_lahan: " . $resto_id . "<br>";
    }

    $conn->close();
} else {
    echo "No kode_lahan provided<br>";
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
                    <h1>SOC Date</h1>
                    <ul>
                        <li><a href="href">Form</a></li>
                        <li>SOC Date</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-5">
                            <div class="card-body">
                            <form method="post" action="summary-soc-edit.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="kode_lahan" value="<?php echo $row['kode_lahan']; ?>">
                                <div class="row">
                                <legend style="color: #538392; padding: 3px; font-weight: bold; text-align:left; margin-left:15px;">SDG</legend>
                                    <div class="col-md-4">
                                    <h5 style="color: #538392; font-weight: bold;">Deadline</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="cons_date">Construction<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="cons_date" name=""><?php echo $construction_date; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="debitair_date">Debit<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="debitair_date" name=""><?php echo $debitair_date; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="kualitasair_date">Kualitas<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="kualitasair_date" name=""><?php echo $kualitasair_date; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="listrik_date">Listrik KWH<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="listrik_date" name=""><?php echo $listrik_date; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="ipal_date">Saluran Kota IPAL<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="ipal_date" name=""><?php echo $ipal_date; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    <h5 style="color: #538392; font-weight: bold;">Done Date</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="cons_date">Construction<strong><span style="color: red;">*</span></strong></label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="cons_date" name="cons_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="debitair_date">Debit Air</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="debitair_date" name="debitair_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="kualitasair_date">Kualitas Air</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="kualitasair_date" name="kualitasair_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="listrik_date">Listrik KWH</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="listrik_date" name="listrik_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="ipal_date">Saluran Kota IPAL</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="ipal_date" name="ipal_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 style="color: #538392; font-weight: bold;">Status</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="cons">Construction</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="cons" name="cons">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="debitair">Debit Air</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="debitair" name="debitair">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="kualitasair">Kualitas Air</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="kualitasair" name="kualitasair">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="listrikkwh">Listrik KWH</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="listrikkwh" name="listrikkwh">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="saluranipal">Saluran Kota IPAL</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="saluranipal" name="saluranipal">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <legend style="color: #538392; padding: 3px; font-weight: bold; text-align:left; margin-left:15px;">Legal</legend>
                                    <div class="col-md-4">
                                    <h5 style="color: #538392; font-weight: bold;">Deadline</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="permitpbg_deadline">Permit PBG</label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="permitpbg_deadline" name=""><?php echo $permitpbg_deadline; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="permitizin_deadline">Permit Izin Lokasi (KPK, PKKPR)</label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="permitizin_deadline" name=""><?php echo $permitizin_deadline; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="landclearance_deadline">Land Clearance</label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="landclearance_deadline" name=""><?php echo $landclearance_deadline; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="parkir_deadline">Parkir</label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="parkir_deadline" name=""><?php echo $parkir_deadline; ?></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-5 col-form-label" for="sampah_deadline">Sampah</label>
                                            <div class="col-sm-5">
                                                <p class="form-control-plaintext" id="sampah_deadline" name=""><?php echo $sampah_deadline; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    <h5 style="color: #538392; font-weight: bold;">Done Date</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="permitpbg_date">Permit PBG</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="permitpbg_date" name="permitpbg_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="permitizin_date">Permit Izin Lokasi (KPK, PKKPR)</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="permitizin_date" name="permitizin_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="landclearance_date">Land Clearance</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="landclearance_date" name="landclearance_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="parkir_date">Parkir</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="parkir_date" name="parkir_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="sampah_date">Sampah</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" id="sampah_date" name="sampah_date" type="date" placeholder=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 style="color: #538392; font-weight: bold;">Status</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="permitpbg_status">Permit PBG</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="permitpbg_status" name="permitpbg_status">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="permitair_status">Permit Izin Lokasi (KPK, PKKPR)</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="permitair_status" name="permitair_status">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="landclearance_status">Land Clearance</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="landclearance_status" name="landclearance_status">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="parkir_status">Parkir</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="parkir_status" name="parkir_status">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label" for="sampah_status">Sampah</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="sampah_status" name="sampah_status">
                                                    <option value="">Pilih</option>
                                                    <option value="Done">Done</option>
                                                    <option value="In Process">In Process</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
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
    
</body>

</html>