START TRANSACTION;

CREATE TABLE IF NOT EXISTS `incomplete_registration` (
  `session_id` varchar(50) NOT NULL,
  `jmeno` varchar(50) DEFAULT NULL,
  `prijmeni` varchar(60) NOT NULL DEFAULT '',
  `nick` varchar(20) NOT NULL DEFAULT '',
  `heslo` varchar(600) DEFAULT NULL,
  `citizen_id` varchar(100) NOT NULL,
  `datum_narozeni` date NOT NULL DEFAULT '0000-00-00',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telefon` varchar(30) DEFAULT NULL,
  `datum_registrace` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `area_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(108, 1, 24, 0, 1, 3, NULL, NULL, NULL, NULL, NULL, '', 'registrace', 'dalsi-krok', 'Registration', 'nextstep', 1, 0, 0),
(108, 2, 24, 0, 1, 3, NULL, NULL, NULL, NULL, NULL, '', 'registracia', 'dalsi-krok', 'Registration', 'nextstep', 1, 0, 0),
(108, 16, 24, 0, 1, 3, NULL, NULL, NULL, NULL, NULL, '', 'registration', 'nextstep', 'Registration', 'nextstep', 1, 0, 0);

set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'just_registered1', '', 'Pro dokončení Vašeho hráčského účtu navštivte jednu z našich <a href="/cs/pobocky">poboček</a>, kde Vám obsluha vytiskne vyplněný registrační formulář a zároveň aktivuje Váš účet.', 1, @a + 1),
(1, 'just_registered2', '', 'Pro ověření totožnosti si, prosím, vezměte s sebou občanský průkaz.', 1, @a + 2),
(1, 'just_registered3', '', 'Děkujeme Vám za registraci a přejeme hodně štěstí ve hře.', 1, @a + 3),
(1, 'register_nextstep', '', 'Registrace II. část', 1, @a + 4);

REPLACE INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'reg_info', '', 'Je zakazáné použivat uživatelské jméno, které je vulgární, rasistické nebo urážlivé. Takový účet bude zablokován.', 1, 10798);
REPLACE INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'register_reg', '', 'Registrace I. část', 1, 10735);
REPLACE INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'register_active', '', 'Aktivujte svůj účet', 1, 10731);
REPLACE INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'reg_error_isEmpty', '', 'Povinné položky jsou označeny hvězdičkou.', 1, 10799);

COMMIT;
