<?php
// Koneksi ke database
include "../koneksi.php";

$confirm_fatpsm = "";

// Inisialisasi variabel $data dengan array kosong
$data = [];

if (isset($_GET['id'])) {
    $kode_lahan = $_GET['id'];
// Query untuk mengambil data dari tabel land
$sql = "SELECT note_psm.*, d.confirm_fatpsm
        FROM note_psm
        JOIN draft d ON note_psm.kode_lahan = d.kode_lahan
        WHERE note_psm.kode_lahan = '$kode_lahan'";
$result = $conn->query($sql);

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

// Periksa apakah query mengembalikan hasil yang valid
if ($result && $result->num_rows > 0) {
    // Ambil data dan masukkan ke dalam array $data
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Jika terdapat request POST untuk menghapus data
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
//     // Ambil ID karyawan yang akan dihapus dari request POST
//     $id = $_POST["id"];

//     // Query untuk menghapus data dari tabel land berdasarkan ID
//     $sql = "DELETE FROM negosiator WHERE id = '$id'";

//     // Eksekusi query
//     if ($conn->query($sql) === TRUE) {
//         echo "Data berhasil dihapus.";
//     } else {
//         echo "Error: " . $sql . "<br>" . $conn->error;
//     }
// }


function calculateScoring($start_date, $sla_date) {
    $start_date_obj = new DateTime($start_date);
    $sla_date_obj = new DateTime($sla_date);
    
    // Jika start_date tidak melebihi sla_date, berikan skor 100
    if ($start_date_obj <= $sla_date_obj) {
        return 100;
    }

    // Menghitung selisih hari antara start_date dan sla_date
    $date_diff = $start_date_obj->diff($sla_date_obj)->days;
    
    // Skor dikurangi dengan selisih hari keterlambatan
    $scoring = max(0, 100 - ($date_diff * 10)); // Penyesuaian sesuai logika bisnis
    
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

// Tutup koneksi database
$conn->close();
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
                    <h1>Log Note PSM</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
									<!-- <p><a class="btn btn-primary btn-icon m-1" href="negosiator/dealing-negosiator-from.php">+ add Negosiation</a></p> -->
									<p>
									  <span class="flex-grow-1"></span></p>
								</div>
                                <p>
							  <div class="table-responsive">
                              <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <!-- <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th> -->
                                                <th>Pasal / Page</th>
                                                <th>Review TAF 1</th>
                                                <th>Remarks</th>
                                                <th>TAF Date 1</th>
                                                <th>Review TAF 2</th>
                                                <th>TAF Date 2</th>
                                                <th>Review TAF 3</th>
                                                <th>TAF Date 3</th>
                                                <th>Review TAF 4</th>
                                                <th>TAF Date 4</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <!-- <td><?= $row['kode_lahan'] ?></td>
                                                <td><?= $row['kode_store'] ?></td>
                                                <td><?= $row['nama_lahan'] ?></td>
                                                <td><?= $row['lokasi'] ?></td> -->
                                                <td><?= $row['pasal_page'] ?></td>
                                                <td><?= $row['catatan_psmfat'] ?></td>
                                                <td><?= $row['remarks'] ?></td>
                                                <?php
                                                // Pastikan $row['fatpsm_date'] sudah didefinisikan dan memeriksa apakah nilai tidak kosong
                                                if (!empty($row['fat_date'])) {
                                                    $date = new DateTime($row['fat_date']);
                                                    $formattedDate = $date->format('d M y H:i');
                                                } else {
                                                    $formattedDate = ''; // Menampilkan string kosong jika tanggal kosong
                                                }
                                                ?>
                                                <td><?= $formattedDate ?></td>
                                                <td><?= $row['review_fat2'] ?></td>
                                                <?php
                                                // Pastikan $row['fatpsm_date'] sudah didefinisikan dan memeriksa apakah nilai tidak kosong
                                                if (!empty($row['fat2_date'])) {
                                                    $date = new DateTime($row['fat2_date']);
                                                    $formattedDate = $date->format('d M y H:i');
                                                } else {
                                                    $formattedDate = ''; // Menampilkan string kosong jika tanggal kosong
                                                }
                                                ?>
                                                <td><?= $formattedDate ?></td>
                                                <td><?= $row['review_fat3'] ?></td>
                                                <?php
                                                // Pastikan $row['fatpsm_date'] sudah didefinisikan dan memeriksa apakah nilai tidak kosong
                                                if (!empty($row['fat3_date'])) {
                                                    $date = new DateTime($row['fat3_date']);
                                                    $formattedDate = $date->format('d M y H:i');
                                                } else {
                                                    $formattedDate = ''; // Menampilkan string kosong jika tanggal kosong
                                                }
                                                ?>
                                                <td><?= $formattedDate ?></td>
                                                <td><?= $row['review_fat4'] ?></td>
                                                <?php
                                                // Pastikan $row['fatpsm_date'] sudah didefinisikan dan memeriksa apakah nilai tidak kosong
                                                if (!empty($row['fat4_date'])) {
                                                    $date = new DateTime($row['fat4_date']);
                                                    $formattedDate = $date->format('d M y H:i');
                                                } else {
                                                    $formattedDate = ''; // Menampilkan string kosong jika tanggal kosong
                                                }
                                                ?>
                                                <td><?= $formattedDate ?></td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');
                                                    $current_day = $now->format('N'); // 1 (Senin) hingga 7 (Minggu)

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    // Cek apakah hari ini adalah hari kerja dan waktu kerja
                                                        // echo '<a href="marketing/marketing-edit-form.php?id='. $row['id'] .'" class="btn btn-sm btn-warning mr-2">
                                                        //     <i class="nav-icon i-Pen-2"></i>
                                                        // </a>';
                                                        if (empty($row['review_fat2']) && $row['review_fat2'] != "Done") {
                                                        echo '<button class="btn btn-sm btn-warning edit-btn mr-2" data-toggle="modal" data-target="#editModal1" data-id="'. $row['id'] .'" data-status="'.$row['review_fat2'] .'">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button>';
                                                        }
                                                        if (!empty($row['review_fat2']) && empty($row['review_fat3']) && $row['review_fat2'] != "Done" && $row['review_fat3'] != "Done") {
                                                        echo '<button class="btn btn-sm btn-primary edit-btn mr-2" data-toggle="modal" data-target="#editModal2" data-id="'. $row['id'] .'" data-status="'.$row['review_fat3'] .'">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button>';
                                                        }
                                                        if (!empty($row['review_fat2']) && !empty($row['review_fat3']) && empty($row['review_fat4']) && $row['review_fat2'] != "Done" && $row['review_fat3'] != "Done" && $row['review_fat4'] != "Done") {
                                                        echo '<button class="btn btn-sm btn-info edit-btn mr-2" data-toggle="modal" data-target="#editModal3" data-id="'. $row['id'] .'" data-status="'.$row['review_fat4'] .'">
                                                            <i class="nav-icon i-Book"></i>
                                                        </button>';
                                                        }
                                                    ?>
                                                </td>

                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal1" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit Revision Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="fat/log-psm2-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" id="modalId" value="<?= $row['id']; ?>">
                                                                    <input type="hidden" name="kode_lahan" value="<?= $row['kode_lahan']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Revision Draft PSM<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="review_fat2">
                                                                            <option value="">Pilih</option>
                                                                            <option value="Done">Done</option>
                                                                            <option value="manual">Masukkan Catatan</option>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Input text untuk catatan, disembunyikan secara default -->
                                                                    <div class="form-group" id="manualInput" style="display: none;">
                                                                        <input type="text" class="form-control" name="manual_note" placeholder="Masukkan Catatan">
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
                                                                <h5 class="modal-title" id="editModalLabel">Edit Revision Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="fat/log-psm3-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" id="modalId2" value="<?= $row['id']; ?>">
                                                                    <input type="hidden" name="kode_lahan" value="<?= $row['kode_lahan']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect2">Status Revision Draft PSM<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect2" name="review_fat3">
                                                                            <option value="">Pilih</option>
                                                                            <option value="Done">Done</option>
                                                                            <option value="manual">Masukkan Catatan</option>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Input text untuk catatan, disembunyikan secara default -->
                                                                    <div class="form-group" id="manualInput2" style="display: none;">
                                                                        <input type="text" class="form-control" name="manual_note2" placeholder="Masukkan Catatan">
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal3" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit Revision Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="statusForm" method="post" action="fat/log-psm4-process.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id" id="modalId3" value="<?= $row['id']; ?>">
                                                                    <input type="hidden" name="kode_lahan" value="<?= $row['kode_lahan']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect3">Status Revision Draft PSM<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect3" name="review_fat4">
                                                                            <option value="">Pilih</option>
                                                                            <option value="Done">Done</option>
                                                                            <option value="manual">Masukkan Catatan</option>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Input text untuk catatan, disembunyikan secara default -->
                                                                    <div class="form-group" id="manualInput3" style="display: none;">
                                                                        <input type="text" class="form-control" name="manual_note3" placeholder="Masukkan Catatan">
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
                                                <!-- <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>                                                 -->
                                                <th>Pasal / Page</th>
                                                <th>Review TAF 1</th>
                                                <th>Remarks</th>
                                                <th>TAF Date 1</th>
                                                <th>Review TAF 2</th>
                                                <th>TAF Date 2</th>
                                                <th>Review TAF 3</th>
                                                <th>TAF Date 3</th>
                                                <th>Review TAF 4</th>
                                                <th>TAF Date 4</th>
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
                                                    <form method="POST" action="approval-owner-delete.php">
                                                        <input type="hidden" name="id" id="delete" value="">
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal untuk "Tepat Waktu" -->
                                    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approvalModalLabel">Pemberitahuan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Data sudah approve tepat waktu.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal untuk "Deadline Approval" -->
                                    <div class="modal fade" id="deadlineModal" tabindex="-1" role="dialog" aria-labelledby="deadlineModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deadlineModalLabel">Pemberitahuan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Tersisa waktu <?php echo $diff->days; ?> hari, segera lakukan approval.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal untuk "Terlambat Approval" -->
                                    <div class="modal fade" id="lateApprovalModal" tabindex="-1" role="dialog" aria-labelledby="lateApprovalModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="lateApprovalModalLabel">Pemberitahuan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Data sudah terlambat untuk di-approve. Telah terlambat <?php echo $lateDays; ?> hari.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>     -->
    
    <script>
        // Fungsi untuk menampilkan atau menyembunyikan input manual
        document.getElementById('statusSelect').addEventListener('change', function () {
            var manualInput = document.getElementById('manualInput');
            if (this.value === 'manual') {
                // Jika opsi 'Masukkan catatan manual' dipilih, tampilkan input manual
                manualInput.style.display = 'block';
            } else {
                // Jika opsi 'Done' dipilih, sembunyikan input manual
                manualInput.style.display = 'none';
            }
        });
    </script>
    
    <script>
        // Fungsi untuk menampilkan atau menyembunyikan input manual
        document.getElementById('statusSelect2').addEventListener('change', function () {
            var manualInput = document.getElementById('manualInput2');
            if (this.value === 'manual') {
                // Jika opsi 'Masukkan catatan manual' dipilih, tampilkan input manual
                manualInput.style.display = 'block';
            } else {
                // Jika opsi 'Done' dipilih, sembunyikan input manual
                manualInput.style.display = 'none';
            }
        });
    </script>
    
    <script>
        // Fungsi untuk menampilkan atau menyembunyikan input manual
        document.getElementById('statusSelect3').addEventListener('change', function () {
            var manualInput = document.getElementById('manualInput3');
            if (this.value === 'manual') {
                // Jika opsi 'Masukkan catatan manual' dipilih, tampilkan input manual
                manualInput.style.display = 'block';
            } else {
                // Jika opsi 'Done' dipilih, sembunyikan input manual
                manualInput.style.display = 'none';
            }
        });
    </script>

    <script>
    // Saat tombol edit diklik
    $('.edit-btn').on('click', function () {
        var id = $(this).data('id'); // Ambil data-id dari tombol
        var status = $(this).data('status'); // Ambil data-status dari tombol

        // Set nilai id ke dalam input hidden di modal
        $('#modalId2').val(id);
        $('#statusSelect2').val(status);

        // Periksa apakah statusnya adalah "manual", jika ya tampilkan input manual note
        if (status === 'manual') {
            $('#manualInput2').show();
        } else {
            $('#manualInput2').hide();
        }
    });
</script>

<script>
    // Saat tombol edit diklik
    $('.edit-btn').on('click', function () {
        var id = $(this).data('id'); // Ambil data-id dari tombol
        var status = $(this).data('status'); // Ambil data-status dari tombol

        // Set nilai id ke dalam input hidden di modal
        $('#modalId3').val(id);
        $('#statusSelect3').val(status);

        // Periksa apakah statusnya adalah "manual", jika ya tampilkan input manual note
        if (status === 'manual') {
            $('#manualInput3').show();
        } else {
            $('#manualInput3').hide();
        }
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

        // Toggle issue detail section based on the initial value of statusSelect
        toggleIssueDetail();
    });
</script>
<?php if ($confirm_fatpsm == 'Pending') { ?>
    <script>
        $(document).ready(function () {
            $('#editModal').modal('show'); // Show modal if status_approvowner is 'Pending'
        });
    </script>
<?php } ?>
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
    // When the document is ready
    $(document).ready(function() {
        // Get the initial value of status approval owner
        var initialStatus = $('#editStatusOwner').val();

        // Function to generate dropdown options based on the initial status
        function generateOptions(initialStatus) {
            var optionsHtml = '';
            // If the initial status is In Process
            if (initialStatus === 'In Process') {
                optionsHtml += '<option value="Pending">Pending</option>';
                optionsHtml += '<option value="Approve">Approve</option>';
            }
            // If the initial status is Pending
            else if (initialStatus === 'Pending') {
                optionsHtml += '<option value="In Process">In Process</option>';
                optionsHtml += '<option value="Approve">Approve</option>';
            }
            // If the initial status is Approve, disable the select input
            else if (initialStatus === 'Approve') {
                optionsHtml += '<option value="Approve" disabled selected>Approve</option>';
            }
            // Update the dropdown options
            $('#editStatusOwner').html(optionsHtml);
        }

        // Generate initial dropdown options based on the initial status
        generateOptions(initialStatus);

        // When the value of the select input changes
        $('#editStatusOwner').change(function() {
            var selectedStatus = $(this).val();
            // Regenerate dropdown options based on the selected status
            generateOptions(selectedStatus);
        });
    });
</script>

<script>
    
</script>

<script>
    function showPopup(daysLeft) {
        alert("Tersisa waktu " + daysLeft + " hari, segera lakukan approval.");
    }

    function showApprovalPopup() {
        alert("Data sudah approve tepat waktu");
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
                    leftColumns: 1 // Jumlah kolom yang ingin di-fix
                }
            });
        });
    </script>
    
    <script>
        function loadNoteDetails(id) {
    $.ajax({
        url: 'getpsm_kode_lahan.php', // Path file PHP untuk mendapatkan kode_lahan
        type: 'GET',
        data: { id: id },
        success: function(response) {
            let data = JSON.parse(response);

            if (data.error) {
                console.error('Error:', data.error);
                return;
            }

            let kodeLahan = data.kode_lahan;

            // Kemudian ambil detail note menggunakan kode_lahan
            $.ajax({
                url: 'notepsm_details.php', // Path file PHP untuk mendapatkan detail note
                type: 'GET',
                data: { kode_lahan: kodeLahan },
                success: function(response) {
                    let noteData = JSON.parse(response);

                    if (noteData.error) {
                        console.error('Error:', noteData.error);
                        return;
                    }

                    const options = { year: 'numeric', month: 'short', day: 'numeric' };
                    const procDateFormatted = noteData.legal_date ? new Date(noteData.legal_date).toLocaleDateString('en-US', options) : 'N/A';
                    const tafDateFormatted = noteData.fat_date ? new Date(noteData.fat_date).toLocaleDateString('en-US', options) : 'N/A';

                    $('#legal_date').text(procDateFormatted);
                    $('#fat_date').text(tafDateFormatted);
                    $('#catatan_psmlegal').text(noteData.catatan_psmlegal || 'N/A');
                    $('#catatan_psmfat').text(noteData.catatan_psmfat || 'N/A');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching note details:', error);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching kode_lahan:', error);
        }
    });
}
    </script>
   
</body>

</html>