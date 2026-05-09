ALTER TABLE `live_1003` ADD `court` ENUM( 'clay', 'grass', 'hardcourt' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `first_service` ;
ALTER TABLE `live_1003` ADD `tiebreak` TINYINT( 1 ) NULL AFTER `court` ;
