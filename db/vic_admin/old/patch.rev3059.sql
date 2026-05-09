START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `type`, `mandatory`, `description`) VALUES
(NULL , 'validation.ticket.minimal.stake', '1', '0', '0', '0', '0', '' , '1', 'Minimální vklad na tiket v centrální měně'),
(NULL, 'validation.ticket.maximal.stake', '10000', '0', '0', '0', '0', '', '1', 'Maximální vklad na tiket v centrální měně');
COMMIT;
