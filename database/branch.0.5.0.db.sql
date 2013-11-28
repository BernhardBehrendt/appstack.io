-- MySQL dump 10.13  Distrib 5.1.57, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tagitall
-- ------------------------------------------------------
-- Server version	5.1.57-1~dotdeb.1

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
  `id_cat` int(32) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `name` char(100) CHARACTER SET latin1 NOT NULL,
  `alias` char(100) NOT NULL,
  `public` tinyint(4) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastmodified` timestamp NULL DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id_cat`),
  KEY `fk_user` (`fk_user`)
) ENGINE=MyISAM AUTO_INCREMENT=269 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (136,1,'/','/',0,'2011-04-05 18:23:28','2011-04-05 18:23:41',1,44),(137,1,'erste_cat','erste_cat',1,'2011-04-05 18:23:41','2011-05-08 00:39:39',2,43),(138,1,'erste_subcat','erste_subcat',0,'2011-04-05 18:25:56','2011-04-08 10:04:18',3,38),(139,23,'/','/',0,'2011-04-06 21:26:40','2011-04-06 21:27:52',1,6),(140,23,'new_category','new_category',0,'2011-04-06 21:27:52','2011-04-06 21:31:25',2,5),(141,23,'welcome','welcome',0,'2011-04-06 21:28:54',NULL,3,4),(142,24,'/','/',0,'2011-04-06 21:36:12','2011-05-07 15:26:44',1,72),(143,1,'trztr','trztr',0,'2011-04-08 10:04:18','2011-04-08 10:08:53',4,37),(144,1,'hgfhgfhf','hgfhgfhf',0,'2011-04-08 10:08:10',NULL,39,40),(145,1,'hgfhg','hgfhg',0,'2011-04-08 10:08:17',NULL,41,42),(146,1,'dfgffd','dfgffd',0,'2011-04-08 10:08:53','2011-05-07 11:43:46',5,36),(147,1,'dfgdfg','dfgdfg',0,'2011-04-08 10:09:01','2011-04-08 10:09:12',8,15),(148,1,'dfgdfgfd','dfgdfgfd',0,'2011-04-08 10:09:12','2011-04-08 10:09:24',9,14),(149,1,'sdfsdfs','sdfsdfs',0,'2011-04-08 10:09:24','2011-04-08 10:09:43',10,13),(150,1,'erterter','erterter',0,'2011-04-08 10:09:43',NULL,11,12),(151,1,'lgluzfp','lgluzfp',0,'2011-04-08 17:11:06',NULL,16,17),(255,31,'navi_top','navi_top',1,'2011-06-01 07:10:39','2011-06-09 18:02:49',3,10),(245,24,'links','links',1,'2011-05-09 09:12:15','2011-05-09 09:13:08',48,51),(242,24,'presse','presse',1,'2011-05-09 09:04:05','2011-05-09 09:04:34',31,32),(155,1,'khlgliu','khlgliu',0,'2011-04-08 17:11:33',NULL,18,19),(156,1,'glizgf','glizgf',0,'2011-04-08 17:11:38',NULL,20,21),(241,25,'statistik','statistik',1,'2011-05-08 22:01:56','2011-05-19 20:43:00',2,3),(158,1,'sdfds','sdfds',0,'2011-04-08 17:12:31',NULL,22,23),(159,1,'sdfds','sdfds',0,'2011-04-08 17:12:37',NULL,24,25),(160,1,'sdfds','sdfds',0,'2011-04-08 17:12:43',NULL,26,27),(161,1,'dsfds','dsfds',0,'2011-04-08 17:12:48',NULL,28,29),(162,1,'sdfds','sdfds',0,'2011-04-08 17:12:54',NULL,30,31),(163,1,'sdfsds','sdfsds',0,'2011-04-08 17:13:00','2011-04-24 11:09:44',6,7),(247,24,'é“¾æŽ¥','é“¾æŽ¥',1,'2011-05-09 09:17:09','2011-05-09 09:17:16',64,65),(250,30,'sfdsfdsfdsdfgds','sfdsfdsfdsdfgds',1,'2011-05-22 11:52:04','2011-06-17 08:01:54',2,3),(166,1,'sdfdsfs','sdfdsfs',0,'2011-04-08 17:13:19',NULL,32,33),(167,1,'sdfds','sdfds',0,'2011-04-08 17:13:35',NULL,34,35),(240,25,'/','/',0,'2011-05-08 22:01:15','2011-05-22 11:15:19',1,6),(254,31,'navigations','navigations',0,'2011-06-01 07:10:22','2011-06-01 07:11:03',2,25),(253,31,'/','/',0,'2011-06-01 07:09:59','2011-06-09 18:20:02',1,30),(258,31,'tabboxes','tabboxes',1,'2011-06-05 22:54:01','2011-06-05 22:55:09',26,27),(249,30,'/','/',0,'2011-05-22 11:51:48','2011-06-13 09:56:58',1,6),(246,24,'press','press',1,'2011-05-09 09:12:27','2011-05-09 09:14:25',49,50),(248,25,'statistik','statistik',1,'2011-05-22 11:15:19','2011-05-22 11:15:28',4,5),(225,24,'images','images',1,'2011-05-05 21:09:33','2011-05-05 21:13:45',68,69),(222,24,'projects','projects',1,'2011-05-01 23:29:54','2011-05-01 23:31:22',44,45),(257,31,'navi_bottom','navi_bottom',0,'2011-06-01 07:11:03',NULL,23,24),(256,31,'navi_main','navi_main',1,'2011-06-01 07:10:50','2011-06-05 23:48:10',11,22),(228,28,'/','/',0,'2011-05-07 08:59:23',NULL,1,2),(226,24,'media','media',1,'2011-05-05 22:53:02','2011-05-05 22:53:11',46,47),(223,24,'æ¼”å‡ºä¸Žå½•éŸ³è®¡åˆ’','æ¼”å‡ºä¸Žå½•éŸ³è®¡åˆ’',1,'2011-05-02 00:34:31','2011-05-02 00:36:14',60,61),(232,24,'audio','audio',1,'2011-05-08 16:08:37','2011-05-09 21:57:30',27,28),(194,24,'navigation','navigation',1,'2011-05-01 19:20:34','2011-05-01 21:50:17',2,67),(229,24,'audios','audios',1,'2011-05-07 15:26:44','2011-05-08 12:46:06',70,71),(227,24,'åª’ä½“','åª’ä½“',1,'2011-05-05 22:55:24','2011-05-06 12:36:55',62,63),(195,24,'deutsch','deutsch',1,'2011-05-01 19:27:06','2011-05-01 21:55:54',3,36),(196,24,'english','english',1,'2011-05-01 19:27:34','2011-05-09 09:12:15',37,52),(197,24,'chinese','chinese',1,'2011-05-01 19:27:47','2011-05-09 09:17:09',53,66),(210,24,'pÃ¤dagogische_tÃ¤tigkeit','pÃ¤dagogische_tÃ¤tigkeit',1,'2011-05-01 21:54:44','2011-05-01 22:02:39',8,19),(209,24,'diskografie','diskografie',1,'2011-05-01 21:54:12','2011-05-01 21:59:54',6,7),(208,24,'lebenslauf','lebenslauf',1,'2011-05-01 21:53:48','2011-05-31 20:45:02',4,5),(201,24,'vita','vita',1,'2011-05-01 19:28:55','2011-05-01 23:13:21',38,39),(202,24,'discography','discography',1,'2011-05-01 19:29:10','2011-05-08 16:03:03',40,41),(203,24,'pedagogical_work','pedagogical_work',1,'2011-05-01 19:29:23','2011-05-01 23:26:37',42,43),(204,24,'ç®€åŽ†','ç®€åŽ†',1,'2011-05-01 19:29:39','2011-05-01 23:46:57',54,55),(205,24,'ä½œå“é›†','ä½œå“é›†',1,'2011-05-01 19:30:03','2011-05-02 00:25:45',56,57),(206,24,'æ•™å­¦é¡¹ç›®è®¡åˆ’','æ•™å­¦é¡¹ç›®è®¡åˆ’',1,'2011-05-01 19:30:17','2011-05-02 00:36:03',58,59),(211,24,'projekte','projekte',1,'2011-05-01 21:55:00','2011-05-01 22:07:53',20,25),(212,24,'medien','medien',1,'2011-05-01 21:55:14','2011-05-08 16:09:10',26,29),(213,24,'links','links',1,'2011-05-01 21:55:36','2011-05-09 09:04:05',30,33),(214,24,'kontakt','kontakt',0,'2011-05-01 21:55:54','2011-05-01 23:32:25',34,35),(215,24,'china','china',0,'2011-05-01 22:01:35','2011-05-07 16:55:43',9,10),(216,24,'kammermusik','kammermusik',0,'2011-05-01 22:01:48','2011-05-02 00:04:24',13,14),(217,24,'aktuelle_gastprofessur','aktuelle_gastprofessur',0,'2011-05-01 22:02:21','2011-05-02 00:04:31',15,16),(218,24,'spezielle_projekte','spezielle_projekte',0,'2011-05-01 22:02:39','2011-05-02 00:04:38',17,18),(219,24,'audio','audio',0,'2011-05-01 22:03:00','2011-05-07 16:55:36',11,12),(220,24,'kammermusik','kammermusik',0,'2011-05-01 22:03:29','2011-05-02 00:05:24',21,22),(221,24,'audio','audio',0,'2011-05-01 22:03:40','2011-05-02 00:05:30',23,24),(259,31,'home','home',1,'2011-06-05 23:38:25','2011-06-05 23:47:36',12,13),(260,31,'company','company',0,'2011-06-05 23:38:37','2011-06-05 23:55:42',14,15),(261,31,'projects','projects',1,'2011-06-05 23:38:53','2011-06-05 23:47:48',16,17),(262,31,'community','community',1,'2011-06-05 23:39:19','2011-06-05 23:47:53',18,19),(263,31,'services','services',1,'2011-06-05 23:39:33','2011-06-05 23:48:00',20,21),(264,31,'customer_login','customer_login',1,'2011-06-09 18:01:05','2011-06-09 18:01:33',6,7),(265,31,'contact','contact',1,'2011-06-09 18:01:27','2011-06-09 18:01:37',8,9),(266,31,'sitemap','sitemap',1,'2011-06-09 18:02:49','2011-06-09 18:02:54',4,5),(267,31,'news','news',1,'2011-06-09 18:20:02','2011-06-09 18:20:57',28,29),(268,30,'icons','icons',0,'2011-06-13 09:56:58','2011-06-17 08:04:53',4,5);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comp_meta_values`
--

DROP TABLE IF EXISTS `comp_meta_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comp_meta_values` (
  `id_cmnv` int(11) NOT NULL AUTO_INCREMENT,
  `fk_comp_meta` int(11) NOT NULL,
  `valname` char(60) NOT NULL,
  `valdef` char(50) DEFAULT NULL,
  PRIMARY KEY (`id_cmnv`),
  KEY `fk_comp_meta_values_comp_metas1` (`fk_comp_meta`)
) ENGINE=MyISAM AUTO_INCREMENT=116169 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comp_meta_values`
--

