ALTER TABLE `vic_admin`.`parameter` ADD  `id_editable` BOOLEAN NOT NULL AFTER  `is_admin`;
ALTER TABLE `vic_admin`.`parameter` CHANGE  `id_editable`  `is_editable` TINYINT( 1 ) NOT NULL;
ALTER TABLE `vic_admin`.`parameter` CHANGE  `is_editable`  `is_editable` TINYINT( 1 ) NOT NULL DEFAULT  '1';
