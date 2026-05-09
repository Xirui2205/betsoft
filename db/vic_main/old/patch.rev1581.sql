USE vic_main;

ALTER TABLE `financial_transaction` ADD `balance` DECIMAL( 11, 2 ) NULL;
ALTER TABLE `financial_transaction` DROP `account_type`;
ALTER TABLE `financial_transaction_type` ADD `account_type` ENUM( 'user', 'branch', 'other' ) NOT NULL;
ALTER TABLE `financial_transaction_type` ADD `low_limit` DECIMAL( 11, 2 ) NULL , ADD `high_limit` DECIMAL( 11, 2 ) NULL;

START TRANSACTION;

UPDATE financial_transaction_type SET
account_type = 'branch'
WHERE name LIKE 'branch.%';

UPDATE financial_transaction_type SET
high_limit = 0,
low_limit = NULL
WHERE 
  name IN ('user.ticket.create-online',
         'branch.ticket.cancel-cash',
         'user.ticket.payout-storno',
         'user.withdraw.cash-branch',
         'user.withdraw.bank-transfer',
         'branch.ticket.create-online',
         'user.ticket.create-cache',         
         'branch.ticket.payout-cash',
         'branch.ticket.payout-online',
         'branch.user-deposit.cash',
         'branch.deposit');


UPDATE financial_transaction_type SET
high_limit = NULL,
low_limit = 0
WHERE 
  name IN ('branch.ticket.create-cash',
         'user.ticket.cancel-online',
         'user.ticket.payout-cash',
         'user.ticket.payout-online',
         'user.deposit.online-card',
         'user.deposit.branch-cash',
         'user.deposit.bank-transfer',
         'branch.ticket.cancel-online'
         'user.ticket.cancel-cash',
         'branch.ticket.payout-storno',
         'branch.user-deposit.cash',
         'branch.withdraw');                         

COMMIT;
