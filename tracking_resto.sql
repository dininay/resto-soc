-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2024 at 09:49 AM
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

--
-- Dumping data for table `doc_legal`
--

INSERT INTO `doc_legal` (`id`, `kode_lahan`, `draw_teknis`, `ba_serahterima`, `mou_parkir`, `info_sampah`, `pkkpr`, `und_tasyakuran`, `nib`, `imb`, `tdup`, `bpbdpm`, `reklame`, `sppl`, `damkar`, `peil_banjir`, `andalalin`, `iuran_warga`) VALUES
(1, 'MLG-001', 'on', 'on', 'on', 'off', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'on'),
(2, 'KDR-003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'MLG-002', 'on', 'on', 'on', 'on', 'on', NULL, 'on', 'on', 'on', 'on', NULL, 'on', 'on', 'on', 'on', 'on');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_loacd`
--

CREATE TABLE `dokumen_loacd` (
  `id` int(10) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `tgl_berlaku` date DEFAULT NULL,
  `masa_berlaku` varchar(255) DEFAULT NULL,
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
  `slavd_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_loacd`
--

INSERT INTO `dokumen_loacd` (`id`, `kode_lahan`, `tgl_berlaku`, `masa_berlaku`, `status_approvloacd`, `status_approvlegalvd`, `lamp_loacd`, `lamp_vd`, `catatan_vd`, `kode_store`, `catatan`, `start_date`, `slaloa_date`, `end_date`, `slavd_date`) VALUES
(11, 'MLG-001', '2024-05-22', NULL, 'Approve', 'Approve', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', NULL, NULL, 'MLGSKN', 'ppp', NULL, NULL, NULL, NULL),
(16, 'KDR-002', '2024-05-28', NULL, 'Approve', 'Approve', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', NULL, NULL, 'KDRRAYA', 'sss', '2024-05-27', '2024-06-03', '2024-05-27', '2024-06-03'),
(17, 'BL-002', '2024-05-29', NULL, 'Approve', 'Approve', 'Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'Data_Keluhan (7) (1).xlsx', NULL, 'BLTRWLNGI', 'loa bl', '2024-05-27', '2024-06-03', '2024-06-23', '2024-06-03'),
(18, 'MLG-002', '2024-05-30', NULL, 'Approve', 'Approve', 'Sertifikat Bem 2021.pdf', NULL, NULL, 'MLGDNYO', 'ddd', '2024-05-27', '2024-06-03', '2024-06-14', '2024-06-03'),
(19, 'SBY-002', NULL, '36', 'Approve', 'Approve', 'semua_data_keluhan.xlsx', NULL, NULL, 'SBYTARA', 'catatan re', '2024-06-23', '2024-06-03', '2024-06-23', '2024-06-27'),
(20, 'SBY-001', '2024-05-30', NULL, 'Approve', 'Approve', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,WhatsApp Image 2024-04-23 at 12.10.56.jpeg', NULL, NULL, 'SBYALN', 'sby 1', '2024-05-27', '2024-06-03', '2024-05-27', '2024-06-03'),
(21, 'BDG-001', NULL, '24', 'Approve', 'Approve', 'Data_Keluhan (7).xlsx,Data_Keluhan (6).xlsx', 'Data_Keluhan (8).xlsx,Data_Keluhan (6).xlsx', NULL, 'BDGBTLIS', 'loa cd', '2024-06-18', '2024-07-09', '2024-06-23', '2024-06-22'),
(22, 'BL-003', NULL, '24', 'Approve', 'Approve', 'Data_Keluhan (6).xlsx', 'Data_Keluhan (7) (2).xlsx', NULL, 'BLTARSPRTMAN', 'ini catatan', '2024-06-23', '2024-07-14', '2024-06-26', '2024-06-27'),
(23, 'KDR-003', NULL, '24', 'Approve', 'Approve', 'MicrosoftTeams-image (7).png', 'MicrosoftTeams-image (7).png', NULL, 'KDRHATTA', 'catatan loa cd', '2024-06-25', '2024-07-14', '2024-06-25', '2024-06-29'),
(24, 'KDR-004', NULL, '24', 'Approve', 'In Process', 'MicrosoftTeams-image (7).png', NULL, NULL, NULL, 'test coba', '2024-07-08', '2024-07-17', NULL, '2024-07-12'),
(25, 'JKT1', NULL, '36', 'Approve', 'Approve', 'image.png', 'Data_Keluhan (7) (2) (1).xlsx', NULL, 'JKTSLTN', 'ppp', '2024-06-26', '2024-07-17', '2024-06-26', '2024-06-30'),
(26, 'BL-001', NULL, '36', 'Approve', 'Approve', '../uploads/Form ST EQP Samarinda - Wahid Hasyim (1).xlsx', '../uploads/Data_Keluhan (6).xlsx', NULL, 'BLTRKOTA', 'ppp', '2024-06-28', '2024-07-19', '2024-07-08', '2024-07-02'),
(27, 'CI-001', NULL, '24', 'Approve', 'In Process', 'WhatsApp Image 2024-07-11 at 16.42.08.jpeg', NULL, NULL, NULL, 'fvgbkj', '2024-07-14', '2024-07-29', NULL, '2024-07-18'),
(29, 'SBY-003', NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-29', NULL, NULL),
(30, 'KRWG-001', NULL, '36', 'Approve', 'Approve', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg,Data_Keluhan (5) (1) (1) (1).xlsx', NULL, 'KRWGSLTN', 'hhh', '2024-07-14', '2024-08-04', '2024-07-14', '2024-07-18');

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
  `valdoc_legal` varchar(1000) DEFAULT NULL,
  `catatan_valdoc` varchar(255) DEFAULT NULL,
  `draft_legal` varchar(255) DEFAULT NULL,
  `confirm_nego` varchar(255) DEFAULT NULL,
  `confirm_fat` varchar(255) DEFAULT NULL,
  `catatan_psmfat` varchar(1000) DEFAULT NULL,
  `psmfat_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `slalegal_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `slavd_date` date DEFAULT NULL,
  `fat_date` date DEFAULT NULL,
  `slafat_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `draft`
--

INSERT INTO `draft` (`id`, `kode_lahan`, `lamp_draf`, `jadwal_psm`, `catatan_legal`, `catatan_draft`, `lamp_signpsm`, `valdoc_legal`, `catatan_valdoc`, `draft_legal`, `confirm_nego`, `confirm_fat`, `catatan_psmfat`, `psmfat_date`, `start_date`, `sla_date`, `slalegal_date`, `end_date`, `slavd_date`, `fat_date`, `slafat_date`) VALUES
(7, 'MLG-001', 'Sertifikat Bem 2021.pdf,Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '2024-05-22', '', '0', NULL, 'Approve', NULL, 'Approve', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', NULL, NULL, NULL),
(11, 'KDR-002', 'RTO.pdf', '2024-06-17', 'legal', '0', 'Data_Keluhan (3).xlsx,Data_Keluhan (2).xlsx', 'Approve', NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-14', '2024-06-28', '2024-06-03', NULL, NULL, NULL, NULL),
(12, 'SBY-001', 'bbb.pdf,Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', '2024-05-30', 'draft sby', '0', NULL, 'Approve', NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-05-31', '2024-06-03', '2024-06-03', '2024-05-31', NULL, NULL, NULL),
(13, 'MLG-002', 'Data_Keluhan (6).xlsx,semua_data_keluhan.xlsx', '2024-06-26', 'ppp', '0', 'semua_data_keluhan.xlsx', 'Approve', 'dfcgvh', 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-23', NULL, '2024-06-23', '2024-06-23', NULL, NULL, NULL),
(14, 'BDG-001', 'Data_Keluhan (7).xlsx,Data_Keluhan (8).xlsx', '2024-06-29', 'updt', '0', 'Data_Keluhan (7).xlsx', 'Approve', NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-23', '2024-07-02', '2024-06-27', '2024-06-23', NULL, NULL, NULL),
(20, 'BL-002', 'Data_Keluhan (8).xlsx', '2024-07-01', 'hhh', '0', 'SLA Resto.png', 'Approve', NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-23', NULL, '2024-07-02', '2024-06-26', NULL, NULL, NULL),
(21, 'SBY-002', 'Data_Keluhan (6).xlsx', '2024-07-22', 'note sraft', '0', 'MicrosoftTeams-image (3).png,MicrosoftTeams-image (2).png', NULL, NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-07-08', NULL, '2024-07-02', '2024-07-08', NULL, NULL, NULL),
(22, 'KDR-003', 'Data_Keluhan (2) (1).xlsx', '2024-06-30', 'test', '0', 'Data_Keluhan (5) (1).xlsx', 'Approve', 'catatan legal', 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-25', NULL, '2024-07-04', '2024-06-25', NULL, NULL, NULL),
(23, 'BL-003', 'WhatsApp Image 2024-06-26 at 08.18.59.jpeg', '2024-06-29', 'catatan', '0', NULL, NULL, NULL, 'Approve', 'In Process', NULL, NULL, NULL, '2024-06-26', NULL, '2024-07-05', NULL, NULL, NULL, NULL),
(24, 'JKT1', 'Data_Keluhan (5) (1).xlsx', '2024-06-30', 'hjkl', '0', 'MicrosoftTeams-image (6).png', 'Approve', 'dfcgvh', 'Approve', 'Approve', NULL, NULL, NULL, '2024-06-26', NULL, '2024-07-05', '2024-06-26', NULL, NULL, NULL),
(25, 'BL-001', NULL, NULL, '', '0', NULL, NULL, NULL, 'In Process', 'In Process', NULL, NULL, NULL, NULL, NULL, '2024-07-17', NULL, NULL, NULL, NULL),
(26, 'KRWG-001', 'Data_Keluhan (5) (1).xlsx,Data_Keluhan (2) (1).xlsx', '2024-07-28', 'lkddcjksw', '0', 'WhatsApp Image 2024-07-11 at 16.42.08.jpeg', NULL, NULL, 'Approve', 'Approve', NULL, NULL, NULL, '2024-07-14', NULL, '2024-07-23', '2024-07-14', NULL, NULL, NULL);

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

--
-- Dumping data for table `hold_project`
--

INSERT INTO `hold_project` (`id`, `kode_lahan`, `project_name`, `issue_detail`, `kronologi`, `pic`, `action_plan`, `update`, `due_date`, `status_hold`) VALUES
(41, 'BL-004', NULL, 'BoD test', 'Data_Keluhan (2) (1) (1).xlsx', 'pp', 'p', NULL, '2024-07-05', 'Done'),
(50, 'BL-004', NULL, 'VL test', NULL, 'p', 'p', NULL, '2024-07-05', 'Done'),
(57, 'BL-004', NULL, 'Val legal test', 'Data_Keluhan (5) (1) (1) (1).xlsx', 'm', 'm', NULL, '2024-07-08', 'Done'),
(58, 'BL-001', NULL, 'test vd', 'Data_Keluhan (7) (2).xlsx', 'v', 'v', NULL, '2024-07-08', 'Done'),
(63, 'SBY-002', NULL, 'test draft', 'Data_Keluhan (8).xlsx', 'd', 'd', NULL, '2024-07-08', 'Done'),
(64, 'SBY-002', NULL, 'test psm', 'Data_Keluhan (1) (2).xlsx,Data_Keluhan (5).xlsx', 'p', 'p', NULL, '2024-07-08', 'Done'),
(65, 'BL-001', NULL, 'w', 'Data_Keluhan (7) (1).xlsx,Data_Keluhan (7).xlsx,Data_Keluhan (8).xlsx', 'w', 'w', NULL, '2024-07-08', 'Done'),
(66, 'JKT1', NULL, 'test permit', 'Data_Keluhan (7).xlsx', 'p', 'p', NULL, '2024-07-08', 'Done'),
(67, 'KDR-004', NULL, 'test loacd', NULL, 'c', 'c', NULL, '2024-07-08', 'Done'),
(68, 'CI-001', NULL, 'test nego', NULL, 'n', 'n', NULL, '2024-07-08', 'Done'),
(69, 'KDR-004', NULL, 'test land survey', NULL, 'sdg', 's', NULL, '2024-07-08', 'In Process'),
(70, 'BL-003', NULL, 'test layouting', 'Data_Keluhan (5) (1) (1).xlsx,Data_Keluhan (7) (2) (1).xlsx', 'l', 'l', NULL, '2024-07-09', 'Done'),
(71, 'SBY-002', NULL, 'test design', NULL, 'n', 'n', NULL, '2024-07-09', 'Done'),
(72, 'SBY-002', NULL, 'test sdg qs', NULL, 'q', 'q', NULL, '2024-07-09', 'Done'),
(73, 'JKT1', NULL, 'test procurement', 'Data_Keluhan (5) (1) (1) (1).xlsx', 'p', 'p', NULL, '2024-07-09', 'Done'),
(74, 'SBY-002', NULL, 'test spk', 'Data_Keluhan (2) (1) (1).xlsx', 'k', 'k', NULL, '2024-07-09', 'Done'),
(75, 'SBY-002', NULL, 'test spk fat', 'Data_Keluhan (2) (1) (1).xlsx', 'f', 'f', NULL, '2024-07-09', 'Done'),
(106, 'BDG-001', NULL, 'test pending bdg 1 with all divisi insert', NULL, 'n', 'n', NULL, '2024-07-10', 'Done'),
(107, 'BL-002', NULL, 'test legal izin kons', 'Data_Keluhan (2) (1) (1).xlsx', 'n', 'n', NULL, '2024-07-10', 'Done'),
(108, 'BDG-001', NULL, 'test steqp', 'Data_Keluhan (7).xlsx', 'p', 'p', NULL, '2024-07-10', 'Done'),
(109, 'MLG-001', NULL, 'test st kontraktor', 'MicrosoftTeams-image (7).png', 'k', 'k', NULL, '2024-07-10', 'Done'),
(110, 'BL-004', NULL, 'test nego', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'n', 'n', NULL, '2024-07-12', 'In Process');

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
  `lamp_monitoring` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konstruksi`
--

INSERT INTO `konstruksi` (`id`, `kode_lahan`, `week_1`, `week_2`, `week_3`, `week_4`, `week_5`, `week_6`, `week_7`, `week_8`, `week_9`, `week_10`, `week_11`, `week_12`, `lamp_monitoring`) VALUES
(1, 'MLG-001', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'MicrosoftTeams-image (7).png'),
(2, 'MLG-002', '3', '5', '2.5', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'WhatsApp Image 2024-06-26 at 08.18.59.jpeg,image.png,Data_Keluhan (2) (1).xlsx'),
(3, 'SBY-001', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(4, 'KDR-003', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(5, 'BDG-001', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(6, 'BL-002', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(7, 'KRWG-001', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(8, 'KRWG-001', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(9, 'KDR-002', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(10, 'KRWG-001', '', '', '', '', '', '', '', '', '', '', '', '', '');

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
  `longitude` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land`
--

INSERT INTO `land` (`id`, `city`, `kode_lahan`, `nama_lahan`, `status_land`, `bp_date`, `lokasi`, `nama_pemilik`, `alamat_pemilik`, `no_tlp`, `luas_area`, `lamp_land`, `status_approvre`, `status_date`, `sla`, `re_date`, `maps`, `latitude`, `longitude`) VALUES
(12, 'MALANG', 'MLG-001', 'Malang Karlos', 'Aktif', NULL, 'm', 'm', 'm', 'm', 'm', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/uts dini.jpeg', 'Approve', '2024-05-21', NULL, '2024-07-14', NULL, NULL, NULL),
(13, 'BLITAR', 'BL-001', 'b', 'Aktif', NULL, 'b', 'b', 'b', 'b', 'b', '../uploads/UTS SISTEM OPERASI.jpeg', 'Approve', '2024-06-28', '2024-07-05', '2024-07-14', NULL, NULL, NULL),
(14, 'KEDIRI', 'KDR-001', 'o', 'Aktif', NULL, 'o', 'o', 'o', 'o', 'o', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'Approve', '2024-06-28', '2024-07-05', '2024-07-14', NULL, NULL, NULL),
(15, 'SURABAYA', 'SBY-002', 'Surabaya', 'Aktif', NULL, 'y', 'y', 'y', 'y', 'y', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'Approve', '2024-05-23', NULL, '2024-07-14', NULL, NULL, NULL),
(16, 'BLITAR', 'BL-002', 'Blitar', 'Aktif', NULL, 'b', 'b', 'b', 'b', 'b', '../uploads/WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(17, 'MALANG', 'MLG-002', 'Mlg', 'Aktif', NULL, 'ml', 'ml', 'ml', 'ml', 'ml', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.58.jpeg', 'Approve', '2024-05-24', '2024-05-31', '2024-07-14', NULL, NULL, NULL),
(18, 'KEDIRI', 'KDR-002', 'kediri', 'Aktif', NULL, 'k', 'k', 'k', 'k', 'k', '../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'Approve', '2024-05-24', '2024-05-31', '2024-07-14', NULL, NULL, NULL),
(19, 'BLITAR', 'BL-003', 'blitar', 'Aktif', NULL, 'p', 'p', 'p', 'p', 'p', '../uploads/Sertifikat Bem 2021.pdf,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(20, 'KEDIRI', 'KDR-003', 'kediri 3', '', NULL, 'ku', 'ku', 'ku', 'ku', 'ku', '../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,../uploads/WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(21, 'SURABAYA', 'SBY-003', 'sby 3', 'Aktif', NULL, 's', 's', 's', 's', 's', '../uploads/Sertifikat Bem 2021.pdf,../uploads/Sertifikat Panitia PKKMB 2021 Dini Naylul Izzah.pdf', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(22, 'MALANG', 'MLG-003', 'mlg 3', 'Aktif', NULL, '', '', '', '', '', '../uploads/WhatsApp Image 2024-06-25 at 15.07.19.jpeg', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(23, 'SURABAYA', 'SBY-004', 'sby 4', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Approve', NULL, NULL, '2024-07-14', NULL, NULL, NULL),
(24, 'MALANG', 'MLG-004', 'mlg 4', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Approve', NULL, NULL, '2024-07-14', NULL, NULL, NULL),
(25, 'BLITAR', 'BL-004', 'BL 4', 'Aktif', NULL, 'blitar', 'blitar', 'blitar', 'blitar', 'blitar', 'WhatsApp Image 2024-06-26 at 08.18.59.jpeg', 'Approve', '2024-06-26', '2024-07-03', '2024-07-14', NULL, NULL, NULL),
(27, 'KEDIRI', 'KDR-004', 'l', 'Aktif', NULL, 'l', 'l', 'l', 'l', 'l', 'Data_Keluhan (7) (2).xlsx', 'Approve', '2024-06-25', '2024-07-02', '2024-07-14', NULL, NULL, NULL),
(28, 'SURABAYA', 'SBY-001', 'Surabaya Diponegoro', 'Aktif', NULL, 'y', 'y', 'y', 'y', 'y', '../uploads/bbb.pdf,../uploads/Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'Approve', '2024-05-27', '2024-06-03', '2024-07-14', NULL, NULL, NULL),
(29, 'BANDUNG', 'BDG-001', 'Bandung Raya', 'Aktif', NULL, 'b', 'b', 'b', 'b', 'b', '../uploads/tracking_resto (1).sql', 'Approve', '2024-06-18', '2024-06-25', '2024-07-14', NULL, NULL, NULL),
(30, 'CIAMIS', 'CI-001', 'ciamis raya', 'Aktif', NULL, 'c', 'c', 'c', 'c', 'c', '../uploads/MicrosoftTeams-image (7).png', 'Approve', '2024-06-23', '2024-06-30', '2024-07-14', NULL, NULL, NULL),
(31, 'DEPOK', 'DPK-001', 'Depok 1', 'Aktif', NULL, 'd', 'd', 'd', 'd', 'd', 'Data_Keluhan (5) (1).xlsx', 'Approve', '2024-06-25', '2024-07-02', '2024-07-14', NULL, NULL, NULL),
(36, 'JAKARTA', 'JKT1', 'Jakarta Test', 'Aktif', NULL, 'Jakarta Pusat', 'test', 'test', 'test', 'test', 'MicrosoftTeams-image (7).png,Data_Keluhan (7) (1).xlsx', 'Approve', '2024-06-25', '2024-07-02', '2024-07-14', NULL, NULL, NULL),
(37, 'TASIKMALAYA', 'Tasik1', 'tasik 1', 'On Planning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Approve', NULL, NULL, '2024-07-14', NULL, NULL, NULL),
(38, 'KARAWANG', 'KRWG-001', 'Karawang 1', 'Aktif', NULL, 'Karawang New', 'K', 'k', '0', '0', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'Approve', '2024-07-14', '2024-07-21', '2024-07-14', 'k', 'k', 'k'),
(39, 'NGAWI', 'NGWI-001', 'Ngawi Tol', 'Aktif', '2024-07-12', 'Jl. Tol Ngawi Solo', 's', 's', 's', 's', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'In Process', '2024-07-14', '2024-07-21', '2024-07-14', 's', 's', 's');

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
(11, 'ST-EQP', '87'),
(12, 'ST-Konstruksi', '91'),
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
(25, 'SPK-FAT', '4'),
(26, 'Land Survey', '5'),
(27, 'Layouting', '5'),
(28, 'Layouting', '5');

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
(1, 'rto', '3'),
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
(19, 'kpt3', '25');

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

--
-- Dumping data for table `mom`
--

INSERT INTO `mom` (`id`, `notes`, `date`, `updated_by`, `status`, `file`) VALUES
(1, 'notes', '2024-07-12', 'PMO', 'Urgent', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg');

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
(1, 'MLG-001', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'n', 'on', 'n', 'n'),
(2, 'KDR-003', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '142563', '', 'on', '2473', ''),
(3, 'MLG-002', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '142563', '', 'on', '2473', '');

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
(1, 'MLG-001', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n'),
(2, 'KDR-003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'MLG-002', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

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
(1, 'BL-002', 'obstacle', 'segera selesaikan', '2024-06-12', 'Diajukan', 'RTO.pdf', '2024-06-12', 'In Process'),
(3, 'SBY-002', 'obstacle pohon', 'segera', '2024-06-19', 'Diajukan', '', '0000-00-00', ''),
(4, 'KDR-003', 'obstacle pohon', 'catatan', '2024-06-25', 'Diajukan', 'Data_Keluhan (2) (1).xlsx', '2024-06-26', 'In Process');

-- --------------------------------------------------------

--
-- Table structure for table `procurement`
--

CREATE TABLE `procurement` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(256) NOT NULL,
  `status_approvsdg` varchar(255) NOT NULL,
  `status_approvprocurement` varchar(255) NOT NULL,
  `catatan_proc` varchar(255) DEFAULT NULL,
  `nama_vendor` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `sla_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `procurement`
--

INSERT INTO `procurement` (`id`, `kode_lahan`, `status_approvsdg`, `status_approvprocurement`, `catatan_proc`, `nama_vendor`, `start_date`, `end_date`, `sla_date`) VALUES
(1, 'MLG-001', 'Approve', 'Approve', NULL, 'VMG_ML1', '2024-06-03', '0000-00-00', '2024-06-05'),
(2, 'SBY-001', 'Approve', 'Approve', NULL, 'BAT001', '2024-06-19', '0000-00-00', '2024-06-28'),
(3, 'KDR-002', 'Approve', 'Approve', 'ikm', 'VMG_ML3', '2024-06-23', '0000-00-00', '2024-07-03'),
(4, 'BL-002', 'Approve', 'Approve', 'catatan', 'VMG_ML2', '2024-06-26', '0000-00-00', '2024-07-07'),
(5, 'BDG-001', 'Approve', 'Approve', 'rtyu', 'CIR001', '2024-06-23', '0000-00-00', '2024-07-07'),
(6, 'MLG-002', 'Approve', 'Approve', 'ecf', 'CIK001', '2024-06-23', '0000-00-00', '2024-07-07'),
(7, 'KDR-003', 'Approve', 'Approve', 'catatan', 'BOG001', '2024-06-25', '0000-00-00', '2024-07-09'),
(8, 'BL-003', 'Approve', 'In Process', NULL, NULL, '0000-00-00', '0000-00-00', '2024-07-09'),
(9, 'JKT1', 'Approve', 'Approve', 'catatan', 'JAK001', '2024-07-09', '0000-00-00', '2024-07-10'),
(10, 'BL-001', 'Approve', 'In Process', NULL, NULL, '0000-00-00', '0000-00-00', '2024-07-12'),
(11, 'SBY-002', 'Approve', 'In Process', NULL, NULL, '0000-00-00', '0000-00-00', '2024-07-23'),
(12, 'KRWG-001', 'Approve', 'Approve', 'ikm', 'BAT001', '2024-07-14', '0000-00-00', '2024-07-28');

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
  `slavl_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `re`
--

INSERT INTO `re` (`id`, `kode_lahan`, `catatan_owner`, `status_approvowner`, `lamp_vl`, `catatan_legal`, `status_approvlegal`, `catatan_nego`, `status_approvnego`, `status_vl`, `catatan_vl`, `start_date`, `end_date`, `nego_date`, `vl_date`, `sla_date`, `slalegal_date`, `slanego_date`, `slavl_date`) VALUES
(32, 'MLG-001', 'mmm', 'Approve', NULL, 'mmm', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-06', '2024-05-22', '2024-05-22', '2024-07-05', '2024-05-11', '2024-05-15', NULL, NULL),
(41, 'SBY-002', '', 'Approve', NULL, '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-24', '2024-05-27', '2024-05-27', '2024-07-05', '2024-05-31', '2024-05-31', '2024-06-03', NULL),
(43, 'MLG-002', '', 'Approve', NULL, '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-05-27', '2024-05-27', '2024-07-05', '2024-05-31', '2024-06-03', '2024-06-03', NULL),
(44, 'KDR-002', '', 'Approve', NULL, '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-05-27', '2024-05-27', '2024-07-05', '2024-06-03', '2024-06-03', '2024-06-03', NULL),
(45, 'BL-002', '', 'Approve', NULL, '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-05-27', '2024-05-27', '2024-07-05', '2024-06-03', '2024-06-03', '2024-06-03', NULL),
(46, 'BL-003', '', 'Approve', 'Data_Keluhan (8).xlsx', '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-05-27', '2024-06-23', '2024-07-05', '2024-06-03', '2024-06-03', '2024-06-03', NULL),
(47, 'SBY-003', '', 'Approve', NULL, '', 'Approve', '', 'Approve', 'Approve', NULL, '2024-06-23', '2024-07-08', '2024-07-08', '2024-07-05', '2024-06-03', '2024-07-02', NULL, NULL),
(48, 'KDR-003', '', 'Approve', 'Data_Keluhan (7) (1).xlsx', 'catatan legal', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-06-23', '2024-06-23', '2024-07-05', '2024-06-03', '2024-06-03', NULL, '2024-07-02'),
(49, 'SBY-001', '', 'Approve', NULL, '', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-05-27', '2024-05-27', '2024-05-27', '2024-07-05', '2024-06-03', '2024-06-03', '2024-06-03', NULL),
(50, 'BDG-001', '', 'Approve', 'Data_Keluhan (8).xlsx', 'wovl', 'Approve', NULL, 'Approve', 'Approve', NULL, '2024-06-18', '2024-06-18', '2024-06-18', '2024-07-05', '2024-06-25', '2024-06-27', NULL, '2024-06-27'),
(51, 'MLG-003', '', 'Approve', NULL, '', 'In Process', NULL, NULL, 'Approve', NULL, '2024-06-23', '0000-00-00', NULL, '2024-07-05', '2024-06-30', '2024-07-02', NULL, NULL),
(56, 'CI-001', '', 'Approve', 'Data_Keluhan (7).xlsx', 'lanjut', 'Approve', 'kamdls', 'Approve', 'Approve', NULL, '2024-06-23', '2024-06-23', '2024-07-08', '2024-07-05', '2024-06-30', '2024-07-02', NULL, '2024-07-02'),
(57, 'DPK-001', 'test', 'Approve', 'Data_Keluhan (7) (2).xlsx', 'test', 'Approve', NULL, 'In Process', 'Approve', NULL, '2024-06-25', '2024-06-25', NULL, '2024-07-05', '2024-07-02', '2024-07-04', NULL, '2024-07-04'),
(59, 'JKT1', 'test owner', 'Approve', 'MicrosoftTeams-image (7).png', 'catatan legal', 'Approve', 'catatan negosiator', 'Approve', 'Approve', NULL, '2024-06-25', '2024-06-25', '2024-06-26', '2024-07-05', '2024-07-02', '2024-07-04', NULL, '2024-07-04'),
(60, 'BL-004', 'n', 'Approve', NULL, 'm', 'Approve', 'c', 'Pending', 'Approve', NULL, '2024-07-05', '2024-07-08', NULL, '2024-07-05', '2024-07-10', '2024-07-12', NULL, '2024-07-05'),
(61, 'KDR-004', 'test owner', 'Approve', 'image.png', 'catatan legal', 'Approve', 'catatan negosiator', 'Approve', 'Approve', NULL, '2024-06-26', '2024-06-26', '2024-06-26', '2024-07-05', '2024-07-10', '2024-07-03', NULL, '2024-07-05'),
(62, 'BL-001', 'abc', 'Approve', 'Data_Keluhan (5).xlsx', 'catatan legal', 'Approve', 'catatan negosiator', 'Approve', 'Approve', NULL, '2024-06-28', '2024-06-28', '2024-06-28', '2024-07-05', '2024-07-05', '2024-07-05', NULL, '2024-07-07'),
(71, 'KRWG-001', '', 'Approve', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', '', '', 'kkk', 'Approve', 'Approve', NULL, '2024-07-14', '0000-00-00', '2024-07-14', '2024-07-14', '2024-07-21', NULL, '2024-07-21', '2024-07-21');

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
  `status_gostore` varchar(255) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `lamp_spk` varchar(1000) DEFAULT NULL,
  `sla_spk` date DEFAULT NULL,
  `spk_date` date DEFAULT NULL,
  `status_spk` varchar(255) DEFAULT NULL,
  `status_fat` varchar(1000) DEFAULT NULL,
  `sla_fat` date DEFAULT NULL,
  `fat_date` date DEFAULT NULL,
  `status_legalizin` varchar(1000) DEFAULT NULL,
  `legalizin_date` date DEFAULT NULL,
  `lamp_legalizin` varchar(1000) DEFAULT NULL,
  `lamp_kom` varchar(1000) DEFAULT NULL,
  `sla_kom` date DEFAULT NULL,
  `kom_date` date DEFAULT NULL,
  `status_kom` varchar(255) DEFAULT NULL,
  `start_konstruksi` date DEFAULT NULL,
  `end_konstruksi` date DEFAULT NULL,
  `lamp_steqp` varchar(1000) DEFAULT NULL,
  `lamp_basteqp` varchar(1000) DEFAULT NULL,
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

INSERT INTO `resto` (`id`, `kode_lahan`, `lamp_splegal`, `catatan_legal`, `nama_store`, `status_land`, `gostore_date`, `status_gostore`, `approved_by`, `submit_date`, `start_date`, `lamp_spk`, `sla_spk`, `spk_date`, `status_spk`, `status_fat`, `sla_fat`, `fat_date`, `status_legalizin`, `legalizin_date`, `lamp_legalizin`, `lamp_kom`, `sla_kom`, `kom_date`, `status_kom`, `start_konstruksi`, `end_konstruksi`, `lamp_steqp`, `lamp_basteqp`, `status_steqp`, `steqp_date`, `sla_steqp`, `lamp_stkonstruksi`, `status_stkonstruksi`, `stkonstruksi_date`, `sla_stkonstruksi`) VALUES
(5, 'MLG-001', 'MicrosoftTeams-image (3).png', 'catatan legal', 'Malang Sukun', 'In Process', '2024-09-11', 'Approve', NULL, '2024-05-31', '2024-05-31', 'MicrosoftTeams-image (5).png,MicrosoftTeams-image (4).png', '2024-06-10', '2024-06-03', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, '', '2024-06-05', '2024-06-21', 'Approve', NULL, NULL, '', '', 'In Process', '2024-06-22', '2024-09-19', 'Data_Keluhan (5).xlsx', 'Approve', '2024-07-10', '2024-09-23'),
(8, 'SBY-001', 'Data_Keluhan (7) (1).xlsx', 'lanjut', '', 'Approve', '2024-10-08', 'In Process', NULL, '2024-06-19', '2024-06-14', 'Data_Keluhan (8).xlsx', '2024-06-26', '2024-06-21', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, '../uploads/Data_Keluhan (7) (1).xlsx', '2024-06-21', '2024-06-24', 'Approve', '2024-07-01', NULL, NULL, NULL, 'In Process', NULL, '2024-09-26', NULL, 'In Process', NULL, '2024-09-30'),
(9, 'KDR-002', 'MicrosoftTeams-image (2).png,Data_Keluhan (4).xlsx', 'poklm', '', NULL, '2024-10-02', 'In Process', NULL, '2024-06-26', '2024-06-23', 'Data_Keluhan (5).xlsx', '2024-06-30', '2024-06-23', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, 'Data_Keluhan (5) (1) (1) (1).xlsx', '2024-06-26', '2024-07-14', 'Approve', '2024-07-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'BDG-001', 'MicrosoftTeams-image (6).png,Data_Keluhan (1) (2).xlsx', 'catatan legal', '', 'In Process', '2024-10-05', 'In Process', NULL, '2024-06-23', '2024-06-23', 'Daily Report#Jepara-Jawa Tengah #29052024 (1).pdf,SOC phase 2.xlsx', '2024-06-30', '2024-06-23', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26', '2024-07-10', 'Approve', NULL, NULL, NULL, NULL, 'Approve', '2024-07-10', '2024-10-05', NULL, 'In Process', NULL, '2024-10-09'),
(11, 'BL-002', 'Data_Keluhan (8).xlsx', 'rtyuio', '', 'In Process', NULL, 'In Process', NULL, '2024-06-25', NULL, 'Data_Keluhan (5) (1).xlsx, Data_Keluhan (2) (1).xlsx', '2024-07-03', '2024-07-09', 'Approve', 'Approve', '2024-07-13', '2024-07-14', 'Approve', '2024-07-10', NULL, NULL, '2024-07-12', '2024-07-10', 'Approve', NULL, NULL, NULL, NULL, 'In Process', NULL, '2024-10-05', NULL, 'In Process', NULL, '2024-10-09'),
(12, 'MLG-002', '', '', '', 'In Process', '2024-10-07', 'In Process', NULL, '0000-00-00', '2024-06-26', '', '2024-06-30', '2024-06-26', 'Approve', 'Approve', NULL, '2024-06-26', 'Approve', '2024-06-26', 'Data_Keluhan (7) (2) (1).xlsx', 'MicrosoftTeams-image (5).png', '2024-06-29', '2024-06-26', 'Approve', '2024-07-07', NULL, NULL, NULL, 'In Process', NULL, '2024-10-02', NULL, 'In Process', NULL, '2024-10-06'),
(13, 'KDR-003', 'Data_Keluhan (2) (1).xlsx', 'cattaan', '', 'In Process', '2024-10-06', 'In Process', NULL, '2024-06-25', '2024-06-25', 'Data_Keluhan (5) (1).xlsx', '2024-07-02', '2024-06-25', 'Approve', NULL, NULL, NULL, NULL, NULL, NULL, 'Data_Keluhan (5) (1).xlsx', '2024-06-27', '2024-06-25', 'Approve', '2024-07-01', NULL, NULL, NULL, 'In Process', NULL, '2024-09-26', NULL, 'In Process', NULL, '2024-09-30'),
(14, 'JKT1', '', '', '', NULL, NULL, NULL, NULL, '0000-00-00', NULL, NULL, '2024-07-16', NULL, 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'BL-003', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'BL-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'SBY-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'KRWG-001', NULL, NULL, NULL, NULL, '2024-11-01', 'In Process', 'Last Updated by BoD', NULL, NULL, 'Data_Keluhan (5) (1).xlsx', '2024-07-21', '2024-07-14', 'Approve', 'Approve', '2024-07-18', '2024-07-14', 'Approve', '2024-07-14', 'WhatsApp Image 2024-07-11 at 16.42.08.jpeg', 'Data_Keluhan (5).xlsx', '2024-07-17', '2024-07-14', 'Approve', '2024-07-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `no_vd` varchar(255) DEFAULT NULL,
  `confirm_sdgdesain` varchar(255) NOT NULL,
  `obstacle` varchar(255) NOT NULL,
  `submit_legal` varchar(1000) NOT NULL,
  `catatan_submit` varchar(1000) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `lamp_pbg` varchar(1000) DEFAULT NULL,
  `lamp_permit` varchar(1000) DEFAULT NULL,
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
  `lamp_layouting` date DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `obs_detail` varchar(1000) DEFAULT NULL,
  `status_obssdg` varchar(255) DEFAULT NULL,
  `lamp_legal` varchar(1000) DEFAULT NULL,
  `obs_date` date DEFAULT NULL,
  `status_obslegal` varchar(255) DEFAULT NULL,
  `obslegal_date` date DEFAULT NULL,
  `sla_obslegal` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_desain`
--

INSERT INTO `sdg_desain` (`id`, `kode_lahan`, `lamp_desainplan`, `catatan_sdgdesain`, `tc`, `no_vd`, `confirm_sdgdesain`, `obstacle`, `submit_legal`, `catatan_submit`, `submit_date`, `lamp_pbg`, `lamp_permit`, `catatan_obslegal`, `start_date`, `sla_date`, `survey_date`, `sla_survey`, `layout_date`, `sla_layout`, `slalegal_date`, `end_date`, `lamp_survey`, `note_survey`, `status_survey`, `lamp_layouting`, `note`, `obs_detail`, `status_obssdg`, `lamp_legal`, `obs_date`, `status_obslegal`, `obslegal_date`, `sla_obslegal`) VALUES
(3, 'MLG-001', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', '', NULL, NULL, 'Approve', '', 'In Process', NULL, NULL, NULL, NULL, NULL, '2024-07-09', '2024-07-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, NULL, NULL, 'Not Obstacle', NULL, '2024-07-09', NULL, NULL, NULL),
(7, 'KDR-002', 'bbb.pdf', 'catatan obstacle', NULL, NULL, 'In Process', '', '', NULL, NULL, NULL, NULL, NULL, '2024-06-19', '2024-07-29', NULL, NULL, NULL, NULL, '2024-06-26', '2024-06-19', NULL, NULL, 'Done', NULL, NULL, NULL, 'Not Obstacle', NULL, '2024-07-09', NULL, NULL, NULL),
(8, 'BL-002', 'Form ST EQP Samarinda - Wahid Hasyim (1).xlsx,Data_Keluhan (5).xlsx', 'ertyui', 'NO TC', '6789', 'In Process', 'No', 'In Process', NULL, NULL, NULL, NULL, NULL, '2024-06-23', '2024-07-29', NULL, NULL, NULL, NULL, NULL, '2024-06-23', NULL, NULL, 'Done', NULL, NULL, NULL, 'Not Obstacle', NULL, '2024-07-09', NULL, NULL, NULL),
(9, 'MLG-002', 'Sertifikat Bem 2021.pdf,Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg', 'hhh', NULL, NULL, 'In Process', '', '', NULL, '2024-06-26', 'Data_Keluhan (5) (1).xlsx', 'Data_Keluhan (8).xlsx,Data_Keluhan (6).xlsx', 'yuijklm', NULL, '2024-06-03', NULL, NULL, NULL, NULL, '2024-07-16', '2024-06-23', NULL, NULL, 'Done', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(10, 'SBY-002', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'hhh', NULL, NULL, 'Approve', '', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-03', NULL, NULL, NULL, NULL, '2024-07-16', NULL, NULL, NULL, 'Done', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(11, 'SBY-001', 'bbb.pdf,WhatsApp Image 2024-04-23 at 12.10.58 (1).jpeg', '', NULL, NULL, 'Approve', '', 'In Process', NULL, NULL, NULL, NULL, NULL, '2024-07-09', '2024-07-29', NULL, NULL, NULL, NULL, NULL, '2024-05-28', NULL, NULL, 'Done', NULL, NULL, NULL, 'Not Obstacle', NULL, '2024-07-09', 'Not Obstacle', NULL, NULL),
(12, 'BDG-001', 'MicrosoftTeams-image (6).png,Data_Keluhan (1) (2).xlsx', '', 'TC', '12345', 'Approve', '', 'In Process', 'hjdkdk', '2024-06-26', NULL, NULL, '', '2024-07-09', '2024-07-22', NULL, NULL, NULL, NULL, NULL, '2024-06-23', NULL, NULL, 'Done', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(13, 'BL-003', 'Daily Report#Jepara-Jawa Tengah #29052024 (1).pdf,SOC phase 2.xlsx', '', 'NO TC', '34567', 'Approve', '', 'In Process', NULL, NULL, NULL, NULL, NULL, '2024-07-09', '2024-07-29', NULL, NULL, NULL, NULL, NULL, '2024-06-23', NULL, NULL, 'Done', '0000-00-00', '', '', 'Not Obstacle', NULL, '2024-07-09', NULL, NULL, NULL),
(14, 'KDR-003', 'Data_Keluhan (2) (1).xlsx', '', 'TC', '90345', 'Approve', '', 'In Process', NULL, NULL, NULL, NULL, 'catatan design', '2024-07-09', '2024-07-27', NULL, NULL, NULL, NULL, NULL, '2024-06-25', NULL, NULL, 'Done', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(15, 'KDR-004', NULL, '', NULL, NULL, 'In Process', 'No', '', NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-30', '2024-07-08', NULL, NULL, NULL, NULL, NULL, 'Data_Keluhan (5) (1) (1).xlsx', 'kskmcd', 'Pending', NULL, '', '', 'Not Obstacle', NULL, '2024-06-26', 'Not Obstacle', NULL, NULL),
(16, 'JKT1', 'WhatsApp Image 2024-06-26 at 08.18.59.jpeg', 'ertyui', 'NO TC', '6789', 'Approve', 'Yes', 'Approve', NULL, NULL, 'Data_Keluhan (5) (1) (1) (1).xlsx', 'Data_Keluhan (7) (2) (1).xlsx', 'yuijklm', '2024-06-26', '2024-07-30', NULL, NULL, NULL, NULL, NULL, '2024-06-26', 'image.png', NULL, 'Done', NULL, 'lalallala', 'jjjj', 'Diajukan', 'MicrosoftTeams-image (7).png', '2024-06-26', 'Done', '2024-06-26', NULL),
(17, 'BL-001', 'Data_Keluhan (3).xlsx', 'hhh', 'NO TC', '34567', 'Approve', 'Yes', 'In Process', NULL, NULL, NULL, NULL, 'test wo obs legal', '2024-06-28', '2024-08-01', NULL, NULL, NULL, NULL, NULL, '2024-07-08', 'MicrosoftTeams-image (6).png', NULL, 'Done', NULL, 'hahahha', 'hahahaha', 'Diajukan', 'Data_Keluhan (7) (2) (1).xlsx', '2024-06-28', 'Done', '2024-07-08', NULL),
(18, 'CI-001', NULL, '', NULL, NULL, 'In Process', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-08', '2024-07-13', NULL, '2024-07-13', NULL, NULL, 'Data_Keluhan (7) (3).xlsx', 'ksmnjdiuf', 'Done', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(19, 'SBY-003', NULL, '', NULL, NULL, 'In Process', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-13', NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, 'In Process', NULL, NULL, NULL, NULL, NULL),
(20, 'KRWG-001', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'ertyui', 'NO TC', '3456789', 'Approve', '', 'Approve', NULL, NULL, 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg', 'WhatsApp Image 2024-07-11 at 16.42.08.jpeg', NULL, '2024-07-14', '2024-08-03', '2024-07-14', '2024-07-19', NULL, '2024-07-19', '2024-07-21', NULL, 'WhatsApp Image 2024-07-11 at 16.42.08.jpeg', 'jkjkjkj', 'Done', '0000-00-00', '', '', 'Not Obstacle', NULL, '2024-07-14', NULL, NULL, NULL);

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
  `lamp_consact` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_pk`
--

INSERT INTO `sdg_pk` (`id`, `kode_lahan`, `month_1`, `month_2`, `month_3`, `all_progress`, `date_month1`, `date_month2`, `date_month3`, `week`, `date_week`, `catatan`, `lamp_pk`, `status_consact`, `sla_consact`, `consact_date`, `lamp_consact`) VALUES
(36, 'MLG-001', '1', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-09-22', NULL, NULL),
(38, 'SBY-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-09-29', NULL, NULL),
(39, 'KDR-003', '3', NULL, NULL, NULL, NULL, NULL, NULL, '3,4,0,0,0,0,0,0,0,0,0,0', NULL, '', '', 'In Process', '2024-09-29', NULL, NULL),
(40, 'MLG-002', '10.5', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-05', NULL, NULL),
(41, 'BDG-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-08', NULL, NULL),
(43, 'BL-002', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-08', NULL, NULL),
(44, 'KRWG-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-27', NULL, NULL),
(45, 'KRWG-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-27', NULL, NULL),
(46, 'KDR-002', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-27', NULL, NULL),
(47, 'KRWG-001', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'In Process', '2024-10-27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sdg_rab`
--

CREATE TABLE `sdg_rab` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `jenis_biaya` varchar(255) NOT NULL,
  `jumlah` varchar(255) NOT NULL,
  `lamp_rab` varchar(1000) NOT NULL,
  `keterangan` varchar(5000) NOT NULL,
  `confirm_sdgqs` varchar(255) NOT NULL,
  `catatan_sdgqs` varchar(255) DEFAULT NULL,
  `sla_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sdg_rab`
--

INSERT INTO `sdg_rab` (`id`, `kode_lahan`, `date`, `jenis_biaya`, `jumlah`, `lamp_rab`, `keterangan`, `confirm_sdgqs`, `catatan_sdgqs`, `sla_date`, `start_date`) VALUES
(3, 'MLG-001', '0000-00-00', 'm', '123', 'Uts Bisdig_Dini Naylul Izzah_20083000059_4B.jpg,WhatsApp Image 2024-04-23 at 12.10.56.jpeg', 'm', 'Approve', NULL, NULL, '2024-05-29'),
(8, 'SBY-001', '0000-00-00', '', '', '', '', 'Approve', NULL, '2024-06-04', '2024-06-14'),
(9, 'KDR-002', '0000-00-00', '', '2000000', 'Data_Keluhan (8).xlsx', 'c', 'Approve', NULL, '2024-06-29', '2024-06-19'),
(10, 'BL-002', '0000-00-00', '', '2134', 'MicrosoftTeams-image (6).png,Data_Keluhan (1) (2).xlsx,Form ST EQP Samarinda - Wahid Hasyim (1).xlsx', 'dwsdc', 'Approve', 'cvg', '2024-07-03', '2024-06-23'),
(11, 'BL-003', '0000-00-00', '', '45678', 'Data_Keluhan (2) (1).xlsx', 'test', 'Approve', 'catatan qs', '2024-07-03', '2024-06-25'),
(12, 'MLG-002', '0000-00-00', '', '5678', 'MicrosoftTeams-image (6).png', 'fcvghjbk', 'Approve', 'sazx', '2024-07-03', '2024-06-23'),
(13, 'BDG-001', '0000-00-00', '', '789', 'Data_Keluhan (5).xlsx,Daily Report#Jepara-Jawa Tengah #29052024 (1).pdf', 'uio', 'Approve', 'fghjk', '2024-07-03', '2024-06-23'),
(14, 'KDR-003', '0000-00-00', '', '123456', 'Data_Keluhan (2) (1).xlsx', 'test keterangan', 'Approve', 'catatan qs', '2024-07-05', '2024-06-25'),
(15, 'JKT1', '0000-00-00', '', '42072', 'Data_Keluhan (7) (2).xlsx', 'c', 'Approve', 'catatan qs', '2024-07-06', '2024-06-26'),
(17, 'BL-001', '0000-00-00', '', '3', 'Data_Keluhan (2).xlsx', 'fcvghjbk', 'Approve', 'fghjk', '2024-07-08', '2024-06-28'),
(37, 'SBY-002', '0000-00-00', '', '2', 'Data_Keluhan (5) (1) (1) (1).xlsx', 'fcvghjbk', 'Approve', 'y', '2024-07-19', '2024-07-09'),
(38, 'KRWG-001', '0000-00-00', '', '63784902', 'WhatsApp Image 2024-07-11 at 16.42.10.jpeg,Data_Keluhan (5) (1) (1).xlsx', 'xcyu', 'Approve', 'approve qs', '2024-07-24', '2024-07-14');

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

--
-- Dumping data for table `sign`
--

INSERT INTO `sign` (`id`, `kode_lahan`, `sdg_pm`, `note_sdgpm`, `legal_alo`, `note_legalalo`, `scm`, `note_scm`, `it_hrga_marketing`, `note_ihm`, `ops_rm`, `note_opsrm`, `sdg_head`, `note_sdghead`, `lamp_rto`, `soc_date`) VALUES
(1, 'MLG-001', 'on', 'nn', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'on', 'n', 'Data_Keluhan (7).xlsx', NULL),
(3, 'MLG-002', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '', 'on', '', '../uploads/Data_Keluhan (5) (1) (1).xlsx', NULL),
(5, 'KDR-003', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', NULL);

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
  `catatan_kpt3` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_academy`
--

INSERT INTO `socdate_academy` (`id`, `kode_lahan`, `kpt_1`, `kpt_2`, `kpt_3`, `status_kpt1`, `status_kpt2`, `status_kpt3`, `kpt_date1`, `kpt_date2`, `kpt_date3`, `sla_kpt1`, `sla_kpt2`, `sla_kpt3`, `lamp_kpt1`, `lamp_kpt2`, `lamp_kpt3`, `catatan_kpt1`, `catatan_kpt2`, `catatan_kpt3`) VALUES
(2, 'MLG-001', '90', '80', '100', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', '2024-06-25', '2024-07-20', '2024-08-14', 'MicrosoftTeams-image (7).png', 'Data_Keluhan (7).xlsx', 'MicrosoftTeams-image (6).png,Data_Keluhan (1) (2).xlsx', NULL, NULL, NULL),
(4, 'SBY-001', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', '2024-07-22', '2024-08-16', '2024-09-10', '', '', '', NULL, NULL, NULL),
(5, 'KDR-003', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', '2024-07-20', '2024-08-14', '2024-09-08', '', '', '', NULL, NULL, NULL),
(6, 'MLG-002', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', '2024-07-21', '2024-08-15', '2024-09-09', '', '', '', NULL, NULL, NULL),
(7, 'BDG-001', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, '2024-07-19', '2024-08-13', '2024-09-07', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'BL-002', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'KDR-002', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, '2024-07-16', '2024-08-10', '2024-09-04', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'KRWG-001', NULL, NULL, NULL, 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, '2024-08-15', '2024-09-09', '2024-10-04', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_fat`
--

CREATE TABLE `socdate_fat` (
  `id` int(255) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `lamp_qris` varchar(1000) DEFAULT NULL,
  `lamp_st` varchar(1000) DEFAULT NULL,
  `fat_date` date DEFAULT NULL,
  `status_fat` varchar(255) DEFAULT NULL,
  `sla_fat` date DEFAULT NULL,
  `catatan_fat` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_fat`
--

INSERT INTO `socdate_fat` (`id`, `kode_lahan`, `lamp_qris`, `lamp_st`, `fat_date`, `status_fat`, `sla_fat`, `catatan_fat`) VALUES
(28, 'MLG-001', 'Data_Keluhan (8).xlsx', 'Data_Keluhan (7) (2).xlsx', '0000-00-00', 'In Process', '2024-09-01', NULL),
(30, 'SBY-001', NULL, NULL, '0000-00-00', 'In Process', '2024-09-28', NULL),
(31, 'KDR-003', NULL, NULL, '0000-00-00', 'In Process', '2024-09-26', NULL),
(32, 'MLG-002', NULL, NULL, '0000-00-00', 'In Process', '2024-09-27', NULL),
(33, 'BDG-001', NULL, NULL, NULL, 'In Process', '2024-09-25', NULL),
(34, 'BL-002', NULL, NULL, NULL, 'In Process', '0000-00-00', NULL),
(37, 'KDR-002', NULL, NULL, NULL, 'In Process', '2024-09-22', NULL),
(38, 'KRWG-001', NULL, NULL, NULL, 'In Process', '2024-10-22', NULL);

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
  `catatan_ff3` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_hr`
--

INSERT INTO `socdate_hr` (`id`, `kode_lahan`, `tm`, `lamp_tm`, `ff_1`, `ff_2`, `ff_3`, `hot`, `lamp_hot`, `sla_ff1`, `sla_ff2`, `sla_ff3`, `status_ff1`, `status_ff2`, `status_ff3`, `ff1_date`, `ff2_date`, `ff3_date`, `lamp_ff1`, `lamp_ff2`, `lamp_ff3`, `status_tm`, `tm_date`, `sla_tm`, `status_hot`, `hot_date`, `sla_hot`, `catatan_tm`, `catatan_hot`, `catatan_ff1`, `catatan_ff2`, `catatan_ff3`) VALUES
(5, 'MLG-001', 'tm', 'Data_Keluhan (7) (1).xlsx', '80', '100', '90', 'h', 'MicrosoftTeams-image (7).png,RTO.pdf', '2024-06-25', '2024-07-20', '2024-08-14', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', 'Data_Keluhan (8).xlsx,Data_Keluhan (6).xlsx', 'MicrosoftTeams-image (2).png', 'MicrosoftTeams-image (6).png,Daily Report#Jepara-Jawa Tengah #29052024 (1).pdf,SOC phase 2.xlsx', 'In Process', '0000-00-00', '2024-05-31', 'In Process', '0000-00-00', '2024-08-09', NULL, NULL, NULL, NULL, NULL),
(7, 'SBY-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-22', '2024-08-16', '2024-09-10', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, 'In Process', '0000-00-00', '2024-06-27', 'In Process', '0000-00-00', '2024-09-05', NULL, NULL, NULL, NULL, NULL),
(8, 'KDR-003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-20', '2024-08-14', '2024-09-08', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, 'In Process', '0000-00-00', '2024-06-25', 'In Process', '0000-00-00', '2024-09-03', NULL, NULL, NULL, NULL, NULL),
(9, 'MLG-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-21', '2024-08-15', '2024-09-09', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, 'In Process', '0000-00-00', '2024-06-26', 'In Process', '0000-00-00', '2024-09-04', NULL, NULL, NULL, NULL, NULL),
(10, 'BDG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-19', '2024-08-13', '2024-09-07', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, 'In Process', '0000-00-00', '2024-06-24', 'In Process', '0000-00-00', '2024-09-02', NULL, NULL, NULL, NULL, NULL),
(11, 'BL-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', 'In Process', 'In Process', 'In Process', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, NULL, 'In Process', '0000-00-00', '0000-00-00', 'In Process', '0000-00-00', '0000-00-00', NULL, NULL, NULL, NULL, NULL),
(14, 'KDR-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-16', '2024-08-10', '2024-09-04', 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, '2024-06-21', 'In Process', NULL, '2024-08-30', NULL, NULL, NULL, NULL, NULL),
(15, 'KRWG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-15', '2024-09-09', '2024-10-04', 'In Process', 'In Process', 'In Process', NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', NULL, '2024-07-21', 'In Process', NULL, '2024-09-29', NULL, NULL, NULL, NULL, NULL);

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
(18, 'MLG-001', 'Data_Keluhan (8).xlsx', 'Data_Keluhan (7) (1).xlsx', '0000-00-00', 'In Process', '2024-09-01', '0000-00-00', NULL),
(20, 'SBY-001', NULL, NULL, '0000-00-00', 'In Process', '2024-09-28', '0000-00-00', NULL),
(21, 'KDR-003', NULL, NULL, '0000-00-00', 'In Process', '2024-09-26', '0000-00-00', NULL),
(22, 'MLG-002', NULL, NULL, '0000-00-00', 'In Process', '2024-09-27', '0000-00-00', NULL),
(23, 'BDG-001', NULL, NULL, NULL, 'In Process', '2024-09-25', NULL, NULL),
(24, 'BL-002', NULL, NULL, NULL, 'In Process', '0000-00-00', NULL, NULL),
(27, 'KDR-002', NULL, NULL, NULL, 'In Process', '2024-09-22', NULL, NULL),
(28, 'KRWG-001', NULL, NULL, NULL, 'In Process', '2024-10-22', NULL, NULL);

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
(22, 'MLG-001', 'k', 'k', 'k', 'Data_Keluhan (1) (2).xlsx,Data_Keluhan (5).xlsx', 'Daily Report#Jepara-Jawa Tengah #29052024 (1).pdf', 'Data_Keluhan (7).xlsx', 'semua_data_keluhan.xlsx', 'MicrosoftTeams-image (7).png', '2024-07-15', '0000-00-00', 'Approve', 'In Process', '2024-09-05', '2024-08-25', 'catatan_it', NULL),
(24, 'SBY-001', NULL, NULL, NULL, 'Data_Keluhan (8).xlsx', 'Form ST EQP Samarinda - Wahid Hasyim.xlsx', NULL, 'Data_Keluhan (5) (1).xlsx', 'Data_Keluhan (7).xlsx', '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-10-02', '2024-09-21', NULL, NULL),
(25, 'KDR-003', NULL, NULL, NULL, 'Data_Keluhan (8).xlsx', 'Data_Keluhan (7) (3).xlsx', NULL, 'WhatsApp Image 2024-06-25 at 15.07.19.jpeg', 'Data_Keluhan (5) (1).xlsx', '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-09-30', '2024-09-19', NULL, NULL),
(26, 'MLG-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-10-01', '2024-09-20', NULL, NULL),
(27, 'BDG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', '2024-09-29', '2024-09-18', NULL, NULL),
(28, 'BL-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', '0000-00-00', '0000-00-00', NULL, NULL),
(31, 'KDR-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', '2024-09-26', '2024-09-15', NULL, NULL),
(32, 'KRWG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', 'In Process', '2024-10-26', '2024-10-15', NULL, NULL);

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
(21, 'MLG-001', 'Data_Keluhan (7).xlsx,Data_Keluhan (8).xlsx', '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-09-01', '2024-08-22', NULL),
(23, 'SBY-001', NULL, '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-09-28', '2024-09-18', NULL),
(24, 'KDR-003', NULL, '0000-00-00', '0000-00-00', 'In Process', 'In Process', '2024-09-26', '2024-09-16', NULL),
(25, 'MLG-002', NULL, '0000-00-00', '0000-00-00', 'In Process', '', '2024-09-27', '0000-00-00', NULL),
(26, 'BDG-001', NULL, NULL, NULL, 'In Process', NULL, '2024-09-25', NULL, NULL),
(27, 'BL-002', NULL, NULL, NULL, 'In Process', NULL, '0000-00-00', NULL, NULL),
(30, 'KDR-002', NULL, NULL, NULL, 'In Process', NULL, '2024-09-22', NULL, NULL),
(31, 'KRWG-001', NULL, NULL, NULL, 'In Process', NULL, '2024-10-22', NULL, NULL);

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
  `merchant_date` date DEFAULT NULL,
  `marketing_date` date DEFAULT NULL,
  `status_marketing` varchar(255) DEFAULT NULL,
  `sla_marketing` date DEFAULT NULL,
  `catatan_marketing` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_marketing`
--

INSERT INTO `socdate_marketing` (`id`, `kode_lahan`, `gmaps`, `lamp_gmaps`, `id_m_shopee`, `id_m_gojek`, `id_m_grab`, `email_resto`, `lamp_merchant`, `merchant_date`, `marketing_date`, `status_marketing`, `sla_marketing`, `catatan_marketing`) VALUES
(20, 'MLG-001', 'l', 'Data_Keluhan (6).xlsx', 'l', 'l', 'l', 'l', NULL, '0000-00-00', '0000-00-00', 'In Process', '2024-09-01', NULL),
(22, 'SBY-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', 'In Process', '2024-09-28', NULL),
(23, 'KDR-003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', 'In Process', '2024-09-26', NULL),
(24, 'MLG-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', 'In Process', '2024-09-27', NULL),
(25, 'BDG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-09-25', NULL),
(26, 'BL-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '0000-00-00', NULL),
(29, 'KDR-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-09-22', NULL),
(30, 'KRWG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-10-22', NULL);

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
(21, 'MLG-001', 'Master Schedule Mie Gacoan.pdf,Kurva S Pantura.pdf', '0000-00-00', 'In Process', '2024-09-03', NULL),
(23, 'SBY-001', NULL, '0000-00-00', 'In Process', '2024-09-30', NULL),
(24, 'KDR-003', 'Data_Keluhan (5) (1) (1) (1).xlsx', '0000-00-00', 'In Process', '2024-09-28', NULL),
(25, 'MLG-002', NULL, '0000-00-00', 'In Process', '2024-09-29', NULL),
(26, 'BDG-001', NULL, NULL, 'In Process', '2024-09-27', NULL),
(27, 'BL-002', NULL, NULL, 'In Process', '0000-00-00', NULL),
(30, 'KDR-002', NULL, NULL, 'In Process', '2024-09-24', NULL),
(31, 'KRWG-001', NULL, NULL, 'In Process', '2024-10-24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socdate_sdg`
--

CREATE TABLE `socdate_sdg` (
  `id` int(11) NOT NULL,
  `kode_lahan` varchar(255) NOT NULL,
  `no_listrik` varchar(255) DEFAULT NULL,
  `lamp_listrik` varchar(1000) DEFAULT NULL,
  `lamp_ka` varchar(1000) DEFAULT NULL,
  `lamp_ipal` varchar(1000) DEFAULT NULL,
  `lamp_eqp` varchar(1000) DEFAULT NULL,
  `lamp_ba` varchar(1000) DEFAULT NULL,
  `sdgpk_date` date DEFAULT NULL,
  `eqp_date` date DEFAULT NULL,
  `ba_date` date DEFAULT NULL,
  `status_sdg` varchar(255) DEFAULT NULL,
  `sla_sdg` date DEFAULT NULL,
  `catatan_sdg` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socdate_sdg`
--

INSERT INTO `socdate_sdg` (`id`, `kode_lahan`, `no_listrik`, `lamp_listrik`, `lamp_ka`, `lamp_ipal`, `lamp_eqp`, `lamp_ba`, `sdgpk_date`, `eqp_date`, `ba_date`, `status_sdg`, `sla_sdg`, `catatan_sdg`) VALUES
(20, 'MLG-001', '1234', 'Data_Keluhan (7) (2).xlsx', 'Data_Keluhan (8).xlsx', 'Data_Keluhan (6).xlsx', NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', 'In Process', '2024-08-25', NULL),
(22, 'SBY-001', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', 'In Process', '2024-09-21', NULL),
(23, 'KDR-003', '1234', 'Data_Keluhan (2) (1).xlsx', 'Data_Keluhan (2) (1).xlsx', 'Data_Keluhan (5) (1).xlsx', NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', 'In Process', '2024-09-19', NULL),
(24, 'MLG-002', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00', '0000-00-00', 'In Process', '2024-09-20', NULL),
(25, 'BDG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-09-18', NULL),
(26, 'BL-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '0000-00-00', NULL),
(29, 'KDR-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-09-15', NULL),
(30, 'KRWG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'In Process', '2024-10-15', NULL);

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
(1, 'MLG-001', '100', 'n', '100', 'n'),
(2, 'KDR-003', '100', '', '90', ''),
(3, 'MLG-002', '90', '', '90', '');

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
(1, 'MLG-001', '100', 'n', '100', 'n'),
(2, 'KDR-003', '100', '', '100', ''),
(3, 'MLG-002', '90', '', '100', '');

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
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n'),
(3, 'KDR-003', '100', '', '90', '', '100', '', '90', '', '100', ''),
(4, 'MLG-002', '90', '', '100', '', '100', '', '80', '', '90', '');

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
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n', '100', 'n'),
(3, 'KDR-003', '100', '', '80', '', '80', '', '100', ''),
(4, 'MLG-002', '100', '', '90', '', '80', '', '100', '');

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
(1, 'MLG-001', '100', 'n', '100', 'n', '100', 'n'),
(2, 'KDR-003', '100', '', '90', '', '100', ''),
(3, 'MLG-002', '90', '', '89', '', '90', '');

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
(1, 'MLG-001', '', '2024-06-07', 'n', 'n', 'In Process'),
(3, 'KDR-003', '', '2024-10-03', 'test', 'test', 'In Process'),
(4, 'MLG-002', '', '0000-00-00', 'test', 'test', 'In Process');

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
(1, 'MLG-001', '80', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n', '100', 'n'),
(3, 'KDR-003', '80', '', '70', '', '80', '', '100', '', '100', '', '100', ''),
(4, 'MLG-002', '90', '', '70', '', '100', '', '90', '', '100', '', '90', '');

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
(1, 'MLG-001', '2024-06-15', '2024-06-12', 'double', '24 Jam', '100.000.000', '30', '2024-06-12', '', '                                            98.335%', '', 'On Schedule', ''),
(2, 'KDR-003', NULL, NULL, 'double', '24 Jam', '120.000.000', '60', '2024-07-11', '                                            7.41%', '                                            90.9585%', NULL, 'Accelerated', NULL),
(3, 'MLG-002', NULL, NULL, '', '9-23', '150.000.000', '40', '2024-07-11', '                                            100.00%', '                                            91.1285%', NULL, 'Delayed', NULL),
(4, 'SBY-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'BDG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'BL-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'KDR-002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'KRWG-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
-- Indexes for table `hold_project`
--
ALTER TABLE `hold_project`
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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `draft`
--
ALTER TABLE `draft`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `hold_project`
--
ALTER TABLE `hold_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `konstruksi`
--
ALTER TABLE `konstruksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `land`
--
ALTER TABLE `land`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `master_sla`
--
ALTER TABLE `master_sla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `master_slacons`
--
ALTER TABLE `master_slacons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `re`
--
ALTER TABLE `re`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `resto`
--
ALTER TABLE `resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sdg_desain`
--
ALTER TABLE `sdg_desain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sdg_land`
--
ALTER TABLE `sdg_land`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sdg_pk`
--
ALTER TABLE `sdg_pk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `sdg_rab`
--
ALTER TABLE `sdg_rab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `sign`
--
ALTER TABLE `sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `socdate_academy`
--
ALTER TABLE `socdate_academy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `socdate_fat`
--
ALTER TABLE `socdate_fat`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `socdate_hr`
--
ALTER TABLE `socdate_hr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `socdate_ir`
--
ALTER TABLE `socdate_ir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `socdate_it`
--
ALTER TABLE `socdate_it`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `socdate_legal`
--
ALTER TABLE `socdate_legal`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `socdate_marketing`
--
ALTER TABLE `socdate_marketing`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `socdate_scm`
--
ALTER TABLE `socdate_scm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `socdate_sdg`
--
ALTER TABLE `socdate_sdg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
