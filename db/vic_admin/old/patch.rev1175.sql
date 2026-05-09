INSERT INTO `role` (`role_id`, `role_name`) VALUES
(10, 'manager'),
(11, 'branch-employee'),
(12, 'technician');

INSERT INTO `role_has_parent` (`role_id`, `parent_id`, `parent_order`) VALUES
(6, 11, 1);
