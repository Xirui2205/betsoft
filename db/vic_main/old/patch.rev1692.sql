ALTER TABLE `ticket` ADD `collection_time` DATETIME NULL;

ALTER TABLE `ticket` ADD `collection_branch_id` INT(10) UNSIGNED NULL ,
ADD INDEX ( `collection_branch_id` );
