-- fixed bad cronjob string for CleanUpBetradarImportLog
UPDATE `vic_main`.`cronjob` SET `exec_constantly_at`='24 2 * * *' WHERE `cronjob_id`=12;
