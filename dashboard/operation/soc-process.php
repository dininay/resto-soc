<?php
// Koneksi ke database tracking_resto
include "../../koneksi.php";

// Ambil data dari form (tabel soc_rto)
$kode_lahan = $_POST["kode_lahan"];
$rto_date = $_POST["rto_date"];
$pengaju_rto = $_POST["pengaju_rto"];
$status_op = $_POST["status_op"];

// Ambil data dari form (tabel soc_sdg)
$bangunan_mural = $_POST["bangunan_mural"];
$note_bm = $_POST["note_bm"];
$daya_listrik = $_POST["daya_listrik"];
$note_dl = $_POST["note_dl"];
$supply_air = $_POST["supply_air"];
$note_sa = $_POST["note_sa"];
$aliran_air = $_POST["aliran_air"];
$note_aa = $_POST["note_aa"];
$kualitas_keramik = $_POST["kualitas_keramik"];
$note_kk = $_POST["note_kk"];
$paving_loading = $_POST["paving_loading"];
$note_pl = $_POST["note_pl"];

// Ambil data dari form (tabel soc_legal)
$perijinan = $_POST["perijinan"];
$note_p = $_POST["note_p"];
$sampah_parkir = $_POST["sampah_parkir"];
$note_sp = $_POST["note_sp"];
$akses_jkm = $_POST["akses_jkm"];
$note_ajkm = $_POST["note_ajkm"];
$pkl = $_POST["pkl"];
$note_pkl = $_POST["note_pkl"];

// Ambil data dari form (tabel soc_it)
$cctv = $_POST["cctv"];
$note_cctv = $_POST["note_cctv"];
$audio_system = $_POST["audio_system"];
$note_as = $_POST["note_as"];
$lan_infra = $_POST["lan_infra"];
$note_lan = $_POST["note_lan"];
$internet_km = $_POST["internet_km"];
$note_interkm = $_POST["note_interkm"];
$internet_cust = $_POST["internet_cust"];
$note_intercut = $_POST["note_intercut"];

// Ambil data dari form (tabel soc_hrga)
$security = $_POST["security"];
$note_security = $_POST["note_security"];
$cs = $_POST["cs"];
$note_cs = $_POST["note_cs"];

// Ambil data dari form (tabel soc_marketing)
$post_content = $_POST["post_content"];
$note_pc = $_POST["note_pc"];
$ojol = $_POST["ojol"];
$note_ojol = $_POST["note_ojol"];
$tikor_maps = $_POST["tikor_maps"];
$note_tm = $_POST["note_tm"];

// Ambil data dari form (tabel soc_fat)
$qris = $_POST["qris"];
$note_qris = $_POST["note_qris"];
$edc = $_POST["edc"];
$note_edc = $_POST["note_edc"];

// Ambil data dari form (tabel note_ba)
$bast_defectlist = $_POST["bast_defectlist"];
$note_bdl = $_POST["note_bdl"];
$check_supervisi = $_POST["check_supervisi"];
$note_checkspv = $_POST["note_checkspv"];
$pengukuran = $_POST["pengukuran"];
$note_pengukuran = $_POST["note_pengukuran"];
$test_mep = $_POST["test_mep"];
$note_testmep = $_POST["note_testmep"];
$st_eqp = $_POST["st_eqp"];
$note_steqp = $_POST["note_steqp"];
$kwh = $_POST["kwh"];
$note_kwh = $_POST["note_kwh"];
$no_kwh = $_POST["no_kwh"];
$pdam = $_POST["pdam"];
$no_pdam = $_POST["no_pdam"];
$note_pdam = $_POST["note_pdam"];

// Ambil data dari form (tabel doc_legal)
$draw_teknis = $_POST["draw_teknis"];
$ba_serahterima = $_POST["ba_serahterima"];
$mou_parkir = $_POST["mou_parkir"];
$info_sampah = $_POST["info_sampah"];
$pkkpr = $_POST["pkkpr"];
$und_tasyakuran = $_POST["und_tasyakuran"];
$nib = $_POST["nib"];
$imb = $_POST["imb"];
$tdup = $_POST["tdup"];
$bpbdpm = $_POST["bpbdpm"];
$reklame = $_POST["reklame"];
$sppl = $_POST["sppl"];
$damkar = $_POST["damkar"];
$peil_banjir = $_POST["peil_banjir"];
$andalalin = $_POST["andalalin"];
$iuran_warga = $_POST["iuran_warga"];

// Ambil data dari form (tabel note_legal)
$note_drawteknis = $_POST["note_drawteknis"];
$note_bast = $_POST["note_bast"];
$note_mouparkir = $_POST["note_mouparkir"];
$note_infosampah = $_POST["note_infosampah"];
$note_pkkpr = $_POST["note_pkkpr"];
$note_undt = $_POST["note_undt"];
$note_nib = $_POST["note_nib"];
$note_imb = $_POST["note_imb"];
$note_tdup = $_POST["note_tdup"];
$note_bpbdpm = $_POST["note_bpbdpm"];
$note_reklame = $_POST["note_reklame"];
$note_sppl = $_POST["note_sppl"];
$note_damkar = $_POST["note_damkar"];
$note_peilbanjir = $_POST["note_peilbanjir"];
$note_andalalin = $_POST["note_andalalin"];
$note_iuranwarga = $_POST["note_iuranwarga"];

