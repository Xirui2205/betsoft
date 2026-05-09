ALTER TABLE `vic_main`.`ticket_live` CHANGE `status` `status` ENUM( 'new', 'wining', 'paid', 'canceled', 'resettled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 
