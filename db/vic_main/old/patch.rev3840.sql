ALTER TABLE `vic_main`.`ticket_live_bet` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `vic_main`.`ticket_live_bet` DROP PRIMARY KEY ;
ALTER TABLE `vic_main`.`ticket_live_bet` ADD PRIMARY KEY ( `id` , `ticket_live_id` ) ;
ALTER TABLE `vic_main`.`ticket_live` ADD `id_live` BIGINT NULL AFTER `id` ,
ADD UNIQUE (
`id_live`
);
ALTER TABLE `vic_main`.`ticket_live` CHANGE `id_live` `id_live` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL ;
