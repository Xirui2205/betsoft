START TRANSACTION;
UPDATE `vic_admin`.`sekce` SET `action` = 'withdrawal-request' WHERE `sekce`.`sekce_id` =225;
UPDATE `vic_admin`.`sekce_has_parent` SET `parent_id` = NULL,`sekce_order`=1000 WHERE `sekce_has_parent`.`sekce_id` = 254;
UPDATE `vic_admin`.`sekce_has_parent` SET `parent_id` = NULL,`sekce_order`=1001 WHERE `sekce_has_parent`.`sekce_id` = 255;
COMMIT;
