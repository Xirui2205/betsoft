START TRANSACTION;

SET NAMES utf8;

/* Email */
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'email_blocking_users_text', NULL , 'Dovolujeme si Vás informovat, že Váš účet "%value%" byl zablokován. S pozdravem Victora-Tip, a.s',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'email_blocking_users_text', NULL , 'We would like to inform you that your account "%value%" has been blocked. Regards Victor-Tip, a.s.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'email_blocking_users_text', NULL , 'Dovoľujeme si Vás informovať, že váš účet "%value%" bol zablokovaný. S pozdravom Victora-Tip, a.s',  '0', @pid);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'email_blocking_users_subject', NULL , 'Váš účet byl zablokován.',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'email_blocking_users_subject', NULL , 'Your account has been blocked.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'email_blocking_users_subject', NULL , 'Váš účet bol zablokovaný.',  '0', @pid);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'email_unblocking_users_text', NULL , 'Dovolujeme si Vás informovat, že Váš účet "%value%" byl odblokován. S pozdravem Victora-Tip, a.s',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'email_unblocking_users_text', NULL , 'We would like to inform you that your account "%value%" has been unblocked. Regards Victor-Tip, a.s.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'email_unblocking_users_text', NULL , 'Dovoľujeme si Vás informovať, že váš účet "%value%" bol odblokovaný. S pozdravom Victora-Tip, a.s',  '0', @pid);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'email_unblocking_users_subject', NULL , 'Váš účet byl odblokován.',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'email_unblocking_users_subject', NULL , 'Your account has been unblocked.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'email_unblocking_users_subject', NULL , 'Váš účet bol odblokovaný.',  '0', @pid);


/* SMS */
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'sms_blocking_users_text', NULL , 'Dovolujeme si Vas informovat, ze Vas ucet "%value%" byl zablokovan. S pozdravem Victora-Tip, a.s',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'sms_blocking_users_text', NULL , 'We would like to inform you that your account "%value%" has been blocked. Regards Victor-Tip, a.s.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'sms_blocking_users_text', NULL , 'Dovolujeme si Vas informovat, ze vas ucet "%value%" bol zablokovany. S pozdravom Victora-Tip, a.s',  '0', @pid);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'sms_unblocking_users_text', NULL , 'Dovolujeme si Vas informovat, ze Vas ucet "%value%" byl odblokovan. S pozdravem Victora-Tip, a.s',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'sms_unblocking_users_text', NULL , 'We would like to inform you that your account "%value%" has been unblocked. Regards Victor-Tip, a.s.',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'sms_unblocking_users_text', NULL , 'Dovolujeme si Vas informovat, ze vas ucet "%value%" bol odblokovany. S pozdravom Victora-Tip, a.s',  '0', @pid);

COMMIT;