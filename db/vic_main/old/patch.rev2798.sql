START TRANSACTION;
DELETE FROM `vic_main`.`translate_missing` WHERE `translate_key`='must_be_logged_in';
DELETE FROM `vic_main`.`translate_pages` WHERE `translate_key`='must_be_logged_in';
DELETE FROM `vic_main`.`preklady` WHERE `index_pole`='must_be_logged_in';  
COMMIT;
