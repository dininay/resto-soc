<?php
// Koneksi ke database
include "../koneksi.php";

$status_approvre = "";
// Query untuk mengambil data dari tabel land
$sql = "SELECT * from land
        WHERE status_land = 'Aktif'";
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
    <link href="../dist-assets/css/plugins/datatables.min.css" rel="stylesheet"  />
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/feather-icon.css">
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <h1>Datatables Submit to Owner</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
									<p>
									  <span class="flex-grow-1"></span></p>
								</div>
                                <p>
							  <div class="table-responsive">
                                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
												<th>Luas Area</th>
                                                <th>No Telepon</th>
                                                <th>Lampiran</th>
                                                <th>Status Confirm to BoD</th>
                                                <th>SLA</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td><?= $row['kode_lahan'] ?></td>
                                            <td><?= $row['nama_lahan'] ?></td>
                                            <td><?= $row['lokasi'] ?></td>
                                            <td><?= $row['luas_area'] ?></td>
                                            <td><?= $row['no_tlp'] ?></td>
                                            <?php
                                            // Bagian ini di dalam loop yang menampilkan data tabel
                                            $lamp_land_files = explode(",", $row['lamp_land']); // Pisahkan nama file menjadi array
                                            ?>

                                            <td>
                                                <ul style="list-style-type: none; padding: 0; margin: 0;">
                                                    <?php foreach ($lamp_land_files as $file): ?>
                                                        <li style="display: inline-block; margin-right: 5px;">
                                                            <a href="uploads/<?= $file ?>" target="_blank">
                                                                <i class="fas fa-file-pdf nav-icon"></i>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_approvre']) {
                                                            case 'Approve':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Pending':
                                                                $badge_color = 'danger';
                                                                break;
                                                            case 'In Process':
                                                                $badge_color = 'primary';
                                                                break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status tidak dikenali
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $row['status_approvre']; ?>
                                                    </span>
                                                </td>
                                            <!-- <td>
                                                <div id="countdown_<?= $row['id'] ?>"></div>
                                                <script>
                                                    // Mengambil tanggal dari PHP dan menambahkannya dengan nilai SLA dari PHP
                                                    var statusDate = new Date("<?= $row['status_date'] ?>");
                                                    var slaDays = parseInt("<?= $row['sla'] ?>");
                                                    var slaDate = new Date(statusDate.setDate(statusDate.getDate() + slaDays)).getTime();

                                                    function updateCountdown(id, targetDate) {
                                                        var now = new Date().getTime();
                                                        var distance = targetDate - now;

                                                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                        document.getElementById(id).innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

                                                        if (distance < 0) {
                                                            clearInterval(x);
                                                            document.getElementById(id).innerHTML = "EXPIRED";
                                                        }
                                                    }

                                                    var x = setInterval(function() {
                                                        updateCountdown("countdown_<?= $row['id'] ?>", slaDate);
                                                    }, 1000);
                                                </script>
                                            </td> -->
                                                    <td>
                                                    <div id="countdown_<?= $row['id'] ?>"></div>
                                                    <div id="re_date_<?= $row['id'] ?>" style="margin-top: 5px;"></div>
                                                    <script>
                                                        // Mendapatkan tanggal SLA dari PHP
                                                        var slaDate_<?= $row['id'] ?> = new Date("<?= $row['sla'] ?>");
                                                        var status_approvre_<?= $row['id'] ?> = "<?= $row['status_approvre'] ?>";
                                                        var reDate_<?= $row['id'] ?> = "<?= $row['re_date'] ?>";

                                                        // Fungsi untuk memperbarui countdown
                                                        function updateCountdown(id, targetDate, status, reDate) {
                                                            var now = new Date().getTime();
                                                            var distance = targetDate.getTime() - now;

                                                            if (status === 'In Process') {
                                                                // Jika status masih "In Process", hitung mundur
                                                                if (distance >= 0) {
                                                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                                    document.getElementById(id).innerHTML = "<span class='badge badge-warning'>H-" + days + "</span>";
                                                                } else {
                                                                    var days = Math.abs(Math.floor(distance / (1000 * 60 * 60 * 24)));
                                                                    document.getElementById(id).innerHTML = "<span class='badge badge-danger'>H+" + days + "</span>";
                                                                }
                                                                document.getElementById("re_date_" + id).innerHTML = "<span>Pending</span>";
                                                            } else if (status === 'Approve') {
                                                                // Jika status sudah "Approve", tampilkan "Done" dengan warna hijau
                                                                document.getElementById(id).innerHTML = "<span class='badge badge-success'>Done</span>";
                                                                document.getElementById("re_date_" + id).innerHTML = "<span>Approved at " + reDate + "</span>";
                                                                // Pastikan tanggal re_date ditampilkan
                                                                document.getElementById("re_date_" + id).style.display = "block";
                                                            }
                                                        }

                                                        // Panggil fungsi updateCountdown setiap detik
                                                        setInterval(function() {
                                                            updateCountdown("countdown_<?= $row['id'] ?>", slaDate_<?= $row['id'] ?>, status_approvre_<?= $row['id'] ?>, reDate_<?= $row['id'] ?>);
                                                        }, 1000);
                                                    </script>
                                                </td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <?php if ($row['status_approvre'] != "Approve"): ?>
                                                        <div>
                                                        <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_approvre'] ?>">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button>
                                                    </div>
                                                    <?php endif; ?>

                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="re/submit-to-owner-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" value="<?=$row["id"]?>" id="modalKodeLahan">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Approve RE<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="status_approvre">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Pending">Pending</option>
                                                                            <option value="Approve">Approve</option>
                                                                            <option value="Reject">Reject</option>
                                                                        </select>
                                                                    </div>
                                                                    <div id="issueDetailSection" class="hidden">
                                                                        <div class="form-group">
                                                                            <label for="issue_detail">Issue Detail<strong><span style="color: red;">*</span></strong></label>
                                                                            <textarea class="form-control" id="issue_detail" name="issue_detail"></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="pic">PIC<strong><span style="color: red;">*</span></strong></label>
                                                                            <select class="form-control" id="pic" name="pic">
                                                                                <option value="">Pilih PIC</option>
                                                                                <option value="Legal">Legal</option>
                                                                                <option value="Marketing">Marketing</option>
                                                                                <option value="Landlord">Landlord</option>
                                                                                <option value="Scm">SCM</option>
                                                                                <option value="Sdg-project">SDG Project</option>
                                                                                <option value="Sdg-design">SDG Design</option>
                                                                                <option value="Sdg-equipment">SDG Equipment</option>
                                                                                <option value="Sdg-qs">SDG QS</option>
                                                                                <option value="Operations">Operations</option>
                                                                                <option value="Procurement">Procurement</option>
                                                                                <option value="Taf">TAF</option>
                                                                                <option value="HR">HR</option>
                                                                                <option value="Academy">Academy</option>
                                                                                <option value="Negotiator">Negotiator</option>
                                                                                <option value="Others">Others</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="action_plan">Action Plan<strong><span style="color: red;">*</span></strong></label>
                                                                            <textarea class="form-control" id="action_plan" name="action_plan"></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="kronologi">Upload File Kronologi<strong><span style="color: red;">*</span></strong></label>
                                                                            <input type="file" class="form-control" id="kronologi" name="kronologi[]" multiple>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
												<th>Luas Area</th>
                                                <th>No Telepon</th>
                                                <th>Lampiran</th>
                                                <th>Status Confirm to BoD</th>
                                                <th>SLA</th>
												<th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <!-- Modal Konfirmasi Hapus -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Data</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus data ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <form method="POST" action="land-sourcing-delete.php">
                                                        <input type="hidden" name="id" id="delete" value="">
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                    <!-- end of col-->
                </div>
                <!-- end of row-->
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
    <script src="../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../dist-assets/js/scripts/script.min.js"></script>
    <script src="../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../dist-assets/js/scripts/customizer.script.min.js"></script>
    <script src="../dist-assets/js/plugins/datatables.min.js"></script>
    <script src="../dist-assets/js/scripts/datatables.script.min.js"></script>
	<script src="../dist-assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="../dist-assets/js/icons/feather-icon/feather-icon.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>   
    <style>
        .hidden {
            display: none;
        }
    </style>
