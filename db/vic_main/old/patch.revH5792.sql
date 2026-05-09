START TRANSACTION;
ALTER TABLE `coupon_saved_free_alias` ENGINE=InnoDB;
RENAME TABLE `coupon_saved_free_alias` TO `coupon_saved_alias`;

ALTER TABLE  `coupon_saved_alias` ADD  `saved_coupon_id` INT UNSIGNED NULL ,
ADD INDEX (  `saved_coupon_id` ) ;
ALTER TABLE  `coupon_saved` CHANGE  `platny_do`  `platny_do` DATETIME NOT NULL ;
ALTER TABLE  `coupon_saved` ADD INDEX (  `platny_do` ) ;

ALTER TABLE  `coupon_saved_alias` ADD FOREIGN KEY (  `saved_coupon_id` ) REFERENCES  `vic_main`.`coupon_saved` (
`saved_coupon_id`
) ON DELETE SET NULL ON UPDATE RESTRICT;
COMMIT;