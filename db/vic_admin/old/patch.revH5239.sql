START TRANSACTION;

SET NAMES utf8;

CREATE TABLE IF NOT EXISTS `host_printed_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_id` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `document_type` varchar(300) DEFAULT NULL,
  `page_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- INSERT SECTION 392 printed-pages/view
-- -------------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:392', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(392, @ar, 'Statistika tisku', 1, 'printed-pages-counter', 'index');

INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(392, NULL, 6);

COMMIT;