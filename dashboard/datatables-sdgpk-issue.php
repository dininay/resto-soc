<?php
// Koneksi ke database
include "../koneksi.php";

// Query untuk mengambil data dari tabel land
$sql = "SELECT
resto.kode_lahan,
land.nama_lahan,
dokumen_loacd.kode_store,
issue.*,
resto.rto_date
FROM resto
JOIN issue on resto.kode_lahan = issue.kode_lahan
JOIN land on resto.kode_lahan = land.kode_lahan
JOIN dokumen_loacd on resto.kode_lahan = dokumen_loacd.kode_lahan
JOIN socdate_sdg on resto.kode_lahan = socdate_sdg.kode_lahan
WHERE issue.status_defect in ('In Process', 'Done')";
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


$sla_query = "SELECT sla FROM master_sla WHERE divisi = 'Konstruksi'";
$sla_result = $conn->query($sla_query);

$sla_value = 0; // Default SLA value

if ($sla_result->num_rows > 0) {
    $row = $sla_result->fetch_assoc();
    $sla_value = $row['sla'];
} else {
    echo "No SLA value found for 'Konstruksi'";
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
                    <h1>Issue List Defect by Contractor</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
                                    <p><a class="btn btn-primary btn-icon m-1" href="sdg-pk/issue-form.php">+ add Issue</a></p>
									<!-- <p><a class="btn btn-primary btn-icon m-1" href="sdg-pk/issue-form.php">+ add Issue</a></p> -->
									<p>
									  <span class="flex-grow-1"></span></p>
								</div>
                                <p>
							  <div class="table-responsive">
                                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lahan</th>
                                                <th>PIC</th>
                                                <th>Detail Issue</th>
                                                <th>Target Done</th>
                                                <th>Lampiran Before</th>
                                                <th>Tanggal Penyelesaian</th>
                                                <th>Lampiran After</th>
                                                <th>Status</th>
                                                <th>SLA</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td><?= $row['kode_lahan'] ?></td>
                                            <td><?= $row['kode_store'] ?></td>
                                            <td><?= $row['nama_lahan'] ?></td>
                                            <td><?= $row['pic'] ?></td>
                                            <td><?= $row['issue'] ?></td>
                                            <td>
                                                <?php if (!empty($row['target_done'])): ?>
                                                    <?php
                                                    $date = new DateTime($row['target_done']);
                                                    $formattedDate = $date->format('d M y');
                                                    ?>
                                                    <?= $formattedDate ?>
                                                <?php else: ?>
                                                    <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                    <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                    <!-- <span>Not Available</span> -->
                                                <?php endif; ?>
                                            </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $file_files = explode(",", $row['lamp_defect']); // Pisahkan nama lamp_defect menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_defect'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($file_files as $file) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $file . '" target="_blank">
                                                                    <i class="fas fa-file-pdf nav-icon"></i>
                                                                </a>
                                                            </li>';
                                                    }
                                                    echo '</ul>
                                                        </td>';
                                                } else {
                                                    // Jika kolom kosong, tampilkan kolom kosong untuk menjaga tata letak tabel
                                                    echo '<td></td>';
                                                }
                                                ?>
                                            <td>
                                                <?php if (!empty($row['tanggal_retensi'])): ?>
                                                    <?php
                                                    $date = new DateTime($row['tanggal_retensi']);
                                                    $formattedDate = $date->format('d M y');
                                                    ?>
                                                    <?= $formattedDate ?>
                                                <?php else: ?>
                                                    <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                    <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                    <!-- <span>Not Available</span> -->
                                                <?php endif; ?>
                                            </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $file_files = explode(",", $row['lamp_badefect']); // Pisahkan nama lamp_badefect menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_badefect'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($file_files as $file) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $file . '" target="_blank">
                                                                    <i class="fas fa-file-pdf nav-icon"></i>
                                                                </a>
                                                            </li>';
                                                    }
                                                    echo '</ul>
                                                        </td>';
                                                } else {
                                                    // Jika kolom kosong, tampilkan kolom kosong untuk menjaga tata letak tabel
                                                    echo '<td></td>';
                                                }
                                                ?>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_defect']) {
                                                            case 'Done':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Urgent':
                                                                $badge_color = 'danger';
                                                                break;
                                                            case 'In Process':
                                                                $badge_color = 'primary';
                                                                break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status_defect tidak dikenali
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $row['status_defect']; ?>
                                                    </span>
                                                </td>       
                                                <td>
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta (sesuaikan dengan timezone lokal Anda)
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    $start_date = $row['defect_date'];
                                                    $sla_date = $row['rto_date'];
                                                    $status_defect = $row['status_defect'];

                                                    // Menghitung scoring
                                                    $scoring = calculateScoring($start_date, $sla_date, $sla_value);
                                                    $remarks = getRemarks($scoring);

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    if ($status_defect === 'Done') {
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
                                                    <!-- Tombol Edit -->
                                                    <?php if ($row['status_defect'] != "Done"): ?>
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta (sesuaikan dengan timezone lokal Anda)
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    if ($row['status_defect'] != "Done") {
                                                        echo '<a href="sdg-pk/issue-edit-form.php?id='. $row['id'] .'" class="btn btn-sm btn-warning mr-2">
                                                            <i class="nav-icon i-Pen-2"></i>
                                                        </a>';
                                                        echo '<button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="'. $row['id'] .'" data-status="'.$row['status_defect'] .'">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button>';
                                                    }
                                                    ?>
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
                                                                <form id="statusForm" method="post" action="sdg-pk/issue-process.php">
                                                                    <input type="hidden" name="id" id="modalKodeLahan">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Defect List<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="status_defect">
                                                                            <option value="Done">Done</option>
                                                                            <option value="In Process">In Process</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="tanggal_retensi">Tanggal Penyelesaian<strong><span style="color: red;">*</span></strong></label>
                                                                        <input type="date" class="form-control" id="tanggal_retensi" name="tanggal_retensi">
                                                                    </div>
                                                                        <div class="form-group">
                                                                            <label for="lamp_badefect">Upload Lampiran After<strong><span style="color: red;">*</span></strong></label>
                                                                            <input type="file" class="form-control" id="lamp_badefect" name="lamp_badefect[]" multiple>
                                                                        </div>
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </tr>
                                            
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lahan</th>
                                                <th>PIC</th>
                                                <th>Detail Issue</th>
                                                <th>Target Done</th>
                                                <th>Lampiran Before</th>
                                                <th>Tanggal Penyelesaian</th>
                                                <th>Lampiran After</th>
                                                <th>Status</th>
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
                                                    <form method="POST" action="re/land-sourcing-delete.php">
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
    <script>
    // JavaScript to handle opening the modal and setting form values
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var kodeLahan = button.data('id'); // Extract info from data-* attributes
        var status = button.data('status'); // Extract status

        // Update the modal's content.
        var modal = $(this);
        modal.find('#modalKodeLahan').val(kodeLahan);
        modal.find('#statusSelect').val(status);
    });
</script>
    <script>
        // Fungsi untuk mengatur id data yang akan dihapus ke dalam modal
        function setDelete(element) {
            var id = element.id;
            document.getElementById('delete').value = id;
        }
    </script>
    <script>
        $(document).ready(function() {
            // Hancurkan DataTable jika sudah ada
            if ($.fn.DataTable.isDataTable('#zero_configuration_table')) {
                $('#zero_configuration_table').DataTable().destroy();
            }

            // Inisialisasi DataTable
            $('#zero_configuration_table').DataTable({
                scrollX: true, // Menambahkan scroll horizontal
                fixedColumns: {
                    leftColumns: 3, // Jumlah kolom yang ingin di-fix
                    topColumns: 2
                }
            });
        });
    </script>
</body>

</html>