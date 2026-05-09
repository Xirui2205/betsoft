ALTER TABLE `vic_main`.`ticket_statistics_cashflow` CHANGE `win_tip_count` `win_tip_count` INT( 10 ) UNSIGNED NOT NULL ,
CHANGE `lost_tip_count` `lost_tip_count` INT( 10 ) UNSIGNED NOT NULL;

ALTER TABLE `vic_main`.`ticket_statistics_payout` CHANGE `win_tip_count` `win_tip_count` INT( 10 ) UNSIGNED NOT NULL ,
CHANGE `lost_tip_count` `lost_tip_count` INT( 10 ) UNSIGNED NOT NULL;

DROP TRIGGER IF EXISTS `vic_main`.`ticket_kurz_update_trg`;
DROP TRIGGER IF EXISTS `vic_main`.`ticket_kurz_insert_trg`;
DROP TRIGGER IF EXISTS `vic_main`.`ticket_kurz_delete_trg`;
