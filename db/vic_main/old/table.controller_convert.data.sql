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
-- Dumping data for table `controller_convert`
--

LOCK TABLES `controller_convert` WRITE;
/*!40000 ALTER TABLE `controller_convert` DISABLE KEYS */;
INSERT INTO `controller_convert` VALUES (0,1,0,1,1,3,NULL,NULL,'404 - Stránka nenalezena',NULL,'Stránka nenalezena','404','index','static-page','not-found'),
(0,2,0,1,1,3,NULL,NULL,'404 - Not found',NULL,'Page not found','404','index','static-page','not-found'),
(0,16,0,1,1,3,NULL,NULL,'404 - Stránka nenalezena',NULL,'Stránka nenalezena','404','index','static-page','not-found'),
(1,1,0,1,1,0,'','','','test desc for betservice homepage','Homepage','index','index','index','index'),
(1,2,0,1,1,0,'','','','','Homepage','index','index','index','index'),
(1,16,0,1,1,0,'','','','','Homepage','index','index','index','index'),
(2,1,1,1,1,3,'','','','','Sázení','sazky','index','Sportsbook','index'),
(2,2,1,1,1,3,'','','','','Sportsbook','sportsbook','index','Sportsbook','index'),
(2,16,1,1,1,3,'','','','','Sázení','sazky-sk','index','Sportsbook','index'),
(3,1,1,2,1,3,'','','','','Live sázení','live-sazky','index','Livebetting','index'),
(3,2,1,2,1,3,'','','','','Live betting','live-betting','index','Livebetting','index'),
(3,16,1,2,1,3,'','','','','Live sázení','live-sazky-sk','index','Livebetting','index'),
(4,1,2,1,1,3,'','','','','Dnešní nabídka','dnesni-nabidka','index','Sportsbook','today'),
(4,2,2,1,1,3,'','','','','Today offer','today','index','Sportsbook','today'),
(4,16,2,1,1,3,'','','','','Dnešná ponuka','dnosna-ponuka','index','Sportsbook','today'),
(5,1,2,2,1,3,'','','','','Dnes a zítra','dnes-a-zitra','index','Sportsbook','today-and-tomorrow'),
(5,2,2,2,1,3,'','','','','Today and tomorrow','today-and-tomorrow','index','Sportsbook','today-and-tomorrow'),
(5,16,2,2,1,3,'','','','','Dnes a zítra','dnes-a-zitra','index','Sportsbook','today-and-tomorrow'),
(6,1,2,3,1,3,NULL,NULL,NULL,NULL,'Fotbal dnes','fotbal-dnes','index','Sportsbook','today-soccer'),
(6,2,2,3,1,3,NULL,NULL,NULL,NULL,'Soccer today','soccer-today','index','Sportsbook','today-soccer'),
(6,16,2,3,1,3,NULL,NULL,NULL,NULL,'Fotbal dnes','fotbal-dnes-sk','index','Sportsbook','today-soccer'),
(7,1,2,4,1,3,NULL,NULL,NULL,NULL,'Tenis dnes','tenis-dnes','index','Sportsbook','today-tennis'),
(7,2,2,4,1,3,NULL,NULL,NULL,NULL,'Tennis today','tennis-today','index','Sportsbook','today-tennis'),
(7,16,2,4,1,3,NULL,NULL,NULL,NULL,'Tenis dnes','tenis-dnes-sk','index','Sportsbook','today-tennis'),
(8,1,0,2,1,3,'','','','','Menu','menu','index','Menu','index'),
(8,2,0,2,1,3,'','','','','','menu-en','index','Menu','index'),
(8,16,0,2,1,3,'','','','','','menu-sk','index','Menu','index'),
(9,1,2,5,1,3,NULL,NULL,NULL,NULL,'Detail','detail','index','Sportsbook','detail'),
(9,2,2,5,1,3,NULL,NULL,NULL,NULL,'Detail','detail','index','Sportsbook','detail'),
(9,16,2,5,1,3,NULL,NULL,NULL,NULL,'Detail','detail-sk','index','Sportsbook','detail'),
(10,1,1,3,1,3,'','','','','AJAX Tiket','ajax','ticket','ajax','ticket'),
(10,2,1,3,1,3,'','','','','AJAX Tiket','ajax','ticket','ajax','ticket'),
(10,16,1,3,1,3,'','','','','AJAX Tiket','ajax','ticket','ajax','ticket'),
(11,1,1,4,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 1','ajax','confirm1','ajax','confirm1'),
(11,2,1,4,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 1','ajax','confirm1','ajax','confirm1'),
(11,16,1,4,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 1','ajax','confirm1','ajax','confirm1'),
(12,1,1,5,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 2','ajax','confirm2','ajax','confirm2'),
(12,2,1,5,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 2','ajax','confirm2','ajax','confirm2'),
(12,16,1,5,1,3,NULL,NULL,NULL,NULL,'AJAX confirm step 2','ajax','confirm2','ajax','confirm2'),
(13,1,3,1,1,3,'','','','','Live zápas','live-sazky','zapas','Livebetting','match'),
(13,2,3,1,1,3,'','','','','Live zápas','live-betting','match','Livebetting','match'),
(13,16,3,1,1,3,'','','','','Live zápas','live-sazky-sk','zapas-sk','Livebetting','match'),
(14,1,1,6,1,3,NULL,NULL,NULL,NULL,'AJAX live online','ajax','liveonline','ajax','liveonline'),
(14,2,1,6,1,3,NULL,NULL,NULL,NULL,'AJAX live online','ajax','liveonline','ajax','liveonline'),
(14,16,1,6,1,3,NULL,NULL,NULL,NULL,'AJAX live online','ajax','liveonline','ajax','liveonline'),
(15,1,0,3,1,3,NULL,NULL,NULL,NULL,'Webservice','Webservice','index','Webservice','index'),
(15,2,0,3,1,3,NULL,NULL,NULL,NULL,'Webservice','Webservice','index','Webservice','index'),
(15,16,0,3,1,3,NULL,NULL,NULL,NULL,'Webservice','Webservice','index','Webservice','index'),
(16,1,0,4,1,3,'','','','','My account','muj-ucet','index','Myaccount','index'),
(16,2,0,4,1,3,'','','','','My account','my-detail','index','Myaccount','index'),
(16,16,0,4,1,3,'','','','','My account','muj-ucet-sk','index','Myaccount','index'),
(17,1,16,1,1,3,NULL,NULL,NULL,NULL,'Ticket','muj-ucet','tiket','Myaccount','ticket'),
(17,2,16,1,1,3,NULL,NULL,NULL,NULL,'Ticket','my-detail','cupon','Myaccount','ticket'),
(17,16,16,1,1,3,NULL,NULL,NULL,NULL,'Ticket','muj-ucet-sk','tiket','Myaccount','ticket'),
(18,1,17,1,1,3,'','','','','Tiket detail','muj-ucet','tiket-detail','Myaccount','slip'),
(18,2,17,1,1,3,'','','','','Tiket detail','my-detail','slip-detail','Myaccount','slip'),
(18,16,17,1,1,3,'','','','','Tiket detail','muj-ucet-sk','tiket-detail','Myaccount','slip'),
(19,1,0,5,1,3,NULL,NULL,NULL,NULL,'Technicka udrzba stranek','technicka-udrzba','index','Maintenance','index'),
(19,2,0,5,1,3,NULL,NULL,NULL,NULL,'Technicka udrzba stranek','maintenance','index','Maintenance','index'),
(19,16,0,5,1,3,NULL,NULL,NULL,NULL,'Technicka udrzba stranek','technicka-udrzba-sk','index','Maintenance','index'),
(20,1,0,6,1,3,'','','','','Herní řád','herni-rad','index','rules','index'),
(20,2,0,6,1,3,'','','','','Rules','playing-rules','index','rules','index'),
(20,16,0,6,1,3,'','','','','Herní řád','herni-rad-sk','index','rules','index'),
(21,1,0,7,1,3,NULL,NULL,NULL,NULL,'Vklad a Vyber','vklad-vyber','index','deposit','index'),
(21,2,0,7,1,3,NULL,NULL,NULL,NULL,'Vklad a Vyber','deposit-withdraw','index','deposit','index'),
(21,16,0,7,1,3,NULL,NULL,NULL,NULL,'Vklad a Vyber','vklad-vyber-sk','index','deposit','index'),
(22,1,0,8,1,3,NULL,NULL,NULL,NULL,'Jak sazet','jak-sazet','index','static-page','how-to-place-bet'),
(22,2,0,8,1,3,NULL,NULL,NULL,NULL,'Jak sazet','how-place-bet','index','static-page','how-to-place-bet'),
(22,16,0,8,1,3,NULL,NULL,NULL,NULL,'Jak sazet','jak-sazet-sk','index','static-page','how-to-place-bet'),
(23,1,0,9,1,3,NULL,NULL,NULL,NULL,'Pobočky','pobocky','index','branch','index'),
(23,2,0,9,1,3,NULL,NULL,NULL,NULL,'Pobočky','branch','index','branch','index'),
(23,16,0,9,1,3,NULL,NULL,NULL,NULL,'Pobočky','pobocky-sk','index','branch','index'),
(24,1,0,10,1,3,NULL,NULL,NULL,NULL,'Registrace','registrace','index','Registration','index'),
(24,2,0,10,1,3,NULL,NULL,NULL,NULL,'Registrace','registration','index','Registration','index'),
(24,16,0,10,1,3,NULL,NULL,NULL,NULL,'Registrace','registrace-sk','index','Registration','index'),
(25,1,0,11,1,3,NULL,NULL,NULL,NULL,'Zapomenute heslo','zapomenute-heslo','index','forgottenpassword','index'),
(25,2,0,11,1,3,NULL,NULL,NULL,NULL,'Zapomenute heslo','forgotten-password','index','forgottenpassword','index'),
(25,16,0,11,1,3,NULL,NULL,NULL,NULL,'Zapomenute heslo','zapomenute-heslo-sk','index','forgottenpassword','index'),
(26,1,0,12,1,3,NULL,NULL,NULL,NULL,'Bonusy','bonusy','index','bonus','index'),
(26,2,0,12,1,3,NULL,NULL,NULL,NULL,'Bonusy','bonuses','index','bonus','index'),
(26,16,0,12,1,3,NULL,NULL,NULL,NULL,'Bonusy','bonusy-sk','index','bonus','index'),
(27,1,0,13,1,3,NULL,NULL,NULL,NULL,'Hraci tydne','nejlepsi-hraci','index','bestplayer','index'),
(27,2,0,13,1,3,NULL,NULL,NULL,NULL,'Hraci tydne','best-player','index','bestplayer','index'),
(27,16,0,13,1,3,NULL,NULL,NULL,NULL,'Hraci tydne','nejlepsi-hraci','index','bestplayer','index'),
(28,1,0,14,1,3,NULL,NULL,NULL,NULL,'Novinky','novinky','index','news','index'),
(28,2,0,14,1,3,NULL,NULL,NULL,NULL,'Novinky','news','index','news','index'),
(28,16,0,14,1,3,NULL,NULL,NULL,NULL,'Novinky','novinky-sk','index','news','index'),
(29,1,0,15,1,3,NULL,NULL,NULL,NULL,'O společnosti','o-spolecnosti','index','company','index'),
(29,2,0,15,1,3,NULL,NULL,NULL,NULL,'O společnosti','about-company','index','company','index'),
(29,16,0,15,1,3,NULL,NULL,NULL,NULL,'O společnosti','o-spolecnosti-sk','index','company','index'),
(30,1,0,16,1,3,'','','','','Spoluprace','spoluprace','index','cooperation','index'),
(30,2,0,16,1,3,'','','','','Cooperation','cooperation','index','cooperation','index'),
(30,16,0,16,1,3,'','','','','Spoluprace','spoluprace-sk','index','cooperation','index'),
(31,1,0,17,1,3,NULL,NULL,NULL,NULL,'RSS','rss-cs','index','rss','index'),
(31,2,0,17,1,3,NULL,NULL,NULL,NULL,'RSS','rss','index','rss','index'),
(31,16,0,17,1,3,NULL,NULL,NULL,NULL,'RSS','rss-sk','index','rss','index'),
(32,1,16,2,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vklad','muj-ucet','vklad','Myaccount','deposit'),
(32,2,16,2,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vklad','my-detail','deposit','Myaccount','deposit'),
(32,16,16,2,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vklad','muj-ucet-sk','index','Myaccount','vyber'),
(33,1,16,3,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vyber','muj-ucet','vyber','Myaccount','index'),
(33,2,16,3,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vyber','my-detail','withdraw','Myaccount','index'),
(33,16,16,3,1,3,NULL,NULL,NULL,NULL,'Muj ucet - vyber','muj-ucet-sk','vyber','Myaccount','index'),
(34,1,0,18,1,3,NULL,NULL,NULL,NULL,'Klub vernych','klub-vernych','index','loyalityclub','index'),
(34,2,0,18,1,3,NULL,NULL,NULL,NULL,'Klub vernych','loyality-club','index','loyalityclub','index'),
(34,16,0,18,1,3,NULL,NULL,NULL,NULL,'Klub vernych','klub-vernych-sk','index','loyalityclub','index'),
(35,1,16,4,1,3,NULL,NULL,NULL,NULL,'Osobni nastaveni','muj-ucet','osobni','Myaccount','personal'),
(35,2,16,4,1,3,NULL,NULL,NULL,NULL,'Osobni nastaveni','my-detail','personal','Myaccount','personal'),
(35,16,16,4,1,3,NULL,NULL,NULL,NULL,'Osobni nastaveni','muj-ucet-sk','osobni-sk','Myaccount',''),
(36,1,1,7,1,3,NULL,NULL,NULL,NULL,'Odhlasen','odhlasen','index','logoff','index'),
(36,2,1,7,1,3,NULL,NULL,NULL,NULL,'Odhlasen','log-off','index','logoff','index'),
(36,16,1,7,1,3,NULL,NULL,NULL,NULL,'Odhlasen','odhlasen-sk','index','logoff','index'),
(37,1,1,1,1,3,NULL,NULL,NULL,NULL,'Statistiky','statistiky','index','stats','index'),
(37,2,1,1,1,3,NULL,NULL,NULL,NULL,'Statistiky','stats','index','stats','index'),
(37,16,1,1,1,3,NULL,NULL,NULL,NULL,'Statistiky','statistiky-sk','index','stats','index'),
(38,1,1,1,1,3,'','','Výsledky','','Výsledky','vysledky','index','scores','index'),
(38,2,1,1,1,3,'','','','','Výsledky','scires','index','scores','index'),
(38,16,1,1,1,3,'','','','','Výsledky','vysledky-sk','index','scores','index'),
(39,1,38,1,0,3,NULL,NULL,NULL,NULL,'','index','index','index','index'),
(39,2,38,1,0,3,NULL,NULL,NULL,NULL,'','index','index','index','index'),
(39,16,38,1,0,3,NULL,NULL,NULL,NULL,'','index','index','index','index'),
(40,1,1,7,1,1,NULL,NULL,NULL,NULL,'AJAX CAPTCHA','ajax','captcha','ajax','captcha'),
(40,2,1,7,1,1,NULL,NULL,NULL,NULL,'AJAX CAPTCH','ajax','captcha','ajax','captcha'),
(40,16,1,7,1,1,NULL,NULL,NULL,NULL,'AJAX CAPTCHA','ajax','captcha','ajax','captcha'),
(41,1,24,2,1,3,NULL,NULL,NULL,NULL,'','registrace','aktivace','Registration','activate'),
(41,2,24,2,1,3,NULL,NULL,NULL,NULL,'','registration','activate','Registration','activate'),
(41,16,24,2,1,3,NULL,NULL,NULL,NULL,'','registracia','aktivace','Registration','activate'),
(42,1,24,3,1,3,NULL,NULL,NULL,NULL,'','registrace','vklad','Registration','deposit'),
(42,2,24,3,1,3,NULL,NULL,NULL,NULL,'','registration','deposit','Registration','deposit'),
(42,16,24,3,1,3,NULL,NULL,NULL,NULL,'','registracia','vklad','Registration','deposit'),
(43,1,24,4,1,3,NULL,NULL,NULL,NULL,'','registration','hra','Registration','play'),
(43,2,24,4,1,3,NULL,NULL,NULL,NULL,'','registracia','play','Registration','play'),
(43,16,24,4,1,3,NULL,NULL,NULL,NULL,'','registration','hra','Registration','play'),
(44,1,1,1,1,2,NULL,NULL,NULL,NULL,'','superhome','index','superhome','index'),
(44,2,1,1,1,2,NULL,NULL,NULL,NULL,'','superhome','index','superhome','index'),
(44,16,1,1,1,2,NULL,NULL,NULL,NULL,'','superhome','index','superhome','index'),
(45,1,0,1,1,3,NULL,NULL,NULL,NULL,'Vyhledávání','vyhledavani','index','Search','index'),
(45,2,0,1,1,3,NULL,NULL,NULL,NULL,'Search','search','index','Search','index'),
(45,16,0,1,1,3,NULL,NULL,NULL,NULL,'Hľadanie','hladanie','index','Search','index'),
(46,1,0,1,1,3,NULL,NULL,'404 - Stránka nenalezena',NULL,'Stránka nenalezena','404','index','static-page','not-found'),
(46,2,0,1,1,3,NULL,NULL,'404 - Not found',NULL,'Page not found','404','index','static-page','not-found'),
(46,16,0,1,1,3,NULL,NULL,'404 - Stránka nenalezena',NULL,'Stránka nenalezena','404','index','static-page','not-found'),
(47,1,0,1,1,3,NULL,NULL,'503 - Stránku nelze zobrazit',NULL,'503 - Stránku nelze zobrazit','503','index','static-page','forbidden'),
(47,2,0,1,1,3,NULL,NULL,'503 - Forbidden',NULL,'Page forbidden','503','index','static-page','forbidden'),
(47,16,0,1,1,3,NULL,NULL,'503 - Stránku nelze zobrazit',NULL,'Stránku nelze zobrazit','503','index','static-page','forbidden'),
(48,1,0,1,1,3,NULL,NULL,'Podrobnosti maxikombinátoru',NULL,'Podrobnosti maxikombinátoru','maxicombinator','detail','ajax','maxicombinator-detail');
/*!40000 ALTER TABLE `controller_convert` ENABLE KEYS */;
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
