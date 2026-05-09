ALTER TABLE `finacni_transakce` ADD `ticket_id` INT UNSIGNED NULL ,
ADD INDEX ( `ticket_id` );

ALTER TABLE `finacni_transakce` CHANGE `ticket_id` `ticket_id` BIGINT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `finacni_transakce` ADD FOREIGN KEY ( `ticket_id` ) REFERENCES `vic_main`.`ticket` (
`ticket_id`
);

