INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H2048', 1, 'nacitani oblasti po blocich (mantis:944)');

INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
(141, 'web.AreasCount', '5', 0, 0, 0, NULL, 1, 0, 1, 'Počet oblastí zobrazených v menu');

