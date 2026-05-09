INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7332', 1, 'New parameter host.ban-all-in');

 INSERT INTO vic_admin.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `description`) VALUES
  (56, 'host.ban-all-in', '0', 0, 0, 0, NULL, 1, 0, 'Povolení(0)/zkázání(1) náběrů všech nesystémových hostů v systému');
