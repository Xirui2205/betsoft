START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7555', 1, 'Day and total point transaction limit. (mantis:566)');


INSERT INTO `vic_admin`.`parameter` (
 `id`,
 `name`,
 `value`,
 `is_host`,
 `is_branch`,
 `is_user`,
 `type`,
 `mandatory`,
 `is_admin`,
 `is_editable`,
 `description`
) VALUES
('', 'point-transaction.limit.total', '10000', '0', '0', '1', NULL , '1', '0', '1', 'Maximální možný počet bodů na kartě.'),
('', 'point-transaction.limit.day-income', '2000', '0', '0', '1', NULL , '1', '0', '1', 'Maximální dení příjem bodů.');

UPDATE `vic_admin`.`parameter` SET
`value` = '0;1;25;2'
WHERE `name` = 'campaign.CreateMoneyTicket.pointRatio';

UPDATE `vic_admin`.`parameter` SET
`value` = '1;1;3;2;10;5;30;15;100;50;300;150;1000;500'
WHERE `name` = 'campaign.BranchVisit.points';

COMMIT;
