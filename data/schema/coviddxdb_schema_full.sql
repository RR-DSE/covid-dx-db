
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,NO_TABLE_OPTIONS' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `access_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_control` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(32) NOT NULL,
  `sample_id` char(32) NOT NULL,
  `password` char(16) NOT NULL,
  `datetime` datetime NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(32) NOT NULL,
  `title` char(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `location` char(64) NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_patients` (
  `id` bigint(20) unsigned NOT NULL,
  `name` char(128) NOT NULL,
  `birthday` date NOT NULL,
  `gender` tinyint(3) unsigned NOT NULL,
  `record` char(32) DEFAULT NULL,
  `state_id_1` char(32) DEFAULT NULL,
  `patient_status` char(32) NOT NULL,
  `patient_status_datetime` datetime NOT NULL,
  `department` bigint(20) unsigned DEFAULT NULL,
  `admittance_date` date DEFAULT NULL,
  `floor` char(128) DEFAULT NULL,
  `room` char(32) DEFAULT NULL,
  `bed` char(32) DEFAULT NULL,
  `hcworker` tinyint(1) DEFAULT NULL,
  `sinave` tinyint(1) DEFAULT NULL,
  `sinave_datetime` datetime DEFAULT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_tests` (
  `id` bigint(20) unsigned NOT NULL,
  `name` char(128) NOT NULL,
  `birthday` date NOT NULL,
  `gender` tinyint(3) unsigned NOT NULL,
  `record` char(32) DEFAULT NULL,
  `episode` char(32) DEFAULT NULL,
  `state_id_1` char(32) DEFAULT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `department` char(128) DEFAULT NULL,
  `department_code` char(32) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `test_code` char(32) NOT NULL,
  `method_code` char(32) DEFAULT NULL,
  `sample_id` char(32) NOT NULL,
  `sample_id_2` int(10) unsigned NOT NULL,
  `accession` char(32) DEFAULT NULL,
  `sample_date` date NOT NULL,
  `result_code` char(32) DEFAULT NULL,
  `result_comments` varchar(512) DEFAULT NULL,
  `result_datetime` datetime DEFAULT NULL,
  `tec_validator` char(128) DEFAULT NULL,
  `validation_datetime` datetime DEFAULT NULL,
  `bio_validator` char(128) DEFAULT NULL,
  `state_report_1` tinyint(1) DEFAULT NULL,
  `state_report_1_datetime` datetime DEFAULT NULL,
  `address_1` varchar(256) DEFAULT NULL,
  `address_2` varchar(64) DEFAULT NULL,
  `email` char(64) DEFAULT NULL,
  `phone` char(32) DEFAULT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `prescriber` char(128) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_transmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_transmissions` (
  `id` bigint(20) unsigned NOT NULL,
  `sample_id` char(32) NOT NULL,
  `class` enum('email','phone','print','internal','unknown') NOT NULL,
  `description` varchar(256) NOT NULL,
  `recipient_class` enum('patient','clinician','manager','caregiver','family','unknown') NOT NULL,
  `recipient_description` varchar(256) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` enum('ok','pending','error','canceled') NOT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `phone` char(16) DEFAULT NULL,
  `contacts` varchar(512) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `birthday` date DEFAULT NULL,
  `state_id` char(32) DEFAULT NULL,
  `email` char(128) NOT NULL,
  `class` enum('patient','clinician','manager','caregiver','family') DEFAULT NULL,
  `date` date NOT NULL,
  `datetime` datetime NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `birthday` date DEFAULT NULL,
  `sample` char(32) DEFAULT NULL,
  `state_id` char(32) DEFAULT NULL,
  `group` char(128) NOT NULL,
  `class` enum('patient','collaborator','internal','contact') NOT NULL,
  `department` char(128) DEFAULT NULL,
  `category` char(128) DEFAULT NULL,
  `location` char(128) DEFAULT NULL,
  `social_institution` tinyint(1) DEFAULT NULL,
  `institution_class` enum('nursing_home','school','children_home','healthcare_unit','other') DEFAULT NULL,
  `date` date NOT NULL,
  `source` char(32) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `sample` (`sample`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `birthday` date NOT NULL,
  `gender` tinyint(3) unsigned NOT NULL,
  `record` char(32) DEFAULT NULL,
  `state_id_1` char(32) DEFAULT NULL,
  `patient_status` char(32) NOT NULL,
  `patient_status_datetime` datetime NOT NULL,
  `department` bigint(20) unsigned DEFAULT NULL,
  `admittance_date` date DEFAULT NULL,
  `floor` char(128) DEFAULT NULL,
  `room` char(32) DEFAULT NULL,
  `bed` char(32) DEFAULT NULL,
  `hcworker` tinyint(1) DEFAULT NULL,
  `sinave` tinyint(1) DEFAULT NULL,
  `sinave_datetime` datetime DEFAULT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profilesetup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profilesetup` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile` bigint(20) unsigned NOT NULL,
  `app` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `user` bigint(20) unsigned NOT NULL,
  `session_id` char(128) NOT NULL,
  `host` char(128) NOT NULL,
  `status` enum('offline','online') NOT NULL,
  `lastact` datetime NOT NULL,
  PRIMARY KEY (`user`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `birthday` date NOT NULL,
  `gender` tinyint(3) unsigned NOT NULL,
  `record` char(32) DEFAULT NULL,
  `episode` char(32) DEFAULT NULL,
  `state_id_1` char(32) DEFAULT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `department` char(128) DEFAULT NULL,
  `department_code` char(32) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `test_code` char(32) NOT NULL,
  `method_code` char(32) DEFAULT NULL,
  `sample_id` char(32) NOT NULL,
  `sample_id_2` int(10) unsigned NOT NULL,
  `accession` char(32) DEFAULT NULL,
  `sample_date` date NOT NULL,
  `result_code` char(32) DEFAULT NULL,
  `result_comments` varchar(512) DEFAULT NULL,
  `result_datetime` datetime DEFAULT NULL,
  `tec_validator` char(128) DEFAULT NULL,
  `validation_datetime` datetime DEFAULT NULL,
  `bio_validator` char(128) DEFAULT NULL,
  `state_report_1` tinyint(1) DEFAULT NULL,
  `state_report_1_datetime` datetime DEFAULT NULL,
  `address_1` varchar(256) DEFAULT NULL,
  `address_2` varchar(64) DEFAULT NULL,
  `email` char(64) DEFAULT NULL,
  `phone` char(32) DEFAULT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `prescriber` char(128) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `sample_id` (`sample_id`),
  KEY `sample_id_2` (`sample_id_2`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transmissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sample_id` char(32) NOT NULL,
  `class` enum('email','phone','print','internal','unknown') NOT NULL,
  `description` varchar(256) NOT NULL,
  `recipient_class` enum('patient','clinician','manager','caregiver','family','unknown') NOT NULL,
  `recipient_description` varchar(256) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` enum('ok','pending','error','canceled') NOT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL,
  `password` char(64) NOT NULL,
  `profile` bigint(20) unsigned NOT NULL,
  `name` char(128) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `status_notes` varchar(512) DEFAULT NULL,
  `add_user` bigint(20) unsigned NOT NULL,
  `add_datetime` datetime NOT NULL,
  `mod_user` bigint(20) unsigned NOT NULL,
  `mod_datetime` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

