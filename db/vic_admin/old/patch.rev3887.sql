START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `description`) VALUES
 (83, 'confirmation.ticket.time', '40', 0, 0, 0, NULL, 1, 0, 'Čas na schválení tiketu v sekundách'),
 (84, 'confirmation.ticket.timeMore', '90', 0, 0, 0, NULL, 1, 0, 'Prodloužený čas na schválení tiketu v sekundách');
COMMIT;
