START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7617', 1, 'PreferenceRate. (mantis:566)');

UPDATE `vic_admin`.`parameter` SET
`value` = '5'
WHERE `name` = 'campaign.PreferenceRate.minCount';

UPDATE `vic_admin`.`parameter` SET
`value` = '5;50;10;150;15;500',
`name` = 'campaign.PreferenceRate.cost'
WHERE `name` = 'campaign.PreferenceRate.cost5';

UPDATE `vic_admin`.`parameter` SET
`value` = '2000',
`name` = 'campaign.PreferenceRate.maxDayLimit'
WHERE `name` = 'campaign.PreferenceRate.cost10';

DELETE FROM `vic_admin`.`parameter` WHERE `name` = 'campaign.PreferenceRate.cost15' LIMIT 1;

COMMIT;
