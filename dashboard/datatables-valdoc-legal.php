<?php
// Koneksi ke database
include "../koneksi.php";

// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kode_lahan"]) && isset($_POST["valdoc_legal"])&& isset($_POST["catatan_valdoc"])) {
    $valdoc_legal = $_POST["valdoc_legal"];
    $kode_lahan = $_POST["kode_lahan"];
    $catatan_valdoc = $_POST["catatan_valdoc"];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui valdoc_legal berdasarkan id
        $sql_update = "UPDATE draft SET valdoc_legal = ?, catatan_valdoc = ? WHERE kode_lahan = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sss", $valdoc_legal, $catatan_valdoc, $kode_lahan);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika valdoc_legal diubah menjadi Approve
            if ($valdoc_legal == 'Approve') {
                $sql_update = "UPDATE draft SET valdoc_legal = ?, catatan_valdoc = ? WHERE kode_lahan = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sss", $valdoc_legal, $catatan_valdoc, $kode_lahan);
                }
            
            // Komit transaksi
            $conn->commit();
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        echo "Status berhasil diperbarui.";
        // Redirect ke halaman datatables-checkval-legal.php
        header("Location: datatables-valdoc-legal.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Query untuk mengambil data dari tabel land
$sql = "SELECT l.id, l.kode_lahan, d.nama_lahan, d.lokasi, d.lamp_land, l.lamp_loacd, t.lamp_draf, t.jadwal_psm, l.kode_store,
t.valdoc_legal, t.confirm_nego, s.lamp_desainplan, s.submit_legal, l.status_approvlegalvd, l.status_approvloacd, s.confirm_sdgdesain
FROM dokumen_loacd l
LEFT JOIN land d ON l.kode_lahan = d.kode_lahan
LEFT JOIN sdg_desain s ON l.kode_lahan = s.kode_lahan
LEFT JOIN draft t ON s.kode_lahan = t.kode_lahan

UNION

SELECT l.id, s.kode_lahan, d.nama_lahan, d.lokasi, d.lamp_land, l.lamp_loacd, t.lamp_draf, t.jadwal_psm, l.kode_store,
t.valdoc_legal, t.confirm_nego, s.lamp_desainplan, s.submit_legal, l.status_approvlegalvd, l.status_approvloacd, s.confirm_sdgdesain
FROM sdg_desain s
LEFT JOIN land d ON s.kode_lahan = d.kode_lahan
LEFT JOIN dokumen_loacd l ON s.kode_lahan = l.kode_lahan
LEFT JOIN draft t ON s.kode_lahan = t.kode_lahan
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
                    <h1>Datatables Validasi Document Legal</h1>
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
                                                <th>Lampiran Land</th>
                                                <th>Approval Loa CD</th>
                                                <th>Lampiran Loa CD</th>
                                                <th>Approval Legal VD</th>
                                                <th>Lampiran Draft</th>
                                                <th>Confirm Legal</th>
                                                <th>Penjadwalan PSM</th>
                                                <th>Approval SDG Design</th>
                                                <th>Lampiran Desain</th>
                                                <th>Validasi</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <td><?= $row['kode_lahan'] ?></td>
                                                <td><?= $row['kode_store'] ?></td>
                                                <td><?= $row['nama_lahan'] ?></td>
                                                <td><?= $row['lokasi'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_draf_files = explode(",", $row['lamp_land']); // Pisahkan nama file menjadi array
                                                ?>

                                                <td>
                                                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                                                        <?php foreach ($lamp_draf_files as $file): ?>
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
                                                        switch ($row['status_approvloacd']) {
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
                                                        <?php echo $row['status_approvloacd']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_loacd_files = explode(",", $row['lamp_loacd']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_loacd'])) {
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
                                                </td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_approvlegalvd']) {
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
                                                        <?php echo $row['status_approvlegalvd']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_draf_files = explode(",", $row['lamp_draf']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_draf'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_draf_files as $file) {
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
                                                </td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['confirm_nego']) {
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
                                                        <?php echo $row['confirm_nego']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['jadwal_psm'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['confirm_sdgdesain']) {
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
                                                        <?php echo $row['confirm_sdgdesain']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_desain_files = explode(",", $row['lamp_desainplan']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_desainplan'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_desain_files as $file) {
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
                                                </td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['valdoc_legal']) {
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
                                                        <?php echo $row['valdoc_legal']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <?php if ($row['valdoc_legal'] != "Approve"): ?>
                                                        <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['kode_lahan'] ?>">
                                                            <i class="nav-icon i-Pen-2"></i>
                                                        </button>
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
                                                                <form id="statusForm" method="post" action="">
                                                                    <input type="hidden" name="kode_lahan" id="modalKodeLahan">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Approve Legal</label>
                                                                        <select class="form-control" id="statusSelect" name="valdoc_legal">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Pending">Pending</option>
                                                                            <option value="Approve">Approve</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="catatan_valdoc">Catatan Legal</label>
                                                                        <input type="text" class="form-control" id="catatan_valdoc" name="catatan_valdoc">
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
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
                                                <th>Lampiran Land</th>
                                                <th>Approval LOA CD</th>
                                                <th>Lampiran Loa CD</th>
                                                <th>Approval Legal VD</th>
                                                <th>Lampiran Draft</th>
                                                <th>Confirm Legal</th>
                                                <th>Penjadwalan PSM</th>
                                                <th>Approval SDG Design</th>
                                                <th>Lampiran Desain</th>
                                                <th>Validasi</th>
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
    // JavaScript to handle opening the modal and setting form values
    $(document).ready(function() {
        $('.edit-btn').click(function() {
            var kodeLahan = $(this).data('id'); // Ambil nilai data-id dari tombol edit yang ditekan

            // Set nilai kode_lahan pada form di modal
            $('#modalKodeLahan').val(kodeLahan);

            // Tampilkan modal
            $('#editModal').modal('show');
        });
    });
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