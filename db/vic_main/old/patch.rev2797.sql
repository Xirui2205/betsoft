START TRANSACTION;

DELETE FROM `vic_main`.`translate_missing` WHERE `translate_key` LIKE '== N/A%' OR `translate_key` LIKE '== E%';
DELETE FROM `vic_main`.`translate_pages` WHERE `translate_key` LIKE '== N/A%' OR `translate_key` LIKE '== E%';

COMMIT;
