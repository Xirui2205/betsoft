START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1800', 1, 'error message (mantis:1082)');
 
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'error_csrf_token', NULL, 'Neplatný bezpečnostní token', '1', '');


commit;
