START TRANSACTION;
-- anonymni uzivatel pobocky KASA1
SET @u=13;
INSERT INTO `vic_main`.`uzivatel` (`user_id`, `branch_id`, `jmeno`, `prijmeni`, `nick`, `heslo`, `pohlavi`, `datum_narozeni`, `email`, `zeme_id`, `ulice`, `psc`, `telefon`, `mena_id`, `info`, `mobil`, `newsletter`, `vyber_status`, `posledni_prihlaseni`, `vyprseni_session`, `datum_registrace`, `individualni_max_vklad`, `zakazany`, `lang_id`, `misto`, `ucet_status`, `vyhernost`, `block`, `block_ip`, `vyhernost_game`, `bet_stats_win`, `bet_stats_lose_acc`, `bet_stats_lose_book`, `bet_total`, `win_ticket`, `lose_ticket`, `delete_ticket`, `num_bet_ticket`, `bet_total2`, `ticket_num`, `finance_rating`, `max_bet`, `book_info`, `self_excluded_until`, `osloveni`, `e_testovaci`, `block_play`, `castka_m`, `castka_w`, `datum_aktivace`, `anonymous`, `watched`, `client_card_number`, `created_by_admin_id`, `allowed_by_admin_id`) VALUES
(@u, 8, 'BRANCH KASA 1', 'BRANCH KASA 1', '', NULL, 'm', '0000-00-00', 'kasa1@it6.cz', 3, 'N/A', 'N/A', '666', 8, NULL, NULL, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 1, 'moje misto', 1, NULL, 0, '0', 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0, 0, 0, 0, 0.0000, 0, 6, 99999999, NULL, '0000-00-00 00:00:00', NULL, 'ne', 0, 0.00, 0.00, NULL, 1, 0, '', 0, 0);

INSERT INTO `vic_main`.`uzivatel_im_data` (`user_id`, `zustatek`, `zetony`, `dluh`, `zustatek_bonus`) VALUES
(@u, 0.0000, 0.00, 0.0000, 0.00);

INSERT INTO `vic_main`.`point_account` (`id`, `user_id`, `point_type_id`, `balance`, `balance_get`, `balance_spend`, `balance_exchange`) VALUES
(NULL, @u, 1, 100.00, 100.00, 0.00, 0.00);

INSERT INTO `vic_main`.`point_transaction` (`transaction_id`, `user_id`, `value`, `type_id`, `time`, `branch_id`, `ticket_id`, `note`, `balance`, `balance_get`, `balance_spend`, `balance_exchange`) VALUES
(NULL, @u, 100.00, 6, '2010-11-19 15:43:03', NULL, NULL, '', 100.00, 100.00, 0.00, 0.00);
COMMIT;
