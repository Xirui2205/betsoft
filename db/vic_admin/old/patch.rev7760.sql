START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7760', NULL, 'Novy vernostni program tiket za body. (mantis:566)');


UPDATE `vic_admin`.`parameter` SET
`value` = '0'
WHERE `name` = 'campaign.CreatePointTicket.minAmount';

UPDATE `vic_admin`.`parameter` SET
`value` = '6'
WHERE `name` = 'campaign.CreatePointTicket.minCount';


COMMIT;
