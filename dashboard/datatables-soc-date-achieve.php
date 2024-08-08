<?php
// Koneksi ke database
include "../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["valdoc_legal"])) {
    $valdoc_legal = $_POST["valdoc_legal"];
    $kode_lahan = $_POST["kode_lahan"];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui valdoc_legal berdasarkan id
        $sql_update = "UPDATE draft SET valdoc_legal = ? WHERE kode_lahan = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ss", $valdoc_legal, $kode_lahan);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika valdoc_legal diubah menjadi Approve
            if ($valdoc_legal == 'Approve') {
                // Ambil data dari tabel draft berdasarkan id yang diedit
                $sql_select = "SELECT kode_lahan, confirm_nego FROM draft WHERE kode_lahan = ?";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->bind_param("s", $kode_lahan);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                if ($row = $result_select->fetch_assoc()) {
                    // Masukkan data ke tabel resto
                    $sql_insert = "INSERT INTO resto (kode_lahan, status_finallegal) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $status_finallegal = "In Process";
                    $stmt_insert->bind_param("ss", $row['kode_lahan'], $status_finallegal);
                    $stmt_insert->execute();
                } else {
                    // Rollback transaksi jika terjadi kesalahan pada select
                    $conn->rollback();
                    echo "Error: Data not found for id: $kode_lahan.";
                    exit;
                }
            }
            // Komit transaksi
            $conn->commit();
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Query untuk mengambil data dari tabel land
$sql = "SELECT land.id, land.kode_lahan, land.nama_lahan, land.lokasi, land.lamp_land, 
resto.*,
summary_soc.*,
draft.end_date AS draft_end_date,
dokumen_loacd.*
FROM land
JOIN draft ON land.kode_lahan = draft.kode_lahan
LEFT JOIN resto ON land.kode_lahan = resto.kode_lahan
LEFT JOIN summary_soc ON resto.kode_lahan = summary_soc.kode_lahan
LEFT JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
GROUP BY summary_soc.kode_lahan
";
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
// Fungsi untuk menentukan remarks berdasarkan perbandingan tanggal
function getStatusRemarks($spk_date, $sla_spk) {
    if ($spk_date === $sla_spk) {
        return "meet";
    } elseif ($spk_date > $sla_spk) {
        return "delayed";
    } else {
        return "good";
    }
}

// Fungsi untuk menentukan warna badge berdasarkan remarks
function getStatusBadgeColor($remarks) {
    switch ($remarks) {
        case 'good':
            return 'success'; // hijau
        case 'delayed':
            return 'danger'; // merah
        case 'meet':
            return 'warning'; // kuning
        default:
            return 'secondary';
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
	<link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icofont/1.0.1/css/icofont.min.css">
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
                    <h1>Update Information All Division</h1>
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
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
                                                <th>Final VD</th>
                                                <th>Final SPK</th>
                                                <th>Actual SPK</th>
                                                <th>Ach Final Tender (%)</th>
                                                <th>Target Kick Off Meeting</th>
                                                <th>Actual Kick Off</th>
                                                <th>Ach Kick Off (%)</th>
                                                <th>Target RTO</th>
                                                <th>Actual RTO</th>
                                                <th>Ach RTO (%)</th>
                                                <th>Target GO</th>
                                                <th>Actual GO</th>
                                                <th>Ach GO (%)</th>
                                                <th>Final Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <?php
                                                // Menetapkan nilai status berdasarkan perbandingan antara spk_date dan sla_spk
                                                $status = '';
                                                $badge_color = '';
                                                if (!empty($row['spk_date']) && !empty($row['sla_spk'])) {
                                                    $status = getStatusRemarks($row['spk_date'], $row['sla_spk']);
                                                    $badge_color = getStatusBadgeColor($status);
                                                }

                                                ?>
                                                <td><?= $row['kode_lahan'] ?></td>
                                                <td><?= $row['kode_store'] ?></td>
                                                <td><?= $row['nama_lahan'] ?></td>
                                                <td><?= $row['lokasi'] ?></td>
                                                <td>
                                                    <?php if (!empty($row['draft_end_date'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['draft_end_date']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['sla_spk'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['sla_spk']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['spk_date'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['spk_date']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['sla_kom'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['sla_kom']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['kom_date'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['kom_date']);
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
                                                $status = '';
                                                $badge_color = '';
                                                if (!empty($row['kom_date']) && !empty($row['sla_kom'])) {
                                                    $status = getStatusRemarks($row['kom_date'], $row['sla_kom']);
                                                    $badge_color = getStatusBadgeColor($status);
                                                }
                                                ?>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <?php
                                                $target_rto = !empty($row['gostore_date']) ? date('d M y', strtotime($row['gostore_date'] . ' -' . 3 . ' days')) : '';
                                                ?>
                                                <td><?= $target_rto ?></td>
                                                <td>
                                                    <?php if (!empty($row['rto_act'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['rto_act']);
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
                                                $status = '';
                                                $badge_color = '';
                                                if (!empty($row['rto_act']) && !empty($target_rto)) {
                                                    $status = getStatusRemarks($row['rto_act'], $target_rto);
                                                    $badge_color = getStatusBadgeColor($status);
                                                }
                                                ?>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['gostore_date'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['gostore_date']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['go_fix'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['go_fix']);
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
                                                $status = '';
                                                $badge_color = '';
                                                if (!empty($row['go_fix']) && !empty($row['gostore_date'])) {
                                                    $status = getStatusRemarks($row['go_fix'], $row['gostore_date']);
                                                    $badge_color = getStatusBadgeColor($status);
                                                }
                                                ?>
                                                <td>
                                                    <span class="badge rounded-pill badge-<?= $badge_color ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <?php
                                                $final_status = '';
                                                if (!empty($row['draft_end_date']) && (empty($row['spk_date']))) {
                                                    $final_status = 'In Preparation';
                                                    $final_badge_color = 'badge-warning'; 
                                                } elseif (empty($row['actual_spk']) && (empty($row['spk_date']) || empty($row['sla_spk']) || empty($row['kom_date']) || empty($row['sla_kom']) || empty($row['rto_act']) || empty($row['go_fix']))) {
                                                        $final_status = 'In Progress';
                                                        $final_badge_color = 'badge-primary'; 
                                                } else {
                                                    $final_status = 'Done';
                                                    $final_badge_color = 'badge-success'; 
                                                }
                                                ?>
                                                <td>
                                                    <span class="badge rounded-pill <?= $final_badge_color ?>">
                                                        <?= $final_status ?>
                                                    </span>
                                                </td>
                                                </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
                                                <th>Final VD</th>
                                                <th>Final SPK</th>
                                                <th>Actual SPK</th>
                                                <th>Ach Final Tender (%)</th>
                                                <th>Target Kick Off Meeting</th>
                                                <th>Actual Kick Off</th>
                                                <th>Ach Kick Off (%)</th>
                                                <th>Target RTO</th>
                                                <th>Actual RTO</th>
                                                <th>Ach RTO (%)</th>
                                                <th>Target GO</th>
                                                <th>Actual GO</th>
                                                <th>Ach GO (%)</th>
                                                <th>Final Status</th>
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
            // Hancurkan DataTable jika sudah ada
            if ($.fn.DataTable.isDataTable('#zero_configuration_table')) {
                $('#zero_configuration_table').DataTable().destroy();
            }

            // Inisialisasi DataTable
            $('#zero_configuration_table').DataTable({
                scrollX: true, // Menambahkan scroll horizontal
                fixedColumns: {
                    leftColumns: 3 // Jumlah kolom yang ingin di-fix
                }
            });
        });
    </script>
</body>

</html>