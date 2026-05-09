INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7942', 1, 'New translations for ticket status filter (mantis:601)');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'ticket_status_not_collected', NULL, 'Výhra nevybrána', 0),
 (1, 'ticket_status_forfeited', NULL, 'Výhra propadla', 0);
