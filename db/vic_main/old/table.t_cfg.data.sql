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
-- Dumping data for table `t_cfg`
--

LOCK TABLES `t_cfg` WRITE;
/*!40000 ALTER TABLE `t_cfg` DISABLE KEYS */;
INSERT INTO `t_cfg` VALUES (3,'2009-04-20 11:39:33','ONLINETIME','10000','cas aktualizace online sledovani','integer'),
(4,'2008-09-15 16:37:31','BOOK_CHECK_TIME_MORE','90','kdyz si BM prodlouzi cas pro schvaleni, je to o tuto hodnotu','integer'),
(5,'2008-09-15 16:11:26','AF_BANNERS_PER_PAGE','10','pocet banneru nastranku vypisu Nastroju','integer'),
(6,'2008-09-12 16:52:47','AFPAYMENT_PERIOD','7','pocet dni, kter musi uplynout od posledni zadosti','integer'),
(8,'2008-09-15 14:56:38','RISK_LIMIT_PCT','10','procentni podil, pres ktery nesmi byt vycerpan risk limit na jednom ticketu','integer'),
(9,'2008-10-13 17:06:20','HH_TODAY_CREDIT','100','max. pocet HH udelenych v jednom dni','integer'),
(10,'2008-10-03 18:57:12','LIVE_WAIT_TIME_NO_CONFIRM','10','live cekaci perioda, kdyz nespada do schvalovani','integer'),
(11,'2008-10-03 18:57:20','LIVE_WAIT_TIME_WITH_CONFIRM','20','live cekaci perioda, kdyz je tiket ve schvalovani','integer'),
(12,'2008-10-03 18:57:32','AFAMOUNT_LIMIT','50','limit AF pro vyber (EUR)','double'),
(13,'2008-09-08 17:59:43','TEST','ble','','double'),
(14,'2008-09-17 13:04:24','MSG_PER_PAGE','15','pocet zprav v sezanmu na strance','integer'),
(15,'2008-10-03 18:57:44','MONEY_PRECISION','2','pocet desetinnych mist u meny','integer'),
(16,'2008-10-03 18:57:56','TRANSFERS_PER_PAGE','10','pocet radku vypisu operaci na konte na jednu stranku','integer'),
(17,'2008-10-03 18:58:05','TICKETS_PER_PAGE','10','pocet radku vypisu ticketu na jednu stranku ','integer'),
(18,'2008-10-03 18:58:15','NEWS_LIST_PERPAGE','10','pocet novinek na strance seznamu novinek','integer'),
(19,'2008-10-03 18:58:28','NEWS_SHORT_ANOTATION_LENGTH','150','max pocet znaku pro oriznuti a doplenni teckami ...','integer'),
(20,'2008-10-03 18:59:08','FSB_MAX_FRIENDS','10','max pocet lidi, kter muze s FSB clovek omeldovat','integer'),
(21,'2008-10-03 18:59:29','FSB_RATE_LIMIT','1.5','limitni kurz, pri jehoz NEdosazeni nelze uplatnit FSB ','double'),
(22,'2008-10-03 18:59:43','HAPPYHOURS_PERIOD','7','delka periody hh, ve ktere se hlida vyuzivani bonusu','integer'),
(23,'2008-09-17 13:26:40','HH_RING_PERIOD','900','prodleva v sec mezi jednotlivymi zazvonenimi HH banneru','integer'),
(24,'2008-09-17 13:28:16','LOS_RATE_LIMIT','1.50','limitni kurz, pri jehoz prekroceni se vsazena castka zapocitava do protaceni penez','double'),
(25,'2008-09-17 13:28:59','DB_RATE_LIMIT','1.50','limitni kurz, pri jehoz prekroceni se vsazena castka posila do funkci deposit bonusu','double'),
(26,'2008-10-03 19:00:22','TP_LIVE_ONRUN','5','perioda ajax req, kdyz je zapas ','integer'),
(27,'2008-10-03 19:00:34','TP_LIVE_ONSTOP','10','perioda ajax req, kdyz je zapas zrovna nebezi','integer'),
(28,'2008-10-03 19:00:47','AUTOTICKET_LIMIT','25','hranice, pod niz tiket nepodleha nejake sade testu','integer'),
(29,'2008-10-03 19:01:02','MAX_SYSTEM_WIN_LIMIT','100000','maximalni mozna vyhra v systemu v EUR','integer'),
(30,'2008-09-17 13:59:27','BET_URL','','','string'),
(31,'2008-09-17 13:59:54','TOURNAMENT_URL','','','string'),
(32,'2008-09-17 14:01:10','KURZ_LESS_ONE','-','znak, pokud je kurz mensi nebo jedna','string'),
(33,'2008-11-13 13:39:06','SAME_TICKET_LIMIT','2','pocet stejnych tiketu za 24 hod','integer'),
(34,'2008-09-18 14:35:58','TC_SINGLE_MIN','45','schvalovaci minimalni mez pro jeden radek na kuponu','integer'),
(35,'2009-02-27 15:32:57','TC_SINGLE_MAX','60','schvalovaci maximalni mez pro jeden radek na kuponu','integer'),
(36,'2008-09-18 14:37:00','TC_MULTI_MIN','80','schvalovaci minimalni mez pro dva a vice radek na kuponu ','integer'),
(37,'2008-09-18 14:37:13','TC_MULTI_MAX','110','schvalovaci maximalni mez pro dva a vice radek na kuponu ','integer'),
(38,'2008-10-03 19:01:39','TICKET_SYSTEM_MAX_BETS','10','maximalni pocet sazek na systemovem tiketu','integer'),
(39,'2008-10-03 19:01:45','TICKET_OTHERS_MAX_BETS','20','maximalni pocet sazek na tiketu ktery neni systemovy','integer'),
(40,'2008-09-29 19:52:47','TICKET_MIN_PRICE','0.5','minimalni castka k vsazeni','double'),
(41,'2008-10-14 08:52:50','WAP_HOST','','adresa wapovych stranek (pro presmerovani)','string'),
(44,'2009-02-23 13:37:37','WINNER_WEAK_FREEBET','50','FREE BET VYPLACENY VYHERCI TYDNE V EUR','integer'),
(45,'2008-10-23 10:45:12','WINNER_MONTH_FREEBET_1','200','FREE BET VYPLACENY VYHERCI MESICE ZA PRVNI MISTO V EUR','integer'),
(46,'2008-10-23 10:45:29','WINNER_MONTH_FREEBET_2','100','FREE BET VYPLACENY VYHERCI MESICE ZA DRUHE MISTO V EUR','integer'),
(47,'2008-10-23 10:45:51','WINNER_MONTH_FREEBET_3','50','FREE BET VYPLACENY VYHERCI MESICE ZA TRETI MISTO V EUR','integer'),
(48,'2008-10-23 18:57:18','LIVE_BETS_PER_PAGE_MOBIL','4','pocet live sazek ve vypisu','integer'),
(49,'2008-10-30 15:36:40','NEW_AMOUNT_DECISION_TIME','180','cas v sekundach na potvrzeni','integer'),
(50,'2008-11-06 16:49:07','BOOK_CHECK_TIME','30','Pocet sekund na povolovani bez nastaveni','integer'),
(51,'2008-12-22 11:23:14','DEPOSIT_MIN_BETS','4','minimalni pocet sazek pro zapsani user deposit','integer'),
(52,'2008-12-22 11:25:25','DEPOSIT_MIN_RATE','1.8','minimalni kurz pro zapsani user deposit','double'),
(53,'2008-12-22 11:26:57','DEPOSIT_MIN_RATE_EACH','1.10','minimalni kurz prave jedne sazky pro zapsani user deposit','double'),
(54,'2009-04-06 00:00:00','TICKETS_PER_PAGE_MOBIL','1','pocet radku vypisu ticketu na jednu stranku ','integer'),
(55,'2009-09-09 14:45:59','NLETTER_BONUS_HREF_ID','143','defaultni id stranky, na kterou odkazuje bonus z newsletteru','integer'),
(56,'2009-04-21 00:00:00','TRANSFERS_PER_PAGE_MOBIL','1','pocet radku vypisu operaci na konte na jednu stranku','integer'),
(57,'2009-09-09 14:45:59','NLETTER_NEWS_ACTUAL','660,659','novinky v newsletteru','string'),
(58,'2009-04-21 18:58:29','NLETTER_LIVE_BET_DELAY','5','odstup zobrazovanych live sazek v newsletteru od ted (v hodinach)','integer'),
(59,'2009-05-20 17:50:50','NLETTER_SEND_DAEMON','/rest/www/release/admin/www/sendMail.php','PHP skript, ktery je volan, aby odeslal newslettery','string'),
(60,'2009-04-21 19:14:51','NLETTER_NOTIFY_EMAIL','','emailova adresa, kam se posila vysledek odeslani newsletteru','string'),
(61,'2009-09-09 14:45:58','NLETTER_BONUS_IMG_ID','4524','defaultni obrázek pro bonus; braný podle id z galerie obrázků','integer'),
(62,'2009-04-28 17:43:23','MINIGAMES_DEFAULT_GAME','54','jaka hra se zobrazi jako prvni na strance live betting calendar','integer'),
(63,'2009-04-29 13:35:09','TRNG_CASINO_COUNT','2','pocet HW generatoru pouzitych pro casino','integer'),
(64,'2009-04-29 13:35:09','TRNG_CASINO_IP1','192.168.30.17','IP adresa HW generatoru pro casino (1)','string'),
(65,'2009-04-29 13:36:40','TRNG_CASINO_IP2','192.168.30.16','IP adresa HW generatoru pro casino (2)','string'),
(66,'2009-04-29 13:36:40','TRNG_CASINO_PORT1','9432','port pro TRNG_CASINO_IP1','integer'),
(67,'2009-04-29 13:38:48','TRNG_CASINO_PORT2','9432','port pro TRNG_CASINO_IP2','integer'),
(68,'2009-04-29 13:38:48','TRNG_CASINO_TIMEOUT','1','jak dlouho cekame na cislo z HW generatoru','integer'),
(69,'2009-06-03 19:33:27','ARENA_SERVER_IP','83.64.220.41','kde bezi aplikacni socket server pro Arenu','string'),
(70,'2009-06-03 19:34:04','ARENA_SERVER_PORT','9876','kde bezi aplikacni socket server pro Arenu - port','integer'),
(71,'2009-06-03 19:35:17','ARENA_WEB_SERVICE','','kde bezi webove sluzby pro Arenu','string'),
(72,'2009-06-10 14:12:25','NLETTER_HOT_BET_DELAY','5','odstup zobrazovanych hot sazek v newsletteru od ted (v hodinach)','integer'),
(73,'2008-10-14 08:52:50','WEB_HOST','','adresa webovych stranek','string'),
(74,'2009-06-16 13:35:02','NLETTER_CLICK_COUNTER','45','zda generovat do odkazu v newsletteru zapocitavani (pokud je hodnota 0, tak ne, jinak je cislem max. ID linku)','integer'),
(75,'2009-07-15 15:17:46','MAX_BETS_FOR_INDIVIDUAL_LIMIT','3',NULL,'integer'),
(76,'2009-07-15 00:00:00','AFFILIATE_MIN_DEP_CLIENTS','0','min pocet vkladajicich uzivatelu pro affiliate partnera aby dostal zaplaceno','integer');
/*!40000 ALTER TABLE `t_cfg` ENABLE KEYS */;
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
