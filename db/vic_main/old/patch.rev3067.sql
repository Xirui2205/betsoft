START TRANSACTION;

INSERT INTO `vic_main`.`financial_transaction_type`
	(`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`) 
VALUES
	(40, 'branch.ticket.renew-canceled-cash', 'Storno strona tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '211', NULL, '602', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0),
	(41, 'user.ticket.renew-canceled', 'Storno strona tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '324', NULL, '602', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0),
	(42, 'branch.ticket.renew-canceled-cash-mp', 'Storno strona manipulacniho poplatku tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '211', NULL, '602', 0, 0, 0, 'host', '0.00', NULL , 'never', NULL, NULL, 0, 0),
	(43, 'user.ticket.renew-canceled-mp', 'Storno strona manipulacniho poplatku tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '324', NULL, '602', 0, 0, 0, 'user', NULL, '0.00' , 'never', NULL, NULL, 0, 0);
	
	
COMMIT;

