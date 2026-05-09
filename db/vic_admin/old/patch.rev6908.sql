CREATE TEMPORARY TABLE vic_admin.tmp_sections(acl_resource_id INT(10) UNSIGNED NOT NULL PRIMARY KEY) Engine=MyISAM;
CREATE TEMPORARY TABLE vic_admin.tmp_privileges(privilege_name VARCHAR(32) COLLATE utf8_general_ci NOT NULL PRIMARY KEY) Engine=MyISAM;

START TRANSACTION;

SET @roleCM = (SELECT acl_role_id FROM vic_admin.acl_role WHERE acl_role_name='content-manager');

INSERT INTO vic_admin.tmp_sections(acl_resource_id)
 SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id IN (143, 153, 133, 134, 290, 214, 142, 135);
INSERT INTO vic_admin.tmp_privileges(privilege_name) VALUES ('read'),('update'),('delete');

INSERT INTO vic_admin.acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set)
 SELECT @roleCM, s.acl_resource_id, p.privilege_name, 1 FROM vic_admin.tmp_sections s CROSS JOIN vic_admin.tmp_privileges p;

COMMIT;

DROP TABLE vic_admin.tmp_sections;
DROP TABLE vic_admin.tmp_privileges;
