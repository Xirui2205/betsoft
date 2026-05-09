-- MySQL dump 10.13  Distrib 5.1.46, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: vic_admin
-- ------------------------------------------------------
-- Server version	5.1.46

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
-- Dumping data for table `sekce`
--

LOCK TABLES `sekce` WRITE;
/*!40000 ALTER TABLE `sekce` DISABLE KEYS */;
INSERT INTO `sekce` VALUES (1,0,'home',1,1,NULL,NULL),
(2,1,'Nastavení',1,1,NULL,NULL),
(3,2,'Administrační menu',1,1,NULL,NULL),
(14,1,'Administrátoři',1,0,NULL,NULL),
(40,1,'Uživatelé',1,0,NULL,NULL),
(41,2,'Jazyky',1,0,NULL,NULL),
(42,2,'Měna',1,0,NULL,NULL),
(43,2,'Země',1,0,NULL,NULL),
(44,40,'Zakázaný',1,0,NULL,NULL),
(45,2,'Uživatelské menu',1,0,NULL,NULL),
(46,1,'Překlady',1,0, NULL,NULL),
(47,1,'Platební metody',1,0, NULL,NULL),
(48,1,'Chyby a logy',1,0, NULL,NULL),
(49,2,'Další nastavení',1,0, NULL,NULL),
(57,1,'Statistiky',1,0, NULL,NULL),
(58,1,'Finance',1,0, NULL,NULL),
(61,57,'Uživatelé statistika',1,0, NULL,NULL),
(62,58,'Kurzy',1,0, NULL,NULL),
(64,58,'Platební operace',1,0, NULL,NULL),
(72,46,'Překlady Wysiwyg',1,0, NULL,NULL),
(75,1,'Bookmakeři',1,0, NULL,NULL),
(76,57,'Sazky statistika',1,0, NULL,NULL),
(77,1,'Limity',1,0, NULL,NULL),
(80,1,'Galerie',1,0, NULL,NULL),
(82,1,'Novinky',1,0, NULL,NULL),
(84,1,'Bonusy',1,0, NULL,NULL),
(88,57,'Uživatel akce',1,0, NULL,NULL),
(89,1,'Book',1,0, NULL,NULL),
(90,89,'Tikety',1,0, NULL,NULL),
(94,57,'Global statisitky',1,0, NULL,NULL),
(98,1,'Promo',1,0, NULL,NULL),
(99,98,'Homepage',1,0, NULL,NULL),
(105,84,'Pridělení free bet bonusu',1,0, NULL,NULL),
(109,2,'Konstanty',1,0, NULL,NULL),
(127,1,'Chat',1,0, NULL,NULL),
(128,1,'Pobočky',1,0, NULL,NULL),
(129,128,'Vytvořit pobočku',1,0, NULL,NULL),
(130,1,'Test',1,0, NULL,NULL),
(131,160,'Statické stránky',1,0, NULL,NULL),
(160, 1, 'Obsah', 1, 0, NULL,NULL),
(161, 160, 'Superhome', 1, 0, NULL,NULL),
(162, 98, 'Superhome', 1, 0, NULL,NULL);

/*!40000 ALTER TABLE `sekce` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-13 16:40:23
