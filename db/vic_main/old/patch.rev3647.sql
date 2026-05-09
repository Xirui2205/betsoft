ALTER TABLE vic_main.`ticket_live` CHANGE `status` `status` ENUM( 'new', 'paid', 'canceled', 'resettled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `type` `type` ENUM( 'simple', 'kombi' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE vic_main.`ticket_live_bet` CHANGE `players` `players` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `tip` `tip` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `sport` `sport` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `market` `market` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `result` `result` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
