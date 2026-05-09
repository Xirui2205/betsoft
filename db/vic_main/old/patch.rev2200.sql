CREATE TABLE IF NOT EXISTS `static_page_layout` (
`layout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`layout_name` varchar(100) NOT NULL,
`layout_file` varchar(100) NOT NULL,
PRIMARY KEY (`layout_id`),
UNIQUE KEY `layout_name` (`layout_name`),
UNIQUE KEY `layout_file` (`layout_file`)
)
ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


ALTER TABLE  `static_page` ADD  `layout_id` INT UNSIGNED NOT NULL DEFAULT  '1' AFTER  `template` ,ADD INDEX (  `layout_id` );
ALTER TABLE  `static_page` ADD FOREIGN KEY (  `layout_id` ) REFERENCES  `vic_main`.`static_page_layout` (`layout_id`);




START SESSION;

INSERT INTO `static_page_layout` (`layout_id`, `layout_name`, `layout_file`)
VALUES(
1, 'default', 'default.phtml'
),(
2, 'popup', 'popup.phtml'
);

INSERT INTO  `vic_main`.`static_page` (

`page_id` ,
`page_name` ,
`lang_id` ,
`template` ,
`layout_id`,
`visible` ,
`page_title` ,
`page_description` ,
`page_keywords` ,
`page_content`
)
VALUES (
16 ,  'terms-of-service',  '1',  'terms-of-service', '2',  '1',  'Obchodní podmínky',  'Obchodní podmínky určující vztah uživatel - BetService',  'smlouva, dohoda, podmínky',  '<p>Text obchodních podmínek, bla bla bla bla bla blabla.....</p>'
), (
17 ,  'terms-of-service',  '2',  'terms-of-service', '2',  '1',  'Terms of Service',  'Terms of service that define the terms of the relationship between client and BetService.',  'contract, agreemenet, conditions',  '<p>Text defining the actual terms of service: bla bla bla bla bla blabla.....</p>'
), (
18 ,  'terms-of-service',  '16',  'terms-of-service', '2',  '1',  'Obchodní podmínky (SK)',  'Obchodní podmínky určující vztah uživatel - BetService (SK)',  'smlouva, dohoda, podmínky (SK)', '<p>Text obchodních podmínek, bla bla bla bla bla blabla..... (SK)</p>'
);


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
`real_action`
)
VALUES (
'61',  '1',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'obchodni-podminky',  'index',  'static-page',  'terms-of-service'
), (
'61',  '2',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'terms-of-service',  'index',  'static-page',  'terms-of-service'
), (
'61',  '16',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'obchodni-podminky',  'index',  'static-page',  'terms-of-service'
);




COMMIT;
