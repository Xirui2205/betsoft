ALTER TABLE `vic_admin`.`log` ADD `bet_id` INT( 10 ) NULL AFTER `user_id` ,
ADD `ticket_id` INT( 10 ) NULL AFTER `bet_id` ,
ADD `coupon_id` INT( 10 ) NULL AFTER `ticket_id` ;
