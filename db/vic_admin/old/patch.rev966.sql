ALTER TABLE  `branch` DROP FOREIGN KEY  `branch_ibfk_3` ;
ALTER TABLE  `branch` CHANGE  `region_id`  `branch_location_id` INT( 10 ) UNSIGNED NOT NULL;
RENAME TABLE  `vic_admin`.`region` TO  `vic_admin`.`branch_location` ;
ALTER TABLE  `branch` ADD FOREIGN KEY (  `branch_location_id` ) REFERENCES  `vic_admin`.`branch_location` (`id`);
