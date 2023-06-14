--
-- Table structure for table `tr_prices`
--

DROP TABLE IF EXISTS `tr_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_external` int(11) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `hash_summ` varchar(32) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(128) DEFAULT NULL,
  `retail_rate` decimal(8,2) NOT NULL,
  `special_rate` decimal(8,2) DEFAULT NULL,
  `tripium_rate` decimal(8,2) DEFAULT NULL,
  `available` int(11) NOT NULL,
  `sold` int(11) NOT NULL,
  `stop_sell` tinyint(4) NOT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `free_sell` tinyint(4) NOT NULL DEFAULT '0',
  `allotment_external_id` int(11) NOT NULL,
  `price_external_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_external` (`id_external`),
  KEY `start` (`start`),
  KEY `idx-tr_prices-price_external_id` (`price_external_id`),
  KEY `idx-tr_prices-name` (`name`),
  CONSTRAINT `tr_prices_ibfk_1` FOREIGN KEY (`id_external`) REFERENCES `tr_shows` (`id_external`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_prices`
--

LOCK TABLES `tr_prices` WRITE;
/*!40000 ALTER TABLE `tr_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_prices` ENABLE KEYS */;
UNLOCK TABLES;
