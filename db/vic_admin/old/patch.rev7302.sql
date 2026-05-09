START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7302', 1, 'New parameters for bank export KB (mantis:503)');

INSERT INTO `vic_admin`.`parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`) VALUES
 ('export.bank.account.kb.user', '43-9315490287/0100', 0, 0, 0, 0, 1, NULL, 1, 'Parametr pro export z KB, bank.účet pro výplaty hráčům'),
 ('export.bank.account.kb.host', '94-3513620217/0100', 0, 0, 0, 0, 1, NULL, 1, 'Parametr pro export z KB, bank.účet pro dotace poboček');

COMMIT;
