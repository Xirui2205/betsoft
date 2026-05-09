

UPDATE `vic_main`.`financial_transaction_type` SET `low_limit` = NULL ,
`high_limit` = '0.00' WHERE `financial_transaction_type`.`name` = 'other.ticket.payout-cash-mp';

