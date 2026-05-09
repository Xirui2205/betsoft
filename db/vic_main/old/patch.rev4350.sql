-- ticket_er_21 was duplicate of ticket_er_9
UPDATE `vic_main`.`preklady` SET `text` = 'Maximální počet sázek na tiketu: {0}' WHERE `lang_id` =1 AND `index_pole` = 'ticket_er_9';
DELETE FROM `vic_main`.`translate_pages` WHERE `translate_key`='ticket_er_21';
DELETE FROM `vic_main`.`translate_missing` WHERE `translate_key`='ticket_er_21';
