START TRANSACTION;

ALTER TABLE `affiliate_partner_ticket_user_provision` RENAME `affiliate_partner_user_ticket`;
ALTER TABLE `affiliate_partner_user_ticket` ADD `provision_paid_out` TINYINT( 10 ) NOT NULL DEFAULT '0';

COMMIT;
