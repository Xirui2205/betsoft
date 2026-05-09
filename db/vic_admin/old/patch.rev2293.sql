CREATE TABLE `vic_admin`.`log` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`time` DATETIME NOT NULL ,
`tag` VARCHAR( 32 ) NULL ,
`priority` INT NOT NULL ,
`message` TEXT NULL ,
`args` TEXT NULL ,
`exception` TEXT NULL ,
`file` VARCHAR( 256 ) NULL ,
`line` INT NULL ,
INDEX ( `time` , `tag` , `priority` )
) ENGINE = InnoDB;

DROP TABLE `vic_admin`.`logy`;

START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1283, 1, 'section:254', 1001, 2);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(2, 1283, 'read', 1);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(254, 1283, 'Logs', 1, 'log', 'view');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(254, null, 0);

COMMIT;
