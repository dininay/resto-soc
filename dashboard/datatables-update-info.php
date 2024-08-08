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
$sql = "SELECT l.id, l.kode_lahan, l.nama_lahan, l.lokasi, l.lamp_land, l.status_approvre,
o.status_approvlegalvd, o.lamp_loacd, o.status_approvloacd,  o.lamp_loacd, o.status_approvlegalvd, o.kode_store,
e.status_approvowner, e.catatan_owner, e.status_approvlegal, e.catatan_legal, e.status_approvnego, e.catatan_nego, e.lamp_vl, e.status_vl, 
t.lamp_draf, t.draft_legal, t.jadwal_psm, t.valdoc_legal, t.lamp_signpsm, t.confirm_nego, s.lamp_desainplan, s.confirm_sdgdesain, s.submit_legal, 
c.status_obssdg, c.obstacle, c.lamp_legal, c.status_obslegal, k.all_progress, 
b.jumlah, b.lamp_rab, b.confirm_sdgqs, v.nama, p.nama_vendor, v.lamp_profil, v.lamp_vendor, p.status_approvprocurement, 
r.lamp_spk, r.sla_kom, r.status_spk, r.gostore_date, r.lamp_kom, r.start_konstruksi, r.status_kom,
q.lamp_steqp, q.lamp_basteqp, q.status_steqp, r.lamp_stkonstruksi, r.status_stkonstruksi
FROM land l
LEFT JOIN sdg_desain s ON l.kode_lahan = s.kode_lahan
LEFT JOIN draft t ON s.kode_lahan = t.kode_lahan
LEFT JOIN re e ON t.kode_lahan = e.kode_lahan
LEFT JOIN dokumen_loacd o ON e.kode_lahan = o.kode_lahan
LEFT JOIN obs_sdg c ON e.kode_lahan = c.kode_lahan
LEFT JOIN sdg_rab b ON e.kode_lahan = b.kode_lahan
LEFT JOIN procurement p ON e.kode_lahan = p.kode_lahan
LEFT JOIN vendor v ON p.nama_vendor = v.kode_vendor
LEFT JOIN resto r ON e.kode_lahan = r.kode_lahan
LEFT JOIN sdg_pk k ON e.kode_lahan = k.kode_lahan
LEFT JOIN equipment q ON e.kode_lahan = q.kode_lahan
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
                                                <th>Lampiran Land</th>
                                                <th>Approval RE</th>
                                                <th>Approval BoD</th>
                                                <th>Catatan Owner</th>
                                                <th>Approval Legal</th>
                                                <th>Catatan Legal</th>
                                                <th>Approval Negosiator</th>
                                                <th>Catatan Negosiator</th>
                                                <th>Lampiran VL</th>
                                                <th>Approval VL</th>
                                                <th>Lampiran Loa CD</th>
                                                <th>Approval Loa CD</th>
                                                <th>Lampiran VD</th>
                                                <th>Approval Legal VD</th>
                                                <th>Lampiran Draft</th>
                                                <th>Approval Draft</th>
                                                <th>Lampiran PSM</th>
                                                <th>Jadwal Sign PSM</th>
                                                <th>Approval Sign</th>
                                                <th>Lampiran Design</th>
                                                <th>Approval SDG Design</th>
                                                <th>Approval Legal WO Design SDG</th>
                                                <th>Approval Validasi Document Legal</th>
                                                <th>Obstacle Land SDG Design</th>
                                                <th>Jenis Obstacle</th>
                                                <th>Lampiran Obstacle</th>
                                                <th>Status Obstacle Legal</th>
                                                <th>Jumlah RAB</th>
                                                <th>Lampiran RAB</th>
                                                <th>Approval SDG QS</th>
                                                <th>Vendor</th>
                                                <th>Lampiran Profil Vendor</th>
                                                <th>Lampiran Lain-lain Vendor</th>
                                                <th>Approval Procurement</th>
                                                <th>Lampiran SPK</th>
                                                <th>Jadwal Kick Off Meeting</th>
                                                <th>Approval SPK</th>
                                                <th>Go Store Date</th>
                                                <th>Lampiran Kick Off Meeting</th>
                                                <th>Tgl Start Konstruksi</th>
                                                <th>Approval Kick Off Meeting</th>
                                                <th>Progress Construction All</th>
                                                <th>Lampiran ST Equipment</th>
                                                <th>Lampiran BA</th>
                                                <th>Approval ST Equipment</th>
                                                <th>Lampiran ST Kontraktor</th>
                                                <th>Approval ST Kontraktor</th>
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
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_approvowner']) {
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
                                                        <?php echo $row['status_approvowner']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['catatan_owner'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_approvlegal']) {
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
                                                        <?php echo $row['status_approvlegal']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['catatan_legal'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_approvnego']) {
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
                                                        <?php echo $row['status_approvnego']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['catatan_nego'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_vl_files = explode(",", $row['lamp_vl']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_vl'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_vl_files as $vl) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $vl . '" target="_blank">
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
                                                        switch ($row['status_vl']) {
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
                                                        <?php echo $row['status_vl']; ?>
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
                                                $lamp_vl_files = explode(",", $row['lamp_vl']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_vl'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_vl_files as $vl) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $vl . '" target="_blank">
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
                                                        switch ($row['draft_legal']) {
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
                                                        <?php echo $row['draft_legal']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_signpsm_files = explode(",", $row['lamp_signpsm']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_signpsm'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_signpsm_files as $lamp_signpsm) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_signpsm . '" target="_blank">
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
                                                    <?php if (!empty($row['jadwal_psm'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['jadwal_psm']);
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_desainplan_files = explode(",", $row['lamp_desainplan']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_desainplan'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_desainplan_files as $lamp_desainplan) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_desainplan . '" target="_blank">
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
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['submit_legal']) {
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
                                                        <?php echo $row['submit_legal']; ?>
                                                    </span>
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
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_obssdg']) {
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
                                                        <?php echo $row['status_obssdg']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['obstacle'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_legal_files = explode(",", $row['lamp_legal']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_legal'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_legal_files as $lamp_legal) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_legal . '" target="_blank">
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
                                                        switch ($row['status_obslegal']) {
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
                                                        <?php echo $row['status_obslegal']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['jumlah'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_rab_files = explode(",", $row['lamp_rab']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_rab'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_rab_files as $lamp_rab) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_rab . '" target="_blank">
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
                                                        switch ($row['confirm_sdgqs']) {
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
                                                        <?php echo $row['confirm_sdgqs']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['nama'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_profil_files = explode(",", $row['lamp_profil']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_profil'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_profil_files as $lamp_profil) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_profil . '" target="_blank">
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_vendor_files = explode(",", $row['lamp_vendor']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_vendor'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_vendor_files as $lamp_vendor) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_vendor . '" target="_blank">
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
                                                        switch ($row['status_approvprocurement']) {
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
                                                        <?php echo $row['status_approvprocurement']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_spk_files = explode(",", $row['lamp_spk']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_spk'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_spk_files as $lamp_spk) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_spk . '" target="_blank">
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
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_spk']) {
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
                                                        <?php echo $row['status_spk']; ?>
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_kom_files = explode(",", $row['lamp_kom']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_kom'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_kom_files as $lamp_kom) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_kom . '" target="_blank">
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
                                                <td><?= $row['start_konstruksi'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_kom']) {
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
                                                        <?php echo $row['status_kom']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['all_progress'] ?></td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_steqp_files = explode(",", $row['lamp_steqp']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_steqp'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_steqp_files as $lamp_steqp) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_steqp . '" target="_blank">
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
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_ba_files = explode(",", $row['lamp_basteqp']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_basteqp'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_ba_files as $lamp_ba) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_ba . '" target="_blank">
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
                                                        switch ($row['status_steqp']) {
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
                                                        <?php echo $row['status_steqp']; ?>
                                                    </span>
                                                </td>
                                                <?php
                                                // Bagian ini di dalam loop yang menampilkan data tabel
                                                $lamp_stkonstruksi_files = explode(",", $row['lamp_stkonstruksi']); // Pisahkan nama file menjadi array
                                                // Periksa apakah array tidak kosong sebelum menampilkan ikon
                                                if (!empty($row['lamp_stkonstruksi'])) {
                                                    echo '<td>
                                                            <ul style="list-style-type: none; padding: 0; margin: 0;">';
                                                    // Loop untuk setiap file dalam array
                                                    foreach ($lamp_stkonstruksi_files as $lamp_stkonstruksi) {
                                                        echo '<li style="display: inline-block; margin-right: 5px;">
                                                                <a href="uploads/' . $lamp_stkonstruksi . '" target="_blank">
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
                                                        switch ($row['status_stkonstruksi']) {
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
                                                        <?php echo $row['status_stkonstruksi']; ?>
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
                                                <th>Lampiran Land</th>
                                                <th>Approval RE</th>
                                                <th>Approval BoD</th>
                                                <th>Catatan Owner</th>
                                                <th>Approval Legal</th>
                                                <th>Catatan Legal</th>
                                                <th>Approval Negosiator</th>
                                                <th>Catatan Negosiator</th>
                                                <th>Lampiran VL</th>
                                                <th>Approval VL</th>
                                                <th>Lampiran Loa CD</th>
                                                <th>Approval Loa CD</th>
                                                <th>Lampiran VD</th>
                                                <th>Approval Legal VD</th>
                                                <th>Lampiran Draft</th>
                                                <th>Approval Draft</th>
                                                <th>Lampiran PSM</th>
                                                <th>Jadwal Sign PSM</th>
                                                <th>Approval Sign</th>
                                                <th>Lampiran Design</th>
                                                <th>Approval SDG Design</th>
                                                <th>Approval Legal WO Design SDG</th>
                                                <th>Approval Validasi Document Legal</th>
                                                <th>Obstacle Land SDG Design</th>
                                                <th>Jenis Obstacle</th>
                                                <th>Lampiran Obstacle</th>
                                                <th>Status Obstacle Legal</th>
                                                <th>Jumlah RAB</th>
                                                <th>Lampiran RAB</th>
                                                <th>Approval SDG QS</th>
                                                <th>Vendor</th>
                                                <th>Lampiran Profil Vendor</th>
                                                <th>Lampiran Lain-lain Vendor</th>
                                                <th>Approval Procurement</th>
                                                <th>Lampiran SPK</th>
                                                <th>Jadwal Kick Off Meeting</th>
                                                <th>Approval SPK</th>
                                                <th>Go Store Date</th>
                                                <th>Lampiran Kick Off Meeting</th>
                                                <th>Tgl Start Konstruksi</th>
                                                <th>Approval Kick Off Meeting</th>
                                                <th>Progress Construction All</th>
                                                <th>Lampiran ST Equipment</th>
                                                <th>Lampiran BA</th>
                                                <th>Approval ST Equipment</th>
                                                <th>Lampiran ST Kontraktor</th>
                                                <th>Approval ST Kontraktor</th>
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