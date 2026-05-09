CREATE TABLE `vic_main`.`import_log_kb_best`(
 `kbi_id` VARCHAR(31) NOT NULL COLLATE ascii_general_ci PRIMARY KEY COMMENT 'ID polozky v ucetnim systemu KBI',
 `transaction_id` INT(11) UNSIGNED NOT NULL,
 `imported_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `admin_id` INT(10) UNSIGNED NOT NULL,
 `batch_name` VARCHAR(128) NOT NULL DEFAULT ''
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
