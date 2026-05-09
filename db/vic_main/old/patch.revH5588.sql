CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO  `preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'1',  'documents', NULL ,  'Dokumenty',  '1',  ''
);

INSERT INTO  `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'1',  'confirm_delete_document', NULL ,  'Opravdu si přejete odstranit tento dokument ?',  '1',  ''
);