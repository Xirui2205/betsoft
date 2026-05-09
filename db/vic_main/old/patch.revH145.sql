START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H145', 1, 'New superhome banner (mantis:783)');

INSERT INTO `vic_main`.`banner` (
 `banner_id` ,
 `controller_id` ,
 `location_id` ,
 `lang_id` ,
 `title` ,
 `image_name` ,
 `url` ,
 `text` ,
 `target` ,
 `valid_from` ,
 `valid_to` ,
 `order`
) VALUES
 (NULL , '44', '3', '1', '', '', '', '', '_self', NULL , NULL , '0'),
 (NULL , '44', '3', '2', '', '', '', '', '_self', NULL , NULL , '0');

COMMIT;
