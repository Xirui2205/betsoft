START TRANSACTION;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'email_change_pass_done', NULL, 'Vaše heslo bylo změněno na:\r\n\r\n{0}\r\n\r\n\r\nPokud budete mít zájem, můžete toto heslo změnit v nastavení vašeho profilu na stránkách www.compbet.com.\r\n\r\n\r\nS pozdravem a přáním dobré pohody\r\n\r\ntým BetService', 0, 3568),
(2, 'email_change_pass_done', NULL, 'Your password has been changed to:\r\n\r\n{0}\r\n\r\nIf you wish, you can change it in your account settings at www.compbet.com.\r\n\r\nWith wishes of best luck\r\n\r\nteam BetService', 0, 3568),
(16, 'email_change_pass_done', NULL, 'email translation for slovak', 0, 3568),
(1, 'email_change_pass_user_action_created', NULL, 'Dobrý den\r\n\r\nDne {0} v {1} jste si vyžádal/a zaslání hesla k doméně www.compbet.com.\r\n\r\nPro změnu hesla prosím navštivte tuto stránku:\r\n\r\n{2}/cs/uzivatel-akce?action={3}\r\n\r\n\r\nPokud nebudete automaticky přesměrován/a na tuto stránku po kliknutí na link, můžete jej vložit do adresového pole prohlížeče pomocí funkcí kopírovat a vložit.\r\n\r\nV případě, že jste o zaslání nového hesla nepožádal/a, můžete tento mail ignorovat.\r\n\r\n\r\nS pozdravem a přáním štěstí ve hře,\r\n\r\ntým BetService', 0, 3567),
(2, 'email_change_pass_user_action_created', NULL, 'Hello\r\n\r\nOn {0} at {1} you have requested to have you password for the domain www.compbet.com emailed to you.\r\n\r\nTo change your password, please visit this page:\r\n\r\n{2}/cs/uzivatel-akce?action={3}\r\n\r\nIf the above link can not be clicked, or if it is not working properly please do copy and paste it into the address bar of your web browser.\r\n\r\nIn case you have not requested to have your password emailed to you, you can ignore this email.\r\n\r\nWith wishes of rich winnings\r\n\r\nteam BetService', 0, 3567),
(16, 'email_change_pass_user_action_created', NULL, 'email translation to slovak missing', 0, 3567);

COMMIT;
