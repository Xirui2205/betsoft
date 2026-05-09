CREATE TABLE `vic_main`.`team` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`betradar_id` INT UNSIGNED NULL ,
`name` VARCHAR( 128 ) NOT NULL ,
`short_name` VARCHAR( 64 ) NOT NULL ,
UNIQUE (
`betradar_id`
)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`bet_handle_range` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`event_id` INT UNSIGNED NOT NULL ,
`typ_id` INT UNSIGNED NOT NULL ,
`from` INT UNSIGNED NOT NULL ,
`to` INT UNSIGNED NOT NULL ,
INDEX ( `event_id` , `typ_id` , `from` , `to` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `bet_handle_range` ADD FOREIGN KEY ( `event_id` ) REFERENCES `vic_main`.`udalost` (
`udalost_id`
);

ALTER TABLE `bet_handle_range` ADD INDEX ( `typ_id` );

ALTER TABLE `bet_handle_range` ADD FOREIGN KEY ( `typ_id` ) REFERENCES `vic_main`.`typ` (
`typ_id`
);

CREATE TABLE `vic_main`.`team_has_bet_handle_range` (
`team_id` INT UNSIGNED NOT NULL ,
`bet_handle_range_id` INT UNSIGNED NOT NULL ,
`number` INT NOT NULL ,
PRIMARY KEY ( `team_id` , `bet_handle_range_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `team_has_bet_handle_range` ADD FOREIGN KEY ( `team_id` ) REFERENCES `vic_main`.`team` (
`id`
);

ALTER TABLE `team_has_bet_handle_range` ADD FOREIGN KEY ( `bet_handle_range_id` ) REFERENCES `vic_main`.`bet_handle_range` (
`id`
);

ALTER TABLE `team` ADD `sport_id` INT UNSIGNED NOT NULL AFTER `betradar_id` ,
ADD INDEX ( `sport_id` );

ALTER TABLE `team` ADD FOREIGN KEY ( `sport_id` ) REFERENCES `vic_main`.`sport` (
`sport_id`
);

