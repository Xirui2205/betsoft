START TRANSACTION;
-- new branch: KASA1
INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`, `currency_id`) VALUES
(8, 3, 1, 'KASA 1', 'KASA1', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'kasa1@compbet.com', 2, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 0);

-- new branch employee: kasa1
INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(10, 'kasa1', 'První', 'Kasa', 'b963ea2d4890b4bb86cda5643d57bd28', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 8);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:8', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:8', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:10', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:10', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_resource_type`(`acl_resource_type_id`, `acl_resource_type_name`) VALUES (3, 'kasa');
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(2, 'kasa:BRANCH_APPLICATION', NULL, 3),
(2, 'kasa:BETTING', NULL, 3),
(2, 'kasa:PAYOUT_TICKETS', NULL, 3),
(2, 'kasa:PAYOUT_TICKETS_CREDIT', NULL, 3),
(2, 'kasa:STORNO_TICKETS', NULL, 3),
(2, 'kasa:CLIENT_LOGIN', NULL, 3),
(2, 'kasa:CLIENT_REGISTER', NULL, 3),
(2, 'kasa:CLIENT_DEACTIVATE', NULL, 3),
(2, 'kasa:CLIENT_CHANGE', NULL, 3),
(2, 'kasa:OFFERS', NULL, 3),
(2, 'kasa:SALES', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_BRANCH_IN', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_BRANCH_OUT', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_CLIENT_IN', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_CLIENT_OUT', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_PAY_BY_BILL', NULL, 3),
(2, 'kasa:FINANCIAL_TRANSACTION_PAY_BY_CARD', NULL, 3),
(2, 'kasa:REPORT_BALANCE', NULL, 3),
(2, 'kasa:REPORT_PAYIN_TICKETS', NULL, 3),
(2, 'kasa:REPORT_PAYOUT_TICKETS', NULL, 3),
(2, 'kasa:REPORT_STORNO_TICKETS', NULL, 3),
(2, 'kasa:REPORT_FINANCIAL_TRANSACTIONS', NULL, 3),
(2, 'kasa:REPORT_RENT', NULL, 3),
(2, 'kasa:REPORT_SALES', NULL, 3),
(2, 'kasa:SYSTEM_SETTINGS_BRANCH_USER', NULL, 3),
(2, 'kasa:SYSTEM_SETTINGS_TECHNICIAN', NULL, 3),
(2, 'kasa:SYSTEM_SETTINGS_ADMIN', NULL, 3),
(2, 'kasa:PASSWORD_CHANGE', NULL, 3),
(2, 'kasa:PASSWORD_CHANGE_ADMIN', NULL, 3);
-- role ID 14 is homebranch
INSERT INTO `vic_admin`.`acl_role_resource_privilege`(`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
SELECT 14, `r`.`acl_resource_id`, 'read', 1 FROM `vic_admin`.`acl_resource` `r` WHERE `r`.`acl_resource_name` LIKE 'kasa:%';
COMMIT;