--
-- Table structure for table `tr_shows_categories`
--

DROP TABLE IF EXISTS `tr_shows_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tr_shows_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_external_show` int(11) NOT NULL,
  `id_external_category` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_external_show` (`id_external_show`),
  KEY `id_external_category` (`id_external_category`),
  CONSTRAINT `tr_shows_categories_ibfk_1` FOREIGN KEY (`id_external_show`) REFERENCES `tr_shows` (`id_external`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tr_shows_categories_ibfk_2` FOREIGN KEY (`id_external_category`) REFERENCES `tr_categories` (`id_external`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tr_shows_categories`
--

LOCK TABLES `tr_shows_categories` WRITE;
/*!40000 ALTER TABLE `tr_shows_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_shows_categories` ENABLE KEYS */;
UNLOCK TABLES;