LOCK TABLES `comp_meta_values` WRITE;
/*!40000 ALTER TABLE `comp_meta_values` DISABLE KEYS */;
INSERT INTO `comp_meta_values` VALUES (52760,358,'charset','utf-8'),(52759,358,'content-type','html'),(52758,357,'index','no'),(52757,356,'countrys','china'),(52756,356,'prefix','cn'),(52755,355,'address','content/'),(52754,354,'index','no'),(52753,353,'charset','utf-8'),(52752,353,'content-type','html'),(52751,352,'countrys','china'),(52750,352,'prefix','cn'),(52749,351,'address','content/'),(52748,350,'index','no'),(52747,349,'charset','utf-8'),(52746,349,'content-type','html'),(52745,348,'countrys','china'),(52744,348,'prefix','cn'),(52743,347,'address','content/'),(52742,346,'charset','utf-8'),(52741,346,'content-type','html'),(52740,345,'countrys','china'),(52739,345,'prefix','cn'),(52738,344,'index','no'),(52737,343,'address','content/'),(52736,342,'index','no'),(52735,341,'charset','utf-8'),(52734,341,'content-type','html'),(52733,340,'countrys','england'),(52732,340,'prefix','en'),(52731,339,'address','content/'),(52730,338,'index','no'),(52729,337,'charset','utf-8'),(52728,337,'content-type','html'),(52727,336,'countrys','england'),(52726,336,'prefix','en'),(52725,335,'address','content/'),(52724,334,'index','no'),(52723,333,'charset','utf-8'),(52722,333,'content-type','html'),(52721,332,'countrys','england'),(52720,332,'prefix','en'),(52719,331,'address','content/'),(52716,329,'charset','utf-8'),(52715,329,'content-type','html'),(52714,328,'index','no'),(52718,330,'countrys','england'),(106896,540,'charset','utf-8'),(52683,308,'prefix','de'),(52681,306,'address','content/'),(52684,308,'countrys','germany;swiss;austria'),(52690,312,'charset','utf-8'),(52689,312,'content-type','html'),(52688,311,'index','no'),(52687,310,'address','content/'),(52686,309,'charset','utf-8'),(52685,309,'content-type','html'),(52704,321,'index','no'),(52700,319,'prefix','de'),(52703,320,'charset','utf-8'),(52702,320,'content-type','html'),(52717,330,'prefix','en'),(52694,315,'prefix','de'),(106895,539,'prefix','de'),(52701,319,'countrys','germany;swiss;austria'),(52693,314,'address','content/'),(52692,313,'countrys','germany;swiss;austria'),(106894,539,'countrys','germany;swiss;austria'),(52699,318,'address','content/'),(52698,317,'index','no'),(52697,316,'charset','utf-8'),(115011,625,'charset','utf-8'),(115010,624,'prefix','de'),(52696,316,'content-type','html'),(52695,315,'countrys','germany;swiss;austria'),(52691,313,'prefix','de'),(106893,538,'address','content/'),(106898,541,'index','no'),(115009,624,'countrys','germany;swiss;austria'),(52682,307,'index','no'),(115008,623,'address','content/'),(115007,622,'depot','DWS'),(106897,540,'content-type','html'),(115006,622,'vermÃ¶gen','20000'),(52711,326,'address','content/'),(116168,878,'word_1','unhappy'),(116167,878,'word_2',NULL),(116166,878,'word_3',NULL),(116165,878,'word_4',NULL),(116164,878,'word_5',NULL),(116151,877,'hashtag_10',NULL),(116150,877,'hashtag_2',NULL),(116149,877,'hashtag_3',NULL),(116148,876,'prefix','de'),(116147,875,'border','ff0000'),(116146,875,'background','000000'),(116145,875,'text','ffffff'),(116144,874,'index','no'),(116143,873,'fsgsdf','fdgfd'),(116142,873,'prefix','dd'),(116141,872,'compid',NULL),(116140,871,'ein_metaname','mit einem defaultwert'),(116163,878,'word_6',NULL),(116162,878,'word_7',NULL),(116161,878,'word_8',NULL),(116160,878,'word_9',NULL),(116159,878,'word_10',NULL),(116158,877,'hashtag_4','gute nacht'),(116157,877,'hashtag_5',NULL),(116156,877,'hashtag_1',NULL),(116155,877,'hashtag_6',NULL),(116154,877,'hashtag_7',NULL),(116153,877,'hashtag_8',NULL),(116152,877,'hashtag_9',NULL),(116139,871,'ein_anderer_metaname','auch mit defaultwert'),(116138,870,'prefix',NULL),(116137,869,'hashtag_1',NULL),(116136,869,'hashtag_2',NULL),(116135,869,'hashtag_3',NULL),(116134,869,'hashtag_4',NULL),(116133,869,'hashtag_5',NULL),(116132,869,'hashtag_6',NULL),(116131,869,'hashtag_7',NULL),(116130,869,'hashtag_8',NULL),(116129,869,'hashtag_9',NULL),(116128,869,'hashtag_10',NULL),(116127,868,'word_10',NULL),(116126,868,'word_9',NULL),(116125,868,'word_8',NULL),(116124,868,'word_7',NULL),(116123,868,'word_6',NULL),(116122,868,'word_5',NULL),(116121,868,'word_4',NULL),(116120,868,'word_3',NULL),(116119,868,'word_2',NULL),(116118,868,'word_1','unhappy'),(116117,867,'hashtag_3',NULL),(116116,867,'hashtag_2',NULL),(116115,867,'hashtag_10',NULL),(116114,867,'hashtag_9',NULL),(116113,867,'hashtag_8',NULL),(116112,867,'hashtag_7',NULL),(116111,867,'hashtag_6',NULL),(116110,867,'hashtag_1',NULL),(116109,867,'hashtag_5',NULL),(116108,867,'hashtag_4','gute nacht'),(116107,866,'prefix','de'),(116106,865,'text','ffffff'),(116105,865,'background','000000'),(116104,865,'border','ff0000'),(116099,864,'word_6',NULL),(116098,864,'word_5',NULL),(116097,864,'word_4',NULL),(116096,864,'word_3',NULL),(116095,864,'word_2',NULL),(116094,864,'word_1','unhappy'),(116093,863,'hashtag_3','glÃ¼ck teilen'),(116092,863,'hashtag_2',NULL),(116091,863,'hashtag_10',NULL),(116090,863,'hashtag_9',NULL),(116089,863,'hashtag_8',NULL),(116088,863,'hashtag_7',NULL),(116087,863,'hashtag_6',NULL),(116086,863,'hashtag_1',NULL),(116085,863,'hashtag_5',NULL),(116084,863,'hashtag_4',NULL),(116083,862,'prefix','de'),(116082,861,'text','ffffff'),(116081,861,'background','000000'),(116080,861,'border','ff0000'),(116103,864,'word_10',NULL),(115898,831,'hashtag_4','glÃ¼cklich'),(115897,831,'hashtag_5',NULL),(116102,864,'word_9',NULL),(115925,835,'word_1','unhappy'),(115924,835,'word_2',NULL),(115923,835,'word_3',NULL),(115922,835,'word_4',NULL),(115921,835,'word_5',NULL),(115920,835,'word_6',NULL),(115919,835,'word_7',NULL),(115918,835,'word_8',NULL),(115917,835,'word_9',NULL),(115916,835,'word_10',NULL),(116101,864,'word_8',NULL),(116100,864,'word_7',NULL),(115901,831,'hashtag_1',NULL),(115896,831,'hashtag_6',NULL),(115895,831,'hashtag_7',NULL),(115894,831,'hashtag_8',NULL),(115893,831,'hashtag_9',NULL),(115892,831,'hashtag_10',NULL),(115891,830,'prefix','de'),(115890,829,'border','ff0000'),(115889,829,'background','000000'),(115888,829,'text','ffffff'),(115900,831,'hashtag_2',NULL),(115899,831,'hashtag_3',NULL),(115093,662,'970z097z','ÃŸ98zu'),(115092,662,'97z0p978z',NULL),(115091,662,'078z087','7'),(115090,662,'870z0897z087',NULL),(115089,662,'o8z87','87z'),(115088,661,'vermÃ¶gen','20000'),(115087,661,'depot','DWS'),(115074,655,'sdfds','sdfds'),(115084,659,'pu9p9u','piuh'),(115083,659,'iuohiouh',NULL),(115082,659,'iuhhiouh',NULL),(115081,659,'ipuohipouh','piuh'),(115080,659,'iuhp','ipuhpiu'),(115079,659,'piuhpiuh','upihpiuh'),(115067,653,'o8z87','87z'),(115066,653,'870z0897z087',NULL),(115065,653,'078z087','7'),(115064,653,'97z0p978z',NULL),(115063,653,'970z097z','ÃŸ98zu'),(115078,658,'sadsa',NULL),(115077,657,'asdasdsa',NULL),(115076,656,'sfsda',NULL),(115075,656,'sdfsdsadd',NULL),(115053,650,'depot','DWS'),(115052,650,'vermÃ¶gen','20000'),(115051,649,'970z097z','ÃŸ98zu'),(115050,649,'97z0p978z',NULL),(115049,649,'078z087','7'),(115048,649,'870z0897z087',NULL),(115047,649,'o8z87','87z'),(115046,648,'vermÃ¶gen','20000'),(115045,648,'depot','DWS'),(115044,647,'o8z87','87z'),(115043,647,'870z0897z087',NULL),(115042,647,'078z087','7'),(115041,647,'97z0p978z',NULL),(115040,647,'970z097z','ÃŸ98zu'),(115039,646,'isaudio','yes'),(115038,645,'isaudio','yes'),(115037,644,'isaudio','yes'),(115036,643,'isaudio','yes'),(115035,642,'isaudio','yes'),(115034,641,'isaudio','yes'),(115031,638,'index','no'),(115030,637,'content-type','html'),(115029,637,'charset','utf-8'),(115028,636,'prefix','cn'),(115027,636,'countrys','chinese'),(115026,635,'address','content/'),(115025,634,'index','no'),(115024,633,'content-type','html'),(115023,633,'charset','utf-8'),(115022,632,'countrys','england'),(115021,632,'prefix','en'),(115020,631,'address','content/'),(115019,630,'index','no'),(115018,629,'content-type','html'),(115017,629,'charset','utf-8'),(115016,628,'prefix','de'),(115015,628,'countrys','germany;swiss;austria'),(115014,627,'address','content/'),(115013,626,'index','no'),(115012,625,'content-type','html'),(115005,621,'index','no'),(115004,620,'content-type','html'),(115003,620,'charset','utf-8'),(115002,619,'countrys','germany;swiss;austria'),(115001,619,'prefix','de'),(115000,618,'address','content/'),(114999,617,'index','no'),(114998,616,'charset','utf-8'),(114997,616,'content-type','html'),(114996,615,'countrys','germany;swiss;austria'),(114995,615,'prefix','de'),(114994,614,'address','content/'),(114993,613,'compid','132'),(114992,612,'compid','131'),(114991,611,'compid','130'),(114990,610,'compid','129'),(114989,609,'compid','128'),(114988,608,'compid','134'),(114987,607,'countrys','all'),(114986,607,'prefix','de;en;cn'),(114985,606,'address','content/'),(114984,605,'charset','none'),(114983,605,'content-type','jpg'),(114982,604,'prefix','de;en;cn'),(114981,604,'countrys','all'),(114980,603,'address','content/'),(114979,602,'content-type','jpg'),(114978,602,'charset','none'),(114977,601,'countrys','all'),(114976,601,'prefix','de;en;cn'),(114975,600,'address','content/'),(114974,599,'charset','none'),(114973,599,'content-type','mp3'),(114972,598,'prefix','de;en;cn'),(114971,598,'countrys','all'),(114970,597,'address','content/'),(114969,596,'content-type','mp3'),(114968,596,'charset','none'),(114967,595,'countrys','all'),(114966,595,'prefix','de;en;cn'),(114965,594,'address','content/'),(114964,593,'charset','none'),(114963,593,'content-type','mp3'),(114957,589,'prefix','de;en;cn'),(114956,589,'countrys','all'),(114955,588,'address','content/'),(114954,587,'content-type','mp3'),(114953,587,'charset','none'),(114962,592,'countrys','all'),(114961,592,'prefix','de;en;cn'),(114960,591,'address','content/'),(114959,590,'charset','none'),(114958,590,'content-type','mp3'),(114942,580,'prefix','de;en;cn'),(114941,580,'countrys','all'),(114940,579,'address','content/'),(114939,578,'content-type','png'),(114938,578,'charset','none'),(114937,577,'charset','utf-8'),(114936,577,'content-type','json'),(114935,576,'content-type','json'),(114934,576,'charset','utf-8'),(114933,575,'charset','utf-8'),(114932,575,'content-type','json'),(114931,574,'prefix','de;en;cn'),(114930,574,'countrys','all'),(114929,573,'address','content/'),(114928,572,'content-type','jpg'),(114927,572,'charset','none'),(114926,571,'countrys','all'),(114925,571,'prefix','de;en;cn'),(114924,570,'address','content/'),(114923,569,'charset','none'),(114922,569,'content-type','jpg'),(114921,568,'prefix','de;en;cn'),(114920,568,'countrys','all'),(114919,567,'address','content/'),(114918,566,'content-type','jpg'),(114917,566,'charset','none'),(114916,565,'countrys','all'),(114915,565,'prefix','de;en;cn'),(114914,564,'address','content/'),(114913,563,'charset','none'),(114912,563,'content-type','jpg');
/*!40000 ALTER TABLE `comp_meta_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comp_metas`
--

DROP TABLE IF EXISTS `comp_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comp_metas` (
  `id_comp_meta` int(11) NOT NULL AUTO_INCREMENT,
  `fk_meta` int(11) NOT NULL,
  `fk_composite` int(11) NOT NULL,
  PRIMARY KEY (`id_comp_meta`),
  KEY `fk_meta` (`fk_meta`),
  KEY `fk_value` (`fk_composite`),
  KEY `fk_comp_metas_composites1` (`fk_composite`),
  KEY `fk_comp_metas_comp_meta_namevalues1` (`id_comp_meta`)
) ENGINE=MyISAM AUTO_INCREMENT=879 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comp_metas`
--

LOCK TABLES `comp_metas` WRITE;
/*!40000 ALTER TABLE `comp_metas` DISABLE KEYS */;
INSERT INTO `comp_metas` VALUES (653,172,144),(656,174,144),(568,161,118),(646,170,134),(638,160,141),(645,170,128),(644,170,129),(637,162,141),(623,159,138),(636,161,141),(622,169,137),(308,161,68),(307,160,68),(541,160,113),(643,170,130),(306,159,68),(642,170,131),(641,170,132),(309,162,68),(635,159,141),(310,159,69),(311,160,69),(312,162,69),(313,161,69),(314,159,70),(315,161,70),(316,162,70),(317,160,70),(318,159,71),(319,161,71),(320,162,71),(321,160,71),(567,159,118),(566,162,118),(565,161,117),(564,159,117),(326,159,73),(330,161,73),(328,160,73),(329,162,73),(331,159,74),(332,161,74),(333,162,74),(334,160,74),(335,159,75),(336,161,75),(337,162,75),(338,160,75),(339,159,76),(340,161,76),(341,162,76),(342,160,76),(343,159,77),(344,160,77),(345,161,77),(346,162,77),(347,159,78),(348,161,78),(349,162,78),(350,160,78),(351,159,79),(352,161,79),(353,162,79),(354,160,79),(355,159,80),(356,161,80),(357,160,80),(358,162,80),(634,160,140),(633,162,140),(650,169,144),(649,172,143),(648,169,143),(647,172,137),(662,172,145),(661,169,145),(659,171,50),(658,176,144),(655,166,50),(657,175,144),(632,161,140),(631,159,140),(630,160,139),(629,162,139),(628,161,139),(627,159,139),(626,160,138),(625,162,138),(624,161,138),(621,160,136),(620,162,136),(619,161,136),(618,159,136),(617,160,135),(616,162,135),(615,161,135),(614,159,135),(613,168,120),(606,159,134),(605,162,134),(604,161,133),(603,159,133),(602,162,133),(601,161,132),(612,168,119),(611,168,133),(610,168,118),(609,168,117),(608,168,124),(607,161,134),(600,159,132),(599,162,132),(598,161,131),(597,159,131),(596,162,131),(595,161,130),(594,159,130),(593,162,130),(589,161,128),(588,159,128),(587,162,128),(592,161,129),(591,159,129),(590,162,129),(580,161,124),(579,159,124),(578,162,124),(577,162,123),(576,162,122),(575,162,121),(574,161,120),(573,159,120),(572,162,120),(571,161,119),(570,159,119),(569,162,119),(563,162,117),(538,159,113),(540,162,113),(539,161,113),(864,182,210),(863,177,210),(835,182,199),(831,177,199),(830,178,199),(829,179,199),(868,182,216),(867,177,216),(866,178,216),(865,179,216),(862,178,210),(861,179,210),(876,178,219),(875,179,219),(872,168,129),(871,184,129),(870,178,218),(869,177,218),(878,182,219),(874,160,129),(873,167,129),(877,177,219);
/*!40000 ALTER TABLE `comp_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `composites`
--

DROP TABLE IF EXISTS `composites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `composites` (
  `id_composite` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(60) NOT NULL,
  `alias` char(60) NOT NULL,
  `pub` tinyint(1) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  `source` text NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_category` int(11) NOT NULL,
  PRIMARY KEY (`id_composite`),
  KEY `fk_user` (`fk_user`)
) ENGINE=MyISAM AUTO_INCREMENT=220 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `composites`
--

LOCK TABLES `composites` WRITE;
/*!40000 ALTER TABLE `composites` DISABLE KEYS */;
INSERT INTO `composites` VALUES (117,'hallodk\n\n','01_bach_violin_partita_3_in_e_bwv_1006-preludio',1,'2011-05-05 21:10:20','2011-05-07 15:47:02','images/01_Bach_Violin_Partita_3_In_E,_BWV_1006-Preludio.jpg',24,225),(68,'hallodk\n\n','cv_de',1,'2011-05-01 20:15:29','2011-05-31 20:46:03','cv_de.html',24,208),(50,'hallodk\n\n','testcomp',1,'2011-04-24 11:11:46','2011-05-20 17:08:32','//images.ui-portal.de/images/232/12640232,pd=1,h=50,mxh=600,mxw=800,w=60.jpg',1,137),(137,'hallodk\n\n','einnahmen',1,'2011-05-08 22:03:17','2011-05-22 11:15:37','http://boerseonline.de/static/bilder/charts/Chartanalysefallend_tk.gif',25,248),(118,'hallodk\n\n','02_penderecki_violinkonzert',1,'2011-05-05 21:12:27','2011-05-07 15:47:52','images/02_Penderecki_Violinkonzert.jpg',24,225),(71,'hallodk\n\n','paed',1,'2011-05-01 22:55:32','2011-05-01 22:55:54','paed_de.html',24,210),(48,'hallodk\n\n','s',0,'2011-04-22 14:34:39',NULL,'k',24,142),(69,'hallodk\n\n','demo',1,'2011-05-01 22:18:40','2011-05-01 22:30:59','demo.html',24,215),(70,'hallodk\n\n','diskografie',1,'2011-05-01 22:40:18','2011-05-01 22:43:03','dis_de.html',24,209),(136,'hallodk\n\n','paed (copy)',1,'2011-05-08 16:10:58','2011-05-08 16:12:44','aud_inf_de.html',24,212),(135,'hallodk\n\n','links',1,'2011-05-08 16:00:22','2011-05-08 16:00:30','links.html',24,213),(134,'hallodk\n\n','04_franckpiano_trio_op11 (copy)',1,'2011-05-07 17:19:17','2011-05-08 15:27:47','audio/04_FranckPiano_Trio_op.11.mp3',24,229),(128,'hallodk\n\n','01_bach_violin_partita_3_in_e_bwv_1006-preludio (copy)',1,'2011-05-07 15:53:28','2011-05-07 15:58:06','audio/01_Bach_Violin_Partita_3_In_E,_BWV_1006-Preludio.mp3',24,229),(129,'hallodk\n\n','02_penderecki_violinkonzert (copy)',1,'2011-05-07 15:53:42','2011-05-07 15:58:12','audio/02_Penderecki_Violinkonzert.mp3',24,229),(130,'hallodk\n\n','03_beethoven_violinkonzert',1,'2011-05-07 15:54:02','2011-05-09 07:59:32','audio/03_Beethoven_Violinkonzert.mp3',24,229),(131,'hallodk\n\n','05_adagio_fuer_10_streicher_und_tunnelklaenge (copy)',1,'2011-05-07 15:54:17','2011-05-07 15:58:24','audio/05_Adagio_fuer_10_Streicher_und_Tunnelklaenge.mp3',24,229),(132,'hallodk\n\n','06_am_grabe_siegfrieds_copy',1,'2011-05-07 15:54:30','2011-06-11 11:36:20','audio/06_Am_Grabe_Siegfrieds.mp3',24,229),(133,'hallodk\n\n','03_beethoven_violinkonzert',1,'2011-05-07 17:18:34','2011-05-09 07:59:04','images/03_Beethoven_Violinkonzert.jpg',24,225),(73,'hallodk\n\n','discography',1,'2011-05-01 23:07:27','2011-05-01 23:09:19','dis_en.html',24,202),(74,'hallodk\n\n','cv_en',1,'2011-05-01 23:13:57','2011-05-01 23:22:08','cv_en.html',24,201),(75,'hallodk\n\n','pedagogical_work',1,'2011-05-01 23:28:08','2011-05-01 23:28:46','paed_en.html',24,203),(76,'hallodk\n\n','projects',1,'2011-05-01 23:30:16','2011-05-01 23:30:53','aud_en.html',24,222),(77,'hallodk\n\n','ç®€åŽ†',1,'2011-05-01 23:47:44','2011-05-01 23:48:32','cv_cn.html',24,204),(78,'hallodk\n\n','dis_cn',1,'2011-05-02 00:25:34','2011-05-02 00:26:24','dis_cn.html',24,205),(79,'hallodk\n\n','paed',1,'2011-05-02 00:31:09','2011-05-02 00:31:51','paed_cn.html',24,206),(80,'hallodk\n\n','aud',1,'2011-05-02 00:35:06','2011-05-02 00:35:54','aud_cn.html',24,223),(124,'hallodk\n\n','04_franckpiano_trio_op11',1,'2011-05-06 12:31:34','2011-05-08 11:11:32','images/04_FranckPiano_Trio_op.11.png',24,225),(113,'hallodk\n\n','projekte (copy)',1,'2011-05-05 08:29:20','2011-05-05 19:17:47','aud_de.html',24,211),(119,'hallodk\n\n','05_adagio_fuer_10_streicher_und_tunnelklaenge',1,'2011-05-05 21:12:30','2011-05-07 15:49:34','images/05_Adagio_fuer_10_Streicher_und_Tunnelklaenge.jpg',24,225),(120,'hallodk\n\n','06_am_grabe_siegfrieds',1,'2011-05-05 21:12:32','2011-05-07 15:51:57','images/06_Am_Grabe_Siegfrieds.jpg',24,225),(121,'hallodk\n\n','symimages',1,'2011-05-05 21:17:32','2011-05-05 22:53:34','http://dsbrg.net/api/composites/edinger/category/images',24,226),(122,'hallodk\n\n','symimages',1,'2011-05-05 22:53:28','2011-05-08 16:10:16','http://dsbrg.net/api/composites/edinger/category/images',24,232),(123,'hallodk\n\n','symimages',1,'2011-05-05 22:54:56','2011-05-05 22:56:24','http://dsbrg.net/api/composites/edinger/category/images',24,227),(138,'hallodk\n\n','presse',1,'2011-05-09 09:04:18','2011-05-09 09:05:00','press_de.html',24,242),(139,'hallodk\n\n','links (copy)',1,'2011-05-09 09:12:51','2011-05-09 09:13:16','links.html',24,245),(140,'hallodk\n\n','presse (copy)',1,'2011-05-09 09:14:03','2011-05-09 09:14:40','press_en.html',24,246),(141,'hallodk\n\n','links (copy)',1,'2011-05-09 09:17:31','2011-05-09 09:17:57','links_cn.html',24,247),(143,'hallodk\n\n','einnahmen (copy)',1,'2011-05-20 16:39:58','2011-05-20 16:40:03','http://boerseonline.de/static/bilder/charts/Chartanalysefallend_tk.gif',25,241),(144,'hallodk\n\n','einnahmen (copy) (copy)',1,'2011-05-20 16:40:06','2011-05-20 16:40:11','http://boerseonline.de/static/bilder/charts/Chartanalysefallend_tk.gif',25,241),(145,'hallodk\n\n','einnahmen (copy)',0,'2011-05-22 11:15:31',NULL,'http://boerseonline.de/static/bilder/charts/Chartanalysefallend_tk.gif',25,241),(199,'Dritter','dritter',1,'2011-05-31 12:13:53','2011-06-14 20:43:24','http://search.twitter.com/search.json',30,250),(210,'Zweiter','zweiter',1,'2011-06-05 17:23:20','2011-06-14 20:43:18','http://search.twitter.com/search.json',30,250),(211,'hallodk\n\n','community_project',1,'2011-06-05 22:54:25','2011-06-05 23:29:33','cyvcdfs',31,258),(212,'hallodk\n\n','new_technologys',1,'2011-06-05 22:54:38','2011-06-05 23:29:12','cyvcdfs',31,258),(217,'hallodk\n\n','index_news',1,'2011-06-09 18:20:31','2011-06-09 20:33:46','http://systemadminbbdk:admin@website.appstack.io/content/news/index_news.html',31,267),(218,'hallodk\n\n','ein_stern',1,'2011-06-13 09:57:48','2011-06-13 10:01:02','376.884,144.983 326.391,131.958 287.036,166.169  	283.82,114.123 239.122,87.266 287.628,68.124 299.358,17.314 332.552,57.531 384.5,52.985 356.509,96.983',30,268),(213,'hallodk\n\n','factsheets',1,'2011-06-05 22:54:41','2011-06-05 23:59:23','cyvcdfsaCSACSACascasCASCASCASCAScasCASc',31,258),(216,'Hallo Munkh wie GEHT es DIR','hallo-munkh-wie-geht-es-dir',1,'2011-06-06 08:55:53','2011-06-17 08:02:18','http://search.twitter.com/search.json',30,250),(215,'hallodk\n\n','factsheets_(copy)',1,'2011-06-05 23:36:11','2011-06-05 23:36:16','cyvcdfs',31,258),(219,'216','216',1,'2011-06-17 08:03:55','2011-06-17 08:06:56','http://search.twitter.com/search.json',30,268);
/*!40000 ALTER TABLE `composites` ENABLE KEYS */;
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
  `valdef` char(60) DEFAULT NULL,
  `req` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mnv`),
  KEY `fk_meta` (`fk_meta`)
) ENGINE=MyISAM AUTO_INCREMENT=16434 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meta_namevalues`
--

