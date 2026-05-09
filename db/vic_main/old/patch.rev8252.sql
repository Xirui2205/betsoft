START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8252', NULL, 'Fixed order of columns for subtypes 210 and 211 (mantis:768)');

UPDATE vic_main.podtyp_sloupce SET poradi=1 WHERE podtyp_id=210 AND sloupec_id=1828;
UPDATE vic_main.podtyp_sloupce SET poradi=2 WHERE podtyp_id=210 AND sloupec_id=1829;
UPDATE vic_main.podtyp_sloupce SET poradi=3 WHERE podtyp_id=210 AND sloupec_id=1830;
UPDATE vic_main.podtyp_sloupce SET poradi=4 WHERE podtyp_id=210 AND sloupec_id=1831;
UPDATE vic_main.podtyp_sloupce SET poradi=1 WHERE podtyp_id=211 AND sloupec_id=1832;
UPDATE vic_main.podtyp_sloupce SET poradi=2 WHERE podtyp_id=211 AND sloupec_id=1833;
UPDATE vic_main.podtyp_sloupce SET poradi=3 WHERE podtyp_id=211 AND sloupec_id=1834;
UPDATE vic_main.podtyp_sloupce SET poradi=4 WHERE podtyp_id=211 AND sloupec_id=1835;
UPDATE vic_main.podtyp_sloupce SET poradi=5 WHERE podtyp_id=211 AND sloupec_id=1836;
UPDATE vic_main.podtyp_sloupce SET poradi=6 WHERE podtyp_id=211 AND sloupec_id=1837;

COMMIT;
