ALTER TABLE `vic_main`.`promo` CHANGE `text` `text` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `vic_main`.`controller_has_promo` CHANGE `controller_id` `controller_id` INT( 3 ) NOT NULL COMMENT 'If 0, promo is default and shows if there is no other promo set.';
