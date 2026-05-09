set foreign_key_checks=0;
start transaction;
truncate vic_admin.branch_type;
INSERT INTO vic_admin.`branch_type` (`id`, `name`) VALUES
(1, 'internet'),
(2, 'herna'),
(3, 'kasino'),
(4, 'trafika'),
(5, 'bar'),
(6, 'sportbar'),
(7, 'samostatná kancelář'),
(8, 'obchod');
set foreign_key_checks=1;
commit;
