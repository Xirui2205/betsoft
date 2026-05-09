START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H552', NULL, 'Fixed parameter name campaign.HappyHourVisit.timeFrom (mantis:885)');

UPDATE vic_admin.parameter SET name='campaign.HappyHourVisit.timeFrom' WHERE name='campaign.HappyHourVisit.timeFrom;';

COMMIT;
