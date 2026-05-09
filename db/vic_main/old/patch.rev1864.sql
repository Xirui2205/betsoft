ALTER TABLE `vic_main`.`ticket` ADD `admin_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL,
 DROP `cupon_id`,
 ADD `coupon_id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `vic_main`.`cupon_data` RENAME `coupon_data`;
ALTER TABLE `vic_main`.`coupon_data` CHANGE `user_id` `user_id` INT( 10 ) UNSIGNED NOT NULL,
 ADD `admin_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT 0 AFTER `user_id`,
 DROP PRIMARY KEY , ADD PRIMARY KEY ( `user_id` , `admin_id` ),
 ADD `coupon_id` INT( 10 ) UNSIGNED NOT NULL AFTER `admin_id` ,
 ADD CONSTRAINT `u__coupon_data__coupon_id` UNIQUE (`coupon_id`);
UPDATE `vic_main`.`ticket` SET `coupon_id`=`ticket_id`;
ALTER TABLE `vic_main`.`ticket` ADD CONSTRAINT `u__ticket__coupon_id` UNIQUE (`coupon_id`);
CREATE TABLE `vic_main`.`coupon_sequence` (
`coupon_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE=MyIsam;
INSERT INTO `vic_main`.`coupon_sequence` SELECT MAX(`coupon_id`) FROM `vic_main`.`ticket`;
DELETE FROM `vic_main`.`coupon_sequence`;

SET @lastCouponId = (SELECT MAX(`coupon_id`) FROM `vic_main`.`ticket`);
DELETE FROM `vic_main`.`coupon_data` WHERE `user_id`=4 AND `admin_id`=0;
INSERT INTO `vic_main`.`coupon_data`(`user_id`,`admin_id`,`coupon_id`) VALUES (4, 0, @lastCouponId);
DELETE FROM `vic_main`.`coupon_data` WHERE `coupon_id`=@lastCouponId;
DELETE FROM `vic_session`.`session`;

SET foreign_key_checks=0;
ALTER TABLE `vic_main`.`jazyky` ADD `mena_id` SMALLINT(5) UNSIGNED NOT NULL,
 ADD CONSTRAINT `fk__jazyky__lang_id` FOREIGN KEY (`mena_id`) REFERENCES `vic_main`.`mena` (`mena_id`);
UPDATE `vic_main`.`jazyky` SET `mena_id`=8 WHERE `lang_id`=1;
UPDATE `vic_main`.`jazyky` SET `mena_id`=2 WHERE `lang_id`=2;
UPDATE `vic_main`.`jazyky` SET `mena_id`=2 WHERE `lang_id`=16;
SET foreign_key_checks=1;

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`) VALUES
(59, 1, 1, 6, 1, 3, NULL, NULL, NULL, NULL, 'AJAX confirm step 3', 'ajax', 'confirm3', 'ajax', 'confirm3'),
(59, 2, 1, 6, 1, 3, NULL, NULL, NULL, NULL, 'AJAX confirm step 3', 'ajax', 'confirm3', 'ajax', 'confirm3'),
(59, 16, 1, 6, 1, 3, NULL, NULL, NULL, NULL, 'AJAX confirm step 3', 'ajax', 'confirm3', 'ajax', 'confirm3');

ALTER TABLE `vic_main`.`coupon_data` CHANGE `amount` `modified` TEXT NULL DEFAULT NULL COLLATE utf8_general_ci;
ALTER TABLE `vic_main`.`mena` ADD `smallest_cash` DECIMAL(11,2) NOT NULL COMMENT 'eg. 100 for 1 cent as smallest cash';
UPDATE `vic_main`.`mena` SET `smallest_cash` = '100' WHERE `mena`.`mena_id` =2;
UPDATE `vic_main`.`mena` SET `smallest_cash` = '1' WHERE `mena`.`mena_id` =8;

ALTER TABLE `vic_main`.`ticket` ADD `point_type_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL,
ADD CONSTRAINT `fk__ticket__point_type_id` FOREIGN KEY (`point_type_id`) REFERENCES `vic_main`.`point_type`(`id`);
