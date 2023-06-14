--
-- Table structure for table `tr_theaters`
--

DROP TABLE IF EXISTS `tr_theaters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_theaters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_external` int(11) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(4) DEFAULT NULL,
  `zip_code` varchar(8) DEFAULT NULL,
  `directions` varchar(1024) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(256) DEFAULT NULL,
  `contacts_phone` varchar(16) DEFAULT NULL,
  `contacts_email` varchar(256) DEFAULT NULL,
  `contacts_fax` varchar(16) DEFAULT NULL,
  `additional_phone` varchar(16) DEFAULT NULL,
  `hash_summ` varchar(32) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location_lat` varchar(16) DEFAULT NULL,
  `location_lng` varchar(16) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_external` (`id_external`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_theaters`
--

LOCK TABLES `tr_theaters` WRITE;
/*!40000 ALTER TABLE `tr_theaters` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_theaters` ENABLE KEYS */;
UNLOCK TABLES;
