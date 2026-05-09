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
-- Dumping data for table `oblast`
--

LOCK TABLES `oblast` WRITE;
/*!40000 ALTER TABLE `oblast` DISABLE KEYS */;
INSERT INTO `oblast` VALUES (245,'zeme_US',0,'usa.gif',2,'US'),
(246,'zeme_GB',0,'eng.gif',1,'GB'),
(247,'zeme_CZ',0,'cze.gif',3,'CZ'),
(248,'zeme_DE',0,'deu.gif',4,'DE'),
(249,'zeme_PL',0,'pol.gif',8,'PL'),
(250,'zeme_FR',0,'fra.gif',9,'FR'),
(256,'zeme_AL',0,'alb.gif',15,'AL'),
(257,'zeme_AM',0,'arm.gif',16,'AM'),
(261,'zeme_AR',0,'arg.gif',20,'AR'),
(263,'zeme_AT',0,'aus.gif',22,'AT'),
(264,'zeme_AU',0,'austral.gif',23,'AU'),
(267,'zeme_BA',0,'bosn.gif',26,'BA'),
(269,'zeme_BD',0,'bangl.gif',28,'BD'),
(270,'zeme_BE',0,'bel.gif',29,'BE'),
(272,'zeme_BG',0,'bul.gif',31,'BG'),
(278,'zeme_BO',0,'boliv.gif',37,'BO'),
(279,'zeme_BR',0,'bra.gif',38,'BR'),
(284,'zeme_BY',0,'bela.gif',43,'BY'),
(286,'zeme_CA',0,'can.gif',45,'CA'),
(291,'zeme_CH',0,'sui.gif',50,'CH'),
(294,'zeme_CL',0,'chil.gif',53,'CL'),
(296,'zeme_CN',0,'chin.gif',55,'CN'),
(297,'zeme_CO',0,'colo.gif',56,'CO'),
(302,'zeme_CY',0,'kyp.gif',61,'CY'),
(304,'zeme_DK',0,'den.gif',63,'DK'),
(308,'zeme_EC',0,'ecu.gif',67,'EC'),
(309,'zeme_EE',0,'est.gif',68,'EE'),
(310,'zeme_EG',0,'egy.gif',69,'EG'),
(313,'zeme_ES',0,'spa.gif',72,'ES'),
(315,'zeme_FI',0,'fin.gif',74,'FI'),
(331,'zeme_GR',0,'gre.gif',90,'GR'),
(340,'zeme_HR',0,'cro.gif',99,'HR'),
(342,'zeme_HU',0,'hun.gif',101,'HU'),
(344,'zeme_IE',0,'ire.gif',103,'IE'),
(351,'zeme_IT',0,'ita.gif',110,'IT'),
(354,'zeme_JP',0,'jap.gif',113,'JP'),
(373,'zeme_LT',0,'lith.gif',132,'LT'),
(374,'zeme_LU',0,'lux.gif',133,'LU'),
(375,'zeme_LV',0,'lotys.gif',134,'LV'),
(378,'zeme_MC',0,'mon.gif',137,'MC'),
(395,'zeme_MX',0,'mex.gif',154,'MX'),
(396,'zeme_MY',0,'mala.gif',155,'MY'),
(404,'zeme_NL',0,'ned.gif',163,'NL'),
(405,'zeme_NO',0,'nor.gif',164,'NO'),
(412,'zeme_PE',0,'peru.gif',171,'PE'),
(420,'zeme_PT',0,'por.gif',179,'PT'),
(425,'zeme_RO',0,'rom.gif',184,'RO'),
(426,'zeme_RU',0,'rus.gif',185,'RU'),
(432,'zeme_SE',0,'swe.gif',191,'SE'),
(435,'zeme_SI',0,'slovi.gif',194,'SI'),
(437,'zeme_SK',0,'slo.gif',196,'SK'),
(439,'zeme_SM',0,'sanmar.gif',198,'SM'),
(451,'zeme_TH',0,'thai.gif',210,'TH'),
(455,'zeme_TN',0,'tun.gif',214,'TN'),
(458,'zeme_TR',0,'tur.gif',217,'TR'),
(463,'zeme_UK',0,'ukra.gif',222,'UA'),
(468,'zeme_VA',0,'vat.gif',227,'VA'),
(470,'zeme_VE',0,'ven.gif',229,'VE'),
(479,'zeme_SB',0,'srb.gif',238,'SB'),
(484,'zeme_XX',0,'',243,NULL),
(485,'zeme_IL',0,'izra.gif',244,'IL'),
(486,'zeme_NZ',0,'zeal.gif',245,'NZ'),
(487,'zeme_KR',0,'kor.gif',246,'KR'),
(488,'wta',0,'wta.gif',250,NULL),
(489,'atp',0,'atp.gif',248,NULL),
(490,'challenger',0,'cha.gif',249,NULL),
(491,'davis_cup',0,'dav.gif',247,NULL),
(492,'bo-int',0,'int.gif',251,NULL),
(493,'itf',0,'itf.gif',252,NULL),
(494,'hopman_cup',0,'hop.gif',253,NULL),
(495,'f-int-cfm',0,'prklut.gif',254,NULL),
(496,'f-int-fm',0,'prut.gif',255,NULL),
(497,'lh_icm',0,'intklut.gif',256,NULL),
(498,'pga_tour',0,'pga.gif',257,NULL),
(499,'epga_tour',0,'epga.gif',258,NULL),
(500,'ryder_cup',0,'ryd.gif',259,NULL),
(501,'us_masters',0,'mast.gif',260,NULL),
(502,'european_order',0,'epom.gif',261,NULL),
(503,'m_rally',0,'wrc.gif',262,NULL),
(504,'m_bikes',0,'mgp.gif',263,NULL),
(505,'m_nascar',0,'nasc.gif',264,NULL),
(506,'m_wtcc',0,'wtcc.gif',265,NULL),
(507,'rugby_league',0,'rfl.gif',266,NULL),
(508,'rugby_union',0,'rgbunio.gif',267,NULL),
(509,'game_33_4',0,'mus.gif',268,NULL),
(510,'game_g_other',0,'oth.gif',269,NULL),
(511,'alpske_lyzovani',0,'alp.gif',270,NULL),
(512,'severske_lyzovani',0,'sev.gif',271,NULL),
(513,'zeme_CR',0,'kost.gif',272,'CR'),
(514,'zeme_IS',0,'isl.gif',273,'IS'),
(515,'zeme_UY',0,'uru.gif',274,'UY'),
(516,'Zabava',0,'fun.gif',275,NULL),
(517,'oscar',0,'osc.gif',276,NULL),
(518,'europe',0,'eur.gif',277,NULL),
(519,'zeme_QA',0,'kat.gif',278,'QA'),
(520,'zeme_MOC',0,'monte.gif',279,NULL),
(521,'zeme_CT',0,'katal.gif',280,NULL),
(522,'Biatlon',0,'bia.gif',281,NULL),
(523,'skoky',0,'sko.gif',282,NULL),
(524,'m_motogp',0,'vmgp.gif',283,NULL),
(525,'m_250cc',0,'250cc.gif',284,NULL),
(526,'m_125cc',0,'125cc.gif',285,NULL),
(527,'assen',0,'ass.gif',286,NULL),
(528,'f1-vpk-cv',0,'pohkon.gif',287,NULL),
(529,'zeme_BH',0,'bah.gif',288,'BH'),
(530,'wmf1',0,'wmf1.gif',289,NULL),
(531,'wmmrc',0,'wmmrc.gif',290,NULL),
(532,'zeme_SIR',0,'northirl.gif',291,NULL),
(533,'zeme_SCO',0,'sco.gif',292,NULL),
(534,'zeme_WA',0,'wales.gif',293,NULL),
(536,'zeme_EN',0,'eng.gif',294,NULL),
(537,'fed_cup',0,'fed.gif',295,NULL),
(538,'zeme_NAM',0,'nrtham.gif',296,NULL),
(539,'Politika',0,'polit.gif',297,NULL),
(540,'Olympic_games_Soc',0,'soccer.gif',315,NULL),
(541,'Olympic_games_Hoc',0,'',323,NULL),
(542,'Olympic_games_Bask',0,'basketball.gif',303,NULL),
(543,'Olympic_games_Bas',0,'baseball.gif',302,NULL),
(544,'Olympic_games_Arc',NULL,'',299,NULL),
(545,'Olympic_games_Ath',0,'atletika.gif',300,NULL),
(546,'Olympic_games_Bad',0,'badminton.gif',301,NULL),
(547,'Olympic_games_BeaV',0,'beachvolley.gif',304,NULL),
(548,'Olympic_games_Box',0,'box.gif',305,NULL),
(549,'Olympic_games_Cyc',0,'cyklo.gif',306,NULL),
(550,'Olympic_games_Div',NULL,'',307,NULL),
(551,'Olympic_games_Eque',NULL,'',308,NULL),
(552,'Olympic_games_Fen',NULL,'',309,NULL),
(553,'Olympic_games_Gym',NULL,'',310,NULL),
(554,'Olympic_games_Han',0,'handball.gif',312,NULL),
(555,'Olympic_games_pHoc',0,'bandyhockey.gif',312,NULL),
(556,'Olympic_games_Jud',NULL,'',313,NULL),
(557,'Olympic_games_Row',NULL,'',314,NULL),
(558,'Olympic_games_Sof',0,'softball.gif',316,NULL),
(559,'Olympic_games_Spe',NULL,'',317,NULL),
(560,'Olympic_games_Swi',0,'swiming.gif',318,NULL),
(561,'Olympic_games_Ten',0,'tennis.gif',319,NULL),
(562,'Olympic_games_Vol',0,'volleyball.gif',320,NULL),
(563,'Olympic_games_Wat',0,'waterpolo.gif',321,NULL),
(564,'Olympic_games_SpeSka',NULL,'',322,NULL),
(565,'Olympic_games_Bia',NULL,'',324,NULL),
(566,'Olympic_games_Cur',NULL,'',325,NULL),
(567,'Olympic_games_Sno',NULL,'',326,NULL),
(568,'Olympic_games_Spec',0,'inte.gif',298,NULL),
(569,'Olympic_games_TT',0,'tabletenis.gif',327,NULL),
(570,'Olympic_games_Triat',0,'triat.gif',328,NULL),
(571,'zeme_SG',0,'sngpr.gif',329,'SG'),
(572,'zema_SA',0,'sa.gif',330,'SA'),
(573,'zeme_FO',0,'faroe_islands.gif',331,'FO'),
(575,'zeme_PY',0,'flag_py_1.gif',332,'PY'),
(576,'zeme_MNO',0,'flag_montenegro.gif',333,'MNO');
/*!40000 ALTER TABLE `oblast` ENABLE KEYS */;
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
