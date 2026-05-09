START TRANSACTION;

INSERT INTO `vic_admin`.`parameter`
	(`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `description`)
VALUES
	(70, 'alert.BranchBlackList.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(71, 'alert.BranchTicketsCount.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(72, 'alert.CanceledTickets.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(73, 'alert.HostDeposits.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(74, 'alert.MailAbstract.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(75, 'alert.NewUsers.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(76, 'alert.TicketCreated.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(77, 'alert.Transaction.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(78, 'alert.UserBlacklist.disabled', '0', 0, 0, 0, NULL, 1, 0, ''),
	(79, 'alert.WinRatio.disabled', '0', 0, 0, 0, NULL, 1, 0, '');
	
COMMIT;
