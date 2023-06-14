--
-- Table structure for table `tr_shows`
--

DROP TABLE IF EXISTS `tr_shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_shows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_external` int(11) NOT NULL,
  `code` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `address` varchar(128) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(8) DEFAULT NULL,
  `zip_code` varchar(8) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `fax` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `directions` text,
  `status` int(1) NOT NULL DEFAULT '0',
  `show_in_footer` tinyint(4) NOT NULL DEFAULT '0',
  `location_external_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `marketing_level` int(2) DEFAULT NULL,
  `voucher_procedure` varchar(1024) DEFAULT NULL,
  `weekly_schedule` int(1) DEFAULT NULL,
  `on_special_text` varchar(1024) DEFAULT NULL,
  `cast_size` varchar(16) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `show_length` int(4) DEFAULT NULL,
  `intermissions` varchar(64) DEFAULT NULL,
  `cut_off` int(4) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `hash_summ` varchar(32) DEFAULT NULL,
  `photos` varchar(4096) DEFAULT NULL,
  `preview_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `seat_map_id` int(11) DEFAULT NULL,
  `display_image` tinyint(4) NOT NULL DEFAULT '0',
  `theatre_id` int(11) DEFAULT NULL,
  `theatre_name` varchar(128) DEFAULT NULL,
  `amenities` varchar(2048) DEFAULT NULL,
  `tags` varchar(256) DEFAULT NULL,
  `videos` varchar(2048) DEFAULT NULL,
  `min_rate` decimal(7,2) DEFAULT NULL,
  `min_rate_source` decimal(7,2) DEFAULT NULL,
  `cancel_policy_text` varchar(2048) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `location_lat` varchar(16) DEFAULT NULL,
  `location_lng` varchar(16) DEFAULT NULL,
  `call_us_to_book` int(1) DEFAULT '0',
  `external_service` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `id_external` (`id_external`),
  KEY `theatre_id` (`theatre_id`),
  KEY `seat_map_id` (`seat_map_id`),
  KEY `preview_id` (`preview_id`),
  KEY `idx-tr_shows-image_id` (`image_id`),
  CONSTRAINT `fk-tr_shows-image_id` FOREIGN KEY (`image_id`) REFERENCES `content_files` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tr_shows_ibfk_1` FOREIGN KEY (`theatre_id`) REFERENCES `tr_theaters` (`id_external`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tr_shows_ibfk_2` FOREIGN KEY (`seat_map_id`) REFERENCES `content_files` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tr_shows_ibfk_3` FOREIGN KEY (`preview_id`) REFERENCES `content_files` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_shows`
--

LOCK TABLES `tr_shows` WRITE;
/*!40000 ALTER TABLE `tr_shows` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_shows` ENABLE KEYS */;
UNLOCK TABLES;
