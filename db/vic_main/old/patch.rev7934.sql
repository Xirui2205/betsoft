INSERT INTO vic_main.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (7934, 1, 'pridani sekce sazky->nabery');

ALTER TABLE `vic_main`.`bet_column` ADD `absolute_stake` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
ADD `bet_count` INT NOT NULL DEFAULT '0';

ALTER TABLE `vic_main`.`bet_column` ADD `bet_count` INTEGER NOT NULL DEFAULT '0',
ADD `bet_count` INT NOT NULL DEFAULT '0';

ALTER TABLE `vic_main`.`sazky` ADD INDEX `i__sazky__status` ( `status` ) ;
