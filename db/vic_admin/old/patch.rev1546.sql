-- Replace '<insert-your-email-here>' to email where to send alerts
START TRANSACTION;
INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`) VALUES
(NULL, 'alert.to', <insert-your-email-here>, 0, 0, 0, NULL, 1),
(NULL, 'alert.RateChange.limit', '1', 0, 0, 0, NULL, 1),
(NULL, 'alert.TicketCreated.limit', '40', 0, 0, 0, NULL, 1);
COMMIT;

