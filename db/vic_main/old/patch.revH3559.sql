start transaction;

ALTER TABLE `typ` CHANGE `order_type_id` `order_type_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `typ` CHANGE `group_master` `group_master` TINYINT( 1 ) NULL DEFAULT '0';

commit;
