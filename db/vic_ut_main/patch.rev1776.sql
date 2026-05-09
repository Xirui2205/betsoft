START TRANSACTION;
ALTER TABLE  `bookmaker_timesheet` DROP FOREIGN KEY  `bookmaker_timesheet_ibfk_1`;
ALTER TABLE  `bookmaker_timesheet` CHANGE  `bookmaker_id`  `admin_id` INT( 10 ) UNSIGNED NOT NULL;
RENAME TABLE  `bookmaker_timesheet` TO  `admin_timesheet`;
COMMIT;
