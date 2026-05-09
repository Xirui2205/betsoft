INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4010', 1, 'Přidáno globální parametr pro spam bot časovač web.registration.FormTimer (mantis:0000113)');

INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`, `manually_inserted`) VALUES
(172, 'web.registration.FormTimer', '10', 0, 0, 0, NULL, 1, 0, 1, 'Počet sekund, kterév musí uživatel minimalne strávit vyplňováním formulaře. Pri rychlem vyplneni se jedna o bota. ', NULL);
