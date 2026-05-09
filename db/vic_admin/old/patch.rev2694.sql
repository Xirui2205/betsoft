START TRANSACTION;

INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`, `currency_id`) VALUES
(3, 3, 1, 'KASA 2', 'KASA2', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'kasa1@compbet.com', 2, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 0),
(4, 3, 1, 'KASA 3', 'KASA3', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'kasa1@compbet.com', 2, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 0),
(5, 3, 1, 'KASA 4', 'KASA4', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'kasa1@compbet.com', 2, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 0),
(6, 3, 1, 'KASA 5', 'KASA5', 'Na Příkopě 1', 'Praha 1', '11000', 'Kasa 1', '+420777065536', 'kasa1@compbet.com', 2, 'Pavel Klinger', 'Ulice 1', '1234567890', '7012310129', 'klinger@compbet.com', 0);

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
('103', '3', 'Host1/Kasa2', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'kasa1@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
('104', '4', 'Host1/Kasa3', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'kasa1@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
('105', '5', 'Host1/Kasa4', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'kasa1@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
('106', '6', 'Host1/Kasa5', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'kasa1@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely');

INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(32, 'kasa2a', 'Kasa2a', 'Kasa2a', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),
(33, 'kasa2b', 'Kasa2b', 'Kasa2b', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),

(34, 'kasa3a', 'Kasa3a', 'Kasa3a', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),
(35, 'kasa3b', 'Kasa3b', 'Kasa3b', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),

(36, 'kasa4a', 'Kasa4a', 'Kasa4a', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),
(37, 'kasa4b', 'Kasa4b', 'Kasa4b', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),

(38, 'kasa5a', 'Kasa5a', 'Kasa5a', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2),
(39, 'kasa5b', 'Kasa5b', 'Kasa5b', 'dac2a33560a2667f8245472dc272cfd3', '', 'kasa1@compbet.com', 0, 0, '', '', '', NULL, 2);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:103', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:103', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:32', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:32', 0);
SET @rhu = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:33', 0);
SET @ru2 = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:33', 0);
SET @rhu2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@ru2, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1),
(@rhu2, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:104', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:104', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:34', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:34', 0);
SET @rhu = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:35', 0);
SET @ru2 = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:35', 0);
SET @rhu2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@ru2, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1),
(@rhu2, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:105', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:105', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:36', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:36', 0);
SET @rhu = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:37', 0);
SET @ru2 = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:37', 0);
SET @rhu2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@ru2, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1),
(@rhu2, @rh, 1);


INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:106', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:106', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:38', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:38', 0);
SET @rhu = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:39', 0);
SET @ru2 = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:39', 0);
SET @rhu2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@ru2, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1),
(@rhu2, @rh, 1);

COMMIT;
