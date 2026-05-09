START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1714', 1, 'predictable passwords (mantis:1064)');

INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
(137 , 'password.forbidden', 'password;qwerty;abc123;welcome;forbal;hokej;sparta;slavia;betservice;111111;123456;123456789;', '0', '0', '1', NULL , '1', '', '1', 'Zakázaná hesla'),
(138 , 'password.maxSimChars', '3', '0', '0', '1', NULL , '1', '', '1', 'Maximální počet povolených shodných znaků uživatelského jména a hesla');

commit;
