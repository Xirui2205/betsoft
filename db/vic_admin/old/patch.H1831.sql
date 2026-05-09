START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1831', 1, 'New parameter branch.allowed.app.versions (mantis:1135)');


INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`) VALUES
 (133, 'branch.allowed.app.versions', '', '0', '0', '0', '0', '1', '', '1', 'Povolené verze pobočkové aplikace. Formát jsou verze (např. 0.123) a nebo intervaly verzí (např. 0.123-4.56) oddělené čárkou či středníkem. Prázdné pokud není omezeno.');

COMMIT;
