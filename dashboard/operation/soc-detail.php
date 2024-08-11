<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";
// Periksa apakah ada data yang dikirimkan melalui URL (ID)
if(isset($_GET['id'])) {
    // Ambil ID dari URL
    $id = $_GET['id'];

    // Query untuk mengambil data dari tabel land
$sql = "
SELECT 
    soc_fat.*, 
    soc_hrga.*, 
    soc_it.*, 
    soc_legal.*, 
    soc_marketing.*, 
    soc_rto.*, 
    soc_sdg.*, 
    note_ba.*, 
    note_legal.*,
    doc_legal.*,
    sign.*,
    land.kode_lahan, 
    land.nama_lahan, 
    land.lokasi, 
    resto.nama_store
FROM soc_fat 
INNER JOIN soc_hrga ON soc_fat.kode_lahan = soc_hrga.kode_lahan
INNER JOIN soc_it ON soc_fat.kode_lahan = soc_it.kode_lahan
INNER JOIN soc_legal ON soc_fat.kode_lahan = soc_legal.kode_lahan
INNER JOIN soc_marketing ON soc_fat.kode_lahan = soc_marketing.kode_lahan
INNER JOIN soc_rto ON soc_fat.kode_lahan = soc_rto.kode_lahan
INNER JOIN soc_sdg ON soc_fat.kode_lahan = soc_sdg.kode_lahan
INNER JOIN note_ba ON soc_fat.kode_lahan = note_ba.kode_lahan
INNER JOIN note_legal ON soc_fat.kode_lahan = note_legal.kode_lahan
INNER JOIN doc_legal ON note_legal.kode_lahan = doc_legal.kode_lahan
INNER JOIN sign ON soc_fat.kode_lahan = sign.kode_lahan
INNER JOIN land ON soc_fat.kode_lahan = land.kode_lahan
INNER JOIN resto ON soc_fat.kode_lahan = resto.kode_lahan
WHERE soc_fat.id = $id";
$result = $conn->query($sql);

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        // Ambil data resep
        $row = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
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
    <link href="../../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="text-left">
    <div class="app-admin-wrap layout-sidebar-compact sidebar-dark-purple sidenav-open clearfix">
		<?php
			include '../../layouts/right-sidebar-data.php';
		?>

        <!--=============== Left side End ================-->
        <div class="main-content-wrap d-flex flex-column">
			<?php
			include '../../layouts/top-sidebar.php';
		?>

			<!-- ============ Body content start ============= -->
            <div class="main-content">
                <div class="breadcrumb">
                    <h1>SOC Data RTO</h1>
                    <ul>
                        <li><a href="href">Detail</a></li>
                        <li>SOC Data RTO</li>
                        <li>
                        <button class="btn btn-primary" onclick="printFormContent()">Print Form Content</button>
                        </li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    
                <div class="col-12">
                    <div class="card mb-5">
                        <div class="card-body printable" id="print-content">
                            <form>
                                <div class="container">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="row">
                                    <div class="col-12 text-center mb-5">
                                        <legend style="color: #538392; padding: 5px; font-weight: bold;">Form Scoring RTO</legend>
                                        <p style="color: #538392; font-weight: bold; position: absolute; top: 0; right: 20px;">Score RTO 
                                            <?php 
                                                $total1 = rtrim(50 * number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2) / 100, '0') . (number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)[strlen(number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)) - 1] == '.' ? '0' : '');
                                                $total2 = rtrim(25 * number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2) / 100, '0') . (number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)[strlen(number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)) - 1] == '.' ? '0' : '');
                                                $total3 = rtrim(6 * number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2) / 100, '0') . (number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)[strlen(number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)) - 1] == '.' ? '0' : '');
                                                $total4 = rtrim(8 * number_format(($row['security'] + $row['cs']) / 2, 2) / 100, '0') . (number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)[strlen(number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');
                                                $total5 = rtrim(5 * number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2) / 100, '0') . (number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)[strlen(number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)) - 1] == '.' ? '0' : '');
                                                $total6 = rtrim(6 * number_format(($row['qris'] + $row['edc']) / 2, 2) / 100, '0') . (number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)[strlen(number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');

                                                $total = $total1 + $total2 + $total3 + $total4 + $total5 + $total6;
                                                echo $total; 
                                            ?>%
                                        </p>
                                        <h5 style="color: #538392; padding: 1px; font-weight: bold;">Ready to Open (<?php echo $row['kode_lahan']; ?> - <?php echo $row['nama_store']; ?>)</h5>
                                        <h6 style="color: #538392; padding: 1px; font-weight: bold;">Tanggal RTO <?php echo $row['rto_date']; ?></h6>
                                    </div>
                                </div>
                                <!-- Tabel soc_rto -->
                                <div class="section-title"></div>
                                <div class="row mb-5">
                                    <div class="col-5" style="margin-bottom: 1px;">
                                        <div class="form-group" style="margin-bottom: 1px;">
                                            <label class="col-form-label" style="padding-right: 0; margin-bottom: 1px;">Diajukan Oleh</label>
                                            <p class="form-control-static" style="margin-bottom: 0;"><?php echo $row['pengaju_rto']; ?></p>
                                        </div>
                                        <div class="form-group" style="margin-bottom: 1px;">
                                            <label class="col-form-label" style="padding-right: 0; margin-bottom: 1px;">Diperiksa Bersama Oleh</label>
                                            <p class="form-control-static" style="margin-bottom: 0;"><?php echo $row['status_op']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-1" style="margin-bottom: 1px;">
                                        <div class="form-group row" style="margin-bottom: 1px; display: flex; flex-direction: column; align-items: flex-start;">
                                            <label class="col-sm-12 col-form-label" style="padding-right: 0; margin-bottom: 0;">File RTO</label>
                                            <div class="form-control-static" style="padding-left: 10px;">
                                                <?php
                                                $lamp_rto_files = explode(",", $row['lamp_rto']);
                                                foreach ($lamp_rto_files as $file): ?>
                                                    <a href="../uploads/<?php echo $file; ?>" target="_blank" style="display: block; margin-top: 5px;">
                                                        <i class="bi bi-file-earmark" style="font-size: 48px;"></i>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6" style="margin-bottom: 1px;">
                                        <div class="form-group row" style="margin-bottom: 1px; display: flex; align-items: center;">
                                            <label class="col-sm-5 col-form-label" style="padding-right: 0;">Sukses RTO</label>
                                            <div class="col-sm-7" style="padding-left: 0;">
                                                <p class="form-control-static" style="margin-bottom: 0;">Skor RTO > 94% 
                                                    <?php if($total > 94): ?>
                                                        <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-bottom: 1px; display: flex; align-items: center;">
                                            <label class="col-sm-5 col-form-label" style="padding-right: 0;">Failed RTO - Proceed GO</label>
                                            <div class="col-sm-7" style="padding-left: 0;">
                                                <p class="form-control-static" style="margin-bottom: 0; ">Skor RTO 75% - 94%</p>
                                                <?php if($total > 75 && $total < 94): ?>
                                                    <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin-bottom: 1px; display: flex; align-items: center;">
                                            <label class="col-sm-5 col-form-label" style="padding-right: 0;">Failed RTO - Failed GO</label>
                                            <div class="col-sm-7" style="padding-left: 0;">
                                                    <?php if($total < 75): ?>
                                                        <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                    <?php endif; ?>
                                                <p class="form-control-static" style="margin-bottom: 0;">Skor RTO 75%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                
                                <!-- Tabel soc_sdg -->
                                <div class="section-title"></div>
                                <h6 style="color: #538392; padding: 1px; font-weight: bold; text-align:center;">Kelaikan Pembukaan Store</h6>
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-10">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr>
                                                    <td rowspan="4" class="text-center" style="background-color: grey; color: white;">No</td>
                                                    <td rowspan="4" colspan="20" class="text-center" style="background-color: grey; color: white;">Parameter</td>
                                                    <td colspan="5" class="text-center" style="background-color: grey; color: white;">Target</td>
                                                    <td colspan="10" class="text-center" style="background-color: #D3D3D3;">Score RTO</td>
                                                </tr>
                                                <tr></tr>
                                                <tr>
                                                    <td colspan="5" class="text-center" style="background-color: grey; color: white;">Bobot</td>
                                                    <td colspan="5" class="text-center" style="background-color: #D3D3D3;">Total Bobot Score</td>
                                                    <td colspan="5" class="text-center" style="background-color: #D3D3D3;">Nilai #3</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-center" style="background-color: grey; color: white;">e</td>
                                                    <td colspan="5" class="text-center" style="background-color: #D3D3D3;">f=d</td>
                                                    <td colspan="5" class="text-center" style="background-color: #D3D3D3;">g=exf</td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="26" class="text-center">Rekapitulasi</td>
                                                    <td colspan="5" class="text-center">100%</td>
                                                    <td colspan="5" class="text-center"><?php 
                                                    $total1 = rtrim(50 * number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2) / 100, '0') . (number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)[strlen(number_format(50 * (($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6 / 100), 2)) - 1] == '.' ? '0' : '');
                                                    $total2 = rtrim(25 * number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2) / 100, '0') . (number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)[strlen(number_format(25 * (($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4 / 100), 2)) - 1] == '.' ? '0' : '');
                                                    $total3 = rtrim(6 * number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2) / 100, '0') . (number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)[strlen(number_format(6 * (($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5 / 100), 2)) - 1] == '.' ? '0' : '');
                                                    $total4 = rtrim(8 * number_format(($row['security'] + $row['cs']) / 2, 2) / 100, '0') . (number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)[strlen(number_format(8 * (($row['security'] + $row['cs']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');
                                                    $total5 = rtrim(5 * number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2) / 100, '0') . (number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)[strlen(number_format(5 * (($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3 / 100), 2)) - 1] == '.' ? '0' : '');
                                                    $total6 = rtrim(6 * number_format(($row['qris'] + $row['edc']) / 2, 2) / 100, '0') . (number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)[strlen(number_format(6 * (($row['qris'] + $row['edc']) / 2 / 100), 2)) - 1] == '.' ? '0' : '');

                                                    $total = $total1 + $total2 + $total3 + $total4 + $total5 + $total6;
                                                    echo $total; 
                                                    ?>%</td>

                                                </tr>
                                                <!-- Data rows -->
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">SDG</td>
                                                    <td colspan="5" class="text-center">50%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 50 * number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2) / 100; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">Legal</td>
                                                    <td colspan="5" class="text-center">25%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 25 * number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2) / 100; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td colspan="20">IT</td>
                                                    <td colspan="5" class="text-center">6%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 6 * number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2) / 100; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td colspan="20">HRGA</td>
                                                    <td colspan="5" class="text-center">8%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['security'] + $row['cs']) / 2, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 8 * number_format(($row['security'] + $row['cs']) / 2, 2) / 100; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td colspan="20">Marketing</td>
                                                    <td colspan="5" class="text-center">5%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 5 * number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2) / 100; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>6</td>
                                                    <td colspan="20">FAT</td>
                                                    <td colspan="5" class="text-center">6%</td>
                                                    <td colspan="5" class="text-center"><?php echo number_format(($row['qris'] + $row['edc']) / 2, 2); ?>%</td>
                                                    <td colspan="5" class="text-center"><?php echo 6 * number_format(($row['qris'] + $row['edc']) / 2, 2) / 100; ?>%</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-12">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr style="background-color: #D3D3D3;">
                                                    <td rowspan="1" colspan="80" class="text-center">Diajukan dan Diperiksa Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diperiksa dan Disetujui Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diketaui Oleh</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center"><?php echo ($row['sdg_pm'] == "on") ? 'Assigned' : $row['sdg_pm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['legal_alo'] == "on") ? 'Assigned' : $row['legal_alo']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['scm'] == "on") ? 'Assigned' : $row['scm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['it_hrga_marketing'] == "on") ? 'Assigned' : $row['it_hrga_marketing']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['ops_rm'] == "on") ? 'Assigned' : $row['ops_rm']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['sdg_head'] == "on") ? 'Assigned' : $row['sdg_head']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center">SDG-PM</td>
                                                    <td colspan="20" class="text-center">Legal-ALO</td>
                                                    <td colspan="20" class="text-center">SCM</td>
                                                    <td colspan="20" class="text-center">IT-HRGA-Marketing</td>
                                                    <td colspan="10" class="text-center">OPS - Region Manager</td>
                                                    <td colspan="10" class="text-center">SDG-Head of SDG</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- view table scoring -->
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-10">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr style="background-color: #A9A9A9;">
                                                    <td rowspan="4" class="text-center">No</td>
                                                    <td rowspan="4" colspan="20" class="text-center">Parameter</td>
                                                    <td rowspan="4" colspan="20" class="text-center">Pengukuran</td>
                                                    <td colspan="10" rowspan="2" class="text-center">Target</td>
                                                    <td colspan="10" rowspan="2" class="text-center">Aktual</td>
                                                    <td colspan="10" rowspan="4" class="text-center">Note</td>
                                                </tr>
                                                <tr></tr>
                                                <tr style="background-color: #A9A9A9;">
                                                    <td colspan="5" class="text-center">Bobot</td>
                                                    <td colspan="5" class="text-center">Nilai #1</td>
                                                    <td colspan="5" class="text-center">Nilai #2</td>
                                                    <td colspan="5" class="text-center">Total Bobot</td>
                                                </tr>
                                                <tr  style="background-color: #A9A9A9;">
                                                    <td colspan="5" class="text-center">a</td>
                                                    <td colspan="5" class="text-center">b</td>
                                                    <td colspan="5" class="text-center">c</td>
                                                    <td colspan="5" class="text-center">d = cxa</td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">I</td>
                                                    <td colspan="40" class="text-center">SDG</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5">600%</td>
                                                    <td colspan="5"><?php echo $row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']; ?>%</td>
                                                    <td colspan="5"><?php echo number_format(($row['bangunan_mural'] + $row['daya_listrik'] + $row['supply_air'] + $row['aliran_air'] + $row['kualitas_keramik'] + $row['paving_loading']) / 6, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <!-- Data rows -->
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">Pekerjaan Bangunan+Mural Selesai 100% (Incl.Pylon Sign )</td>
                                                    <td colspan="20">Temuan Defect Item 
                                                    0 : Ada item yang belum diselesaikan (Merujuk pada RAB/BQ)
                                                    1 : Jika pekerjaan selesai komplit sudah tidak ada tukang dan kondisi bersih</td>
                                                    <td colspan="5" class="text-center">30%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['bangunan_mural']; ?>%</td>
                                                    <td colspan="5"><?php echo 30 * $row['bangunan_mural'] / 100;?>%</td>
                                                    <td colspan="5"><?php echo $row['note_bm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td rowspan="6">2</td>
                                                    <td colspan="65">- Kualitas dan Fungsional Store:</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20"> a. Ketersedian Daya Listrik Sudah Sesuai PLN** (Mandatory 100%) </td>
                                                    <td colspan="20">Running Test Semua Electric Equipment dalam kurun waktu 8 Jam
                                                    0 : Ada trip issue
                                                    1 : Tidak Ada Trip</td>
                                                    <td colspan="5" class="text-center">15%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['daya_listrik']; ?>%</td>
                                                    <td colspan="5"><?php echo 15 * $row['daya_listrik'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_bm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20"> b. Ketersediaan Suply Air** (Mandatory 100%)</td>
                                                    <td colspan="20">Memenuhi Debit Air : Standart 0,7 Liter/Detik Untuk Sumur Bor
                                                    Kualitas Air : Sesuai Permenkes No.32 Tahun 2017</td>
                                                    <td colspan="5" class="text-center">20%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['supply_air']; ?>%</td>
                                                    <td colspan="5"><?php echo 20 * $row['supply_air'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_sa']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">c. Elevasi Semua Aliran Air (Paving, Kitchen, IPAL, Fold Drain) </td>
                                                    <td colspan="20">Penilaian defect :
                                                    0 : Jika Ada 1 Titik Genangan Air
                                                    1 : Jika Tidak Ada Genangan Sama Sekali</td>
                                                    <td colspan="5" class="text-center">20%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['aliran_air']; ?>%</td>
                                                    <td colspan="5"><?php echo 20 * $row['aliran_air'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_aa']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">d. Kualitas Pemasangan Keramik (Kitchen, Indoor, Outdoor, Penunjang)</td>
                                                    <td colspan="20">Luasan Defect / Total Luas Area (cacat 1 keramik dihitung 1 lembar)</td>
                                                    <td colspan="5" class="text-center">10%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['kualitas_keramik']; ?>%</td>
                                                    <td colspan="5"><?php echo 10 * $row['kualitas_keramik'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_kk']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">e. Kualitas Paving dan Loading Area</td>
                                                    <td colspan="20">Luasan defect / total luas area parkir</td>
                                                    <td colspan="5" class="text-center">5%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['paving_loading']; ?>%</td>
                                                    <td colspan="5"><?php echo 5 * $row['paving_loading'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_pl']; ?></td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">II</td>
                                                    <td colspan="40" class="text-center">Legal</td>
                                                    <td colspan="5">100%</td>
                                                        <td colspan="5">400%</td>
                                                        <td colspan="5"><?php echo $row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']; ?>%</td>
                                                        <td colspan="5"><?php echo number_format(($row['perijinan'] + $row['sampah_parkir'] + $row['akses_jkm'] + $row['pkl']) / 4, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">Perijinan Selesai (Tentative Sampai GO Mei 2024)</td>
                                                    <td colspan="20">NIB, TDUP, SKRK, SPPL/UKL-UPL</td>
                                                    <td colspan="5" class="text-center">15%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['perijinan']; ?>%</td>
                                                    <td colspan="5"><?php echo 15 *  $row['perijinan'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_p']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">PIC Sampah & Parkir Selesai**(Mandatory 100%)</td>
                                                    <td colspan="20">MoU Parkir & Sampah</td>
                                                    <td colspan="5" class="text-center">40%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['sampah_parkir']; ?>%</td>
                                                    <td colspan="5"><?php echo 40 * $row['sampah_parkir'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_sp']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td colspan="20">Akses JKM</td>
                                                    <td colspan="20">Mengacu pada proses Andalalin</td>
                                                    <td colspan="5" class="text-center">5%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['akses_jkm']; ?>%</td>
                                                    <td colspan="5"><?php echo 5 * $row['akses_jkm'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_ajkm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td colspan="20">Entrance Tidak Ada Obstacle PKL** (Mandatory 100%)</td>
                                                    <td colspan="20"></td>
                                                    <td colspan="5" class="text-center">40%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['pkl']; ?>%</td>
                                                    <td colspan="5"><?php echo 40 * $row['pkl'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_pkl']; ?></td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">III</td>
                                                    <td colspan="40" class="text-center">IT</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5">500%</td>
                                                    <td colspan="5"><?php echo $row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']; ?>%</td>
                                                    <td colspan="5"><?php echo number_format(($row['cctv'] + $row['audio_system'] + $row['lan_infra'] + $row['internet_cust'] + $row['internet_km']) / 5, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">CCTV</td>
                                                    <td colspan="20">Mengacu pada plotting penempatan IT</td>
                                                    <td colspan="5" class="text-center">30%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['cctv']; ?>%</td>
                                                    <td colspan="5"><?php echo 30 * $row['cctv'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_cctv']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">Audio System</td>
                                                    <td colspan="20">Kedatangan H-3 s.d H-5 dari GO</td>
                                                    <td colspan="5" class="text-center">20%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['audio_system']; ?>%</td>
                                                    <td colspan="5"><?php echo 20 * $row['audio_system'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_as']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td colspan="20">Jaringan LAN</td>
                                                    <td colspan="20">Kedatangan H-3 s.d H-5 dari GO</td>
                                                    <td colspan="5" class="text-center">20%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['lan_infra']; ?>%</td>
                                                    <td colspan="5"><?php echo 20 * $row['lan_infra'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_lan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td colspan="20">Internet Kasir & Manager (Biznet / Indihome dll)</td>
                                                    <td colspan="20">Memastikan bisa connect saat RTO</td>
                                                    <td colspan="5" class="text-center">15%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['internet_km']; ?>%</td>
                                                    <td colspan="5"><?php echo 15 * $row['internet_km'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_interkm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td colspan="20">Internet Customer (Telkom WMS)</td>
                                                    <td colspan="20">Memastikan bisa connect saat RTO</td>
                                                    <td colspan="5" class="text-center">15%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['internet_cust']; ?>%</td>
                                                    <td colspan="5"><?php echo 15 * $row['internet_cust'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_intercut']; ?></td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">IV</td>
                                                    <td colspan="40" class="text-center">HRGA</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5">200%</td>
                                                    <td colspan="5"><?php echo $row['security'] + $row['cs']; ?>%</td>
                                                    <td colspan="5"><?php echo number_format(($row['security'] + $row['cs']) / 2, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">Security</td>
                                                    <td colspan="20">MOU Security</td>
                                                    <td colspan="5" class="text-center">50%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['security']; ?>%</td>
                                                    <td colspan="5"><?php echo 50 * $row['security'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_security']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">Cleaning Service</td>
                                                    <td colspan="20">MOU Cleaning Service</td>
                                                    <td colspan="5" class="text-center">50%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['cs']; ?>%</td>
                                                    <td colspan="5"><?php echo 50 * $row['cs'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_cs']; ?></td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">V</td>
                                                    <td colspan="40" class="text-center">Marketing</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5">300%</td>
                                                    <td colspan="5"><?php echo $row['post_content'] + $row['ojol']  + $row['tikor_maps']; ?>%</td>
                                                    <td colspan="5"><?php echo number_format(($row['post_content'] + $row['ojol'] + $row['tikor_maps']) / 3, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">Posting Content 100%</td>
                                                    <td colspan="20">Mengacu Status Upload </td>
                                                    <td colspan="5" class="text-center">35%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['post_content']; ?>%</td>
                                                    <td colspan="5"><?php echo 35 * $row['post_content'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_pc']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">Aktif Ojol/Agregator 100%</td>
                                                    <td colspan="20">Daftar, Promo, ID ke Resto dan Tikor </td>
                                                    <td colspan="5" class="text-center">45%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['ojol']; ?>%</td>
                                                    <td colspan="5"><?php echo 45 * $row['ojol'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_ojol']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td colspan="20">Pendaftaran Tikor G-Maps</td>
                                                    <td colspan="20">Lokasi di G-Maps </td>
                                                    <td colspan="5" class="text-center">20%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['tikor_maps']; ?>%</td>
                                                    <td colspan="5"><?php echo 20 * $row['tikor_maps'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_tm']; ?></td>
                                                </tr>
                                                <tr style="background-color: #D3D3D3;">
                                                    <td colspan="1" class="text-center">VI</td>
                                                    <td colspan="40" class="text-center">FAT</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5">200%</td>
                                                    <td colspan="5"><?php echo $row['qris'] + $row['edc']; ?>%</td>
                                                    <td colspan="5"><?php echo number_format(($row['qris'] + $row['edc']) / 2, 2); ?>%</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td colspan="20">Fulfillment Media Pembayaran (QRIS)</td>
                                                    <td colspan="20"></td>
                                                    <td colspan="5" class="text-center">50%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['qris']; ?>%</td>
                                                    <td colspan="5"><?php echo 50 * $row['qris'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_qris']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td colspan="20">Ketersediaan mesin EDC</td>
                                                    <td colspan="20"></td>
                                                    <td colspan="5" class="text-center">50%</td>
                                                    <td colspan="5">100%</td>
                                                    <td colspan="5"><?php echo $row['edc']; ?>%</td>
                                                    <td colspan="5"><?php echo 50 * $row['edc'] / 100; ?>%</td>
                                                    <td colspan="5"><?php echo $row['note_edc']; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-12">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr style="background-color: #D3D3D3;">
                                                    <td rowspan="1" colspan="80" class="text-center">Diajukan dan Diperiksa Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diperiksa dan Disetujui Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diketaui Oleh</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center"><?php echo ($row['sdg_pm'] == "on") ? 'Assigned' : $row['sdg_pm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['legal_alo'] == "on") ? 'Assigned' : $row['legal_alo']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['scm'] == "on") ? 'Assigned' : $row['scm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['it_hrga_marketing'] == "on") ? 'Assigned' : $row['it_hrga_marketing']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['ops_rm'] == "on") ? 'Assigned' : $row['ops_rm']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['sdg_head'] == "on") ? 'Assigned' : $row['sdg_head']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center">SDG-PM</td>
                                                    <td colspan="20" class="text-center">Legal-ALO</td>
                                                    <td colspan="20" class="text-center">SCM</td>
                                                    <td colspan="20" class="text-center">IT-HRGA-Marketing</td>
                                                    <td colspan="10" class="text-center">OPS - Region Manager</td>
                                                    <td colspan="10" class="text-center">SDG-Head of SDG</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-10">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr style="background-color: #D3D3D3;">
                                                    <td rowspan="1" class="text-center">No</td>
                                                    <td rowspan="1" colspan="20" class="text-center">Dokumen</td>
                                                    <td rowspan="1" colspan="20" class="text-center">Check</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Note</td>
                                                </tr>
                                                <tr>
                                                    <td rowspan="9">1</td>
                                                    <td colspan="20" rowspan="9">Dokumen Berita Acara</td>
                                                    <td colspan="20">BAST Pekerjaan dan Form Defect List ST
                                                        <?php if ($row['bast_defectlist'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['bast_defectlist'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Form Checklist Supervisi
                                                        <?php if ($row['check_supervisi'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['check_supervisi'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_checkspv']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Form Pengukuran Aktual Bangunan (Termasuk Kitchen)
                                                        <?php if ($row['pengukuran'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['pengukuran'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_pengukuran']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Form Test-Test MEP : Test Rendam Area Basah, Test Plumbing 
                                                    Air Kotor dan Air Bersih (Test Gelontor dan Pompa),
                                                        <?php if ($row['test_mep'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['test_mep'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_testmep']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Test Insulasi/Resistansi Isolasi/Merger, Test Grounding, Test Nyala Lampu & Kipas.
                                                        <?php if ($row['test_mep'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['test_mep'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_testmep']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">BAST dan Form Checklist Equipment (ST-Equipment) : Kelengkapan, Kualitas dan Fungsi Kitchen Equiment
                                                        <?php if ($row['st_eqp'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['st_eqp'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_steqp']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Stainless Steel, Restomart, Genset, Gutter, Freezer, Chiller Undercounter, Cold Storage, Meubellair (Meja Kursi Kayu)
                                                        <?php if ($row['st_eqp'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['st_eqp'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_steqp']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">KWH Meter PLN
                                                        <?php if ($row['kwh'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['kwh'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_kwh']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">PDAM, No meter
                                                        <?php if ($row['pdam'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['pdam'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_pdam']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td rowspan="15">2</td>
                                                    <td colspan="20" rowspan="15">Dokumen Perijinan Legal</td>
                                                    <td colspan="20">As Built Drawing & Spesifikasi Teknis
                                                        <?php if ($row['draw_teknis'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['draw_teknis'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">MoU Parkir**
                                                        <?php if ($row['mou_parkir'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['mou_parkir'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Surat Keteragan Rencana Kota ( SKRK ) / (PKKPR)
                                                        <?php if ($row['pkkpr'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['pkkpr'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">No Induk Berusaha ( NIB )
                                                        <?php if ($row['nib'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['nib'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Tanda Daftar Usaha Pariwisata (TDUP)
                                                        <?php if ($row['tdup'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['tdup'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Ijin Reklame
                                                        <?php if ($row['reklame'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['reklame'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Berita Acara Serah Terima Kunci
                                                        <?php if ($row['ba_serahterima'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['ba_serahterima'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Informasi pengelolaan sampah**
                                                        <?php if ($row['info_sampah'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['info_sampah'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Surat Undangan acara Tasyakuran
                                                        <?php if ($row['und_tasyakuran'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['und_tasyakuran'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Ijin Mendirikan Bangunan (IMB/PBG)
                                                        <?php if ($row['imb'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['imb'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Permohonan Self Assesment ke BPBDPM
                                                        <?php if ($row['bpbdpm'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['bpbdpm'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">SPPL/ UKL-UPL
                                                        <?php if ($row['sppl'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['sppl'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Rekom Damkar (***optional berdasarkan lokasi)
                                                        <?php if ($row['damkar'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['damkar'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Peil Banjir (***optional berdasarkan lokasi)
                                                        <?php if ($row['peil_banjir'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['peil_banjir'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20">Analisis Dampak Lalu Lintas (ANDALALIN)-(*optional berdasarkan lokasi)
                                                        <?php if ($row['andalalin'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['andalalin'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td rowspan="9">3</td>
                                                    <td colspan="20" rowspan="9">Catatan Khusus</td>
                                                    <td colspan="20">Informasi iuran warga (*optional berdasarkan lokasi)
                                                        <?php if ($row['iuran_warga'] == "on"): ?>
                                                            <i class="bi bi-check-circle-fill" style="color: green; font-size: 1.2rem;"></i>
                                                        <?php elseif ($row['iuran_warga'] == "off"): ?>
                                                            <i class="bi bi-x-circle-fill" style="color: red; font-size: 1.2rem;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td colspan="10"><?php echo $row['note_bdl']; ?></td>
                                                </tr>
                                            </table>
                                                    <p style="margin-bottom:1px;">(*) = Input nilai 0% - 100%</p>
                                                    <p>(**) = Item Mandatory HARUS 100%, Jika Tidak Tercapai 100% Failed RTO, Failed GO</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="table-responsive col-lg-12">
                                            <table class="table table-bordered" style="margin-bottom: 20px;">
                                                <tr style="background-color: #D3D3D3;">
                                                    <td rowspan="1" colspan="80" class="text-center">Diajukan dan Diperiksa Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diperiksa dan Disetujui Oleh</td>
                                                    <td rowspan="1" colspan="10" class="text-center">Diketaui Oleh</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center"><?php echo ($row['sdg_pm'] == "on") ? 'Assigned' : $row['sdg_pm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['legal_alo'] == "on") ? 'Assigned' : $row['legal_alo']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['scm'] == "on") ? 'Assigned' : $row['scm']; ?></td>
                                                    <td colspan="20" class="text-center"><?php echo ($row['it_hrga_marketing'] == "on") ? 'Assigned' : $row['it_hrga_marketing']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['ops_rm'] == "on") ? 'Assigned' : $row['ops_rm']; ?></td>
                                                    <td colspan="10" class="text-center"><?php echo ($row['sdg_head'] == "on") ? 'Assigned' : $row['sdg_head']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="20" class="text-center">SDG-PM</td>
                                                    <td colspan="20" class="text-center">Legal-ALO</td>
                                                    <td colspan="20" class="text-center">SCM</td>
                                                    <td colspan="20" class="text-center">IT-HRGA-Marketing</td>
                                                    <td colspan="10" class="text-center">OPS - Region Manager</td>
                                                    <td colspan="10" class="text-center">SDG-Head of SDG</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tambahkan form field lainnya untuk tabel sign -->

                            </form>
                        </div>
                    </div>
                </div>
				<!-- end of main-content -->
                <!-- Footer Start -->
                <div class="flex-grow-1"></div>
                <!-- fotter end -->
            </div>
        </div>
    </div>
    <!-- ============ Search UI Start ============= -->
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
    <script src="../../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../dist-assets/js/scripts/script.min.js"></script>
    <script src="../../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../../dist-assets/js/scripts/customizer.script.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js"></script>
    
    <script>
        // Tambahkan event listener untuk radio button ganti_lampiran
        document.querySelectorAll('input[name="ganti_lampiran"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'ya') {
                    // Jika pilihannya ya, tampilkan input untuk unggah file baru
                    document.getElementById('lampiran_baru').style.display = 'block';
                } else {
                    // Jika pilihannya tidak, sembunyikan input untuk unggah file baru
                    document.getElementById('lampiran_baru').style.display = 'none';
                }
            });
        });
    </script>
    <script>
    function printPDF() {
        window.print();
    }
</script>
<!-- <script>
    // Inisialisasi Dropzone
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#dropzone", {
        url: "upload.php", // Tentukan URL untuk mengunggah file
        addRemoveLinks: true, // Tampilkan tautan hapus untuk setiap file yang diunggah
        acceptedFiles: "application/pdf, image/jpeg, image/jpg, image/png",
 // Jenis file yang diizinkan (dalam contoh ini, hanya file PDF yang diizinkan)
        init: function() {
            // Aksi setelah unggahan selesai
            this.on("complete", function(file) {
                // Hapus file dari Dropzone jika unggahan berhasil
                if (file.status === "success") {
                    myDropzone.removeFile(file);
                }
            });
        }
    });

    // Tambahkan file yang sudah ada ke Dropzone saat inisialisasi
    var existingFiles = "<?php echo $row['lamp_land']; ?>";
    if (existingFiles) {
        var files = existingFiles.split(",");
        for (var i = 0; i < files.length; i++) {
            var mockFile = { name: files[i], size: 12345 }; // Ganti 12345 dengan ukuran sebenarnya jika diketahui
            myDropzone.emit("addedfile", mockFile);
            myDropzone.emit("thumbnail", mockFile, "path/to/thumbnail"); // Ganti path/to/thumbnail dengan URL thumbnail yang sesuai
            myDropzone.emit("complete", mockFile);
        }
    }
</script> -->
<script>
function printFormContent() {
    var formContent = document.getElementById('print-content').innerHTML;
    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print Form Content</title>');
    // Salin seluruh gaya CSS dari dokumen saat ini ke dokumen cetak
    var stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
    stylesheets.forEach(function(stylesheet) {
        printWindow.document.write('<link rel="stylesheet" href="' + stylesheet.href + '">');
    });
    printWindow.document.write('</head><body>');
    printWindow.document.write(formContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>


</body>

</html>