START TRANSACTION;

ALTER TABLE `mena`
CHANGE `mena_T` `mena_T` int(10) unsigned NULL,
CHANGE `mena_TA` `mena_TA` int(10) unsigned NULL,
CHANGE `mena_ADM` `mena_ADM` int(10) unsigned NULL,
COMMENT='';
INSERT INTO `mena` (`mena_text`) VALUES
('USD'),
('JPY'),
('BGN'),
('DKK'),
('GBP'),
('HUF'),
('LTL'),
('PLN'),
('RON'),
('SEK'),
('CHF'),
('NOK'),
('HRK'),
('RUB'),
('TRY'),
('AUD'),
('BRL'),
('CAD'),
('CNY'),
('HKD'),
('IDR'),
('ILS'),
('INR'),
('KRW'),
('MXN'),
('MYR'),
('NZD'),
('PHP'),
('SGD'),
('THB'),
('ZAR');

ALTER TABLE `mena_kurz`
CHANGE `kurz` `kurz` double(15,5) NOT NULL AFTER `mena_id`,
COMMENT='InnoDB free: 128000 kB';

/* výchozí měna EUR (2) */
ALTER TABLE `uzivatel`
CHANGE `mena_id` `mena_id` smallint(5) unsigned NOT NULL DEFAULT '2' AFTER `telefon`,
COMMENT='';

/* nastaveni cronu na každý 1. den v měsíci, 2. hodinu a 15. minutu */
SELECT @typeId := (MAX(type_id)+1) FROM cronjob_type;
SELECT @cronjobId := (MAX(cronjob_id)+1) FROM cronjob;
INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (@typeId, 'currencyRatesUpdate');
INSERT INTO `cronjob`
(`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES 
(@cronjobId,   @typeId,        NULL,     0,          0,              NULL,      NULL,          '15 2 1 * *');

/* pohled user_currency */
CREATE VIEW user_currency AS SELECT u.*, c.mena_text FROM uzivatel u JOIN mena c ON u.mena_id = c.mena_id;

/* smazání struktury z mantisu 8 (samostatná tabulka currency a nový atribut u uživatele) */
ALTER TABLE `uzivatel` DROP FOREIGN KEY `uzivatel_ibfk_1`;
ALTER TABLE `uzivatel`
DROP `currency_id`,
COMMENT='';
DROP TABLE currency;

/* pro případ samostatné tabulky currency:

ALTER TABLE `uzivatel` DROP FOREIGN KEY `uzivatel_ibfk_1`;
ALTER TABLE `uzivatel` ADD CONSTRAINT `uzivatel_ibfk_1` FOREIGN KEY (`currency_id`)
REFERENCES `vic_main`.`currency`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `currency`
ADD `rate` float(10,5) unsigned NOT NULL AFTER `iso`,
COMMENT='';

INSERT INTO `currency` (`iso`, `rate`) VALUES ('EUR', 1);
INSERT INTO `currency` (`iso`) VALUES
('USD'),
('JPY'),
('BGN'),
('CZK'),
('DKK'),
('GBP'),
('HUF'),
('LTL'),
('PLN'),
('RON'),
('SEK'),
('CHF'),
('NOK'),
('HRK'),
('RUB'),
('TRY'),
('AUD'),
('BRL'),
('CAD'),
('CNY'),
('HKD'),
('IDR'),
('ILS'),
('INR'),
('KRW'),
('MXN'),
('MYR'),
('NZD'),
('PHP'),
('SGD'),
('THB'),
('ZAR');
*/

COMMIT;