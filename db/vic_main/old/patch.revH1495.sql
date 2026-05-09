INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1495', 1, 'Translations and parameters (mantis:1049)');
INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1495', 1, 'Translations and parameters (mantis:1049)');


INSERT INTO `vic_main`.`preklady`
	(`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES (1, 'reset_too_many_attempts', NULL, 'Počet pokusů o reset hesla je za určitý čas limitován. Zkuste to prosím později.', 0);

INSERT INTO `vic_admin`.`parameter`
	(`name`,`value`,`is_host`,`is_branch`,`is_user`,`type`,`mandatory`,`is_admin`,`is_editable`,`description`)
VALUES ('resetPassword.time.limit', 1, 0, 0, 0, NULL, 1, 0, 1, 'Doba za kterou se scitaji pokusy o reset uzivatelskeho hesla z jedne ip adresy.');

INSERT INTO `vic_admin`.`parameter`
	(`name`,`value`,`is_host`,`is_branch`,`is_user`,`type`,`mandatory`,`is_admin`,`is_editable`,`description`)
VALUES ('resetPassword.attempts.limit', 3, 0, 0, 0, NULL, 1, 0, 1, 'Pocet pokusu o reset uzivatelskeho hesla za casovou jednotku.');

CREATE TABLE `vic_main`.`reset_password_attempts` (
  `ipv4` int(12) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
