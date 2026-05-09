START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1871', 1, 'New parameter recaptcha.key.public + private (mantis:1057)');


INSERT INTO `vic_admin`.`parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`) VALUES
 ('recaptcha.key.public', '6LdneNQSAAAAAD01ZVnPygkgU4I43AE0cyIFp6ZQ', '0', '0', '0', '0', '1', '', '1', 'Verejny klic k recaptcha sluzbe');

INSERT INTO `vic_admin`.`parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`) VALUES
 ('recaptcha.key.private', '6LdneNQSAAAAAL9M60iGDsmyJplYpsu4gG61r0bN', '0', '0', '0', '0', '1', '', '1', 'Soukromy klic k recaptcha sluzbe');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES (1, 'reg_e_missingvalue', NULL, 'Povinná položka', '1');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES (1, 'missing captcha fields', NULL, 'Povinná položka', '1');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES (1, 'captcha value is wrong: incorrect-captcha-sol', NULL, 'Nesprávně opsáno', '1');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
VALUES (1, 'wrong_captcha', NULL, 'Nesprávně opsáno', '1');

COMMIT;
