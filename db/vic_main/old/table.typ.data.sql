-- MySQL dump 10.13  Distrib 5.1.47, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: vic_main
-- ------------------------------------------------------
-- Server version	5.1.47

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
-- Dumping data for table `typ`
--

LOCK TABLES `typ` WRITE;
/*!40000 ALTER TABLE `typ` DISABLE KEYS */;
INSERT INTO `typ` VALUES (16,'1.polocas',1,29),
(17,'2.polocas',1,30),
(18,'handicap',1,8),
(19,'zapas',1,1),
(20,'vice-mene',1,44),
(21,'vitez_ligy',1,70),
(22,'vitez_zapasu',1,6),
(23,'presny_vysledek',1,75),
(24,'dvojita_sance',1,5),
(25,'asijsky_handicap',1,45),
(26,'1.tretina',1,32),
(27,'2.tretina',1,33),
(28,'3.tretina',1,34),
(29,'konecne_umisteni',1,60),
(30,'ano-ne',1,55),
(31,'celkovy_vitez',1,82),
(32,'vitez',1,80),
(33,'postup',1,77),
(34,'kdo_lepe_v_sezone',1,200),
(35,'Draw_No_Bet',1,37),
(36,'Top_3',1,90),
(37,'vitez_poharu',1,38),
(38,'Top_2',1,85),
(39,'Top_4',1,95),
(40,'Top_6',1,100),
(41,'Top_8',1,105),
(42,'Top_10',1,110),
(43,'Top_15',1,115),
(44,'Top_16',0,120),
(45,'vice-mene_hry',1,56),
(46,'vice-mene_sety',1,57),
(47,'asijsky_handicap_hry',1,58),
(48,'asijsky_handicap_set',1,59),
(49,'f-strelci',1,210),
(50,'polocas-konec',1,40),
(51,'1.polocas_vice-mene',1,35),
(52,'nejlepsi_strelec',1,230),
(53,'handicap_01',1,9),
(54,'f-strelci1',1,240),
(55,'f-strelci2',1,250),
(56,'handicap_02',1,10),
(57,'handicap_03',1,11),
(58,'handicap_10',1,12),
(59,'handicap_20',1,13),
(60,'handicap_30',1,14),
(61,'1.gol',1,15),
(62,'2.gol',1,16),
(63,'3.gol',1,17),
(64,'4.gol',1,18),
(65,'5.gol',1,19),
(66,'1.set',1,350),
(67,'2.set',1,360),
(68,'3.set',1,370),
(69,'4.set',1,380),
(70,'5.set',0,390),
(71,'10b_1.set',1,400),
(72,'10b_2.set',1,410),
(73,'10b_3.set',1,420),
(74,'10b_4.set',1,430),
(75,'10b_5.set',1,440),
(76,'6.gol',1,20),
(77,'7.gol',1,21),
(78,'8.gol',1,22),
(88,'1.tretina_vice-mene',1,41),
(89,'2.tretina_vice-mene',1,42),
(90,'3.tretina_vice-mene',1,43),
(91,'9.gol',1,23),
(92,'10.gol',1,24),
(93,'11.gol',1,25),
(94,'12.gol',1,26),
(95,'prvni_gol',1,27),
(96,'licha_suda',1,39),
(97,'prvni_set',1,7),
(98,'finalova_dvojice',1,460),
(99,'20b_1.set',1,500),
(100,'20b_2.set',1,510),
(101,'20b_3.set',1,520),
(102,'20b_4.set',1,530),
(103,'live_vykop',1,1),
(104,'live_penalty',1,1),
(105,'live_vitzap',1,1),
(106,'live_vitsetu',1,1),
(107,'live_dalsigol',1,28),
(108,'live_sazbezrem',1,4),
(109,'live_tretina',1,31),
(110,'live_OT_VM',1,1),
(111,'live_dalsigol_OT',1,1),
(112,'live_dalsigol_pen',1,1),
(113,'zlute_karty',1,1),
(114,'cervene_karty',1,1);
/*!40000 ALTER TABLE `typ` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-15 13:35:21
