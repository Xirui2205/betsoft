TRUNCATE TABLE `vic_main`.`uzivatel_im_data`;
TRUNCATE TABLE `vic_main`.`point_account`;
TRUNCATE TABLE `vic_main`.`webpay_order`;
TRUNCATE TABLE `vic_main`.`financial_transaction`;

TRUNCATE TABLE `vic_main`.`point_transaction`;
TRUNCATE `vic_main`.archive_cronjob_param;
TRUNCATE `vic_main`.archive_cronjob;
TRUNCATE `vic_main`.cronjob_param;
TRUNCATE `vic_main`.cronjob;
TRUNCATE `vic_main`.ticket_kurz;
TRUNCATE `vic_main`.tickethash_ticket;
TRUNCATE `vic_main`.tickethash;
TRUNCATE `vic_main`.ticket_combination;
TRUNCATE `vic_main`.ticket;
TRUNCATE `vic_main`.uzivatel;

START TRANSACTION;

INSERT INTO `vic_main`.`uzivatel` (`user_id`, `jmeno`, `prijmeni`, `nick`, `heslo`, `pohlavi`, `datum_narozeni`, `email`, `zeme_id`, `ulice`, `psc`, `telefon`, `mena_id`, `info`, `mobil`, `newsletter`, `vyber_status`, `posledni_prihlaseni`, `vyprseni_session`, `datum_registrace`, `individualni_max_vklad`, `zakazany`, `lang_id`, `misto`, `ucet_status`, `vyhernost`, `block`, `block_ip`, `vyhernost_game`, `bet_stats_win`, `bet_stats_lose_acc`, `bet_stats_lose_book`, `bet_total`, `win_ticket`, `lose_ticket`, `delete_ticket`, `num_bet_ticket`, `bet_total2`, `ticket_num`, `finance_rating`, `max_bet`, `book_info`, `self_excluded_until`, `osloveni`, `e_testovaci`, `block_play`, `castka_m`, `castka_w`, `datum_aktivace`, `branch_id`, `anonymous`, `watched`, `client_card_number`, `created_by_admin_id`, `allowed_by_admin_id`) VALUES
(1, 'Anonymní', 'Kasa 1', 'Anonymkasa1', 'e20d56f7a999432aa3262a81a3753e42', 'm', '0000-00-00', 'vesely@it6.cz', 3, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 'N/A ANONYMOUS', 8, 'N/A ANONYMOUS', 'N/A ANONYMOUS', 0, 0, '2009-07-01 00:00:00', 0, '2009-07-01 00:00:00', 0, 0, 1, 'N/A ANONYMOUS', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '1.0000', 1, 0, 0, 1, '1.0000', 1, 6, 100000, 'N/A ANONYMOUS', '0000-00-00 00:00:00', 'aas', 'ne', 0, '0.00', '0.00', NULL, 2, 0, 0, '', 0, 0),
(2, 'Petr', 'Šťastný', 'stastny', 'e20d56f7a999432aa3262a81a3753e42', 'm', '1978-09-13', 'stastny@it6.cz', 3, 'Perlovka 1', '11000', '777065536', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(3, 'Pavel', 'Klinger', 'klinger', 'e20d56f7a999432aa3262a81a3753e42', 'm', '1978-09-13', 'klinger@it6.cz', 3, 'Perlovka 1', '11000', '777065536', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(4, 'Filip', 'Veselý', 'vesely', 'e20d56f7a999432aa3262a81a3753e42', 'm', '1978-09-13', 'vesely@it6.cz', 3, 'Perlovka 1', '11000', '777065536', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(5, 'Martin', 'Bohal', 'bohal', 'e20d56f7a999432aa3262a81a3753e42', 'm', '1978-09-13', 'bohal@it6.cz', 3, 'Perlovka 1', '11000', '777065536', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),

(6, 'Testovací', 'Uživatel1', 'test1', '9c84dbb4d7c44736af151fca31fa9657', 'm', '1965-12-30', '1@compbet.com', 3, 'Test 1', '11000', '123456789', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(7, 'Testovací', 'Uživatel2', 'test2', '9c84dbb4d7c44736af151fca31fa9657', 'm', '1965-12-30', '2@compbet.com', 3, 'Test 1', '11000', '123456789', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(8, 'Testovací', 'Uživatel3', 'test3', '9c84dbb4d7c44736af151fca31fa9657', 'm', '1965-12-30', '3@compbet.com', 3, 'Test 1', '11000', '123456789', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(9, 'Testovací', 'Uživatel4', 'test4', '9c84dbb4d7c44736af151fca31fa9657', 'm', '1965-12-30', '4@compbet.com', 3, 'Test 1', '11000', '123456789', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0),
(10, 'Miroslav', 'Černý', 'cerny', '2456a23033040a42bf0322f3d86bf563', 'm', '1965-12-30', 'cerny@bump.cz', 3, 'Test 1', '11000', '123456789', 8, NULL, NULL, 1, 0, '0000-00-00 00:00:00', 0, '2010-01-29 17:07:21', 0, 0, 1, 'Praha 1', 1, NULL, 0, '', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', 0, 0, 0, 0, '0.0000', 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, '0.00', '0.00', '2010-09-28 18:21:09', 8, 0, 0, '', 0, 0);


INSERT INTO `vic_main`.`uzivatel_im_data` (`user_id`, `zustatek`, `zetony`, `dluh`, `zustatek_bonus`) VALUES
(1, '10000.000', '0.00', '0.0000', '0.00'),
(2, '10000.0200', '0.00', '0.0000', '0.00'),
(3, '10000.0200', '0.00', '0.0000', '0.00'),
(4, '10000.0200', '0.00', '0.0000', '0.00'),
(5, '10000.0200', '0.00', '0.0000', '0.00'),
(6, '10000.0200', '0.00', '0.0000', '0.00'),
(7, '10000.0200', '0.00', '0.0000', '0.00'),
(8, '10000.0200', '0.00', '0.0000', '0.00'),
(9, '10000.0200', '0.00', '0.0000', '0.00'),
(10, '10000.0200', '0.00', '0.0000', '0.00');

INSERT INTO `vic_main`.`point_account` (`id`, `user_id`, `point_type_id`, `balance`, `balance_get`, `balance_spend`, `balance_exchange`) VALUES
(NULL, 1, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 2, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 3, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 4, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 5, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 6, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 7, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 8, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 9, 1, '500.00', '0.00', '0.00', '0.00'),
(NULL, 10,1, '500.00', '0.00', '0.00', '0.00');

COMMIT;
