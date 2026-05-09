ALTER TABLE `vic_main`.`financial_transaction_type` ADD `from_sub` VARCHAR( 16 ) NULL AFTER `from`;
ALTER TABLE `vic_main`.`financial_transaction_type` ADD `thru_sub` VARCHAR( 16 ) NULL AFTER `thru`;
ALTER TABLE `vic_main`.`financial_transaction_type` ADD `to_sub` VARCHAR( 16 ) NULL AFTER `to`;

START TRANSACTION;

UPDATE `vic_main`.financial_transaction_type
SET
from_sub = '999',
thru_sub = '999',
to_sub = '999';

UPDATE `vic_main`.financial_transaction_type
SET
from_sub = '%hostId%'
WHERE `from` = '211';

UPDATE `vic_main`.financial_transaction_type
SET
to_sub = '%hostId%'
WHERE `to` = '211';

UPDATE `vic_main`.financial_transaction_type
SET
thru_sub = '%hostId%'
WHERE `thru` = '211';

COMMIT;
