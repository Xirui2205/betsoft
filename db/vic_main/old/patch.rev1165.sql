ALTER TABLE `vic_main`.`finacni_transakce` ADD `branch_id` INT UNSIGNED NULL ,
ADD `note` VARCHAR( 256 ) NOT NULL ;
ALTER TABLE `finacni_transakce` ADD INDEX ( `branch_id` ) ;
ALTER TABLE `vic_main`.`finacni_transakce` CHANGE `user_id` `user_id` INT( 11 ) UNSIGNED NULL ;

CREATE TABLE `vic_main`.`financial_transaction_type` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 64 ) NOT NULL ,
UNIQUE (
`name`
)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;


ALTER TABLE `vic_main`.`finacni_transakce` CHANGE `typ_platby` `typ_platby` INT( 4 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 vklad 2 vyber';
ALTER TABLE `vic_main`.`finacni_transakce` ADD INDEX ( `typ_platby` ) ;
ALTER TABLE `vic_main`.`finacni_transakce` ADD FOREIGN KEY ( `typ_platby` ) REFERENCES `vic_main`.`financial_transaction_type` (
`id`
);

ALTER TABLE `financial_transaction_type` ADD `note` TEXT NOT NULL ;

ALTER TABLE `finacni_transakce` CHANGE `datum` `datum` DATETIME NOT NULL ;

ALTER TABLE `finacni_transakce` DROP `metoda_id` ,
DROP `metoda_sub_id` ;



INSERT INTO `financial_transaction_type` (`id`, `name`, `note`) VALUES
(1, 'branch.ticket.create-cash', ''),
(2, 'user.ticket.create-online', ''),
(3, 'branch.ticket.cancel-cash', ''),
(4, 'user.ticket.cancel-online', ''),
(5, 'user.ticket.payout-cash', ''),
(6, 'user.ticket.payout-online', ''),
(7, 'user.ticket.payout-storno', ''),
(8, 'user.deposit.online-card', ''),
(9, 'user.deposit.branch-cash', ''),
(10, 'user.deposit.bank-transfer', ''),
(11, 'user.withdraw.cash-branch', ''),
(12, 'user.withdraw.bank-transfer', ''),
(13, 'branch.ticket.create-online', ''),
(14, 'branch.ticket.cancel-online', ''),
(15, 'user.ticket.create-cache', ''),
(16, 'user.ticket.cancel-cash', ''),
(17, 'branch.ticket.payout-cash', ''),
(18, 'branch.ticket.payout-online', ''),
(19, 'branch.ticket.payout-storno', 'Storno již proplaceného tiketu. (Lze delat jen online)'),
(20, 'branch.user-deposit.cash', ''),
(21, 'branch.user-withdraw.cash', ''),
(22, 'branch.deposit', ''),
(23, 'branch.withdraw', '');