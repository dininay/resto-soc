<?php
// Koneksi ke database
include "../koneksi.php";
// Proses jika ada pengiriman data dari formulir untuk memperbarui status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["status_spk"])) {
    $id = $_POST["id"];
    $status_spk = $_POST["status_spk"];
    $spk_date = date("Y-m-d H:i:s");
    
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Query untuk memperbarui status_finallegal berdasarkan id
        $sql_update = "UPDATE resto SET status_spk = ?, spk_date = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $status_spk, $spk_date, $id);

        // Eksekusi query update
        if ($stmt_update->execute() === TRUE) {
            // Jika status_finallegal diubah menjadi Approve
            if ($status_spk == 'Approve') {

                    // Ambil jumlah hari dari tabel master_sla berdasarkan divisi SPK
                    $sql_sla = "SELECT sla FROM master_sla WHERE divisi = 'KOM'";
                    $result_sla = $conn->query($sql_sla);
                    if ($result_sla->num_rows > 0) {
                        $row_sla = $result_sla->fetch_assoc();
                        $hari_sla = $row_sla['sla'];
                        echo "SLA days: $hari_sla<br>";

                        // Tentukan sla_spk
                        $sla_kom = date("Y-m-d", strtotime("$spk_date + $hari_sla days"));
                        echo "SLA SPK: $sla_kom<br>";

                        // Update sla_spk dan status_spk di tabel resto
                        $sql_update_spk = "UPDATE resto SET sla_kom = ?, status_kom = 'In Process' WHERE id = ?";
                        $stmt_update_spk = $conn->prepare($sql_update_spk);
                        $stmt_update_spk->bind_param("si", $sla_kom, $id);
                        $stmt_update_spk->execute();
                    } else {
                        // Rollback transaksi jika data SLA tidak ditemukan
                        $conn->rollback();
                        echo "Error: Data SLA tidak ditemukan untuk divisi SPK.";
                        exit;
                    }
            }
            // Komit transaksi
            $conn->commit();
            echo "Status dan data berhasil diperbarui.";
        } else {
            // Rollback transaksi jika terjadi kesalahan pada update
            $conn->rollback();
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
        header("Location: datatables-monitoring-op.php");
        exit; // Pastikan tidak ada output lain setelah header redirect
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
// Query untuk mengambil data dari tabel land
$sql = "
SELECT 
    l.kode_lahan, 
    l.nama_lahan, 
    l.lokasi, 
    l.lamp_land, 
    c.lamp_loacd, 
    d.lamp_draf, 
    r.*, 
    p.*, 
    d.jadwal_psm, 
    s.*, 
    v.nama_vendor, 
    v.kode_vendor, 
    v.lamp_vendor, 
    v.lamp_profil,
    c.kode_store,
    k.*
FROM 
    draft d
INNER JOIN 
    land l ON d.kode_lahan = l.kode_lahan
INNER JOIN 
    dokumen_loacd c ON d.kode_lahan = c.kode_lahan
INNER JOIN 
    resto r ON d.kode_lahan = r.kode_lahan
INNER JOIN 
    sdg_pk s ON r.kode_lahan = s.kode_lahan
INNER JOIN 
    procurement p ON r.kode_lahan = p.kode_lahan
INNER JOIN 
    konstruksi k ON r.kode_lahan = k.kode_lahan
LEFT JOIN (
    SELECT 
        v1.nama AS nama_vendor, 
        v1.kode_vendor, 
        v1.lamp_vendor, 
        v1.lamp_profil
    FROM 
        vendor v1
) v ON p.nama_vendor = v.kode_vendor
WHERE 
    r.status_kom IN ('Done','On Going Project')
GROUP BY 
    l.kode_lahan";
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
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
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
                    <h1>List Construction Activity by Vendor per Week</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
									<!-- <p><a class="btn btn-primary btn-icon m-1" href="legal/sp-submit-form.php">+ add SP Legal </a></p> -->
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
                                                <th>Lampiran Izin Konstruksi</th>
                                                <th>Nama Vendor</th>
                                                <th>Lampiran Vendor</th>
                                                <th>Lampiran SPK</th>
                                                <th>Lampiran KOM</th>
                                                <th>Status Konstruksi</th>
                                                <th>Lampiran Progress per Week</th>
                                                <th>Progress Week 1</th>
                                                <th>Progress Week 2</th>
                                                <th>Progress Week 3</th>
                                                <th>Progress Week 4</th>
                                                <th>Progress Week 5</th>
                                                <th>Progress Week 6</th>
                                                <th>Progress Week 7</th>
                                                <th>Progress Week 8</th>
                                                <th>Progress Week 9</th>
                                                <th>Progress Week 10</th>
                                                <th>Progress Week 11</th>
                                                <th>Progress Week 12</th>
                                                <th>Progress Week 13</th>
                                                <th>Progress Week 14</th>
                                                <th>Progress Week 15</th>
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
                                                <td><?= $row['lokasi'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_splegal_files = explode(",", $row['lamp_splegal']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_splegal'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_splegal_files as $splegal) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $splegal . '" target="_blank">
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
                                                <td><?= $row['nama_vendor'] ?></td>  
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_splegal_files = explode(",", $row['lamp_vendor']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_vendor'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_splegal_files as $splegal) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $splegal . '" target="_blank">
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_spk_files = explode(",", $row['lamp_spk']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_spk'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_spk_files as $splegal) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $splegal . '" target="_blank">
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_kom_files = explode(",", $row['lamp_kom']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_kom'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_kom_files as $kom) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $kom . '" target="_blank">
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
                                                        switch ($row['status_consact']) {
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
                                                        <?php echo $row['status_consact']; ?>
                                                    </span>
                                                </td>   
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_monitoring_files = explode(",", $row['lamp_monitoring']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_monitoring'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_monitoring_files as $monitoring) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $monitoring . '" target="_blank">
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
                                                
                                                <td><?= $row['week_1'] ?>%</td> 
                                                <td><?= $row['week_2'] ?>%</td> 
                                                <td><?= $row['week_3'] ?>%</td> 
                                                <td><?= $row['week_4'] ?>%</td> 
                                                <td><?= $row['week_5'] ?>%</td> 
                                                <td><?= $row['week_6'] ?>%</td> 
                                                <td><?= $row['week_7'] ?>%</td> 
                                                <td><?= $row['week_8'] ?>%</td> 
                                                <td><?= $row['week_9'] ?>%</td> 
                                                <td><?= $row['week_10'] ?>%</td> 
                                                <td><?= $row['week_11'] ?>%</td> 
                                                <td><?= $row['week_12'] ?>%</td> 
                                                <td><?= $row['week_13'] ?>%</td> 
                                                <td><?= $row['week_14'] ?>%</td> 
                                                <td><?= $row['week_15'] ?>%</td> 
                                                
                                                <td>
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta (sesuaikan dengan timezone lokal Anda)
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    $start_date = $row['consact_date'];
                                                    $sla_date = $row['sla_consact'];
                                                    $status_consact = $row['status_consact'];

                                                    // Menghitung scoring
                                                    $scoring = calculateScoring($start_date, $sla_date, $sla_value);
                                                    $remarks = getRemarks($scoring);

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    if ($status_consact === 'Approve') {
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
                                                    <?php if ($row['status_consact'] != "Approve"): ?>
                                                    <?php
                                                    // Mengatur timezone ke Asia/Jakarta (sesuaikan dengan timezone lokal Anda)
                                                    date_default_timezone_set('Asia/Jakarta');

                                                    // Mendapatkan waktu sekarang
                                                    $now = new DateTime();
                                                    $current_time = $now->format('H:i');

                                                    // Jam kerja
                                                    $work_start = '08:00';
                                                    $work_end = '17:00';

                                                    if ($row['status_consact'] != "Approve") {
                                                        echo '<a href="sdg-pk/monitoring-edit-form.php?id='. $row['id'] .'" class="btn btn-sm btn-warning mr-2">
                                                            <i class="nav-icon i-Pen-2"></i>
                                                        </a>';
                                                        // echo '<button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="'. $row['id'] .'" data-status="'.$row['status_consact'] .'">
                                                        //     <i class="nav-icon i-Book"></i>
                                                        // </button>';
                                                    }
                                                    ?>
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
                                                                <form id="statusForm" method="post" action="">
                                                                    <input type="hidden" name="id" id="modalId" value="<?= $row['id']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="statusSelect">Status Approve Construction<strong><span style="color: red;">*</span></strong></label>
                                                                        <select class="form-control" id="statusSelect" name="status_consact">
                                                                            <option value="In Process">In Process</option>
                                                                            <option value="Pending">Pending</option>
                                                                            <option value="Approve">Approve</option>
                                                                        </select>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                <th>Lampiran Izin Konstruksi</th>
                                                <th>Nama Vendor</th>
                                                <th>Lampiran Vendor</th>
                                                <th>Lampiran SPK</th>
                                                <th>Lampiran KOM</th>
                                                <th>Status Konstruksi</th>
                                                <th>Lampiran Progress per Week</th>
                                                <th>Progress Week 1</th>
                                                <th>Progress Week 2</th>
                                                <th>Progress Week 3</th>
                                                <th>Progress Week 4</th>
                                                <th>Progress Week 5</th>
                                                <th>Progress Week 6</th>
                                                <th>Progress Week 7</th>
                                                <th>Progress Week 8</th>
                                                <th>Progress Week 9</th>
                                                <th>Progress Week 10</th>
                                                <th>Progress Week 11</th>
                                                <th>Progress Week 12</th>
                                                <th>Progress Week 13</th>
                                                <th>Progress Week 14</th>
                                                <th>Progress Week 15</th>
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
            $('#modalId').val(id);
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
    $(".edit-btn").click(function() {
        // Sembunyikan semua form yang terbuka
        $(".status-form").hide();
        // Tampilkan form di samping tombol edit yang diklik
        $(this).next(".status-form").show();
    });
});
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