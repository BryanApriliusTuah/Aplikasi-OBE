-- MySQL dump 10.13  Distrib 8.0.41, for macos15 (arm64)
--
-- Host: 127.0.0.1    Database: obe_db_dummy
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analisis_cpl`
--

DROP TABLE IF EXISTS `analisis_cpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analisis_cpl` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `program_studi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tahun_akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `angkatan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mode` enum('auto','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'auto',
  `auto_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `analisis_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `bukti_dokumentasi_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded bukti dokumentasi asesmen file',
  `notulensi_rapat_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded notulensi rapat evaluasi CPL file',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analisis_cpmk`
--

DROP TABLE IF EXISTS `analisis_cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analisis_cpmk` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` int NOT NULL,
  `tahun_akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `program_studi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mode` enum('auto','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'auto',
  `analisis_singkat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `auto_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `analisis_cpmk_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  CONSTRAINT `analisis_cpmk_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analysis_templates`
--

DROP TABLE IF EXISTS `analysis_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analysis_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `option_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `option_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `template_tercapai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `template_tidak_tercapai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_key` (`option_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analysis_templates_cpl`
--

DROP TABLE IF EXISTS `analysis_templates_cpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analysis_templates_cpl` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `option_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `option_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `template_tercapai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `template_tidak_tercapai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_key` (`option_key`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bahan_kajian`
--

DROP TABLE IF EXISTS `bahan_kajian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_kajian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_bk` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_bk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_bk` (`kode_bk`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bk_mk`
--

DROP TABLE IF EXISTS `bk_mk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bk_mk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bahan_kajian_id` int NOT NULL,
  `mata_kuliah_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_kajian_id` (`bahan_kajian_id`,`mata_kuliah_id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `bk_mk_ibfk_1` FOREIGN KEY (`bahan_kajian_id`) REFERENCES `bahan_kajian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bk_mk_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpl`
--

DROP TABLE IF EXISTS `cpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_cpl` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_cpl` enum('P','KK','S','KU') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_cpl` (`kode_cpl`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpl_bk`
--

DROP TABLE IF EXISTS `cpl_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl_bk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpl_id` int NOT NULL,
  `bk_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bk_id` (`bk_id`,`cpl_id`),
  KEY `cpl_id` (`cpl_id`),
  CONSTRAINT `cpl_bk_ibfk_1` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpl_bk_ibfk_2` FOREIGN KEY (`bk_id`) REFERENCES `bahan_kajian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpl_cpmk`
--

DROP TABLE IF EXISTS `cpl_cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl_cpmk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpl_id` int NOT NULL,
  `cpmk_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cpl_id` (`cpl_id`),
  KEY `cpmk_id` (`cpmk_id`),
  CONSTRAINT `cpl_cpmk_ibfk_1` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpl_cpmk_ibfk_2` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpl_mk`
--

DROP TABLE IF EXISTS `cpl_mk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl_mk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpl_id` int NOT NULL,
  `mata_kuliah_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cplmk` (`cpl_id`,`mata_kuliah_id`),
  UNIQUE KEY `uq_cpl_mk` (`cpl_id`,`mata_kuliah_id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `cpl_mk_ibfk_1` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpl_mk_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpl_pl`
--

DROP TABLE IF EXISTS `cpl_pl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl_pl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpl_id` int NOT NULL,
  `pl_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpl_id` (`cpl_id`,`pl_id`),
  KEY `pl_id` (`pl_id`),
  CONSTRAINT `cpl_pl_ibfk_1` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpl_pl_ibfk_2` FOREIGN KEY (`pl_id`) REFERENCES `profil_lulusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpmk`
--

DROP TABLE IF EXISTS `cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpmk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_cpmk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cpmk_kode` (`kode_cpmk`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cpmk_mk`
--

DROP TABLE IF EXISTS `cpmk_mk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpmk_mk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpmk_id` int NOT NULL,
  `mata_kuliah_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cpmk_mk` (`cpmk_id`,`mata_kuliah_id`),
  UNIQUE KEY `mata_kuliah_id` (`mata_kuliah_id`,`cpmk_id`),
  CONSTRAINT `fk_cpmk` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mk` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=449 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cqi`
--

DROP TABLE IF EXISTS `cqi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cqi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('cpl','cpmk') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cpl',
  `program_studi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `angkatan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jadwal_id` int unsigned DEFAULT NULL,
  `kode_cpl` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_cpmk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masalah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rencana_perbaikan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `penanggung_jawab` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jadwal_pelaksanaan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `program_studi_tahun_akademik_angkatan` (`program_studi`,`tahun_akademik`,`angkatan`),
  KEY `jadwal_id` (`jadwal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dosen`
--

DROP TABLE IF EXISTS `dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gelar_depan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gelar_belakang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jabatan_fungsional` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_dosen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_keaktifan` enum('Aktif','Tidak Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `fakultas_kode` int DEFAULT NULL,
  `program_studi_kode` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nip` (`nip`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  KEY `fk_dosen_fakultas` (`fakultas_kode`),
  KEY `fk_dosen_program_studi` (`program_studi_kode`),
  CONSTRAINT `fk_dosen_fakultas` FOREIGN KEY (`fakultas_kode`) REFERENCES `fakultas` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_dosen_program_studi` FOREIGN KEY (`program_studi_kode`) REFERENCES `program_studi` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_dosen_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fakultas`
--

DROP TABLE IF EXISTS `fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fakultas` (
  `kode` int NOT NULL,
  `nama_singkat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_resmi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nip_dekan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_dekan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grade_config`
--

DROP TABLE IF EXISTS `grade_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grade_letter` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Grade letter (A, AB, B, BC, C, D, E)',
  `min_score` decimal(5,2) NOT NULL COMMENT 'Minimum score for this grade (0-100)',
  `max_score` decimal(5,2) NOT NULL COMMENT 'Maximum score for this grade (0-100)',
  `grade_point` decimal(3,2) DEFAULT NULL COMMENT 'Grade point value (e.g., 4.0 for A)',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Grade description (e.g., Istimewa, Baik Sekali)',
  `is_passing` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = passing grade, 0 = failing grade',
  `order_number` int NOT NULL COMMENT 'Order for sorting grades',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade_letter` (`grade_letter`),
  KEY `order_number` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jadwal`
--

DROP TABLE IF EXISTS `jadwal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` int NOT NULL,
  `program_studi_kode` int DEFAULT NULL,
  `tahun_akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'A',
  `ruang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('active','inactive','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_nilai_validated` tinyint(1) DEFAULT '0' COMMENT '0 = Not validated, 1 = Validated by admin',
  `validated_at` datetime DEFAULT NULL COMMENT 'Timestamp when nilai was validated',
  `validated_by` int DEFAULT NULL COMMENT 'User ID who validated the nilai',
  `rubrik_penilaian_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded rubrik penilaian file',
  `contoh_soal_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded contoh soal dan jawaban file',
  `notulen_rapat_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded notulen rapat evaluasi file',
  `kelas_id` bigint DEFAULT NULL COMMENT 'References kelas table from API',
  `kelas_jenis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'e.g. Kelas Reguler',
  `kelas_semester` int DEFAULT NULL COMMENT 'e.g. 20252',
  `kelas_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Aktif',
  `mk_kurikulum_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Mata kuliah kurikulum code from API',
  `total_mahasiswa` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal` (`mata_kuliah_id`,`program_studi_kode`,`tahun_akademik`,`kelas`),
  KEY `idx_program_studi_kode` (`program_studi_kode`),
  KEY `idx_tahun_akademik` (`tahun_akademik`),
  KEY `idx_status` (`status`),
  KEY `idx_jadwal_semester` (`tahun_akademik`,`program_studi_kode`),
  KEY `idx_jadwal_waktu` (`hari`,`jam_mulai`),
  KEY `fk_jadwal_kelas` (`kelas_id`),
  CONSTRAINT `fk_jadwal_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_mata_kuliah` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_program_studi` FOREIGN KEY (`program_studi_kode`) REFERENCES `program_studi` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jadwal_dosen`
--

DROP TABLE IF EXISTS `jadwal_dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jadwal_id` int NOT NULL,
  `dosen_id` int NOT NULL,
  `role` enum('leader','member') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jadwal_id` (`jadwal_id`),
  KEY `idx_dosen_id` (`dosen_id`),
  KEY `idx_role` (`role`),
  KEY `idx_dosen_role_jadwal` (`dosen_id`,`role`),
  CONSTRAINT `fk_jadwal_dosen_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_dosen_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jadwal_mahasiswa`
--

DROP TABLE IF EXISTS `jadwal_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jadwal_id` int NOT NULL,
  `nim` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jadwal_nim` (`jadwal_id`,`nim`),
  KEY `fk_jadwal_mahasiswa_nim` (`nim`),
  CONSTRAINT `fk_jadwal_mahasiswa_mengajar` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_mahasiswa_nim` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `kelas_id` bigint NOT NULL,
  `kelas_sem_id` int NOT NULL,
  `kelas_nama` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `matakuliah_kurikulum_id` bigint NOT NULL,
  `matakuliah_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `matakuliah_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kurikulum_id` int NOT NULL DEFAULT '0',
  `kurikulum_status` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fakultas_kode` int DEFAULT NULL,
  `fakultas_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `program_studi_kode` int DEFAULT NULL,
  `program_studi_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`kelas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kurikulum`
--

DROP TABLE IF EXISTS `kurikulum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kurikulum` (
  `id` int NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `revisi` int DEFAULT '0',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fakultas_kode` int DEFAULT NULL,
  `program_studi_kode` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kurikulum_fakultas` (`fakultas_kode`),
  KEY `fk_kurikulum_program_studi` (`program_studi_kode`),
  CONSTRAINT `fk_kurikulum_fakultas` FOREIGN KEY (`fakultas_kode`) REFERENCES `fakultas` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_kurikulum_program_studi` FOREIGN KEY (`program_studi_kode`) REFERENCES `program_studi` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mahasiswa`
--

DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nim` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kelamin` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `program_studi_kode` int DEFAULT NULL,
  `tahun_angkatan` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_mahasiswa` enum('Aktif','Cuti','Lulus','Mengundurkan Diri','DO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nim` (`nim`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  KEY `fk_mahasiswa_program_studi` (`program_studi_kode`),
  CONSTRAINT `fk_mahasiswa_program_studi` FOREIGN KEY (`program_studi_kode`) REFERENCES `program_studi` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_mahasiswa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3382 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mata_kuliah`
--

DROP TABLE IF EXISTS `mata_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_kuliah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_mk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi_singkat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipe` enum('wajib','pilihan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `semester` tinyint NOT NULL,
  `sks` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategori` enum('wajib_teori','wajib_praktikum','pilihan','mkwk') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'wajib_teori',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_mk` (`kode_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_jenis_kegiatan`
--

DROP TABLE IF EXISTS `mbkm_jenis_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_jenis_kegiatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_kegiatan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kegiatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sks_konversi` int NOT NULL DEFAULT '20',
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode_kegiatan` (`kode_kegiatan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_kegiatan`
--

DROP TABLE IF EXISTS `mbkm_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_kegiatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis_kegiatan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Magang' COMMENT 'Type of MBKM activity (text input)',
  `judul_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pembimbing_lapangan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kontak_pembimbing` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dosen_pembimbing_id` int DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi_minggu` int DEFAULT NULL,
  `sks_dikonversi` int DEFAULT '20',
  `deskripsi_kegiatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dokumen_pendukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_kegiatan` enum('diajukan','disetujui','ditolak','berlangsung','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'diajukan',
  `tahun_akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nilai_type` enum('cpmk','cpl') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Type of achievement: CPMK or CPL',
  `cpmk_id` int DEFAULT NULL COMMENT 'FK to cpmk table if nilai_type is cpmk',
  `cpl_id` int DEFAULT NULL COMMENT 'FK to cpl table if nilai_type is cpl',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dosen_pembimbing` (`dosen_pembimbing_id`),
  KEY `idx_status` (`status_kegiatan`),
  KEY `idx_kegiatan_cpmk` (`cpmk_id`),
  KEY `idx_kegiatan_cpl` (`cpl_id`),
  CONSTRAINT `fk_kegiatan_cpl` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_kegiatan_cpmk` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mbkm_dosen` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_kegiatan_mahasiswa`
--

DROP TABLE IF EXISTS `mbkm_kegiatan_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_kegiatan_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kegiatan_id` int NOT NULL,
  `mahasiswa_id` int NOT NULL,
  `peran` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Role in the activity: Ketua, Anggota, etc',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan_mahasiswa` (`kegiatan_id`,`mahasiswa_id`),
  KEY `idx_kegiatan` (`kegiatan_id`),
  KEY `idx_mahasiswa` (`mahasiswa_id`),
  KEY `idx_kegiatan_mahasiswa_combined` (`kegiatan_id`,`mahasiswa_id`),
  CONSTRAINT `fk_kegiatan_mhs_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kegiatan_mhs_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_komponen_nilai`
--

DROP TABLE IF EXISTS `mbkm_komponen_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_komponen_nilai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis_kegiatan_id` int NOT NULL,
  `nama_komponen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`),
  CONSTRAINT `fk_komponen_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_nilai`
--

DROP TABLE IF EXISTS `mbkm_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_nilai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kegiatan_id` int NOT NULL,
  `komponen_id` int NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `penilai` enum('pembimbing_lapangan','dosen_pembimbing','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'dosen_pembimbing',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan_komponen` (`kegiatan_id`,`komponen_id`),
  KEY `idx_kegiatan` (`kegiatan_id`),
  KEY `idx_komponen` (`komponen_id`),
  CONSTRAINT `fk_nilai_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_nilai_komponen` FOREIGN KEY (`komponen_id`) REFERENCES `mbkm_komponen_nilai` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbkm_nilai_akhir`
--

DROP TABLE IF EXISTS `mbkm_nilai_akhir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_nilai_akhir` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kegiatan_id` int NOT NULL,
  `nilai_angka` decimal(5,2) NOT NULL,
  `nilai_huruf` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_kelulusan` enum('Lulus','Tidak Lulus') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Lulus',
  `nilai_type` enum('cpmk','cpl') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Type of achievement this score applies to',
  `cpmk_id` int DEFAULT NULL COMMENT 'FK to cpmk table if nilai_type is cpmk',
  `cpl_id` int DEFAULT NULL COMMENT 'FK to cpl table if nilai_type is cpl',
  `catatan_akhir` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tanggal_penilaian` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan` (`kegiatan_id`),
  KEY `idx_cpmk` (`cpmk_id`),
  KEY `idx_cpl` (`cpl_id`),
  CONSTRAINT `fk_nilai_akhir_cpl` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_nilai_akhir_cpmk` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_nilai_akhir_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mk_prasyarat`
--

DROP TABLE IF EXISTS `mk_prasyarat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mk_prasyarat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` int NOT NULL,
  `prasyarat_mk_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  KEY `prasyarat_mk_id` (`prasyarat_mk_id`),
  CONSTRAINT `mk_prasyarat_ibfk_1` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mk_prasyarat_ibfk_2` FOREIGN KEY (`prasyarat_mk_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nilai_cpmk_mahasiswa`
--

DROP TABLE IF EXISTS `nilai_cpmk_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_cpmk_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL COMMENT 'FK to the mahasiswa table',
  `jadwal_id` int NOT NULL COMMENT 'FK to the specific class offering in jadwal',
  `cpmk_id` int NOT NULL COMMENT 'FK to the cpmk table',
  `nilai_cpmk` decimal(5,2) NOT NULL COMMENT 'The numerical score achieved for this CPMK (0-100)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mahasiswa_cpmk_in_class` (`mahasiswa_id`,`jadwal_id`,`cpmk_id`),
  KEY `fk_nilai_cpmk_mahasiswa` (`mahasiswa_id`),
  KEY `fk_nilai_cpmk_jadwal` (`jadwal_id`),
  KEY `fk_nilai_cpmk_cpmk` (`cpmk_id`),
  CONSTRAINT `fk_nilai_cpmk_cpmk_id` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_cpmk_jadwal_id` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_cpmk_mahasiswa_id` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nilai_mahasiswa`
--

DROP TABLE IF EXISTS `nilai_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL COMMENT 'Foreign key to the mahasiswa table',
  `jadwal_id` int NOT NULL COMMENT 'Foreign key to the jadwal table to identify the specific class',
  `nilai_akhir` decimal(5,2) DEFAULT NULL COMMENT 'Final numerical score, e.g., 85.50',
  `nilai_huruf` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Letter grade, e.g., A, B+, C',
  `status_kelulusan` enum('Lulus','Tidak Lulus','Diproses') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Diproses',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Optional notes from the lecturer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mahasiswa_jadwal` (`mahasiswa_id`,`jadwal_id`),
  KEY `fk_nilai_mahasiswa` (`mahasiswa_id`),
  KEY `fk_nilai_jadwal` (`jadwal_id`),
  KEY `idx_nilai_huruf` (`nilai_huruf`),
  KEY `idx_status_kelulusan` (`status_kelulusan`),
  CONSTRAINT `fk_nilai_jadwal_id` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_mahasiswa_id` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nilai_teknik_penilaian`
--

DROP TABLE IF EXISTS `nilai_teknik_penilaian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_teknik_penilaian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL,
  `jadwal_id` int NOT NULL,
  `rps_mingguan_id` int NOT NULL COMMENT 'Reference to rps_mingguan where teknik_penilaian is defined',
  `teknik_penilaian_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'e.g., partisipasi, observasi, tes_tulis_uts, tes_tulis_uas, etc.',
  `nilai` decimal(5,2) DEFAULT NULL COMMENT 'Score for this teknik_penilaian (0-100)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nilai_teknik_penilaian_jadwal_id_foreign` (`jadwal_id`),
  KEY `nilai_teknik_penilaian_rps_mingguan_id_foreign` (`rps_mingguan_id`),
  KEY `idx_nilai_teknik` (`mahasiswa_id`,`jadwal_id`,`rps_mingguan_id`,`teknik_penilaian_key`),
  CONSTRAINT `nilai_teknik_penilaian_jadwal_id_foreign` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nilai_teknik_penilaian_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nilai_teknik_penilaian_rps_mingguan_id_foreign` FOREIGN KEY (`rps_mingguan_id`) REFERENCES `rps_mingguan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1236 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profil_lulusan`
--

DROP TABLE IF EXISTS `profil_lulusan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_lulusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_pl` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pl` (`kode_pl`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profil_prodi`
--

DROP TABLE IF EXISTS `profil_prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_prodi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_universitas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_fakultas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_prodi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_ketua_prodi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nip_ketua_prodi` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_dekan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nip_dekan` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program_studi`
--

DROP TABLE IF EXISTS `program_studi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_studi` (
  `kode` int NOT NULL,
  `nama_singkat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_resmi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nip_kaprodi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_kaprodi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fakultas_kode` int NOT NULL,
  PRIMARY KEY (`kode`),
  KEY `program_studi_fakultas_kode_foreign` (`fakultas_kode`),
  CONSTRAINT `program_studi_fakultas_kode_foreign` FOREIGN KEY (`fakultas_kode`) REFERENCES `fakultas` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rps`
--

DROP TABLE IF EXISTS `rps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` int NOT NULL,
  `semester` int DEFAULT NULL,
  `tahun_ajaran` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_penyusunan` date DEFAULT NULL,
  `status` enum('draft','final','arsip') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `rps_ibfk_1` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rps_mingguan`
--

DROP TABLE IF EXISTS `rps_mingguan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rps_mingguan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rps_id` int NOT NULL,
  `minggu` int NOT NULL,
  `cpl_id` int NOT NULL,
  `cpmk_id` int NOT NULL,
  `tahap_penilaian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sub_cpmk_id` int NOT NULL,
  `indikator` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `kriteria_penilaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `teknik_penilaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `instrumen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `materi_pembelajaran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `metode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `bobot` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  KEY `cpl_id` (`cpl_id`),
  KEY `cpmk_id` (`cpmk_id`),
  KEY `sub_cpmk_id` (`sub_cpmk_id`),
  CONSTRAINT `rps_mingguan_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rps_mingguan_ibfk_2` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`),
  CONSTRAINT `rps_mingguan_ibfk_3` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`),
  CONSTRAINT `rps_mingguan_ibfk_4` FOREIGN KEY (`sub_cpmk_id`) REFERENCES `sub_cpmk` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rps_pengampu`
--

DROP TABLE IF EXISTS `rps_pengampu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rps_pengampu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rps_id` int NOT NULL,
  `dosen_id` int NOT NULL,
  `peran` enum('pengampu','koordinator','penyusun') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pengampu',
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  KEY `fk_rps_pengampu_to_dosen` (`dosen_id`),
  CONSTRAINT `fk_rps_pengampu_to_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rps_pengampu_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rps_referensi`
--

DROP TABLE IF EXISTS `rps_referensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rps_referensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rps_id` int NOT NULL,
  `tipe` enum('utama','pendukung') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'utama',
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penulis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerbit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  CONSTRAINT `rps_referensi_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `standar_minimal_cpl`
--

DROP TABLE IF EXISTS `standar_minimal_cpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `standar_minimal_cpl` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `persentase` decimal(5,2) DEFAULT '75.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `standar_minimal_cpmk`
--

DROP TABLE IF EXISTS `standar_minimal_cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `standar_minimal_cpmk` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `persentase` decimal(5,2) NOT NULL DEFAULT '75.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_cpmk`
--

DROP TABLE IF EXISTS `sub_cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_cpmk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpmk_id` int NOT NULL,
  `kode_sub_cpmk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cpmk_id` (`cpmk_id`),
  CONSTRAINT `sub_cpmk_ibfk_1` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_cpmk_mk`
--

DROP TABLE IF EXISTS `sub_cpmk_mk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_cpmk_mk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sub_cpmk_id` int NOT NULL,
  `mata_kuliah_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sub_cpmk_id` (`sub_cpmk_id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `sub_cpmk_mk_ibfk_1` FOREIGN KEY (`sub_cpmk_id`) REFERENCES `sub_cpmk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sub_cpmk_mk_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','dosen','mahasiswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=931 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `view_jadwal_lengkap`
--

DROP TABLE IF EXISTS `view_jadwal_lengkap`;
/*!50001 DROP VIEW IF EXISTS `view_jadwal_lengkap`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_jadwal_lengkap` AS SELECT 
 1 AS `id`,
 1 AS `kode_mk`,
 1 AS `nama_mk`,
 1 AS `semester`,
 1 AS `sks`,
 1 AS `program_studi_kode`,
 1 AS `tahun_akademik`,
 1 AS `kelas`,
 1 AS `ruang`,
 1 AS `hari`,
 1 AS `jam_mulai`,
 1 AS `jam_selesai`,
 1 AS `status`,
 1 AS `dosen_pengampu`,
 1 AS `dosen_ketua`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `view_mbkm_lengkap`
--

DROP TABLE IF EXISTS `view_mbkm_lengkap`;
/*!50001 DROP VIEW IF EXISTS `view_mbkm_lengkap`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_mbkm_lengkap` AS SELECT 
 1 AS `id`,
 1 AS `judul_kegiatan`,
 1 AS `nim_list`,
 1 AS `nama_mahasiswa_list`,
 1 AS `mahasiswa_detail`,
 1 AS `jumlah_mahasiswa`,
 1 AS `program_studi_kode`,
 1 AS `jenis_kegiatan`,
 1 AS `tempat_kegiatan`,
 1 AS `tanggal_mulai`,
 1 AS `tanggal_selesai`,
 1 AS `durasi_minggu`,
 1 AS `sks_dikonversi`,
 1 AS `dosen_pembimbing`,
 1 AS `status_kegiatan`,
 1 AS `tahun_akademik`,
 1 AS `nilai_type`,
 1 AS `cpmk_id`,
 1 AS `cpl_id`,
 1 AS `kode_cpmk`,
 1 AS `cpmk_deskripsi`,
 1 AS `kode_cpl`,
 1 AS `cpl_deskripsi`,
 1 AS `nilai_angka`,
 1 AS `nilai_huruf`,
 1 AS `status_kelulusan`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_jadwal_lengkap`
--

/*!50001 DROP VIEW IF EXISTS `view_jadwal_lengkap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_jadwal_lengkap` AS select `jm`.`id` AS `id`,`mk`.`kode_mk` AS `kode_mk`,`mk`.`nama_mk` AS `nama_mk`,`mk`.`semester` AS `semester`,`mk`.`sks` AS `sks`,`jm`.`program_studi_kode` AS `program_studi_kode`,`jm`.`tahun_akademik` AS `tahun_akademik`,`jm`.`kelas` AS `kelas`,`jm`.`ruang` AS `ruang`,`jm`.`hari` AS `hari`,`jm`.`jam_mulai` AS `jam_mulai`,`jm`.`jam_selesai` AS `jam_selesai`,`jm`.`status` AS `status`,group_concat((case when (`jd`.`role` = 'leader') then concat(`d`.`nama_lengkap`,' (Ketua)') else `d`.`nama_lengkap` end) order by `jd`.`role` DESC,`d`.`nama_lengkap` ASC separator ', ') AS `dosen_pengampu`,(select `d2`.`nama_lengkap` from (`jadwal_dosen` `jd2` join `dosen` `d2` on((`jd2`.`dosen_id` = `d2`.`id`))) where ((`jd2`.`jadwal_id` = `jm`.`id`) and (`jd2`.`role` = 'leader')) limit 1) AS `dosen_ketua` from (((`jadwal` `jm` join `mata_kuliah` `mk` on((`jm`.`mata_kuliah_id` = `mk`.`id`))) left join `jadwal_dosen` `jd` on((`jm`.`id` = `jd`.`jadwal_id`))) left join `dosen` `d` on((`jd`.`dosen_id` = `d`.`id`))) group by `jm`.`id`,`mk`.`kode_mk`,`mk`.`nama_mk`,`mk`.`semester`,`mk`.`sks`,`jm`.`program_studi_kode`,`jm`.`tahun_akademik`,`jm`.`kelas`,`jm`.`ruang`,`jm`.`hari`,`jm`.`jam_mulai`,`jm`.`jam_selesai`,`jm`.`status` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_mbkm_lengkap`
--

/*!50001 DROP VIEW IF EXISTS `view_mbkm_lengkap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_mbkm_lengkap` AS select `k`.`id` AS `id`,`k`.`judul_kegiatan` AS `judul_kegiatan`,group_concat(distinct `m`.`nim` order by `km`.`id` ASC separator ', ') AS `nim_list`,group_concat(distinct `m`.`nama_lengkap` order by `km`.`id` ASC separator ', ') AS `nama_mahasiswa_list`,group_concat(distinct concat(`m`.`nama_lengkap`,' (',`m`.`nim`,')') order by `km`.`id` ASC separator ', ') AS `mahasiswa_detail`,count(distinct `km`.`mahasiswa_id`) AS `jumlah_mahasiswa`,(select `mahasiswa`.`program_studi_kode` from `mahasiswa` where (`mahasiswa`.`id` = (select `mbkm_kegiatan_mahasiswa`.`mahasiswa_id` from `mbkm_kegiatan_mahasiswa` where (`mbkm_kegiatan_mahasiswa`.`kegiatan_id` = `k`.`id`) limit 1))) AS `program_studi_kode`,`k`.`jenis_kegiatan` AS `jenis_kegiatan`,`k`.`tempat_kegiatan` AS `tempat_kegiatan`,`k`.`tanggal_mulai` AS `tanggal_mulai`,`k`.`tanggal_selesai` AS `tanggal_selesai`,`k`.`durasi_minggu` AS `durasi_minggu`,`k`.`sks_dikonversi` AS `sks_dikonversi`,`d`.`nama_lengkap` AS `dosen_pembimbing`,`k`.`status_kegiatan` AS `status_kegiatan`,`k`.`tahun_akademik` AS `tahun_akademik`,`k`.`nilai_type` AS `nilai_type`,`k`.`cpmk_id` AS `cpmk_id`,`k`.`cpl_id` AS `cpl_id`,`cpmk`.`kode_cpmk` AS `kode_cpmk`,`cpmk`.`deskripsi` AS `cpmk_deskripsi`,`cpl`.`kode_cpl` AS `kode_cpl`,`cpl`.`deskripsi` AS `cpl_deskripsi`,`na`.`nilai_angka` AS `nilai_angka`,`na`.`nilai_huruf` AS `nilai_huruf`,`na`.`status_kelulusan` AS `status_kelulusan` from ((((((`mbkm_kegiatan` `k` left join `mbkm_kegiatan_mahasiswa` `km` on((`k`.`id` = `km`.`kegiatan_id`))) left join `mahasiswa` `m` on((`km`.`mahasiswa_id` = `m`.`id`))) left join `dosen` `d` on((`k`.`dosen_pembimbing_id` = `d`.`id`))) left join `mbkm_nilai_akhir` `na` on((`k`.`id` = `na`.`kegiatan_id`))) left join `cpmk` on((`k`.`cpmk_id` = `cpmk`.`id`))) left join `cpl` on((`k`.`cpl_id` = `cpl`.`id`))) group by `k`.`id`,`k`.`judul_kegiatan`,`k`.`jenis_kegiatan`,`k`.`tempat_kegiatan`,`k`.`tanggal_mulai`,`k`.`tanggal_selesai`,`k`.`durasi_minggu`,`k`.`sks_dikonversi`,`d`.`nama_lengkap`,`k`.`status_kegiatan`,`k`.`tahun_akademik`,`k`.`nilai_type`,`k`.`cpmk_id`,`k`.`cpl_id`,`cpmk`.`kode_cpmk`,`cpmk`.`deskripsi`,`cpl`.`kode_cpl`,`cpl`.`deskripsi`,`na`.`nilai_angka`,`na`.`nilai_huruf`,`na`.`status_kelulusan` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-04 15:59:30
