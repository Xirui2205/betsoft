INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1579', 1, 'Update and change table for blocking accounts by ip - (UKO120898)');
 
ALTER TABLE `vic_main`.`uzivatel_block` ADD INDEX `i__uzivatel_block__user_id` ( `user_id` );
ALTER TABLE `vic_main`.`uzivatel_block` ADD INDEX `i__uzivatel_block__block_ip` ( `block_ip` );

ALTER TABLE `uzivatel_block` ADD FOREIGN KEY ( `user_id` ) REFERENCES `vic_main`.`uzivatel` (`user_id` 
) ON DELETE RESTRICT ON UPDATE RESTRICT ;
