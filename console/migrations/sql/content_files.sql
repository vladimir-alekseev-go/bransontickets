--
-- Table structure for table `content_files`
--

DROP TABLE IF EXISTS `content_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(256) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `file_source_name` varchar(128) NOT NULL,
  `dir` varchar(32) NOT NULL,
  `source_url` varchar(256) DEFAULT NULL,
  `source_file_time` int(11) NOT NULL DEFAULT '0',
  `old` int(11) DEFAULT NULL,
  `path_old` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_files`
--

LOCK TABLES `content_files` WRITE;
/*!40000 ALTER TABLE `content_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_files` ENABLE KEYS */;
UNLOCK TABLES;
