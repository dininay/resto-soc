<?php
// Koneksi ke database
include "../koneksi.php";

// Inisialisasi variabel $data dengan array kosong
$data = [];
if (isset($_GET['id'])) {
    $kode_lahan = $_GET['id'];

    // Query untuk mengambil data dari tabel crew
    $sql = "SELECT * from utensil WHERE kode_lahan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_lahan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        echo "";
    }
} else {
    echo "Kode Lahan tidak ditentukan.";
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
    <style>
        .hidden {
            display: none;
        },

        .small-column {
            max-width: 300px; /* Atur lebar maksimum sesuai kebutuhan */
            overflow: hidden; /* Memotong konten yang meluas */
            text-overflow: ellipsis; /* Menampilkan elipsis jika konten terlalu panjang */
            white-space: nowrap; /* Mencegah teks membungkus ke baris baru */
        }

        th, td {
                white-space: nowrap;
            }
        table.dataTable {
            border-collapse:  collapse!important;
        }
    </style>
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
                    <h1>Data SCM Utensil</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
									<!-- <p><a class="btn btn-primary btn-icon m-1" href="fat/fat-from.php">+ add Data </a></p> -->
									<p>
									  <span class="flex-grow-1"></span></p>
								</div>
                                <p>
							  <div class="table-responsive">
                                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Kode Utensil</th>
                                                <th>Item Name</th>
                                                <th>PIC</th>
                                                <th>Satuan</th>
                                                <th>QTY Target</th>
                                                <th>QTY Arrival</th>
                                                <th>Mandatory</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <td><?= $row['kode_utensil'] ?></td>
                                                <td><?= $row['item_name'] ?></td>
                                                <td><?= $row['pic'] ?></td>
                                                <td><?= $row['satuan'] ?></td>
                                                <td><?= $row['qty_target'] ?></td>
                                                <td><?= $row['qty_arrival'] ?></td>
                                                <td><?= $row['mandatory'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_utensil']) {
                                                            case 'Receive In Store':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Progress Utensil Receive':
                                                                $badge_color = 'danger';
                                                                break;
                                                            case 'In Process':
                                                                $badge_color = 'primary';
                                                                break;
                                                            case 'Pending In Procurement':
                                                                $badge_color = 'warning';
                                                            break;
                                                            case 'Pending In IT':
                                                                $badge_color = 'warning';
                                                            break;
                                                            case 'Pending In HRGA':
                                                                $badge_color = 'warning';
                                                            break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status tidak dikenali
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $row['status_utensil']; ?>
                                                    </span>
                                                </td> 
                                                <td>
                                                <!-- Tombol Edit -->
                                                    <?php if ($row['status_utensil'] != "Receive In Store"): ?>
                                                    <button class="btn btn-sm btn-warning edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_utensil'] ?>" data-kode="<?= $row['kode_utensil'] ?>" data-qty="<?= $row['qty_target'] ?>">
                                                    <i class="nav-icon i-Pen-2"></i></button>

                                                    <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModalb" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_utensil'] ?>" data-kode="<?= $row['kode_utensil'] ?>">
                                                    <i class="nav-icon i-Book"></i></button>
                                                    <?php endif; ?>
                                                </td>
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
                                                                <form id="statusForm" method="post" action="scm/datascma-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" value=<?= $row['id'] ?> id="modalKodeLahan">
                                                                    <div class="form-group">
                                                                        <label for="kode_utensil">Kode Utensil<strong><span style="color: red;">*</strong></label>
                                                                        <input type="text" class="form-control" id="kode_utensil" name="kode_utensil">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="qty_target">QTY Target<strong><span style="color: red;">*</strong></label>
                                                                        <input type="number" class="form-control" id="qty_target" name="qty_target">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="qty_arrival">QTY Arrival<strong><span style="color: red;">*</strong></label>
                                                                        <input type="number" class="form-control" id="qty_arrival" name="qty_arrival">
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
                                                <!-- Modal -->
                                                <div class="modal fade" id="editModalb" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="scm/datascmb-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" value=<?= $row['id'] ?> id="modalId">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Utensil<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="status_utensil" Placeholder="Pilih">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Receive In Store">Receive In Store (Done)</option>
                                                                            <option value="Pending In Procurement">Pending In Procurement</option>
                                                                            <option value="Pending In IT">Pending In IT</option>
                                                                            <option value="Pending In HRGA">Pending In HRGA</option>
                                                                            <option value="Progress Utensil Receive">Progress Utensil Receive</option>
                                                                        </select>
                                                                    </div>
                                                                    <div id="receiveDetailSection" class="hidden">
                                                                        <div class="form-group">
                                                                            <label for="note_utensil">Catatan<strong><span style="color: red;">*</strong></label>
                                                                            <textarea class="form-control" id="note_utensil" name="note_utensil"></textarea>
                                                                        </div>
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
                                                <th>Kode Utensil</th>
                                                <th>Item Name</th>
                                                <th>PIC</th>
                                                <th>Satuan</th>
                                                <th>QTY Target</th>
                                                <th>QTY Arrival</th>
                                                <th>Mandatory</th>
                                                <th>Status</th>
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
                                                    <form method="POST" action="draft-sewa-delete.php">
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
            <img src="../dist-assets/images/logo.png" alt="" class="logo">
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
    <script src="../dist-assets/js/plugins/datatables.min.js"></script>
    <script src="../dist-assets/js/scripts/datatables.script.min.js"></script>
	<script src="../dist-assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="../dist-assets/js/icons/feather-icon/feather-icon.js"></script>
    <script>
    $(document).ready(function(){
        // Saat tombol edit diklik
        $('.edit-btn').click(function(){
            // Ambil data-id dari tombol edit
            var id = $(this).data('id');

            // Isi nilai input tersembunyi dengan ID yang diambil
            $('#modalKodeLahan').val(id);
        });
    });
    
    $(document).ready(function() {
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id'); // Extract id from data-* attribute
            var kode_utensil = button.data('kode'); // Extract kode_utensil from data-* attribute
            var qty_target = button.data('qty'); // Extract kode_utensil from data-* attribute

            var modal = $(this);
            
            // Set the kode_utensil value in the input field
            modal.find('#kode_utensil').val(kode_utensil);
            modal.find('#qty_target').val(qty_target);
        });
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
    
    // Function to toggle the visibility of issue detail section
    function toggleReceiveDetail() {
        var statusSelect = document.getElementById("statusSelect");
        var receiveDetailSection = document.getElementById("receiveDetailSection");

        if (statusSelect.value === "Progress Utensil Receive") {
            receiveDetailSection.style.display = "block";
        } else {
            receiveDetailSection.style.display = "none";
        }
    }

    // Event listener for statusSelect change
    $('#statusSelect').on('change', function () {
        toggleIssueDetail();
        toggleReceiveDetail();
    });
</script>
    <script>
        $(document).ready(function() {
            // Saat tombol edit diklik
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var status_utensil = $(this).data('status');
                
                // Isi nilai input tersembunyi dengan ID dan status yang diambil
                $('#modalId').val(id);
                $('#statusSelect').val(status_utensil).change(); // Set status and trigger change event
            });

            $('#editModalb').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id'); // Extract id from data-* attribute
                var status_utensil = button.data('status'); // Extract status_utensil from data-* attribute

                var modal = $(this);
                
                // Set the modalId value in the input field
                modal.find('#modalId').val(id);
                // Set the statusSelect value
                modal.find('#statusSelect').val(status_utensil);
                // Call functions to handle detail sections visibility
                toggleIssueDetail();
                toggleReceiveDetail();
            });
            
            // Function to toggle the visibility of issue detail section
            function toggleIssueDetail() {
                var statusSelect = document.getElementById("statusSelect");
                var issueDetailSection = document.getElementById("issueDetailSection");

                if (statusSelect.value === "Pending In Procurement") { // Update to correct value
                    issueDetailSection.style.display = "block";
                } else {
                    issueDetailSection.style.display = "none";
                }
            }

            // Function to toggle the visibility of receive detail section
            function toggleReceiveDetail() {
                var statusSelect = document.getElementById("statusSelect");
                var receiveDetailSection = document.getElementById("receiveDetailSection");

                if (statusSelect.value === "Progress Utensil Receive") {
                    receiveDetailSection.style.display = "block";
                } else {
                    receiveDetailSection.style.display = "none";
                }
            }

            // Event listener for statusSelect change
            $('#statusSelect').on('change', function () {
                toggleIssueDetail();
                toggleReceiveDetail();
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