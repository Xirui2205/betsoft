START TRANSACTION;

UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch contract' WHERE  `sekce`.`sekce_id` =163;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch bank account' WHERE  `sekce`.`sekce_id` =164;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch contract' WHERE  `sekce`.`sekce_id` =165;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch parameter' WHERE  `sekce`.`sekce_id` =166;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch parameter' WHERE  `sekce`.`sekce_id` =167;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch contract' WHERE  `sekce`.`sekce_id` =168;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch bank account' WHERE  `sekce`.`sekce_id` =169;
UPDATE  `vic_admin`.`sekce` SET  `nazev` =  'Branch contract' WHERE  `sekce`.`sekce_id` =203;

INSERT INTO  `vic_admin`.`sekce` (
`sekce_id` ,
`parent_id` ,
`nazev` ,
`zobrazeno` ,
`poradi` ,
`controller` ,
`action`
)
VALUES (
206 ,  '2',  'Global Parameters',  '0',  '0',  'settings-global-parameters',  'view-branch'
), (
207 ,  '2',  'Global Parameters',  '0',  '0',  'settings-global-parameters',  'view-user'
), (
208 ,  '2',  'Global Parameters (NEW)',  '1',  '0',  'settings-global-parameters',  'index'
), (
209 ,  '2',  'Global Parameters',  '0',  '0',  'settings-global-parameters',  'update-branch'
), (
210 ,  '2',  'Global Parameters',  '0',  '0',  'settings-global-parameters',  'update-user'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '206',  'read',  '1'
), (
NULL ,  '2',  '206',  'update',  '1'
), (
NULL ,  '2',  '206',  'delete',  '1'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '207',  'read',  '1'
), (
NULL ,  '2',  '207',  'update',  '1'
), (
NULL ,  '2',  '207',  'delete',  '1'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '208',  'read',  '1'
), (
NULL ,  '2',  '208',  'update',  '1'
), (
NULL ,  '2',  '208',  'delete',  '1'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '209',  'read',  '1'
), (
NULL ,  '2',  '209',  'update',  '1'
), (
NULL ,  '2',  '209',  'delete',  '1'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '210',  'read',  '1'
), (
NULL ,  '2',  '210',  'update',  '1'
), (
NULL ,  '2',  '210',  'delete',  '1'
);

COMMIT;
