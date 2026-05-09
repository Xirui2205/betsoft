CREATE TABLE `vic_main`.`financial_transaction_handle_sequence` (
`handle` INT UNSIGNED NOT NULL ,
`user_id` INT UNSIGNED NULL ,
PRIMARY KEY ( `handle` , `user_id` )
) ENGINE = MYISAM ;

CREATE TABLE `vic_main`.`point_transaction_handle_sequence` (
`handle` INT UNSIGNED NOT NULL ,
`user_id` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `handle` , `user_id` )
) ENGINE = MYISAM ;

ALTER TABLE `vic_main`.`financial_transaction` ADD `handle` INT UNSIGNED NOT NULL AFTER `transaction_id`;

ALTER TABLE `vic_main`.`point_transaction` ADD `handle` INT UNSIGNED NOT NULL AFTER `transaction_id`;

DELIMITER ;;
CREATE PROCEDURE fn_generate_handles()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE hnd INT;
  DECLARE tid INT;
  DECLARE uid INT;
  DECLARE trs CURSOR FOR
    SELECT transaction_id, user_id FROM vic_main.financial_transaction;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
  
  
  OPEN trs;
  FETCH trs INTO tid,uid; 
  WHILE done = 0 DO
  
	SELECT Count(*) INTO hnd
        FROM vic_main.financial_transaction
        WHERE transaction_id < tid AND user_id = uid;

    UPDATE vic_main.financial_transaction SET handle = hnd
    WHERE transaction_id = tid;

    FETCH trs INTO tid,uid; 
  END WHILE;
  CLOSE trs;
END;;

DELIMITER ;

CALL fn_generate_handles;
DROP PROCEDURE fn_generate_handles;

DELIMITER ;;
CREATE PROCEDURE fn_generate_handles()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE hnd INT;
  DECLARE tid INT;
  DECLARE uid INT;
  DECLARE trs CURSOR FOR
    SELECT transaction_id, user_id FROM vic_main.point_transaction;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
  
  
  OPEN trs;
  FETCH trs INTO tid,uid; 
  WHILE done = 0 DO
  
	SELECT Count(*) INTO hnd
        FROM vic_main.point_transaction
        WHERE transaction_id < tid AND user_id = uid;

    UPDATE vic_main.point_transaction SET handle = hnd
    WHERE transaction_id = tid;

    FETCH trs INTO tid,uid; 
  END WHILE;
  CLOSE trs;
END;;

DELIMITER ;

CALL fn_generate_handles;
DROP PROCEDURE fn_generate_handles;


INSERT INTO vic_main.financial_transaction_handle_sequence (handle, user_id)
(SELECT (Max(handle)+1) AS handle, user_Id
FROM vic_main.financial_transaction
GROUP BY user_id);

INSERT INTO vic_main.point_transaction_handle_sequence (handle, user_id)
(SELECT (Max(handle)+1) AS handle, user_Id
FROM vic_main.point_transaction
GROUP BY user_id);

