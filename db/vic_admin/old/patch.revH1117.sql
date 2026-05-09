START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)VALUES (1117, 1, 'Added parameter transaction.withdrawCash.autoconfirm.max');



INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`)
VALUES (127, 'transaction.withdrawCash.autoconfirm.max', '200', '0', '0', '1', NULL, '1', '0', '1', 'Určuje maximální částku, která bude při žádosti o výběr na pobočce schválena automaticky.');



COMMIT;