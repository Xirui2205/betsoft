-- Here is query that tries to retrieve all rows, that will be inserted
-- you can use it to check if inserts will pass successfully or why they didn't.
-- You will have to merge data if needed.
-- SELECT * FROM `vic_main`.`preklady` WHERE `index_pole` IN (
-- 'ticket_created',
-- 'ticket_simple_short',
-- 'ticket_combi_short',
-- 'ticket_system_short',
-- 'ticket_maxicombinator_short',
-- 'ticket_paidout',
-- 'ticket_not_paidout',
-- 'tip',
-- 'result'
-- );

START TRANSACTION;

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_created', '', 'Tiket založen', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_created', '', 'Ticket created', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_created', '', 'Tiket založen', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_simple_short', '', 'Jednoduchá', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_simple_short', '', 'Simple', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_simple_short', '', 'Jednoduchá', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_combi_short', '', 'Kombinovaná', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_combi_short', '', 'Combined', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_combi_short', '', 'Kombinovaná', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_system_short', '', 'Systémová', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_system_short', '', 'System', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_system_short', '', 'Systémová', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_maxicombinator_short', '', 'Maxikombinátor', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_maxicombinator_short', '', 'Maxicombinator', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_maxicombinator_short', '', 'Maxikombinátor', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_paidout', '', 'Vyplacená', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_paidout', '', 'Paid out', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_paidout', '', 'Vyplacená', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'ticket_not_paidout', '', 'Nevyplacená', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'ticket_not_paidout', '', 'Not paid out', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'ticket_not_paidout', '', 'Nevyplacená', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'tip', '', 'Tip', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'tip', '', 'Tip', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'tip', '', 'Tip', 0, @tid);

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'result', '', 'Výsledek', 0);
SET @tid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (2, 'result', '', 'Result', 0, @tid);
INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`,`preklad_id`) VALUES
 (16, 'result', '', 'Výsledek', 0, @tid);

COMMIT;
