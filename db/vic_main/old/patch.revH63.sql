INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H63', 1, 'Columns validation features (mantis:793)');

ALTER TABLE `vic_main`.`podtyp_sloupce` ADD COLUMN `max_event_count` SMALLINT(5) NULL DEFAULT NULL,
 ADD COLUMN `max_event_count_param` SMALLINT(5) NULL DEFAULT NULL;

START TRANSACTION;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=154;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=155;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=156;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=157;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=8, max_event_count_param=8 WHERE sloupec_id=158;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=10, max_event_count_param=10 WHERE sloupec_id=159;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=15, max_event_count_param=15 WHERE sloupec_id=160;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=20, max_event_count_param=20 WHERE sloupec_id=161;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1418;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1419;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=1420;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=8, max_event_count_param=8 WHERE sloupec_id=1421;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1422;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1423;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=1424;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1596;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=1597;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=4, max_event_count_param=4 WHERE sloupec_id=1598;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=8, max_event_count_param=8 WHERE sloupec_id=1599;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=16, max_event_count_param=16 WHERE sloupec_id=1600;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1615;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=1616;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1617;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=4, max_event_count_param=4 WHERE sloupec_id=1618;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=8, max_event_count_param=8 WHERE sloupec_id=1619;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1819;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1820;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=1821;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=10, max_event_count_param=10 WHERE sloupec_id=1822;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1823;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1824;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=1825;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=10, max_event_count_param=10 WHERE sloupec_id=1826;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=15, max_event_count_param=15 WHERE sloupec_id=1827;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1847;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=1848;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=1849;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=4, max_event_count_param=4 WHERE sloupec_id=1850;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=1851;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=1852;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=2010;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=2011;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=5, max_event_count_param=5 WHERE sloupec_id=2012;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=2013;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=2, max_event_count_param=2 WHERE sloupec_id=2014;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=2015;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=4, max_event_count_param=4 WHERE sloupec_id=2016;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=5, max_event_count_param=5 WHERE sloupec_id=2017;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=6, max_event_count_param=6 WHERE sloupec_id=2018;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=1, max_event_count_param=1 WHERE sloupec_id=2019;
UPDATE `vic_main`.`podtyp_sloupce` SET max_event_count=3, max_event_count_param=3 WHERE sloupec_id=2020;

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'ticket_er_31', NULL, 'Překročen počet sázek na událost', 0),
 (2, 'ticket_er_31', NULL, 'Too much bets for event', 0),
 (16, 'ticket_er_31', NULL, 'Překročen počet sázek na událost', 0);

COMMIT;
