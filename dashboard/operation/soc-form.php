<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Query untuk mengambil semua kode lahan yang sudah ada dalam tabel land
$result_existing = $conn->query("SELECT DISTINCT kode_lahan FROM (
    SELECT kode_lahan FROM soc_fat
    UNION
    SELECT kode_lahan FROM soc_hrga
    UNION
    SELECT kode_lahan FROM soc_it
    UNION
    SELECT kode_lahan FROM soc_legal
    UNION
    SELECT kode_lahan FROM soc_marketing
    UNION
    SELECT kode_lahan FROM soc_rto
    UNION
    SELECT kode_lahan FROM soc_sdg
    UNION
    SELECT kode_lahan FROM note_ba
    UNION
    SELECT kode_lahan FROM doc_legal
    UNION
    SELECT kode_lahan FROM note_legal
) AS combined_tables");

// Simpan kode lahan yang sudah ada ke dalam array
$existing_land_codes = array();
while ($row_existing = $result_existing->fetch_assoc()) {
    $existing_land_codes[] = $row_existing['kode_lahan'];
}

// Query untuk mengambil semua kode lahan yang sudah disetujui
$result = $conn->query("SELECT DISTINCT kode_lahan FROM resto WHERE status_kom = 'On Going Project'");

// Buat opsi-opsi untuk formulir
$land_options = "";
while ($row = $result->fetch_assoc()) {
    // Tambahkan opsi hanya jika kode lahan tidak ada dalam array existing_land_codes
    if (!in_array($row["kode_lahan"], $existing_land_codes)) {
        $land_options .= "<option value='{$row["kode_lahan"]}'>{$row["kode_lahan"]}</option>";
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
                    <h1>SOC to RTO</h1>
                    <ul>
                        <li><a href="href">Form</a></li>
                        <li>SOC to RTO</li>
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card mb-5">
                                <div class="card-body">
                                    <form method="post" action="soc-process.php" enctype="multipart/form-data">
                                            <legend style="color: #538392; padding: 5px; font-weight: bold; text-align:center;">Form Scoring RTO</legend>
                                        <div class="form-group">
                                            <label for="kode_lokasi">Kode Lahan</label>
                                            <select class="form-control" id="kode_lokasi" name="kode_lahan">
                                                <option value="">Pilih Kode Lahan</option>
                                                <?php echo $land_options; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_lahan">Nama Lahan</label>
                                            <input class="form-control" id="nama_lahan" name="nama_lahan" type="text" placeholder="Nama Lahan" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                            <input class="form-control" id="lokasi" name="lokasi" type="text" placeholder="Lokasi" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_store">Nama Store</label>
                                            <input class="form-control" id="nama_store" name="nama_store" type="text" placeholder="Nama Store" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label for="rto_date">Tanggal RTO</label>
                                            <input class="form-control" id="rto_date" name="rto_date" type="date" placeholder="Tanggal RTO" />
                                        </div>
                                        <div class="form-group">
                                            <label for="pengaju_rto">Pengaju RTO</label>
                                            <input class="form-control" id="pengaju_rto" name="pengaju_rto" type="text" placeholder="Diajukan Oleh" />
                                        </div>
                                        <div class="form-group">
                                            <label for="status_op">Diperiksa Bersama Oleh</label>
                                            <input class="form-control" id="status_op" name="status_op" type="text" placeholder="Diperiksa Bersama Oleh" />
                                        </div>

                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">SDG</legend>
                                            <div class="form-group">
                                                <label for="bangunan_mural">
                                                    <h5 style="margin-bottom: 2px; font-weight: bold;">1. Pekerjaan Bangunan + Mural Selesai 100% (Incl.Pylon Sign)</h5>
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">Pekerjaan Bangunan + Mural Selesai 100% (Incl.Pylon Sign)</h6>
                                                    <p style="margin-bottom: 2px;">Temuan Defect Item</p>
                                                    <p style="margin-bottom: 2px;">0 : Ada item yang belum diselesaikan (Merujuk pada RAB/BQ)</p>
                                                    <p style="margin-bottom: 2px;">1 : Jika pekerjaan selesai komplit sudah tidak ada tukang dan kondisi bersih</p>
                                                </label>
                                                <input class="form-control" id="bangunan_mural" name="bangunan_mural" type="text" placeholder="Pekerjaan Bangunan + Mural"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="note_bm">Note</label>
                                                <input class="form-control" id="note_bm" name="note_bm" type="text" placeholder="Note"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="daya_listrik">
                                                    <h5 style="margin-bottom: 2px; font-weight: bold;">2. Kualitas dan Fungsional Store</h5>
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">a. Ketersedian Daya Listrik Sudah Sesuai PLN (Mandatory 100%)</h6>
                                                    <p style="margin-bottom: 2px;">Running Test Semua Electric Equipment dalam kurun waktu 8 Jam</p>
                                                    <p style="margin-bottom: 2px;">0 : Ada trip issue</p>
                                                    <p style="margin-bottom: 2px;">1 : Tidak Ada Trip</p>
                                                </label>
                                                <input class="form-control" id="daya_listrik" name="daya_listrik" type="text" placeholder="Daya Listrik"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="note_dl">Note</label>
                                                <input class="form-control" id="note_dl" name="note_dl" type="text" placeholder="Note" />
                                            </div>
                                            <div class="form-group">
                                                <label for="supply_air">
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">b. Ketersediaan Suply Air (Mandatory 100%)</h6>
                                                    <p style="margin-bottom: 2px;">Memenuhi Debit Air : Standart 0,7 Liter/Detik Untuk Sumur Bor</p>
                                                    <p style="margin-bottom: 2px;">Kualitas Air : Sesuai Permenkes No.32 Tahun 2017</p>
                                                </label>
                                                <input class="form-control" id="supply_air" name="supply_air" type="text" placeholder="Supply Air" />
                                            </div>
                                            <div class="form-group">
                                                <label for="note_sa">Note</label>
                                                <input class="form-control" id="note_sa" name="note_sa" type="text" placeholder="Note" />
                                            </div>
                                            <div class="form-group">
                                                <label for="aliran_air">
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">c. Elevasi Semua Aliran Air (Paving, Kitchen, IPAL, Fold Drain)</h6>
                                                    <p style="margin-bottom: 2px;">Penilaian defect :</p>
                                                    <p style="margin-bottom: 2px;">0 : Jika Ada 1 Titik Genangan Air</p>
                                                    <p style="margin-bottom: 2px;">1 : Jika Tidak Ada Genangan Sama Sekali</p>
                                                </label>
                                                <input class="form-control" id="aliran_air" name="aliran_air" type="text" placeholder="Aliran Air" />
                                            </div>
                                            <div class="form-group">
                                                <label for="note_aa">Note</label>
                                                <input class="form-control" id="note_aa" name="note_aa" type="text" placeholder="Note" />
                                            </div>
                                            <div class="form-group">
                                                <label for="kualitas_keramik">
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">d. Kualitas Pemasangan Keramik (Kitchen, Indoor, Outdoor,  Penunjang)</h6>
                                                    <p style="margin-bottom: 2px;">Luasan Defect / Total Luas Area (cacat 1 keramik dihitung 1 lembar)</p>
                                                </label>
                                                <input class="form-control" id="kualitas_keramik" name="kualitas_keramik" type="text" placeholder="Kualitas Keramik" />
                                            </div>
                                            <div class="form-group">
                                                <label for="note_kk">Note</label>
                                                <input class="form-control" id="note_kk" name="note_kk" type="text" placeholder="Note" />
                                            </div>
                                            <div class="form-group">
                                                <label for="paving_loading">
                                                    <h6 style="margin-bottom: 2px; font-weight: bold;">e. Kualitas Paving dan Loading Area</h6>
                                                    <p style="margin-bottom: 2px;">Luasan defect / total luas area parkir</p>
                                                </label>
                                                <input class="form-control" id="paving_loading" name="paving_loading" type="text" placeholder="Paving Loading" />
                                            </div>
                                            <div class="form-group">
                                                <label for="note_pl">Note</label>
                                                <input class="form-control" id="note_pl" name="note_pl" type="text" placeholder="Note" />
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">Legal</legend>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="perijinan">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Perijinan Selesai (Tentative Sampai GO Mei 2024 )</h6>
                                                            <p style="margin-bottom: 2px;">NIB, TDUP, SKRK, SPPL/UKL-UPL</p>
                                                        </label>
                                                        <input class="form-control" id="perijinan" name="perijinan" type="text" placeholder="Perijinan"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_p">Note</label>
                                                        <input class="form-control" id="note_p" name="note_p" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="sampah_parkir">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">PIC Sampah & Parkir Selesai**(Mandatory 100%)</h6>
                                                            <p style="margin-bottom: 2px;">MoU Parkir & Sampah</p>
                                                        </label>
                                                        <input class="form-control" id="sampah_parkir" name="sampah_parkir" type="text" placeholder="Sampah & Parkir"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_sp">Note</label>
                                                        <input class="form-control" id="note_sp" name="note_sp" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="akses_jkm">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Akses JKM</h6>
                                                            <p style="margin-bottom: 2px;">Mengacu pada proses Andalalin</p>
                                                        </label>
                                                        <input class="form-control" id="akses_jkm" name="akses_jkm" type="text" placeholder="Akses JKM"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_ajkm">Note</label>
                                                        <input class="form-control" id="note_ajkm" name="note_ajkm" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="pkl">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Entrance Tidak Ada Obstacle PKL (Mandatory 100%)</h6>
                                                            <p style="margin-bottom: 2px;"></p>
                                                        </label>
                                                        <input class="form-control" id="pkl" name="pkl" type="text" placeholder="PKL"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_pkl">Note</label>
                                                        <input class="form-control" id="note_pkl" name="note_pkl" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">IT</legend>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="cctv">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">CCTV</h6>
                                                            <p style="margin-bottom: 2px;">Mengacu pada plotting penempatan IT</p>
                                                        </label>
                                                        <input class="form-control" id="cctv" name="cctv" type="text" placeholder="CCTV"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_cctv">Note</label>
                                                        <input class="form-control" id="note_cctv" name="note_cctv" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="audio_system">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Audio System</h6>
                                                            <p style="margin-bottom: 2px;">Kedatangan H-3 s.d H-5 dari GO</p>
                                                        </label>
                                                        <input class="form-control" id="audio_system" name="audio_system" type="text" placeholder="Audio System"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_as">Note</label>
                                                        <input class="form-control" id="note_as" name="note_as" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="lan_infra">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">LAN Infrastructure (Tanpa Printer Kasir & POS)</h6>
                                                            <p style="margin-bottom: 2px;">Kedatangan H-3 s.d H-5 dari GO</p>
                                                        </label>
                                                        <input class="form-control" id="lan_infra" name="lan_infra" type="text" placeholder="LAN Infrastruktur"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_lan">Note</label>
                                                        <input class="form-control" id="note_lan" name="note_lan" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="internet_km">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Internet Kasir & Manager (Biznet / Indihome dll)</h6>
                                                            <p style="margin-bottom: 2px;">Memastikan bisa connect saat RTO</p>
                                                            <p style="margin-bottom: 2px;"></p>
                                                        </label>
                                                        <input class="form-control" id="internet_km" name="internet_km" type="text" placeholder="Internet Kasir & Manager"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_interkm">Note</label>
                                                        <input class="form-control" id="note_interkm" name="note_interkm" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="internet_cust">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Internet Customer (Telkom WMS)</h6>
                                                            <p style="margin-bottom: 2px;">Memastikan bisa connect saat RTO</p>
                                                            <p style="margin-bottom: 2px;"></p>
                                                        </label>
                                                        <input class="form-control" id="internet_cust" name="internet_cust" type="text" placeholder="Internet Customer"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_intercut">Note</label>
                                                        <input class="form-control" id="note_intercut" name="note_intercut" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">HRGA</legend>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="security">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Security</h6>
                                                            <p style="margin-bottom: 2px;">MoU security</p>
                                                        </label>
                                                        <input class="form-control" id="security" name="security" type="text" placeholder="Security"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_security">Note</label>
                                                        <input class="form-control" id="note_security" name="note_security" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="cs">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Cleaning Service</h6>
                                                            <p style="margin-bottom: 2px;">MoU Cleaning Service</p>
                                                        </label>
                                                        <input class="form-control" id="cs" name="cs" type="text" placeholder="Audio System"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_cs">Note</label>
                                                        <input class="form-control" id="note_cs" name="note_cs" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">Marketing</legend>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="post_content">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Posting Content 100%</h6>
                                                            <p style="margin-bottom: 2px;">Mengacu Status Upload</p>
                                                        </label>
                                                        <input class="form-control" id="post_content" name="post_content" type="text" placeholder="Posting Content"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_pc">Note</label>
                                                        <input class="form-control" id="note_pc" name="note_pc" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="ojol">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Aktif Ojol/Agregator 100%</h6>
                                                            <p style="margin-bottom: 2px;">Daftar, Promo, ID ke Resto dan Tikor</p>
                                                        </label>
                                                        <input class="form-control" id="ojol" name="ojol" type="text" placeholder="Aktif Ojol"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_ojol">Note</label>
                                                        <input class="form-control" id="note_ojol" name="note_ojol" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="tikor_maps">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Pendaftaran Tikor G-Maps</h6>
                                                            <p style="margin-bottom: 2px;">Lokasi di G-Maps</p>
                                                        </label>
                                                        <input class="form-control" id="tikor_maps" name="tikor_maps" type="text" placeholder="LAN Infrastruktur"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_tm">Note</label>
                                                        <input class="form-control" id="note_tm" name="note_tm" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #538392; padding: 5px; border-radius: 10px; font-weight: bold;">FAT</legend>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="qris">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Fulfillment Media Pembayaran (QRIS)</h6>
                                                        </label>
                                                        <input class="form-control" id="qris" name="qris" type="text" placeholder="QRIS"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_qris">Note</label>
                                                        <input class="form-control" id="note_qris" name="note_qris" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group">
                                                        <label for="edc">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Ketersediaan mesin EDC</h6>
                                                        </label>
                                                        <input class="form-control" id="edc" name="edc" type="text" placeholder="Mesin EDC"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_edc">Note</label>
                                                        <input class="form-control" id="note_edc" name="note_edc" type="text" placeholder="Note" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset style="padding: 10px; margin-bottom: 20px;">
                                            <legend style="color: white; background-color: #B7B597; padding: 5px; border-radius: 10px; font-weight: bold;">Checklist Dokumen</legend>
                                            <h5 style="margin-top: 1px; font-weight: bold; color: #4D869C;">BAST Pekerjaan dan Form Defect List ST</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="bast_defectlist" name="bast_defectlist">
                                                        <label class="form-check-label" for="bast_defectlist">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">BAST Pekerjaan dan Form Defect List ST</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_bdl">Note</label>
                                                        <input class="form-control" id="note_bdl" name="note_bdl" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="check_supervisi" name="check_supervisi">
                                                        <label class="form-check-label" for="check_supervisi">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Form Checklist Supervisi</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_checkspv">Note</label>
                                                        <input class="form-control" id="note_checkspv" name="note_checkspv" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-5">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="pengukuran" name="pengukuran">
                                                        <label class="form-check-label" for="pengukuran">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Form Pengukuran Aktual Bangunan (Termasuk Kitchen)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_pengukuran">Note</label>
                                                        <input class="form-control" id="note_pengukuran" name="note_pengukuran" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="test_mep" name="test_mep">
                                                        <label class="form-check-label" for="test_mep">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Form Test MEP : Test Rendam Area Basah, 
                                                            Test Plumbing Air Kotor dan Air Bersih (Test Gelontor dan Pompa); Test Insulasi / Resistansi Isolasi / Merger, 
                                                            Test Grounding, Test Nyala Lampu & Kipas</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_testmep">Note</label>
                                                        <input class="form-control" id="note_testmep" name="note_testmep" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="st_eqp" name="st_eqp">
                                                        <label class="form-check-label" for="st_eqp">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">BAST & Form Checklist (ST-Equipment) : 
                                                            Kelengkapan, Kualitas & Fungsi Kitchen ; Stainless Steel, Restomart, Genset, Gutter, Freezer, 
                                                            Chiller Undercounter, Cold Storage, Meubellair (Meja Kursi)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_steqp">Note</label>
                                                        <input class="form-control" id="note_steqp" name="note_steqp" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="kwh" name="kwh">
                                                        <label class="form-check-label" for="kwh">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">KWH Meter PLN</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="no_kwh">No KWH Meter</label>
                                                        <input class="form-control" id="no_kwh" name="no_kwh" type="text" placeholder="No KWH"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_kwh">Note</label>
                                                        <input class="form-control" id="note_kwh" name="note_kwh" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check"style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="pdam" name="pdam">
                                                        <label class="form-check-label" for="pdam">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">PLN</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="no_pdam">No Meter PDAM</label>
                                                        <input class="form-control" id="no_pdam" name="no_pdam" type="text" placeholder="No PDAM"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_pdam">Note</label>
                                                        <input class="form-control" id="note_pdam" name="note_pdam" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-5" style="margin-top: 1px; font-weight: bold; color: #4D869C;">Dokumen Perijinan Legal</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="draw_teknis" name="draw_teknis">
                                                        <label class="form-check-label" for="draw_teknis">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">As Built Drawing & Spesifikasi Teknis</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_drawteknis">Note</label>
                                                        <input class="form-control" id="note_drawteknis" name="note_drawteknis" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="ba_serahterima" name="ba_serahterima">
                                                        <label class="form-check-label" for="ba_serahterima">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Berita Acara Serah Terima Kunci</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_bast">Note</label>
                                                        <input class="form-control" id="note_bast" name="note_bast" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="mou_parkir" name="mou_parkir">
                                                        <label class="form-check-label" for="mou_parkir">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">MoU Parkir**</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_mouparkir">Note</label>
                                                        <input class="form-control" id="note_mouparkir" name="note_mouparkir" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="info_sampah" name="info_sampah">
                                                        <label class="form-check-label" for="info_sampah">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Informasi pengelolaan sampah**</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_infosampah">Note</label>
                                                        <input class="form-control" id="note_infosampah" name="note_infosampah" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="pkkpr" name="pkkpr">
                                                        <label class="form-check-label" for="pkkpr">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Surat Keteragan Rencana Kota ( SKRK ) / (PKKPR)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_pkkpr">Note</label>
                                                        <input class="form-control" id="note_pkkpr" name="note_pkkpr" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="und_tasyakuran" name="und_tasyakuran">
                                                        <label class="form-check-label" for="und_tasyakuran">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Surat Undangan acara Tasyakuran</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_undt">Note</label>
                                                        <input class="form-control" id="note_undt" name="note_undt" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="nib" name="nib">
                                                        <label class="form-check-label" for="nib">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">No Induk Berusaha ( NIB )</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_nib">Note</label>
                                                        <input class="form-control" id="note_nib" name="note_nib" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="imb" name="imb">
                                                        <label class="form-check-label" for="imb">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Ijin Mendirikan Bangunan (IMB/PBG)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_imb">Note</label>
                                                        <input class="form-control" id="note_imb" name="note_imb" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="tdup" name="tdup">
                                                        <label class="form-check-label" for="tdup">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Tanda Daftar Usaha Pariwisata (TDUP)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_tdup">Note</label>
                                                        <input class="form-control" id="note_tdup" name="note_tdup" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="bpbdpm" name="bpbdpm">
                                                        <label class="form-check-label" for="bpbdpm">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Permohonan Self Assesment ke BPBDPM</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_bpbdpm">Note</label>
                                                        <input class="form-control" id="note_bpbdpm" name="note_bpbdpm" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="reklame" name="reklame">
                                                        <label class="form-check-label" for="reklame">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Ijin Reklame</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_reklame">Note</label>
                                                        <input class="form-control" id="note_reklame" name="note_reklame" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="sppl" name="sppl">
                                                        <label class="form-check-label" for="sppl">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">SPPL/ UKL-UPL</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_sppl">Note</label>
                                                        <input class="form-control" id="note_sppl" name="note_sppl" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="damkar" name="damkar">
                                                        <label class="form-check-label" for="damkar">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Rekom Damkar (***optional berdasarkan lokasi)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_damkar">Note</label>
                                                        <input class="form-control" id="note_damkar" name="note_damkar" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="peil_banjir" name="peil_banjir">
                                                        <label class="form-check-label" for="peil_banjir">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Peil Banjir (***optional berdasarkan lokasi)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_peilbanjir">Note</label>
                                                        <input class="form-control" id="note_peilbanjir" name="note_peilbanjir" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-2">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="andalalin" name="andalalin">
                                                        <label class="form-check-label" for="andalalin">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Analisis Dampak Lalu Lintas (ANDALALIN)-(*optional berdasarkan lokasi)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_andalalin">Note</label>
                                                        <input class="form-control" id="note_andalalin" name="note_andalalin" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-5" style="margin-top: 1px; font-weight: bold; color: #4D869C;">Catatan Khusus</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="iuran_warga" name="iuran_warga">
                                                        <label class="form-check-label" for="iuran_warga">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Informasi iuran warga (*optional berdasarkan lokasi)</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_iuranwarga">Note</label>
                                                        <input class="form-control" id="note_iuranwarga" name="note_iuranwarga" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-5" style="margin-top: 1px; font-weight: bold; color: #EE4E4E;">Diajukan dan Diperiksa Oleh</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="sdg_pm" name="sdg_pm">
                                                        <label class="form-check-label" for="sdg_pm">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign SDG PM</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_sdgpm">SDG PM</label>
                                                        <input class="form-control" id="note_sdgpm" name="note_sdgpm" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="legal_alo" name="legal_alo">
                                                        <label class="form-check-label" for="legal_alo">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign Legal ALO</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_legalalo">Legal ALO</label>
                                                        <input class="form-control" id="note_legalalo" name="note_legalalo" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="scm" name="scm">
                                                        <label class="form-check-label" for="scm">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign SCM</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_scm">SCM</label>
                                                        <input class="form-control" id="note_scm" name="note_scm" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="it_hrga_marketing" name="it_hrga_marketing">
                                                        <label class="form-check-label" for="it_hrga_marketing">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign IT - HRGA - Marketing</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_ihm">IT - HRGA - Marketing</label>
                                                        <input class="form-control" id="note_ihm" name="note_ihm" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-5" style="margin-top: 1px; font-weight: bold; color: #EE4E4E;">Diperiksa dan Disetujui Oleh</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="ops_rm" name="ops_rm">
                                                        <label class="form-check-label" for="ops_rm">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign OPS RM</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_opsrm">OPS RM</label>
                                                        <input class="form-control" id="note_opsrm" name="note_opsrm" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-5" style="margin-top: 1px; font-weight: bold; color: #EE4E4E;">Diperiksa dan Disetujui Oleh</h5>
                                            <div class="row" style="justify-content: center;">
                                                <div class="col-lg-6">
                                                    <div class="form-group form-check" style="margin-bottom: 2px;">
                                                        <input type="checkbox" class="form-check-input" id="sdg_head" name="sdg_head">
                                                        <label class="form-check-label" for="sdg_head">
                                                            <h6 style="margin-bottom: 2px; font-weight: bold;">Assign SDG - Head Of SDG</h6>
                                                        </label>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="note_sdghead">SDG - Head Of SDG</label>
                                                        <input class="form-control" id="note_sdghead" name="note_sdghead" type="text" placeholder="Note"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="file_upload_1">
                                                            <h5 class="font-weight-bold mb-2" style="margin-top: 1px; font-weight: bold; color: #EE4E4E;">Upload File</h5>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="file" class="form-control-file d-none" id="file_upload_1" name="lamp_rto[]" onchange="updateFileName(this)">
                                                            <input type="text" class="form-control" id="file_name_1" placeholder="Choose file..." readonly>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('file_upload_1').click()">Browse</button>
                                                                <button type="button" class="btn btn-primary" onclick="addFileInput()">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="additional_files" class="row"></div>
                                            <div class="uploaded-files">
                                                <h6 class="font-weight-bold mb-2">Files:</h6>
                                                <ul id="file_list"></ul>
                                            </div>
                                        </fieldset>

                                        <!-- Tambahkan kolom lainnya di sini sesuai kebutuhan -->
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

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
    <script src="../../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../dist-assets/js/scripts/script.min.js"></script>
    <script src="../../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../../dist-assets/js/scripts/customizer.script.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#kode_lokasi').change(function() {
                var kode_lahan = $(this).val();
                $.ajax({
                    url: 'soc-get.php', // Gunakan file yang sama sebagai URL
                    type: 'post',
                    data: {kode_lahan: kode_lahan},
                    dataType: 'json',
                    success: function(response) {
                        $('#nama_lahan').val(response.nama_lahan);
                        $('#lokasi').val(response.lokasi);
                        $('#nama_store').val(response.nama_store);
                //         var lampiranLink = 'uploads/' + response.lamp_land;
                // $('#lampiran-link').attr('href', lampiranLink);
                // $('#lampiran-link').text(response.lamp_land);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Terjadi kesalahan saat mengambil data dari server.');
                    }
                });
            });
        });
    </script>

