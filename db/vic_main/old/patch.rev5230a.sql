-- after applying this SQL patch apply related PHP patch, that will update values in column `data`

ALTER TABLE `vic_main`.`ticket_combination` ADD `data` TEXT NOT NULL;
