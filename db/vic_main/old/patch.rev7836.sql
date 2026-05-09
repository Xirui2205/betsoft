START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7836', NULL, 'Novy vernostni program oprava uctovani zruseni bodoveho tiketu. (mantis:566)');

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '324',
`to` = '548' WHERE `financial_transaction_type`.`name` = 'user.exchange-from-points-cancel';


COMMIT;
