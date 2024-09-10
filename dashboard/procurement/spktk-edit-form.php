<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Periksa apakah ada data yang dikirimkan melalui URL (ID)
if(isset($_GET['id'])) {
    // Ambil ID dari URL
    $id = $_GET['id'];

    // Query untuk mendapatkan data resep berdasarkan ID
    $result = $conn->query("SELECT * FROM jobadd WHERE id = '$id'");

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
                    <h1>Data SPK Job Add Less</h1>
                    <ul>
                        <li><a href="href">Edit</a></li>
                        <li>Data SPK Job Add Less</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-5">
                            <div class="card-body">
                            <form method="post" action="spktk-edit.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="lamp_jobadd">Upload Lampiran SPK Job Add<strong><span style="color: red;">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multple-file-upload" >
                                            <input name="lamp_jobadd[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lampiran Company Profile Sebelumnya</label>
                                    <div class="col-sm-9">
                                        <?php echo $row['lamp_profil']; ?>
                                    </div>
                                </div> -->
                                <!-- Tambahkan pertanyaan apakah ingin mengganti lampiran -->
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mau Ganti Lampiran?</label>
                                    <div class="col-sm-9">
                                        <input type="radio" name="ganti_lampirancp" value="ya"> Ya
                                        <input type="radio" name="ganti_lampirancp" value="tidak" checked> Tidak
                                    </div>
                                </div> -->
                                <!-- Jika pengguna ingin mengganti lampiran, tampilkan input untuk unggah file -->
                                <!-- <div class="form-group row" id="lampiran_barucp" style="display: none;">
                                    <label class="col-sm-3 col-form-label" for="lamp_profil">Upload Baru</label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multple-file-upload">
                                            <input name="lamp_profil[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lampiran Sebelumnya</label>
                                    <div class="col-sm-9">
                                        <?php echo $row['lamp_vendor']; ?>
                                    </div>
                                </div> -->
                                <!-- Tambahkan pertanyaan apakah ingin mengganti lampiran -->
                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mau Ganti Lampiran?</label>
                                    <div class="col-sm-9">
                                        <input type="radio" name="ganti_lampiran" value="ya"> Ya
                                        <input type="radio" name="ganti_lampiran" value="tidak" checked> Tidak
                                    </div>
                                </div> -->
                                <!-- Jika pengguna ingin mengganti lampiran, tampilkan input untuk unggah file -->
                                <!-- <div class="form-group row" id="lampiran_baru" style="display: none;">
                                    <label class="col-sm-3 col-form-label" for="lamp_vendor">Upload Baru</label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multple-file-upload">
                                            <input name="lamp_vendor[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div> -->

                                <!-- <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="nama_vendor">Nama Vendor</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="nama_vendor" name="nama_vendor">
                                            <option value="">Pilih Nama Vendor</option>
                                            <?php
                                            if ($result_vendor && $result_vendor->num_rows > 0) {
                                                while ($row_vendor = $result_vendor->fetch_assoc()) {
                                                    echo "<option value='" . $row_vendor["kode_vendor"] . "' data-lampiran='" . $row_vendor["lamp_vendor"] . "'>" . $row_vendor["nama"] . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>Tidak ada kode vendor tersedia</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div> -->
                                    <!-- <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="lamp_profil">Lampiran Company Profile</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="lamp_profil" name="lamp_profil" value="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="lamp_vendor">Lampiran Vendor</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="lamp_vendor" name="lamp_vendor" value="" readonly>
                                        </div>
                                    </div>-->
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
    // Tambahkan event listener untuk radio button ganti_lampirancp
    document.querySelectorAll('input[name="ganti_lampirancp"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'ya') {
                // Jika pilihannya ya, tampilkan input untuk unggah file baru
                document.getElementById('lampiran_barucp').style.display = 'block';
            } else {
                // Jika pilihannya tidak, sembunyikan input untuk unggah file baru
                document.getElementById('lampiran_barucp').style.display = 'none';
            }
        });
    });

    // Tambahkan event listener untuk radio button ganti_lampiran
    document.querySelectorAll('input[name="ganti_lampiran"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'ya') {
                // Jika pilihannya ya, tampilkan input untuk unggah file baru
                document.getElementById('lampiran_baru').style.display = 'block';
            } else {
                // Jika pilihannya tidak, sembunyikan input untuk unggah file baru
                document.getElementById('lampiran_baru').style.display = 'none';
            }
        });
    });
</script>
    <script>
    function fetchVendors(cityId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "procurement_get.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById("kode_vendor").innerHTML = xhr.responseText;
            }
        };
        xhr.send("city_id=" + cityId);
    }
</script>
    <script>
        function updateLampiran() {
            var selectedOption = document.getElementById('nama_vendor').selectedOptions[0];
            var lampiran = selectedOption.getAttribute('data-lampiran');
            var profil = selectedOption.getAttribute('data-profil');
            document.getElementById('lamp_profil').value = profil;
            document.getElementById('lamp_vendor').value = lampiran;
        }
    </script>
</body>

</html>