CREATE TABLE `vic_main`.`translate_missing` (
`translate_key` VARCHAR( 32 ) NOT NULL ,
`lang_id` INT( 10 ) NOT NULL ,
`controller` VARCHAR( 100 ) NOT NULL ,
`action` VARCHAR( 100 ) NOT NULL ,
`status` ENUM( 'empty', 'missing' ) NOT NULL ,
PRIMARY KEY ( `translate_key` , `lang_id` , `controller` , `action` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`translate_pages` (
`translate_key` VARCHAR( 32 ) NOT NULL ,
`controller` VARCHAR( 100 ) NOT NULL ,
`action` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `translate_key` , `controller` , `action` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
