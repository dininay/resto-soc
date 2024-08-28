-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2024 at 07:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tracking_resto`
--

-- --------------------------------------------------------

--
-- Table structure for table `doc_legal`
--

CREATE TABLE `doc_legal` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `draw_teknis` varchar(255) DEFAULT NULL,
  `ba_serahterima` varchar(255) DEFAULT NULL,
  `mou_parkir` varchar(255) DEFAULT NULL,
  `info_sampah` varchar(255) DEFAULT NULL,
  `pkkpr` varchar(255) DEFAULT NULL,
  `und_tasyakuran` varchar(255) DEFAULT NULL,
  `nib` varchar(255) DEFAULT NULL,
  `imb` varchar(255) DEFAULT NULL,
  `tdup` varchar(255) DEFAULT NULL,
  `bpbdpm` varchar(255) DEFAULT NULL,
  `reklame` varchar(255) DEFAULT NULL,
  `sppl` varchar(255) DEFAULT NULL,
  `damkar` varchar(255) DEFAULT NULL,
  `peil_banjir` varchar(255) DEFAULT NULL,
  `andalalin` varchar(255) DEFAULT NULL,
  `iuran_warga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_loacd`
--

CREATE TABLE `dokumen_loacd` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `tgl_berlaku` date DEFAULT NULL,
  `masa_berlaku` varchar(255) DEFAULT NULL,
  `deal_sewa` varchar(100) DEFAULT NULL,
  `status_approvloacd` varchar(255) DEFAULT NULL,
  `status_approvlegalvd` varchar(255) DEFAULT NULL,
  `lamp_loacd` varchar(1000) DEFAULT NULL,
  `lamp_vd` varchar(1000) DEFAULT NULL,
  `catatan_vd` varchar(1000) DEFAULT NULL,
  `kode_store` varchar(255) DEFAULT NULL,
  `catatan` varchar(1000) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `slaloa_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slavd_date` date DEFAULT NULL,
  `slavdlegal_date` date DEFAULT NULL,
  `vdlegal_date` date DEFAULT NULL,
  `lamp_vdsign` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_loacd`
--

