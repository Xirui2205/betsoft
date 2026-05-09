START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7538', null, 'Branch In/Out Alert');
 
SET NAMES UTF8;

INSERT INTO `vic_admin`.`parameter` (`name`,`value`,`is_host`,`is_branch`,`is_user`,`type`,`mandatory`,`is_admin`,`is_editable`,`description`)
VALUES ('alert.hostInOutStatus.to', 'martin.bohal@compbet.com', 0, 0, 0, null, 1, 0, 1, 'Emailové adresy na které jsou zasílány alerty o dotacích a odvodech poboček (oddělené čárkou bez mezer)');


COMMIT;
