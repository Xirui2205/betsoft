ALTER TABLE `vic_admin`.`parameter` CHANGE `name` `name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `value` `value` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`) VALUES
	('campaign.CreatePointTicket.minAmount', '100', 0, 0, 0, NULL, 1, 0),
	('campaign.CreatePointTicket.minCount', '3', 0, 0, 0, NULL, 1, 0),
	('campaign.CreatePointTicket.minRate', '1.5', 0, 0, 0, NULL, 1, 0),
	
	('campaign.CreateMoneyTicket.minAmount', '10', 0, 0, 0, NULL, 1, 0),
	('campaign.CreateMoneyTicket.minCount', '3', 0, 0, 0, NULL, 1, 0),
	('campaign.CreateMoneyTicket.minRate', '1.5', 0, 0, 0, NULL, 1, 0),
	('campaign.CreateMoneyTicket.pointRatio', '0.1', 0, 0, 0, NULL, 1, 0),
	('campaign.CreateMoneyTicket.pointOffset', '0', 0, 0, 0, NULL, 1, 0),
	
	('campaign.PreferenceRate.maxAmount', '500', 0, 0, 0, NULL, 1, 0),
	('campaign.PreferenceRate.minCount', '3', 0, 0, 0, NULL, 1, 0),
	('campaign.PreferenceRate.minRate', '7', 0, 0, 0, NULL, 1, 0),
	('campaign.PreferenceRate.cost5', '50', 0, 0, 0, NULL, 1, 0),
	('campaign.PreferenceRate.cost10', '150', 0, 0, 0, NULL, 1, 0),
	('campaign.PreferenceRate.cost15', '500', 0, 0, 0, NULL, 1, 0),
	
	('campaign.BranchVisit.minAmount', '10', 0, 0, 0, NULL, 1, 0),
	('campaign.BranchVisit.points', '3', 0, 0, 0, NULL, 1, 0);
COMMIT;

