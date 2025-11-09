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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bahan_kajian`
--

LOCK TABLES `bahan_kajian` WRITE;
/*!40000 ALTER TABLE `bahan_kajian` DISABLE KEYS */;
INSERT INTO `bahan_kajian` VALUES (1,'BK01','Social Issues and Professional Practice','2025-06-14 13:48:49'),(2,'BK02','Security','2025-06-14 13:48:49'),(3,'BK03','Project Management','2025-06-14 13:48:49'),(4,'BK04','User Experience Design','2025-06-14 13:48:49'),(5,'BK05','Software Development Fundamentals','2025-06-14 13:48:49'),(6,'BK06','Data Management','2025-06-14 13:48:49'),(7,'BK07','Parallel and Distributed Computing','2025-06-14 13:48:49'),(8,'BK08','Network and Communication','2025-06-14 13:48:49'),(9,'BK09','Human-Computer Interaction','2025-06-14 13:48:49'),(10,'BK10','Software Engineering','2025-06-14 13:48:49'),(11,'BK11','Operating Systems','2025-06-14 13:48:49'),(12,'BK12','Algoritmics Foundation','2025-06-14 13:48:49'),(13,'BK13','Foundation of Programming Languages','2025-06-14 13:48:49'),(14,'BK14','Programming Fundamentals','2025-06-14 13:48:49'),(15,'BK15','Systems Fundamentals','2025-06-14 13:48:49'),(16,'BK16','Architecture and Organization','2025-06-14 13:48:49'),(17,'BK17','Graphics and Interactive Techniques','2025-06-14 13:48:49'),(18,'BK18','Artificial Intelligence','2025-06-14 13:48:49'),(19,'BK19','Specialized Platform Development','2025-06-14 13:48:49'),(20,'BK20','Mathematical and Statistical Foundations','2025-06-14 13:48:49'),(21,'BK21','Pengembangan Diri','2025-06-14 13:48:49'),(22,'BK22','Metodologi Penelitian','2025-06-14 13:48:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bk_mk`
--

