-- MySQL dump 10.13  Distrib 8.0.41, for macos15 (arm64)
--
-- Host: 127.0.0.1    Database: obe_db
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
-- Table structure for table `bahan_kajian`
--

DROP TABLE IF EXISTS `bahan_kajian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_kajian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_bk` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_bk` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_bk` (`kode_bk`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bahan_kajian`
--

LOCK TABLES `bahan_kajian` WRITE;
/*!40000 ALTER TABLE `bahan_kajian` DISABLE KEYS */;
INSERT INTO `bahan_kajian` VALUES (29,'BK06','Data Management','2025-06-14 20:48:49'),(30,'BK07','Parallel and Distributed Computing','2025-06-14 20:48:49'),(31,'BK08','Network and Communication','2025-06-14 20:48:49'),(32,'BK09','Human-Computer Interaction','2025-06-14 20:48:49'),(33,'BK10','Software Engineering','2025-06-14 20:48:49'),(34,'BK11','Operating Systems','2025-06-14 20:48:49'),(35,'BK12','Algoritmics Foundation','2025-06-14 20:48:49'),(36,'BK13','Foundation of Programming Languages','2025-06-14 20:48:49'),(37,'BK14','Programming Fundamentals','2025-06-14 20:48:49'),(38,'BK15','Systems Fundamentals','2025-06-14 20:48:49'),(39,'BK16','Architecture and Organization','2025-06-14 20:48:49'),(40,'BK17','Graphics and Interactive Techniques','2025-06-14 20:48:49'),(41,'BK18','Artificial Intelligence','2025-06-14 20:48:49'),(42,'BK19','Specialized Platform Development','2025-06-14 20:48:49'),(43,'BK20','Mathematical and Statistical Foundations','2025-06-14 20:48:49'),(44,'BK21','Pengembangan Diri','2025-06-14 20:48:49'),(45,'BK22','Metodologi Penelitian','2025-06-14 20:48:49'),(46,'BK01','Social Issues and Professional Practice','2025-07-31 13:16:38'),(47,'BK02','Security','2025-07-31 13:16:59'),(48,'BK03','Project Management','2025-07-31 13:17:32'),(49,'BK04','User Experience Design','2025-07-31 13:17:47'),(50,'BK05','Software Development Fundamentals','2025-07-31 13:18:07'),(51,'BK89','testing','2025-08-10 19:41:38');
/*!40000 ALTER TABLE `bahan_kajian` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bk_mk`
--

LOCK TABLES `bk_mk` WRITE;
/*!40000 ALTER TABLE `bk_mk` DISABLE KEYS */;
INSERT INTO `bk_mk` VALUES (24,29,100),(25,29,101),(26,29,103),(33,33,183),(34,33,216),(31,35,183),(11,44,66),(10,44,68),(36,48,192),(35,48,207);
/*!40000 ALTER TABLE `bk_mk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cpl`
--

DROP TABLE IF EXISTS `cpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_cpl` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_cpl` enum('P','KK','S','KU') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_cpl` (`kode_cpl`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl`
--

LOCK TABLES `cpl` WRITE;
/*!40000 ALTER TABLE `cpl` DISABLE KEYS */;
INSERT INTO `cpl` VALUES (11,'CPL01','Menginternalisasi nilai-nilai Pancasila dalam kehidupan masyarakat yang beragam','P','2025-06-14 20:46:50'),(13,'CPL03','Memiliki kemampuan untuk menerapkan teori matematika dan sistem komputer untuk menyelesaikan berbagai permasalahan dengan prinsip-prinsip computing.','KU','2025-06-14 20:46:50'),(14,'CPL04','Memiliki kemampuan untuk menggunakan dan menerapkan berbagai algoritma/metode untuk memecahkan masalah pada suatu organisasi.','KK','2025-06-14 20:46:50'),(20,'CPL05','Memiliki kemampuan bekerjasama yang baik dalam tim multi displin (A3) serta berkomunikasi (A2) secara efektif, baik secara lisan maupun tulisan (bahan kajian)\r\n','KU','2025-06-15 18:18:25'),(21,'CPL06','Mampu mengambil keputusan (C5) secara tepat dalam konteks penyelesaian masalah di di bidang keahliannya, berdasarkan hasil analisis data','KU','2025-06-22 17:10:57'),(23,'CPL08','Mampu merancang (C6) dan menciptakan (C6) solusi inovatif untuk memecahkan masalah di dunia industri dengan pendekatan sistem cerdas menggunakan algoritma kompleks.','KK','2025-06-22 17:10:57'),(24,'CPL09','Memiliki kemampuan memahami (C2), merancang (C6), mengimplementasikan (C3), mengevaluasi (C5) dan memelihara (C3) sistem komputer, arsitektur komputer, serta jaringan komputer sesuai dengan kebutuhan organisasi','KK','2025-06-22 17:10:57'),(31,'CPL07','Memiliki kemampuan untuk menganalisis (C4), merancang (C6), menerapkan (C3), menguji (C5) dan memelihara (C3) perangkat lunak dengan berbagai kompleksitas','KK','2025-08-28 09:21:09'),(32,'CPL02','Menunjukkan sikap profesional, responsif terhadap perkembangan teknologi dan pemahaman tentang pembelajaran sepanjang hayat','S','2025-08-28 09:24:59');
/*!40000 ALTER TABLE `cpl` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_bk`
--

LOCK TABLES `cpl_bk` WRITE;
/*!40000 ALTER TABLE `cpl_bk` DISABLE KEYS */;
INSERT INTO `cpl_bk` VALUES (4,13,34),(5,13,35),(6,14,35),(7,13,36),(35,14,36),(36,14,37),(37,14,40),(8,11,44),(15,20,44),(27,21,45),(23,11,46),(25,24,47),(26,20,48),(28,21,48);
/*!40000 ALTER TABLE `cpl_bk` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_cpmk`
--

LOCK TABLES `cpl_cpmk` WRITE;
/*!40000 ALTER TABLE `cpl_cpmk` DISABLE KEYS */;
INSERT INTO `cpl_cpmk` VALUES (1,11,108,'2025-08-19 17:15:32','2025-08-19 17:15:32'),(2,11,120,'2025-08-19 17:23:30','2025-08-19 17:23:30'),(7,21,113,'2025-08-28 08:51:20','2025-08-28 08:51:20'),(8,13,111,'2025-08-28 12:01:08','2025-08-28 12:01:08');
/*!40000 ALTER TABLE `cpl_cpmk` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=370 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_mk`
--

LOCK TABLES `cpl_mk` WRITE;
/*!40000 ALTER TABLE `cpl_mk` DISABLE KEYS */;
INSERT INTO `cpl_mk` VALUES (369,11,171),(307,11,181),(309,11,188),(308,11,196),(310,11,203),(311,11,204),(358,11,213),(317,13,182),(318,13,198),(319,13,199),(320,13,200),(321,13,202),(360,13,213),(324,14,166),(325,14,167),(326,14,171),(327,14,183),(328,14,188),(364,20,181),(329,20,192),(330,20,198),(365,20,203),(332,20,204),(333,20,207),(361,20,213),(335,21,166),(336,21,167),(337,21,171),(338,21,183),(339,21,188),(366,21,207),(362,21,213),(347,23,166),(348,23,167),(349,23,171),(350,23,183),(351,23,188),(353,24,166),(354,24,167),(355,24,171),(356,24,183),(357,24,188);
/*!40000 ALTER TABLE `cpl_mk` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_pl`
--

LOCK TABLES `cpl_pl` WRITE;
/*!40000 ALTER TABLE `cpl_pl` DISABLE KEYS */;
INSERT INTO `cpl_pl` VALUES (9,11,6),(1,11,8),(4,13,6),(6,14,6),(13,14,14);
/*!40000 ALTER TABLE `cpl_pl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cpmk`
--

DROP TABLE IF EXISTS `cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpmk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_cpmk` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cpmk_kode` (`kode_cpmk`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpmk`
--

LOCK TABLES `cpmk` WRITE;
/*!40000 ALTER TABLE `cpmk` DISABLE KEYS */;
INSERT INTO `cpmk` VALUES (108,'CPMK011','Mampu menginternalisasi nilai-nilai Pancasila dalam pengambilan keputusan dan penyelesaian masalah sosial\r\n','2025-08-05 21:15:56','2025-08-05 21:15:56'),(109,'CPMK022','Mampu menunjukkan sikap responsif dan adaptif terhadap perkembangan teknologi terkini (responsif teknologi)\r\n','2025-08-05 21:16:51','2025-08-05 21:16:51'),(111,'CPMK033','Mampu menerapkan teori matematika yang dapat menerapkan metode statistik dan probabilistik untuk pengambilan keputusan berbasis data\r\n','2025-08-05 21:20:01','2025-08-05 21:20:01'),(112,'CPMK053','Mampu menulis laporan teknis, dokumentasi, atau karya ilmiah yang terstruktur dan sesuai kaidah kebahasaan dan akademik (berkomunikasi efektif secara tertulis)','2025-08-05 21:21:43','2025-08-05 21:21:43'),(113,'CPMK062','Mampu menerapkan metodologi penelitian untuk pengumpulan dan analisis data secara sistematis.\r\n','2025-08-05 21:23:13','2025-08-05 21:23:13'),(115,'CPMK041','Mampu menerapkan algoritma dan metode pemrograman untuk membangun solusi perangkat lunak yang efisien terhadap permasalahan fungsional organisasi','2025-08-07 16:43:38','2025-08-07 16:43:38'),(116,'CPMK071','ini testing','2025-08-08 03:33:20','2025-08-08 03:35:49'),(120,'CPMK012','testing','2025-08-10 14:10:46','2025-08-10 14:10:46'),(121,'CPMK013','testing','2025-08-10 14:11:02','2025-08-10 14:11:02'),(122,'CPMK014','Testing saja','2025-10-11 00:00:52','2025-10-11 00:00:52'),(123,'CPMK015','Testing','2025-10-11 00:00:59','2025-10-11 00:00:59');
/*!40000 ALTER TABLE `cpmk` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=999 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpmk_mk`
--

LOCK TABLES `cpmk_mk` WRITE;
/*!40000 ALTER TABLE `cpmk_mk` DISABLE KEYS */;
INSERT INTO `cpmk_mk` VALUES (968,108,213),(977,109,196),(993,111,199),(933,111,202),(934,111,213),(985,113,213),(946,115,183),(976,116,216),(998,120,196);
/*!40000 ALTER TABLE `cpmk_mk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dosen`
--

DROP TABLE IF EXISTS `dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan_fungsional` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_keaktifan` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nip` (`nip`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  CONSTRAINT `fk_dosen_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dosen`
--

LOCK TABLES `dosen` WRITE;
/*!40000 ALTER TABLE `dosen` DISABLE KEYS */;
INSERT INTO `dosen` VALUES (2,NULL,'1234','Ibu Lala S.Pd M.Pd','','Aktif','2025-08-27 08:49:33','2025-09-19 14:14:32'),(4,16,'12345','Bapa A','Lektor Kepala','Aktif','2025-08-27 16:43:34','2025-09-19 14:15:17'),(6,NULL,'123','Ibu Ibu an','Dosen Pengajar','Aktif','2025-09-19 12:56:59','2025-09-19 13:17:19');
/*!40000 ALTER TABLE `dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_dosen`
--

DROP TABLE IF EXISTS `jadwal_dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jadwal_mengajar_id` int NOT NULL,
  `dosen_id` int NOT NULL,
  `role` enum('leader','member') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal_dosen` (`jadwal_mengajar_id`,`dosen_id`),
  KEY `idx_jadwal_id` (`jadwal_mengajar_id`),
  KEY `idx_dosen_id` (`dosen_id`),
  KEY `idx_role` (`role`),
  KEY `idx_dosen_role_jadwal` (`dosen_id`,`role`),
  CONSTRAINT `fk_jadwal_dosen_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_dosen_jadwal` FOREIGN KEY (`jadwal_mengajar_id`) REFERENCES `jadwal_mengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_dosen`
--

LOCK TABLES `jadwal_dosen` WRITE;
/*!40000 ALTER TABLE `jadwal_dosen` DISABLE KEYS */;
INSERT INTO `jadwal_dosen` VALUES (29,12,2,'leader','2025-09-18 13:27:24'),(30,12,4,'member','2025-09-18 13:27:24'),(33,9,2,'member','2025-09-18 13:36:42'),(34,9,4,'member','2025-09-18 13:36:42'),(35,11,2,'leader','2025-09-18 13:36:54'),(40,13,2,'leader','2025-09-19 02:08:08'),(42,13,4,'member','2025-09-19 02:08:08'),(45,15,4,'leader','2025-09-19 14:19:45'),(46,15,6,'member','2025-09-19 14:19:45'),(48,17,2,'leader','2025-09-25 03:47:28'),(49,16,6,'leader','2025-09-25 04:25:34'),(50,16,4,'member','2025-09-25 04:25:34'),(51,16,2,'member','2025-09-25 04:25:34');
/*!40000 ALTER TABLE `jadwal_dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_mengajar`
--

DROP TABLE IF EXISTS `jadwal_mengajar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_mengajar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` int NOT NULL,
  `program_studi` enum('Teknik Informatika','Sistem Informasi','Teknik Komputer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Teknik Informatika',
  `tahun_akademik` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(5) COLLATE utf8mb4_general_ci DEFAULT 'A',
  `ruang` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('active','inactive','completed') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal` (`mata_kuliah_id`,`tahun_akademik`,`kelas`),
  KEY `idx_program_studi` (`program_studi`),
  KEY `idx_tahun_akademik` (`tahun_akademik`),
  KEY `idx_status` (`status`),
  KEY `idx_jadwal_semester` (`tahun_akademik`,`program_studi`),
  KEY `idx_jadwal_waktu` (`hari`,`jam_mulai`),
  CONSTRAINT `fk_jadwal_mata_kuliah` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_mengajar`
--

LOCK TABLES `jadwal_mengajar` WRITE;
/*!40000 ALTER TABLE `jadwal_mengajar` DISABLE KEYS */;
INSERT INTO `jadwal_mengajar` VALUES (9,215,'Sistem Informasi','2025/2026 Genap','C','Ruang FT-8','Senin','13:30:00','13:25:00','active','2025-09-17 17:05:16','2025-09-17 17:05:16'),(11,216,'Teknik Informatika','2025/2026 Genap','A','FT-8','Rabu','11:30:00','11:45:00','active','2025-09-17 17:51:23','2025-09-18 13:36:54'),(12,199,'Teknik Informatika','2025/2026 Genap','A','FT-1','Senin','15:30:00','16:45:00','active','2025-09-18 13:27:24','2025-09-18 13:27:24'),(13,200,'Teknik Informatika','2025/2026 Genap','B','Audit','Sabtu','12:30:00','14:45:00','active','2025-09-19 02:06:43','2025-09-19 02:08:08'),(15,214,'Teknik Informatika','2025/2026 Ganjil','A',NULL,'Selasa',NULL,NULL,'active','2025-09-19 14:19:45','2025-09-19 14:19:45'),(16,213,'Teknik Informatika','2025/2026 Genap','A',NULL,'Senin',NULL,NULL,'active','2025-09-24 17:40:46','2025-09-24 17:40:46'),(17,171,'Teknik Informatika','2025/2026 Genap','A',NULL,NULL,NULL,NULL,'active','2025-09-25 03:47:28','2025-09-25 03:47:28');
/*!40000 ALTER TABLE `jadwal_mengajar` ENABLE KEYS */;
UNLOCK TABLES;

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
  `program_studi` enum('Teknik Informatika','Sistem Informasi','Teknik Komputer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tahun_angkatan` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_mahasiswa` enum('Aktif','Cuti','Lulus','Mengundurkan Diri','DO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nim` (`nim`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  CONSTRAINT `fk_mahasiswa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa`
--

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES (1,NULL,'JTE2024001','Aditya Pratama','Teknik Informatika','2024','Aktif','2025-09-19 22:44:06','2025-10-15 07:08:24'),(2,NULL,'JTE2024002','Bella Safitri','Teknik Informatika','2024','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(3,NULL,'JTE2023010','Candra Gunawan','Teknik Informatika','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(4,NULL,'JTE2023011','Dian Lestari','Teknik Informatika','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(5,NULL,'JTE2022025','Eko Wahyudi','Teknik Informatika','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(6,NULL,'JTE2022026','Fitriani','Teknik Informatika','2022','Cuti','2025-09-19 22:44:06','2025-09-19 22:44:06'),(7,NULL,'JTE2021005','Gilang Ramadhan','Teknik Informatika','2021','Lulus','2025-09-19 22:44:06','2025-09-19 22:44:06'),(8,NULL,'JSI2024001','Hesti Puspita','Sistem Informasi','2024','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(9,NULL,'JSI2024002','Indra Setiawan','Sistem Informasi','2024','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(10,NULL,'JSI2023008','Joko Susilo','Sistem Informasi','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(11,NULL,'JSI2023009','Kartika Dewi','Sistem Informasi','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(12,NULL,'JSI2022018','Lia Amelia','Sistem Informasi','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(13,NULL,'JSI2022019','Muhammad Fauzi','Sistem Informasi','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(14,NULL,'JSI2021003','Nadia Putri','Sistem Informasi','2021','Lulus','2025-09-19 22:44:06','2025-09-19 22:44:06'),(15,NULL,'JKO2024001','Oscar Maulana','Teknik Komputer','2024','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(16,NULL,'JKO2023005','Putri Wulandari','Teknik Komputer','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(17,NULL,'JKO2023006','Rian Hidayat','Teknik Komputer','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(18,NULL,'JKO2022011','Siska Anggraini','Teknik Komputer','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(19,NULL,'JKO2022012','Tegar Firmansyah','Teknik Komputer','2022','Mengundurkan Diri','2025-09-19 22:44:06','2025-09-19 22:44:06'),(22,18,'213030503137','Mayrika Chinta','Teknik Informatika','2025','Aktif','2025-10-15 07:09:17','2025-10-15 07:09:37');
/*!40000 ALTER TABLE `mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mata_kuliah`
--

DROP TABLE IF EXISTS `mata_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_kuliah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_mk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi_singkat` text COLLATE utf8mb4_general_ci NOT NULL,
  `tipe` enum('wajib','pilihan') COLLATE utf8mb4_general_ci NOT NULL,
  `semester` tinyint NOT NULL,
  `sks` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategori` enum('wajib_teori','wajib_praktikum','pilihan','mkwk') COLLATE utf8mb4_general_ci DEFAULT 'wajib_teori',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_mk` (`kode_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_kuliah`
--

LOCK TABLES `mata_kuliah` WRITE;
/*!40000 ALTER TABLE `mata_kuliah` DISABLE KEYS */;
INSERT INTO `mata_kuliah` VALUES (166,'1DCP171030','Aljabar Linier dan Matriks','Mempelajari aljabar linier dan aplikasi matriks.','wajib',1,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(167,'1DCP561030','Arsitektur dan Organisasi Komputer','Dasar-dasar arsitektur komputer.','wajib',1,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(171,'1DCP182032','Struktur Data','Mempelajari struktur data dalam pemrograman.','wajib',2,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(181,'1DCP523020','Bahasa Inggris','Bahasa Inggris dasar.','wajib',3,2,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(182,'1DCP573032','Basis Data I','Pengantar basis data.','wajib',3,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(183,'1DCP213032','Pemrograman Berorientasi Obyek','Dasar-dasar pemrograman berorientasi objek.','wajib',3,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(188,'1DCU024020','Kewarganegaraan','Mata kuliah kewarganegaraan.','wajib',4,2,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(192,'1DCP874030','Manajemen Proyek','Dasar manajemen proyek perangkat lunak.','wajib',4,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(196,'1DCU105020','Pancasila','Mata kuliah Pancasila.','wajib',5,2,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(198,'1DCP665030','Technoprenuership','Dasar technopreneurship.','wajib',5,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(199,'1DCP905030','Keamanan Data dan Informasi','Dasar keamanan data dan informasi.','wajib',5,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(200,'1DCP855030','Jaringan Syaraf Tiruan','Dasar jaringan syaraf tiruan.','wajib',5,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(202,'1DCP685032','Data Mining','Dasar data mining.','wajib',5,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(203,'1DCU116020','Bahasa Indonesia','Bahasa Indonesia dasar.','wajib',6,2,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(204,'1DCP936030','Pembelajaran Mesin','Dasar pembelajaran mesin.','wajib',6,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(207,'1DCP830030','Program Profesional','Program profesional komputer.','wajib',6,3,'2025-08-04 13:46:57','2025-08-04 13:46:57','wajib_teori'),(213,'1DCP115030','Metode penelitian','Mata kuliah metpen','wajib',5,3,'2025-08-05 20:52:10','2025-08-05 20:52:10','mkwk'),(214,'Idcp190019','algoritma dan pemrograman II','UNTUK MATKUL ALPRO II','wajib',2,3,'2025-08-07 16:53:48','2025-08-07 16:53:48','wajib_praktikum'),(215,'1DCP191032','Algoritma dan Pemrograman I','Mata kuliah Alpro I','wajib',1,3,'2025-08-08 03:14:26','2025-08-08 03:14:26','wajib_praktikum'),(216,'1DCP353030','IMK','Mata kuliah imk / kompleksitas algoritma','wajib',3,3,'2025-08-08 03:31:21','2025-08-08 03:31:21','wajib_teori');
/*!40000 ALTER TABLE `mata_kuliah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mbkm_jenis_kegiatan`
--

DROP TABLE IF EXISTS `mbkm_jenis_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_jenis_kegiatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_kegiatan` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kegiatan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `sks_konversi` int NOT NULL DEFAULT '20',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode_kegiatan` (`kode_kegiatan`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_jenis_kegiatan`
--

LOCK TABLES `mbkm_jenis_kegiatan` WRITE;
/*!40000 ALTER TABLE `mbkm_jenis_kegiatan` DISABLE KEYS */;
INSERT INTO `mbkm_jenis_kegiatan` VALUES (1,'MBKM01','Magang/Praktik Kerja','Kegiatan magang di industri/instansi',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(2,'MBKM02','Pertukaran Pelajar','Program pertukaran mahasiswa',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(3,'MBKM03','Asistensi Mengajar di Satuan Pendidikan','Mengajar di sekolah',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(4,'MBKM04','Penelitian/Riset','Kegiatan penelitian',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(5,'MBKM05','Proyek Kemanusiaan','Kegiatan sosial kemasyarakatan',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(6,'MBKM06','Kegiatan Wirausaha','Membangun startup/usaha',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(7,'MBKM07','Studi/Proyek Independen','Proyek mandiri bersertifikat',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04'),(8,'MBKM08','Membangun Desa/KKN Tematik','KKN dengan tema khusus',20,'aktif','2025-10-14 15:13:04','2025-10-14 15:13:04');
/*!40000 ALTER TABLE `mbkm_jenis_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mbkm_kegiatan`
--

DROP TABLE IF EXISTS `mbkm_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_kegiatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis_kegiatan_id` int NOT NULL,
  `judul_kegiatan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_kegiatan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pembimbing_lapangan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kontak_pembimbing` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dosen_pembimbing_id` int DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi_minggu` int DEFAULT NULL,
  `sks_dikonversi` int DEFAULT '20',
  `deskripsi_kegiatan` text COLLATE utf8mb4_general_ci,
  `dokumen_pendukung` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_kegiatan` enum('diajukan','disetujui','ditolak','berlangsung','selesai') COLLATE utf8mb4_general_ci DEFAULT 'diajukan',
  `tahun_akademik` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`),
  KEY `idx_dosen_pembimbing` (`dosen_pembimbing_id`),
  KEY `idx_status` (`status_kegiatan`),
  CONSTRAINT `fk_mbkm_dosen` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mbkm_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_kegiatan`
--

LOCK TABLES `mbkm_kegiatan` WRITE;
/*!40000 ALTER TABLE `mbkm_kegiatan` DISABLE KEYS */;
INSERT INTO `mbkm_kegiatan` VALUES (1,1,'Magang sebagai Software Developer di PT. Tech Startup Indonesia','PT. Tech Startup Indonesia, Jakarta','Budi Santoso, S.Kom','budi.santoso@techstartup.id',2,'2025-02-01','2025-07-31',26,20,'Mengembangkan aplikasi mobile dan web menggunakan Flutter dan Laravel. Terlibat dalam full-stack development dan berkolaborasi dengan tim product.',NULL,'selesai','2024/2025 Genap','2025-10-14 15:28:19','2025-10-14 15:28:19'),(2,4,'Penelitian: Implementasi Machine Learning untuk Prediksi Cuaca','Laboratorium Komputasi, Fakultas Teknik UPR','Dr. Siti Rahmawati, M.Kom','siti.rahmawati@upr.ac.id',4,'2025-03-01','2025-08-31',26,20,'Penelitian menggunakan algoritma Random Forest dan LSTM untuk memprediksi cuaca lokal Kalimantan Tengah. Melakukan pengumpulan data, preprocessing, modeling, dan evaluasi.',NULL,'disetujui','2024/2025 Genap','2025-10-14 15:28:19','2025-10-14 15:28:19'),(3,3,'Asistensi Mengajar di SMA Negeri 1 Palangka Raya','SMA Negeri 1 Palangka Raya','Drs. Ahmad Hidayat','ahmad.h@sman1.sch.id',2,'2025-01-15','2025-06-30',24,20,'Membantu mengajar mata pelajaran Informatika untuk kelas X dan XI. Membuat materi pembelajaran interaktif dan membimbing siswa dalam project-based learning.',NULL,'berlangsung','2024/2025 Genap','2025-10-14 15:28:19','2025-10-14 15:28:19'),(4,2,'Pertukaran Pelajar ke Universitas Gadjah Mada','Universitas Gadjah Mada, Yogyakarta','Dr. Bambang Sutrisno, M.T','bambang.s@ugm.ac.id',4,'2025-02-10','2025-07-20',23,20,'Mengambil mata kuliah Computer Vision, Big Data Analytics, dan Cloud Computing di program studi Ilmu Komputer UGM.',NULL,'disetujui','2024/2025 Genap','2025-10-14 15:28:19','2025-10-14 15:28:19'),(5,6,'Membangun Startup: EduTech Platform untuk Pembelajaran Online','Inkubator Bisnis UPR','Ir. Rudi Hartono, M.M','rudi.h@upr.ac.id',2,'2025-03-01','2025-08-31',26,20,'Mengembangkan platform pembelajaran online berbasis web dan mobile. Melakukan riset pasar, product development, dan customer acquisition.',NULL,'berlangsung','2024/2025 Genap','2025-10-14 15:28:19','2025-10-14 15:28:19'),(10,5,'Magang jadi Pengangguran','Testing','','',6,'2025-01-01','2025-02-01',5,20,'',NULL,'selesai','2025/2026 Ganjil','2025-10-15 16:03:48','2025-10-15 09:24:51'),(13,2,'Pete Pete an','Jakarta ','','',6,'2025-01-01','2025-02-01',5,20,'Gatau bro jangan tanya',NULL,'diajukan','2025/2026','2025-10-15 09:37:35','2025-10-15 09:41:04');
/*!40000 ALTER TABLE `mbkm_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

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
  `peran` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Role in the activity: Ketua, Anggota, etc',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan_mahasiswa` (`kegiatan_id`,`mahasiswa_id`),
  KEY `idx_kegiatan` (`kegiatan_id`),
  KEY `idx_mahasiswa` (`mahasiswa_id`),
  KEY `idx_kegiatan_mahasiswa_combined` (`kegiatan_id`,`mahasiswa_id`),
  CONSTRAINT `fk_kegiatan_mhs_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kegiatan_mhs_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_kegiatan_mahasiswa`
--

LOCK TABLES `mbkm_kegiatan_mahasiswa` WRITE;
/*!40000 ALTER TABLE `mbkm_kegiatan_mahasiswa` DISABLE KEYS */;
INSERT INTO `mbkm_kegiatan_mahasiswa` VALUES (1,1,1,'Peserta','2025-10-14 16:43:46'),(2,2,2,'Peserta','2025-10-14 16:43:46'),(3,3,3,'Peserta','2025-10-14 16:43:46'),(4,4,4,'Peserta','2025-10-14 16:43:46'),(5,5,5,'Peserta','2025-10-14 16:43:46'),(21,10,22,'Peserta','2025-10-15 16:03:48'),(26,13,1,'Peserta','2025-10-15 16:41:04'),(27,13,2,'Peserta','2025-10-15 16:41:04'),(28,13,22,'Peserta','2025-10-15 16:41:04');
/*!40000 ALTER TABLE `mbkm_kegiatan_mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mbkm_komponen_nilai`
--

DROP TABLE IF EXISTS `mbkm_komponen_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbkm_komponen_nilai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis_kegiatan_id` int NOT NULL,
  `nama_komponen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`),
  CONSTRAINT `fk_komponen_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_komponen_nilai`
--

LOCK TABLES `mbkm_komponen_nilai` WRITE;
/*!40000 ALTER TABLE `mbkm_komponen_nilai` DISABLE KEYS */;
INSERT INTO `mbkm_komponen_nilai` VALUES (1,1,'Kehadiran dan Kedisiplinan',20.00,'Penilaian kehadiran dan kedisiplinan selama magang','2025-10-14 15:13:04'),(2,1,'Kinerja dan Produktivitas',30.00,'Penilaian hasil kerja dan produktivitas','2025-10-14 15:13:04'),(3,1,'Sikap dan Etika Kerja',20.00,'Penilaian sikap profesional dan etika','2025-10-14 15:13:04'),(4,1,'Laporan Akhir',30.00,'Penilaian laporan akhir kegiatan','2025-10-14 15:13:04'),(5,4,'Proposal dan Metodologi',20.00,'Penilaian proposal penelitian dan metodologi yang digunakan','2025-10-14 15:28:19'),(6,4,'Pelaksanaan Penelitian',25.00,'Penilaian proses pelaksanaan penelitian','2025-10-14 15:28:19'),(7,4,'Analisis Data dan Hasil',30.00,'Penilaian analisis data dan interpretasi hasil','2025-10-14 15:28:19'),(8,4,'Publikasi/Laporan Akhir',25.00,'Penilaian paper atau laporan penelitian','2025-10-14 15:28:19'),(9,3,'Kehadiran dan Kedisiplinan',15.00,'Penilaian kehadiran dan ketepatan waktu','2025-10-14 15:28:19'),(10,3,'Kemampuan Mengajar',35.00,'Penilaian kemampuan menyampaikan materi','2025-10-14 15:28:19'),(11,3,'Interaksi dengan Siswa',25.00,'Penilaian komunikasi dan bimbingan kepada siswa','2025-10-14 15:28:19'),(12,3,'Kreativitas dan Inovasi',25.00,'Penilaian metode mengajar dan media pembelajaran','2025-10-14 15:28:19'),(13,2,'Nilai Mata Kuliah',50.00,'Nilai rata-rata mata kuliah yang diambil','2025-10-14 15:28:19'),(14,2,'Partisipasi Akademik',20.00,'Penilaian keaktifan dan partisipasi','2025-10-14 15:28:19'),(15,2,'Adaptasi dan Kemandirian',15.00,'Penilaian kemampuan beradaptasi','2025-10-14 15:28:19'),(16,2,'Laporan Akhir',15.00,'Penilaian laporan kegiatan pertukaran','2025-10-14 15:28:19'),(17,6,'Business Model & Planning',25.00,'Penilaian model bisnis dan perencanaan','2025-10-14 15:28:19'),(19,6,'Marketing & Customer Acquisition',20.00,'Penilaian strategi pemasaran dan perolehan pelanggan','2025-10-14 15:28:19'),(20,6,'Financial & Sustainability',25.00,'Penilaian aspek keuangan dan keberlanjutan','2025-10-14 15:28:19'),(21,5,'Perencanaan Program',20.00,'Penilaian perencanaan dan persiapan','2025-10-14 15:28:19'),(22,5,'Pelaksanaan Kegiatan',30.00,'Penilaian pelaksanaan program','2025-10-14 15:28:19'),(23,5,'Kolaborasi dan Kepemimpinan',25.00,'Penilaian kerjasama tim dan leadership','2025-10-14 15:28:19'),(24,5,'Dampak dan Keberlanjutan',25.00,'Penilaian dampak program terhadap masyarakat','2025-10-14 15:28:19'),(25,7,'Proposal dan Rencana Kerja',20.00,'Penilaian proposal dan timeline','2025-10-14 15:28:19'),(26,7,'Pelaksanaan dan Progres',30.00,'Penilaian kemajuan pelaksanaan','2025-10-14 15:28:19'),(27,7,'Hasil dan Deliverables',35.00,'Penilaian hasil akhir dan deliverables','2025-10-14 15:28:19'),(28,7,'Presentasi dan Dokumentasi',15.00,'Penilaian presentasi dan dokumentasi','2025-10-14 15:28:19'),(29,8,'Identifikasi Masalah',20.00,'Penilaian analisis kebutuhan masyarakat','2025-10-14 15:28:19'),(30,8,'Program dan Implementasi',35.00,'Penilaian pelaksanaan program','2025-10-14 15:28:19'),(31,8,'Kebermanfaatan Program',25.00,'Penilaian manfaat program bagi masyarakat','2025-10-14 15:28:19'),(32,8,'Laporan dan Dokumentasi',20.00,'Penilaian laporan kegiatan','2025-10-14 15:28:19'),(33,6,'Product Development',30.00,'Penilaian pengembangan produk/layanan','2025-10-14 16:24:52');
/*!40000 ALTER TABLE `mbkm_komponen_nilai` ENABLE KEYS */;
UNLOCK TABLES;

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
  `catatan` text COLLATE utf8mb4_general_ci,
  `penilai` enum('pembimbing_lapangan','dosen_pembimbing','admin') COLLATE utf8mb4_general_ci DEFAULT 'dosen_pembimbing',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan_komponen` (`kegiatan_id`,`komponen_id`),
  KEY `idx_kegiatan` (`kegiatan_id`),
  KEY `idx_komponen` (`komponen_id`),
  CONSTRAINT `fk_nilai_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_nilai_komponen` FOREIGN KEY (`komponen_id`) REFERENCES `mbkm_komponen_nilai` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_nilai`
--

LOCK TABLES `mbkm_nilai` WRITE;
/*!40000 ALTER TABLE `mbkm_nilai` DISABLE KEYS */;
INSERT INTO `mbkm_nilai` VALUES (1,1,1,90.00,'Selalu hadir tepat waktu dan menunjukkan dedikasi tinggi','dosen_pembimbing','2025-10-14 15:28:19','2025-10-14 15:28:19'),(2,1,2,85.00,'Mampu menyelesaikan task dengan baik dan tepat waktu','dosen_pembimbing','2025-10-14 15:28:19','2025-10-14 15:28:19'),(3,1,3,88.00,'Menunjukkan sikap profesional dan mudah beradaptasi','dosen_pembimbing','2025-10-14 15:28:19','2025-10-14 15:28:19'),(4,1,4,87.00,'Laporan lengkap dan terstruktur dengan baik','dosen_pembimbing','2025-10-14 15:28:19','2025-10-14 15:28:19'),(5,10,21,85.00,'','dosen_pembimbing','2025-10-15 16:24:51','2025-10-15 16:24:51'),(6,10,22,85.00,'','dosen_pembimbing','2025-10-15 16:24:51','2025-10-15 16:24:51'),(7,10,23,80.00,'','dosen_pembimbing','2025-10-15 16:24:51','2025-10-15 16:24:51'),(8,10,24,95.00,'','dosen_pembimbing','2025-10-15 16:24:51','2025-10-15 16:24:51');
/*!40000 ALTER TABLE `mbkm_nilai` ENABLE KEYS */;
UNLOCK TABLES;

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
  `nilai_huruf` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `status_kelulusan` enum('Lulus','Tidak Lulus') COLLATE utf8mb4_general_ci DEFAULT 'Lulus',
  `catatan_akhir` text COLLATE utf8mb4_general_ci,
  `tanggal_penilaian` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan` (`kegiatan_id`),
  CONSTRAINT `fk_nilai_akhir_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_nilai_akhir`
--

LOCK TABLES `mbkm_nilai_akhir` WRITE;
/*!40000 ALTER TABLE `mbkm_nilai_akhir` DISABLE KEYS */;
INSERT INTO `mbkm_nilai_akhir` VALUES (1,1,87.10,'A','Lulus','Mahasiswa menunjukkan performa sangat baik selama magang. Mendapat apresiasi dari pembimbing lapangan.','2025-08-15','2025-10-14 15:28:19','2025-10-14 15:28:19'),(2,10,86.25,'A','Lulus',NULL,'2025-10-15','2025-10-15 16:24:51','2025-10-15 16:24:51');
/*!40000 ALTER TABLE `mbkm_nilai_akhir` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mk_prasyarat`
--

LOCK TABLES `mk_prasyarat` WRITE;
/*!40000 ALTER TABLE `mk_prasyarat` DISABLE KEYS */;
INSERT INTO `mk_prasyarat` VALUES (28,182,171),(33,191,182),(38,200,185),(41,204,200),(43,207,182),(44,212,207),(46,183,214),(47,214,215);
/*!40000 ALTER TABLE `mk_prasyarat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai_cpmk_mahasiswa`
--

DROP TABLE IF EXISTS `nilai_cpmk_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_cpmk_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL COMMENT 'FK to the mahasiswa table',
  `jadwal_mengajar_id` int NOT NULL COMMENT 'FK to the specific class offering in jadwal_mengajar',
  `cpmk_id` int NOT NULL COMMENT 'FK to the cpmk table',
  `nilai_cpmk` decimal(5,2) NOT NULL COMMENT 'The numerical score achieved for this CPMK (0-100)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mahasiswa_cpmk_in_class` (`mahasiswa_id`,`jadwal_mengajar_id`,`cpmk_id`),
  KEY `fk_nilai_cpmk_mahasiswa` (`mahasiswa_id`),
  KEY `fk_nilai_cpmk_jadwal` (`jadwal_mengajar_id`),
  KEY `fk_nilai_cpmk_cpmk` (`cpmk_id`),
  CONSTRAINT `fk_nilai_cpmk_cpmk_id` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_cpmk_jadwal_id` FOREIGN KEY (`jadwal_mengajar_id`) REFERENCES `jadwal_mengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_cpmk_mahasiswa_id` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_cpmk_mahasiswa`
--

LOCK TABLES `nilai_cpmk_mahasiswa` WRITE;
/*!40000 ALTER TABLE `nilai_cpmk_mahasiswa` DISABLE KEYS */;
INSERT INTO `nilai_cpmk_mahasiswa` VALUES (1,5,16,108,75.00,'2025-09-24 10:44:02','2025-10-12 06:47:44'),(2,5,16,120,75.00,'2025-09-24 10:44:02','2025-09-24 22:02:38'),(4,5,16,111,75.00,'2025-09-24 10:44:02','2025-10-12 06:47:44'),(5,5,16,113,75.00,'2025-09-24 10:44:02','2025-10-12 06:47:44'),(6,3,16,108,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(7,3,16,120,75.00,'2025-09-24 10:51:41','2025-09-24 22:02:38'),(9,3,16,111,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(10,3,16,113,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(11,4,16,108,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(12,4,16,120,75.00,'2025-09-24 10:51:41','2025-09-24 22:02:38'),(14,4,16,111,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(15,4,16,113,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(16,1,16,108,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(17,1,16,120,75.00,'2025-09-24 10:51:41','2025-09-24 22:02:38'),(19,1,16,111,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(20,1,16,113,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(21,2,16,108,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(22,2,16,120,75.00,'2025-09-24 10:51:41','2025-09-24 22:02:38'),(24,2,16,111,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(25,2,16,113,75.00,'2025-09-24 10:51:41','2025-10-12 06:47:44');
/*!40000 ALTER TABLE `nilai_cpmk_mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai_mahasiswa`
--

DROP TABLE IF EXISTS `nilai_mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL COMMENT 'Foreign key to the mahasiswa table',
  `jadwal_mengajar_id` int NOT NULL COMMENT 'Foreign key to the jadwal_mengajar table to identify the specific class',
  `nilai_akhir` decimal(5,2) DEFAULT NULL COMMENT 'Final numerical score, e.g., 85.50',
  `nilai_huruf` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Letter grade, e.g., A, B+, C',
  `status_kelulusan` enum('Lulus','Tidak Lulus','Diproses') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Diproses',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Optional notes from the lecturer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mahasiswa_jadwal` (`mahasiswa_id`,`jadwal_mengajar_id`),
  KEY `fk_nilai_mahasiswa` (`mahasiswa_id`),
  KEY `fk_nilai_jadwal` (`jadwal_mengajar_id`),
  KEY `idx_nilai_huruf` (`nilai_huruf`),
  KEY `idx_status_kelulusan` (`status_kelulusan`),
  CONSTRAINT `fk_nilai_jadwal_id` FOREIGN KEY (`jadwal_mengajar_id`) REFERENCES `jadwal_mengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_mahasiswa_id` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_mahasiswa`
--

LOCK TABLES `nilai_mahasiswa` WRITE;
/*!40000 ALTER TABLE `nilai_mahasiswa` DISABLE KEYS */;
INSERT INTO `nilai_mahasiswa` VALUES (16,5,16,75.00,'AB','Lulus',NULL,'2025-09-24 10:44:02','2025-10-12 06:47:44'),(46,3,16,75.00,'AB','Lulus',NULL,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(47,4,16,75.00,'AB','Lulus',NULL,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(48,1,16,75.00,'AB','Lulus',NULL,'2025-09-24 10:51:41','2025-10-12 06:47:44'),(49,2,16,75.00,'AB','Lulus',NULL,'2025-09-24 10:51:42','2025-10-12 06:47:44');
/*!40000 ALTER TABLE `nilai_mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profil_lulusan`
--

DROP TABLE IF EXISTS `profil_lulusan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_lulusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_pl` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pl` (`kode_pl`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profil_lulusan`
--

LOCK TABLES `profil_lulusan` WRITE;
/*!40000 ALTER TABLE `profil_lulusan` DISABLE KEYS */;
INSERT INTO `profil_lulusan` VALUES (6,'PL01','Lulusan memiliki kemampuan untuk menganalisa dan menyelesaikan berbagai permasalahan dengan prinsip-prinsip computing.','2025-06-14 20:45:24'),(7,'PL02','Lulusan memiliki kemampuan menganalisis, merancang, dan mengimplementasikan perangkat lunak serta solusi berbasis komputasi, termasuk kecerdasan buatan, yang sesuai dengan kebutuhan pengguna','2025-06-14 20:45:24'),(8,'PL03','Lulusan mampu bertindak dan menilai secara profesional sesuai dengan nilai-nilai Pancasila','2025-06-14 20:45:24'),(14,'PL04','Lulusan mampu berpikir logis, kritis serta sistematis dalam memanfaatkan ilmu pengetahuan informatika/ ilmu komputer untuk menyelesaikan masalah nyata.1','2025-08-05 21:10:22');
/*!40000 ALTER TABLE `profil_lulusan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profil_prodi`
--

DROP TABLE IF EXISTS `profil_prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_prodi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_universitas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_fakultas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_prodi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_ketua_prodi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nip_ketua_prodi` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_dekan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nip_dekan` char(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profil_prodi`
--

LOCK TABLES `profil_prodi` WRITE;
/*!40000 ALTER TABLE `profil_prodi` DISABLE KEYS */;
INSERT INTO `profil_prodi` VALUES (3,'UNIVERSITAS PALANGKA RAYA','TEKNIK','TEKNIK INFORMATIKA','Ariesta Lestari, S.Kom., M.Cs., PhD','198003222005012004','Frieda, S.T., M.T','197212231997022002','image.png','2025-08-28 02:14:43');
/*!40000 ALTER TABLE `profil_prodi` ENABLE KEYS */;
UNLOCK TABLES;

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
  `tahun_ajaran` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_penyusunan` date DEFAULT NULL,
  `status` enum('draft','final','arsip') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `catatan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `rps_ibfk_1` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps`
--

LOCK TABLES `rps` WRITE;
/*!40000 ALTER TABLE `rps` DISABLE KEYS */;
INSERT INTO `rps` VALUES (14,213,5,'2024/2025','2025-08-23','draft','','2025-08-22 17:30:43','2025-08-28 10:54:10'),(15,199,5,'2024/2025','2025-02-22','draft','test','2025-08-28 11:59:10','2025-08-28 11:59:21');
/*!40000 ALTER TABLE `rps` ENABLE KEYS */;
UNLOCK TABLES;

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
  `tahap_penilaian` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sub_cpmk_id` int NOT NULL,
  `indikator` text COLLATE utf8mb4_general_ci,
  `kriteria_penilaian` text COLLATE utf8mb4_general_ci,
  `teknik_penilaian` text COLLATE utf8mb4_general_ci,
  `instrumen` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `materi_pembelajaran` text COLLATE utf8mb4_general_ci,
  `metode` text COLLATE utf8mb4_general_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps_mingguan`
--

LOCK TABLES `rps_mingguan` WRITE;
/*!40000 ALTER TABLE `rps_mingguan` DISABLE KEYS */;
INSERT INTO `rps_mingguan` VALUES (23,15,1,13,111,'[\"Tengah Semester\"]',77,'[\"test2\"]','[\"test\"]','{\"tes_tulis_uts\":5}','[\"test\"]','test','[\"Team Base Project\"]',5,'2025-08-28 12:22:01'),(29,14,1,11,108,'[\"Perkuliahan\"]',70,'[\"tetst\",\"test2\"]','[\"Kehadiran\"]','{\"partisipasi\":6}','[\"test\"]','test','[\"Case study\",\"Kuliah\",\"Seminar atau yang setara\"]',6,'2025-09-02 12:27:14'),(30,14,2,11,108,'[\"Perkuliahan\"]',71,'[\"tetyy\"]','[\"Ketepatan Jawaban Tugas\"]','{\"observasi\":4}','[\"tttt\"]','test','[\"Case study\"]',4,'2025-09-02 12:43:12'),(31,14,3,13,111,'[\"Tengah Semester\"]',80,'[\"test\"]','[\"Ketepatan Jawaban UTS\"]','{\"tes_tulis_uts\":50}','[]','UTS','[\"Praktik Lapangan\"]',50,'2025-10-11 07:09:08'),(32,14,4,21,113,'[\"Akhir Semester\"]',81,'[\"UTS\"]','[\"Ketepatan Jawaban UAS\"]','{\"tes_tulis_uas\":40}','[]','','[\"Case study\"]',40,'2025-10-11 07:30:08');
/*!40000 ALTER TABLE `rps_mingguan` ENABLE KEYS */;
UNLOCK TABLES;

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
  `peran` enum('pengampu','koordinator','penyusun') COLLATE utf8mb4_general_ci DEFAULT 'pengampu',
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  KEY `fk_rps_pengampu_to_dosen` (`dosen_id`),
  CONSTRAINT `fk_rps_pengampu_to_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rps_pengampu_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps_pengampu`
--

LOCK TABLES `rps_pengampu` WRITE;
/*!40000 ALTER TABLE `rps_pengampu` DISABLE KEYS */;
INSERT INTO `rps_pengampu` VALUES (73,14,2,'pengampu'),(75,14,2,'koordinator'),(78,15,2,'pengampu'),(79,15,2,'koordinator');
/*!40000 ALTER TABLE `rps_pengampu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rps_referensi`
--

DROP TABLE IF EXISTS `rps_referensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rps_referensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rps_id` int NOT NULL,
  `tipe` enum('utama','pendukung') COLLATE utf8mb4_general_ci DEFAULT 'utama',
  `judul` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penulis` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerbit` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  CONSTRAINT `rps_referensi_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps_referensi`
--

LOCK TABLES `rps_referensi` WRITE;
/*!40000 ALTER TABLE `rps_referensi` DISABLE KEYS */;
INSERT INTO `rps_referensi` VALUES (15,15,'utama','tesy','test','','','','2025-08-28 11:59:39');
/*!40000 ALTER TABLE `rps_referensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_cpmk`
--

DROP TABLE IF EXISTS `sub_cpmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_cpmk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpmk_id` int NOT NULL,
  `kode_sub_cpmk` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cpmk_id` (`cpmk_id`),
  CONSTRAINT `sub_cpmk_ibfk_1` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_cpmk`
--

LOCK TABLES `sub_cpmk` WRITE;
/*!40000 ALTER TABLE `sub_cpmk` DISABLE KEYS */;
INSERT INTO `sub_cpmk` VALUES (68,120,'SubCPMK0121','testing','2025-08-20 12:22:17','2025-08-20 12:22:17'),(70,108,'SubCPMK0111','testt','2025-08-20 13:17:34','2025-08-20 20:20:35'),(71,108,'SubCPMK0112','test','2025-08-22 11:16:01','2025-08-22 11:16:01'),(73,116,'SubCPMK0712','test22222222222222','2025-08-28 00:50:38','2025-08-28 07:56:35'),(74,116,'SubCPMK0713','t6est','2025-08-28 00:50:49','2025-08-28 00:50:49'),(75,116,'SubCPMK0711','test','2025-08-28 00:51:10','2025-08-28 00:51:10'),(76,116,'SubCPMK0714','test','2025-08-28 00:51:23','2025-08-28 00:51:23'),(77,111,'SubCPMK0331','test','2025-08-28 05:01:35','2025-08-28 05:01:35'),(78,120,'SubCPMK0122','Testing','2025-09-24 10:40:10','2025-09-24 10:40:10'),(79,120,'SubCPMK0123','Testing','2025-10-11 00:03:08','2025-10-11 00:03:08'),(80,111,'SubCPMK0332','Testing','2025-10-11 00:07:34','2025-10-11 00:07:34'),(81,113,'SubCPMK0621','Metopen','2025-10-11 00:29:09','2025-10-11 00:29:09');
/*!40000 ALTER TABLE `sub_cpmk` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_cpmk_mk`
--

LOCK TABLES `sub_cpmk_mk` WRITE;
/*!40000 ALTER TABLE `sub_cpmk_mk` DISABLE KEYS */;
INSERT INTO `sub_cpmk_mk` VALUES (50,68,196,'2025-08-20 12:22:17','2025-08-20 12:22:17'),(54,70,213,'2025-08-20 20:20:35','2025-08-20 20:20:35'),(55,71,213,'2025-08-22 11:16:01','2025-08-22 11:16:01'),(58,74,216,'2025-08-28 00:50:49','2025-08-28 00:50:49'),(59,75,216,'2025-08-28 00:51:10','2025-08-28 00:51:10'),(60,76,216,'2025-08-28 00:51:23','2025-08-28 00:51:23'),(61,73,216,'2025-08-28 07:56:35','2025-08-28 07:56:35'),(62,77,199,'2025-08-28 05:01:35','2025-08-28 05:01:35'),(63,78,213,'2025-09-24 10:40:10','2025-09-24 10:40:10'),(64,79,213,'2025-10-11 00:03:08','2025-10-11 00:03:08'),(65,80,213,'2025-10-11 00:07:34','2025-10-11 00:07:34'),(66,81,213,'2025-10-11 00:29:09','2025-10-11 00:29:09');
/*!40000 ALTER TABLE `sub_cpmk_mk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','dosen','mahasiswa') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$uCUV3FbS0QoHEGJ5vLKbUetKDUmY09jg.SWhb1iLzvso9mmqm0B2a','admin','2025-05-27 21:50:24','2025-08-27 22:55:47'),(16,'dosen','$2y$12$a/qf.MmL8j0dFOWVTsRD8eEtvSnbtoNakxFucjfYrKHqZHni3DsvO','dosen','2025-09-19 14:15:17','2025-09-19 16:22:00'),(18,'213030503137','$2y$12$o2pq7TGXjJRlvd3MXY1Mvu2iBThJzoJ3d1WwpNl6ShQ2U/sk01H92','mahasiswa','2025-10-15 07:09:37','2025-10-15 21:03:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

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
 1 AS `program_studi`,
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
 1 AS `program_studi`,
 1 AS `jenis_kegiatan`,
 1 AS `tempat_kegiatan`,
 1 AS `tanggal_mulai`,
 1 AS `tanggal_selesai`,
 1 AS `durasi_minggu`,
 1 AS `sks_dikonversi`,
 1 AS `dosen_pembimbing`,
 1 AS `status_kegiatan`,
 1 AS `tahun_akademik`,
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
/*!50001 VIEW `view_jadwal_lengkap` AS select `jm`.`id` AS `id`,`mk`.`kode_mk` AS `kode_mk`,`mk`.`nama_mk` AS `nama_mk`,`mk`.`semester` AS `semester`,`mk`.`sks` AS `sks`,`jm`.`program_studi` AS `program_studi`,`jm`.`tahun_akademik` AS `tahun_akademik`,`jm`.`kelas` AS `kelas`,`jm`.`ruang` AS `ruang`,`jm`.`hari` AS `hari`,`jm`.`jam_mulai` AS `jam_mulai`,`jm`.`jam_selesai` AS `jam_selesai`,`jm`.`status` AS `status`,group_concat((case when (`jd`.`role` = 'leader') then concat(`d`.`nama_lengkap`,' (Ketua)') else `d`.`nama_lengkap` end) order by `jd`.`role` DESC,`d`.`nama_lengkap` ASC separator ', ') AS `dosen_pengampu`,(select `d2`.`nama_lengkap` from (`jadwal_dosen` `jd2` join `dosen` `d2` on((`jd2`.`dosen_id` = `d2`.`id`))) where ((`jd2`.`jadwal_mengajar_id` = `jm`.`id`) and (`jd2`.`role` = 'leader')) limit 1) AS `dosen_ketua` from (((`jadwal_mengajar` `jm` join `mata_kuliah` `mk` on((`jm`.`mata_kuliah_id` = `mk`.`id`))) left join `jadwal_dosen` `jd` on((`jm`.`id` = `jd`.`jadwal_mengajar_id`))) left join `dosen` `d` on((`jd`.`dosen_id` = `d`.`id`))) group by `jm`.`id`,`mk`.`kode_mk`,`mk`.`nama_mk`,`mk`.`semester`,`mk`.`sks`,`jm`.`program_studi`,`jm`.`tahun_akademik`,`jm`.`kelas`,`jm`.`ruang`,`jm`.`hari`,`jm`.`jam_mulai`,`jm`.`jam_selesai`,`jm`.`status` */;
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
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_mbkm_lengkap` AS select `k`.`id` AS `id`,`k`.`judul_kegiatan` AS `judul_kegiatan`,group_concat(distinct `m`.`nim` order by `km`.`id` ASC separator ', ') AS `nim_list`,group_concat(distinct `m`.`nama_lengkap` order by `km`.`id` ASC separator ', ') AS `nama_mahasiswa_list`,group_concat(distinct concat(`m`.`nama_lengkap`,' (',`m`.`nim`,')') order by `km`.`id` ASC separator ', ') AS `mahasiswa_detail`,count(distinct `km`.`mahasiswa_id`) AS `jumlah_mahasiswa`,(select `mahasiswa`.`program_studi` from `mahasiswa` where (`mahasiswa`.`id` = (select `mbkm_kegiatan_mahasiswa`.`mahasiswa_id` from `mbkm_kegiatan_mahasiswa` where (`mbkm_kegiatan_mahasiswa`.`kegiatan_id` = `k`.`id`) limit 1))) AS `program_studi`,`jk`.`nama_kegiatan` AS `jenis_kegiatan`,`k`.`tempat_kegiatan` AS `tempat_kegiatan`,`k`.`tanggal_mulai` AS `tanggal_mulai`,`k`.`tanggal_selesai` AS `tanggal_selesai`,`k`.`durasi_minggu` AS `durasi_minggu`,`k`.`sks_dikonversi` AS `sks_dikonversi`,`d`.`nama_lengkap` AS `dosen_pembimbing`,`k`.`status_kegiatan` AS `status_kegiatan`,`k`.`tahun_akademik` AS `tahun_akademik`,`na`.`nilai_angka` AS `nilai_angka`,`na`.`nilai_huruf` AS `nilai_huruf`,`na`.`status_kelulusan` AS `status_kelulusan` from (((((`mbkm_kegiatan` `k` join `mbkm_jenis_kegiatan` `jk` on((`k`.`jenis_kegiatan_id` = `jk`.`id`))) left join `mbkm_kegiatan_mahasiswa` `km` on((`k`.`id` = `km`.`kegiatan_id`))) left join `mahasiswa` `m` on((`km`.`mahasiswa_id` = `m`.`id`))) left join `dosen` `d` on((`k`.`dosen_pembimbing_id` = `d`.`id`))) left join `mbkm_nilai_akhir` `na` on((`k`.`id` = `na`.`kegiatan_id`))) group by `k`.`id`,`k`.`judul_kegiatan`,`jk`.`nama_kegiatan`,`k`.`tempat_kegiatan`,`k`.`tanggal_mulai`,`k`.`tanggal_selesai`,`k`.`durasi_minggu`,`k`.`sks_dikonversi`,`d`.`nama_lengkap`,`k`.`status_kegiatan`,`k`.`tahun_akademik`,`na`.`nilai_angka`,`na`.`nilai_huruf`,`na`.`status_kelulusan` */;
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

-- Dump completed on 2025-11-09 22:11:22
