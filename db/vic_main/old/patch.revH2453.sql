START TRANSACTION;

UPDATE `vic_main`.`client_card_number_feed` SET branch_id=NULL WHERE branch_id=0;

COMMIT;