START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1395', 1, 'New column score_note for bets');
 
ALTER TABLE `sazky` ADD `score_note` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;

 
COMMIT;
