START TRANSACTION;

UPDATE `vic_admin`.`branch` SET banned = NULL WHERE banned = '0000-00-00';

COMMIT;