LOCK TABLES `bk_mk` WRITE;
/*!40000 ALTER TABLE `bk_mk` DISABLE KEYS */;
INSERT INTO `bk_mk` VALUES (1,1,11),(2,5,2),(3,5,7),(4,6,8),(5,10,7),(6,12,2),(7,14,2),(8,16,5),(9,20,3),(10,20,4),(11,21,11),(12,22,12);
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
  `kode_cpl` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_cpl` enum('P','KK','S','KU') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_cpl` (`kode_cpl`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl`
--

LOCK TABLES `cpl` WRITE;
/*!40000 ALTER TABLE `cpl` DISABLE KEYS */;
INSERT INTO `cpl` VALUES (1,'CPL01','Menginternalisasi nilai-nilai Pancasila dalam kehidupan masyarakat yang beragam','P','2025-06-14 13:46:50'),(2,'CPL02','Menunjukkan sikap profesional, responsif terhadap perkembangan teknologi dan pemahaman tentang pembelajaran sepanjang hayat','S','2025-06-14 13:46:50'),(3,'CPL03','Memiliki kemampuan untuk menerapkan teori matematika dan sistem komputer untuk menyelesaikan berbagai permasalahan dengan prinsip-prinsip computing.','KU','2025-06-14 13:46:50'),(4,'CPL04','Memiliki kemampuan untuk menggunakan dan menerapkan berbagai algoritma/metode untuk memecahkan masalah pada suatu organisasi.','KK','2025-06-14 13:46:50'),(5,'CPL05','Memiliki kemampuan bekerjasama yang baik dalam tim multi displin serta berkomunikasi secara efektif, baik secara lisan maupun tulisan','KU','2025-06-15 11:18:25'),(6,'CPL06','Mampu mengambil keputusan secara tepat dalam konteks penyelesaian masalah di bidang keahliannya, berdasarkan hasil analisis data','KU','2025-06-22 10:10:57'),(7,'CPL07','Memiliki kemampuan untuk menganalisis, merancang, menerapkan, menguji dan memelihara perangkat lunak dengan berbagai kompleksitas','KK','2025-06-22 10:10:57'),(8,'CPL08','Mampu merancang dan menciptakan solusi inovatif untuk memecahkan masalah di dunia industri dengan pendekatan sistem cerdas menggunakan algoritma kompleks.','KK','2025-06-22 10:10:57'),(9,'CPL09','Memiliki kemampuan memahami, merancang, mengimplementasikan, mengevaluasi dan memelihara sistem komputer, arsitektur komputer, serta jaringan komputer sesuai dengan kebutuhan organisasi','KK','2025-06-22 10:10:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_bk`
--

LOCK TABLES `cpl_bk` WRITE;
/*!40000 ALTER TABLE `cpl_bk` DISABLE KEYS */;
INSERT INTO `cpl_bk` VALUES (1,1,1),(8,5,3),(11,7,5),(10,7,10),(14,9,11),(4,3,12),(5,4,12),(6,4,14),(13,9,16),(12,8,18),(3,3,20),(2,2,21),(7,5,21),(9,6,22);
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
INSERT INTO `cpl_cpmk` VALUES (1,1,1,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,2,2,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,3,3,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(4,4,4,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(5,5,5,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(6,6,6,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(7,7,8,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(8,7,9,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(9,7,7,'2025-11-02 13:31:58','2025-11-02 13:31:58'),(10,8,8,'2025-11-02 13:32:06','2025-11-02 13:32:06'),(11,9,9,'2025-11-02 13:32:15','2025-11-02 13:32:15');
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_mk`
--

LOCK TABLES `cpl_mk` WRITE;
/*!40000 ALTER TABLE `cpl_mk` DISABLE KEYS */;
INSERT INTO `cpl_mk` VALUES (18,1,8),(1,1,11),(26,1,15),(19,2,8),(2,2,11),(27,2,15),(3,3,3),(4,3,4),(20,3,8),(5,3,14),(28,3,15),(6,4,2),(7,4,6),(8,4,7),(21,4,8),(22,5,8),(10,5,10),(9,5,12),(23,6,8),(11,6,12),(12,7,7),(13,7,8),(24,8,8),(14,8,14),(16,9,5),(25,9,8),(17,9,13);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpl_pl`
--

LOCK TABLES `cpl_pl` WRITE;
/*!40000 ALTER TABLE `cpl_pl` DISABLE KEYS */;
INSERT INTO `cpl_pl` VALUES (1,1,3),(2,2,3),(3,3,1),(4,4,1),(5,5,1),(6,6,2),(7,7,2),(8,8,2),(9,9,1);
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
  `kode_cpmk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cpmk_kode` (`kode_cpmk`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpmk`
--

LOCK TABLES `cpmk` WRITE;
/*!40000 ALTER TABLE `cpmk` DISABLE KEYS */;
INSERT INTO `cpmk` VALUES (1,'CPMK011','Mampu menginternalisasi nilai-nilai Pancasila dalam pengambilan keputusan dan penyelesaian masalah sosial','2025-08-05 14:15:56','2025-08-05 14:15:56'),(2,'CPMK022','Mampu menunjukkan sikap responsif dan adaptif terhadap perkembangan teknologi terkini','2025-08-05 14:16:51','2025-08-05 14:16:51'),(3,'CPMK033','Mampu menerapkan teori matematika yang dapat menerapkan metode statistik dan probabilistik untuk pengambilan keputusan berbasis data','2025-08-05 14:20:01','2025-08-05 14:20:01'),(4,'CPMK041','Mampu menerapkan algoritma dan metode pemrograman untuk membangun solusi perangkat lunak yang efisien terhadap permasalahan fungsional organisasi','2025-08-07 09:43:38','2025-08-07 09:43:38'),(5,'CPMK053','Mampu menulis laporan teknis, dokumentasi, atau karya ilmiah yang terstruktur dan sesuai kaidah kebahasaan dan akademik','2025-08-05 14:21:43','2025-08-05 14:21:43'),(6,'CPMK062','Mampu menerapkan metodologi penelitian untuk pengumpulan dan analisis data secara sistematis','2025-08-05 14:23:13','2025-08-05 14:23:13'),(7,'CPMK071','Mampu menganalisis dan merancang struktur data yang efisien','2025-08-07 20:33:20','2025-08-07 20:35:49'),(8,'CPMK081','Mampu mengimplementasikan konsep pemrograman berorientasi objek','2025-08-10 07:10:46','2025-08-10 07:10:46'),(9,'CPMK091','Mampu merancang dan mengimplementasikan basis data','2025-08-10 07:11:02','2025-08-10 07:11:02'),(10,'CPMK101','Mampu menerapkan prinsip keamanan informasi','2025-10-10 17:00:52','2025-10-10 17:00:52');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpmk_mk`
--

LOCK TABLES `cpmk_mk` WRITE;
/*!40000 ALTER TABLE `cpmk_mk` DISABLE KEYS */;
INSERT INTO `cpmk_mk` VALUES (12,1,8),(1,1,11),(20,1,15),(13,2,8),(2,2,11),(21,2,15),(14,3,8),(3,3,14),(4,4,2),(5,4,7),(15,4,8),(16,5,8),(6,5,12),(17,6,8),(7,6,12),(8,7,6),(18,7,8),(9,8,7),(19,8,8),(10,9,8),(11,10,13);
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
  `nip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan_fungsional` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_keaktifan` enum('Aktif','Tidak Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nip` (`nip`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  CONSTRAINT `fk_dosen_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dosen`
--

LOCK TABLES `dosen` WRITE;
/*!40000 ALTER TABLE `dosen` DISABLE KEYS */;
INSERT INTO `dosen` VALUES (1,NULL,'197001011995031001','Dr. Ahmad Fauzi, S.Kom., M.T.','Lektor Kepala','Aktif','2025-08-27 08:49:33','2025-11-03 11:16:15'),(2,NULL,'197205101996032001','Dr. Siti Nurhaliza, S.T., M.Kom.','Lektor Kepala','Aktif','2025-08-27 08:49:33','2025-10-21 02:37:12'),(3,10,'197503151997031002','Budi Santoso, S.Kom., M.T.','Lektor','Aktif','2025-08-27 16:43:34','2025-11-03 11:16:30'),(4,NULL,'197807202000032001','Dewi Lestari, S.T., M.Kom.','Lektor','Aktif','2025-09-19 12:56:59','2025-10-21 02:37:17'),(5,NULL,'198001052003121001','Ir. Joko Widodo, M.T., Ph.D.','Lektor Kepala','Aktif','2025-09-19 12:56:59','2025-09-19 13:17:19');
/*!40000 ALTER TABLE `dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_config`
--

DROP TABLE IF EXISTS `grade_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grade_letter` varchar(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Grade letter (A, AB, B, BC, C, D, E)',
  `min_score` decimal(5,2) NOT NULL COMMENT 'Minimum score for this grade (0-100)',
  `max_score` decimal(5,2) NOT NULL COMMENT 'Maximum score for this grade (0-100)',
  `grade_point` decimal(3,2) DEFAULT NULL COMMENT 'Grade point value (e.g., 4.0 for A)',
  `description` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Grade description (e.g., Istimewa, Baik Sekali)',
  `is_passing` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = passing grade, 0 = failing grade',
  `order_number` int NOT NULL COMMENT 'Order for sorting grades',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade_letter` (`grade_letter`),
  KEY `order_number` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_config`
--

LOCK TABLES `grade_config` WRITE;
/*!40000 ALTER TABLE `grade_config` DISABLE KEYS */;
INSERT INTO `grade_config` VALUES (1,'A',80.01,100.00,4.00,'Istimewa',1,1,1,NULL,'2025-11-09 15:00:42'),(2,'AB',70.01,80.00,3.50,'Baik Sekali',1,2,1,NULL,NULL),(3,'B',65.01,70.00,3.00,'Baik',1,3,1,NULL,NULL),(4,'BC',60.01,65.00,2.50,'Cukup Baik',1,4,1,NULL,NULL),(5,'C',50.01,60.00,2.00,'Cukup',1,5,1,NULL,NULL),(6,'D',40.01,50.00,1.00,'Kurang',0,6,1,NULL,NULL),(7,'E',0.00,40.00,0.00,'Sangat Kurang',0,7,1,NULL,NULL);
/*!40000 ALTER TABLE `grade_config` ENABLE KEYS */;
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
  `role` enum('leader','member') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal_dosen` (`jadwal_mengajar_id`,`dosen_id`),
  KEY `idx_jadwal_id` (`jadwal_mengajar_id`),
  KEY `idx_dosen_id` (`dosen_id`),
  KEY `idx_role` (`role`),
  KEY `idx_dosen_role_jadwal` (`dosen_id`,`role`),
  CONSTRAINT `fk_jadwal_dosen_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_dosen_jadwal` FOREIGN KEY (`jadwal_mengajar_id`) REFERENCES `jadwal_mengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_dosen`
--

LOCK TABLES `jadwal_dosen` WRITE;
/*!40000 ALTER TABLE `jadwal_dosen` DISABLE KEYS */;
INSERT INTO `jadwal_dosen` VALUES (14,8,3,'leader','2025-11-02 13:46:50'),(15,8,4,'member','2025-11-02 13:46:50'),(16,9,4,'leader','2025-11-03 11:01:27');
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
  `program_studi` enum('Teknik Informatika','Sistem Informasi','Teknik Komputer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Teknik Informatika',
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal` (`mata_kuliah_id`,`program_studi`,`tahun_akademik`,`kelas`),
  KEY `idx_program_studi` (`program_studi`),
  KEY `idx_tahun_akademik` (`tahun_akademik`),
  KEY `idx_status` (`status`),
  KEY `idx_jadwal_semester` (`tahun_akademik`,`program_studi`),
  KEY `idx_jadwal_waktu` (`hari`,`jam_mulai`),
  CONSTRAINT `fk_jadwal_mata_kuliah` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_mengajar`
--

LOCK TABLES `jadwal_mengajar` WRITE;
/*!40000 ALTER TABLE `jadwal_mengajar` DISABLE KEYS */;
INSERT INTO `jadwal_mengajar` VALUES (8,8,'Teknik Informatika','2024/2025 Ganjil','A',NULL,'Senin','12:30:00','14:30:00','active','2025-11-02 13:46:50','2025-11-09 01:09:35',0,NULL,NULL),(9,15,'Teknik Informatika','2024/2025 Ganjil','A','Ft1','Senin','06:30:00','06:31:00','active','2025-11-03 11:01:27','2025-11-03 11:01:27',0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa`
--

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES (1,NULL,'21.11.0001','Andi Setiawan','Teknik Informatika','2021','Aktif','2025-09-19 22:44:06','2025-10-21 02:37:19'),(2,NULL,'21.11.0002','Bella Kusuma','Teknik Informatika','2021','Aktif','2025-09-19 22:44:06','2025-10-21 02:37:22'),(3,NULL,'21.11.0003','Cahya Permana','Teknik Informatika','2021','Aktif','2025-09-19 22:44:06','2025-10-21 02:37:24'),(4,NULL,'21.11.0004','Dian Pratiwi','Teknik Informatika','2021','Aktif','2025-09-19 22:44:06','2025-10-21 02:37:26'),(5,NULL,'21.11.0005','Eko Nugroho','Teknik Informatika','2021','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(6,NULL,'22.11.0001','Fitri Handayani','Sistem Informasi','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(7,NULL,'22.11.0002','Gilang Ramadhan','Sistem Informasi','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(8,NULL,'22.11.0003','Hesti Wulandari','Sistem Informasi','2022','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(9,NULL,'23.11.0001','Irfan Hakim','Teknik Informatika','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06'),(10,NULL,'23.11.0002','Jasmine Putri','Teknik Informatika','2023','Aktif','2025-09-19 22:44:06','2025-09-19 22:44:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_kuliah`
--

LOCK TABLES `mata_kuliah` WRITE;
/*!40000 ALTER TABLE `mata_kuliah` DISABLE KEYS */;
INSERT INTO `mata_kuliah` VALUES (1,'1DCP101030','Pengantar Teknologi Informasi','Pengenalan dasar teknologi informasi dan komputer','wajib',1,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(2,'1DCP102040','Algoritma dan Pemrograman','Dasar-dasar algoritma dan pemrograman','wajib',1,4,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(3,'1DCP103030','Matematika Diskrit','Konsep matematika untuk ilmu komputer','wajib',1,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(4,'1DCP171030','Aljabar Linier dan Matriks','Mempelajari aljabar linier dan aplikasi matriks','wajib',1,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(5,'1DCP561030','Arsitektur dan Organisasi Komputer','Dasar-dasar arsitektur komputer','wajib',1,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(6,'1DCP182032','Struktur Data','Mempelajari struktur data dalam pemrograman','wajib',2,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(7,'1DCP213032','Pemrograman Berorientasi Obyek','Dasar-dasar pemrograman berorientasi objek','wajib',3,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(8,'1DCP573032','Basis Data I','Pengantar basis data','wajib',3,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(9,'1DCU024020','Kewarganegaraan','Mata kuliah kewarganegaraan','wajib',4,2,'2025-08-04 06:46:57','2025-08-04 06:46:57','mkwk'),(10,'1DCP874030','Manajemen Proyek','Dasar manajemen proyek perangkat lunak','wajib',4,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(11,'1DCU105020','Pancasila','Mata kuliah Pancasila','wajib',5,2,'2025-08-04 06:46:57','2025-08-04 06:46:57','mkwk'),(12,'1DCP115030','Metode Penelitian','Mata kuliah metodologi penelitian','wajib',5,3,'2025-08-05 13:52:10','2025-08-05 13:52:10','mkwk'),(13,'1DCP905030','Keamanan Data dan Informasi','Dasar keamanan data dan informasi','wajib',5,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(14,'1DCP685032','Data Mining','Dasar data mining','wajib',5,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori'),(15,'1DCP936030','Pembelajaran Mesin','Dasar pembelajaran mesin','wajib',6,3,'2025-08-04 06:46:57','2025-08-04 06:46:57','wajib_teori');
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
-- Dumping data for table `mbkm_jenis_kegiatan`
--

LOCK TABLES `mbkm_jenis_kegiatan` WRITE;
/*!40000 ALTER TABLE `mbkm_jenis_kegiatan` DISABLE KEYS */;
INSERT INTO `mbkm_jenis_kegiatan` VALUES (1,'MBKM01','Magang/Praktik Kerja','Kegiatan magang di industri/instansi',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,'MBKM02','Pertukaran Pelajar','Program pertukaran mahasiswa',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,'MBKM03','Asistensi Mengajar di Satuan Pendidikan','Mengajar di sekolah',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26'),(4,'MBKM04','Penelitian/Riset','Kegiatan penelitian',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26'),(5,'MBKM05','Proyek Kemanusiaan','Kegiatan sosial kemasyarakatan',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26'),(6,'MBKM06','Kegiatan Wirausaha','Membangun startup/usaha',20,'aktif','2025-10-20 15:39:26','2025-10-20 15:39:26');
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`),
  KEY `idx_dosen_pembimbing` (`dosen_pembimbing_id`),
  KEY `idx_status` (`status_kegiatan`),
  CONSTRAINT `fk_mbkm_dosen` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mbkm_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_kegiatan`
--

LOCK TABLES `mbkm_kegiatan` WRITE;
/*!40000 ALTER TABLE `mbkm_kegiatan` DISABLE KEYS */;
INSERT INTO `mbkm_kegiatan` VALUES (1,1,'Magang sebagai Software Developer di PT. Tech Startup Indonesia','PT. Tech Startup Indonesia, Jakarta','Budi Santoso, S.Kom','budi.santoso@techstartup.id',2,'2025-02-01','2025-07-31',26,20,'Mengembangkan aplikasi mobile dan web menggunakan Flutter dan Laravel',NULL,'selesai','2024/2025 Genap','2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,4,'Penelitian: Implementasi Machine Learning untuk Prediksi Cuaca','Laboratorium Komputasi, Fakultas Teknik UPR','Dr. Siti Rahmawati, M.Kom','siti.rahmawati@upr.ac.id',4,'2025-03-01','2025-08-31',26,20,'Penelitian menggunakan algoritma Random Forest dan LSTM',NULL,'disetujui','2024/2025 Genap','2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,5,'Program Pemberdayaan Masyarakat Digital','Desa Teknologi, Palangka Raya','Kepala Desa','08123456789',3,'2025-01-01','2025-02-01',5,20,'Memberikan pelatihan komputer dasar kepada masyarakat',NULL,'selesai','2024/2025 Genap','2025-10-20 15:39:26','2025-10-20 15:39:26'),(4,1,'Backend Developer di PT Solusi Digital','PT Solusi Digital, Surabaya','Rizki Pratama, S.Kom','rizki@solusidigital.id',1,'2024-02-23','2024-07-19',21,20,'Kegiatan Backend Developer di PT Solusi Digital... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(5,1,'Frontend Developer di PT Media Kreatif','PT Media Kreatif, Bandung','Sinta Dewi, S.T.','sinta@mediakreatif.co.id',3,'2024-01-12','2024-07-12',26,20,'Kegiatan Frontend Developer di PT Media Kreatif... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2024/2025 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(6,1,'Data Analyst di PT Analytics Indonesia','PT Analytics Indonesia, Jakarta','Ahmad Fauzan, S.Kom., M.T.','ahmad@analytics.id',1,'2024-05-21','2024-11-19',26,20,'Kegiatan Data Analyst di PT Analytics Indonesia... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2024/2025 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(7,1,'Mobile Developer di PT Aplikasi Nusantara','PT Aplikasi Nusantara, Yogyakarta','Dian Pertiwi, S.Kom','dian@aplikasi.id',3,'2024-06-28','2024-12-20',25,20,'Kegiatan Mobile Developer di PT Aplikasi Nusantara... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'disetujui','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(8,2,'Program Pertukaran ke Universiti Malaya','Universiti Malaya, Malaysia','Prof. Dr. Ahmad Hassan','ahmad.hassan@um.edu.my',5,'2024-02-08','2024-06-27',20,20,'Kegiatan Program Pertukaran ke Universiti Malaya... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(9,2,'Exchange Program ke NUS Singapore','National University of Singapore','Dr. Lee Wei Ming','leeweiming@nus.edu.sg',5,'2024-07-08','2024-12-02',21,20,'Kegiatan Exchange Program ke NUS Singapore... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2024/2025 Genap','2025-10-20 16:03:10','2025-10-20 16:03:10'),(10,2,'Student Exchange to Chulalongkorn University','Chulalongkorn University, Thailand','Assoc. Prof. Somchai Pradit','somchai@chula.ac.th',1,'2024-04-11','2024-09-12',22,20,'Kegiatan Student Exchange to Chulalongkorn University... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(11,3,'Asistensi Mengajar di SMAN 1 Palangka Raya','SMAN 1 Palangka Raya','Sutrisno, S.Pd., M.Pd.','08123456701',4,'2024-04-08','2024-09-30',25,20,'Kegiatan Asistensi Mengajar di SMAN 1 Palangka Raya... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'disetujui','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(12,3,'Teaching Assistant di SMA Negeri 3','SMA Negeri 3, Palangka Raya','Rina Wati, S.Pd','08123456702',1,'2024-04-27','2024-09-21',21,20,'Kegiatan Teaching Assistant di SMA Negeri 3... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'disetujui','2024/2025 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(13,3,'Mengajar TIK di SMP Negeri 5','SMP Negeri 5, Palangka Raya','Bambang Setiawan, S.Kom','08123456703',4,'2024-02-05','2024-08-12',27,20,'Kegiatan Mengajar TIK di SMP Negeri 5... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2024/2025 Genap','2025-10-20 16:03:10','2025-10-20 16:03:10'),(14,4,'Sistem Rekomendasi Berbasis Deep Learning','Lab AI, Fakultas Teknik UPR','Dr. Ratna Sari, M.Kom','ratna@upr.ac.id',2,'2024-05-01','2024-09-25',21,20,'Kegiatan Sistem Rekomendasi Berbasis Deep Learning... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(15,4,'IoT untuk Smart Agriculture','Lab Embedded System UPR','Dr. Eko Budiyanto, M.T.','eko@upr.ac.id',4,'2024-08-11','2025-02-02',25,20,'Kegiatan IoT untuk Smart Agriculture... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Genap','2025-10-20 16:03:10','2025-10-20 16:03:10'),(16,4,'Blockchain untuk E-Voting System','Lab Keamanan Informasi UPR','Dr. Hendra Kusuma, M.Kom','hendra@upr.ac.id',5,'2024-03-06','2024-07-24',20,20,'Kegiatan Blockchain untuk E-Voting System... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'disetujui','2023/2024 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10'),(17,5,'Digitalisasi UMKM di Desa Wisata','Desa Tumbang Nusa, Kalimantan Tengah','Kepala Desa Tumbang Nusa','08123456710',2,'2024-06-09','2024-11-17',23,20,'Kegiatan Digitalisasi UMKM di Desa Wisata... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2023/2024 Genap','2025-10-20 16:03:10','2025-10-20 16:03:10'),(18,5,'Pemberdayaan Masyarakat melalui Teknologi','Kelurahan Pahandut','Lurah Pahandut','08123456711',5,'2024-01-23','2024-07-16',25,20,'Kegiatan Pemberdayaan Masyarakat melalui Teknologi... dengan fokus pada pengembangan kompetensi mahasiswa',NULL,'selesai','2024/2025 Ganjil','2025-10-20 16:03:10','2025-10-20 16:03:10');
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
  `peran` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Role in the activity: Ketua, Anggota, etc',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan_mahasiswa` (`kegiatan_id`,`mahasiswa_id`),
  KEY `idx_kegiatan` (`kegiatan_id`),
  KEY `idx_mahasiswa` (`mahasiswa_id`),
  KEY `idx_kegiatan_mahasiswa_combined` (`kegiatan_id`,`mahasiswa_id`),
  CONSTRAINT `fk_kegiatan_mhs_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kegiatan_mhs_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_kegiatan_mahasiswa`
--

LOCK TABLES `mbkm_kegiatan_mahasiswa` WRITE;
/*!40000 ALTER TABLE `mbkm_kegiatan_mahasiswa` DISABLE KEYS */;
INSERT INTO `mbkm_kegiatan_mahasiswa` VALUES (1,1,1,'Peserta','2025-10-20 15:39:26'),(2,2,2,'Peserta','2025-10-20 15:39:26'),(3,3,3,'Ketua','2025-10-20 15:39:26'),(4,3,4,'Anggota','2025-10-20 15:39:26'),(5,3,5,'Anggota','2025-10-20 15:39:26'),(6,4,4,'Ketua','2025-10-20 16:03:10'),(7,5,10,'Ketua','2025-10-20 16:03:10'),(8,5,5,'Anggota','2025-10-20 16:03:10'),(9,6,10,'Ketua','2025-10-20 16:03:10'),(10,6,5,'Peserta','2025-10-20 16:03:10'),(11,6,8,'Anggota','2025-10-20 16:03:10'),(12,7,8,'Ketua','2025-10-20 16:03:10'),(13,8,2,'Ketua','2025-10-20 16:03:10'),(14,9,5,'Ketua','2025-10-20 16:03:10'),(15,9,6,'Peserta','2025-10-20 16:03:10'),(16,9,2,'Anggota','2025-10-20 16:03:10'),(17,10,7,'Ketua','2025-10-20 16:03:10'),(18,10,1,'Peserta','2025-10-20 16:03:10'),(19,11,9,'Ketua','2025-10-20 16:03:10'),(20,11,2,'Peserta','2025-10-20 16:03:10'),(21,12,9,'Ketua','2025-10-20 16:03:10'),(22,13,1,'Ketua','2025-10-20 16:03:10'),(23,14,3,'Ketua','2025-10-20 16:03:10'),(24,14,5,'Peserta','2025-10-20 16:03:10'),(25,14,1,'Peserta','2025-10-20 16:03:10'),(26,15,9,'Ketua','2025-10-20 16:03:10'),(27,16,1,'Ketua','2025-10-20 16:03:10'),(28,17,5,'Ketua','2025-10-20 16:03:10'),(29,17,2,'Peserta','2025-10-20 16:03:10'),(30,18,1,'Ketua','2025-10-20 16:03:10'),(31,18,8,'Anggota','2025-10-20 16:03:10'),(32,18,4,'Peserta','2025-10-20 16:03:10');
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
  `nama_komponen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`),
  CONSTRAINT `fk_komponen_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_komponen_nilai`
--

LOCK TABLES `mbkm_komponen_nilai` WRITE;
/*!40000 ALTER TABLE `mbkm_komponen_nilai` DISABLE KEYS */;
INSERT INTO `mbkm_komponen_nilai` VALUES (1,1,'Kehadiran dan Kedisiplinan',20.00,'Penilaian kehadiran dan kedisiplinan selama magang','2025-10-20 15:39:26'),(2,1,'Kinerja dan Produktivitas',30.00,'Penilaian hasil kerja dan produktivitas','2025-10-20 15:39:26'),(3,1,'Sikap dan Etika Kerja',20.00,'Penilaian sikap profesional dan etika','2025-10-20 15:39:26'),(4,1,'Laporan Akhir',30.00,'Penilaian laporan akhir kegiatan','2025-10-20 15:39:26'),(5,4,'Proposal dan Metodologi',20.00,'Penilaian proposal penelitian dan metodologi','2025-10-20 15:39:26'),(6,4,'Pelaksanaan Penelitian',25.00,'Penilaian proses pelaksanaan','2025-10-20 15:39:26'),(7,4,'Analisis Data dan Hasil',30.00,'Penilaian analisis data','2025-10-20 15:39:26'),(8,4,'Publikasi/Laporan Akhir',25.00,'Penilaian paper atau laporan','2025-10-20 15:39:26'),(9,5,'Perencanaan Program',20.00,'Penilaian perencanaan','2025-10-20 15:39:26'),(10,5,'Pelaksanaan Kegiatan',30.00,'Penilaian pelaksanaan','2025-10-20 15:39:26'),(11,5,'Kolaborasi dan Kepemimpinan',25.00,'Penilaian kerjasama tim','2025-10-20 15:39:26'),(12,5,'Dampak dan Keberlanjutan',25.00,'Penilaian dampak program','2025-10-20 15:39:26'),(13,2,'Adaptasi Budaya dan Bahasa',15.00,'Kemampuan beradaptasi dengan budaya','2025-10-20 16:03:10'),(14,2,'Prestasi Akademik',35.00,'Nilai yang diperoleh di universitas tujuan','2025-10-20 16:03:10'),(15,2,'Kehadiran dan Partisipasi',20.00,'Keaktifan dalam kegiatan','2025-10-20 16:03:10'),(16,2,'Laporan Pengalaman',30.00,'Dokumentasi dan refleksi pengalaman','2025-10-20 16:03:10'),(17,3,'Persiapan Pembelajaran',20.00,'Rencana dan materi pembelajaran','2025-10-20 16:03:10'),(18,3,'Pelaksanaan Mengajar',30.00,'Kualitas penyampaian materi','2025-10-20 16:03:10'),(19,3,'Pengelolaan Kelas',20.00,'Kemampuan mengelola kelas','2025-10-20 16:03:10'),(20,3,'Evaluasi dan Refleksi',30.00,'Penilaian pembelajaran dan refleksi','2025-10-20 16:03:10'),(21,6,'Business Plan',25.00,'Kelengkapan dan kelayakan rencana bisnis','2025-10-20 16:03:10'),(22,6,'Implementasi Bisnis',30.00,'Pelaksanaan kegiatan usaha','2025-10-20 16:03:10'),(23,6,'Inovasi dan Kreativitas',20.00,'Tingkat inovasi produk/layanan','2025-10-20 16:03:10'),(24,6,'Keberlanjutan Usaha',25.00,'Potensi keberlanjutan bisnis','2025-10-20 16:03:10');
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
-- Dumping data for table `mbkm_nilai`
--

LOCK TABLES `mbkm_nilai` WRITE;
/*!40000 ALTER TABLE `mbkm_nilai` DISABLE KEYS */;
INSERT INTO `mbkm_nilai` VALUES (1,1,1,90.00,'Selalu hadir tepat waktu','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,1,2,85.00,'Mampu menyelesaikan task dengan baik','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,1,3,88.00,'Menunjukkan sikap profesional','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(4,1,4,87.00,'Laporan lengkap dan terstruktur','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(5,3,9,85.00,'Perencanaan matang','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(6,3,10,90.00,'Pelaksanaan sangat baik','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(7,3,11,88.00,'Kerjasama tim solid','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(8,3,12,92.00,'Dampak positif ke masyarakat','dosen_pembimbing','2025-10-20 15:39:26','2025-10-20 15:39:26'),(9,4,1,81.17,'Perlu peningkatan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(10,4,2,88.96,'Luar biasa','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(11,4,3,81.28,'Perlu peningkatan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(12,4,4,86.24,'Luar biasa','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(13,5,1,82.18,'Sangat baik','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(14,5,2,84.91,'Excellent','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(15,5,3,91.27,'Excellent','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(16,5,4,91.22,'Perlu peningkatan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(17,6,1,77.40,'Sangat baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(18,6,2,86.48,'Memuaskan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(19,6,3,91.28,'Baik','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(20,6,4,75.83,'Sangat baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(21,8,13,84.97,'Excellent','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(22,8,14,87.48,'Perlu peningkatan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(23,8,15,90.48,'Perlu peningkatan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(24,8,16,87.58,'Perlu peningkatan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(25,9,13,81.52,'Excellent','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(26,9,14,87.62,'Kompeten','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(27,9,15,92.27,'Kompeten','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(28,9,16,85.21,'Perlu peningkatan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(29,10,13,86.09,'Luar biasa','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(30,10,14,87.69,'Excellent','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(31,10,15,86.30,'Sangat baik','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(32,10,16,83.83,'Memuaskan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(33,13,17,93.02,'Memuaskan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(34,13,18,93.44,'Memuaskan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(35,13,19,80.73,'Baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(36,13,20,88.38,'Excellent','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(37,14,5,77.00,'Memuaskan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(38,14,6,91.32,'Luar biasa','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(39,14,7,88.39,'Luar biasa','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(40,14,8,85.96,'Baik','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(41,15,5,89.19,'Memuaskan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(42,15,6,75.41,'Luar biasa','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(43,15,7,80.65,'Sangat baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(44,15,8,84.44,'Sangat baik','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(45,17,9,83.15,'Baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(46,17,10,76.18,'Baik','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(47,17,11,90.34,'Memuaskan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(48,17,12,78.10,'Kompeten','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(49,18,9,85.88,'Perlu peningkatan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(50,18,10,79.61,'Perlu peningkatan','dosen_pembimbing','2025-10-20 16:03:10','2025-10-20 16:03:10'),(51,18,11,84.18,'Kompeten','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10'),(52,18,12,88.54,'Memuaskan','pembimbing_lapangan','2025-10-20 16:03:10','2025-10-20 16:03:10');
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
  `nilai_huruf` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_kelulusan` enum('Lulus','Tidak Lulus') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Lulus',
  `catatan_akhir` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tanggal_penilaian` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kegiatan` (`kegiatan_id`),
  CONSTRAINT `fk_nilai_akhir_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `mbkm_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mbkm_nilai_akhir`
--

LOCK TABLES `mbkm_nilai_akhir` WRITE;
/*!40000 ALTER TABLE `mbkm_nilai_akhir` DISABLE KEYS */;
INSERT INTO `mbkm_nilai_akhir` VALUES (1,1,87.50,'A','Lulus','Mahasiswa menunjukkan performa sangat baik selama magang','2025-08-15','2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,3,88.75,'A','Lulus','Program berdampak positif bagi masyarakat','2025-02-15','2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,4,84.41,'A-','Lulus','Mahasiswa menunjukkan kompetensi yang sangat baik','2025-08-28','2025-10-20 16:03:10','2025-10-20 16:03:10'),(4,5,87.40,'A','Lulus','Kompetensi sesuai dengan target pembelajaran','2025-08-12','2025-10-20 16:03:10','2025-10-20 16:03:10'),(5,6,82.75,'A-','Lulus','Kompetensi sesuai dengan target pembelajaran','2025-09-19','2025-10-20 16:03:10','2025-10-20 16:03:10'),(6,8,87.63,'A','Lulus','Performa memuaskan sepanjang kegiatan','2025-07-27','2025-10-20 16:03:10','2025-10-20 16:03:10'),(7,9,86.65,'A','Lulus','Mencapai capaian pembelajaran dengan baik','2025-09-13','2025-10-20 16:03:10','2025-10-20 16:03:10'),(8,10,85.98,'A','Lulus','Performa memuaskan sepanjang kegiatan','2025-10-07','2025-10-20 16:03:10','2025-10-20 16:03:10'),(9,13,88.89,'A','Lulus','Performa memuaskan sepanjang kegiatan','2025-08-29','2025-10-20 16:03:10','2025-10-20 16:03:10'),(10,14,85.67,'A','Lulus','Mencapai capaian pembelajaran dengan baik','2025-08-28','2025-10-20 16:03:10','2025-10-20 16:03:10'),(11,15,82.42,'A-','Lulus','Kompetensi sesuai dengan target pembelajaran','2025-10-03','2025-10-20 16:03:10','2025-10-20 16:03:10'),(12,17,81.94,'A-','Lulus','Performa memuaskan sepanjang kegiatan','2025-07-20','2025-10-20 16:03:10','2025-10-20 16:03:10'),(13,18,84.55,'A-','Lulus','Performa memuaskan sepanjang kegiatan','2025-09-23','2025-10-20 16:03:10','2025-10-20 16:03:10');
/*!40000 ALTER TABLE `mbkm_nilai_akhir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025-01-15-100000','App\\Database\\Migrations\\CreateNilaiTeknikPenilaianTable','default','App',1762089800,1),(2,'2025-01-16-100000','App\\Database\\Migrations\\AddValidationToJadwalMengajar','default','App',1762168438,2),(3,'2025-01-17-100000','App\\Database\\Migrations\\CreateGradeConfigTable','default','App',1762700001,3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mk_prasyarat`
--

LOCK TABLES `mk_prasyarat` WRITE;
/*!40000 ALTER TABLE `mk_prasyarat` DISABLE KEYS */;
INSERT INTO `mk_prasyarat` VALUES (1,6,2),(2,7,2),(3,8,6),(4,14,8),(5,15,14);
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
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_cpmk_mahasiswa`
--

LOCK TABLES `nilai_cpmk_mahasiswa` WRITE;
/*!40000 ALTER TABLE `nilai_cpmk_mahasiswa` DISABLE KEYS */;
INSERT INTO `nilai_cpmk_mahasiswa` VALUES (9,1,8,3,4.27,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(10,1,8,9,34.15,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(11,1,8,7,1.71,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(12,1,8,8,6.83,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(13,1,8,1,1.71,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(14,1,8,2,4.27,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(15,1,8,5,1.71,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(16,1,8,6,1.71,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(17,1,8,4,23.70,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(18,2,8,3,4.36,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(19,2,8,9,35.02,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(20,2,8,7,1.75,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(21,2,8,8,6.98,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(22,2,8,1,1.75,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(23,2,8,2,4.36,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(24,2,8,5,1.75,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(25,2,8,6,1.75,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(26,2,8,4,28.20,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(27,3,8,3,4.00,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(28,3,8,9,32.50,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(29,3,8,7,1.60,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(30,3,8,8,6.40,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(31,3,8,1,1.60,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(32,3,8,2,4.00,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(33,3,8,5,1.60,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(34,3,8,6,1.60,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(35,3,8,4,25.80,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(36,4,8,3,3.61,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(37,4,8,9,33.52,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(38,4,8,7,1.45,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(39,4,8,8,5.78,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(40,4,8,1,1.45,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(41,4,8,2,3.61,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(42,4,8,5,1.45,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(43,4,8,6,1.45,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(44,4,8,4,27.30,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(45,5,8,3,4.53,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(46,5,8,9,40.29,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(47,5,8,7,1.81,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(48,5,8,8,7.25,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(49,5,8,1,1.81,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(50,5,8,2,4.53,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(51,5,8,5,1.81,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(52,5,8,6,1.81,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(53,5,8,4,21.60,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(54,9,8,3,4.39,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(55,9,8,9,35.39,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(56,9,8,7,1.76,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(57,9,8,8,7.02,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(58,9,8,1,1.76,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(59,9,8,2,4.39,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(60,9,8,5,1.76,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(61,9,8,6,1.76,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(62,9,8,4,26.40,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(63,10,8,3,2.51,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(64,10,8,9,22.04,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(65,10,8,7,1.01,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(66,10,8,8,4.02,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(67,10,8,1,1.01,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(68,10,8,2,2.51,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(69,10,8,5,1.01,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(70,10,8,6,1.01,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(71,10,8,4,15.00,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(72,1,9,1,50.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(73,1,9,2,50.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(74,2,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(75,2,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(76,3,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(77,3,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(78,4,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(79,4,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(80,5,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(81,5,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(82,9,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(83,9,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(84,10,9,1,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(85,10,9,2,0.00,'2025-11-03 04:02:37','2025-11-03 04:02:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_mahasiswa`
--

LOCK TABLES `nilai_mahasiswa` WRITE;
/*!40000 ALTER TABLE `nilai_mahasiswa` DISABLE KEYS */;
INSERT INTO `nilai_mahasiswa` VALUES (6,1,8,80.06,'A','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(7,2,8,85.92,'A','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(8,3,8,79.10,'AB','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(9,4,8,79.62,'AB','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(10,5,8,85.44,'A','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(11,9,8,84.63,'A','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(12,10,8,50.12,'C','Lulus',NULL,'2025-11-02 09:58:35','2025-11-09 01:43:41'),(13,1,9,100.00,'A','Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(14,2,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(15,3,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(16,4,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(17,5,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(18,9,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37'),(19,10,9,0.00,'E','Tidak Lulus',NULL,'2025-11-03 04:02:37','2025-11-03 04:02:37');
/*!40000 ALTER TABLE `nilai_mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai_teknik_penilaian`
--

DROP TABLE IF EXISTS `nilai_teknik_penilaian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_teknik_penilaian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int NOT NULL,
  `jadwal_mengajar_id` int NOT NULL,
  `rps_mingguan_id` int NOT NULL COMMENT 'Reference to rps_mingguan where teknik_penilaian is defined',
  `teknik_penilaian_key` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'e.g., partisipasi, observasi, tes_tulis_uts, tes_tulis_uas, etc.',
  `nilai` decimal(5,2) DEFAULT NULL COMMENT 'Score for this teknik_penilaian (0-100)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nilai_teknik_penilaian_jadwal_mengajar_id_foreign` (`jadwal_mengajar_id`),
  KEY `nilai_teknik_penilaian_rps_mingguan_id_foreign` (`rps_mingguan_id`),
  KEY `idx_nilai_teknik` (`mahasiswa_id`,`jadwal_mengajar_id`,`rps_mingguan_id`,`teknik_penilaian_key`),
  CONSTRAINT `nilai_teknik_penilaian_jadwal_mengajar_id_foreign` FOREIGN KEY (`jadwal_mengajar_id`) REFERENCES `jadwal_mengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nilai_teknik_penilaian_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nilai_teknik_penilaian_rps_mingguan_id_foreign` FOREIGN KEY (`rps_mingguan_id`) REFERENCES `rps_mingguan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_teknik_penilaian`
--

LOCK TABLES `nilai_teknik_penilaian` WRITE;
/*!40000 ALTER TABLE `nilai_teknik_penilaian` DISABLE KEYS */;
INSERT INTO `nilai_teknik_penilaian` VALUES (1,1,8,147,'observasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(2,1,8,141,'partisipasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(3,1,8,142,'partisipasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(4,1,8,143,'partisipasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(5,1,8,144,'partisipasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(6,1,8,145,'partisipasi',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(7,1,8,146,'tes_lisan',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(8,1,8,149,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(9,1,8,150,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(10,1,8,151,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(11,1,8,152,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(12,1,8,153,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(13,1,8,154,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(14,1,8,155,'proyek',85.38,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(15,1,8,148,'tes_tulis_uts',79.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(16,1,8,156,'tes_tulis_uas',74.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(17,2,8,147,'observasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(18,2,8,141,'partisipasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(19,2,8,142,'partisipasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(20,2,8,143,'partisipasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(21,2,8,144,'partisipasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(22,2,8,145,'partisipasi',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(23,2,8,146,'tes_lisan',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(24,2,8,149,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(25,2,8,150,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(26,2,8,151,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(27,2,8,152,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(28,2,8,153,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(29,2,8,154,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(30,2,8,155,'proyek',87.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(31,2,8,148,'tes_tulis_uts',94.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(32,2,8,156,'tes_tulis_uas',76.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(33,3,8,147,'observasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(34,3,8,141,'partisipasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(35,3,8,142,'partisipasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(36,3,8,143,'partisipasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(37,3,8,144,'partisipasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(38,3,8,145,'partisipasi',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(39,3,8,146,'tes_lisan',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(40,3,8,149,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(41,3,8,150,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(42,3,8,151,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(43,3,8,152,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(44,3,8,153,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(45,3,8,154,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(46,3,8,155,'proyek',80.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(47,3,8,148,'tes_tulis_uts',86.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(48,3,8,156,'tes_tulis_uas',71.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(49,4,8,147,'observasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(50,4,8,141,'partisipasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(51,4,8,142,'partisipasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(52,4,8,143,'partisipasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(53,4,8,144,'partisipasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(54,4,8,145,'partisipasi',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(55,4,8,146,'tes_lisan',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(56,4,8,149,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(57,4,8,150,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(58,4,8,151,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(59,4,8,152,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(60,4,8,153,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(61,4,8,154,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(62,4,8,155,'proyek',72.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(63,4,8,148,'tes_tulis_uts',91.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(64,4,8,156,'tes_tulis_uas',78.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(65,5,8,147,'observasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(66,5,8,141,'partisipasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(67,5,8,142,'partisipasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(68,5,8,143,'partisipasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(69,5,8,144,'partisipasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(70,5,8,145,'partisipasi',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(71,5,8,146,'tes_lisan',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(72,5,8,149,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(73,5,8,150,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(74,5,8,151,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(75,5,8,152,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(76,5,8,153,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(77,5,8,154,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(78,5,8,155,'proyek',90.63,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(79,5,8,148,'tes_tulis_uts',72.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(80,5,8,156,'tes_tulis_uas',92.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(81,9,8,147,'observasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(82,9,8,141,'partisipasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(83,9,8,142,'partisipasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(84,9,8,143,'partisipasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(85,9,8,144,'partisipasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(86,9,8,145,'partisipasi',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(87,9,8,146,'tes_lisan',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(88,9,8,149,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(89,9,8,150,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(90,9,8,151,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(91,9,8,152,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(92,9,8,153,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(93,9,8,154,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(94,9,8,155,'proyek',87.75,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(95,9,8,148,'tes_tulis_uts',88.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(96,9,8,156,'tes_tulis_uas',77.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(97,10,8,147,'observasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(98,10,8,141,'partisipasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(99,10,8,142,'partisipasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(100,10,8,143,'partisipasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(101,10,8,144,'partisipasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(102,10,8,145,'partisipasi',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(103,10,8,146,'tes_lisan',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(104,10,8,149,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(105,10,8,150,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(106,10,8,151,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(107,10,8,152,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(108,10,8,153,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(109,10,8,154,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(110,10,8,155,'proyek',50.25,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(111,10,8,148,'tes_tulis_uts',50.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(112,10,8,156,'tes_tulis_uas',50.00,'2025-11-02 16:58:35','2025-11-09 08:43:41'),(113,1,9,157,'tes_tulis_uts',100.00,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(114,1,9,158,'tes_tulis_uas',100.00,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(115,2,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(116,2,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(117,3,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(118,3,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(119,4,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(120,4,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(121,5,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(122,5,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(123,9,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(124,9,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(125,10,9,157,'tes_tulis_uts',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37'),(126,10,9,158,'tes_tulis_uas',NULL,'2025-11-03 11:02:37','2025-11-03 11:02:37');
/*!40000 ALTER TABLE `nilai_teknik_penilaian` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profil_lulusan`
--

LOCK TABLES `profil_lulusan` WRITE;
/*!40000 ALTER TABLE `profil_lulusan` DISABLE KEYS */;
INSERT INTO `profil_lulusan` VALUES (1,'PL01','Lulusan memiliki kemampuan untuk menganalisa dan menyelesaikan berbagai permasalahan dengan prinsip-prinsip computing.','2025-06-14 13:45:24'),(2,'PL02','Lulusan memiliki kemampuan menganalisis, merancang, dan mengimplementasikan perangkat lunak serta solusi berbasis komputasi, termasuk kecerdasan buatan, yang sesuai dengan kebutuhan pengguna','2025-06-14 13:45:24'),(3,'PL03','Lulusan mampu bertindak dan menilai secara profesional sesuai dengan nilai-nilai Pancasila','2025-06-14 13:45:24'),(4,'PL04','Lulusan mampu berpikir logis, kritis serta sistematis dalam memanfaatkan ilmu pengetahuan informatika/ ilmu komputer untuk menyelesaikan masalah nyata.','2025-08-05 14:10:22');
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
-- Dumping data for table `profil_prodi`
--

LOCK TABLES `profil_prodi` WRITE;
/*!40000 ALTER TABLE `profil_prodi` DISABLE KEYS */;
INSERT INTO `profil_prodi` VALUES (1,'UNIVERSITAS PALANGKA RAYA','TEKNIK','TEKNIK INFORMATIKA','Ariesta Lestari, S.Kom., M.Cs., PhD','198003222005012004','Frieda, S.T., M.T','197212231997022002','logo_upr.png','2025-08-27 19:14:43');
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
  `tahun_ajaran` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_penyusunan` date DEFAULT NULL,
  `status` enum('draft','final','arsip') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `rps_ibfk_1` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps`
--

LOCK TABLES `rps` WRITE;
/*!40000 ALTER TABLE `rps` DISABLE KEYS */;
INSERT INTO `rps` VALUES (13,8,3,'2025/2026','2025-11-02','draft','','2025-11-02 13:26:01','2025-11-02 13:26:01'),(14,15,3,'2024/2025','2025-11-03','draft','','2025-11-03 10:55:43','2025-11-03 10:55:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps_mingguan`
--

LOCK TABLES `rps_mingguan` WRITE;
/*!40000 ALTER TABLE `rps_mingguan` DISABLE KEYS */;
INSERT INTO `rps_mingguan` VALUES (141,13,1,7,9,'[\"Perkuliahan\"]',17,'[\"Apa itu indikator\"]','[\"Kehadiran\"]','{\"partisipasi\":2}','[]','Penjelasan RPL','[\"Kuliah\"]',2,'2025-11-02 13:27:04'),(142,13,2,7,9,'[\"Perkuliahan\"]',18,'[\"Halo world\"]','[\"Kehadiran\"]','{\"partisipasi\":2}','[]','','[\"Kuliah\"]',2,'2025-11-02 13:27:54'),(143,13,3,7,7,'[\"Perkuliahan\"]',21,'[\"Test\"]','[\"Kehadiran\"]','{\"partisipasi\":2}','[]','','[\"Kuliah\"]',2,'2025-11-02 13:36:25'),(144,13,4,7,8,'[\"Perkuliahan\"]',22,'[\"Test\"]','[\"Kehadiran\"]','{\"partisipasi\":2}','[]','','[\"Kuliah\"]',2,'2025-11-02 13:36:52'),(145,13,5,1,1,'[\"Perkuliahan\"]',23,'[\"Test\"]','[\"Kehadiran\"]','{\"partisipasi\":2}','[]','','[]',2,'2025-11-02 13:37:26'),(146,13,6,2,2,'[\"Perkuliahan\"]',24,'[\"Test\"]','[\"Ketepatan Jawaban Kuis\",\"Ketepatan Jawaban Tugas\"]','{\"tes_lisan\":5}','[]','','[\"Case study\"]',5,'2025-11-02 13:38:15'),(147,13,7,3,3,'[\"Perkuliahan\"]',25,'[\"Test\"]','[\"Ketepatan Jawaban Tugas\"]','{\"observasi\":5}','[]','','[\"Case study\"]',5,'2025-11-02 13:38:55'),(148,13,8,4,4,'[\"Tengah Semester\"]',26,'[\"Test\"]','[\"Ketepatan Jawaban UTS\"]','{\"tes_tulis_uts\":30}','[]','','[\"Kuliah\"]',30,'2025-11-02 13:39:56'),(149,13,9,5,5,'[\"Tengah Semester\"]',27,'[\"Test\"]','[\"Kualitas Presentasi\"]','{\"proyek\":2}','[]','','[]',2,'2025-11-02 13:40:44'),(150,13,10,6,6,'[\"Tengah Semester\"]',28,'[\"Test\"]','[\"Ketepatan Jawaban Tugas\"]','{\"proyek\":2}','[]','','[\"Team Base Project\"]',2,'2025-11-02 13:41:24'),(151,13,11,8,8,'[\"Tengah Semester\"]',22,'[\"Test\"]','[\"Ketepatan Jawaban Tugas\"]','{\"proyek\":2}','[]','','[\"Team Base Project\"]',2,'2025-11-02 13:42:10'),(152,13,12,8,8,'[\"Tengah Semester\"]',29,'[\"Test\"]','[\"Ketepatan Jawaban Tugas\"]','{\"proyek\":2}','[]','','[\"Team Base Project\"]',2,'2025-11-02 13:43:31'),(153,13,13,8,8,'[\"Tengah Semester\"]',30,'[\"Test\"]','[\"Ketepatan Jawaban Tugas\"]','{\"proyek\":2}','[]','','[\"Team Base Project\"]',2,'2025-11-02 13:43:58'),(154,13,14,9,9,'[\"Tengah Semester\"]',17,'[\"Test\"]','[\"Hasil Proyek\"]','{\"proyek\":5}','[]','','[\"Team Base Project\"]',5,'2025-11-02 13:44:46'),(155,13,15,9,9,'[\"Tengah Semester\"]',18,'[\"Test\"]','[\"Hasil Proyek\"]','{\"proyek\":5}','[]','','[\"Team Base Project\"]',5,'2025-11-02 13:45:23'),(156,13,16,9,9,'[\"Akhir Semester\"]',31,'[\"Test\"]','[\"Ketepatan Jawaban UAS\"]','{\"tes_tulis_uas\":30}','[]','','[\"Kuliah\"]',30,'2025-11-02 13:46:15'),(157,14,1,1,1,'[\"Tengah Semester\"]',32,'[\"Test\"]','[\"Ketepatan Jawaban UTS\"]','{\"tes_tulis_uts\":50}','[]','','[]',50,'2025-11-03 11:00:20'),(158,14,2,2,2,'[\"Akhir Semester\"]',33,'[\"Test\"]','[\"Ketepatan Jawaban UAS\"]','{\"tes_tulis_uas\":50}','[]','','[]',50,'2025-11-03 11:00:45');
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
  `peran` enum('pengampu','koordinator','penyusun') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pengampu',
  PRIMARY KEY (`id`),
  KEY `rps_id` (`rps_id`),
  KEY `fk_rps_pengampu_to_dosen` (`dosen_id`),
  CONSTRAINT `fk_rps_pengampu_to_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rps_pengampu_ibfk_1` FOREIGN KEY (`rps_id`) REFERENCES `rps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rps_pengampu`
--

LOCK TABLES `rps_pengampu` WRITE;
/*!40000 ALTER TABLE `rps_pengampu` DISABLE KEYS */;
INSERT INTO `rps_pengampu` VALUES (29,13,4,'pengampu'),(30,13,4,'koordinator'),(31,14,1,'pengampu'),(32,14,1,'koordinator');
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
-- Dumping data for table `rps_referensi`
--

LOCK TABLES `rps_referensi` WRITE;
/*!40000 ALTER TABLE `rps_referensi` DISABLE KEYS */;
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
  `kode_sub_cpmk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cpmk_id` (`cpmk_id`),
  CONSTRAINT `sub_cpmk_ibfk_1` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_cpmk`
--

LOCK TABLES `sub_cpmk` WRITE;
/*!40000 ALTER TABLE `sub_cpmk` DISABLE KEYS */;
INSERT INTO `sub_cpmk` VALUES (1,1,'SubCPMK0111','Menjelaskan nilai-nilai Pancasila','2025-08-20 06:17:34','2025-08-20 13:20:35'),(2,1,'SubCPMK0112','Mengaplikasikan nilai-nilai Pancasila dalam studi kasus','2025-08-22 04:16:01','2025-08-22 04:16:01'),(3,2,'SubCPMK0221','Mengidentifikasi perkembangan teknologi terkini','2025-08-20 05:22:17','2025-08-20 05:22:17'),(4,2,'SubCPMK0222','Menerapkan teknologi terkini dalam solusi','2025-09-24 03:40:10','2025-09-24 03:40:10'),(5,3,'SubCPMK0331','Menerapkan teori probabilitas','2025-08-27 22:01:35','2025-08-27 22:01:35'),(6,3,'SubCPMK0332','Menerapkan metode statistik','2025-10-10 17:07:34','2025-10-10 17:07:34'),(7,4,'SubCPMK0411','Menganalisis algoritma pemrograman','2025-08-27 17:50:38','2025-08-28 00:56:35'),(8,4,'SubCPMK0412','Mengimplementasikan solusi pemrograman','2025-08-27 17:50:49','2025-08-27 17:50:49'),(9,5,'SubCPMK0531','Menulis laporan teknis','2025-08-27 17:51:10','2025-08-27 17:51:10'),(10,5,'SubCPMK0532','Menyusun dokumentasi sistem','2025-08-27 17:51:23','2025-08-27 17:51:23'),(11,6,'SubCPMK0621','Menerapkan metodologi penelitian','2025-10-10 17:29:09','2025-10-10 17:29:09'),(12,6,'SubCPMK0622','Menganalisis data penelitian','2025-10-10 17:03:08','2025-10-10 17:03:08'),(13,7,'SubCPMK0711','Menganalisis struktur data','2025-08-27 17:50:38','2025-08-28 00:56:35'),(14,7,'SubCPMK0712','Mengimplementasikan struktur data','2025-08-27 17:50:49','2025-08-27 17:50:49'),(15,8,'SubCPMK0811','Memahami konsep OOP','2025-08-27 17:51:10','2025-08-27 17:51:10'),(16,8,'SubCPMK0812','Mengimplementasikan OOP','2025-08-27 17:51:23','2025-08-27 17:51:23'),(17,9,'SubCPMK0911','Merancang ERD','2025-08-27 17:51:10','2025-08-27 17:51:10'),(18,9,'SubCPMK0912','Mengimplementasikan database','2025-08-27 17:51:23','2025-08-27 17:51:23'),(19,10,'SubCPMK1011','Mengidentifikasi ancaman keamanan','2025-08-27 17:51:10','2025-08-27 17:51:10'),(20,10,'SubCPMK1012','Menerapkan kontrol keamanan','2025-08-27 17:51:23','2025-08-27 17:51:23'),(21,7,'SubCPMK0713','Test','2025-11-02 06:33:54','2025-11-02 06:33:54'),(22,8,'SubCPMK0813','TEST','2025-11-02 06:34:21','2025-11-02 06:34:21'),(23,1,'SubCPMK0113','Test','2025-11-02 06:34:43','2025-11-02 06:34:43'),(24,2,'SubCPMK0223','Test','2025-11-02 06:34:57','2025-11-02 06:34:57'),(25,3,'SubCPMK0333','Test','2025-11-02 06:35:05','2025-11-02 06:35:05'),(26,4,'SubCPMK0413','Test','2025-11-02 06:35:18','2025-11-02 06:35:18'),(27,5,'SubCPMK0533','Test','2025-11-02 06:35:32','2025-11-02 06:35:32'),(28,6,'SubCPMK0623','Test','2025-11-02 06:35:40','2025-11-02 06:35:40'),(29,8,'SubCPMK0814','Test','2025-11-02 06:42:33','2025-11-02 06:42:33'),(30,8,'SubCPMK0815','Test','2025-11-02 06:42:44','2025-11-02 06:42:44'),(31,9,'SubCPMK0913','Test','2025-11-02 06:45:38','2025-11-02 06:45:38'),(32,1,'SubCPMK0114','Test','2025-11-03 03:59:20','2025-11-03 03:59:20'),(33,2,'SubCPMK0224','Test','2025-11-03 03:59:30','2025-11-03 03:59:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_cpmk_mk`
--

LOCK TABLES `sub_cpmk_mk` WRITE;
/*!40000 ALTER TABLE `sub_cpmk_mk` DISABLE KEYS */;
INSERT INTO `sub_cpmk_mk` VALUES (1,1,11,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(2,2,11,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(3,3,11,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(4,4,11,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(5,5,14,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(6,6,14,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(7,7,2,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(8,8,2,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(9,9,12,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(10,10,12,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(11,11,12,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(12,12,12,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(13,13,6,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(14,14,6,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(15,15,7,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(16,16,7,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(17,17,8,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(18,18,8,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(19,19,13,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(20,20,13,'2025-10-20 15:39:26','2025-10-20 15:39:26'),(21,21,8,'2025-11-02 06:33:54','2025-11-02 06:33:54'),(22,22,8,'2025-11-02 06:34:21','2025-11-02 06:34:21'),(23,23,8,'2025-11-02 06:34:43','2025-11-02 06:34:43'),(24,24,8,'2025-11-02 06:34:57','2025-11-02 06:34:57'),(25,25,8,'2025-11-02 06:35:05','2025-11-02 06:35:05'),(26,26,8,'2025-11-02 06:35:18','2025-11-02 06:35:18'),(27,27,8,'2025-11-02 06:35:32','2025-11-02 06:35:32'),(28,28,8,'2025-11-02 06:35:40','2025-11-02 06:35:40'),(29,29,8,'2025-11-02 06:42:33','2025-11-02 06:42:33'),(30,30,8,'2025-11-02 06:42:44','2025-11-02 06:42:44'),(31,31,8,'2025-11-02 06:45:38','2025-11-02 06:45:38'),(32,32,15,'2025-11-03 03:59:20','2025-11-03 03:59:20'),(33,33,15,'2025-11-03 03:59:30','2025-11-03 03:59:30');
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
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','dosen','mahasiswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$uCUV3FbS0QoHEGJ5vLKbUetKDUmY09jg.SWhb1iLzvso9mmqm0B2a','admin','2025-05-27 21:50:24','2025-08-27 22:55:47'),(10,'dosen','$2y$12$6PGYbrXL.lOBhMdT3/5ej.OblG3AKkOvkEzHHC3ybIBlmEj2nTJ6.','dosen','2025-11-03 11:16:30','2025-11-03 11:16:30');
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

-- Dump completed on 2025-11-09 22:29:10