<script>
$(document).ready(function() {
    // Saat tombol edit diklik
    $('.edit-btn').click(function() {
        // Ambil data-id dari tombol edit
        var id = $(this).data('id');

        // Isi nilai input tersembunyi dengan ID yang diambil
        $('#modalKodeLahan').val(id);
    });

    // Function to toggle the visibility of issue detail section
    function toggleIssueDetail() {
        var statusSelect = document.getElementById("statusSelect");
        var issueDetailSection = document.getElementById("issueDetailSection");

        if (statusSelect.value === "Pending") {
            issueDetailSection.style.display = "block";
        } else {
            issueDetailSection.style.display = "none";
        }
    }

    // Event listener for statusSelect change
    $('#statusSelect').on('change', function() {
        toggleIssueDetail();
    });

    // Call toggleIssueDetail on document ready to set initial state
    toggleIssueDetail();
});

// Show modal if status_approvre is 'Pending'
<?php if ($status_approvre == 'Pending') { ?>
    $(document).ready(function() {
        $('#editModal').modal('show');
    });
<?php } ?>
</script>

    <script>
$(document).ready(function() {
    $(".edit-btn").click(function() {
        // Sembunyikan semua form yang terbuka
        $(".status-form").hide();
        // Tampilkan form di samping tombol edit yang diklik
        $(this).next(".status-form").show();
    });
});
</script>
    <script>
        // Fungsi untuk mengatur id data yang akan dihapus ke dalam modal
        function setDelete(element) {
            var id = element.id;
            document.getElementById('delete').value = id;
        }
    </script>
</body>

</html>