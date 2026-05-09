ALTER TABLE `vic_main`.`ticket` ADD `mp_amount` DECIMAL( 10, 2 ) NULL AFTER `mp_win` ,
ADD `mp_win_amount` DECIMAL( 10, 2 ) NULL AFTER `mp_amount`;

START TRANSACTION;

UPDATE `vic_main`.`ticket` SET
`mp_win_amount` = CEIL(`castka`*`mp_win`)
WHERE `vyplacen`=1 AND `is_loss`=0;

COMMIT;
