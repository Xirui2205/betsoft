INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1961', 1, 'new ticket game for ticke of month (mantis:1079)');

INSERT INTO `vic_main`.`ticket_game_campaign` (
`id` ,
`name` ,
`valid_from` ,
`valid_to` ,
`handler_class` ,
`event_dependent` ,
`visible`
)
VALUES (
2 , 'ticket_of_month', '2012-07-26 00:00:00', '2099-12-31 00:00:00', 'It6_Campaign_TicketGame_TicketOfMonth', '0', '1'
);
