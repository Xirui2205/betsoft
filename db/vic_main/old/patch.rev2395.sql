START TRANSACTION;

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
'62' , '1',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'promo',  'hra',  'static-page',  'promo-play'
), (
'62' ,'2',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'promo',  'play',  'static-page',  'promo-play'
), (
'62' ,'16',  '0',  '19',  '0',  '3', NULL , NULL , NULL , NULL ,  '',  'promo',  'hra',  'static-page',  'promo-play'
);


INSERT INTO  `vic_main`.`static_page` (
`page_id` ,
`page_name` ,
`lang_id` ,
`template` ,
`layout_id` ,
`visible` ,
`page_title` ,
`page_description` ,
`page_keywords` ,
`page_content`
)
VALUES (
'19',  'promo-play',  '1',  'promo-play',  '1',  '1',  'Play Promo Page CS',  'page desc promo play CS',  'keywords CS',  'promo page to be made by marketing - CS'
), (
'20',  'promo-play',  '2',  'promo-play',  '1',  '1',  'Play Promo Page ENG',  'page desc promo play ENG',  'keywords ENG',  'promo page to be made by marketing - ENG'
), (
'21',  'promo-play',  '16',  'promo-play',  '1',  '1',  'Play Promo Page SK',  'page desc promo play SK',  'keywords SK',  'promo page to be made by marketing - SK'
);

COMMIT;