LOCK TABLES `meta_namevalues` WRITE;
/*!40000 ALTER TABLE `meta_namevalues` DISABLE KEYS */;
INSERT INTO `meta_namevalues` VALUES (16433,184,'ein_anderer_metaname','auch mit defaultwert',0),(16432,184,'ein_metaname','mit einem defaultwert',0),(16431,183,'w','s',0),(16430,182,'word_10',NULL,0),(16429,182,'word_9',NULL,0),(16428,182,'word_8',NULL,0),(16427,182,'word_7',NULL,0),(16426,182,'word_6',NULL,0),(16425,182,'word_5',NULL,0),(16424,182,'word_4',NULL,0),(16423,182,'word_3',NULL,0),(16422,182,'word_2',NULL,0),(16421,182,'word_1',NULL,0),(16420,181,'stadtplz','frankfurt',0),(16419,180,'second_property',NULL,0),(16418,180,'first_property','withdefault',0),(16417,179,'text','cccccc',0),(16416,179,'background','000000',0),(16415,179,'border','ff0000',0),(16414,178,'prefix',NULL,0),(16413,177,'hashtag_10',NULL,0),(16412,177,'hashtag_9',NULL,0),(16411,177,'hashtag_8',NULL,0),(16410,177,'hashtag_7',NULL,0),(16409,177,'hashtag_6',NULL,0),(16408,177,'hashtag_5',NULL,0),(16407,177,'hashtag_4',NULL,0),(16406,177,'hashtag_3',NULL,0),(16405,177,'hashtag_2',NULL,0),(16404,177,'hashtag_1',NULL,0),(16403,176,'sadsa',NULL,0),(16402,175,'asdasdsa',NULL,0),(16401,174,'sdfsdsadd',NULL,0),(16400,174,'sfsda',NULL,0),(16395,172,'970z097z','ÃŸ98zu',0),(16394,172,'97z0p978z',NULL,0),(16393,172,'078z087','7',0),(16392,172,'870z0897z087',NULL,0),(16391,172,'o8z87','87z',0),(16390,171,'piuhpiuh','upihpiuh',0),(16389,171,'iuhp','ipuhpiu',0),(16388,171,'ipuohipouh','piuh',0),(16381,168,'compid',NULL,0),(16380,167,'prefix','dd',0),(16379,167,'fsgsdf','fdgfd',0),(16378,166,'sdfds','sdfds',0),(14371,159,'address','content/',0),(14376,162,'content-type','html',0),(14375,162,'charset','utf-8',0),(16387,171,'iuhhiouh',NULL,0),(16386,171,'iuohiouh',NULL,0),(16385,171,'pu9p9u','piuh',0),(16384,170,'isaudio','yes',0),(16383,169,'vermÃ¶gen',NULL,0),(14374,161,'prefix','de',0),(14373,161,'countrys','germany;swiss;austria',0),(14372,160,'index','no',0),(16382,169,'depot',NULL,0);
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
) ENGINE=MyISAM AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metas`
--

LOCK TABLES `metas` WRITE;
/*!40000 ALTER TABLE `metas` DISABLE KEYS */;
INSERT INTO `metas` VALUES (161,'24','language',0,0),(160,'24','landing',0,0),(169,'25','zinsen',0,0),(170,'24','isaudio',0,0),(159,'24','basepath',0,0),(168,'24','hasaudio',0,0),(162,'24','mime',0,0),(167,'24','test',0,0),(171,'1','aasdasdf',0,0),(166,'1','test',0,0),(172,'25','sdfdsds',0,0),(174,'25','fdsioh_fdsg',0,0),(175,'25','asdassa',0,0),(176,'25','adsas',0,0),(177,'30','q',0,0),(178,'30','lang',0,0),(179,'30','colorhex',0,0),(180,'30','metainformation',0,0),(181,'30','in',0,0),(182,'30','blacklist',0,0),(183,'1','s',0,0),(184,'24','test_meta',0,0);
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
  `name` char(50) NOT NULL,
  `fk_group` int(11) NOT NULL,
  `href` char(255) NOT NULL,
  `target` char(10) DEFAULT NULL,
  `fornav` char(50) DEFAULT NULL,
  PRIMARY KEY (`id_right`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES (1,'Profile',1,'user/profile','_self','mytagitall'),(2,'User',100,'user/management','_self','mytagitall'),(3,'Application Builder',1,'builder','_blank','mytagitall'),(4,'Logout',1,'user/logout','_self',NULL),(5,'Press Information',0,'content/press','_self','home'),(6,'Related Articles',0,'documentation/relate','_self','documentation');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_description_values`
