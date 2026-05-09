ALTER TABLE `parameter` ADD `description` TEXT NOT NULL;
START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `description`) VALUES
(NULL, 'betradar.Bet.InitialStatus', '2', '0', '0', '0', '', '1', '', '2 => suspended, 0=>active'),
(NULL, 'betradar.Bet.Autoupdate.default', '1', '0', '0', '0', '', '1', '', 'výchozí stav aktualizování kurzu z betradaru'),
(NULL, 'betradar.Bet.BookmakerId', '1', '0', '0', '0', '', '1', '', 'autor sazky ktera prijde z betradaru'),
(NULL, 'betradar.Email.OnError', '1', '0', '0', '0', '', '1', '', 'posilat maily pri chybach'),
(NULL, 'betradar.Email.Always', '0', '0', '0', '0', '', '1', '', 'posilat email pri kazdem importu');
COMMIT;
