<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Periksa apakah ada data yang dikirimkan melalui URL (ID)
if(isset($_GET['id'])) {
    // Ambil ID dari URL
    $id = $_GET['id'];

    // Query untuk mendapatkan data resep berdasarkan ID
    $result = $conn->query("SELECT * FROM land WHERE id = '$id'");

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
                    <h1>Land Sourcing</h1>
                    <ul>
                        <li><a href="href">Edit</a></li>
                        <li>Land Sourcing</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-5">
                            <div class="card-body">
                            <form method="post" action="land-sourcing-edit.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="kode_lokasi">Inventory Code</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="kode_lokasi" name="kode_lahan" type="text" value="<?php echo $row['kode_lahan']; ?>" placeholder="Penanggungjawab" readonly/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="nama_lokasi">Nama Lahan</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="nama_lokasi" name="nama_lahan" type="text" value="<?php echo $row['nama_lahan']; ?>" placeholder="Example : Malang Sukun" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="lokasi">Lokasi</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="lokasi" name="lokasi" type="text" value="<?php echo $row['lokasi']; ?>" placeholder="Example : Jl. Raya S.Supriadi Kec.Sukun Kota Malang Jawa Timur, No. XX Kode Pos : XXXXX" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="nama_pemilik">Nama Pemilik</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="nama_pemilik" name="nama_pemilik" value="<?php echo $row['nama_pemilik']; ?>" type="text" placeholder="Example : Pak Abadi" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="alamat_pemilik">Alamat Pemilik</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="alamat_pemilik" name="alamat_pemilik" value="<?php echo $row['alamat_pemilik']; ?>" type="text" placeholder="Example : Jl. Raya S.Supriadi Kec.Sukun Kota Malang Jawa Timur, No. XX Kode Pos : XXXXX" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="no_telepon">No Telepon</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="no_telepon" name="no_tlp" type="number" value="<?php echo $row['no_tlp']; ?>" placeholder="Example : 08XXXXXXXXXX" maxlength="13" oninput="validatePhoneNumber(this)"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="luas_area">Luas Area (m3)</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="luas_area" name="luas_area" type="text" value="<?php echo $row['luas_area']; ?>" placeholder="Example : 10000" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lampiran Sebelumnya</label>
                                    <div class="col-sm-9">
                                        <?php echo $row['lamp_land']; ?>
                                    </div>
                                </div>
                                <!-- Tambahkan pertanyaan apakah ingin mengganti lampiran -->
                                <!-- <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Mau Ganti Lampiran?</label>
                                    <div class="col-sm-10">
                                        <input type="radio" name="ganti_lampiran" value="ya"> Ya
                                        <input type="radio" name="ganti_lampiran" value="tidak" checked> Tidak
                                    </div>
                                </div> -->
                                <!-- Jika pengguna ingin mengganti lampiran, tampilkan input untuk unggah file -->
                                <div class="form-group row" id="lampiran_baru">
                                    <label class="col-sm-3 col-form-label" for="lamp_land">Upload Baru</label>
                                    <div class="col-sm-9">
                                        <div class="dropzone" id="multple-file-upload" >
                                            <input name="lamp_land[]" type="file" multiple="multiple" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="maps">Link Maps</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="maps" name="maps" type="text" value="<?php echo $row['maps']; ?>" placeholder="Example : https://google.com/maps/......." />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="latitude">Latitude</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="latitude" name="latitude" type="text" value="<?php echo $row['latitude']; ?>" placeholder="-X.XXXXXXX" step="0.0000001" min="-9" max="9" title="Format: -X.XXXXXXX" oninput="validateLatitude(this)"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="longitude">Longitude</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="longitude" name="longitude" type="text" value="<?php echo $row['longitude']; ?>" placeholder="XXX.XXXXXXX" pattern="^-?\d{1,3}\.\d{1,7}$" title="Format: XXX.XXXXXXX" oninput="validateLongitude(this)" required />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="harga_sewa">Harga Sewa</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="harga_sewa" name="harga_sewa" type="text" value="<?php echo $row['harga_sewa']; ?>" placeholder="Masukkan angka saja" oninput="formatRupiah(this)"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="mintahun_sewa">Minimum Tahun Sewa</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="mintahun_sewa" name="mintahun_sewa" type="text" value="<?php echo $row['mintahun_sewa']; ?>" placeholder="Example : 5" />
                                    </div>
                                </div>
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
    function validatePhoneNumber(input) {
        if (input.value.length > 13) {
            input.value = input.value.slice(0, 13);
        }
    }
    </script>

<script>
function validateLatitude(input) {
    // Regex pattern for latitude format -X.XXXXXXX
    const pattern = /^-?\d{1,2}\.\d{1,7}$/;

    // If the input value doesn't match the pattern, set custom validity message
    if (!pattern.test(input.value)) {
        input.setCustomValidity("Invalid format. Use -X.XXXXXXX with maximum 7 digits after the decimal.");
    } else {
        input.setCustomValidity("");
    }
}
</script>

<script>
function validateLongitude(input) {
    // Regex pattern for longitude format XXX.XXXXXX
    const pattern = /^-?\d{1,3}\.\d{1,7}$/;

    // If the input value doesn't match the pattern, set custom validity message
    if (!pattern.test(input.value)) {
        input.setCustomValidity("Invalid format. Use XXX.XXXXXX with a maximum of 6 digits after the decimal point.");
    } else {
        input.setCustomValidity("");
    }
}
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js"></script>
    
    <script>
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
<!-- <script>
    // Inisialisasi Dropzone
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#dropzone", {
        url: "upload.php", // Tentukan URL untuk mengunggah file
        addRemoveLinks: true, // Tampilkan tautan hapus untuk setiap file yang diunggah
        acceptedFiles: "application/pdf, image/jpeg, image/jpg, image/png",
 // Jenis file yang diizinkan (dalam contoh ini, hanya file PDF yang diizinkan)
        init: function() {
            // Aksi setelah unggahan selesai
            this.on("complete", function(file) {
                // Hapus file dari Dropzone jika unggahan berhasil
                if (file.status === "success") {
                    myDropzone.removeFile(file);
                }
            });
        }
    });

    // Tambahkan file yang sudah ada ke Dropzone saat inisialisasi
    var existingFiles = "<?php echo $row['lamp_land']; ?>";
    if (existingFiles) {
        var files = existingFiles.split(",");
        for (var i = 0; i < files.length; i++) {
            var mockFile = { name: files[i], size: 12345 }; // Ganti 12345 dengan ukuran sebenarnya jika diketahui
            myDropzone.emit("addedfile", mockFile);
            myDropzone.emit("thumbnail", mockFile, "path/to/thumbnail"); // Ganti path/to/thumbnail dengan URL thumbnail yang sesuai
            myDropzone.emit("complete", mockFile);
        }
    }
</script> -->

</body>

</html>