-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2024 at 05:24 AM
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
  `draw_teknis` varchar(255) NOT NULL,
  `ba_serahterima` varchar(255) NOT NULL,
  `mou_parkir` varchar(255) NOT NULL,
  `info_sampah` varchar(255) NOT NULL,
  `pkkpr` varchar(255) NOT NULL,
  `und_tasyakuran` varchar(255) NOT NULL,
  `nib` varchar(255) NOT NULL,
  `imb` varchar(255) NOT NULL,
  `tdup` varchar(255) NOT NULL,
  `bpbdpm` varchar(255) NOT NULL,
  `reklame` varchar(255) NOT NULL,
  `sppl` varchar(255) NOT NULL,
  `damkar` varchar(255) NOT NULL,
  `peil_banjir` varchar(255) NOT NULL,
  `andalalin` varchar(255) NOT NULL,
  `iuran_warga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_legal`
--

INSERT INTO `doc_legal` (`id`, `kode_lahan`, `draw_teknis`, `ba_serahterima`, `mou_parkir`, `info_sampah`, `pkkpr`, `und_tasyakuran`, `nib`, `imb`, `tdup`, `bpbdpm`, `reklame`, `sppl`, `damkar`, `peil_banjir`, `andalalin`, `iuran_warga`) VALUES
(1, 'MLG-001', 'on', 'on', 'on', 'off', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_loacd`
--

CREATE TABLE `dokumen_loacd` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `tgl_berlaku` date DEFAULT NULL,
  `status_approvowner` varchar(255) NOT NULL,
  `status_approvlegal` varchar(255) NOT NULL,
  `status_approvnego` varchar(255) NOT NULL,
  `status_approvre` varchar(255) DEFAULT NULL,
  `status_approvlegalvd` varchar(255) DEFAULT NULL,
  `lamp_land` varchar(255) NOT NULL,
  `lamp_loacd` varchar(1000) DEFAULT NULL,
  `catatan` varchar(1000) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `slaloa_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slavd_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_loacd`
--

INSERT INTO `dokumen_loacd` (`id`, `kode_lahan`, `tgl_berlaku`, `status_approvowner`, `status_approvlegal`, `status_approvnego`, `status_approvre`, `status_approvlegalvd`, `lamp_land`, `lamp_loacd`, `catatan`, `start_date`, `slaloa_date`, `end_date`, `slavd_date`) VALUES
(11, 'MLG-001', '2024-05-22', 'Approve', 'Approve', 'Approve', 'Approve', 'Approve', '../uploads/Sertifikat Bem 2021.pdf', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'ppp', NULL, NULL, NULL, NULL),
(16, 'KDR-002', '2024-05-28', 'Approve', 'Approve', 'Approve', 'Approve', 'Approve', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'sss', '2024-05-27', '2024-06-03', '2024-05-27', '2024-06-03'),
(17, 'BL-002', '2024-05-29', 'Approve', 'Approve', 'Approve', 'Approve', 'In Process', '../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'loa bl', '2024-05-27', '2024-06-03', NULL, '2024-06-03'),
(18, 'MLG-002', '2024-05-30', 'Approve', 'Approve', 'Approve', 'Approve', 'Approve', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '../uploads/Sertifikat Bem 2021.pdf', 'ddd', '2024-05-27', '2024-06-03', '2024-06-14', '2024-06-03'),
(19, 'SBY-002', NULL, 'Approve', 'Approve', 'Approve', 'In Process', NULL, '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', NULL, NULL, NULL, '2024-06-03', NULL, NULL),
(20, 'SBY-001', '2024-05-30', 'Approve', 'Approve', 'Approve', 'Approve', 'Approve', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'sby 1', '2024-05-27', '2024-06-03', '2024-05-27', '2024-06-03');

-- --------------------------------------------------------

--
-- Table structure for table `draft`
--

CREATE TABLE `draft` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `nama_lahan` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `lamp_land` varchar(255) NOT NULL,
  `lamp_loacd` varchar(255) NOT NULL,
  `lamp_draf` varchar(255) DEFAULT NULL,
  `jadwal_psm` date DEFAULT NULL,
  `catatan_legal` varchar(1000) NOT NULL,
  `valdoc_legal` varchar(1000) DEFAULT NULL,
  `draft_legal` varchar(255) DEFAULT NULL,
  `confirm_nego` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slavd_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `draft`
--

INSERT INTO `draft` (`id`, `kode_lahan`, `nama_lahan`, `lokasi`, `lamp_land`, `lamp_loacd`, `lamp_draf`, `jadwal_psm`, `catatan_legal`, `valdoc_legal`, `draft_legal`, `confirm_nego`, `start_date`, `sla_date`, `slalegal_date`, `end_date`, `slavd_date`) VALUES
(7, 'MLG-001', 'm', 'm', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/uts dini.jpeg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/Sertifikat Bem 2021.pdf,../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '2024-05-22', '', 'Approve', 'Approve', 'Approve', NULL, NULL, NULL, '0000-00-00', NULL),
(11, 'KDR-002', 'kediri', 'k', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/RTO.pdf', '2024-06-17', 'legal', NULL, 'Approve', 'In Process', '2024-06-14', '2024-06-28', '2024-06-03', '0000-00-00', NULL),
(12, 'SBY-001', 'sby 1', 'y', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', '../uploads/bbb.pdf,../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '2024-05-30', 'draft sby', 'Approve', 'Approve', 'Approve', '2024-05-31', '2024-06-03', '2024-06-03', '2024-05-31', NULL),
(13, 'MLG-002', 'Mlg', 'ml', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '../uploads/Sertifikat Bem 2021.pdf', NULL, NULL, '', NULL, 'In Process', 'In Process', NULL, NULL, '2024-06-23', NULL, NULL);

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
  `lokasi` varchar(255) DEFAULT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `alamat_pemilik` varchar(255) DEFAULT NULL,
  `no_tlp` varchar(50) DEFAULT NULL,
  `luas_area` varchar(255) DEFAULT NULL,
  `lamp_land` varchar(1000) DEFAULT NULL,
  `status_approvre` varchar(255) DEFAULT NULL,
  `status_date` date DEFAULT NULL,
  `sla` date DEFAULT NULL,
  `re_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land`
--

INSERT INTO `land` (`id`, `city`, `kode_lahan`, `nama_lahan`, `status_land`, `lokasi`, `nama_pemilik`, `alamat_pemilik`, `no_tlp`, `luas_area`, `lamp_land`, `status_approvre`, `status_date`, `sla`, `re_date`) VALUES
(12, 'MALANG', 'MLG-001', 'm', 'Aktif', 'm', 'm', 'm', 'm', 'm', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/uts dini.jpeg', 'Approve', '2024-05-21', NULL, '2024-05-22'),
(13, 'BLITAR', 'BL-001', 'b', 'Reject', 'b', 'b', 'b', 'b', 'b', '../uploads/UTS SISTEM OPERASI.jpeg', 'In Process', '2024-05-22', NULL, '2024-05-23'),
(14, 'KEDIRI', 'KDR-001', 'o', 'Reject', 'o', 'o', 'o', 'o', 'o', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'In Process', '2024-05-23', NULL, '2024-05-24'),
(15, 'SURABAYA', 'SBY-002', 'Surabaya', 'Aktif', 'y', 'y', 'y', 'y', 'y', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'Approve', '2024-05-23', NULL, '2024-05-24'),
(16, 'BLITAR', 'BL-002', 'Blitar', 'Aktif', 'b', 'b', 'b', 'b', 'b', '../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-05-27'),
(17, 'MALANG', 'MLG-002', 'Mlg', 'Aktif', 'ml', 'ml', 'ml', 'ml', 'ml', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', 'Approve', '2024-05-24', '2024-05-31', '2024-05-24'),
(18, 'KEDIRI', 'KDR-002', 'kediri', 'Aktif', 'k', 'k', 'k', 'k', 'k', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'Approve', '2024-05-24', '2024-05-31', '2024-05-27'),
(19, 'BLITAR', 'BL-003', 'blitar', 'Aktif', 'p', 'p', 'p', 'p', 'p', '../uploads/Sertifikat Bem 2021.pdf,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-05-27'),
(20, 'KEDIRI', 'KDR-003', 'kediri 3', '', 'ku', 'ku', 'ku', 'ku', 'ku', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-05-27'),
(21, 'SURABAYA', 'SBY-003', 'sby 3', 'Aktif', 's', 's', 's', 's', 's', '../uploads/Sertifikat Bem 2021.pdf,../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'Approve', '2024-05-27', '2024-06-03', '2024-05-27'),
(22, 'MALANG', 'MLG-003', 'mlg 3', 'Aktif', NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-05-27', '2024-06-03', NULL),
(23, 'SURABAYA', 'SBY-004', 'sby 4', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'MALANG', 'MLG-004', 'mlg 4', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'BLITAR', 'BL-004', 'BL 4', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'KEDIRI', 'KDR-004', '', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'SURABAYA', 'SBY-001', 'sby 1', 'Aktif', 'y', 'y', 'y', 'y', 'y', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'Approve', '2024-05-27', '2024-06-03', '2024-05-27');

-- --------------------------------------------------------

--
-- Table structure for table `master_city`
--

CREATE TABLE `master_city` (
  `IDCity` int(11) DEFAULT NULL,
  `City` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_city`
--

INSERT INTO `master_city` (`IDCity`, `City`) VALUES
(1, 'BANDUNG'),
(2, 'BATU'),
(3, 'BEKASI'),
(4, 'BLITAR'),
(5, 'BOGOR'),
(6, 'BOJONEGORO'),
(7, 'CIAMIS'),
(8, 'CIANJUR'),
(9, 'CIKARANG'),
(10, 'CILACAP'),
(11, 'CIMAHI'),
(12, 'CIREBON'),
(13, 'DEPOK'),
(14, 'GRESIK'),
(15, 'JAKARTA'),
(16, 'JEMBER'),
(17, 'JOMBANG'),
(18, 'KARAWANG'),
(19, 'KEDIRI'),
(20, 'KLATEN'),
(21, 'KUDUS'),
(22, 'LAMONGAN'),
(23, 'MADIUN'),
(24, 'MAGELANG'),
(25, 'MAKASSAR'),
(26, 'MALANG'),
(27, 'MOJOKERTO'),
(28, 'NGAWI'),
(29, 'PAMEKASAN'),
(30, 'PASURUAN'),
(31, 'PEKALONGAN'),
(32, 'PONOROGO'),
(33, 'PROBOLINGGO'),
(34, 'PURBALINGGA'),
(35, 'PURWAKARTA'),
(36, 'PURWOKERTO'),
(37, 'SALATIGA'),
(38, 'SEMARANG'),
(39, 'SIDOARJO'),
(40, 'SLEMAN'),
(41, 'SUKOHARJO'),
(42, 'SUMEDANG'),
(43, 'SURABAYA'),
(44, 'SURAKARTA'),
(45, 'TANGERANG'),
(46, 'TASIKMALAYA'),
(47, 'TEGAL'),
(48, 'TUBAN'),
(49, 'TULUNGAGUNG'),
(50, 'YOGYAKARTA');

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
(11, 'ST-EQP', '87'),
(12, 'ST-Konstruksi', '91'),
(13, 'LOA-CD', '21'),
(14, 'VD', '4'),
(15, 'Draft-Sewa', '9'),
(16, 'TTD-Sewa', '14'),
(17, 'Design', '34'),
(18, 'Permit', '34'),
(19, 'QS', '10'),
(20, 'Tender', '14'),
(22, 'RTO', '4'),
(23, 'VL', '9');

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
(1, NULL, NULL),
(2, 'sdg', '14'),
(3, 'legal', '7'),
(4, 'legal_permit', '17'),
(5, 'ir', '7'),
(6, 'hrga_tm', '100'),
(7, 'hrga_cff1', '75'),
(8, 'hrga_cff2', '50'),
(9, 'hrga_cff3', '25'),
(10, 'ho_training', '30'),
(11, 'scm', '5'),
(12, 'it', '3'),
(13, 'it_config', '14'),
(14, 'marketing', '7'),
(15, 'marketing_rm', '30'),
(16, 'fat', '7');

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

--
-- Dumping data for table `note_ba`
--

INSERT INTO `note_ba` (`id`, `kode_lahan`, `bast_defectlist`, `note_bdl`, `check_supervisi`, `note_checkspv`, `pengukuran`, `note_pengukuran`, `test_mep`, `note_testmep`, `st_eqp`, `note_steqp`, `kwh`, `no_kwh`, `note_kwh`, `pdam`, `no_pdam`, `note_pdam`) VALUES
(1, 'MLG-001', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'n', 'on', 'n', 'n');

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

--
-- Dumping data for table `note_legal`
--

INSERT INTO `note_legal` (`id`, `kode_lahan`, `note_drawteknis`, `note_bast`, `note_mouparkir`, `note_infosampah`, `note_pkkpr`, `note_undt`, `note_nib`, `note_imb`, `note_tdup`, `note_bpbdpm`, `note_reklame`, `note_sppl`, `note_damkar`, `note_peilbanjir`, `note_andalalin`, `note_iuranwarga`) VALUES
(1, 'MLG-001', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n');

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

--
-- Dumping data for table `obs_sdg`
--

INSERT INTO `obs_sdg` (`id`, `kode_lahan`, `obstacle`, `note`, `obs_date`, `status_obssdg`, `lamp_legal`, `obslegal_date`, `status_obslegal`) VALUES
(1, 'BL-002', 'obstacle', 'segera selesaikan', '2024-06-12', 'Diajukan', '../uploads/RTO.pdf', '2024-06-12', 'In Process');

-- --------------------------------------------------------

--
-- Table structure for table `procurement`
--

CREATE TABLE `procurement` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(256) NOT NULL,
  `status_approvsdg` varchar(255) NOT NULL,
  `status_approvprocurement` varchar(255) NOT NULL,
  `lamp_desainplan` varchar(1000) NOT NULL,
  `lamp_rab` varchar(1000) NOT NULL,
  `nama_vendor` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `sla_date` date DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `lamp_vendor` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `procurement`
--

INSERT INTO `procurement` (`id`, `kode_lahan`, `status_approvsdg`, `status_approvprocurement`, `lamp_desainplan`, `lamp_rab`, `nama_vendor`, `start_date`, `end_date`, `sla_date`, `rate`, `lamp_vendor`) VALUES
(1, 'MLG-001', 'Approve', 'Approve', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'VMG_ML1', '2024-06-03', '0000-00-00', '2024-06-05', NULL, '../uploads/Data_Keluhan (1).xlsx');

-- --------------------------------------------------------

--
-- Table structure for table `re`
--

CREATE TABLE `re` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(50) NOT NULL,
  `nama_lahan` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `luas_area` varchar(255) NOT NULL,
  `lamp_land` varchar(1000) NOT NULL,
  `catatan_owner` varchar(1000) NOT NULL,
  `status_approvowner` varchar(255) NOT NULL,
  `catatan_legal` varchar(1000) NOT NULL,
  `status_approvlegal` varchar(255) NOT NULL,
  `catatan_nego` varchar(1000) DEFAULT NULL,
  `status_approvnego` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `nego_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `slanego_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `re`
--

INSERT INTO `re` (`id`, `kode_lahan`, `nama_lahan`, `lokasi`, `luas_area`, `lamp_land`, `catatan_owner`, `status_approvowner`, `catatan_legal`, `status_approvlegal`, `catatan_nego`, `status_approvnego`, `start_date`, `end_date`, `nego_date`, `sla_date`, `slalegal_date`, `slanego_date`) VALUES
(32, 'MLG-001', 'm', 'm', 'm', '../uploads/Sertifikat Bem 2021.pdf', 'mmm', 'Approve', 'mmm', 'Approve', NULL, 'Approve', '2024-05-06', '2024-05-22', '2024-05-22', '2024-05-11', '2024-05-15', NULL),
(41, 'SBY-002', 'Surabaya', 'y', 'y', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '', 'Approve', '', 'Approve', NULL, 'Approve', '2024-05-24', '2024-05-27', '2024-05-27', '2024-05-31', '2024-05-31', '2024-06-03'),
(43, 'MLG-002', 'Mlg', 'ml', 'ml', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '', 'Approve', '', 'Approve', NULL, 'Approve', '2024-05-27', '2024-05-27', '2024-05-27', '2024-05-31', '2024-06-03', '2024-06-03'),
(44, 'KDR-002', 'kediri', 'k', 'k', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '', 'Approve', '', 'Approve', NULL, 'Approve', '2024-05-27', '2024-05-27', '2024-05-27', '2024-06-03', '2024-06-03', '2024-06-03'),
(45, 'BL-002', 'Blitar', 'b', 'b', '../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '', 'Approve', '', 'Approve', NULL, 'Approve', '2024-05-27', '2024-05-27', '2024-05-27', '2024-06-03', '2024-06-03', '2024-06-03'),
(46, 'BL-003', 'blitar', 'p', 'p', '../uploads/Sertifikat Bem 2021.pdf,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', '', 'Approve', '', 'Approve', NULL, 'In Process', '2024-05-27', '2024-05-27', NULL, '2024-06-03', '2024-06-03', '2024-06-03'),
(47, 'SBY-003', 'sby 3', 's', 's', '../uploads/Sertifikat Bem 2021.pdf,../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '', 'In Process', '', '', NULL, NULL, '0000-00-00', '0000-00-00', NULL, '2024-06-03', NULL, NULL),
(48, 'KDR-003', 'kediri 3', 'ku', 'ku', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', '', 'Approve', '', 'In Process', NULL, NULL, '2024-05-27', '0000-00-00', NULL, '2024-06-03', '2024-06-03', NULL),
(49, 'SBY-001', 'sby 1', 'y', 'y', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '', 'Approve', '', 'Approve', NULL, 'Approve', '2024-05-27', '2024-05-27', '2024-05-27', '2024-06-03', '2024-06-03', '2024-06-03');

-- --------------------------------------------------------

--
-- Table structure for table `resto`
--

CREATE TABLE `resto` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(11) NOT NULL,
  `lamp_splegal` varchar(1000) NOT NULL,
  `catatan_legal` varchar(1000) NOT NULL,
  `nama_store` varchar(255) NOT NULL,
  `status_land` varchar(255) DEFAULT NULL,
  `gostore_date` date NOT NULL,
  `status_gostore` varchar(255) DEFAULT NULL,
  `submit_date` date NOT NULL,
  `status_finallegal` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `lamp_spk` varchar(1000) DEFAULT NULL,
  `sla_spk` date DEFAULT NULL,
  `spk_date` date DEFAULT NULL,
  `status_spk` varchar(255) DEFAULT NULL,
  `lamp_kom` varchar(1000) DEFAULT NULL,
  `sla_kom` date DEFAULT NULL,
  `kom_date` date DEFAULT NULL,
  `status_kom` varchar(255) DEFAULT NULL,
  `lamp_steqp` varchar(1000) DEFAULT NULL,
  `status_steqp` varchar(255) DEFAULT NULL,
  `steqp_date` date DEFAULT NULL,
  `sla_steqp` date DEFAULT NULL,
  `lamp_stkonstruksi` varchar(1000) DEFAULT NULL,
  `status_stkonstruksi` varchar(255) DEFAULT NULL,
  `stkonstruksi_date` date DEFAULT NULL,
  `sla_stkonstruksi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resto`
--

INSERT INTO `resto` (`id`, `kode_lahan`, `lamp_splegal`, `catatan_legal`, `nama_store`, `status_land`, `gostore_date`, `status_gostore`, `submit_date`, `status_finallegal`, `start_date`, `lamp_spk`, `sla_spk`, `spk_date`, `status_spk`, `lamp_kom`, `sla_kom`, `kom_date`, `status_kom`, `lamp_steqp`, `status_steqp`, `steqp_date`, `sla_steqp`, `lamp_stkonstruksi`, `status_stkonstruksi`, `stkonstruksi_date`, `sla_stkonstruksi`) VALUES
(5, 'MLG-001', '../uploads/MicrosoftTeams-image (3).png', 'catatan legal', 'Malang Sukun', 'In Process', '2024-09-11', 'In Process', '2024-05-31', 'Approve', '2024-05-31', '../uploads/MicrosoftTeams-image (5).png,MicrosoftTeams-image (4).png', '2024-06-10', '2024-06-03', 'Approve', '../uploads/MicrosoftTeams-image (3).png', '2024-06-05', '2024-06-03', 'Approve', NULL, 'In Process', NULL, '2024-08-29', NULL, 'In Process', NULL, '2024-09-02'),
(8, 'SBY-001', '', '', '', NULL, '0000-00-00', NULL, '0000-00-00', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sdg_desain`
--

CREATE TABLE `sdg_desain` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_land` varchar(1000) NOT NULL,
  `lamp_desainplan` varchar(1000) DEFAULT NULL,
  `catatan_sdgdesain` varchar(1000) NOT NULL,
  `confirm_sdgdesain` varchar(255) NOT NULL,
  `obstacle` varchar(255) NOT NULL,
  `submit_legal` varchar(1000) NOT NULL,
  `start_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_desain`
--

INSERT INTO `sdg_desain` (`id`, `kode_lahan`, `lamp_land`, `lamp_desainplan`, `catatan_sdgdesain`, `confirm_sdgdesain`, `obstacle`, `submit_legal`, `start_date`, `sla_date`, `slalegal_date`, `end_date`) VALUES
(3, 'MLG-001', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/uts dini.jpeg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'mlg sdg', 'Approve', 'No', 'Approve', NULL, NULL, NULL, NULL),
(7, 'KDR-002', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '../uploads/bbb.pdf', 'kdr 2', 'In Process', 'Yes', '', NULL, '2024-06-03', NULL, NULL),
(8, 'BL-002', '../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'bl 2', 'In Process', 'No', '', NULL, '2024-06-03', NULL, NULL),
(9, 'MLG-002', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', '../uploads/Sertifikat Bem 2021.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'mlg 2', 'Obstacle', 'Yes', 'In Process', NULL, '2024-06-03', '2024-06-04', NULL),
(10, 'SBY-002', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'sby 2', 'In Process', '', '', NULL, '2024-06-03', NULL, NULL),
(11, 'SBY-001', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '../uploads/bbb.pdf,../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg', 'design sby', 'Approve', 'No', 'Approve', '2024-05-28', '2024-06-03', NULL, '2024-05-28');

-- --------------------------------------------------------

--
-- Table structure for table `sdg_land`
--

CREATE TABLE `sdg_land` (
  `id` int(11) NOT NULL,
  `id_sdg_desain` varchar(255) NOT NULL,
  `land_survey` varchar(255) NOT NULL,
  `keterangan` varchar(1000) NOT NULL,
  `obstacle` varchar(1000) NOT NULL,
  `lamp_desainplan` varchar(1000) NOT NULL,
  `lamp_desainfix` varchar(1000) NOT NULL,
  `start_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `lamp_monitoring` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_pk`
--

INSERT INTO `sdg_pk` (`id`, `kode_lahan`, `month_1`, `month_2`, `month_3`, `date_month1`, `date_month2`, `date_month3`, `week`, `date_week`, `catatan`, `lamp_pk`, `status_consact`, `sla_consact`, `consact_date`, `lamp_consact`, `lamp_monitoring`) VALUES
(5, 'MLG-001', '1.00%', '2.00%', '%', NULL, NULL, NULL, '0.8,1,0,0,0,0,0,0,0,0,0,0', '', '', '', 'In Process', '2024-09-01', NULL, '../uploads/Data_Keluhan (8).xlsx', 'semua_data_keluhan.xlsx,Form ST EQP Samarinda - Wahid Hasyim (1).xlsx');

-- --------------------------------------------------------

--
-- Table structure for table `sdg_rab`
--

CREATE TABLE `sdg_rab` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_desainplan` varchar(1000) NOT NULL,
  `date` date NOT NULL,
  `jenis_biaya` varchar(255) NOT NULL,
  `jumlah` varchar(255) NOT NULL,
  `lamp_rab` varchar(1000) NOT NULL,
  `keterangan` varchar(5000) NOT NULL,
  `confirm_sdgqs` varchar(255) NOT NULL,
  `sla_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_rab`
--

INSERT INTO `sdg_rab` (`id`, `kode_lahan`, `lamp_desainplan`, `date`, `jenis_biaya`, `jumlah`, `lamp_rab`, `keterangan`, `confirm_sdgqs`, `sla_date`, `start_date`) VALUES
(3, 'MLG-001', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '0000-00-00', 'm', '123', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'm', 'Approve', NULL, '2024-05-29'),
(8, 'SBY-001', '../uploads/bbb.pdf,../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg', '0000-00-00', '', '', '', '', 'In Process', '2024-06-04', '0000-00-00');

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
  `lamp_rto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sign`
--

INSERT INTO `sign` (`id`, `kode_lahan`, `sdg_pm`, `note_sdgpm`, `legal_alo`, `note_legalalo`, `scm`, `note_scm`, `it_hrga_marketing`, `note_ihm`, `ops_rm`, `note_opsrm`, `sdg_head`, `note_sdghead`, `lamp_rto`) VALUES
(1, 'MLG-001', 'on', 'nn', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', '../uploads/Data_Keluhan (7).xlsx');

-- --------------------------------------------------------

--
-- Table structure for table `socdate_sdg`
--

CREATE TABLE `socdate_sdg` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `cons_date` date NOT NULL,
  `debitair_date` date NOT NULL,
  `kualitasair_date` date NOT NULL,
  `listrik_date` date NOT NULL,
  `ipal_date` date NOT NULL,
  `cons` varchar(255) NOT NULL,
  `debitair` varchar(255) NOT NULL,
  `kualitasair` varchar(255) NOT NULL,
  `listrikkwh` varchar(255) NOT NULL,
  `saluranipal` varchar(255) NOT NULL,
  `pylosign_date` date NOT NULL,
  `pylonsign` varchar(255) NOT NULL,
  `brankasoligen_date` date NOT NULL,
  `brankasoligen` varchar(255) NOT NULL,
  `cschiller_date` date NOT NULL,
  `cschiller` varchar(255) NOT NULL,
  `steqp_date` date NOT NULL,
  `steqp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `soc_fat`
--

INSERT INTO `soc_fat` (`id`, `kode_lahan`, `qris`, `note_qris`, `edc`, `note_edc`) VALUES
(1, 'MLG-001', '100', 'n', '100', 'n');

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

--
-- Dumping data for table `soc_hrga`
--

INSERT INTO `soc_hrga` (`id`, `kode_lahan`, `security`, `note_security`, `cs`, `note_cs`) VALUES
(1, 'MLG-001', '100', 'n', '100', 'n');

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

--
-- Dumping data for table `soc_it`
--

INSERT INTO `soc_it` (`id`, `kode_lahan`, `cctv`, `note_cctv`, `audio_system`, `note_as`, `lan_infra`, `note_lan`, `internet_km`, `note_interkm`, `internet_cust`, `note_intercut`) VALUES
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n');

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

--
-- Dumping data for table `soc_legal`
--

INSERT INTO `soc_legal` (`id`, `kode_lahan`, `perijinan`, `note_p`, `sampah_parkir`, `note_sp`, `akses_jkm`, `note_ajkm`, `pkl`, `note_pkl`) VALUES
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n', '100', 'n');

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

--
-- Dumping data for table `soc_marketing`
--

INSERT INTO `soc_marketing` (`id`, `kode_lahan`, `post_content`, `note_pc`, `ojol`, `note_ojol`, `tikor_maps`, `note_tm`) VALUES
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n');

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

--
-- Dumping data for table `soc_rto`
--

INSERT INTO `soc_rto` (`id`, `kode_lahan`, `nama_store`, `rto_date`, `pengaju_rto`, `status_op`, `status_rto`) VALUES
(1, 'MLG-001', '', '2024-06-07', 'n', 'n', 'In Process');

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

--
-- Dumping data for table `soc_sdg`
--

INSERT INTO `soc_sdg` (`id`, `kode_lahan`, `bangunan_mural`, `note_bm`, `daya_listrik`, `note_dl`, `supply_air`, `note_sa`, `aliran_air`, `note_aa`, `kualitas_keramik`, `note_kk`, `paving_loading`, `note_pl`) VALUES
(1, 'MLG-001', '80', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n');

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
(1, 'MLG-001', '2024-06-15', '2024-06-12', 'double', '24 Jam', '100.000.000', '30', '2024-06-12', '', '                                            98.335%', '', 'On Schedule', '');

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
  `lamp_vendor` varchar(1000) NOT NULL,
  `status_lokasi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id`, `kode_vendor`, `city`, `nama`, `alamat`, `nohp`, `rate`, `detail`, `lamp_vendor`, `status_lokasi`) VALUES
(3, 'VMG_ML1', 'MALANG', 'b', 'b', 'b', '', 'b', 'uploads/Data_Keluhan (1).xlsx', 'Aktif'),
(4, 'VMG_ML2', 'MALANG', 'm', 'm', 'mm', '', 'm', '../uploads/Data_Keluhan (4).xlsx', 'On Planning'),
(5, 'VMG_ML3', 'MALANG', 'k', 'k', 'k', '', 'k', '../uploads/Data_Keluhan (2).xlsx', 'On Planning');

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
-- Indexes for table `land`
--
ALTER TABLE `land`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `sdg_land`
--
ALTER TABLE `sdg_land`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokumen_loacd`
--
ALTER TABLE `dokumen_loacd`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `draft`
--
ALTER TABLE `draft`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `land`
--
ALTER TABLE `land`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `master_sla`
--
ALTER TABLE `master_sla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `master_slacons`
--
ALTER TABLE `master_slacons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `note_ba`
--
ALTER TABLE `note_ba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `note_legal`
--
ALTER TABLE `note_legal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `obs_sdg`
--
ALTER TABLE `obs_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `procurement`
--
ALTER TABLE `procurement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `re`
--
ALTER TABLE `re`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `resto`
--
ALTER TABLE `resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sdg_desain`
--
ALTER TABLE `sdg_desain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sdg_land`
--
ALTER TABLE `sdg_land`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sdg_pk`
--
ALTER TABLE `sdg_pk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sdg_rab`
--
ALTER TABLE `sdg_rab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sign`
--
ALTER TABLE `sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `socdate_sdg`
--
ALTER TABLE `socdate_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soc_fat`
--
ALTER TABLE `soc_fat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `soc_hrga`
--
ALTER TABLE `soc_hrga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `soc_it`
--
ALTER TABLE `soc_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `soc_legal`
--
ALTER TABLE `soc_legal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `soc_marketing`
--
ALTER TABLE `soc_marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `soc_rto`
--
ALTER TABLE `soc_rto`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `soc_sdg`
--
ALTER TABLE `soc_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `summary_soc`
--
ALTER TABLE `summary_soc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
