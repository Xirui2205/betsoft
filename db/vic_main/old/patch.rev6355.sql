START TRANSACTION;

DELETE FROM `vic_main`.`sport_result_text`;

INSERT INTO `vic_main`.`sport_result_text`(`sport_id`,`lang_id`,`ot`,`ap`) VALUES
(1001, 1, 'pp', 'pen'),
(1006, 1, 'pp', ''),
(1011, 1, 'pp', 'sn'),
(1015, 1, 'p', ''),
(1020, 1, 'pp', 'th'),
(1022, 1, 'pp', 'sn'),
(1025, 1, 'p', ''),
(1026, 1, 'pp', ''),
(1032, 1, 'pp', 'ph'),
(1035, 1, 'pp', 'pen'),
(1039, 1, 'p', ''),
(1043, 1, 'p', '');

COMMIT;
