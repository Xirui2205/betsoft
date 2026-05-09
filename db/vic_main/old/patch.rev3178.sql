START TRANSACTION;

UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'about-company' WHERE `controller_convert`.`c_id` = 29 AND `controller_convert`.`lang_id` = 1;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'about-company' WHERE `controller_convert`.`c_id` = 29 AND `controller_convert`.`lang_id` = 2;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'about-company' WHERE `controller_convert`.`c_id` = 29 AND `controller_convert`.`lang_id` = 16;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'cooperation' WHERE `controller_convert`.`c_id` = 30 AND `controller_convert`.`lang_id` = 1;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'cooperation' WHERE `controller_convert`.`c_id` = 30 AND `controller_convert`.`lang_id` = 2;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'static-page', `real_action` = 'cooperation' WHERE `controller_convert`.`c_id` = 30 AND `controller_convert`.`lang_id` = 16;

INSERT INTO  `vic_main`.`static_page` (`page_id`, `page_name`, `lang_id`, `template`, `layout_id`, `visible`, `page_title`, `page_description`, `page_keywords`, `page_content`) VALUES
(NULL, 'about-company', 1, 'index', 1, 1, 'O společnosti', '', '', '<p>o společnosti text</p>'),
(NULL, 'about-company', 2, 'index', 1, 1, '', '', '', ''),
(NULL, 'about-company', 16, 'index', 1, 1, '', '', '', ''),
(NULL, 'cooperation', 1, 'index', 1, 1, 'Spolupráce', '', '', '<p>text spolupr&aacute;ce</p>'),
(NULL, 'cooperation', 2, 'index', 1, 1, '', '', '', ''),
(NULL, 'cooperation', 16, 'index', 1, 1, '', '', '', '');

COMMIT;
