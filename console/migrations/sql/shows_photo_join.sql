--
-- Table structure for table `shows_photo_join`
--

DROP TABLE IF EXISTS `shows_photo_join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shows_photo_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `preview_id` int(11) DEFAULT NULL,
  `activity` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx-shows_photo_join-photo_id` (`photo_id`),
  KEY `idx-shows_photo_join-preview_id` (`preview_id`),
  KEY `idx-shows_photo_join-item_id` (`item_id`),
  CONSTRAINT `fk-shows_photo_join-item_id` FOREIGN KEY (`item_id`) REFERENCES `tr_shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-shows_photo_join-photo_id` FOREIGN KEY (`photo_id`) REFERENCES `content_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-shows_photo_join-preview_id` FOREIGN KEY (`preview_id`) REFERENCES `content_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shows_photo_join`
--

LOCK TABLES `shows_photo_join` WRITE;
/*!40000 ALTER TABLE `shows_photo_join` DISABLE KEYS */;
/*!40000 ALTER TABLE `shows_photo_join` ENABLE KEYS */;
UNLOCK TABLES;