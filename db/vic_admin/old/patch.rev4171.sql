START TRANSACTION;
INSERT INTO vic_admin.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(10, 'branch-owner', 1),
(11, 'branch-technician', 1);

INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(133, 'cerny_kasa', 'Miroslav', 'Černý', 'dac2a33560a2667f8245472dc272cfd3', '', 'cerny@bump.cz', 0, 0, '', '', '', NULL, 2),
(134, 'cerny_admin', 'Miroslav', 'Černý', 'dac2a33560a2667f8245472dc272cfd3', '', 'cerny@bump.cz', 0, 0, '', '', '', NULL, 2),
(135, 'cerny_majitel', 'Miroslav', 'Černý', 'dac2a33560a2667f8245472dc272cfd3', '', 'cerny@bump.cz', 0, 0, '', '', '', NULL, 2),
(136, 'cerny_technik', 'Miroslav', 'Černý', 'dac2a33560a2667f8245472dc272cfd3', '', 'cerny@bump.cz', 0, 0, '', '', '', NULL, 2);

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:133',0);
SET @kasa = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:134',0);
SET @admin = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:135',0);
SET @majitel = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:136',0);
SET @admin = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent` (`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@kasa, 8, 1),
(@admin, 9, 1),
(@majitel, 10, 1),
(@admin, 11, 1);



-- role kasa

SET @kasaid = (SELECT acl_role_id FROM vic_admin.`acl_role` WHERE `acl_role_name` = 'branch-employee');
DELETE FROM  `vic_admin`.`acl_role_resource_privilege` WHERE `acl_role_id` = @kasaid;
INSERT INTO `vic_admin`.`acl_role_resource_privilege`(`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
SELECT @kasaid, `r`.`acl_resource_id`, 'read', 1 FROM `vic_admin`.`acl_resource` `r` WHERE
`r`.`acl_resource_name` = 'kasa:BRANCH_APPLICATION'
OR `r`.`acl_resource_name` = 'kasa:BETTING'
OR `r`.`acl_resource_name` = 'kasa:PAYOUT_TICKETS_CREDIT'
OR `r`.`acl_resource_name` = 'kasa:STORNO_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:CLIENT_LOGIN'
OR `r`.`acl_resource_name` = 'kasa:CLIENT_REGISTER'
OR `r`.`acl_resource_name` = 'kasa:CLIENT_DEACTIVATE'
OR `r`.`acl_resource_name` = 'kasa:CLIENT_CHANGE'
OR `r`.`acl_resource_name` = 'kasa:OFFERS'
OR `r`.`acl_resource_name` = 'kasa:SALES'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_BRANCH_IN'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_BRANCH_OUT'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_CLIENT_IN'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_CLIENT_OUT'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_PAY_BY_BILL'
OR `r`.`acl_resource_name` = 'kasa:FINANCIAL_TRANSACTION_PAY_BY_CARD'
OR `r`.`acl_resource_name` = 'kasa:REPORT_BALANCE'
OR `r`.`acl_resource_name` = 'kasa:REPORT_PAYIN_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_PAYOUT_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_STORNO_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_FINANCIAL_TRANSACTIONS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_SALES'
OR `r`.`acl_resource_name` = 'kasa:SYSTEM_SETTINGS_BRANCH_USER'
OR `r`.`acl_resource_name` = 'kasa:PASSWORD_CHANGE';

-- role admin 
SET @adminid =  (SELECT acl_role_id FROM vic_admin.`acl_role` WHERE `acl_role_name` = 'branch-admin');
INSERT INTO `vic_admin`.`acl_role_resource_privilege`(`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
SELECT @adminid, `r`.`acl_resource_id`, 'read', 1 FROM `vic_admin`.`acl_resource` `r` WHERE `r`.`acl_resource_name` LIKE 'kasa:%';
COMMIT;

-- role technik 
SET @technikid = (SELECT acl_role_id FROM vic_admin.`acl_role` WHERE `acl_role_name` = 'branch-technician');
INSERT INTO `vic_admin`.`acl_role_resource_privilege`(`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
SELECT @technikid, `r`.`acl_resource_id`, 'read', 1 FROM `vic_admin`.`acl_resource` `r` WHERE
`r`.`acl_resource_name` = 'kasa:BRANCH_APPLICATION'
OR `r`.`acl_resource_name` = 'kasa:OFFERS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_BALANCE'
OR `r`.`acl_resource_name` = 'kasa:SYSTEM_SETTINGS_TECHNICIAN'
OR `r`.`acl_resource_name` = 'kasa:PASSWORD_CHANGE';


-- role majitel
SET @ownerid = (SELECT acl_role_id FROM vic_admin.`acl_role` WHERE `acl_role_name` = 'branch-owner');
INSERT INTO `vic_admin`.`acl_role_resource_privilege`(`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
SELECT @ownerid, `r`.`acl_resource_id`, 'read', 1 FROM `vic_admin`.`acl_resource` `r` WHERE
`r`.`acl_resource_name` = 'kasa:BRANCH_APPLICATION'
OR `r`.`acl_resource_name` = 'kasa:REPORT_BALANCE'
OR `r`.`acl_resource_name` = 'kasa:REPORT_PAYIN_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_PAYOUT_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_STORNO_TICKETS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_FINANCIAL_TRANSACTIONS'
OR `r`.`acl_resource_name` = 'kasa:REPORT_RENT'
OR `r`.`acl_resource_name` = 'kasa:REPORT_SALES'
OR `r`.`acl_resource_name` = 'kasa:SYSTEM_SETTINGS_BRANCH_USER'
OR `r`.`acl_resource_name` = 'kasa:PASSWORD_CHANGE';
COMMIT;


START TRANSACTION;

INSERT INTO vic_admin.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(13, 'gov-supervisor', 1);

INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(137, 'statni_dozor', 'Státní', 'dozor', '4b9f73f4e2400588d340d106c4352fad', '', '?', 0, 0, '', '', '', NULL, NULL);

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:137',1);
SET @admin = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent` (`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@admin, 13, 1);

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '13', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:1'
OR acl_resource_name='section:148'
OR acl_resource_name='section:272'
OR acl_resource_name='section:275'
OR acl_resource_name='section:247'
OR acl_resource_name='section:211';

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '3', `acl_resource_id`, 'update', '1'
FROM vic_admin.acl_resource
where acl_resource_name='section:275';


COMMIT;
