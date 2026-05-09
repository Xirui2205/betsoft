start transaction;

UPDATE `vic_admin`.`sekce` SET `nazev` = 'client_cards' WHERE `sekce`.`sekce_id` = 334;

commit;
