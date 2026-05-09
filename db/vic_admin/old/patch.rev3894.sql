START TRANSACTION;
insert into vic_admin.acl_role_has_parent (acl_role_id,parent_id,parent_order) values
(1107,1,1),
(1109,1,1),
(1111,1,1),
(1113,1,1),
(1115,1,1),
(1117,1,1),
(1119,1,1),
(1121,1,1),
(1123,1,1),
(1125,1,1),
(1127,1,1),
(1129,1,1),
(1131,1,1);
COMMIT;


START TRANSACTION;

INSERT INTO `vic_admin`.`branch_location` (
`id` ,
`name`
)
VALUES (
NULL , 'Testovací koutek'
);

SET @loc = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`branch` (`id`, `type_id`, `branch_location_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`, `currency_id`) VALUES
(5000, 2, @loc, 'Kasa Hradec 1', 'kasa-hradec-1', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'j.benedek@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'j.benedek@compbet.com',  8),
(5001, 2, @loc, 'Kasa Hradec 2', 'kasa-hradec-1', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'j.benedek@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'j.benedek@compbet.com', 8),

(5002, 2, @loc, 'Kasa Roudnice 1', 'kasa-roudnice-1', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'v.hercl@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'v.hercl@compbet.com', 8),
(5003, 2, @loc, 'Kasa Roudnice 2', 'kasa-roudnice-2', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'v.hercl@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'v.hercl@compbet.com', 8),
(5004, 2, @loc, 'Kasa Roudnice 3', 'kasa-roudnice-3', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'v.hercl@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'v.hercl@compbet.com', 8),
(5005, 2, @loc, 'Kasa Roudnice 4', 'kasa-roudnice-4', 'N/A', 'N/A', 'N/A', 'N/A', '000', 'v.hercl@compbet.com', 1, 'N/A', 'N/A', 'N/A', 'N/A', 'v.hercl@compbet.com',8);

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
(10, 5000, 'Host1/Kasa Hradec 1', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'j.benedek@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
(11, 5001, 'Host1/Kasa Hradec 2', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'j.benedek@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
(12, 5002, 'Host1/Kasa Roudnice 1', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'v.hercl@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
(13, 5003, 'Host1/Kasa Roudnice 2', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'v.hercl@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
(14, 5004, 'Host1/Kasa Roudnice 3', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'v.hercl@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely'),
(15, 5005, 'Host1/Kasa Roudnice 4', '1', '1', '0', 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL , NULL , NULL , NULL , NULL , 'v.hercl@compbet.com', NULL , NULL , NULL , NULL , 'Pro testovací účely');


INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(40, 'kasahrad1', 'kasa-hradec-1', 'kasa-hradec-1', 'dac2a33560a2667f8245472dc272cfd3', '', 'j.benedek@compbet.com', 0, 0, '', '', '', NULL, 5000),
(41, 'kasahrad2', 'kasa-hradec-2', 'kasa-hradec-2', 'dac2a33560a2667f8245472dc272cfd3', '', 'j.benedek@compbet.com', 0, 0, '', '', '', NULL, 5001),
(42, 'kasaroud1', 'kasa-roudnice-1', 'kasa-roudnice-1', 'dac2a33560a2667f8245472dc272cfd3', '', 'v.hercl@compbet.com', 0, 0, '', '', '', NULL, 5002),
(43, 'kasaroud2', 'kasa-roudnice-2', 'kasa-roudnice-2', 'dac2a33560a2667f8245472dc272cfd3', '', 'v.hercl@compbet.com', 0, 0, '', '', '', NULL, 5003),
(44, 'kasaroud3', 'kasa-roudnice-3', 'kasa-roudnice-3', 'dac2a33560a2667f8245472dc272cfd3', '', 'v.hercl@compbet.com', 0, 0, '', '', '', NULL, 5004),
(45, 'kasaroud4', 'kasa-roudnice-4', 'kasa-roudnice-4', 'dac2a33560a2667f8245472dc272cfd3', '', 'v.hercl@compbet.com', 0, 0, '', '', '', NULL, 5005);


INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5000', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5000', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:40', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:40', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5001', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5001', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:41', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:41', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5002', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5002', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:42', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:42', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5003', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5003', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:43', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:43', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5004', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5004', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:44', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:44', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('branch:5005', 0);
SET @rb = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch:5005', 0);
SET @rh = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('user:45', 0);
SET @ru = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role`(`acl_role_name`, `assignable`) VALUES('homebranch-user:45', 0);
SET @rhu = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@ru, 8, 1),
(@rb, 13, 1),
(@rh, 14, 1),
(@rhu, @rh, 1);

INSERT INTO vic_main.`uzivatel` (`user_id`, `branch_id`, `jmeno`, `prijmeni`, `nick`, `heslo`, `citizen_id`, `pohlavi`, `datum_narozeni`, `email`, `zeme_id`, `ulice`, `psc`, `telefon`, `mena_id`, `info`, `mobil`, `newsletter`, `vyber_status`, `posledni_prihlaseni`, `vyprseni_session`, `datum_registrace`, `individualni_max_vklad`, `zakazany`, `lang_id`, `misto`, `ucet_status`, `vyhernost`, `block`, `block_ip`, `block_time`, `vyhernost_game`, `bet_stats_win`, `bet_stats_lose_acc`, `bet_stats_lose_book`, `bet_total`, `win_ticket`, `lose_ticket`, `delete_ticket`, `num_bet_ticket`, `bet_total2`, `ticket_num`, `finance_rating`, `max_bet`, `book_info`, `self_excluded_until`, `osloveni`, `e_testovaci`, `block_play`, `castka_m`, `castka_w`, `datum_aktivace`, `anonymous`, `watched`, `client_card_number`, `created_by_admin_id`, `allowed_by_admin_id`) VALUES
(40, 5000, 'Anonym/Kasa Hradec 1', 'Anonym/Kasa Hradec 1', 'kasahradec1', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5000@compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0),
(41, 5001, 'Anonym/Kasa Hradec 2', 'Anonym/Kasa Hradec 2', 'kasahradec2', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5001@compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0),
(42, 5002, 'Anonym/Kasa Roudnice 1', 'Anonym/Kasa Roudnice 1', 'kasaroud1', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5002@compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0),
(43, 5003, 'Anonym/Kasa Roudnice 2', 'Anonym/Kasa Roudnice 2', 'kasaroud2', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5003compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0),
(44, 5004, 'Anonym/Kasa Roudnice 3', 'Anonym/Kasa Roudnice 3', 'kasaroud3', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5004@compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0),
(45, 5005, 'Anonym/Kasa Roudnice 4', 'Anonym/Kasa Roudnice 4', 'kasaroud4', 'e20d56f7a999432aa3262a81a3753e42', '', 'm', '1970-01-01', 'kasa5005@compbet.com', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'fff', 1, NULL, 0, '', NULL, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1, 0, 0, 1, 1.0000, 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', '', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0);

COMMIT;

