START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
('user.settingLimits.min', 100, 0, 0, 1, NULL, 1, 1, 1, 'minimální částka v nastavení uživatelského limitu (zodpovědné sázení)'),
('user.settingLimits.max', 1000000, 0, 0, 1, NULL, 1, 1, 1, 'maximální částka v nastavení uživatelského limitu (zodpovědné sázení)');

commit;
