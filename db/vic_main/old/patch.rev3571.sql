CREATE TABLE `vic_main`.`match_live` (
`id` INT UNSIGNED NOT NULL ,
`status` ENUM( 'NOT_STARTED,BEGIN', 'END', '1_HALF', '2_HALF', '1_THIRD', '2_THIRD', '3_THIRD', '1_Q', '2_Q', '3_Q', '4_Q', 'OVERTIME', 'PAUSE', 'STOP', '1_SET', '2_SET', '3_SET', '4_SET', '5_SET', 'LIVE_WARMUP', 'LIVE_UNFINISHED', 'LIVE_PENALTY', 'LIVE_CANCELED' ) NOT NULL ,
`start` DATETIME NOT NULL ,
`sport` VARCHAR( 128 ) NOT NULL ,
`league` VARCHAR( 128 ) NOT NULL ,
`home_team` VARCHAR( 128 ) NOT NULL ,
`away_team` VARCHAR( 128 ) NOT NULL ,
`special_id` INT UNSIGNED NULL ,
`minute` INT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB DEFAULT CHARSET 'utf8' COLLATE 'utf8_general_ci';
