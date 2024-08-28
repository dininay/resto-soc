<?php
// Koneksi ke database
include "../koneksi.php";

// Query untuk mengambil data dari tabel land
$sql = "
        SELECT 
        summary_soc.*,
        resto.gostore_date,
        equipment.sla_steqp,
        land.kode_lahan,
        land.nama_lahan,
        land.lokasi,
        socdate_academy.kpt_1,
        socdate_academy.kpt_2,
        socdate_academy.kpt_3,
        socdate_fat.lamp_qris,
        socdate_fat.lamp_st,
        socdate_hr.tm,
        socdate_hr.lamp_tm,
        socdate_hr.ff_1,
        socdate_hr.ff_2,
        socdate_hr.ff_3,
        socdate_hr.lamp_ff1,
        socdate_hr.lamp_ff2,
        socdate_hr.lamp_ff3,
        socdate_hr.hot,
        socdate_hr.lamp_hot,
        socdate_ir.lamp_rabcs,
        socdate_ir.lamp_rabsecurity,
        socdate_it.kode_dvr,
        socdate_it.web_report,
        socdate_it.akun_gis,
        socdate_it.lamp_internet,
        socdate_it.lamp_cctv,
        socdate_it.lamp_printer,
        socdate_it.lamp_sound,
        socdate_it.lamp_config,
        socdate_legal.mou_parkirsampah,
        socdate_marketing.gmaps,
        socdate_marketing.lamp_gmaps,
        socdate_marketing.id_m_shopee,
        socdate_marketing.id_m_gojek,
        socdate_marketing.id_m_grab,
        socdate_marketing.email_resto,
        socdate_marketing.lamp_merchant,
        socdate_scm.lamp_sj,
        socdate_sdg.lamp_sumberair,
        socdate_sdg.lamp_filterair,
        socdate_sdg.lamp_ujilab,
        socdate_sdg.lampwo_reqipal,
        socdate_sdg.lamp_slo,
        socdate_sdg.lamp_nidi,
        sdg_desain.lamp_permit,
        sdg_desain.lamp_pbg,
        sdg_pk.month_1,
        sdg_pk.month_2,
        sdg_pk.month_3,
        dokumen_loacd.kode_store
        FROM resto
         JOIN land ON resto.kode_lahan = land.kode_lahan
         JOIN equipment ON resto.kode_lahan = equipment.kode_lahan
         JOIN sdg_pk ON resto.kode_lahan = sdg_pk.kode_lahan
         JOIN socdate_academy ON land.kode_lahan = socdate_academy.kode_lahan
         JOIN socdate_fat ON land.kode_lahan = socdate_fat.kode_lahan
         JOIN socdate_hr ON land.kode_lahan = socdate_hr.kode_lahan
         JOIN socdate_ir ON land.kode_lahan = socdate_ir.kode_lahan
         JOIN socdate_it ON land.kode_lahan = socdate_it.kode_lahan
         JOIN socdate_marketing ON land.kode_lahan = socdate_marketing.kode_lahan
         JOIN socdate_legal ON land.kode_lahan = socdate_legal.kode_lahan
         JOIN socdate_scm ON land.kode_lahan = socdate_scm.kode_lahan
         JOIN socdate_sdg ON land.kode_lahan = socdate_sdg.kode_lahan
         JOIN dokumen_loacd ON land.kode_lahan = dokumen_loacd.kode_lahan
         JOIN sdg_desain ON land.kode_lahan = sdg_desain.kode_lahan
         JOIN summary_soc ON resto.kode_lahan = summary_soc.kode_lahan
         LEFT JOIN soc_fat ON summary_soc.kode_lahan = soc_fat.kode_lahan
         LEFT JOIN soc_hrga ON soc_fat.kode_lahan = soc_hrga.kode_lahan
         LEFT JOIN soc_it ON soc_fat.kode_lahan = soc_it.kode_lahan
         LEFT JOIN soc_legal ON soc_fat.kode_lahan = soc_legal.kode_lahan
         LEFT JOIN soc_marketing ON soc_fat.kode_lahan = soc_marketing.kode_lahan
         LEFT JOIN soc_rto ON soc_fat.kode_lahan = soc_rto.kode_lahan
         LEFT JOIN soc_sdg ON soc_fat.kode_lahan = soc_sdg.kode_lahan
         LEFT JOIN note_ba ON soc_fat.kode_lahan = note_ba.kode_lahan
         LEFT JOIN note_legal ON soc_fat.kode_lahan = note_legal.kode_lahan
         LEFT JOIN doc_legal ON note_legal.kode_lahan = doc_legal.kode_lahan
         LEFT JOIN sign ON soc_fat.kode_lahan = sign.kode_lahan
         GROUP BY summary_soc.kode_lahan";
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
                    <h1>List Data Summary SOC</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3"></h4>
								<div class="footer-bottom float-right">
                                <!-- <p><a class="btn btn-primary btn-icon m-1" href="operation/summary-soc-form.php">+ add Summary</a></p> -->
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
                                                <th>Target GO</th>
                                                <th>Actual GO</th>
                                                <th>RTO Actual</th>
												<th>ST Equipment</th>
                                                <th>Type Kitchen</th>
                                                <th>Jam Operasional</th>
                                                <th>Project Sales</th>
												<th>Crew Needed</th>
                                                <th>SPK Release</th>
                                                <th>GO Construction Progress</th>
                                                <th>RTO Score</th>
												<th>Kualitas GO</th>
                                                <th>Status GO</th>
                                                <th>Jumlah Hari</th>
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
                                                $date = new DateTime($row['gostore_date']);
                                                $formattedDate = $date->format('d M y');
                                                ?>
                                                <td><?= $formattedDate ?></td>
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
                                                <td><?= $row['sla_steqp'] ?></td>
                                                <td><?= $row['type_kitchen'] ?></td>
                                                <td><?= $row['jam_ops'] ?></td>
                                                <td>Rp. <?= $row['project_sales'] ?></td>
                                                <td><?= $row['crew_needed'] ?></td>
                                                <td>
                                                    <?php if (!empty($row['spk_release'])): ?>
                                                        <?php
                                                        $date = new DateTime($row['spk_release']);
                                                        $formattedDate = $date->format('d M y');
                                                        ?>
                                                        <?= $formattedDate ?>
                                                    <?php else: ?>
                                                        <!-- Jika kosong, tampilkan pesan atau biarkan kosong -->
                                                        <!-- Misalnya, <span>-</span> atau <span>Not Available</span> -->
                                                        <!-- <span>Not Available</span> -->
                                                    <?php endif; ?>
                                                </td>
                                                <!-- <td>
                                                    <?php 
                                                        // Memeriksa apakah setiap nilai tidak kosong
                                                        if (!is_null($row['bangunan_mural']) && !is_null($row['daya_listrik']) && !is_null($row['supply_air']) && 
                                                            !is_null($row['aliran_air']) && !is_null($row['kualitas_keramik']) && !is_null($row['paving_loading']) && 
                                                            !is_null($row['perijinan']) && !is_null($row['sampah_parkir']) && !is_null($row['akses_jkm']) && 
                                                            !is_null($row['pkl']) && !is_null($row['cctv']) && !is_null($row['audio_system']) && 
                                                            !is_null($row['lan_infra']) && !is_null($row['internet_cust']) && !is_null($row['internet_km']) && 
                                                            !is_null($row['security']) && !is_null($row['cs']) && !is_null($row['post_content']) && 
                                                            !is_null($row['ojol']) && !is_null($row['tikor_maps']) && !is_null($row['qris']) && !is_null($row['edc'])) {

                                                            // Jika semua nilai ada, lakukan perhitungan
                                                            $total1 = rtrim(50 * number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2) / 100, '0') . (number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)[strlen(number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)) - 1] == '.' ? '0' : '');
                                                            $total2 = rtrim(25 * number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2) / 100, '0') . (number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)[strlen(number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)) - 1] == '.' ? '0' : '');
                                                            $total3 = rtrim(6 * number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2) / 100, '0') . (number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)[strlen(number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)) - 1] == '.' ? '0' : '');
                                                            $total4 = rtrim(8 * number_format(($row['security'] + $row['cs']) / 2, 2) / 100, '0') . (number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)[strlen(number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');
                                                            $total5 = rtrim(5 * number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2) / 100, '0') . (number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)[strlen(number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)) - 1] == '.' ? '0' : '');
                                                            $total6 = rtrim(6 * number_format(($row['qris'] + $row['edc']) / 2, 2) / 100, '0') . (number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)[strlen(number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');

                                                            $total = $total1 + $total2 + $total3 + $total4 + $total5 + $total6;
                                                            echo $total . "%"; 
                                                        } else {
                                                            // Jika ada nilai yang kosong, tidak menampilkan apa-apa
                                                            echo "-";
                                                        }
                                                    ?>
                                                </td> -->
                                                <td>
                                                    <?php 
                                                    if (!empty($row['month_1']) && !empty($row['month_2']) && !empty($row['month_3'])) {
                                                        $total = $row['month_1'] + $row['month_2'] + $row['month_3']; 
                                                        echo $total . "%";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $fat = (( !is_null($row['lamp_qris']) ? 100 : 0 ) + ( !is_null($row['lamp_st']) ? 100 : 0 )) / 2;

                                                        $academy = (( !is_null($row['kpt_1']) ? 100 : 0 ) + ( !is_null($row['kpt_2']) ? 100 : 0 ) + ( !is_null($row['kpt_3']) ? 100 : 0 )) / 3;

                                                        $hr = (( !is_null($row['tm']) ? 100 : 0 ) + ( !is_null($row['lamp_tm']) ? 100 : 0 ) + ( !is_null($row['ff_1']) ? 100 : 0 ) + ( !is_null($row['lamp_ff1']) ? 100 : 0 ) + ( !is_null($row['ff_2']) ? 100 : 0 ) + ( !is_null($row['lamp_ff2']) ? 100 : 0 ) + ( !is_null($row['ff_3']) ? 100 : 0 ) + ( !is_null($row['lamp_ff3']) ? 100 : 0 ) + ( !is_null($row['hot']) ? 100 : 0 ) + ( !is_null($row['lamp_hot']) ? 100 : 0 )) / 10;

                                                        $ir = (( !is_null($row['lamp_rabcs']) ? 100 : 0 ) + ( !is_null($row['lamp_rabsecurity']) ? 100 : 0 )) / 2;

                                                        $it = (( !is_null($row['kode_dvr']) ? 100 : 0 ) + ( !is_null($row['web_report']) ? 100 : 0 ) + ( !is_null($row['akun_gis']) ? 100 : 0 ) + ( !is_null($row['lamp_internet']) ? 100 : 0 ) + ( !is_null($row['lamp_cctv']) ? 100 : 0 ) + ( !is_null($row['lamp_config']) ? 100 : 0 ) + ( !is_null($row['lamp_printer']) ? 100 : 0 ) + ( !is_null($row['lamp_sound']) ? 100 : 0 )) / 8;

                                                        $legal = (( !is_null($row['mou_parkirsampah']) ? 100 : 0 ) + ( !is_null($row['lamp_pbg']) ? 100 : 0 ) + ( !is_null($row['lamp_permit']) ? 100 : 0 )) / 3;

                                                        $marketing = (( !is_null($row['gmaps']) ? 100 : 0 ) + ( !is_null($row['lamp_gmaps']) ? 100 : 0 ) + ( !is_null($row['id_m_shopee']) ? 100 : 0 ) + ( !is_null($row['id_m_gojek']) ? 100 : 0 ) + ( !is_null($row['id_m_grab']) ? 100 : 0 ) + ( !is_null($row['email_resto']) ? 100 : 0 ) + ( !is_null($row['lamp_merchant']) ? 100 : 0 )) / 7;

                                                        $scm = (( !is_null($row['lamp_sj']) ? 100 : 0 ));

                                                        $sdg = (( !is_null($row['lamp_ujilab']) ? 100 : 0 ) + ( !is_null($row['lamp_sumberair']) ? 100 : 0 ) + ( !is_null($row['lamp_filterair']) ? 100 : 0 ) + ( !is_null($row['lamp_slo']) ? 100 : 0 ) + ( !is_null($row['lamp_nidi']) ? 100 : 0 ) + ( !is_null($row['lampwo_reqipal']) ? 100 : 0 )) / 6;

                                                        $total = ($fat + $academy + $hr + $ir + $it + $legal + $marketing + $scm + $sdg) / 9;
                                                        $fix = number_format($total, 2);
                                                        echo $fix; 
                                                    ?>%
                                                </td>
                                                <td><?= $row['kualitas_go'] ?></td>
                                                <td>
                                                    <?php
                                                        // Tentukan warna badge berdasarkan status approval owner
                                                        $badge_color = '';
                                                        switch ($row['status_go']) {
                                                            case 'On Schedule':
                                                                $badge_color = 'success';
                                                                break;
                                                            case 'Hold':
                                                                $badge_color = 'danger';
                                                                break;
                                                            case 'Delayed':
                                                                $badge_color = 'warning';
                                                                break;
                                                            case 'Accelerated':
                                                                $badge_color = 'primary';
                                                                break;
                                                            default:
                                                                $badge_color = 'secondary'; // Warna default jika status tidak dikenali
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill badge-<?php echo $badge_color; ?>">
                                                        <?php echo $row['status_go']; ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['jml_hari'] ?></td>
                                                <td>
                                                <!-- Tombol Edit -->
                                                        <div>
                                                            <a href="operation/summary-soc-edit-form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning mr-2">
                                                            <i class="i-Pen-2"></i>
                                                            </a>
                                                        </div>
                                                </td>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Inventory Code</th>
                                                <th>Kode Store</th>
                                                <th>Nama Lokasi</th>
                                                <th>Alamat Lokasi</th>
                                                <th>Target GO</th>
                                                <th>Actual GO</th>
                                                <th>RTO Actual</th>
												<th>ST Equipment</th>
                                                <th>Type Kitchen</th>
                                                <th>Jam Operasional</th>
                                                <th>Project Sales</th>
												<th>Crew Needed</th>
                                                <th>SPK Release</th>
                                                <th>GO Construction Progress</th>
                                                <th>RTO Score</th>
												<th>Kualitas GO</th>
                                                <th>Status GO</th>
                                                <th>Jumlah Hari</th>
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