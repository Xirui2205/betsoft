INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('104', '1', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, 'Zodpovedne sazeni', 'zodpovedne-sazeni', 'index', 'responsible-betting', 'index', '1', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('104', '2', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, 'Zodpovedne hranie', '', 'index', 'responsible-betting', 'index', '1', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('104', '16', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, 'Responsible betting', 'responsible-betting', 'index', 'responsible-betting', 'index', '1', '0', '0');

INSERT INTO `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'1', 'responsible_betting', NULL , 'Zodpovědné sázení', '1', ''
);

INSERT INTO `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'2', 'responsible_betting', NULL , 'Zodpovedné hranie', '1', ''
);

INSERT INTO `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'16', 'responsible_betting', NULL , 'Responsible betting', '1', ''
);
