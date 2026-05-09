START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
(93, 'ticket.cancelation.limit', '600', 1, 1, 0, NULL, 0, 1, 0, 'Pocet sekund kdy jde tiket vystornovat na pobocce.');


COMMIT;
