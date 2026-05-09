START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1714', 1, 'predictable passwords (mantis:1064)');
 
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'reg_e_msgsimusername', NULL, 'Heslo je podobné uživatelskému jménu', '1', '');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'reg_e_msgforbiddenpass', NULL, 'Použití tohoto hesla je zakázané', '1', '');

commit;
