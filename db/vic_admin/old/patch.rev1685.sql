-- vychazi z obsahu vic_admin na serveru (prejato od Martina)
START TRANSACTION;
SET foreign_key_checks=0;
UPDATE `vic_admin`.`branch_type` SET `id`=`id`+1 ORDER BY `id` DESC;
INSERT INTO `vic_admin`.`branch_type`(`id`, `name`) VALUES (1, 'internet');
UPDATE `vic_admin`.`branch` SET `type_id`=`type_id`+1;
UPDATE `vic_admin`.`branch` SET `id`=3 WHERE `id`=2;
UPDATE `vic_admin`.`branch` SET `id`=2 WHERE `id`=1;
INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`, `currency_id`) VALUES
(1, 1, 2, 'Internet', 'heh', 'Server', 'Praha', '10000', 'Internet', '1234567890', 'stastny@compbet.com', 2, 'Filip Veselý', 'Palmovka', '87654321', 'CZ8001019876', 'vesely@compbet.com', 8);
SET foreign_key_checks=1;
COMMIT;
