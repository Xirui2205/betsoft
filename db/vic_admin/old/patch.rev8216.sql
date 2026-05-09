START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8216', 1, 'New parameter (mantis:755)');

INSERT INTO `vic_admin`.`parameter` (
 `id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`
) VALUES
(121, 'confirmation.ticket.bet.risklimit.percent', '10', 0, 0, 0, 0, 1, NULL , 1, 'Kolik procent z risklimitu musí presáhnout risk amount sázky, aby šel tiket do schvalování.');

COMMIT;
