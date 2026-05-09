INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7655', 1, 'Index i__ticket__coupon_id');

ALTER TABLE `vic_main`.`ticket` ADD INDEX `i__ticket__coupon_id`(`coupon_id`);
 