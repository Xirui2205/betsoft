START TRANSACTION;
UPDATE `vic_admin`.`parameter` set `is_editable` = '1';
UPDATE `vic_admin`.`parameter` SET `is_editable` = '0' WHERE `parameter`.`name` ='betradar.Bet.InitialStatus';
COMMIT;
