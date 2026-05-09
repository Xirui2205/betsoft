START TRANSACTION;

INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`, `currency_id`) VALUES
(7, 1, 1, 'Livebetting', 'Livebetting', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'livebetting@compbet.com', 1, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 8);

INSERT INTO `vic_admin`.`host` (
`id` ,
`branch_id` ,
`name` ,
`allowed` ,
`version` ,
`is_online` ,
`hardware` ,
`display` ,
`printer` ,
`ip` ,
`win_sn` ,
`provider_dns` ,
`provider_gateway` ,
`provider_ip` ,
`provider_username` ,
`provider_password` ,
`vic_email` ,
`vic_email_password` ,
`vic_admin_password` ,
`vic_employee_password_1` ,
`vic_employee_password_2` ,
`note`
)
VALUES
('7', '7', 'Host1/Livebetting', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'livebetting@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely');

INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(10, 'livebetting', 'Livebetting', 'Livebetting', 'dac2a33560a2667f8245472dc272cfd3', '', 'Livebetting@compbet.com', 0, 0, '', '', '', NULL, 7);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:7', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:7', 0);
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

COMMIT;
