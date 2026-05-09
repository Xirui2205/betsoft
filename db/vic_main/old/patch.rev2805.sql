-- WARNING:
-- This patch can cause problems when applied on testing server,
-- because this patch is using fixed IDs for inserted rows, while at same time
-- on testing new translations can be added through admin (should be at least).
-- Complaints should be directed to original author of this patch.

ALTER TABLE `vic_main`.`uzivatel` ADD `block_time` DATETIME NULL AFTER `block_ip`;
ALTER TABLE `vic_main`.`preklady` CHANGE `index_pole` `index_pole` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';


START TRANSACTION;
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(2, 'auth_failed_banned', '', '', 0, 3545),
(1, 'auth_failed_banned', '', 'Váš účet je zakázán', 0, 3545),
(16, 'auth_failed_banned', '', '', 0, 3545),

(2, 'auth_failed_not_activated', '', '', 0, 3546),
(1, 'auth_failed_not_activated', '', 'Váš účet musí být aktivován na pobočce', 0, 3546),
(16, 'auth_failed_not_activated', '', '', 0, 3546),

(2, 'auth_failed_banned_self', '', '', 0, 3547),
(1, 'auth_failed_banned_self', '', 'Váš účet jste si nechali zablokovat', 0, 3547),
(16, 'auth_failed_banned_self', '', '', 0, 3547);
COMMIT;
