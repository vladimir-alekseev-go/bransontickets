--
-- Table structure for table `tr_categories`
--

DROP TABLE IF EXISTS `tr_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_external` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `hash_summ` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_external` (`id_external`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_categories`
--

LOCK TABLES `tr_categories` WRITE;
/*!40000 ALTER TABLE `tr_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_categories` ENABLE KEYS */;
UNLOCK TABLES;