// Ambil data dari form (tabel sign)
$sdg_pm = $_POST["sdg_pm"];
$note_sdgpm = $_POST["note_sdgpm"];
$legal_alo = $_POST["legal_alo"];
$note_legalalo = $_POST["note_legalalo"];
$scm = $_POST["scm"];
$note_scm = $_POST["note_scm"];
$it_hrga_marketing = $_POST["it_hrga_marketing"];
$note_ihm = $_POST["note_ihm"];
$ops_rm = $_POST["ops_rm"];
$note_opsrm = $_POST["note_opsrm"];
$sdg_head = $_POST["sdg_head"];
$note_sdghead = $_POST["note_sdghead"];
// Periksa apakah kunci 'lampiran' ada dalam $_FILES
if(isset($_FILES["lamp_rto"])) {
    // Simpan lampiran ke folder tertentu
    $lamp_rto = array();
    $total_files = count($_FILES['lamp_rto']['name']);
    for($i = 0; $i < $total_files; $i++) {
        $file_tmp = $_FILES['lamp_rto']['tmp_name'][$i];
        $file_name = $_FILES['lamp_rto']['name'][$i];
        $file_path = "../uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $lamp_rto[] = $file_path;
    }
    $lamp_rto = implode(",", $lamp_rto);
} else {
    $lamp_rto = "";
}
$status_rto = "In Process";

// Proses penyimpanan data ke dalam tabel
// $stmt = $conn->prepare("INSERT INTO summary_soc (kode_lahan) VALUES (?)");
// $stmt->bind_param("s", $kode_lahan);
// $stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_rto (kode_lahan, rto_date, pengaju_rto, status_op, status_rto) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $kode_lahan, $rto_date, $pengaju_rto, $status_op, $status_rto);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_sdg (kode_lahan, bangunan_mural, note_bm, daya_listrik, note_dl, supply_air, note_sa, aliran_air, note_aa, kualitas_keramik, note_kk, paving_loading, note_pl) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssss", $kode_lahan, $bangunan_mural, $note_bm, $daya_listrik, $note_dl, $supply_air, $note_sa, $aliran_air, $note_aa, $kualitas_keramik, $note_kk, $paving_loading, $note_pl);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_legal (kode_lahan, perijinan, note_p, sampah_parkir, note_sp, akses_jkm, note_ajkm, pkl, note_pkl) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $kode_lahan, $perijinan, $note_p, $sampah_parkir, $note_sp, $akses_jkm, $note_ajkm, $pkl, $note_pkl);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_it (kode_lahan, cctv, note_cctv, audio_system, note_as, lan_infra, note_lan, internet_km, note_interkm, internet_cust, note_intercut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssss", $kode_lahan, $cctv, $note_cctv, $audio_system, $note_as, $lan_infra, $note_lan, $internet_km, $note_interkm, $internet_cust, $note_intercut);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_hrga (kode_lahan, security, note_security, cs, note_cs) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $kode_lahan, $security, $note_security, $cs, $note_cs);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_marketing (kode_lahan, post_content, note_pc, ojol, note_ojol, tikor_maps, note_tm) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $kode_lahan, $post_content, $note_pc, $ojol, $note_ojol, $tikor_maps, $note_tm);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO soc_fat (kode_lahan, qris, note_qris, edc, note_edc) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $kode_lahan, $qris, $note_qris, $edc, $note_edc);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO note_ba (kode_lahan, bast_defectlist, note_bdl, check_supervisi, note_checkspv, pengukuran, note_pengukuran, test_mep, note_testmep, st_eqp, note_steqp, kwh, note_kwh, no_kwh, pdam, no_pdam, note_pdam) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssssssss", $kode_lahan, $bast_defectlist, $note_bdl, $check_supervisi, $note_checkspv, $pengukuran, $note_pengukuran, $test_mep, $note_testmep, $st_eqp, $note_steqp, $kwh, $note_kwh, $no_kwh, $pdam, $no_pdam, $note_pdam);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO doc_legal (kode_lahan, draw_teknis, ba_serahterima, mou_parkir, info_sampah, pkkpr, und_tasyakuran, nib, imb, tdup, bpbdpm, reklame, sppl, damkar, peil_banjir, andalalin, iuran_warga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssssssss", $kode_lahan, $draw_teknis, $ba_serahterima, $mou_parkir, $info_sampah, $pkkpr, $und_tasyakuran, $nib, $imb, $tdup, $bpbdpm, $reklame, $sppl, $damkar, $peil_banjir, $andalalin, $iuran_warga);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO note_legal (kode_lahan, note_drawteknis, note_bast, note_mouparkir, note_infosampah, note_pkkpr, note_undt, note_nib, note_imb, note_tdup, note_bpbdpm, note_reklame, note_sppl, note_damkar, note_peilbanjir, note_andalalin, note_iuranwarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssssssss", $kode_lahan, $note_drawteknis, $note_bast, $note_mouparkir, $note_infosampah, $note_pkkpr, $note_undt, $note_nib, $note_imb, $note_tdup, $note_bpbdpm, $note_reklame, $note_sppl, $note_damkar, $note_peilbanjir, $note_andalalin, $note_iuranwarga);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO sign (kode_lahan, sdg_pm, note_sdgpm, legal_alo, note_legalalo, scm, note_scm, it_hrga_marketing, note_ihm, ops_rm, note_opsrm, sdg_head, note_sdghead, lamp_rto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssssss", $kode_lahan, $sdg_pm, $note_sdgpm, $legal_alo, $note_legalalo, $scm, $note_scm, $it_hrga_marketing, $note_ihm, $ops_rm, $note_opsrm, $sdg_head, $note_sdghead, $lamp_rto);
$stmt->execute();

if ($stmt->execute()) {
    // Redirect ke halaman datatable-land-sourcing
    header("Location: " . $base_url . "/datatables-soc.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
