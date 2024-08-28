<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Periksa apakah ada data yang dikirimkan melalui URL (ID)
if(isset($_GET['id'])) {
    // Ambil ID dari URL
    $id = $_GET['id'];

    // Query untuk mendapatkan data resep berdasarkan ID
    $result = $conn->query("SELECT * FROM socdate_sdg WHERE id = '$id'");

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        // Ambil data resep
        $row = $result->fetch_assoc();
        
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
                    <h1>MEP - Listrik Evaluation</h1>
                    <ul>
                        <li><a href="href">Edit</a></li>
                        <li>MEP - Listrik Evaluation</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-5">
                            <div class="card-body">
                            <form method="post" action="sdgpk-rto-listrik-edit.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="sumber_listrik">Sumber Listrik<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="sumber_listrik" name="sumber_listrik" onchange="toggleListrikDetail()">
                                            <option value="">Select Sumber Listrik</option>
                                            <option value="Penyambungan Daya Baru PLN" <?php echo ($row['sumber_listrik'] == 'Penyambungan Daya Baru PLN') ? 'selected' : ''; ?>>Penyambungan Daya Baru PLN</option>
                                            <option value="Tambah Listrik Existing" <?php echo ($row['sumber_listrik'] == 'Tambah Listrik Existing') ? 'selected' : ''; ?>>Tambah Listrik Existing</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="note-listrik" style="display: none;">
                                    <label class="col-sm-3 col-form-label" for="form_pengajuanlistrik">Upload Form Pengajuan Listrik<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multiple-file-upload">
                                            <input name="form_pengajuanlistrik[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" id="tgl-listrik" style="display: none;">
                                    <label class="col-sm-3 col-form-label" for="pasanglistrik_date">Tgl Pemasangan Listrik<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="pasanglistrik_date" name="pasanglistrik_date" type="date" placeholder="Tgl Pemasangan Listrik" value="<?php echo $row['pasanglistrik_date']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="id_pln">ID PLN<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="id_pln" name="id_pln" type="text" placeholder="ID PLN" value="<?php echo $row['id_pln']; ?>"/>
                                    </div>
                                </div>
                                <!-- <div class="form-group row" id="note-listrik" style="display: none;">
                                    <label class="col-sm-3 col-form-label" for="note_sumberlistrik">Note Lainnya<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="note_sumberlistrik" name="note_sumberlistrik" type="text" placeholder="Lainnya" value="<?php echo $row['note_sumberlistrik']; ?>"/>
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="hasil_va">Hasil VA (Voltege Ampere)<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="hasil_va" name="hasil_va" type="text" placeholder="Hasil VA" value="<?php echo $row['hasil_va']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="lamp_slo">Upload Document SLO<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multiple-file-upload">
                                            <input name="lamp_slo[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="lamp_nidi">Upload Document NIDI<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multiple-file-upload">
                                            <input name="lamp_nidi[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="biaya_perkwh">Biaya Per KWH</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="biaya_perkwh" name="biaya_perkwh" type="text" placeholder="Ketikkan Angka Saja" value="<?php echo $row['biaya_perkwh']; ?>"oninput="formatRupiah(this)"/>
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <div class="col-sm-9">
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
    function toggleListrikDetail() {
        var sumberListrik = document.getElementById("sumber_listrik");
        var lampiranFilter = document.getElementById("note-listrik");
        var tglListrik = document.getElementById("tgl-listrik");
        if (sumberListrik.value === "Penyambungan Daya Baru PLN") {
            lampiranFilter.style.display = "flex";
            tglListrik.style.display = "flex";
        } else {
            lampiranFilter.style.display = "none";
            tglListrik.style.display = "none";
        }
    }

    // Panggil fungsi saat halaman dimuat untuk menyesuaikan tampilan berdasarkan nilai awal
    window.onload = toggleListrikDetail;
    </script>
    
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

    <script>
    function toggleUjilabDetail() {
        var ujiLab = document.getElementById("kesesuaian_ujilab");
        var lampiranUjilab = document.getElementById("uji-lab");
        if (ujiLab.value === "Yes") {
            lampiranUjilab.style.display = "flex";
        } else {
            lampiranUjilab.style.display = "none";
        }
    }

    // Call the function on page load to set the initial display state
    window.onload = toggleUjilabDetail;
</script>
</body>

</html>