<script>
        let fileIndex = 1;

        function addFileInput() {
            fileIndex++;
            const fileInputDiv = document.createElement('div');
            fileInputDiv.classList.add('col-lg-12', 'mb-2');
            fileInputDiv.innerHTML = `
                <div class="form-group">
                    <div class="input-group">
                        <input type="file" class="form-control-file d-none" id="file_upload_${fileIndex}" name="files[]" onchange="updateFileName(this)">
                        <input type="text" class="form-control" id="file_name_${fileIndex}" placeholder="Choose file..." readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('file_upload_${fileIndex}').click()">Browse</button>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('additional_files').appendChild(fileInputDiv);
        }

        function updateFileName(input) {
            const fileInputId = input.id;
            const fileNameInputId = fileInputId.replace('upload', 'name');
            const fileNameInput = document.getElementById(fileNameInputId);
            fileNameInput.value = input.files.length > 0 ? input.files[0].name : '';
            updateFileList();
        }

        function updateFileList() {
            const fileList = document.getElementById('file_list');
            fileList.innerHTML = '';
            document.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.files.length > 0) {
                    Array.from(input.files).forEach(file => {
                        const li = document.createElement('li');
                        li.textContent = file.name;
                        fileList.appendChild(li);
                    });
                }
            });
        }

        document.querySelector('#file_upload_1').addEventListener('change', updateFileList);
    </script>
</body>

</html>