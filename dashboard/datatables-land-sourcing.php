<?php
// Koneksi ke database
include "../koneksi.php";

$status_approvre = "";
// Query untuk mengambil data dari tabel land
$sql = "SELECT 
land.*, re.status_approvowner, re.start_date, re.sla_date FROM land Left JOIN re ON re.kode_lahan = land.kode_lahan
where status_land = 'Aktif'";
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
$sla_query = "SELECT sla FROM master_sla WHERE divisi = 'Owner Surveyor'";
$sla_result = $conn->query($sla_query);

$sla_value = 0; // Default SLA value

if ($sla_result->num_rows > 0) {
    $row = $sla_result->fetch_assoc();
    $sla_value = $row['sla'];
} else {
    echo "No SLA value found for 'Owner Surveyor'";
}

// Fungsi untuk menghitung scoring
function calculateScoring($start_date, $sla_date, $sla) {
    $today = new DateTime();
    $start_date = $start_date ?: $today->format('Y-m-d');
    $sla_date = $sla_date ?: $today->format('Y-m-d');
    $sla_days = $sla ?: 0;

    $start_date_obj = new DateTime($start_date);
    $sla_date_obj = new DateTime($sla_date);

    $date_diff = $sla_date_obj->diff($start_date_obj)->days + 1;

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
function getRemarks($start_date, $sla_date, $sla) {
    if (new DateTime($start_date) <= new DateTime($sla_date)) {
        return "good";
    } else {
        $scoring = calculateScoring($start_date, $sla_date, $sla);
        if ($scoring >= 0) {
            return "good";
        } elseif ($scoring >= -30) {
            return "poor";
        } else {
            return "bad";
        }
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
    <link href="../dist-assets/css/plugins/datatables.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../dist-assets/css/feather-icon.css">
    <link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.2/css/fixedColumns.dataTables.min.css">
    <!-- Muat jQuery terlebih dahulu -->
    <!-- Muat DataTables setelah jQuery -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
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
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
        
    </style>
    <style>
        
    </style>
    <!-- <style>
        div.dataTables_wrapper {
            width: 100%;
                margin: 0 auto;
        }

        .table-scroll {
            overflow-y: auto;
            max-height: 400px; /* Sesuaikan dengan tinggi yang diinginkan */
            margin-bottom: 1rem;
        }

        .table-scroll table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-scroll thead {
            position: -webkit-sticky; /* Safari */
            position: sticky;
            top: 0;
            background: #fff; /* Sesuaikan dengan warna background tabel */
            z-index: 1;
        }

        div.dataTables_scrollHead table.dataTable {
            margin-bottom: 0 !important;
        }

        div.dataTables_scrollBody table {
            border-top: none;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        div.dataTables_scrollBody table thead .sorting:after,
        div.dataTables_scrollBody table thead .sorting_asc:after,
        div.dataTables_scrollBody table thead .sorting_desc:after {
            display: none;
        }

        div.dataTables_scrollBody table tbody tr:first-child th,
        div.dataTables_scrollBody table tbody tr:first-child td {
            border-top: none;
        }

        div.dataTables_scrollFoot>.dataTables_scrollFootInner {
            box-sizing: content-box;
        }

        div.dataTables_scrollFoot>.dataTables_scrollFootInner>table {
            margin-top: 0 !important;
            border-top: none;
        }
    </style> -->
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
                    <h1>Datatables Land Sourcing</h1>
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
							    <div class="table-responsive">
                                    <div class="table-scroll">
                                        
                                    <table class="display table table-striped table-bordered display nowrap" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Activate Date</th>
                                                <th>Inventory Code</th>
                                                <th>Kota</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
												<th>Luas Area</th>
                                                <th>No Telepon</th>
                                                <th>Maps</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>Harga Sewa</th>
                                                <th>Minimum Tahun Sewa</th>
                                                <th>Lampiran</th>
                                                <th>Status BoD</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                        <tr>
                                            <?php
                                            if (!empty($row['bp_date'])) {
                                                $date = new DateTime($row['bp_date']);
                                                $formattedDate = $date->format('d M y'); // Format menjadi 12 Aug 24
                                                echo "<td>$formattedDate</td>";
                                            } else {
                                                echo "<td></td>"; // Jika tanggal kosong, tampilkan sel kosong
                                            }
                                            ?>
                                            <td><?= $row['kode_lahan'] ?></td>
                                            <td><?= $row['city'] ?></td>
                                            <td><?= $row['nama_lahan'] ?></td>
                                            <td><?= $row['lokasi'] ?></td>
                                            <td><?= $row['luas_area'] ?></td>
                                            <td><?= $row['no_tlp'] ?></td>
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
                                            <td><?= $row['latitude'] ?></td>
                                            <td><?= $row['longitude'] ?></td>
                                            <td><?= $row['harga_sewa'] ?></td>
                                            <td><?= $row['mintahun_sewa'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_loacd_files = explode(",", $row['lamp_land']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_land'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_loacd_files as $file) {
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
                                                    $badge_color = '';
                                                    $badge_text = '';
                                                    $status_approvowner = $row['status_approvowner'];
                                                    
                                                    if ($status_approvowner === 'Approve') {
                                                        // Tentukan remarks berdasarkan SLA dan tanggal
                                                        $remarks = getRemarks($row['start_date'], $row['sla_date'], $sla_value);

                                                        // Tentukan warna badge dan teks berdasarkan remarks
                                                        $badge_color = getBadgeColor($remarks);
                                                        $badge_text = 'Approve ' . ucfirst($remarks) ;
                                                    } else {
                                                        // Warna untuk status selain 'Approve'
                                                        switch ($status_approvowner) {
                                                            case 'Pending':
                                                                $badge_color = 'danger';
                                                                $badge_text = 'Pending';
                                                                break;
                                                            case 'In Process':
                                                                $badge_color = 'warning';
                                                                $badge_text = 'In Process';
                                                                break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status tidak dikenali
                                                                $badge_text = 'Unknown Status'; // Teks default jika status tidak dikenali
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $badge_text; ?>
                                                    </span>
                                                </td>

                                            <td>
                                            <!-- Tombol Edit -->
                                            <a href="re/land-sourcing-edit-form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning mb-2">
                                                <i class="i-Pen-2"></i>
                                            </a>
                                            
                                            <!-- <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id'] ?>" data-status="<?= $row['status_approvre'] ?>">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button> -->
                                                <!-- Tombol Hapus -->
                                            <!-- <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal" id="<?php echo $row['id']; ?>" onclick="setDelete(this)">
                                                <i class="nav-icon i-Close-Window"></i>
                                            </button> -->
                                            </td>
                                        </tr>
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
                                                                        <label for="statusSelect">Submit to BoD</label>
                                                                        <select class="form-control" id="statusSelect" name="status_approvre">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Pending">Pending</option>
                                                                            <option value="Approve">Approve</option>
                                                                            <option value="Reject">Reject</option>
                                                                        </select>
                                                                    </div>
                                                                    <div id="issueDetailSection" class="hidden">
                                                                        <div class="form-group">
                                                                            <label for="issue_detail">Issue Detail</label>
                                                                            <textarea class="form-control" id="issue_detail" name="issue_detail"></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="pic">PIC</label>
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
                                                                            <label for="action_plan">Action Plan</label>
                                                                            <textarea class="form-control" id="action_plan" name="action_plan"></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="kronologi">Upload File Kronologi</label>
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
                                                <th>Activate Date</th>
                                                <th>Inventory Code</th>
                                                <th>Kota</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
												<th>Luas Area</th>
                                                <th>No Telepon</th>
                                                <th>Maps</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>Harga Sewa</th>
                                                <th>Minimum Tahun Sewa</th>
                                                <th>Lampiran</th>
                                                <th>Status BoD</th>
												<th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>    
                                    </div>
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
    <script>
$(document).ready(function() {
    // Destroy existing DataTable instance if it exists
    if ($.fn.DataTable.isDataTable('#zero_configuration_table')) {
        $('#zero_configuration_table').DataTable().destroy();
    }

    // Initialize DataTable
    var table = $('#zero_configuration_table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        fixedColumns: {
            leftColumns: 3 // Number of fixed columns
        } // Fix the header to the top
    });

    // // Adjust the visibility of the columns in the `thead` and `tfoot` based on the DataTable's columns
    // function adjustHeaderFooter() {
    //     var columns = table.columns().visible(); // Get the visibility of all columns
    //     $('#zero_configuration_table thead th').each(function(index) {
    //         // Toggle visibility of the `thead` based on the column visibility
    //         $(this).toggle(columns[index]);
    //     });
    //     $('#zero_configuration_table tfoot th').each(function(index) {
    //         // Toggle visibility of the `tfoot` based on the column visibility
    //         $(this).toggle(columns[index]);
    //     });
    // }

    // // Adjust header/footer visibility on DataTable draw
    // table.on('draw', function() {
    //     adjustHeaderFooter();
    // });

    // // Call the adjustHeaderFooter function initially after DataTable initialization
    // setTimeout(adjustHeaderFooter, 100);

    // // Add event listener for window resize to adjust header/footer visibility
    // $(window).on('resize', function() {
    //     adjustHeaderFooter();
    // });
});
</script>
</body>

</html>