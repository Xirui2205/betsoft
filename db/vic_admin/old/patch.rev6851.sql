CREATE TEMPORARY TABLE vic_admin.tmp_sections(acl_resource_id INT(10) UNSIGNED NOT NULL PRIMARY KEY) Engine=MyISAM;
CREATE TEMPORARY TABLE vic_admin.tmp_privileges(privilege_name VARCHAR(32) COLLATE utf8_general_ci NOT NULL PRIMARY KEY) Engine=MyISAM;

START TRANSACTION;

SET @res = (SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=255);
SET @roleB = (SELECT acl_role_id FROM vic_admin.acl_role WHERE acl_role_name='bookmaker');
SET @roleSB = (SELECT acl_role_id FROM vic_admin.acl_role WHERE acl_role_name='superbookmaker');

REPLACE INTO vic_admin.acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set) VALUES
(@roleB, @res, 'read', 1),
(@roleSB, @res, 'read', 1);

INSERT INTO vic_admin.tmp_sections(acl_resource_id)
 SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id IN (160,262,259,260,261,161,258,98,162,99,82,45,80,131,2,46,274,72);
INSERT INTO vic_admin.tmp_privileges(privilege_name) VALUES ('read'),('update'),('delete');

INSERT INTO vic_admin.acl_role(acl_role_id, acl_role_name, assignable) VALUES (17, 'content-manager', 1);
INSERT INTO vic_admin.acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set)
 SELECT 17, s.acl_resource_id, p.privilege_name, 1 FROM vic_admin.tmp_sections s CROSS JOIN vic_admin.tmp_privileges p;

COMMIT;

DROP TABLE vic_admin.tmp_sections;
DROP TABLE vic_admin.tmp_privileges;
