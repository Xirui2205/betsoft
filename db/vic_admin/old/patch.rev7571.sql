START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7571', null, 'Transaction confirmation Alert');
 
SET NAMES UTF8;

INSERT INTO `vic_admin`.`parameter` (`name`,`value`,`is_host`,`is_branch`,`is_user`,`type`,`mandatory`,`is_admin`,`is_editable`,`description`)
VALUES ('alert.transactionConfirmation.to', 'martin.bohal@compbet.com', 0, 1, 0, null, 1, 0, 1, 'Emailové adresy na které jsou zasílány alerty o transakcích čekajících na schválení (oddělené čárkou bez mezer)');


COMMIT;
