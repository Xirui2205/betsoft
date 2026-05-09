ALTER TABLE `vic_main`.`ticket`
 ADD COLUMN `castka_cash` DECIMAL (10,2) UNSIGNED NOT NULL DEFAULT 0.0 COMMENT 'castka zaokrouhlena na platidlo' AFTER `castka`,
 DROP INDEX `u__ticket__coupon_id`;
