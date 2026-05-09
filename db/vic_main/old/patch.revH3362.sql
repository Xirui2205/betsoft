start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'live_tickets', '', 'Live tikety', 1, @a + 1),
(2, 'live_tickets', '', 'Live tickets', 1, @a + 1),
(16, 'live_tickets', '', '', 1, @a + 1),
(1, 'unique_users', '', 'Unikátních uživatelů', 1, @a + 2),
(2, 'unique_users', '', 'Unique users', 1, @a + 2),
(16, 'unique_users', '', '', 1, @a + 2),
(1, 'daily_total_report', '', 'Denní celkový report', 1, @a + 3),
(2, 'daily_total_report', '', 'Daily total report', 1, @a + 3),
(16, 'daily_total_report', '', '', 1, @a + 3),
(1, 'daily_stem_report', '', 'Denní report kmene', 1, @a + 4),
(2, 'daily_stem_report', '', 'Daily stem report', 1, @a + 4),
(16, 'daily_stem_report', '', '', 1, @a + 4),
(1, 'monthly_stem_report', '', 'Měsíční report kmene', 1, @a + 5),
(2, 'monthly_stem_report', '', 'Monthly stem report', 1, @a + 5),
(16, 'monthly_stem_report', '', '', 1, @a + 5),
(1, 'daily_branch_report', '', 'Denní report poboček', 1, @a + 6),
(2, 'daily_branch_report', '', 'Daily branch report', 1, @a + 6),
(16, 'daily_branch_report', '', '', 1, @a + 6),
(1, 'monthly_branch_report', '', 'Měsíční report poboček', 1, @a + 7),
(2, 'monthly_branch_report', '', 'Monthly branch report', 1, @a + 7),
(16, 'monthly_branch_report', '', '', 1, @a + 7);

commit;
