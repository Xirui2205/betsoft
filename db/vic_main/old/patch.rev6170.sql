ALTER TABLE `vic_main`.`typ` ADD `order_type` SET('rate_asc') NULL DEFAULT NULL;

UPDATE `vic_main`.`typ` SET `order_type`='rate_asc' WHERE `typ_id` IN (32,36,37,38,39,40,41,42,43,44);
