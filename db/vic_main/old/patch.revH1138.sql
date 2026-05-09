INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1138', 1, 'Ticket game support tables and initial data, i18n resources (mantis:976)');

CREATE TABLE `vic_main`.`ticket_game_campaign` (
 `id` SMALLINT(5) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
 `name` VARCHAR(128) NOT NULL COLLATE utf8_general_ci,
 `valid_from` DATETIME NOT NULL,
 `valid_to` DATETIME NOT NULL,
 `handler_class` VARCHAR(256) NOT NULL COLLATE utf8_general_ci,
 `event_dependent` BOOLEAN NOT NULL DEFAULT 0,
 `visible` BOOLEAN NOT NULL DEFAULT 1
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`ticket_game_result` (
 `game_id` SMALLINT(5) UNSIGNED NOT NULL,
 `ticket_id` BIGINT(20) UNSIGNED NOT NULL,
 `evaluation` DECIMAL(10,2) NOT NULL,
 PRIMARY KEY (`game_id`, `ticket_id`),
 CONSTRAINT `fk__ticket_game_result__game_id` FOREIGN KEY (`game_id`) REFERENCES `ticket_game_campaign`(`id`),
 CONSTRAINT `fk__ticket_game_result__ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket`(`ticket_id`)
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`ticket_game_event` (
 `game_id` SMALLINT(5) UNSIGNED NOT NULL,
 `udalost_id` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`game_id`, `udalost_id`),
 CONSTRAINT `fk__ticket_game_event__game_id` FOREIGN KEY (`game_id`) REFERENCES `ticket_game_campaign`(`id`),
 CONSTRAINT `fk__ticket_game_event__udalost_id` FOREIGN KEY (`udalost_id`) REFERENCES `udalost`(`udalost_id`)
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

START TRANSACTION;

INSERT INTO `vic_main`.`ticket_game_campaign`(`id`, `name`, `valid_from`, `valid_to`, `handler_class`, `event_dependent`, `visible`) VALUES
 (1, 'game_ipad_euro_2012', '2012-06-03 22:00:00', '2012-06-30 21:59:59', 'It6_Campaign_TicketGame_IPadEuro2012', 1, 1);

INSERT INTO  `vic_main`.`controller_convert` (
`c_id` ,
`lang_id` ,
`parent_id` ,
`poradi` ,
`zobrazeno` ,
`cols` ,
`left_side` ,
`right_side` ,
`title` ,
`description` ,
`text` ,
`req_controller` ,
`req_action` ,
`real_controller` ,
`real_action` ,
`after_login` ,
`actionless`
)
VALUES
(94,  1, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game', 'ajax', 'ticket-game', 'ajax', 'ticket-game', '1', '0'),
(94,  2, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game', 'ajax', 'ticket-game', 'ajax', 'ticket-game', '1', '0'),
(94, 16, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game', 'ajax', 'ticket-game', 'ajax', 'ticket-game', '1', '0'),
(95,  1, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game ticket', 'soutezni-tikety', 'index', 'bestplayer', 'ticket-game', '1', '1'),
(95,  2, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game ticket', 'game-tickets', 'index', 'bestplayer', 'ticket-game', '1', '1'),
(95, 16, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'Ticket game ticket', 'soutezni-tikety', 'index', 'bestplayer', 'ticket-game', '1', '1');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'game_ipad_euro_2012', NULL, 'Soutěž o iPad EURO 2012', 0),
 (2, 'game_ipad_euro_2012', NULL, 'Contest for iPad EURO 2012', 0),
 (16, 'game_ipad_euro_2012', NULL, 'Súťaž o iPad EURO 2012', 0),
 (1, 'ticket_game_ticket_info', NULL, 'Více...', 0),
 (2, 'ticket_game_ticket_info', NULL, 'More...', 0),
 (16, 'ticket_game_ticket_info', NULL, 'Viac...', 0),
 (1, 'ticket_game', NULL, 'Soutěž o tiket', 0),
 (2, 'ticket_game', NULL, 'Ticket contest', 0),
 (16, 'ticket_game', NULL, 'Súťaž o tiket', 0);

COMMIT;
