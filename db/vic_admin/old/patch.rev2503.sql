ALTER TABLE `vic_admin`.`branch` ADD `banned` DATE NULL ,
ADD INDEX ( `banned` );

UPDATE `vic_admin`.`branch` SET
`banned` = NULL;

