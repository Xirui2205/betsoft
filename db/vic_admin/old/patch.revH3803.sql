START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H3803', 1, 'New section SMS (mantis:105)');

SET NAMES utf8;

-- INSERT SECTION 353
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:353', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(353, @ar, 'SMS', 1, 'sms', 'index'); 

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(15, @ar, 'read', 1),
(2, @ar, 'read', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `sekce_order`) VALUES
(353, 1);


-- INSERT SECTION 354
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:354', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(354, @ar, 'sms_view', 0, 'sms', 'view');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(15, @ar, 'read', 1),
(2, @ar, 'read', 1);


-- SMS tables + data
CREATE TABLE IF NOT EXISTS `vic_admin`.`sms_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `vic_admin`.`sms_type` (`id`, `name`) VALUES
(1, 'Authorizační SMS'),
(2, 'SMS s výsledkem tiketu');

CREATE TABLE IF NOT EXISTS `vic_admin`.`sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sms_type_id` int(11) unsigned NOT NULL,
  `message_text` text CHARACTER SET utf8 NOT NULL,
  `phone_number` varchar(32) CHARACTER SET utf8 NOT NULL,
  `send` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `sms_type_id` (`sms_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='SMS v sekci Odeslané SMS v adminu' AUTO_INCREMENT=3 ;

ALTER TABLE `vic_admin`.`sms`
  ADD CONSTRAINT `sms_ibfk_1` FOREIGN KEY (`sms_type_id`) REFERENCES `sms_type` (`id`);

COMMIT;