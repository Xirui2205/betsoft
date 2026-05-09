START TRANSACTION;

SET NAMES utf8;

ALTER TABLE `parameter_log` ADD `branch_id` INT( 10 ) NULL AFTER `param_id` 

COMMIT;