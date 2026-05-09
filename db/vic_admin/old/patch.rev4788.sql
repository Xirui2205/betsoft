CREATE TABLE  `vic_admin`.`branch_opening_hours` (
`branch_id` INT UNSIGNED NOT NULL ,
`day_number` INT UNSIGNED NOT NULL COMMENT  '0=monday, sunday=6',
`from` TIME NOT NULL ,
`to` TIME NOT NULL
) ENGINE = INNODB;



START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:286', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(286, @ar, 'Branch Opening Hours', 0, 'branch-opening-hours', 'view');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(286, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'write', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'write', 1),
(2, @ar, 'delete', 1);


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:287', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(287, @ar, 'Branch Opening Hours', 0, 'branch-opening-hours', 'update');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(287, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'write', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'write', 1),
(2, @ar, 'delete', 1);


INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES
(1, 'branch_opening_hours_edit_description', NULL, 'Hodiny zadáváme ve formátu xx:xx-xx:xx bez mezer. Jednotlivé intervaly definující dobu nepřetržitého otevření oddělujeme středníkem (např: xx:xx-xx:xx;xx:xx-xx:xx)', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
VALUES (2, 'branch_opening_hours_edit_description', NULL, 'text eng', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
VALUES (16, 'branch_opening_hours_edit_description', NULL, 'text sk', '0', @pid);


COMMIT;
