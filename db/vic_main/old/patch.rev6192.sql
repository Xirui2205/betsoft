START TRANSACTION;

UPDATE `vic_main`.`cronjob_param` SET param_value='"LiveTicketNotConfirmed"' WHERE cronjob_id=13 AND param_name='name';

COMMIT;