ALTER TABLE `vic_main`.`uzivatel`
 ADD COLUMN `entry_bonus_amount` DECIMAL (10,2) NULL DEFAULT NULL AFTER `entry_bonus_base`;

START TRANSACTION;
-- this is hack in fact, 2000 should be dynamic limit from global parameter
UPDATE vic_main.uzivatel SET entry_bonus_amount=LEAST(2000,entry_bonus_base) WHERE entry_bonus_base IS NOT NULL;
COMMIT;
