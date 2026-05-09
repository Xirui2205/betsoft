INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7371', NULL, 'renaming betradar.ImportLog.queriedLanguage parameter');


UPDATE vic_admin.parameter SET name='betradar.queriedLanguage' WHERE name='betradar.ImportLog.queriedLanguage';
