start transaction;
UPDATE `vic_admin`.`sekce` SET `zobrazeno` = '0' WHERE `sekce`.`sekce_id` =144;
UPDATE `vic_admin`.`sekce` SET `nazev` = 'Vytvořit sázky' WHERE `sekce`.`sekce_id` =145;
commit;
