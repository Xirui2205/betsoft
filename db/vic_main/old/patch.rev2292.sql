ALTER TABLE  `vic_main`.`static_page_layout` ADD  `required_data` TEXT NOT NULL;
UPDATE  `vic_main`.`static_page_layout` SET  `required_data` =  'leftRightNews' WHERE  `static_page_layout`.`layout_id` =1;
