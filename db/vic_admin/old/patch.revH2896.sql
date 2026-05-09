START TRANSACTION;

ALTER TABLE `parameter` CHANGE `value` `value` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

commit;
