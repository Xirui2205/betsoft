START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1702', NULL, 'Updated validation message ticket_er_13 for AKO check (mantis:1115)');

UPDATE `vic_main`.`preklady` SET `text` = 'AKO sázka minimum sázek (kurz {1}): {0}' WHERE `preklady`.`lang_id`=1 AND `preklady`.`index_pole` = 'ticket_er_13';
UPDATE `vic_main`.`preklady` SET `text` = 'AKO bet - min bet count (rate {1}): {0}' WHERE `preklady`.`lang_id`=2 AND `preklady`.`index_pole` = 'ticket_er_13';
UPDATE `vic_main`.`preklady` SET `text` = 'AKO bet - min bet count (kurz {1}): {0}' WHERE `preklady`.`lang_id`=16 AND `preklady`.`index_pole` = 'ticket_er_13';

COMMIT;
