START TRANSACTION;

INSERT INTO vic_admin.acl_resource (`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:339', 1001, 2);
set @a=(SELECT MAX(acl_resource_id) FROM vic_admin.acl_resource);
INSERT INTO vic_admin.sekce (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (339, @a, 'Competition', 1, 'competitions', 'tickets');
INSERT INTO vic_admin.sekce_has_parent (`sekce_id`, `parent_id`, `sekce_order`) VALUES (339, NULL, 1);

commit;
