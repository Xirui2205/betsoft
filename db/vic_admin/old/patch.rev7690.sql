START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7690', NULL, 'PreferenceRate. (mantis:566)');


UPDATE `vic_admin`.`parameter` SET
`description` = 'Cena zvýhodněného kurzu ve formátu (vyse1;cena1;vyse2;cena2;...)'
WHERE `name` = 'campaign.PreferenceRate.cost';

UPDATE `vic_admin`.`parameter` SET
`description` = 'Maximalni pocet bodu ktere jde za den utratit za zvyhodneny kurz.'
WHERE `name` = 'campaign.PreferenceRate.maxDayLimit';


COMMIT;
