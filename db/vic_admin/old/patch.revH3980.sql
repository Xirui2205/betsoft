/*
Remove script (for testing this patch):

delete from sekce_has_parent where sekce_id=365;

delete from acl_role_resource_privilege
where acl_resource_id in (
	select acl_resource_id from
	acl_resource
	where acl_resource_name in('section:365', 'section:366', 'section:367', 'section:368', 'section:369', 'section:370')
);

delete from sekce where sekce_id in(365, 366, 367, 368, 369, 370);

delete from acl_resource
	where acl_resource_name in('section:365', 'section:366', 'section:367', 'section:368', 'section:369', 'section:370');

delete from parameter
where name in ('web.articlesHomepageCount', 'web.homepageTabsInterval');
*/

START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H3980', 1, 'Articles - admin (mantis:105)');

--
-- INSERT SECTION 365 (index)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:365', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(365, @ar, 'Články', 1, 'article', 'index'); 

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `sekce_order`, `parent_id`) VALUES
(365, 1, 160);


-- INSERT SECTION 366 (update)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:366', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(366, @ar, 'Upravit', 0, 'article', 'update');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'update', 1),
(2, @ar, 'update', 1),
(3, @ar, 'update', 1),
(4, @ar, 'update', 1),
(17, @ar, 'update', 1),
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);

-- INSERT SECTION 367 (insert)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:367', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(367, @ar, 'Přidat', 0, 'article', 'insert');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'insert', 1),
(2, @ar, 'insert', 1),
(3, @ar, 'insert', 1),
(4, @ar, 'insert', 1),
(17, @ar, 'insert', 1),
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);

-- INSERT SECTION 368 (delete)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:368', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(368, @ar, 'Smazat', 0, 'article', 'delete');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'delete', 1),
(2, @ar, 'delete', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'delete', 1),
(17, @ar, 'delete', 1),
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);

-- INSERT SECTION 369 (loadBetByAlias)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:369', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(369, @ar, 'getSazkaByAlias (json only)', 0, 'article', 'load-bet-by-alias');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);


-- INSERT SECTION 370 (getOddsByBetId)
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:370', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(370, @ar, 'getOddsByBetId (json only)', 0, 'article', 'get-odds-by-bet-id');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(3, @ar, 'read', 1),
(4, @ar, 'read', 1),
(17, @ar, 'read', 1);



INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`type` ,
`mandatory` ,
`is_admin`,
`description`
)
VALUES
(NULL , 'web.articlesHomepageCount', '5', '0', '0', '0', NULL , '1', '0', 'Počet článků na hlavní stránce. (Lze nastavit i na nulu.)');


INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`type` ,
`mandatory` ,
`is_admin`,
`description`
)
VALUES
(NULL , 'web.homepageTabsInterval', '4000', '0', '0', '0', NULL , '1', '0', 'Rychlost automatického přepínání záložek na hlavní stránce.');


COMMIT;