-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: tagitall
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `name` char(16) CHARACTER SET latin1 NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id_cat`),
  KEY `fk_user` (`fk_user`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'/',1,10),(2,1,'Erster D1',2,3),(3,1,'Letzter D1',8,9),(4,1,'Nach Erster D1',4,7),(5,1,'Erstes Kind',5,6);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comp_metas`
--

DROP TABLE IF EXISTS `comp_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comp_metas` (
  `id_comp_meta_val` int(11) NOT NULL,
  `fk_meta` int(11) NOT NULL,
  `fk_value` int(11) NOT NULL,
  PRIMARY KEY (`id_comp_meta_val`),
  KEY `fk_meta` (`fk_meta`),
  KEY `fk_value` (`fk_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comp_metas`
--

LOCK TABLES `comp_metas` WRITE;
/*!40000 ALTER TABLE `comp_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `comp_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comp_vals`
--

DROP TABLE IF EXISTS `comp_vals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comp_vals` (
  `id_comp_val` int(11) NOT NULL,
  `value` char(255) NOT NULL,
  PRIMARY KEY (`id_comp_val`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comp_vals`
--

LOCK TABLES `comp_vals` WRITE;
/*!40000 ALTER TABLE `comp_vals` DISABLE KEYS */;
/*!40000 ALTER TABLE `comp_vals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `composites`
--

DROP TABLE IF EXISTS `composites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `composites` (
  `id_composites` int(11) NOT NULL AUTO_INCREMENT,
  `fk_meta` int(11) DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `name` char(60) NOT NULL,
  `source` char(200) NOT NULL,
  PRIMARY KEY (`id_composites`),
  KEY `fk_meta` (`fk_meta`),
  KEY `fk_user` (`fk_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `composites`
--

LOCK TABLES `composites` WRITE;
/*!40000 ALTER TABLE `composites` DISABLE KEYS */;
/*!40000 ALTER TABLE `composites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `id_content` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contents`
--

LOCK TABLES `contents` WRITE;
/*!40000 ALTER TABLE `contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id_group` int(11) NOT NULL,
  `groupname` char(60) DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meta_namevalues`
--

DROP TABLE IF EXISTS `meta_namevalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_namevalues` (
  `id_mnv` int(11) NOT NULL AUTO_INCREMENT,
  `fk_meta` int(11) NOT NULL,
  `valname` char(60) NOT NULL,
  `valdef` char(255) DEFAULT NULL,
  `req` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mnv`),
  KEY `fk_meta` (`fk_meta`)
) ENGINE=MyISAM AUTO_INCREMENT=9593 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meta_namevalues`
--

LOCK TABLES `meta_namevalues` WRITE;
/*!40000 ALTER TABLE `meta_namevalues` DISABLE KEYS */;
INSERT INTO `meta_namevalues` VALUES (13,6,'xcvyx','bbbbb',0),(3,2,'das','ist',0),(4,2,'eine','admin meta',0),(12,6,'xcvvx','bbbbb',0),(15,8,'test','teset',0),(9588,61,'sdfd',NULL,0),(9575,54,'bÃ¶bj','dsfdsds',0),(9587,60,'sdfds',NULL,0),(9586,59,'dfgdf',NULL,0),(9574,54,'Ã¶uhÃ¶bj','sdfsdds',0),(9567,52,'dfgd',NULL,0),(9553,43,'asda','asdasd',0),(9572,54,'khÃ¶lkh','Ã¶k',0),(9580,56,'00000','sdas',0),(9579,55,'jkgjkgl','asdas',0),(9557,46,'ss','yfdsfsd',0),(9558,46,'sss',NULL,0),(9573,54,'Ã¶kuhÃ¶','kuhÃ¶lk',0),(9571,54,'kh','uh',0),(9570,54,'sdfsdf',NULL,0),(9578,54,'khkuÃ¶h','kh',0),(9577,54,'ib','uhÃ¶kuh',0),(9576,54,'Ã¶kb','dsfsd',0),(9565,51,'dsfsd','asdsadsa',0),(9566,51,'sdfds',NULL,0),(9581,56,'0000000000000','Wert',0),(9582,56,'12345678910111213141516','dfsfsdfsd',0),(9592,64,'ertert',NULL,0);
/*!40000 ALTER TABLE `meta_namevalues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metas`
--

DROP TABLE IF EXISTS `metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metas` (
  `id_meta` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` varchar(45) NOT NULL,
  `name` varchar(60) NOT NULL,
  `in_used` tinyint(1) NOT NULL,
  `on_off` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_meta`),
  KEY `fk_user` (`fk_user`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metas`
--

LOCK TABLES `metas` WRITE;
/*!40000 ALTER TABLE `metas` DISABLE KEYS */;
INSERT INTO `metas` VALUES (2,'2','das_ist_eine_admin_meta',0,0),(6,'2','meta_informationen',0,0),(59,'1','fsdfsdf',0,0),(60,'1','sdfds',0,0),(52,'1','fgfdguidhfd',0,0),(43,'1','sdfdsfs',0,0),(54,'1','gdfgdfgf',0,0),(46,'1','ssss',0,0),(56,'1','namenstest',0,0),(55,'1','ilhlih',0,0),(51,'1','sdfsd',0,0),(61,'1','sdfs',0,0),(64,'1','meta_informationen',0,0);
/*!40000 ALTER TABLE `metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `id_right` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL,
  `fk_group` int(11) NOT NULL,
  `href` char(20) NOT NULL,
  `target` char(10) NOT NULL DEFAULT '_self',
  `fornav` char(15) DEFAULT NULL,
  PRIMARY KEY (`id_right`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES (1,'Profile',1,'user/profile','_self','mytagitall'),(2,'User Management',100,'user/management','_self','mytagitall'),(3,'Semantic Interface',1,'tagitall','_blank','mytagitall'),(4,'Logout',1,'user/logout','_self',NULL),(5,'Pressinformation',0,'content/press','_self','home'),(6,'Related Articles',0,'documentation/relate','_self','documentation');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `types` (
  `id_content_type` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(1) DEFAULT NULL,
  `basesrc` char(60) DEFAULT NULL,
  `iconpath` char(60) DEFAULT NULL,
  PRIMARY KEY (`id_content_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types`
--

LOCK TABLES `types` WRITE;
/*!40000 ALTER TABLE `types` DISABLE KEYS */;
/*!40000 ALTER TABLE `types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `fk_group` int(11) DEFAULT NULL,
  `username` char(20) NOT NULL,
  `password` char(32) NOT NULL,
  `firstname` char(20) NOT NULL,
  `lastname` char(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` char(30) NOT NULL,
  `phone` char(20) DEFAULT NULL,
  `ZIP` char(10) NOT NULL,
  `country` enum('Afghanistan','&Auml;gypten','Albanien','Algerien','Amerik. Samoa','Andorra','Angola','Anguilla','Antarktika','Antigua / Barbuda','&Auml;quatorialguinea','Argentinien','Armenien','Aruba','Aserbaidschan','&Auml;thiopien','Australien','Bahamas','Bahrain','Bangladesh','Barbados','Belgien','Belize','Benin','Bermuda','Bhutan','Bolivien','Bosnien und Herzegowina','Botsuana','Bouvetinsel','Brasilien','Britisches Territorium im Indischen Ozean','Brunei Darussalam','Bulgarien','Burkina Faso','Burundi','Cayman Islands','Chile','China','Cookinseln','Costa Rica','D&auml;nemark','Deutschland','Dominica','Dominikanische Republik','Dschibuti','Ecuador','El Salvador','Elfenbeink&uuml;ste','Eritrea','Estland','Falklandinseln','F&auml;r&ouml;er','Fidschi','Finnland','Frankreich','Franz&ouml;sisch Guayana','Franz&ouml;sische S&uuml;d- und Antarktisgebiete','Franz&ouml;sisch-Polynesien','Gabun','Gambia','Georgien','Ghana','Gibraltar','Grenada','Griechenland','Gr&ouml;nland','Gro&szlig;britannien','Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Heard und McDonaldinseln','Honduras','Hongkong','Indien','Indonesien','Irak','Iran','Irland','Island','Israel','Italien','Jamaika','Japan','Jemen','Jordanien','Jungferninseln Amerikanisch','Jungferninseln Britisch','Kambodscha','Kamerun','Kanada','Kapverdische Inseln','Kasachstan','Katar','Kenia','Kirgisistan','Kiribati','Kokosinseln Keelinginseln','Kolumbien','Komoren','Kongo Dem. Republik','Kongo Republik','Korea demokratische Volksrepublik','Korea Republik','Kosovo','Kroatien','Kuba','Kuwait','Laos','Lesotho','Lettland','Libanon','Liberia','Libyen / Libysch-Arabische Dschamahirja','Liechtenstein','Litauen','Luxemburg','Macau','Madagaskar','Malawi','Malaysia','Malediven','Mali','Malta','Marokko','Marshallinseln','Martinique','Mauretanien','Mauritius','Mayotte','Mexiko','Mikronesien','Monaco','Mongolei','Montenegro','Montserrat','Mosambik','Myanmar','Namibia','Nauru','Nepal','Neukaledonien','Neuseeland','Nicaragua','Niederl. Antillen','Niederlande','Niger','Nigeria','Niue','N&ouml;rdliche Marianen','Norfolkinsel','Norwegen','Oman','&Ouml;sterreich','Pakistan','Pal&auml;stinensische Gebiete','Palau','Panama','Papua-Neuguinea','Paraguay','Peru','Philippinen','Polen','Portugal','Puerto Rico','Republik Mazedonien','Republik Moldau','Republik Suriname','Reunion','Ruanda','Rum&auml;nien','Russische F&ouml;deration','Saint-Pierre und Miquelon','Sambia','Samoa','San Marino','Sao Tome und Principe','Saudi-Arabien','Schweden','Schweiz','Senegal','Serbien','Seychellen','Sierra Leone','Singapur','Slowakei','Slowenien','Solomonen','Somalia','South Georgia and South Sandwich','Spanien','Spitzbergen und Jan Mayen','Sri Lanka','St. Helena','St. Kitts und Nevis','St. Lucia','St. Vincent und die Grenadinen','S&uuml;dafrika','Sudan','Swasiland','Syrien','Tadschikistan','Taiwan','Tansania','Thailand','Timor-Leste','Togo','Tokelau','Tonga','Trinidad und Tobago','Tschad','Tschechische Republik','Tunesien','T&uuml;rkei','Turkmenistan','Turks- und Caicosinseln','Tuvalu','Uganda','Ukraine','Ungarn','United States Minor Outlying Islands','Uruguay','Uzbekistan','Vanuatu','Vatikan','Venezuela','Vereinigte Arabische Emirate','Vereinigte Staaten von Amerika','Vietnam','Wallis and Futuna','Weihnachtsinsel','Wei&szlig;russland','Westsahara','Zentralafrikanische Republik','Zimbabwe','Zypern') DEFAULT NULL,
  `city` char(20) DEFAULT NULL,
  `address` char(30) DEFAULT NULL,
  `your_branch_working_in` enum('Webdesign','Webdevelopment','Socialnetworks','Search Engine','Data Moddeling') NOT NULL,
  `your_interests` set('Coding','Sematics','Ai','Socialdevelopment','Finance','Analytics') NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `confirmed` char(32) DEFAULT NULL,
  `catbuild` timestamp NULL DEFAULT NULL,
  `compbuild` timestamp NULL DEFAULT NULL,
  `metabuild` timestamp NULL DEFAULT NULL,
  `groupbuild` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `your_prefered_username_UNIQUE` (`username`),
  KEY `fk_group` (`fk_group`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,100,'root','2757142c2111b8bd34a0d1b718f59c0c','Rootuser','Rootuser','0000-00-00','rootuser@system.com','','44444','Afghanistan','','','Webdesign','Coding','2011-01-01 21:25:39','confirmed','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00',NULL),(2,1,'admin','89088dc0047cf877395138c3d9041ca0','Admin','Admin','0000-00-00','admin@tagitall.com','','55555','Deutschland','','','Webdesign','Coding,Sematics,Ai,Socialdevelopment,Finance,Analytics','2010-10-24 00:12:00','confirmed',NULL,NULL,NULL,NULL),(3,1,'harry','594f803b380a41396ed63dca39503542','aaa','aa','0000-00-00','dfsdfds@dsfsdde','','49534','Afghanistan','dsfds','','Webdesign','Coding,Sematics','2010-10-25 20:06:32','confirmed',NULL,NULL,NULL,NULL),(4,1,'bernhard','eaa72d8db7e4c24d35052865786f7b98','bernhard','bezdek','0000-00-00','bernhardbezdek@gmailcom','','63075','Afghanistan','Offenbach','MainstraÃŸe 121','Webdesign','Coding,Sematics,Ai','2010-11-22 19:54:57','confirmed',NULL,NULL,NULL,NULL),(5,1,'bernhardb','1696e781befb290e9d8984a923c1feed','bernhard','bezdek','0000-00-00','bernhardbezdek@gmailcom','','56412','Afghanistan','','','Webdesign','Coding','2010-12-18 17:30:54','confirmed',NULL,NULL,NULL,NULL),(6,1,'dok','ed4c137a17b08b2f62b044ffbc078c7e','dok','dok','0000-00-00','dok@domailde','dok','dok','Afghanistan','','','Webdesign','Coding','2010-12-18 17:35:44','confirmed',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-01-29 13:37:05