INSERT INTO `dokumen_loacd` (`id`, `kode_lahan`, `tgl_berlaku`, `masa_berlaku`, `deal_sewa`, `status_approvloacd`, `status_approvlegalvd`, `lamp_loacd`, `lamp_vd`, `catatan_vd`, `kode_store`, `catatan`, `start_date`, `slaloa_date`, `end_date`, `slavd_date`, `slavdlegal_date`, `vdlegal_date`, `lamp_vdsign`) VALUES
(37, 'MLG-001', NULL, '10', 'Rp. 8.000.000', 'Approve', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', '', 'MALJAK', '', '2024-08-11', '2024-09-01', '2024-08-11', '2024-08-15', NULL, NULL, NULL),
(38, 'CIMH-001', NULL, '4', 'Rp. 15.000.000', 'Approve', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', 'sip', 'CIMRAYA', 'catatan loa cd', '2024-08-12', '2024-09-02', '2024-08-12', '2024-08-16', '2024-08-21', '2024-08-12', 'Template Laporan Harian Kerja Ibu.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `draft`
--

CREATE TABLE `draft` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_draf` varchar(255) DEFAULT NULL,
  `jadwal_psm` date DEFAULT NULL,
  `catatan_legal` varchar(1000) DEFAULT NULL,
  `catatan_draft` varchar(1000) DEFAULT NULL,
  `lamp_signpsm` varchar(1000) DEFAULT NULL,
  `draft_legal` varchar(255) DEFAULT NULL,
  `confirm_nego` varchar(255) DEFAULT NULL,
  `catatan_psm` varchar(1000) DEFAULT NULL,
  `confirm_fatpsm` varchar(255) DEFAULT NULL,
  `catatan_psmfat` varchar(1000) DEFAULT NULL,
  `psmfat_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slapsm_date` date DEFAULT NULL,
  `slafatpsm_date` date DEFAULT NULL,
  `confirm_bod` varchar(255) DEFAULT NULL,
  `lamp_bod` varchar(1000) DEFAULT NULL,
  `bod_date` date DEFAULT NULL,
  `slabod_date` date DEFAULT NULL,
  `lamp_nego` varchar(1000) DEFAULT NULL,
  `catatan_bod` varchar(1000) DEFAULT NULL,
  `confirm_re` varchar(255) DEFAULT NULL,
  `catatan_re` varchar(1000) DEFAULT NULL,
  `draftre_date` date DEFAULT NULL,
  `sladraftre_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `draft`
--

INSERT INTO `draft` (`id`, `kode_lahan`, `lamp_draf`, `jadwal_psm`, `catatan_legal`, `catatan_draft`, `lamp_signpsm`, `draft_legal`, `confirm_nego`, `catatan_psm`, `confirm_fatpsm`, `catatan_psmfat`, `psmfat_date`, `start_date`, `sla_date`, `slalegal_date`, `end_date`, `slapsm_date`, `slafatpsm_date`, `confirm_bod`, `lamp_bod`, `bod_date`, `slabod_date`, `lamp_nego`, `catatan_bod`, `confirm_re`, `catatan_re`, `draftre_date`, `sladraftre_date`) VALUES
(36, 'MLG-001', 'Template Laporan Harian Kerja Ibu.pdf', NULL, NULL, NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'In Process', 'Approve', '', 'Approve', '', '2024-08-11', NULL, NULL, '2024-08-19', '2024-08-11', '2024-08-19', '2024-08-15', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-11', '2024-08-13', NULL, '', 'Done', '', '2024-08-11', '2024-08-19'),
(37, 'CIMH-001', NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-20', NULL, '2024-08-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) DEFAULT NULL,
  `lamp_steqp` varchar(1000) DEFAULT NULL,
  `lamp_basteqp` varchar(1000) DEFAULT NULL,
  `status_steqp` varchar(1000) DEFAULT NULL,
  `steqp_date` date DEFAULT NULL,
  `sla_steqp` date DEFAULT NULL,
  `status_eqpdev` varchar(1000) DEFAULT NULL,
  `lamp_eqpdev` varchar(1000) DEFAULT NULL,
  `eqpdev_date` date DEFAULT NULL,
  `sla_eqpdev` date DEFAULT NULL,
  `status_eqpdevprocur` varchar(1000) DEFAULT NULL,
  `lamp_spkeqpdev` varchar(1000) DEFAULT NULL,
  `eqpdevprocur_date` date DEFAULT NULL,
  `sla_eqpdevprocur` date DEFAULT NULL,
  `progress_eqpsite` varchar(1000) DEFAULT NULL,
  `lamp_eqpsite` varchar(1000) DEFAULT NULL,
  `eqpsite_date` date DEFAULT NULL,
  `sla_eqpsite` date DEFAULT NULL,
  `defect_eqpsite` varchar(1000) DEFAULT NULL,
  `notedefect_eqpsite` varchar(1000) DEFAULT NULL,
  `status_eqpsite` varchar(1000) DEFAULT NULL,
  `status_woeqp` varchar(100) DEFAULT NULL,
  `lamp_woeqp` varchar(1000) DEFAULT NULL,
  `woeqp_date` date DEFAULT NULL,
  `status_eqptaf` varchar(100) DEFAULT NULL,
  `sla_eqptaf` date DEFAULT NULL,
  `eqptaf_date` date DEFAULT NULL,
  `catatan_eqptaf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `kode_lahan`, `lamp_steqp`, `lamp_basteqp`, `status_steqp`, `steqp_date`, `sla_steqp`, `status_eqpdev`, `lamp_eqpdev`, `eqpdev_date`, `sla_eqpdev`, `status_eqpdevprocur`, `lamp_spkeqpdev`, `eqpdevprocur_date`, `sla_eqpdevprocur`, `progress_eqpsite`, `lamp_eqpsite`, `eqpsite_date`, `sla_eqpsite`, `defect_eqpsite`, `notedefect_eqpsite`, `status_eqpsite`, `status_woeqp`, `lamp_woeqp`, `woeqp_date`, `status_eqptaf`, `sla_eqptaf`, `eqptaf_date`, `catatan_eqptaf`) VALUES
(14, 'MLG-001', NULL, NULL, 'In Process', NULL, '2024-11-14', 'In Process', NULL, '2024-08-11', '2024-11-14', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-11', '2024-11-14', NULL, NULL, NULL, '2024-11-14', NULL, NULL, 'In Process', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', NULL, 'Approve', '2024-08-17', '2024-08-11', '');

-- --------------------------------------------------------

--
-- Table structure for table `hold_project`
--

CREATE TABLE `hold_project` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `issue_detail` varchar(1000) DEFAULT NULL,
  `kronologi` varchar(1000) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `action_plan` varchar(1000) DEFAULT NULL,
  `update` varchar(1000) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status_hold` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE `issue` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) DEFAULT NULL,
  `tanggal_retensi` date DEFAULT NULL,
  `lamp_badefect` varchar(1000) DEFAULT NULL,
  `status_defect` varchar(255) DEFAULT NULL,
  `defect_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issue`
--

INSERT INTO `issue` (`id`, `kode_lahan`, `tanggal_retensi`, `lamp_badefect`, `status_defect`, `defect_date`) VALUES
(10, 'MLG-001', NULL, NULL, 'Not Yet', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `konstruksi`
--

CREATE TABLE `konstruksi` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `week_1` varchar(255) NOT NULL,
  `week_2` varchar(255) NOT NULL,
  `week_3` varchar(255) NOT NULL,
  `week_4` varchar(255) NOT NULL,
  `week_5` varchar(255) NOT NULL,
  `week_6` varchar(255) NOT NULL,
  `week_7` varchar(255) NOT NULL,
  `week_8` varchar(255) NOT NULL,
  `week_9` varchar(255) NOT NULL,
  `week_10` varchar(255) NOT NULL,
  `week_11` varchar(255) NOT NULL,
  `week_12` varchar(255) NOT NULL,
  `week_13` varchar(100) DEFAULT NULL,
  `week_14` varchar(100) DEFAULT NULL,
  `week_15` varchar(100) DEFAULT NULL,
  `lamp_monitoring` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konstruksi`
--

INSERT INTO `konstruksi` (`id`, `kode_lahan`, `week_1`, `week_2`, `week_3`, `week_4`, `week_5`, `week_6`, `week_7`, `week_8`, `week_9`, `week_10`, `week_11`, `week_12`, `week_13`, `week_14`, `week_15`, `lamp_monitoring`) VALUES
(12, 'MLG-001', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `land`
--

CREATE TABLE `land` (
  `id` int(10) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `nama_lahan` varchar(255) NOT NULL,
  `status_land` varchar(255) NOT NULL,
  `bp_date` date DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `alamat_pemilik` varchar(255) DEFAULT NULL,
  `no_tlp` varchar(50) DEFAULT NULL,
  `luas_area` varchar(255) DEFAULT NULL,
  `lamp_land` varchar(1000) DEFAULT NULL,
  `status_approvre` varchar(255) DEFAULT NULL,
  `status_date` date DEFAULT NULL,
  `sla` date DEFAULT NULL,
  `re_date` date DEFAULT NULL,
  `maps` varchar(1000) DEFAULT NULL,
  `latitude` varchar(1000) DEFAULT NULL,
  `longitude` varchar(1000) DEFAULT NULL,
  `harga_sewa` varchar(100) DEFAULT NULL,
  `mintahun_sewa` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land`
--

INSERT INTO `land` (`id`, `city`, `kode_lahan`, `nama_lahan`, `status_land`, `bp_date`, `lokasi`, `nama_pemilik`, `alamat_pemilik`, `no_tlp`, `luas_area`, `lamp_land`, `status_approvre`, `status_date`, `sla`, `re_date`, `maps`, `latitude`, `longitude`, `harga_sewa`, `mintahun_sewa`) VALUES
(42, 'MALANG', 'MLG-001', 'Malang Jakarta', 'Aktif', '2024-08-11', 'Jl. Jakarta Malang', 'Pak Awaludin', 'Jl. Jombang', '082265293431', '1000', 'Template Laporan Harian Kerja Ibu.pdf', 'Approve', '2024-08-11', '2024-08-18', NULL, 'https://www.google.com/maps/place/Mie+Gacoan+Jl.+Jakarta/@-7.9629063,112.621132,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd6296dc886eb2b:0x9155f320e6f9cc0c!8m2!3d-7.9629116!4d112.6237069!16s%2Fg%2F11v02w5rcb?entry=ttu', '-7.9629063', '112.621132', 'Rp. 10.000.000', '5'),
(43, 'CIMAHI', 'CIMH-001', 'Cimahi Kebun Raya', 'Aktif', '2024-08-12', 'Jl. Raya Kebun Cibodas', 'Pak Kurnia', 'Jl. Bunga Coklat', '089765435675', '2000', 'Template Laporan Harian Kerja Ibu.pdf', 'Approve', '2024-08-12', '2024-08-19', NULL, 'https://www.google.com/maps/place/Badan+Koordinasi+Wilayah+Pemerintahan+dan+Pembangunan+Jawa+Timur+III+(+BAKORWIL+III+)+Malang/@-7.9633877,112.6215021,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd6282b6f0d0a61:0x474046e3ce87264a!8m2!3d-7.963393!4d112.624077!16s%2Fg%2F1hm1v4b5x?entry=ttu', '-7.9776716', '112.621132', 'Rp. 20.000.000', '7');

-- --------------------------------------------------------

--
-- Table structure for table `master_city`
--

CREATE TABLE `master_city` (
  `IDCity` int(11) NOT NULL,
  `City` varchar(512) DEFAULT NULL,
  `Ct` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_city`
--

INSERT INTO `master_city` (`IDCity`, `City`, `Ct`) VALUES
(1, 'BANDUNG', 'BDG'),
(2, 'BATU', 'BT'),
(3, 'BEKASI', 'BKS'),
(4, 'BLITAR', 'BLTR'),
(5, 'BOGOR', 'BGR'),
(6, 'BOJONEGORO', 'BJNGR'),
(7, 'CIAMIS', 'CIMS'),
(8, 'CIANJUR', 'CINJR'),
(9, 'CIKARANG', 'CIKRG'),
(10, 'CILACAP', 'CILCP'),
(11, 'CIMAHI', 'CIMH'),
(12, 'CIREBON', 'CIRBN'),
(13, 'DEPOK', 'DPK'),
(14, 'GRESIK', 'GRSK'),
(15, 'JAKARTA', 'JKT'),
(16, 'JEMBER', 'JMBR'),
(17, 'JOMBANG', 'JMBG'),
(18, 'KARAWANG', 'KRWG'),
(19, 'KEDIRI', 'KDR'),
(20, 'KLATEN', 'KLTN'),
(21, 'KUDUS', 'KDS'),
(22, 'LAMONGAN', 'LMGN'),
(23, 'MADIUN', 'MDN'),
(24, 'MAGELANG', 'MGLG'),
(25, 'MAKASSAR', 'MKSR'),
(26, 'MALANG', 'MLG'),
(27, 'MOJOKERTO', 'MJKRT'),
(28, 'NGAWI', 'NGWI'),
(29, 'PAMEKASAN', 'PMKSN'),
(30, 'PASURUAN', 'PSRN'),
(31, 'PEKALONGAN', 'PKLGN'),
(32, 'PONOROGO', 'PNRGO'),
(33, 'PROBOLINGGO', 'PROBLGO'),
(34, 'PURBALINGGA', 'PURBLGA'),
(35, 'PURWAKARTA', 'PRWKRTA'),
(36, 'PURWOKERTO', 'PRWKRTO'),
(37, 'SALATIGA', 'SLTG'),
(38, 'SEMARANG', 'SMRG'),
(39, 'SIDOARJO', 'SDRJ'),
(40, 'SLEMAN', 'SLMAN'),
(41, 'SUKOHARJO', 'SKHRJO'),
(42, 'SUMEDANG', 'SMDG'),
(43, 'SURABAYA', 'SBY'),
(44, 'SURAKARTA', 'SRKRTA'),
(45, 'TANGERANG', 'TGRG'),
(46, 'TASIKMALAYA', 'TSMLYA'),
(47, 'TEGAL', 'TGAL'),
(48, 'TUBAN', 'TBN'),
(49, 'TULUNGAGUNG', 'TLG'),
(50, 'YOGYAKARTA', 'YGYKRTA');

-- --------------------------------------------------------

--
-- Table structure for table `master_sla`
--

CREATE TABLE `master_sla` (
  `id` int(11) NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `sla` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_sla`
--

INSERT INTO `master_sla` (`id`, `divisi`, `sla`) VALUES
(1, 'RE', '7'),
(2, 'Legal', '7'),
(3, 'Owner Surveyor', '7'),
(4, 'Negosiator', '7'),
(5, 'SDG-Design', '7'),
(6, 'SDG-QS', '7'),
(7, 'Procurement', '7'),
(8, 'SPK', '7'),
(9, 'KOM', '2'),
(10, 'Konstruksi', '90'),
(11, 'ST-EQP', '88'),
(12, 'ST-Konstruksi', '95'),
(13, 'LOA-CD', '21'),
(14, 'VD', '4'),
(15, 'Draft-Sewa', '9'),
(16, 'TTD-Sewa', '14'),
(17, 'Design', '20'),
(18, 'Permit', '34'),
(19, 'QS', '10'),
(20, 'Tender', '14'),
(22, 'RTO', '4'),
(23, 'VL', '9'),
(24, 'FAT-Sewa', '4'),
(25, 'SPK-FAT', '6'),
(26, 'Land Survey', '5'),
(27, 'Layouting', '10'),
(29, 'Table Sewa', '8'),
(30, 'Sign', '2'),
(31, 'Final PSM', '8'),
(32, 'Review PSM TAF', '6');

-- --------------------------------------------------------

--
-- Table structure for table `master_slacons`
--

CREATE TABLE `master_slacons` (
  `id` int(11) NOT NULL,
  `divisi` varchar(255) DEFAULT NULL,
  `sla` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_slacons`
--

INSERT INTO `master_slacons` (`id`, `divisi`, `sla`) VALUES
(1, 'rto', '4'),
(2, 'sdg', '14'),
(3, 'legal', '7'),
(4, 'legal_permit', '17'),
(5, 'ir', '7'),
(6, 'hrga_tm', '100'),
(7, 'hrga_ff1', '75'),
(8, 'hrga_ff2', '50'),
(9, 'hrga_ff3', '25'),
(10, 'hot', '30'),
(11, 'scm', '5'),
(12, 'it', '3'),
(13, 'it_config', '14'),
(14, 'marketing', '7'),
(15, 'marketing_rm', '30'),
(16, 'fat', '7'),
(17, 'kpt1', '75'),
(18, 'kpt2', '50'),
(19, 'kpt3', '25'),
(20, 'hrga_fl', '100'),
(21, 'mep', '21'),
(22, 'spk-procur', '7'),
(23, 'payment-taf', '9'),
(24, 'wo-scm', '7');

-- --------------------------------------------------------

--
-- Table structure for table `mom`
--

CREATE TABLE `mom` (
  `id` int(11) NOT NULL,
  `notes` varchar(1000) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `file` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_ba`
--

CREATE TABLE `note_ba` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `bast_defectlist` varchar(255) NOT NULL,
  `note_bdl` varchar(255) DEFAULT NULL,
  `check_supervisi` varchar(255) NOT NULL,
  `note_checkspv` varchar(255) DEFAULT NULL,
  `pengukuran` varchar(255) NOT NULL,
  `note_pengukuran` varchar(255) DEFAULT NULL,
  `test_mep` varchar(255) NOT NULL,
  `note_testmep` varchar(255) DEFAULT NULL,
  `st_eqp` varchar(255) NOT NULL,
  `note_steqp` varchar(255) DEFAULT NULL,
  `kwh` varchar(255) NOT NULL,
  `no_kwh` varchar(255) DEFAULT NULL,
  `note_kwh` varchar(255) DEFAULT NULL,
  `pdam` varchar(255) NOT NULL,
  `no_pdam` varchar(255) DEFAULT NULL,
  `note_pdam` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_legal`
--

CREATE TABLE `note_legal` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `note_drawteknis` varchar(1000) DEFAULT NULL,
  `note_bast` varchar(1000) DEFAULT NULL,
  `note_mouparkir` varchar(1000) DEFAULT NULL,
  `note_infosampah` varchar(1000) DEFAULT NULL,
  `note_pkkpr` varchar(1000) DEFAULT NULL,
  `note_undt` varchar(1000) DEFAULT NULL,
  `note_nib` varchar(1000) DEFAULT NULL,
  `note_imb` varchar(1000) DEFAULT NULL,
  `note_tdup` varchar(1000) DEFAULT NULL,
  `note_bpbdpm` varchar(1000) DEFAULT NULL,
  `note_reklame` varchar(1000) DEFAULT NULL,
  `note_sppl` varchar(1000) DEFAULT NULL,
  `note_damkar` varchar(1000) DEFAULT NULL,
  `note_peilbanjir` varchar(1000) DEFAULT NULL,
  `note_andalalin` varchar(1000) DEFAULT NULL,
  `note_iuranwarga` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obs_sdg`
--

CREATE TABLE `obs_sdg` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `obstacle` varchar(1000) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `obs_date` date NOT NULL,
  `status_obssdg` varchar(255) NOT NULL,
  `lamp_legal` varchar(1000) NOT NULL,
  `obslegal_date` date NOT NULL,
  `status_obslegal` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procurement`
--

CREATE TABLE `procurement` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(256) NOT NULL,
  `status_approvsdg` varchar(255) NOT NULL,
  `status_approvprocurement` varchar(255) NOT NULL,
  `lamp_spkrabcons` varchar(1000) DEFAULT NULL,
  `status_tender` varchar(100) DEFAULT NULL,
  `catatan_tender` varchar(1000) DEFAULT NULL,
  `catatan_proc` varchar(255) DEFAULT NULL,
  `nama_vendor` varchar(255) DEFAULT NULL,
  `alamat` varchar(1000) DEFAULT NULL,
  `nohp` varchar(255) DEFAULT NULL,
  `detail` varchar(1000) DEFAULT NULL,
  `lamp_profil` varchar(1000) DEFAULT NULL,
  `lamp_vendor` varchar(1000) DEFAULT NULL,
  `start_date` date NOT NULL,
  `sla_spkrab` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `sla_date` date DEFAULT NULL,
  `status_fattender` varchar(255) DEFAULT NULL,
  `sla_fattender` date DEFAULT NULL,
  `catatan_fattender` varchar(1000) DEFAULT NULL,
  `fattender_date` date DEFAULT NULL,
  `status_spkfat` varchar(100) DEFAULT NULL,
  `status_spkfaturugan` varchar(100) DEFAULT NULL,
  `spkfat_date` date DEFAULT NULL,
  `spkfaturugan_date` date DEFAULT NULL,
  `sla_spkfat` date DEFAULT NULL,
  `sla_spkfaturugan` date DEFAULT NULL,
  `catatan_spkfat` varchar(255) DEFAULT NULL,
  `catatan_spkfaturugan` varchar(255) DEFAULT NULL,
  `status_procururugan` varchar(100) DEFAULT NULL,
  `catatan_procururugan` varchar(255) DEFAULT NULL,
  `lamp_spkurugan` varchar(1000) DEFAULT NULL,
  `spkurugan_date` date DEFAULT NULL,
  `sla_spkurugan` date DEFAULT NULL,
  `status_tenderurugan` varchar(100) DEFAULT NULL,
  `catatan_tenderurugan` varchar(255) DEFAULT NULL,
  `nama_vendorurugan` varchar(100) DEFAULT NULL,
  `alamat_vendorurugan` varchar(255) DEFAULT NULL,
  `detail_vendorurugan` varchar(255) DEFAULT NULL,
  `lamp_profilurugan` varchar(1000) DEFAULT NULL,
  `lamp_vendorurugan` varchar(1000) DEFAULT NULL,
  `slatenderurugan_date` date DEFAULT NULL,
  `tenderurugan_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `procurement`
--

INSERT INTO `procurement` (`id`, `kode_lahan`, `status_approvsdg`, `status_approvprocurement`, `lamp_spkrabcons`, `status_tender`, `catatan_tender`, `catatan_proc`, `nama_vendor`, `alamat`, `nohp`, `detail`, `lamp_profil`, `lamp_vendor`, `start_date`, `sla_spkrab`, `end_date`, `sla_date`, `status_fattender`, `sla_fattender`, `catatan_fattender`, `fattender_date`, `status_spkfat`, `status_spkfaturugan`, `spkfat_date`, `spkfaturugan_date`, `sla_spkfat`, `sla_spkfaturugan`, `catatan_spkfat`, `catatan_spkfaturugan`, `status_procururugan`, `catatan_procururugan`, `lamp_spkurugan`, `spkurugan_date`, `sla_spkurugan`, `status_tenderurugan`, `catatan_tenderurugan`, `nama_vendorurugan`, `alamat_vendorurugan`, `detail_vendorurugan`, `lamp_profilurugan`, `lamp_vendorurugan`, `slatenderurugan_date`, `tenderurugan_date`) VALUES
(15, 'MLG-001', 'Approve', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', 'Done', '', '', 'PT. ABC', 'Jl. Jakarta', '08652438787', 'tes', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-11', '2024-08-25', '2024-08-11', '2024-08-25', NULL, NULL, NULL, NULL, 'Approve', NULL, '2024-08-11', NULL, '2024-08-17', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'CIMH-001', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', NULL, '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, 'Approve', NULL, '2024-08-13', NULL, '2024-08-19', NULL, '', 'Approve', '', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-13', '2024-08-27', 'Done', '', 'PT.', '', '', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-27', '2024-08-13');

-- --------------------------------------------------------

--
-- Table structure for table `re`
--

CREATE TABLE `re` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(50) NOT NULL,
  `catatan_owner` varchar(1000) NOT NULL,
  `status_approvowner` varchar(255) NOT NULL,
  `lamp_vl` varchar(1000) DEFAULT NULL,
  `lamp_vlsign` varchar(255) DEFAULT NULL,
  `catatan_legal` varchar(1000) NOT NULL,
  `status_approvlegal` varchar(255) NOT NULL,
  `catatan_nego` varchar(1000) DEFAULT NULL,
  `status_approvnego` varchar(255) DEFAULT NULL,
  `status_vl` varchar(255) DEFAULT NULL,
  `catatan_vl` varchar(1000) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `nego_date` date DEFAULT NULL,
  `vl_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `slanego_date` date DEFAULT NULL,
  `slavl_date` date DEFAULT NULL,
  `slavllegal_date` date DEFAULT NULL,
  `vllegal_date` date DEFAULT NULL,
  `deal_sewa` varchar(100) DEFAULT NULL,
  `masa_berlaku` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `re`
--

INSERT INTO `re` (`id`, `kode_lahan`, `catatan_owner`, `status_approvowner`, `lamp_vl`, `lamp_vlsign`, `catatan_legal`, `status_approvlegal`, `catatan_nego`, `status_approvnego`, `status_vl`, `catatan_vl`, `start_date`, `end_date`, `nego_date`, `vl_date`, `sla_date`, `slalegal_date`, `slanego_date`, `slavl_date`, `slavllegal_date`, `vllegal_date`, `deal_sewa`, `masa_berlaku`) VALUES
(76, 'MLG-001', '', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', NULL, '', '', '', 'Approve', 'Approve', '', '2024-08-11', '0000-00-00', '2024-08-11', '2024-08-11', '2024-08-18', NULL, '2024-08-18', '2024-08-18', NULL, NULL, NULL, NULL),
(77, 'CIMH-001', '', 'Approve', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', '', '', 'catatan negosiator', 'Approve', 'Approve', 'sudah oke', '2024-08-12', '0000-00-00', '2024-08-12', '2024-08-12', '2024-08-19', NULL, '2024-08-19', '2024-08-19', '2024-08-21', '2024-08-12', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resto`
--

CREATE TABLE `resto` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(11) NOT NULL,
  `lamp_splegal` varchar(1000) DEFAULT NULL,
  `catatan_legal` varchar(1000) DEFAULT NULL,
  `nama_store` varchar(255) DEFAULT NULL,
  `status_land` varchar(255) DEFAULT NULL,
  `gostore_date` date DEFAULT NULL,
  `rto_date` date DEFAULT NULL,
  `status_gostore` varchar(255) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `lamp_spk` varchar(1000) DEFAULT NULL,
  `sla_spk` date DEFAULT NULL,
  `spk_date` date DEFAULT NULL,
  `status_spk` varchar(255) DEFAULT NULL,
  `status_fat` varchar(1000) DEFAULT NULL,
  `lamp_signedtaf` varchar(1000) DEFAULT NULL,
  `sla_fat` date DEFAULT NULL,
  `fat_date` date DEFAULT NULL,
  `status_schedule` varchar(255) DEFAULT NULL,
  `status_scheduleurugan` varchar(10) DEFAULT NULL,
  `catatan_schedule` varchar(1000) DEFAULT NULL,
  `catatan_scheduleurugan` varchar(100) DEFAULT NULL,
  `schedule_date` date DEFAULT NULL,
  `scheduleurugan_date` date DEFAULT NULL,
  `status_legalizin` varchar(100) DEFAULT NULL,
  `legalizin_date` date DEFAULT NULL,
  `lamp_legalizin` varchar(1000) DEFAULT NULL,
  `lamp_kom` varchar(1000) DEFAULT NULL,
  `sla_kom` date DEFAULT NULL,
  `start_slakom` date DEFAULT NULL,
  `kom_date` date DEFAULT NULL,
  `status_kom` varchar(255) DEFAULT NULL,
  `obstacle_kom` varchar(255) DEFAULT NULL,
  `note_kom` varchar(100) DEFAULT NULL,
  `lamp_obskom` varchar(500) DEFAULT NULL,
  `start_konstruksi` date DEFAULT NULL,
  `end_konstruksi` date DEFAULT NULL,
  `submit_gis` varchar(255) DEFAULT NULL,
  `lamp_stkonstruksi` varchar(1000) DEFAULT NULL,
  `status_stkonstruksi` varchar(255) DEFAULT NULL,
  `stkonstruksi_date` date DEFAULT NULL,
  `sla_stkonstruksi` date DEFAULT NULL,
  `obstacle_stkons` varchar(255) DEFAULT NULL,
  `note_stkons` varchar(1000) DEFAULT NULL,
  `lamp_obsstkons` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resto`
--

INSERT INTO `resto` (`id`, `kode_lahan`, `lamp_splegal`, `catatan_legal`, `nama_store`, `status_land`, `gostore_date`, `rto_date`, `status_gostore`, `approved_by`, `submit_date`, `start_date`, `lamp_spk`, `sla_spk`, `spk_date`, `status_spk`, `status_fat`, `lamp_signedtaf`, `sla_fat`, `fat_date`, `status_schedule`, `status_scheduleurugan`, `catatan_schedule`, `catatan_scheduleurugan`, `schedule_date`, `scheduleurugan_date`, `status_legalizin`, `legalizin_date`, `lamp_legalizin`, `lamp_kom`, `sla_kom`, `start_slakom`, `kom_date`, `status_kom`, `obstacle_kom`, `note_kom`, `lamp_obskom`, `start_konstruksi`, `end_konstruksi`, `submit_gis`, `lamp_stkonstruksi`, `status_stkonstruksi`, `stkonstruksi_date`, `sla_stkonstruksi`, `obstacle_stkons`, `note_stkons`, `lamp_obsstkons`) VALUES
(25, 'MLG-001', NULL, NULL, NULL, 'In Process', '2024-10-27', '2024-10-23', 'In Process', 'Last Updated by BoD', NULL, NULL, NULL, '2024-08-18', NULL, 'In Process', NULL, NULL, NULL, NULL, 'Approve', NULL, '', NULL, '2024-08-11', NULL, NULL, NULL, NULL, 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-17', '2024-08-11', '2024-08-11', 'Done', 'Yes', 'tes', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-18', NULL, NULL, NULL, 'In Process', NULL, '2024-11-21', NULL, NULL, NULL),
(26, 'CIMH-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sdg_desain`
--

CREATE TABLE `sdg_desain` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_desainplan` varchar(1000) DEFAULT NULL,
  `catatan_sdgdesain` varchar(1000) DEFAULT NULL,
  `tc` varchar(255) DEFAULT NULL,
  `confirm_sdgdesain` varchar(255) NOT NULL,
  `confirm_sdgurugan` varchar(255) DEFAULT NULL,
  `catatan_sdgurugan` varchar(255) DEFAULT NULL,
  `urugan_date` date DEFAULT NULL,
  `obstacle` varchar(255) NOT NULL,
  `submit_legal` varchar(1000) NOT NULL,
  `catatan_submit` varchar(1000) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `lamp_pbg` varchar(1000) DEFAULT NULL,
  `lamp_permit` varchar(1000) DEFAULT NULL,
  `lamp_urugan` varchar(500) DEFAULT NULL,
  `urugan` varchar(100) DEFAULT NULL,
  `catatan_obslegal` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `survey_date` date DEFAULT NULL,
  `sla_survey` date DEFAULT NULL,
  `layout_date` date DEFAULT NULL,
  `sla_layout` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `lamp_survey` varchar(1000) DEFAULT NULL,
  `note_survey` varchar(1000) DEFAULT NULL,
  `status_survey` varchar(255) DEFAULT NULL,
  `lamp_layouting` varchar(255) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `obs_detail` varchar(1000) DEFAULT NULL,
  `status_obssdg` varchar(255) DEFAULT NULL,
  `lamp_legal` varchar(1000) DEFAULT NULL,
  `obs_date` date DEFAULT NULL,
  `status_obslegal` varchar(255) DEFAULT NULL,
  `obslegal_date` date DEFAULT NULL,
  `sla_obslegal` date DEFAULT NULL,
  `submit_wo` varchar(1000) DEFAULT NULL,
  `lamp_wo` varchar(1000) DEFAULT NULL,
  `wo_date` date DEFAULT NULL,
  `lamp_spkwo` varchar(500) DEFAULT NULL,
  `status_spkwo` varchar(100) DEFAULT NULL,
  `sla_spkwo` date DEFAULT NULL,
  `spkwo_date` date DEFAULT NULL,
  `catatan_spkwo` varchar(100) DEFAULT NULL,
  `status_spkwofat` varchar(100) DEFAULT NULL,
  `catatan_spkwofat` text DEFAULT NULL,
  `spkwofat_date` date DEFAULT NULL,
  `sla_spkwofat` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_desain`
--

INSERT INTO `sdg_desain` (`id`, `kode_lahan`, `lamp_desainplan`, `catatan_sdgdesain`, `tc`, `confirm_sdgdesain`, `confirm_sdgurugan`, `catatan_sdgurugan`, `urugan_date`, `obstacle`, `submit_legal`, `catatan_submit`, `submit_date`, `lamp_pbg`, `lamp_permit`, `lamp_urugan`, `urugan`, `catatan_obslegal`, `start_date`, `sla_date`, `survey_date`, `sla_survey`, `layout_date`, `sla_layout`, `slalegal_date`, `end_date`, `lamp_survey`, `note_survey`, `status_survey`, `lamp_layouting`, `note`, `obs_detail`, `status_obssdg`, `lamp_legal`, `obs_date`, `status_obslegal`, `obslegal_date`, `sla_obslegal`, `submit_wo`, `lamp_wo`, `wo_date`, `lamp_spkwo`, `status_spkwo`, `sla_spkwo`, `spkwo_date`, `catatan_spkwo`, `status_spkwofat`, `catatan_spkwofat`, `spkwofat_date`, `sla_spkwofat`) VALUES
(27, 'MLG-001', 'Template Laporan Harian Kerja Ibu.pdf', '', 'TC', 'Approve', NULL, NULL, NULL, '', 'In Process', NULL, NULL, NULL, NULL, NULL, 'No', NULL, '2024-08-11', '2024-08-31', NULL, '2024-08-16', NULL, NULL, '2024-08-18', NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'tes', 'In Process', '0000-00-00', 'tes', 'tes', 'Done', NULL, '2024-08-11', 'In Process', NULL, '2024-09-01', 'Yes', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-11', 'Template Laporan Harian Kerja Ibu.pdf', 'Approve', '2024-08-18', '2024-08-11', '', 'Approve', '', '2024-08-11', '2024-08-17'),
(28, 'CIMH-001', NULL, NULL, NULL, 'In Process', 'Approve', '', '2024-08-13', 'Yes', '', NULL, NULL, NULL, NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'Yes', NULL, NULL, '2024-09-02', NULL, '2024-08-22', NULL, NULL, NULL, NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'tes', NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'tes', 'tes', 'Done', NULL, '2024-08-13', 'In Process', NULL, '2024-09-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sdg_pk`
--

CREATE TABLE `sdg_pk` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) DEFAULT NULL,
  `month_1` varchar(1000) NOT NULL,
  `month_2` varchar(255) DEFAULT NULL,
  `month_3` varchar(255) DEFAULT NULL,
  `all_progress` varchar(255) DEFAULT NULL,
  `date_month1` date DEFAULT NULL,
  `date_month2` date DEFAULT NULL,
  `date_month3` date DEFAULT NULL,
  `week` varchar(1000) DEFAULT NULL,
  `date_week` varchar(1000) DEFAULT NULL,
  `catatan` varchar(1000) NOT NULL,
  `lamp_pk` varchar(1000) NOT NULL,
  `status_consact` varchar(255) DEFAULT NULL,
  `sla_consact` date DEFAULT NULL,
  `consact_date` date DEFAULT NULL,
  `lamp_consact` varchar(1000) DEFAULT NULL,
  `catatan_consact` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_pk`
--

INSERT INTO `sdg_pk` (`id`, `kode_lahan`, `month_1`, `month_2`, `month_3`, `all_progress`, `date_month1`, `date_month2`, `date_month3`, `week`, `date_week`, `catatan`, `lamp_pk`, `status_consact`, `sla_consact`, `consact_date`, `lamp_consact`, `catatan_consact`) VALUES
(49, 'MLG-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-11-16', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sdg_rab`
--

CREATE TABLE `sdg_rab` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `date` date DEFAULT NULL,
  `jenis_biaya` varchar(255) DEFAULT NULL,
  `jumlah` varchar(255) DEFAULT NULL,
  `lamp_rab` varchar(1000) DEFAULT NULL,
  `keterangan` varchar(5000) DEFAULT NULL,
  `confirm_sdgqs` varchar(255) DEFAULT NULL,
  `catatan_sdgqs` varchar(255) DEFAULT NULL,
  `confirm_qsurugan` varchar(100) DEFAULT NULL,
  `catatan_qsurugan` varchar(255) DEFAULT NULL,
  `slaurugan_date` date DEFAULT NULL,
  `qsurugan_date` date DEFAULT NULL,
  `jumlah_urugan` varchar(255) DEFAULT NULL,
  `ket_urugan` varchar(255) DEFAULT NULL,
  `lamp_raburugan` varchar(1000) DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_rab`
--

INSERT INTO `sdg_rab` (`id`, `kode_lahan`, `date`, `jenis_biaya`, `jumlah`, `lamp_rab`, `keterangan`, `confirm_sdgqs`, `catatan_sdgqs`, `confirm_qsurugan`, `catatan_qsurugan`, `slaurugan_date`, `qsurugan_date`, `jumlah_urugan`, `ket_urugan`, `lamp_raburugan`, `sla_date`, `start_date`) VALUES
(43, 'MLG-001', '0000-00-00', '', 'Rp. 90.000.000', 'Template Laporan Harian Kerja Ibu.pdf', 'tes', 'Approve', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-21', '2024-08-11'),
(44, 'CIMH-001', NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, 'Approve', '', '2024-08-23', '2024-08-13', 'Rp. 20.000.000', 'tes', 'Template Laporan Harian Kerja Ibu.pdf', '2024-08-23', '2024-08-13');

-- --------------------------------------------------------

--
-- Table structure for table `sign`
--

CREATE TABLE `sign` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `sdg_pm` varchar(255) NOT NULL,
  `note_sdgpm` varchar(255) DEFAULT NULL,
  `legal_alo` varchar(255) NOT NULL,
  `note_legalalo` varchar(255) DEFAULT NULL,
  `scm` varchar(255) NOT NULL,
  `note_scm` varchar(255) DEFAULT NULL,
  `it_hrga_marketing` varchar(255) NOT NULL,
  `note_ihm` varchar(255) DEFAULT NULL,
  `ops_rm` varchar(255) NOT NULL,
  `note_opsrm` varchar(255) DEFAULT NULL,
  `sdg_head` varchar(255) NOT NULL,
  `note_sdghead` varchar(255) DEFAULT NULL,
  `lamp_rto` varchar(255) NOT NULL,
  `soc_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `socdate_academy`
--

CREATE TABLE `socdate_academy` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `kpt_1` varchar(255) DEFAULT NULL,
  `kpt_2` varchar(255) DEFAULT NULL,
  `kpt_3` varchar(255) DEFAULT NULL,
  `status_kpt1` varchar(255) DEFAULT NULL,
  `status_kpt2` varchar(255) DEFAULT NULL,
  `status_kpt3` varchar(255) DEFAULT NULL,
  `kpt_date1` date DEFAULT NULL,
  `kpt_date2` date DEFAULT NULL,
  `kpt_date3` date DEFAULT NULL,
  `sla_kpt1` date DEFAULT NULL,
  `sla_kpt2` date DEFAULT NULL,
  `sla_kpt3` date DEFAULT NULL,
  `lamp_kpt1` varchar(1000) DEFAULT NULL,
  `lamp_kpt2` varchar(1000) DEFAULT NULL,
  `lamp_kpt3` varchar(1000) DEFAULT NULL,
  `catatan_kpt1` varchar(1000) DEFAULT NULL,
  `catatan_kpt2` varchar(1000) DEFAULT NULL,
  `catatan_kpt3` varchar(1000) DEFAULT NULL,
  `crew_needed1` varchar(100) DEFAULT NULL,
  `crew_needed2` varchar(100) DEFAULT NULL,
  `crew_needed3` varchar(100) DEFAULT NULL,
  `crew_act1` varchar(100) DEFAULT NULL,
  `crew_act2` varchar(100) DEFAULT NULL,
  `crew_act3` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_academy`
--

INSERT INTO `socdate_academy` (`id`, `kode_lahan`, `kpt_1`, `kpt_2`, `kpt_3`, `status_kpt1`, `status_kpt2`, `status_kpt3`, `kpt_date1`, `kpt_date2`, `kpt_date3`, `sla_kpt1`, `sla_kpt2`, `sla_kpt3`, `lamp_kpt1`, `lamp_kpt2`, `lamp_kpt3`, `catatan_kpt1`, `catatan_kpt2`, `catatan_kpt3`, `crew_needed1`, `crew_needed2`, `crew_needed3`, `crew_act1`, `crew_act2`, `crew_act3`) VALUES
(14, 'MLG-001', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, '2024-08-09', '2024-09-03', '2024-09-28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_fat`
--

CREATE TABLE `socdate_fat` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_qris` varchar(1000) DEFAULT NULL,
  `lamp_st` varchar(1000) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `atm_bank` varchar(1000) DEFAULT NULL,
  `fat_date` date DEFAULT NULL,
  `status_fat` varchar(255) DEFAULT NULL,
  `sla_fat` date DEFAULT NULL,
  `catatan_fat` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_fat`
--

INSERT INTO `socdate_fat` (`id`, `kode_lahan`, `lamp_qris`, `lamp_st`, `email`, `atm_bank`, `fat_date`, `status_fat`, `sla_fat`, `catatan_fat`) VALUES
(40, 'MLG-001', NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-10-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_hr`
--

CREATE TABLE `socdate_hr` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `tm` varchar(255) DEFAULT NULL,
  `lamp_tm` varchar(255) DEFAULT NULL,
  `ff_1` varchar(255) DEFAULT NULL,
  `ff_2` varchar(255) DEFAULT NULL,
  `ff_3` varchar(255) DEFAULT NULL,
  `hot` varchar(255) DEFAULT NULL,
  `lamp_hot` varchar(255) DEFAULT NULL,
  `sla_ff1` date DEFAULT NULL,
  `sla_ff2` date DEFAULT NULL,
  `sla_ff3` date DEFAULT NULL,
  `status_ff1` varchar(255) DEFAULT NULL,
  `status_ff2` varchar(255) DEFAULT NULL,
  `status_ff3` varchar(255) DEFAULT NULL,
  `ff1_date` date DEFAULT NULL,
  `ff2_date` date DEFAULT NULL,
  `ff3_date` date DEFAULT NULL,
  `lamp_ff1` varchar(255) DEFAULT NULL,
  `lamp_ff2` varchar(255) DEFAULT NULL,
  `lamp_ff3` varchar(255) DEFAULT NULL,
  `status_tm` varchar(255) DEFAULT NULL,
  `tm_date` date DEFAULT NULL,
  `sla_tm` date DEFAULT NULL,
  `status_hot` varchar(255) DEFAULT NULL,
  `hot_date` date DEFAULT NULL,
  `sla_hot` date DEFAULT NULL,
  `catatan_tm` varchar(1000) DEFAULT NULL,
  `catatan_hot` varchar(1000) DEFAULT NULL,
  `catatan_ff1` varchar(1000) DEFAULT NULL,
  `catatan_ff2` varchar(1000) DEFAULT NULL,
  `catatan_ff3` varchar(1000) DEFAULT NULL,
  `persen_ff1` varchar(255) DEFAULT NULL,
  `persen_ff2` varchar(255) DEFAULT NULL,
  `persen_ff3` varchar(255) DEFAULT NULL,
  `persen_hot` varchar(255) DEFAULT NULL,
  `status_fl` varchar(255) DEFAULT NULL,
  `catatan_fl` varchar(1000) DEFAULT NULL,
  `fl_date` date DEFAULT NULL,
  `lamp_fl` varchar(1000) DEFAULT NULL,
  `sla_fl` date DEFAULT NULL,
  `fl` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_hr`
--

INSERT INTO `socdate_hr` (`id`, `kode_lahan`, `tm`, `lamp_tm`, `ff_1`, `ff_2`, `ff_3`, `hot`, `lamp_hot`, `sla_ff1`, `sla_ff2`, `sla_ff3`, `status_ff1`, `status_ff2`, `status_ff3`, `ff1_date`, `ff2_date`, `ff3_date`, `lamp_ff1`, `lamp_ff2`, `lamp_ff3`, `status_tm`, `tm_date`, `sla_tm`, `status_hot`, `hot_date`, `sla_hot`, `catatan_tm`, `catatan_hot`, `catatan_ff1`, `catatan_ff2`, `catatan_ff3`, `persen_ff1`, `persen_ff2`, `persen_ff3`, `persen_hot`, `status_fl`, `catatan_fl`, `fl_date`, `lamp_fl`, `sla_fl`, `fl`) VALUES
(18, 'MLG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-09', '2024-09-03', '2024-09-28', 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, '2024-11-19', 'In Process', NULL, '2024-09-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, '2024-11-19', NULL),
(19, 'CIMH-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, '2024-11-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, '2024-11-20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_ir`
--

CREATE TABLE `socdate_ir` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_rabcs` varchar(1000) DEFAULT NULL,
  `lamp_rabsecurity` varchar(1000) DEFAULT NULL,
  `cs_sec_date` date DEFAULT NULL,
  `status_ir` varchar(255) DEFAULT NULL,
  `sla_ir` date DEFAULT NULL,
  `ir_date` date DEFAULT NULL,
  `catatan_ir` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_ir`
--

INSERT INTO `socdate_ir` (`id`, `kode_lahan`, `lamp_rabcs`, `lamp_rabsecurity`, `cs_sec_date`, `status_ir`, `sla_ir`, `ir_date`, `catatan_ir`) VALUES
(30, 'MLG-001', NULL, NULL, NULL, 'In Process', '2024-10-16', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_it`
--

CREATE TABLE `socdate_it` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `kode_dvr` varchar(255) DEFAULT NULL,
  `web_report` varchar(1000) DEFAULT NULL,
  `akun_gis` varchar(1000) DEFAULT NULL,
  `lamp_internet` varchar(1000) DEFAULT NULL,
  `lamp_cctv` varchar(1000) DEFAULT NULL,
  `lamp_config` varchar(1000) DEFAULT NULL,
  `lamp_printer` varchar(1000) DEFAULT NULL,
  `lamp_sound` varchar(1000) DEFAULT NULL,
  `it_date` date DEFAULT NULL,
  `config_date` date DEFAULT NULL,
  `status_it` varchar(255) DEFAULT NULL,
  `status_itconfig` varchar(255) DEFAULT NULL,
  `sla_it` date DEFAULT NULL,
  `sla_itconfig` date DEFAULT NULL,
  `catatan_it` varchar(1000) DEFAULT NULL,
  `catatan_config` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_it`
--

INSERT INTO `socdate_it` (`id`, `kode_lahan`, `kode_dvr`, `web_report`, `akun_gis`, `lamp_internet`, `lamp_cctv`, `lamp_config`, `lamp_printer`, `lamp_sound`, `it_date`, `config_date`, `status_it`, `status_itconfig`, `sla_it`, `sla_itconfig`, `catatan_it`, `catatan_config`) VALUES
(34, 'MLG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', '2024-10-20', '2024-10-09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_legal`
--

CREATE TABLE `socdate_legal` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `mou_parkirsampah` varchar(1000) DEFAULT NULL,
  `permit_date` date DEFAULT NULL,
  `sampahparkir_date` date DEFAULT NULL,
  `status_legal` varchar(255) DEFAULT NULL,
  `status_permit` varchar(255) DEFAULT NULL,
  `sla_legal` date DEFAULT NULL,
  `sla_permit` date DEFAULT NULL,
  `catatan_legal` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_legal`
--

INSERT INTO `socdate_legal` (`id`, `kode_lahan`, `mou_parkirsampah`, `permit_date`, `sampahparkir_date`, `status_legal`, `status_permit`, `sla_legal`, `sla_permit`, `catatan_legal`) VALUES
(33, 'MLG-001', NULL, NULL, NULL, 'In Process', NULL, '2024-10-16', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_marketing`
--

CREATE TABLE `socdate_marketing` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `gmaps` varchar(1000) DEFAULT NULL,
  `lamp_gmaps` varchar(1000) DEFAULT NULL,
  `id_m_shopee` varchar(255) DEFAULT NULL,
  `id_m_gojek` varchar(255) DEFAULT NULL,
  `id_m_grab` varchar(255) DEFAULT NULL,
  `email_resto` varchar(255) DEFAULT NULL,
  `lamp_merchant` varchar(1000) DEFAULT NULL,
  `lamp_content` varchar(1000) DEFAULT NULL,
  `issue_marketing` varchar(1000) DEFAULT NULL,
  `note_issuemarketing` varchar(1000) DEFAULT NULL,
  `merchant_date` date DEFAULT NULL,
  `marketing_date` date DEFAULT NULL,
  `status_marketing` varchar(255) DEFAULT NULL,
  `sla_marketing` date DEFAULT NULL,
  `catatan_marketing` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_marketing`
--

INSERT INTO `socdate_marketing` (`id`, `kode_lahan`, `gmaps`, `lamp_gmaps`, `id_m_shopee`, `id_m_gojek`, `id_m_grab`, `email_resto`, `lamp_merchant`, `lamp_content`, `issue_marketing`, `note_issuemarketing`, `merchant_date`, `marketing_date`, `status_marketing`, `sla_marketing`, `catatan_marketing`) VALUES
(32, 'MLG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-10-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_scm`
--

CREATE TABLE `socdate_scm` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_sj` varchar(1000) DEFAULT NULL,
  `sj_date` date DEFAULT NULL,
  `status_scm` varchar(255) DEFAULT NULL,
  `sla_scm` date DEFAULT NULL,
  `catatan_scm` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_scm`
--

INSERT INTO `socdate_scm` (`id`, `kode_lahan`, `lamp_sj`, `sj_date`, `status_scm`, `sla_scm`, `catatan_scm`) VALUES
(33, 'MLG-001', NULL, NULL, 'In Process', '2024-10-18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_sdg`
--

CREATE TABLE `socdate_sdg` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `sumber_air` varchar(255) DEFAULT NULL,
  `lamp_sumberair` varchar(500) DEFAULT NULL,
  `kesesuaian_ujilab` varchar(255) DEFAULT NULL,
  `lamp_ujilab` varchar(1000) DEFAULT NULL,
  `filter_air` varchar(255) DEFAULT NULL,
  `lamp_filterair` varchar(500) DEFAULT NULL,
  `debit_airsumur` varchar(255) DEFAULT NULL,
  `debit_airpdam` varchar(255) DEFAULT NULL,
  `id_pdam` varchar(255) DEFAULT NULL,
  `status_sdgsumber` varchar(255) DEFAULT NULL,
  `sdgsumber_date` date DEFAULT NULL,
  `sumber_listrik` varchar(255) DEFAULT NULL,
  `note_sumberlistrik` varchar(500) DEFAULT NULL,
  `form_pengajuanlistrik` varchar(1000) DEFAULT NULL,
  `hasil_va` varchar(500) DEFAULT NULL,
  `id_pln` varchar(255) DEFAULT NULL,
  `biaya_perkwh` varchar(255) DEFAULT NULL,
  `status_sdglistrik` varchar(255) DEFAULT NULL,
  `sdglistrik_date` date DEFAULT NULL,
  `lampwo_reqipal` varchar(500) DEFAULT NULL,
  `status_sdgipal` varchar(255) DEFAULT NULL,
  `sdgipal_date` date DEFAULT NULL,
  `lamp_spkwofilterair` varchar(500) DEFAULT NULL,
  `status_procurspkwofa` varchar(255) DEFAULT NULL,
  `catatan_spkwofa` varchar(255) DEFAULT NULL,
  `sla_spkwofa` date DEFAULT NULL,
  `spkwofa_date` date DEFAULT NULL,
  `lamp_spkwoipal` varchar(500) DEFAULT NULL,
  `status_spkwoipal` varchar(255) DEFAULT NULL,
  `catatan_spkwoipal` varchar(100) DEFAULT NULL,
  `sla_spkwoipal` date DEFAULT NULL,
  `spkwoipal_date` date DEFAULT NULL,
  `lamp_paylistrik` varchar(500) DEFAULT NULL,
  `lamp_paypdam` varchar(500) DEFAULT NULL,
  `status_tafpay` varchar(255) DEFAULT NULL,
  `sla_tafpay` date DEFAULT NULL,
  `tafpay_date` date DEFAULT NULL,
  `tipe_ipal` varchar(255) DEFAULT NULL,
  `note_ipalscm` varchar(1000) DEFAULT NULL,
  `lamp_woipal` varchar(500) DEFAULT NULL,
  `lamp_hbm` varchar(500) DEFAULT NULL,
  `status_scmipal` varchar(255) DEFAULT NULL,
  `scmipal_date` date DEFAULT NULL,
  `sla_scmipal` date DEFAULT NULL,
  `sla_mep` date DEFAULT NULL,
  `catatan_sdg` varchar(1000) DEFAULT NULL,
  `status_spkfataf` text DEFAULT NULL,
  `status_spkwoipaltaf` text DEFAULT NULL,
  `spkfataf_date` date DEFAULT NULL,
  `spkwoipaltaf_date` date DEFAULT NULL,
  `sla_spkfataf` date DEFAULT NULL,
  `sla_spkwoipaltaf` date DEFAULT NULL,
  `catatan_spkfataf` varchar(100) DEFAULT NULL,
  `catatan_spkwoipaltaf` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_sdg`
--

INSERT INTO `socdate_sdg` (`id`, `kode_lahan`, `sumber_air`, `lamp_sumberair`, `kesesuaian_ujilab`, `lamp_ujilab`, `filter_air`, `lamp_filterair`, `debit_airsumur`, `debit_airpdam`, `id_pdam`, `status_sdgsumber`, `sdgsumber_date`, `sumber_listrik`, `note_sumberlistrik`, `form_pengajuanlistrik`, `hasil_va`, `id_pln`, `biaya_perkwh`, `status_sdglistrik`, `sdglistrik_date`, `lampwo_reqipal`, `status_sdgipal`, `sdgipal_date`, `lamp_spkwofilterair`, `status_procurspkwofa`, `catatan_spkwofa`, `sla_spkwofa`, `spkwofa_date`, `lamp_spkwoipal`, `status_spkwoipal`, `catatan_spkwoipal`, `sla_spkwoipal`, `spkwoipal_date`, `lamp_paylistrik`, `lamp_paypdam`, `status_tafpay`, `sla_tafpay`, `tafpay_date`, `tipe_ipal`, `note_ipalscm`, `lamp_woipal`, `lamp_hbm`, `status_scmipal`, `scmipal_date`, `sla_scmipal`, `sla_mep`, `catatan_sdg`, `status_spkfataf`, `status_spkwoipaltaf`, `spkfataf_date`, `spkwoipaltaf_date`, `sla_spkfataf`, `sla_spkwoipaltaf`, `catatan_spkfataf`, `catatan_spkwoipaltaf`) VALUES
(32, 'MLG-001', 'PDAM', 'Template Laporan Harian Kerja Ibu.pdf', 'Yes', 'Template Laporan Harian Kerja Ibu.pdf', 'Yes', 'Template Laporan Harian Kerja Ibu.pdf', '900', '800', '12345', 'Proceed', NULL, 'PLN', '', 'Template Laporan Harian Kerja Ibu.pdf', '12345', '67890', 'Rp. 100.000', 'Proceed', NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'Proceed', NULL, 'Template Laporan Harian Kerja Ibu.pdf', 'Approve', '', '2024-08-18', '2024-08-11', 'Template Laporan Harian Kerja Ibu.pdf', 'Approve', '', '2024-08-18', '2024-08-11', NULL, NULL, 'In Process', '2024-08-18', NULL, '67890', 'tes', 'Template Laporan Harian Kerja Ibu.pdf', 'Template Laporan Harian Kerja Ibu.pdf', 'In Process', '2024-08-11', '2024-08-18', '2024-10-02', NULL, 'Approve', 'Approve', '2024-08-11', '2024-08-11', '2024-08-17', '2024-08-17', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `soc_fat`
--

CREATE TABLE `soc_fat` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `qris` varchar(255) NOT NULL,
  `note_qris` varchar(255) DEFAULT NULL,
  `edc` varchar(255) NOT NULL,
  `note_edc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_hrga`
--

CREATE TABLE `soc_hrga` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `security` varchar(255) NOT NULL,
  `note_security` varchar(255) DEFAULT NULL,
  `cs` varchar(255) NOT NULL,
  `note_cs` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_it`
--

CREATE TABLE `soc_it` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `cctv` varchar(255) NOT NULL,
  `note_cctv` varchar(255) DEFAULT NULL,
  `audio_system` varchar(255) NOT NULL,
  `note_as` varchar(255) DEFAULT NULL,
  `lan_infra` varchar(255) NOT NULL,
  `note_lan` varchar(255) DEFAULT NULL,
  `internet_km` varchar(255) NOT NULL,
  `note_interkm` varchar(255) DEFAULT NULL,
  `internet_cust` varchar(255) NOT NULL,
  `note_intercut` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_legal`
--

CREATE TABLE `soc_legal` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `perijinan` varchar(255) NOT NULL,
  `note_p` varchar(255) DEFAULT NULL,
  `sampah_parkir` varchar(255) NOT NULL,
  `note_sp` varchar(255) DEFAULT NULL,
  `akses_jkm` varchar(255) NOT NULL,
  `note_ajkm` varchar(255) DEFAULT NULL,
  `pkl` varchar(255) NOT NULL,
  `note_pkl` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_marketing`
--

CREATE TABLE `soc_marketing` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `post_content` varchar(255) NOT NULL,
  `note_pc` varchar(255) DEFAULT NULL,
  `ojol` varchar(255) NOT NULL,
  `note_ojol` varchar(255) DEFAULT NULL,
  `tikor_maps` varchar(255) NOT NULL,
  `note_tm` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_rto`
--

CREATE TABLE `soc_rto` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `nama_store` varchar(255) NOT NULL,
  `rto_date` date NOT NULL,
  `pengaju_rto` varchar(255) NOT NULL,
  `status_op` varchar(255) NOT NULL,
  `status_rto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soc_sdg`
--

CREATE TABLE `soc_sdg` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `bangunan_mural` varchar(255) NOT NULL,
  `note_bm` varchar(255) DEFAULT NULL,
  `daya_listrik` varchar(255) NOT NULL,
  `note_dl` varchar(255) DEFAULT NULL,
  `supply_air` varchar(255) NOT NULL,
  `note_sa` varchar(255) DEFAULT NULL,
  `aliran_air` varchar(255) NOT NULL,
  `note_aa` varchar(255) DEFAULT NULL,
  `kualitas_keramik` varchar(255) NOT NULL,
  `note_kk` varchar(255) DEFAULT NULL,
  `paving_loading` varchar(255) NOT NULL,
  `note_pl` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `summary_soc`
--

CREATE TABLE `summary_soc` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `go_fix` date DEFAULT NULL,
  `rto_act` date DEFAULT NULL,
  `type_kitchen` varchar(255) DEFAULT NULL,
  `jam_ops` varchar(255) DEFAULT NULL,
  `project_sales` varchar(255) DEFAULT NULL,
  `crew_needed` varchar(255) DEFAULT NULL,
  `spk_release` varchar(255) DEFAULT NULL,
  `gocons_progress` varchar(1000) DEFAULT NULL,
  `rto_score` varchar(255) DEFAULT NULL,
  `kualitas_go` varchar(255) DEFAULT NULL,
  `status_go` varchar(255) DEFAULT NULL,
  `jml_hari` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `summary_soc`
--

INSERT INTO `summary_soc` (`id`, `kode_lahan`, `go_fix`, `rto_act`, `type_kitchen`, `jam_ops`, `project_sales`, `crew_needed`, `spk_release`, `gocons_progress`, `rto_score`, `kualitas_go`, `status_go`, `jml_hari`) VALUES
(17, 'MLG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'MLG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(5) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `level`) VALUES
(1, 'Admin PPA', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin'),
(2, 'Departemen Legal', 'legal', '2fbd4ee396cdac223059952a7fe01e54', 'Legal'),
(3, 'Departemen Real Estate', 're', '12eccbdd9b32918131341f38907cbbb5', 'Re');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id` int(11) NOT NULL,
  `kode_vendor` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `nohp` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `detail` varchar(1000) NOT NULL,
  `lamp_profil` varchar(1000) DEFAULT NULL,
  `lamp_vendor` varchar(1000) NOT NULL,
  `status_lokasi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id`, `kode_vendor`, `city`, `nama`, `alamat`, `nohp`, `rate`, `detail`, `lamp_profil`, `lamp_vendor`, `status_lokasi`) VALUES
(3, 'VMG_ML1', 'MALANG', 'm', 'm', 'm', '', 'm', '', '', 'Aktif'),
(4, 'VMG_ML2', 'MALANG', 'm', 'm', 'mm', '', 'm', 'Data_Keluhan (8).xlsx', 'Data_Keluhan (6).xlsx', 'Aktif'),
(5, 'VMG_ML3', 'MALANG', 'k', 'k', 'k', '', 'k', NULL, 'Data_Keluhan (2).xlsx', 'Aktif'),
(6, 'BAT001', 'BATU', 'batu ceria', 'batu kota', '456789', '', 'batu batu', 'Data_Keluhan (8).xlsx', 'Data_Keluhan (8).xlsx', 'Aktif'),
(7, 'BEK001', 'BEKASI', 'k', 'k', 'k', '', 'k', 'MicrosoftTeams-image (7).png', 'Data_Keluhan (6).xlsx', 'On Planning'),
(8, 'CIK001', 'CIKARANG', 'c', 'c', 'c', '', 'c', 'Data_Keluhan (8).xlsx', 'MicrosoftTeams-image (7).png', 'Aktif'),
(9, 'CIR001', 'CIREBON', 'io;', 'ikm', 'kiml', '', 'rty', 'Data_Keluhan (7) (1).xlsx', 'MicrosoftTeams-image (6).png,Data_Keluhan (5).xlsx', 'Aktif'),
(10, 'BOG001', 'BOGOR', 'bogor', 'bogor', '4567', '', 'detail', 'Data_Keluhan (2) (1).xlsx', 'Data_Keluhan (5) (1).xlsx', 'Aktif'),
(11, 'JAK001', 'JAKARTA', 'jakarta', 'jakarta', 'jakarta', '', 'jakarta', 'Data_Keluhan (5) (1).xlsx', 'Data_Keluhan (5) (1).xlsx', 'Aktif');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doc_legal`
--
ALTER TABLE `doc_legal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokumen_loacd`
--
ALTER TABLE `dokumen_loacd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `draft`
--
ALTER TABLE `draft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hold_project`
--
ALTER TABLE `hold_project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `konstruksi`
--
ALTER TABLE `konstruksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `land`
--
ALTER TABLE `land`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_city`
--
ALTER TABLE `master_city`
  ADD PRIMARY KEY (`IDCity`) USING BTREE;

--
-- Indexes for table `master_sla`
--
ALTER TABLE `master_sla`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_slacons`
--
ALTER TABLE `master_slacons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mom`
--
ALTER TABLE `mom`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `note_ba`
--
ALTER TABLE `note_ba`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `note_legal`
--
ALTER TABLE `note_legal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `obs_sdg`
--
ALTER TABLE `obs_sdg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `procurement`
--
ALTER TABLE `procurement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `re`
--
ALTER TABLE `re`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resto`
--
ALTER TABLE `resto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sdg_desain`
--
ALTER TABLE `sdg_desain`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sdg_pk`
--
ALTER TABLE `sdg_pk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sdg_rab`
--
ALTER TABLE `sdg_rab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sign`
--
ALTER TABLE `sign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_academy`
--
ALTER TABLE `socdate_academy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_fat`
--
ALTER TABLE `socdate_fat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_hr`
--
ALTER TABLE `socdate_hr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_ir`
--
ALTER TABLE `socdate_ir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_it`
--
ALTER TABLE `socdate_it`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_legal`
--
ALTER TABLE `socdate_legal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_marketing`
--
ALTER TABLE `socdate_marketing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_scm`
--
ALTER TABLE `socdate_scm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socdate_sdg`
--
ALTER TABLE `socdate_sdg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_fat`
--
ALTER TABLE `soc_fat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_hrga`
--
ALTER TABLE `soc_hrga`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_it`
--
ALTER TABLE `soc_it`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_legal`
--
ALTER TABLE `soc_legal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_marketing`
--
ALTER TABLE `soc_marketing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_rto`
--
ALTER TABLE `soc_rto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soc_sdg`
--
ALTER TABLE `soc_sdg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `summary_soc`
--
ALTER TABLE `summary_soc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doc_legal`
--
ALTER TABLE `doc_legal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dokumen_loacd`
--
ALTER TABLE `dokumen_loacd`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `draft`
--
ALTER TABLE `draft`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hold_project`
--
ALTER TABLE `hold_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `issue`
--
ALTER TABLE `issue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `konstruksi`
--
ALTER TABLE `konstruksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `land`
--
ALTER TABLE `land`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `master_sla`
--
ALTER TABLE `master_sla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `master_slacons`
--
ALTER TABLE `master_slacons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `mom`
--
ALTER TABLE `mom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `note_ba`
--
ALTER TABLE `note_ba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `note_legal`
--
ALTER TABLE `note_legal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `obs_sdg`
--
ALTER TABLE `obs_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `procurement`
--
ALTER TABLE `procurement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `re`
--
ALTER TABLE `re`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `resto`
--
ALTER TABLE `resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sdg_desain`
--
ALTER TABLE `sdg_desain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `sdg_pk`
--
ALTER TABLE `sdg_pk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `sdg_rab`
--
ALTER TABLE `sdg_rab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `sign`
--
ALTER TABLE `sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `socdate_academy`
--
ALTER TABLE `socdate_academy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `socdate_fat`
--
ALTER TABLE `socdate_fat`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `socdate_hr`
--
ALTER TABLE `socdate_hr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `socdate_ir`
--
ALTER TABLE `socdate_ir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `socdate_it`
--
ALTER TABLE `socdate_it`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `socdate_legal`
--
ALTER TABLE `socdate_legal`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `socdate_marketing`
--
ALTER TABLE `socdate_marketing`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `socdate_scm`
--
ALTER TABLE `socdate_scm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `socdate_sdg`
--
ALTER TABLE `socdate_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `soc_fat`
--
ALTER TABLE `soc_fat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `soc_hrga`
--
ALTER TABLE `soc_hrga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `soc_it`
--
ALTER TABLE `soc_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `soc_legal`
--
ALTER TABLE `soc_legal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `soc_marketing`
--
ALTER TABLE `soc_marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `soc_rto`
--
ALTER TABLE `soc_rto`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `soc_sdg`
--
ALTER TABLE `soc_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `summary_soc`
--
ALTER TABLE `summary_soc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
