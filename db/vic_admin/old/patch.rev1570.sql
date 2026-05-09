START TRANSACTION;

ALTER TABLE  `bank_account` DROP FOREIGN KEY  `bank_account_ibfk_1` ;
DROP TABLE  `currency`;

COMMIT;
