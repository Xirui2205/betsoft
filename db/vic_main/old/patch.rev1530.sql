DELETE FROM `vic_main`.`controller_convert` WHERE `controller_convert`.`c_id` = 50 AND `controller_convert`.`lang_id` = 1;
DELETE FROM `vic_main`.`controller_convert` WHERE `controller_convert`.`c_id` = 51 AND `controller_convert`.`lang_id` = 1;
INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`) VALUES
(50, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'vklad-vyber', 'transakce', 'Deposit', 'transactions'),
(51, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'deposit-withdraw', 'transactions', 'Deposit', 'transactions'),
(52, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Transakce', 'vklad-vyber-sk', 'transakce', 'Deposit', 'transactions'),
(53, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'vklad-vyber', 'zadost-o-vyber', 'Deposit', 'withdraw-request'),
(54, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'deposit-withdraw', 'withdrawal-request', 'Deposit', 'withdraw-request'),
(55, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Žádost o výběr', 'vklad-vyber-sk', 'ziadost-o-vyber', 'Deposit', 'withdraw-request'),
(56, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'vklad-kartou', 'Deposit', 'deposit-card'),
(57, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'deposit-card', 'Deposit', 'deposit-card'),
(58, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad kartou', 'vklad-vyber', 'vklad-kartou-sk', 'Deposit', 'deposit-card'),
(59, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'vklad-bankou', 'Deposit', 'deposit-bank'),
(60, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'deposit-bank', 'Deposit', 'deposit-bank'),
(61, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad bankou', 'vklad-vyber', 'vklad-bankou-sk', 'Deposit', 'deposit-bank'),
(62, 1, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'vklad-na-pobocce', 'Deposit', 'deposit-branch'),
(63, 2, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'deposit-branch', 'Deposit', 'deposit-branch'),
(64, 16, 21, 4, 1, 3, NULL, NULL, NULL, NULL, 'Vklad na pobocce', 'vklad-vyber', 'vklad-na-pobocce-sk', 'Deposit', 'deposit-branch');
