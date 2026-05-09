START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
('alert.TransactionMultiple.duration', '5', 0, 1, 1, NULL, 1, 0, 1, 'Časový interval za který se eviduje posílaní transakce pro odeslání alertu TransactionMultiple.'),
('alert.transactionMultiple.to', 'info@compbet.com,technik@compbet.com', 0, 1, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány alerty o vícenásobných výběrech.');

commit;
