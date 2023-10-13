-- MySQL dump 10.13  Distrib 8.0.34, for Linux (x86_64)
--
-- Host: mysql    Database: laravel
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `airport_airport_group`
--

DROP TABLE IF EXISTS `airport_airport_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airport_airport_group` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `airport_id` bigint unsigned NOT NULL,
  `airport_group_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `airport_airport_group_id` (`airport_id`,`airport_group_id`),
  KEY `airport_airport_group_airport_group_id_foreign` (`airport_group_id`),
  CONSTRAINT `airport_airport_group_airport_group_id_foreign` FOREIGN KEY (`airport_group_id`) REFERENCES `airport_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `airport_airport_group_airport_id_foreign` FOREIGN KEY (`airport_id`) REFERENCES `airports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `airport_airport_group`
--

LOCK TABLES `airport_airport_group` WRITE;
/*!40000 ALTER TABLE `airport_airport_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `airport_airport_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `airport_groups`
--

DROP TABLE IF EXISTS `airport_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airport_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The name of the group',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `airport_group_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `airport_groups`
--

LOCK TABLES `airport_groups` WRITE;
/*!40000 ALTER TABLE `airport_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `airport_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `airports`
--

DROP TABLE IF EXISTS `airports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `icao_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E.g. EGGD',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `airports_icao_code_unique` (`icao_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `airports`
--

LOCK TABLES `airports` WRITE;
/*!40000 ALTER TABLE `airports` DISABLE KEYS */;
/*!40000 ALTER TABLE `airports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_notification_types`
--

DROP TABLE IF EXISTS `discord_notification_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_notification_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discord_notification_types_type_unique` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_notification_types`
--

LOCK TABLES `discord_notification_types` WRITE;
/*!40000 ALTER TABLE `discord_notification_types` DISABLE KEYS */;
INSERT INTO `discord_notification_types` VALUES (1,'flow_measure_notified','2023-10-08 12:08:17'),(2,'flow_measure_activated','2023-10-08 12:08:17'),(3,'flow_measure_withdrawn','2023-10-08 12:08:17'),(4,'flow_measure_expired','2023-10-08 12:08:17');
/*!40000 ALTER TABLE `discord_notification_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_tag_flight_information_region`
--

DROP TABLE IF EXISTS `discord_tag_flight_information_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_tag_flight_information_region` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flight_information_region_id` bigint unsigned NOT NULL,
  `discord_tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discord_tag_flight_information_region` (`discord_tag_id`,`flight_information_region_id`),
  KEY `discord_flight_information_region_id` (`flight_information_region_id`),
  CONSTRAINT `discord_discord_tag_id` FOREIGN KEY (`discord_tag_id`) REFERENCES `discord_tags` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discord_flight_information_region_id` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_tag_flight_information_region`
--

LOCK TABLES `discord_tag_flight_information_region` WRITE;
/*!40000 ALTER TABLE `discord_tag_flight_information_region` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_tag_flight_information_region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_tags`
--

DROP TABLE IF EXISTS `discord_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The tag to use',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'What the tag is for / who it is targeted at',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discord_tags_tag_unique` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_tags`
--

LOCK TABLES `discord_tags` WRITE;
/*!40000 ALTER TABLE `discord_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_notification_flow_measure`
--

DROP TABLE IF EXISTS `discord_notification_flow_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_notification_flow_measure` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discord_notification_id` bigint unsigned NOT NULL,
  `flow_measure_id` bigint unsigned NOT NULL,
  `discord_notification_type_id` bigint unsigned NOT NULL,
  `notified_as` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'What the identifier of the flow measure was at the time the discord notification was sent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discord_flow_measure_discord` (`discord_notification_id`),
  KEY `discord_flow_measure_flow` (`flow_measure_id`),
  KEY `discord_flow_measure_type` (`discord_notification_type_id`),
  CONSTRAINT `discord_flow_measure_discord` FOREIGN KEY (`discord_notification_id`) REFERENCES `discord_notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discord_flow_measure_flow` FOREIGN KEY (`flow_measure_id`) REFERENCES `flow_measures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discord_flow_measure_type` FOREIGN KEY (`discord_notification_type_id`) REFERENCES `discord_notification_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_notification_flow_measure`
--

LOCK TABLES `discord_notification_flow_measure` WRITE;
/*!40000 ALTER TABLE `discord_notification_flow_measure` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_notification_flow_measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_notifications`
--

DROP TABLE IF EXISTS `discord_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `division_discord_webhook_id` bigint unsigned DEFAULT NULL COMMENT 'Which divisional discord server this notification was sent to',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `embeds` json DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `discord_notifications_webhook` (`division_discord_webhook_id`),
  KEY `discord_notifications_created_at_index` (`created_at`),
  CONSTRAINT `discord_notifications_webhook` FOREIGN KEY (`division_discord_webhook_id`) REFERENCES `division_discord_webhooks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_notifications`
--

LOCK TABLES `discord_notifications` WRITE;
/*!40000 ALTER TABLE `discord_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `division_discord_webhook_flight_information_region`
--

DROP TABLE IF EXISTS `division_discord_webhook_flight_information_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division_discord_webhook_flight_information_region` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `division_discord_webhook_id` bigint unsigned NOT NULL,
  `flight_information_region_id` bigint unsigned NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discord_webhook_fir_unique` (`division_discord_webhook_id`,`flight_information_region_id`),
  KEY `division_discord_fir` (`flight_information_region_id`),
  CONSTRAINT `division_discord_fir` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `division_discord_fir_discord` FOREIGN KEY (`division_discord_webhook_id`) REFERENCES `division_discord_webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `division_discord_webhook_flight_information_region`
--

LOCK TABLES `division_discord_webhook_flight_information_region` WRITE;
/*!40000 ALTER TABLE `division_discord_webhook_flight_information_region` DISABLE KEYS */;
/*!40000 ALTER TABLE `division_discord_webhook_flight_information_region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `division_discord_webhooks`
--

DROP TABLE IF EXISTS `division_discord_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division_discord_webhooks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The webhook URL',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'What this webhook is for',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `division_discord_webhooks_url_unique` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `division_discord_webhooks`
--

LOCK TABLES `division_discord_webhooks` WRITE;
/*!40000 ALTER TABLE `division_discord_webhooks` DISABLE KEYS */;
/*!40000 ALTER TABLE `division_discord_webhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_participants`
--

DROP TABLE IF EXISTS `event_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `cid` bigint unsigned NOT NULL,
  `origin` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_participants_event_id_foreign` (`event_id`),
  CONSTRAINT `event_participants_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_participants`
--

LOCK TABLES `event_participants` WRITE;
/*!40000 ALTER TABLE `event_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The event name',
  `date_start` datetime NOT NULL COMMENT 'When the event begins (Z)',
  `date_end` datetime NOT NULL COMMENT 'When the event ends (Z)',
  `flight_information_region_id` bigint unsigned NOT NULL,
  `vatcan_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The VATCAN events system code',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_flight_information_region_id_foreign` (`flight_information_region_id`),
  KEY `events_date_start_date_end_index` (`date_start`,`date_end`),
  KEY `events_deleted_at_index` (`deleted_at`),
  KEY `events_created_at_index` (`created_at`),
  CONSTRAINT `events_flight_information_region_id_foreign` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_information_region_flow_measure`
--

DROP TABLE IF EXISTS `flight_information_region_flow_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_information_region_flow_measure` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flow_measure_id` bigint unsigned NOT NULL COMMENT 'The flow measure',
  `flight_information_region_id` bigint unsigned NOT NULL COMMENT 'The flight information region that needs to be concerned with the flow measure',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fir_flow_measure_unique` (`flight_information_region_id`,`flow_measure_id`),
  KEY `flight_information_region_flow_measure` (`flow_measure_id`),
  CONSTRAINT `flight_information_region_flow_measure` FOREIGN KEY (`flow_measure_id`) REFERENCES `flow_measures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flow_measure_flight_information_region` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_information_region_flow_measure`
--

LOCK TABLES `flight_information_region_flow_measure` WRITE;
/*!40000 ALTER TABLE `flight_information_region_flow_measure` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_information_region_flow_measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_information_region_user`
--

DROP TABLE IF EXISTS `flight_information_region_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_information_region_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `flight_information_region_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flight_information_region_user` (`user_id`,`flight_information_region_id`),
  KEY `flight_information_region_id` (`flight_information_region_id`),
  CONSTRAINT `flight_information_region_id` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flight_information_region_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_information_region_user`
--

LOCK TABLES `flight_information_region_user` WRITE;
/*!40000 ALTER TABLE `flight_information_region_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_information_region_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_information_regions`
--

DROP TABLE IF EXISTS `flight_information_regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_information_regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The FIR id, e.g. EGTT',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The name of the FIR, e.g. London',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flight_information_regions_identifier_unique` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_information_regions`
--

LOCK TABLES `flight_information_regions` WRITE;
/*!40000 ALTER TABLE `flight_information_regions` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_information_regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flow_measures`
--

DROP TABLE IF EXISTS `flow_measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flow_measures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The identifier of the flow rule',
  `user_id` bigint unsigned NOT NULL COMMENT 'The user who created this flow measure',
  `flight_information_region_id` bigint unsigned NOT NULL COMMENT 'The flight information region issuing this flow measure',
  `event_id` bigint unsigned DEFAULT NULL COMMENT 'The event that this measure belongs to, if any',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The reason given for the flow measure being in place',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The type of flow measure',
  `value` int unsigned DEFAULT NULL COMMENT 'Used to specify the value of the measure, for all but mandatory_route',
  `mandatory_route` json DEFAULT NULL COMMENT 'Used to specify mandatory route strings',
  `filters` json NOT NULL COMMENT 'Any filters applied to the rule',
  `start_time` datetime NOT NULL COMMENT 'When the flow measure starts (Z)',
  `end_time` datetime NOT NULL COMMENT 'When the flow measure ends (Z)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flow_measures_user_id_foreign` (`user_id`),
  KEY `flow_measures_flight_information_region_id_foreign` (`flight_information_region_id`),
  KEY `flow_measures_event_id_foreign` (`event_id`),
  KEY `flow_measures_start_time_end_time_index` (`start_time`,`end_time`),
  KEY `flow_measures_deleted_at_index` (`deleted_at`),
  KEY `flow_measures_created_at_index` (`created_at`),
  KEY `flow_measures_identifier_index` (`identifier`),
  CONSTRAINT `flow_measures_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flow_measures_flight_information_region_id_foreign` FOREIGN KEY (`flight_information_region_id`) REFERENCES `flight_information_regions` (`id`),
  CONSTRAINT `flow_measures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flow_measures`
--

LOCK TABLES `flow_measures` WRITE;
/*!40000 ALTER TABLE `flow_measures` DISABLE KEYS */;
/*!40000 ALTER TABLE `flow_measures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2022_04_26_194754_create_flight_information_regions_table',1),(2,'2022_04_26_194754_create_roles_table',1),(3,'2022_04_26_194842_create_users_table',1),(4,'2022_04_26_195947_add_roles',1),(5,'2022_04_26_201324_create_flight_information_region_user_table',1),(6,'2022_04_26_211837_create_airports_table',1),(7,'2022_04_26_211905_create_airport_groups_table',1),(8,'2022_04_26_211957_create_airport_airport_group_table',1),(9,'2022_04_26_212720_create_events_table',1),(10,'2022_04_27_113215_add_oauth_fields_in_users',1),(11,'2022_04_28_192549_create_flow_measures_table',1),(12,'2022_05_02_154200_add_participants_column_to_events_table',1),(13,'2022_05_03_194237_create_discord_tags_table',1),(14,'2022_05_03_194357_create_discord_tag_flight_information_region_table',1),(15,'2022_05_03_200854_create_flight_information_region_flow_measure_table',1),(16,'2022_05_05_122316_create_discord_notifications_table',1),(17,'2022_05_23_202255_add_embeds_column_to_discord_notifications_table',1),(18,'2022_05_24_122756_make_embed_nullable_in_discord_notifications',1),(19,'2022_05_29_122427_create_activity_log_table',1),(20,'2022_05_29_122428_add_event_column_to_activity_log_table',1),(21,'2022_05_29_122429_add_batch_uuid_column_to_activity_log_table',1),(22,'2022_06_01_204104_add_index_to_airport_groups_table',1),(23,'2022_06_10_094740_create_discord_notification_types_table',1),(24,'2022_06_10_094832_create_discord_notification_flow_measure_table',1),(25,'2022_06_10_101521_drop_column_from_discord_notifications_table',1),(26,'2022_06_30_174947_create_division_discord_webhooks_table',1),(27,'2022_06_30_182456_create_division_discord_webhook_flight_information_region_table',1),(28,'2022_06_30_193055_add_division_discord_webhook_id_column_to_discord_notifications_table',1),(29,'2022_07_19_171630_add_event_manager_in_roles',1),(30,'2022_07_26_200013_create_event_participants_table',1),(31,'2022_07_28_184847_drop_event_participants_column',1),(32,'2022_08_03_193646_add_tag_column_to_division_discord_webhook_flight_information_region_table',1),(33,'2022_08_03_194127_migrate_division_discord_webhook_tags',1),(34,'2022_08_04_144847_drop_tag_column_from_division_discord_webhook_table',1),(35,'2022_08_18_162121_add_index_to_discord_notifications_table',1),(36,'2022_10_17_183844_drop_unique_index_on_flow_measures_table',1),(37,'2022_11_15_191602_add_coordinates_to_airport_table',1),(38,'2022_11_15_213619_create_vatsim_pilot_statuses_table',1),(39,'2022_11_16_160530_create_vatsim_pilots_table',1),(40,'2022_11_24_203502_drop_index_from_vatsim_pilots_table',1),(41,'2023_03_27_190127_create_failed_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A unique key for identifying the role for code purposes',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'SYSTEM','System user','2023-10-08 12:06:52','2023-10-08 12:06:52'),(2,'NMT','Network Management Team','2023-10-08 12:06:52','2023-10-08 12:06:52'),(3,'FLOW_MANAGER','Flow Manager','2023-10-08 12:06:53','2023-10-08 12:06:53'),(4,'USER','Normal User - View Only','2023-10-08 12:06:53','2023-10-08 12:06:53'),(5,'EVENT_MANAGER','Event Manager','2023-10-08 12:08:48','2023-10-08 12:08:48');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL COMMENT 'The user''s VATSIM CID',
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token_expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vatsim_pilot_statuses`
--

DROP TABLE IF EXISTS `vatsim_pilot_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vatsim_pilot_statuses` (
  `id` smallint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vatsim_pilot_statuses`
--

LOCK TABLES `vatsim_pilot_statuses` WRITE;
/*!40000 ALTER TABLE `vatsim_pilot_statuses` DISABLE KEYS */;
INSERT INTO `vatsim_pilot_statuses` VALUES (1,'Ground'),(2,'Departing'),(3,'Cruise'),(4,'Descending'),(5,'Landed');
/*!40000 ALTER TABLE `vatsim_pilot_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vatsim_pilots`
--

DROP TABLE IF EXISTS `vatsim_pilots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vatsim_pilots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `callsign` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The pilot callsign',
  `cid` bigint unsigned NOT NULL COMMENT 'The users CID',
  `departure_airport` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_airport` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altitude` mediumint NOT NULL,
  `cruise_altitude` mediumint unsigned DEFAULT NULL,
  `route_string` text COLLATE utf8mb4_unicode_ci,
  `vatsim_pilot_status_id` smallint unsigned NOT NULL COMMENT 'The calculated flight status',
  `estimated_arrival_time` timestamp NULL DEFAULT NULL COMMENT 'The calculated EAT',
  `distance_to_destination` double(8,2) DEFAULT NULL COMMENT 'The calculated distance to destination',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vatsim_pilots_callsign_unique` (`callsign`),
  KEY `vatsim_pilots_vatsim_pilot_status_id_foreign` (`vatsim_pilot_status_id`),
  KEY `vatsim_pilots_departure_airport_index` (`departure_airport`),
  KEY `vatsim_pilots_destination_airport_index` (`destination_airport`),
  KEY `vatsim_pilots_created_at_index` (`created_at`),
  KEY `vatsim_pilots_updated_at_index` (`updated_at`),
  CONSTRAINT `vatsim_pilots_vatsim_pilot_status_id_foreign` FOREIGN KEY (`vatsim_pilot_status_id`) REFERENCES `vatsim_pilot_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vatsim_pilots`
--

LOCK TABLES `vatsim_pilots` WRITE;
/*!40000 ALTER TABLE `vatsim_pilots` DISABLE KEYS */;
/*!40000 ALTER TABLE `vatsim_pilots` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-08 12:11:53
