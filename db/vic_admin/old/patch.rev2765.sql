CREATE TABLE `vic_admin`.`host_message` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`host_id` INT UNSIGNED NOT NULL ,
`message` TEXT NOT NULL ,
`time` DATETIME NOT NULL ,
`red` TINYINT( 1 ) NOT NULL ,
`red_time` DATETIME NULL ,
INDEX ( `host_id` , `red` )
) ENGINE = InnoDB;

ALTER TABLE `host_message` ADD FOREIGN KEY ( `host_id` ) REFERENCES `vic_admin`.`host` (
`id`
);


START TRANSACTION;
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1297, 1, 'section:268', 1001, 2);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(268, 1297, 'Send message', 1, 'host-message', 'send');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'268', NULL, '1'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', '1297', 'read', '1');

COMMIT;

