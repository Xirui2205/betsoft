START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
('rootBetAliasIntervalNew.from', 10001, 0, 0, 1, NULL, 1, 1, 1, 'Dolní limit rozsahu pro nové aliasy.'),
('rootBetAliasIntervalNew.to', 99999, 0, 0, 1, NULL, 1, 1, 1, 'Horní limit rozsahu pro nové aliasy.');

commit;
