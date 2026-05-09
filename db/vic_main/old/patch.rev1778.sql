ALTER TABLE  `financial_transaction`
CHANGE  `note`  `note` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE  `status`  `status` ENUM(  'ok',  'pending',  'canceled',  'pre-deposit',  'no-deposit' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
