ALTER TABLE `vic_main`.`uzivatel` ADD `created_by_admin_id` INT( 10 ) UNSIGNED NOT NULL ,
ADD INDEX ( `created_by_admin_id` ) ;

ALTER TABLE `vic_main`.`uzivatel` ADD `allowed_by_admin_id` INT( 10 ) UNSIGNED NOT NULL ,
ADD INDEX ( `allowed_by_admin_id` ) ;
