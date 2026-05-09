START TRANSACTION;
INSERT INTO  `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES
('1',  'user_message', NULL ,  'Zpráva pro uživatele',  '0',  ''),
('2',  'user_message', NULL ,  'Message for client',  '0',  ''),
('16',  'user_message', NULL ,  '(sk-trans: user_message)',  '0',  ''),

('1',  'betservice_bets', NULL ,  'BetService &ndash; Sázky',  '0',  ''),
('2',  'betservice_bets', NULL ,  'BetService &ndash; Bets',  '0',  ''),
('16',  'betservice_bets', NULL ,  '(sk-trans: betservice_bets)',  '0',  ''),

('1',  'bets_results_canceled', NULL ,  'Storno výsledku sázky',  '0',  ''),
('2',  'bets_results_canceled', NULL ,  'Bet Result Canceled',  '0',  ''),
('16',  'bets_results_canceled', NULL ,  '(sk-trans: bets_results_canceled)',  '0',  ''),

('1',  'following_bet_canceled', NULL ,  'Byl stornován výsledek následující sázky',  '0',  ''),
('2',  'following_bet_canceled', NULL ,  'The result of the following bet was canceled',  '0',  ''),
('16',  'following_bet_canceled', NULL ,  '(sk-trans: following_bet_canceled)',  '0',  ''),

('1',  'debt', NULL ,  'Dluh',  '0',  ''),
('2',  'debt', NULL ,  'You owe:',  '0',  ''),
('16',  'debt', NULL ,  '(sk-trans: debt)',  '0',  ''),

('1',  'deduction', NULL ,  'Odečet',  '0',  ''),
('2',  'deduction', NULL ,  'Deduction',  '0',  ''),
('16',  'deduction', NULL ,  '(sk-trans: deduction)',  '0',  ''),

('1',  'copyright', NULL ,  'Všechna práva vyhrazena CompBet 2009',  '0',  ''),
('2',  'copyright', NULL ,  'All Rights Reserved CompBet 2009',  '0',  ''),
('16',  'copyright', NULL ,  '(sk-trans: copyright)',  '0',  ''),

('1',  'age_disclaimer', NULL ,  'Sázení je povoleno pouze osobám starším 18ti let.',  '0',  ''),
('2',  'age_disclaimer', NULL ,  'Betting only allowed for people over 18 years of age.',  '0',  ''),
('16',  'age_disclaimer', NULL ,  '(sk-trans: age_disclaimer)',  '0',  ''),

('1',  'new_user_registration', NULL ,  'registrace nového uživatele',  '0',  ''),
('2',  'new_user_registration', NULL ,  'New User Registration',  '0',  ''),
('16',  'new_user_registration', NULL ,  '(sk-trans: new_user_registration)',  '0',  ''),

('1',  'you_just_registered', NULL ,  'Právě jste se zaregistroval na servru compbet.com',  '0',  ''),
('2',  'you_just_registered', NULL ,  'You just registered at the server compbet.com',  '0',  ''),
('16',  'you_just_registered', NULL ,  '(sk-trans: you_just_registered)',  '0',  ''),

('1',  'your_username_is', NULL ,  'Vaše uživatelské jméno je',  '0',  ''),
('2',  'your_username_is', NULL ,  'Your username is',  '0',  ''),
('16',  'your_username_is', NULL ,  '(sk-trans: your_username_is)',  '0',  ''),

('1',  'registration_time', NULL ,  'Čas registrace',  '0',  ''),
('2',  'registration_time', NULL ,  'Registration Time',  '0',  ''),
('16',  'registration_time', NULL ,  '(sk-trans: registration_time)',  '0',  ''),

('1',  'win_ratio', NULL ,  'Výhernost',  '0',  ''),
('2',  'win_ratio', NULL ,  'Win Ratio',  '0',  ''),
('16',  'win_ratio', NULL ,  '(sk-trans: win_ratio)',  '0',  ''),

('1',  'regular_alarm', NULL ,  'Pravidelný Alarm',  '0',  ''),
('2',  'regular_alarm', NULL ,  'Regular Alarm',  '0',  ''),
('16',  'regular_alarm', NULL ,  '(sk-trans: regular_alarm)',  '0',  ''),

('1',  'critical_alarm', NULL ,  'Kritický Alarm',  '0',  ''),
('2',  'critical_alarm', NULL ,  'Critical Alarm',  '0',  ''),
('16',  'critical_alarm', NULL ,  '(sk-trans: critical_alarm)',  '0',  ''),

('1',  'frequent_transactions', NULL ,  'Časté Transakce',  '0',  ''),
('2',  'frequent_transactions', NULL ,  'Frequent Transactions',  '0',  ''),
('16',  'frequent_transactions', NULL ,  '(sk-trans: frequent_transactions)',  '0',  ''),

('1',  'ticket_created', NULL ,  'Ticket created',  '0',  ''),
('2',  'ticket_created', NULL ,  'Ticket Created',  '0',  ''),
('16',  'ticket_created', NULL ,  '(sk-trans: ticket_created)',  '0',  ''),

('1',  'user_id', NULL ,  'Id Uživatele',  '0',  ''),
('2',  'user_id', NULL ,  'User Id',  '0',  ''),
('16',  'user_id', NULL ,  '(sk-trans: user_id)',  '0',  ''),

('1',  'amount', NULL ,  'Částka',  '0',  ''),
('2',  'amount', NULL ,  'Amount',  '0',  ''),
('16',  'amount', NULL ,  '(sk-trans: amount)',  '0',  ''),

('1',  'odds_change', NULL ,  'Změna Kurzu',  '0',  ''),
('2',  'odds_change', NULL ,  'Odds Changed',  '0',  ''),
('16',  'odds_change', NULL ,  '(sk-trans: odds_change)',  '0',  ''),

('1',  'bet', NULL ,  'Sázka',  '0',  ''),
('2',  'bet', NULL ,  'Bet',  '0',  ''),
('16',  'bet', NULL ,  '(sk-trans: bet)',  '0',  ''),

('1',  'bookmaker', NULL ,  'Bookmaker',  '0',  ''),
('2',  'bookmaker', NULL ,  'Bookmaker',  '0',  ''),
('16',  'bookmaker', NULL ,  '(sk-trans: bookmaker)',  '0',  ''),

('1',  'newly_allowed_users', NULL ,  'Nově povolení uživatelé',  '0',  ''),
('2',  'newly_allowed_users', NULL ,  'Newly allowed users',  '0',  ''),
('16',  'newly_allowed_users', NULL ,  '(sk-trans: newly_allowed_users)',  '0',  ''),

('1',  'branch_funding', NULL ,  'Dotace poboček',  '0',  ''),
('2',  'branch_funding', NULL ,  'Branch Funding',  '0',  ''),
('16',  'branch_funding', NULL ,  '(sk-trans: branch_funding)',  '0',  ''),

('1',  'canceled_tickets', NULL ,  'Zrušené tikety',  '0',  ''),
('2',  'canceled_tickets', NULL ,  'Canceled Tickets',  '0',  ''),
('16',  'canceled_tickets', NULL ,  '(sk-trans: canceled_tickets)',  '0',  ''),

('1',  'ticket_count_on_branch', NULL ,  'Počty tiketů na pobočkách',  '0',  ''),
('2',  'ticket_count_on_branch', NULL ,  'No of tickets on branch',  '0',  ''),
('16',  'ticket_count_on_branch', NULL ,  '(sk-trans: ticket_count_on_branch)',  '0',  ''),

('1',  'banned_branches', NULL ,  'Zakázané pobočky',  '0',  ''),
('2',  'banned_branches', NULL ,  'Banned Branches',  '0',  ''),
('16',  'banned_branches', NULL ,  '(sk-trans: banned_branches)',  '0',  '');
COMMIT;
