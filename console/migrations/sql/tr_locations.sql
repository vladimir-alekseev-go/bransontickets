--
-- Table structure for table `tr_locations`
--

DROP TABLE IF EXISTS `tr_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `hash_summ` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-tr_locations-external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_locations`
--

LOCK TABLES `tr_locations` WRITE;
/*!40000 ALTER TABLE `tr_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_locations` ENABLE KEYS */;
UNLOCK TABLES;