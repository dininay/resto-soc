<?php
// Koneksi ke database
include "../koneksi.php";

// Inisialisasi variabel $data dengan array kosong
$data = [];
if (isset($_GET['id'])) {
    $kode_lahan = $_GET['id'];

    // Query untuk mengambil data dari tabel crew
    $sql = "SELECT crewfl.*, socdate_hraca.sla_flaca FROM crewfl INNER JOIN socdate_hraca ON socdate_hraca.kode_lahan = crewfl.kode_lahan WHERE crewfl.kode_lahan = ? AND status_lolos = 'Siap Training'";
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

$sla_query = "SELECT sla FROM master_slacons WHERE divisi = 'hrga_tm'";
$sla_result = $conn->query($sla_query);

$sla_value = 0; // Default SLA value

if ($sla_result->num_rows > 0) {
    $row = $sla_result->fetch_assoc();
    $sla_value = $row['sla'];
} else {
    echo "No SLA value found for 'hrga_tm'";
}

// Fungsi untuk menghitung scoring
function calculateScoring($start_date, $sla_date, $sla) {
    $start_date_obj = new DateTime($start_date);
    $sla_date_obj = new DateTime($sla_date);
    
    if ($start_date_obj <= $sla_date_obj) {
        return 100; // Skor 100 jika start_date tidak melebihi sla_date
    }

    $date_diff = $start_date_obj->diff($sla_date_obj)->days;
    $sla_days = $sla ?: 0;

    if ($sla_days != 0) {
        if ($date_diff > $sla_days) {
            $scoring = -((($date_diff - $sla_days) / $sla_days) * 100);
        } else {
            $scoring = ((($sla_days - $date_diff) / $sla_days) * 100);
        }
    } else {
        $scoring = 0;
    }

    return round($scoring, 2);
}

// Fungsi untuk menentukan remarks berdasarkan scoring
function getRemarks($scoring) {
    if ($scoring >= 75) {
        return "good";
    } elseif ($scoring >= 0) {
        return "poor";
    } else {
        return "bad";
    }
}

// Fungsi untuk menentukan warna badge berdasarkan remarks
function getBadgeColor($remarks) {
    switch ($remarks) {
        case 'good':
            return 'success'; // Hijau
        case 'poor':
            return 'warning'; // Kuning
        case 'bad':
            return 'danger'; // Merah
        default:
            return 'secondary'; // Default jika remarks tidak dikenali
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
                    <h1>Data Crew FL</h1>
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
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Gender</th>
                                                <th>Birth Of Date</th>
                                                <th>Alamat</th>
                                                <th>Usia</th>
                                                <th>Status Lolos</th>
                                                <th>Score OJE</th>
                                                <th>Status OJE</th>
                                                <th>Day 1 Onboarding</th>
                                                <th>Completion Rate</th>
                                                <th>SLA</th>
                                                <th>Status Akhir</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <td><?= $row['no'] ?></td>
                                                <td><?= $row['nama'] ?></td>
                                                <td><?= $row['gender'] ?></td>
                                                <td><?= date('d M Y', strtotime($row['ttl'])) ?></td>
                                                <td><?= $row['alamat'] ?></td>
                                                <td><?= $row['usia'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_lolos']) {
                                                            case 'Siap Training':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Cancel':
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
                                                        <?php echo $row['status_lolos']; ?>
                                                    </span>
                                                </td> 
                                                <td><?= $row['score_oje'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_oje']) {
                                                            case 'Lolos':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Tidak Lolos':
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
                                                        <?php echo $row['status_oje']; ?>
                                                    </span>
                                                </td>    
                                                <td>
                                                    <?= (!empty($row['onboarding_date']) && $row['onboarding_date'] != '0000-00-00') ? date('d M Y', strtotime($row['onboarding_date'])) : '' ?>
                                                </td>
                                                <td><?= $row['comprate'] ?>%</td>
                                                <td>
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta (sesuaikan dengan timezone lokal Anda)
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    $start_date = $row['acafl_date'];
                                                    $sla_date = $row['sla_flaca'];
                                                    $status_last = $row['status_last'];

                                                    // Menghitung scoring
                                                    $scoring = calculateScoring($start_date, $sla_date, $sla_value);
                                                    $remarks = getRemarks($scoring);

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    if ($status_last === 'Done' || $status_last === 'Resign') {
                                                        // Menentukan label berdasarkan remarks
                                                        $status_label = '';
                                                        switch ($remarks) {
                                                            case 'good':
                                                                $status_label = 'Done Good';
                                                                break;
                                                            case 'poor':
                                                                $status_label = 'Done Poor';
                                                                break;
                                                            case 'bad':
                                                                $status_label = 'Done Bad';
                                                                break;
                                                        }

                                                        echo '<button type="button" class="btn btn-sm btn-' . getBadgeColor($remarks) . '" data-toggle="modal" data-target="#approvalModal">' . $status_label . '</button>';
                                                    } else {
                                                        // Memeriksa apakah waktu sekarang di luar jam kerja
                                                        if ($current_time < $work_start || $current_time > $work_end) {
                                                            echo '<button type="button" class="btn btn-sm btn-info">Di Luar Jam Kerja</button>';
                                                        } else {
                                                            // Convert $sla_date to DateTime object
                                                            $sla_date_obj = new DateTime($sla_date);

                                                            // Menghitung jumlah hari menuju SLA date
                                                            $diff = $now->diff($sla_date_obj);
                                                            $daysDifference = (int)$diff->format('%R%a'); // Menyertakan tanda plus atau minus

                                                            if ($daysDifference < 0) {
                                                                // SLA telah terlewat, hitung sebagai hari terlambat
                                                                echo '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#lateApprovalModal">Terlewat ' . abs($daysDifference) . ' hari</button>';
                                                            } else {
                                                                // SLA belum tercapai, hitung mundur
                                                                echo '<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#deadlineModal">H - ' . $daysDifference . '</button>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_last']) {
                                                            case 'Done':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'In Process':
                                                                $badge_color = 'primary';
                                                                break;
                                                                case 'Resign':
                                                                    $badge_color = 'danger';
                                                                    break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status tidak dikenali
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $row['status_last']; ?>
                                                    </span>
                                                </td>    
                                                <td>
                                                <!-- Tombol Edit -->
                                                    <button class="btn btn-sm btn-warning edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_lolos'] ?>">
                                                    <i class="nav-icon i-Pen-2"></i></button>
                                                    
                                                    <?php if ($row['status_oje'] != "In Process"): ?>
                                                    <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal2" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_lolos'] ?>">
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
                                                                <form id="statusForm" method="post" action="academy/datacrewfla-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" value=<?= $row['id'] ?> id="modalKodeLahan">
                                                                    <div class="form-group">
                                                                        <label for="score_oje">Score OJE<strong><span style="color: red;">*</span></strong></label>
                                                                        <input type="text" class="form-control" id="score_oje" name="score_oje">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status OJE<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="status_oje" Placeholder="Pilih">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Tidak Lolos">Tidak Lolos</option>
                                                                            <option value="Lolos">Lolos</option>
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
                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal2" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="academy/datacrewflb-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" value=<?= $row['id'] ?> id="modalId">
                                                                    <div class="form-group">
                                                                        <label for="onboarding_date">Day 1 Training</label>
                                                                        <input type="date" class="form-control" id="onboarding_date" name="onboarding_date">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Academy</label>
                                                                        <select class="form-control" id="statusSelect" name="status_last" Placeholder="Pilih">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Done">Done</option>
                                                                            <option value="Resign">Resign</option>
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
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Gender</th>
                                                <th>Birth Of Date</th>
                                                <th>Alamat</th>
                                                <th>Usia</th>
                                                <th>Status Lolos</th>
                                                <th>Score OJE</th>
                                                <th>Status OJE</th>
                                                <th>Day 1 Onboarding</th>
                                                <th>Completion Rate</th>
                                                <th>SLA</th>
                                                <th>Status Akhir</th>
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
    $('#statusSelect').on('change', function () {
        toggleIssueDetail();
    });
</script>
    <script>
    $(document).ready(function(){
        // Saat tombol edit diklik
        $('.edit-btn').click(function(){
            // Ambil data-id dari tombol edit
            var id = $(this).data('id');

            // Isi nilai input tersembunyi dengan ID yang diambil
            $('#modalId').val(id);
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

    // Event listener for statusSelect change
    $('#statusSelect').on('change', function () {
        toggleIssueDetail();
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