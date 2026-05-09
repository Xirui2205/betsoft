START TRANSACTION;
DELETE FROM `vic_main`.`controller_convert` WHERE `c_id`>=50 AND `c_id`<=64;
INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`) VALUES
(50, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'vklad-vyber', 'transakce', 'Deposit', 'transactions'),
(50, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'deposit-withdraw', 'transactions', 'Deposit', 'transactions'),
(50, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'vklad-vyber-sk', 'transakce', 'Deposit', 'transactions'),
(51, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'vklad-vyber', 'zadost-o-vyber', 'Deposit', 'withdraw-request'),
(51, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'deposit-withdraw', 'withdrawal-request', 'Deposit', 'withdraw-request'),
(51, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'vklad-vyber-sk', 'ziadost-o-vyber', 'Deposit', 'withdraw-request'),
(52, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'vklad-kartou', 'Deposit', 'deposit-card'),
(52, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'deposit-card', 'Deposit', 'deposit-card'),
(52, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'vklad-kartou-sk', 'Deposit', 'deposit-card'),
(53, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'vklad-bankou', 'Deposit', 'deposit-bank'),
(53, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'deposit-bank', 'Deposit', 'deposit-bank'),
(53, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'vklad-bankou-sk', 'Deposit', 'deposit-bank'),
(54, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'vklad-na-pobocce', 'Deposit', 'deposit-branch'),
(54, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'deposit-branch', 'Deposit', 'deposit-branch'),
(54, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'vklad-na-pobocce-sk', 'Deposit', 'deposit-branch');
COMMIT;
