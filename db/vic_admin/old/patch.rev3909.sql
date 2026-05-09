
-- !!! moved from vic_main/patch.rev3909.sql where it had bad DB identifier

ALTER TABLE  `vic_admin`.`host` CHANGE  `allowed`  `banned` DATETIME NULL DEFAULT NULL;
UPDATE `vic_admin`.`host` SET banned=NULL WHERE 1=1;
