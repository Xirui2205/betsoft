start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'provision_paid_out', '', 'Provize vyplacena', 1, @a + 1),
(2, 'provision_paid_out', '', 'Provision paid out', 1, @a + 1),
(16, 'provision_paid_out', '', 'Provízia vyplatená', 1, @a + 1),
(1, 'provision_paid_out_no', '', 'Provize nevyplacena', 1, @a + 2),
(2, 'provision_paid_out_no', '', 'Provision paid out no', 1, @a + 2),
(16, 'provision_paid_out_no', '', 'Provízia nevyplatená', 1, @a + 2),
(1, 'date_activation', '', 'Datum aktivace', 1, @a + 3),
(2, 'date_activation', '', 'Date activation', 1, @a + 3),
(16, 'date_activation', '', 'Dátum aktivácie', 1, @a + 3),
(1, 'actual_no_paidout_provision', '', 'Aktuálně nevyplacené provize', 1, @a + 4),
(2, 'actual_no_paidout_provision', '', 'Actual no paid out provision', 1, @a + 4),
(16, 'actual_no_paidout_provision', '', 'Aktuálne nevyplatené provízie', 1, @a + 4);

commit;
