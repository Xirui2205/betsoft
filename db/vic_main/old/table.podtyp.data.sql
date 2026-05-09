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
-- Dumping data for table `podtyp`
--

LOCK TABLES `podtyp` WRITE;
/*!40000 ALTER TABLE `podtyp` DISABLE KEYS */;
INSERT INTO `podtyp` VALUES (23,0,0,'','1X2'),
(24,0,0,'','1X12X2'),
(25,0,0,'','vice/mene'),
(27,0,0,'','ano/ne'),
(29,0,0,'','12'),
(30,1,4,'','V2368101520'),
(40,1,5,'','Varianta A'),
(71,1,6,'','Varianta B'),
(73,1,5,'','Varianta C'),
(74,1,6,'','Varianta D'),
(75,1,5,'','Varianta E'),
(76,1,5,'','Varianta F'),
(77,1,5,'','Varianta G'),
(78,1,5,'','Varianta H'),
(79,1,5,'','Varianta I'),
(80,1,6,'','Varianta J'),
(81,1,6,'','Varianta K'),
(82,1,6,'','Varianta L'),
(83,1,5,'','Varianta M'),
(84,1,5,'','Varianta N'),
(85,1,6,'','Varianta O'),
(86,1,5,'','Varianta P'),
(87,1,5,'','Varianta Q'),
(88,1,5,'','Varianta R'),
(89,1,6,'','Varianta S'),
(90,1,6,'','Varianta T'),
(91,1,6,'','Varianta U'),
(92,0,0,'','V368'),
(93,0,0,'','V36'),
(94,1,5,NULL,'Přesný výsledek 1178025212'),
(95,0,0,'','ano'),
(96,0,0,'','1'),
(97,0,0,'','1_copy'),
(98,1,6,NULL,'Přesný výsledek 1182703382'),
(99,1,6,NULL,'Přesný výsledek 1183720203'),
(100,0,0,'','postup'),
(101,1,6,NULL,'Přesný výsledek 1184568912'),
(102,1,6,'','Přesný výsledek hhh'),
(103,1,6,NULL,'Přesný výsledek 1190959123'),
(104,1,6,NULL,'Přesný výsledek 1195455980'),
(105,0,0,'','V24816'),
(106,0,0,'','01234'),
(107,1,3,'','HT/FT'),
(108,0,0,'','V2348'),
(109,0,0,'0,5','vice/mene_0.5'),
(110,0,0,'1,5','vice/mene_1.5'),
(111,0,0,'2,5','vice/mene_2.5'),
(112,0,0,'3,5','vice/mene_3.5'),
(113,0,0,'4,5','vice/mene_4.5'),
(114,0,0,'5,5','vice/mene_5.5'),
(115,0,0,'6,5','vice/mene_6.5'),
(116,0,0,'7,5','vice/mene_7.5'),
(117,0,0,'8,5','vice/mene_8.5'),
(118,0,0,'9,5','vice/mene_9.5'),
(119,0,0,'','1X2_gol'),
(120,1,6,NULL,'Přesný výsledek 1221729974'),
(121,0,0,'','licha/suda'),
(122,1,5,NULL,'Přesný výsledek 1224685154'),
(123,1,6,NULL,'Přesný výsledek 1224826442'),
(124,1,6,NULL,'Přesný výsledek 1224826444'),
(125,1,5,NULL,'Přesný výsledek 1227592430'),
(126,1,7,NULL,'Přesný výsledek 1231595537'),
(127,1,0,NULL,'Přesný výsledek 1233085664'),
(128,1,2,'','tenis_PV2'),
(129,1,3,'','tenis_PV3'),
(130,0,0,'-1,5','AH_1.5'),
(131,0,0,'-2,5','AH_2.5'),
(132,0,0,'-3,5','AH_3.5'),
(133,0,0,'-4,5','AH_4.5'),
(134,0,0,'-5,5','AH_5.5'),
(135,0,0,'-6,5','AH_6.5'),
(136,0,0,'-7,5','AH_7.5'),
(137,0,0,'-8,5','AH_8.5'),
(138,0,0,'-9,5','AH_9.5'),
(139,0,0,'-10,5','AH_10.5'),
(140,0,0,'-14,5','AH_14.5'),
(141,0,0,'-19,5','AH_19.5'),
(142,0,0,'-24,5','AH_24.5'),
(143,0,0,'-29,5','AH_29.5'),
(144,0,0,'+1,5','AH_1.5_copy'),
(145,0,0,'+2,5','AH_2.5_copy'),
(146,0,0,'+3,5','AH_3.5_copy'),
(147,0,0,'+4,5','AH_4.5_copy'),
(148,0,0,'+5,5','AH_5.5_copy'),
(149,0,0,'+6,5','AH_6.5_copy'),
(150,0,0,'+7,5','AH_7.5_copy'),
(151,0,0,'+8,5','AH_8.5_copy'),
(152,0,0,'+9,5','AH_9.5_copy'),
(153,0,0,'+10,5','AH_10.5_copy'),
(154,0,0,'+14,5','AH_14.5_copy'),
(155,0,0,'+19,5','AH_19.5_copy'),
(156,0,0,'+24,5','AH_24.5_copy'),
(157,0,0,'+29,5','AH_29.5_copy'),
(158,0,0,'119,5','vice/mene_119.5'),
(159,0,0,'124,5','vice/mene_124.5'),
(160,0,0,'129,5','vice/mene_129.5'),
(161,0,0,'134,5','vice/mene_134.5'),
(162,0,0,'139,5','vice/mene_139.5'),
(163,0,0,'144,5','vice/mene_144.5'),
(164,0,0,'149,5','vice/mene_149.5'),
(165,0,0,'154,5','vice/mene_154.5'),
(166,0,0,'159,5','vice/mene_159.5'),
(167,0,0,'164,5','vice/mene_164.5'),
(168,0,0,'169,5','vice/mene_169.5'),
(169,0,0,'174,5','vice/mene_174.5'),
(170,0,0,'179,5','vice/mene_179.5'),
(171,0,0,'','Testd'),
(172,0,0,'1.gol','aa test');
/*!40000 ALTER TABLE `podtyp` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-15 13:35:20
