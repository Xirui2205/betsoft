START TRANSACTION;

-- reverting patch.rev2491
UPDATE `vic_main`.`point_transaction_type` SET `low_limit` = '0.0', `high_limit` = NULL WHERE `point_transaction_type`.`name`='pointTicket.cancel';

-- new stuff
INSERT INTO `vic_main`.`point_transaction_type` (`id`, `name`, `kind`, `point_type_id`, `note`, `from`, `thru`, `to`, `debiting`, `low_limit`, `high_limit`) VALUES 
 (10, 'pointTicket.payout-storno', 'get', '1', 'Cancel point ticket payout', NULL , NULL , NULL , '1', NULL , '0.0');
UPDATE `vic_main`.`point_transaction_type` SET `kind` = 'spend' WHERE `point_transaction_type`.`name`='pointTicket.cancel';
UPDATE `vic_main`.`point_transaction_type` SET `debiting` = '1'
 WHERE `name` = 'manual';
UPDATE `vic_main`.`financial_transaction_type` SET `debiting` = '1'
 WHERE `name` IN ('user.ticket.payout-storno', 'branch.ticket.payout-storno', 'user.manual-balance');

COMMIT;
