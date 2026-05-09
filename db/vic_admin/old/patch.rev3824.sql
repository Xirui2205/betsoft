start transaction;

INSERT INTO `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(131, 'gabriel', 'Tomáš', 'Gabriel', '9c84dbb4d7c44736af151fca31fa9657', '', 'el.gabriel@seznam.cz', 0, 0, '', '', '', NULL, NULL),
(132, 'gabriel_b', 'Tomáš', 'Gabriel', 'b1e48a56a866e39e0d281e08afbc88a7', '', 'el.gabriel@seznam.cz', 0, 0, '', '', '', NULL, NULL);


INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:131',0);
SET @a = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, 'user:132',0);
SET @b = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent` (`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@a, 2, 1),
(@b, 3, 1);

commit;

ALTER TABLE vic_admin.`host_message` CHANGE `red` `read` TINYINT( 1 ) NOT NULL ,
CHANGE `red_time` `read_time` DATETIME NULL DEFAULT NULL;

ALTER TABLE vic_admin.`host_message` CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
