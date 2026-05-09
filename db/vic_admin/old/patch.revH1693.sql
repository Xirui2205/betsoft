START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1693', 1, 'New parameter validation.ticket.ako.minimal.rate (mantis:1115)');

INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `is_editable`, `type`, `mandatory`, `description`) VALUES
 (130, 'validation.ticket.ako.minimal.rate', '1.1', 0, 0, 0, 0, 1, NULL, 1, 'Minimální kurz pro započítání příležitosti do AKO počtu při kontrole AKO příležitostí.');

COMMIT;
