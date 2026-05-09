ALTER TABLE `vic_main`.`udalost` ADD `pozice_offergen` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `pozice`;
ALTER TABLE `vic_main`.`udalost` ADD INDEX `i__udalost__pozice_offergen` ( `pozice_offergen` );
ALTER TABLE `vic_main`.`udalost` ADD INDEX `i__udalost__pozice` ( `pozice` );


START TRANSACTION;

UPDATE `vic_main`.`udalost` SET
`pozice_offergen` = `pozice`;

COMMIT;

