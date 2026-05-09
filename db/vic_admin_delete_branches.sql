-- here select all branches to be deleted
CREATE TEMPORARY TABLE vic_admin.tmp_branch(id INT(10) UNSIGNED PRIMARY KEY);
INSERT INTO vic_admin.tmp_branch(id)
 SELECT id FROM vic_admin.branch WHERE id IN (2,4,5,6,1137) OR id>=5000;

-- next queries should delete all dependent data from vic_admin database
CREATE TEMPORARY TABLE vic_admin.tmp_host(id INT(10) UNSIGNED PRIMARY KEY);
INSERT INTO vic_admin.tmp_host(id)
 SELECT id FROM vic_admin.host WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);
CREATE TEMPORARY TABLE vic_admin.tmp_admin(id INT(10) UNSIGNED PRIMARY KEY);
INSERT INTO vic_admin.tmp_admin(id)
 SELECT admin_id FROM vic_admin.admin WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);
CREATE TEMPORARY TABLE vic_admin.tmp_role(id INT(10) UNSIGNED PRIMARY KEY);
INSERT INTO vic_admin.tmp_role(id)
 SELECT acl_role_id FROM vic_admin.acl_role WHERE acl_role_name IN (SELECT CONCAT('user:',id) FROM vic_admin.tmp_admin);

START TRANSACTION;
DELETE FROM vic_admin.acl_role_has_parent WHERE acl_role_id IN (SELECT id FROM vic_admin.tmp_role);
DELETE FROM vic_admin.acl_role_resource_privilege WHERE acl_role_id IN (SELECT id FROM vic_admin.tmp_role);
DELETE FROM vic_admin.acl_role WHERE acl_role_id IN (SELECT id FROM vic_admin.tmp_role);

DELETE FROM vic_admin.admin_has_parameter WHERE admin_id IN (SELECT id FROM vic_admin.tmp_admin);
DELETE FROM vic_admin.admin WHERE admin_id IN (SELECT id FROM vic_admin.tmp_admin);

DELETE FROM vic_admin.contract_parameter_value WHERE contract_id IN (
 SELECT contract_id FROM vic_admin.contract WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch)
);
DELETE FROM vic_admin.contract WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);

DELETE FROM vic_admin.host_has_parameter WHERE host_id IN (SELECT id FROM vic_admin.tmp_host);
DELETE FROM vic_admin.host_message WHERE host_id IN (SELECT id FROM vic_admin.tmp_host);
DELETE FROM vic_admin.host WHERE host_id IN (SELECT id FROM vic_admin.tmp_host);

DELETE FROM vic_admin.branch_has_parameter WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);
DELETE FROM vic_admin.branch_has_bank_account WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);
DELETE FROM vic_admin.branch_opening_hours WHERE branch_id IN (SELECT id FROM vic_admin.tmp_branch);
DELETE FROM vic_admin.branch WHERE id IN (SELECT id FROM vic_admin.tmp_branch);
COMMIT;

DROP TABLE vic_admin.tmp_role;
DROP TABLE vic_admin.tmp_admin;
DROP TABLE vic_admin.tmp_host;
DROP TABLE vic_admin.tmp_branch;
