UPDATE `vic_admin`.`branch_type` SET `name` = 'Internet' WHERE `branch_type`.`id` =1;
set foreign_key_checks=0;
DELETE FROM `vic_admin`.`branch` WHERE `branch`.`id` = 1;
INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`) VALUES
(1, 1, 1, 'INTERNET', 'internet', 'N/A INTERNET', 'N/A INTERNET', 'N/A INTERNET', 'N/A INTERNET', '000', 'vesely@compbet.com', 1, 'N/A INTERNET', 'N/A INTERNET', 'N/A INTERNET', 'N/A INTERNET', 'vesely@compbet.com');
set foreign_key_checks=1;

ALTER TABLE `vic_main`.`uzivatel` ADD `anonymous` BOOLEAN NOT NULL DEFAULT '0';
