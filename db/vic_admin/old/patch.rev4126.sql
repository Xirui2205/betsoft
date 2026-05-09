START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`name`,`value`,`is_host`,`is_branch`,`is_user`,`is_admin`,`is_editable`,`type`,`mandatory`,`description`) VALUES
('ticket.max.bet.count.simple',50,0,0,0,0,0,NULL,1,'Maximální počet sázek na tiketu - multi-simple'),
('ticket.max.bet.count.ako',50,0,0,0,0,0,NULL,1,'Maximální počet sázek na tiketu - AKO'),
('ticket.max.bet.count.system',50,0,0,0,0,0,NULL,1,'Maximální počet sázek na tiketu - system'),
('ticket.max.bet.count.maxikombi',50,0,0,0,0,0,NULL,1,'Maximální počet sázek na tiketu - maxikombinátor'),
('ticket.max.group.count.maxikombi',50,0,0,0,0,0,NULL,1,'Maximální počet skupin maxikombinátoru (bez tutovky)');
COMMIT;
