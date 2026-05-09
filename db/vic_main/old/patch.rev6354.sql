ALTER TABLE `ticket_statistics_payout` ADD `won_count` INT NOT NULL ,
ADD `lost_count` INT NOT NULL;

ALTER TABLE `ticket_statistics_cashflow` ADD `won_count` INT NOT NULL ,
ADD `lost_count` INT NOT NULL;