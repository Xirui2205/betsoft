START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.exchange-from-points' WHERE `financial_transaction_type`.`id` =25;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.create' WHERE `financial_transaction_type`.`id` =2;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'branch.ticket.collect' WHERE `financial_transaction_type`.`id` =17;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.cancel' WHERE `financial_transaction_type`.`id` =4;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Zalozeni tiketu na pobocce za penize. Anonimne i neanonymne.' WHERE `financial_transaction_type`.`id` =1;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Zalozeni tiketu z uctu uzivatele (na pobocce i na internetu)' WHERE `financial_transaction_type`.`id` =2;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Strono tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)' WHERE `financial_transaction_type`.`id` =3;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Strono tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)' WHERE `financial_transaction_type`.`id` =4;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Dotace pobocky' WHERE `financial_transaction_type`.`id` =22;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vyber penez z pobocky (opak dotace pobocky)' WHERE `financial_transaction_type`.`id` =23;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.withdraw.bank' WHERE `financial_transaction_type`.`id` =12;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.deposit.bank' WHERE `financial_transaction_type`.`id` =10;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.deposit.card' WHERE `financial_transaction_type`.`id` =8;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.withdraw.cash' WHERE `financial_transaction_type`.`id` =11;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.deposit.cash' WHERE `financial_transaction_type`.`id` =9;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vklad penez uzivatele na pobocce.' WHERE `financial_transaction_type`.`id` =9;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'branch.user.withdraw-cash' WHERE `financial_transaction_type`.`id` =21;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'branch.user.deposit-cash' WHERE `financial_transaction_type`.`id` =20;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vyplata tiketu v hotovosti na pobocce' WHERE `financial_transaction_type`.`id` =17;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vyber uzivatele v hotovosti na pobocce (transakce stran pobocky)' WHERE `financial_transaction_type`.`id` =21;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vklad uzivatele v hotovosti na pobocce (transakce stran pobocky)' WHERE `financial_transaction_type`.`id` =20;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Dotace penezniho konta uzivatele z konta bodoveho' WHERE `financial_transaction_type`.`id` =25;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vklad penez bankovnim prevodem' WHERE `financial_transaction_type`.`id` =10;
UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.deposit.manual',
`note` = 'Rucni dotace uctu uzivatele.' WHERE `financial_transaction_type`.`id` =26;

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`) VALUES
(27, 'user.withdraw.manual', 'Rucni sebrani penez uzivateli.', NULL, NULL, NULL, 0, 0, 1, 'user', NULL, NULL, 'never', NULL, NULL, 0, 0);

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`) VALUES
(28, 'other.ticket.forfeit', 'Propadnuti nevyzvednute vyhry', NULL, NULL, NULL, 0, 0, 1, 'other', NULL, NULL, 'never', NULL, NULL, 0, 0);

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'other.ticket.payout-cash',
`note` = 'Oznaceni cash tiketu za vyhrany (nikoliv jeho vyplaceni na pobocce), tato transakce je jen pro ucetnictvi' WHERE `financial_transaction_type`.`id` =18;

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.payout-cancel',
`note` = 'Zruseni vyplaceni necash tiketu' WHERE `financial_transaction_type`.`id` =7;

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'other.ticket.payout-cash-cancel',
`note` = 'Zruseni ozanceni cash tiketu za vyhrany. (tato transakce je pouze pro ucetnictvi)',
`account_type` = 'other' WHERE `financial_transaction_type`.`id` =19;

UPDATE `vic_main`.`financial_transaction_type` SET `account_type` = 'other' WHERE `financial_transaction_type`.`id` =18;

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.payout' WHERE `financial_transaction_type`.`id` =6;

UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Vyplaceni necash tiketu.' WHERE `financial_transaction_type`.`id` =6;

DELETE FROM `vic_main`.`financial_transaction`
WHERE type_id NOT IN(22,23,25,1,2,17,4,24,8,12,11,6,7,3,20,21,9,10,26,27,28,18,19);

DELETE FROM `vic_main`.`financial_transaction_type`
WHERE id NOT IN(22,23,25,1,2,17,4,24,8,12,11,6,7,3,20,21,9,10,26,27,28,18,19);


INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`) VALUES
(16, 'other.ticket.collect-individual', 'Vyplata tiketu individualne v kancelari', '379', NULL, '211', 0, 0, 0, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0);

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '324',
`thru` = '379', `to` = '' WHERE `financial_transaction_type`.`id` =7;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '548',
`to` = '379' WHERE `financial_transaction_type`.`id` =18;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '379',
`to` = '548' WHERE `financial_transaction_type`.`id` =19;

UPDATE `vic_main`.`financial_transaction_type` SET`from`='379', `to` = '648' WHERE `financial_transaction_type`.`id` =28;

UPDATE `vic_main`.`financial_transaction_type` SET `to` = '211' WHERE `financial_transaction_type`.`id` =16;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '548',
`to` = '324' WHERE `financial_transaction_type`.`id` =25;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '548',
`to` = '324' WHERE `financial_transaction_type`.`id` =26;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '324',
`to` = '602' WHERE `financial_transaction_type`.`id` =27;

UPDATE `vic_main`.`financial_transaction_type` SET `late_deposit` = '1' WHERE `financial_transaction_type`.`id` =11;

UPDATE `vic_main`.`financial_transaction_type` SET `need_confirm` = '1',
`late_deposit` = '1' WHERE `financial_transaction_type`.`id` =22;

UPDATE `vic_main`.`financial_transaction_type` SET `need_confirm` = '1',
`late_deposit` = '1' WHERE `financial_transaction_type`.`id` =23;


COMMIT;
