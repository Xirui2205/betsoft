START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7825', 1, 'Novy vernostni program happy hour. (mantis:566)');


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
('', 'campaign.HappyHourVisit.timeFrom;', '7:00:00', '0', '0', '0', NULL , '0', '0', '1', 'Cas od kdy ma byt generovan visit happy hour (HH:MM:SS) v UTC'),
('', 'campaign.HappyHourVisit.timeTo', '23:30:00', '0', '0', '0', NULL , '0', '0', '1', 'Cas do kdy ma byt generovan visit happy hour (HH:MM:SS) v UTC'),
('', 'campaign.HappyHourVisit.amount', '10', '0', '0', '0', NULL , '0', '0', '1', 'Pocet pridelenych bodu v ramci visit happy hour'),
('', 'campaign.HappyHourVisit.usersCount', '50', '0', '0', '0', NULL , '0', '0', '1', 'Maximalni pocet uzivatelu kteri mohou dostat body.');

COMMIT;
