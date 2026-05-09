CREATE TABLE `vic_admin`.`database_patch` (
 `id` INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
 `revision` VARCHAR(64) NOT NULL,
 `appplied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `not_reapplicable` BOOLEAN NULL DEFAULT 1 COMMENT 'Use NULL instead of zero value',
 `note` TEXT NULL DEFAULT NULL,
 CONSTRAINT `u__database_patch__revision_not_reapplicable` UNIQUE (`revision`, `not_reapplicable`)
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

DELIMITER ;;
CREATE PROCEDURE `vic_admin`.`fn_test_database_patch`(rev VARCHAR(64))
BEGIN
 DECLARE patchId INT;
 SELECT `id` INTO patchId FROM `database_patch` WHERE `revision`=rev;
 IF patchId IS NULL THEN
  SET @s = CONCAT('CALL `\'Database patch dependency ', rev, '\'`');
  PREPARE stmt FROM @s;
  EXECUTE stmt;
 END IF;
END;;
DELIMITER ;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7205', 1, 'Initial record');
