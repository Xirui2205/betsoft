-- !!! tento patch byl ve vic_admin, ale patri do vic_main !!!

set foreign_key_checks=0;
start transaction;
truncate vic_main.financial_transaction_type;
INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`) VALUES
(1, 'branch.ticket.create-cash', 'Založení tiketu na pobočce za peníze. Anonymně i neanonymně.', '211', '%hostId%', NULL, '999', '602', '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', NULL),
(2, 'user.ticket.create', 'Zalozeni tiketu z uctu uzivatele (na pobocce i na internetu)', '324', '999', NULL, '999', '602', '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', NULL),
(3, 'branch.ticket.cancel-cash', 'Storno tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '602', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'host', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'POK', NULL),
(4, 'user.ticket.cancel', 'Storno tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '602', '999', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'WEB', NULL),
(6, 'user.ticket.payout', 'Vyplaceni necash tiketu.', '548', '999', '379', '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'WEB', 'Výplata sázenky: %ticketId%'),
(7, 'user.ticket.payout-cancel', 'Zruseni vyplaceni necash tiketu', '324', '999', '379', '999', '548', '999', 0, 0, 1, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', NULL),
(8, 'user.deposit.card', 'Klient dotuje hráčské konto kartou.', '378', '999', NULL, '999', '324', '999', 0, 1, 0, 'user', '200.00', '10000.00', 'always', '2.00', '0.10', 1, 1, 'BNK', NULL),
(9, 'user.deposit.cash', 'Vklad peněz v hotovosti na pobočce\r\n', '211', '%hostId%', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', '10000.00', 'never', NULL, NULL, 1, 0, 'POK', NULL),
(10, 'user.deposit.bank', 'Vklad penez bankovnim prevodem', '221', '999', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'BNK', NULL),
(11, 'user.withdraw.cash', 'Výběr uživatele na pobočce v hotovosti', '324', '999', NULL, '999', '379', '999', 1, 1, 0, 'user', '-5000.00', '-20.00', 'never', NULL, NULL, 1, 1, 'INT', NULL),
(12, 'user.withdraw.bank', 'Výběr uživatele bankovním převodem', '324', '999', NULL, '999', '379', '999', 1, 0, 0, 'user', '-5532.00', '-500.00', 'always', '3.00', '0.10', 1, 1, 'INT', NULL),
(16, 'other.ticket.collect-individual', 'Vyplata tiketu individualne v kancelari', '379', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0, NULL, NULL),
(17, 'branch.ticket.collect', 'Vyplata tiketu v hotovosti na pobocce', '379', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'host', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'POK', NULL),
(18, 'other.ticket.payout-cash', 'Oznaceni cash tiketu za vyhrany (nikoliv jeho vyplaceni na pobocce), tato transakce je jen pro ucetnictvi', '548', '999', NULL, '999', '379', '999', 0, 0, 0, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0, NULL, 'Výplata sázenky: %ticketId%'),
(19, 'other.ticket.payout-cash-cancel', 'Zruseni ozanceni cash tiketu za vyhrany. (tato transakce je pouze pro ucetnictvi)', '379', '999', NULL, '999', '548', '999', 0, 0, 1, 'other', '0.00', NULL, 'never', NULL, NULL, 0, 0, NULL, NULL),
(20, 'branch.user.deposit-cash', 'Vklad uzivatele v hotovosti na pobocce (transakce stran pobocky)', NULL, '999', NULL, '999', NULL, '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, NULL, NULL),
(21, 'branch.user.withdraw-cash', 'Vyber uzivatele v hotovosti na pobocce (transakce stran pobocky)', '379', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'host', NULL, NULL, 'never', NULL, NULL, 0, 0, 'POK', NULL),
(22, 'branch.deposit', 'Dotace pobocky', '211', '%hostId%', NULL, '999', '261', '999', 1, 1, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, NULL, NULL),
(23, 'branch.withdraw', 'Vyber penez z pobocky (opak dotace pobocky)', '261', '999', NULL, '999', '211', '%hostId%', 1, 0, 0, 'host', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'POK', NULL),
(24, 'fee.general', 'Poplatky jdoucí do výnosu', NULL, '999', NULL, '999', NULL, '999', 0, 0, 1, 'other', NULL, NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(25, 'user.exchange-from-points', 'Dotace penezniho konta uzivatele z konta bodoveho', '548', '999', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 1, 1, NULL, NULL),
(26, 'user.deposit.manual', 'Rucni dotace uctu uzivatele.', NULL, '999', NULL, '999', NULL, '999', 0, 0, 1, 'user', NULL, NULL, 'never', NULL, NULL, 0, 0, NULL, NULL),
(27, 'user.withdraw.manual', 'Rucni sebrani penez uzivateli.', NULL, '999', NULL, '999', NULL, '999', 0, 0, 1, 'user', NULL, NULL, 'never', NULL, NULL, 0, 0, NULL, NULL),
(28, 'other.ticket.forfeit', 'Propadnuti nevyzvednute vyhry', '379', '999', NULL, '999', '648', '999', 0, 0, 1, 'other', NULL, NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(29, 'branch.ticket.create-cash-mp', 'Manipulacni poplatek ze zalozeni tiketu na pobocce za penize. Anonymne i neanonymne.', '211', '%hostId%', NULL, '999', '602', '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', NULL),
(30, 'user.ticket.create-mp', 'Manipulacni poplatek ze zalozeni tiketu z uctu uzivatele (na pobocce i na internetu)', '324', '999', NULL, '999', '602', '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'INT', 'Manipulační poplatek'),
(31, 'branch.ticket.cancel-cash-mp', 'Storno manipulacniho poplatku tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '602', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'host', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'POK', NULL),
(32, 'user.ticket.cancel-mp', 'Storno manipulacniho poplatku tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '602', '999', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(33, 'user.ticket.payout-mp', 'Vyherni manipulacni poplatek pri vyplaceni necash tiketu.', '548', '999', '379', '999', '324', '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'INT', 'Výherní manipulační poplatek tiketu  %ticketId%'),
(34, 'user.ticket.payout-cancel-mp', 'Zruseni vyherniho manipulacniho poplatku pri zruseni vyplaceni necash tiketu', '324', '999', '379', '999', '548', '999', 0, 0, 1, 'user', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(35, 'other.ticket.collect-individual-mp', 'Vyherni manipulacni poplatek pri vyplate tiketu individualne v kancelari', '379', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'other', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', 'Výherní manipulační poplatek tiketu  %ticketId%'),
(36, 'branch.ticket.collect-mp', 'Vyherni manipulacni poplatek pri vyplate tiketu v hotovosti na pobocce', '379', '999', NULL, '999', '211', '%hostId%', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', 'Výherní manipulační poplatek tiketu  %ticketId%'),
(37, 'other.ticket.payout-cash-mp', 'Vyherni manipulacni poplatek pri oznaceni cash tiketu za vyhrany (nikoliv jeho vyplaceni na pobocce), tato transakce je jen pro ucetnictvi', '548', '999', NULL, '999', '379', '999', 0, 0, 0, 'other', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(38, 'other.ticket.payout-cash-cancel-mp', 'Storno vyherniho manipulacniho poplatku pri zruseni ozanceni cash tiketu za vyhrany. (tato transakce je pouze pro ucetnictvi)', '379', '999', NULL, '999', '548', '999', 0, 0, 1, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'INT', NULL),
(39, 'other.ticket.forfeit-mp', 'Propadnuti vitezneho manipulacniho poplatku pri propadnuti nevyzvednute vyhry', '379', '999', NULL, '999', '648', '999', 0, 0, 1, 'other', NULL, NULL, 'never', NULL, NULL, 0, 0, 'INT', NULL),
(40, 'branch.ticket.renew-canceled-cash', 'Storno storna tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '211', '%hostId%', NULL, '999', '602', '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', NULL),
(41, 'user.ticket.renew-canceled', 'Storno storna tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '324', '999', NULL, '999', '602', '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', NULL),
(42, 'branch.ticket.renew-canceled-cash-mp', 'Storno storna manipulacniho poplatku tiketu zalozeneho na poboce za penize (anonymniho i neanonymniho)', '211', '%hostId%', NULL, '999', '602', '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', NULL),
(43, 'user.ticket.renew-canceled-mp', 'Storno storna manipulacniho poplatku tiketu zalozeneho z uctu uzivatele (na pobocce i internetu)', '324', '999', NULL, '999', '602', '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', NULL);
commit;
set foreign_key_checks=1;
