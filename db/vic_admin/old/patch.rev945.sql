SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `branch_has_bank_account` DROP FOREIGN KEY `branch_has_bank_account_ibfk_3` ;

ALTER TABLE `branch_has_bank_account` DROP FOREIGN KEY `branch_has_bank_account_ibfk_1` ;

ALTER TABLE `branch_has_bank_account` DROP FOREIGN KEY `branch_has_bank_account_ibfk_2` ;

ALTER TABLE  `vic_admin`.`branch_has_bank_account` DROP PRIMARY KEY;
ALTER TABLE  `vic_admin`.`branch_has_bank_account` DROP INDEX  `bank_account_id`;
ALTER TABLE  `vic_admin`.`branch_has_bank_account` ADD INDEX  `branch_id` (  `branch_id` );
ALTER TABLE  `vic_admin`.`branch_has_bank_account` ADD PRIMARY KEY (  `bank_account_id` );

ALTER TABLE `branch_has_bank_account` ADD FOREIGN KEY ( `branch_id` ) REFERENCES `vic_admin`.`branch` (
`id`
);

ALTER TABLE `branch_has_bank_account` ADD FOREIGN KEY ( `bank_account_id` ) REFERENCES `vic_admin`.`bank_account` (
`account_id`
);

ALTER TABLE `branch_has_bank_account` ADD FOREIGN KEY ( `bank_account_type` ) REFERENCES `vic_admin`.`bank_account_type` (
`type_id`
);


INSERT INTO  `vic_admin`.`sekce` (
`sekce_id` ,
`parent_id` ,
`nazev` ,
`zobrazeno` ,
`poradi` ,
`controller` ,
`action`
)
VALUES (
'165',  '0',  'Branch crud',  '1',  '0',  'branch',  'delete'
);

SET foreign_key_checks = 1;
