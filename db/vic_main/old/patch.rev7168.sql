ALTER TABLE `vic_main`.`udalost` DROP INDEX `i__udalost__pozice_offergen` ,
ADD UNIQUE `u__udalost__pozice_offergen` ( `pozice_offergen` );

ALTER TABLE `vic_main`.`udalost` DROP INDEX `i__udalost__pozice` ,
ADD UNIQUE `u__udalost__pozice` ( `pozice` );