--

DROP TABLE IF EXISTS `service_description_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_description_values` (
  `id_value` int(11) NOT NULL AUTO_INCREMENT,
  `valuepattern` varchar(255) NOT NULL,
  PRIMARY KEY (`id_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_description_values`
--

LOCK TABLES `service_description_values` WRITE;
/*!40000 ALTER TABLE `service_description_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_description_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_descriptions`
--

DROP TABLE IF EXISTS `service_descriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_descriptions` (
  `id_service_description` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value_fk` int(11) NOT NULL,
  `service_fk` int(11) NOT NULL,
  PRIMARY KEY (`id_service_description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_descriptions`
--

LOCK TABLES `service_descriptions` WRITE;
/*!40000 ALTER TABLE `service_descriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_descriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_types`
--

DROP TABLE IF EXISTS `service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_types` (
  `id_content_type` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(1) DEFAULT NULL,
  `basesrc` char(1) DEFAULT NULL,
  `iconpath` char(1) DEFAULT NULL,
  PRIMARY KEY (`id_content_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_types`
--

LOCK TABLES `service_types` WRITE;
/*!40000 ALTER TABLE `service_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
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
  `firstname` char(50) NOT NULL,
  `lastname` char(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` char(50) NOT NULL,
  `phone` char(50) DEFAULT NULL,
  `ZIP` char(10) NOT NULL,
  `country` enum('Afghanistan','&Auml;gypten','Albanien','Algerien','Amerik. Samoa','Andorra','Angola','Anguilla','Antarktika','Antigua / Barbuda','&Auml;quatorialguinea','Argentinien','Armenien','Aruba','Aserbaidschan','&Auml;thiopien','Australien','Bahamas','Bahrain','Bangladesh','Barbados','Belgien','Belize','Benin','Bermuda','Bhutan','Bolivien','Bosnien und Herzegowina','Botsuana','Bouvetinsel','Brasilien','Britisches Territorium im Indischen Ozean','Brunei Darussalam','Bulgarien','Burkina Faso','Burundi','Cayman Islands','Chile','China','Cookinseln','Costa Rica','D&auml;nemark','Deutschland','Dominica','Dominikanische Republik','Dschibuti','Ecuador','El Salvador','Elfenbeink&uuml;ste','Eritrea','Estland','Falklandinseln','F&auml;r&ouml;er','Fidschi','Finnland','Frankreich','Franz&ouml;sisch Guayana','Franz&ouml;sische S&uuml;d- und Antarktisgebiete','Franz&ouml;sisch-Polynesien','Gabun','Gambia','Georgien','Ghana','Gibraltar','Grenada','Griechenland','Gr&ouml;nland','Gro&szlig;britannien','Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Heard und McDonaldinseln','Honduras','Hongkong','Indien','Indonesien','Irak','Iran','Irland','Island','Israel','Italien','Jamaika','Japan','Jemen','Jordanien','Jungferninseln Amerikanisch','Jungferninseln Britisch','Kambodscha','Kamerun','Kanada','Kapverdische Inseln','Kasachstan','Katar','Kenia','Kirgisistan','Kiribati','Kokosinseln Keelinginseln','Kolumbien','Komoren','Kongo Dem. Republik','Kongo Republik','Korea demokratische Volksrepublik','Korea Republik','Kosovo','Kroatien','Kuba','Kuwait','Laos','Lesotho','Lettland','Libanon','Liberia','Libyen / Libysch-Arabische Dschamahirja','Liechtenstein','Litauen','Luxemburg','Macau','Madagaskar','Malawi','Malaysia','Malediven','Mali','Malta','Marokko','Marshallinseln','Martinique','Mauretanien','Mauritius','Mayotte','Mexiko','Mikronesien','Monaco','Mongolei','Montenegro','Montserrat','Mosambik','Myanmar','Namibia','Nauru','Nepal','Neukaledonien','Neuseeland','Nicaragua','Niederl. Antillen','Niederlande','Niger','Nigeria','Niue','N&ouml;rdliche Marianen','Norfolkinsel','Norwegen','Oman','&Ouml;sterreich','Pakistan','Pal&auml;stinensische Gebiete','Palau','Panama','Papua-Neuguinea','Paraguay','Peru','Philippinen','Polen','Portugal','Puerto Rico','Republik Mazedonien','Republik Moldau','Republik Suriname','Reunion','Ruanda','Rum&auml;nien','Russische F&ouml;deration','Saint-Pierre und Miquelon','Sambia','Samoa','San Marino','Sao Tome und Principe','Saudi-Arabien','Schweden','Schweiz','Senegal','Serbien','Seychellen','Sierra Leone','Singapur','Slowakei','Slowenien','Solomonen','Somalia','South Georgia and South Sandwich','Spanien','Spitzbergen und Jan Mayen','Sri Lanka','St. Helena','St. Kitts und Nevis','St. Lucia','St. Vincent und die Grenadinen','S&uuml;dafrika','Sudan','Swasiland','Syrien','Tadschikistan','Taiwan','Tansania','Thailand','Timor-Leste','Togo','Tokelau','Tonga','Trinidad und Tobago','Tschad','Tschechische Republik','Tunesien','T&uuml;rkei','Turkmenistan','Turks- und Caicosinseln','Tuvalu','Uganda','Ukraine','Ungarn','United States Minor Outlying Islands','Uruguay','Uzbekistan','Vanuatu','Vatikan','Venezuela','Vereinigte Arabische Emirate','Vereinigte Staaten von Amerika','Vietnam','Wallis and Futuna','Weihnachtsinsel','Wei&szlig;russland','Westsahara','Zentralafrikanische Republik','Zimbabwe','Zypern') DEFAULT NULL,
  `city` char(50) DEFAULT NULL,
  `address` char(100) DEFAULT NULL,
  `your_branch_working_in` enum('Webdesign','Webdevelopment','Socialnetworks','Search Engine','Data Moddeling') DEFAULT NULL,
  `your_interests` set('Coding','Sematics','Ai','Socialdevelopment','Finance','Analytics') DEFAULT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `confirmed` char(50) DEFAULT NULL,
  `catbuild` int(11) DEFAULT NULL,
  `compbuild` int(11) DEFAULT NULL,
  `metabuild` int(11) DEFAULT NULL,
  `groupbuild` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `your_prefered_username_UNIQUE` (`username`),
  KEY `fk_group` (`fk_group`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,100,'root','002edc51273bd2d1ac2777be38ae11f5','Rootuser','Rootuser','0000-00-00','rootuser@system.com',NULL,'44444','Afghanistan',NULL,NULL,'Webdesign','Coding','2011-06-04 16:00:55','confirmed',0,0,1307203255,NULL),(23,1,'mbehrendt','4d2a1208a215e5eb8af3f5799c84372a','Maike','Behrendt','0000-00-00','maikebehrendt@gmailcom','0907979778','55118','Deutschland','Mainz','Hindenburgstr51','Webdesign','Coding','2011-04-06 21:31:02','confirmed',0,NULL,NULL,NULL),(28,1,'martinb','343b97b532250fd379cfcf6e3941a592','martin','bbbbbbb','0000-00-00','bernhardbezdek@gmailcom','','39485','Afghanistan','Offenbach','none','Webdesign','Coding,Sematics','2011-05-07 08:58:53','confirmed',NULL,NULL,NULL,NULL),(25,1,'bernhardb','4b52d4d8c5403a593370e961ed2a7b38','Bernhard','Bezdek','0000-00-00','bernhardbezdek@gmailcom','','56412','Afghanistan','','','Webdesign','Coding,Sematics','2011-05-22 11:15:28','confirmed',0,0,1305926163,NULL),(24,1,'edinger','59cbd7173fe621777cc499a964e2effb','Christiane','Edinger','0000-00-00','dk@samplositioncom','','12345','Deutschland','Duisburg','','Webdesign','Coding,Sematics,Ai','2011-06-11 11:25:28','confirmed',0,0,1307791528,NULL),(29,NULL,'roberta','b0b2deb0e8c04bfbba01c0e70911b085','roberta','roberta','0000-00-00','bernhardbezdek@gmailcom','','39485','Deutschland','Offenbach','none','Socialnetworks','Coding,Sematics','2011-05-07 09:08:16','c62ebe87ada763f9ee3327bea9d8ba3f',NULL,NULL,NULL,NULL),(30,1,'demo','936670c0c6ca8f414ce5e02060928966','Bernhard','Bezdek','0000-00-00','bernhardbezdek@gmailcom','','11111','Deutschland','Offenbach','','Webdesign','Coding,Sematics,Ai,Socialdevelopment,Finance,Analytics','2011-06-13 09:57:55','confirmed',0,0,1306966548,NULL),(31,1,'appstack','649ed9b6965982038872f3fee8e0a5c8','appstack','appstack','0000-00-00','bernhardbezdek@gmailcom','','88888','Deutschland','Offenbach','','Webdesign','Coding','2011-06-09 18:20:57','confirmed',0,0,NULL,NULL);
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

-- Dump completed on 2011-06-17 16:33:12
