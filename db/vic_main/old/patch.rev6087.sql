START TRANSACTION;

UPDATE `vic_main`.`cronjob_param` `p`
 JOIN `vic_main`.`cronjob` `j`
 ON `p`.`cronjob_id`=`j`.`cronjob_id` AND `j`.`cronjob_type`=1
 SET `p`.`param_name`='ConfirmPasswordChange'
 WHERE `p`.`param_name`='Emailtype1';

UPDATE `vic_main`.`cronjob_param` `p`
 JOIN `vic_main`.`cronjob` `j`
 ON `p`.`cronjob_id`=`j`.`cronjob_id` AND `j`.`cronjob_type`=1
 SET `p`.`param_name`='PasswordChanged'
 WHERE `p`.`param_name`='Emailtype2';

UPDATE `vic_main`.`cronjob_param` `p`
 JOIN `vic_main`.`cronjob` `j`
 ON `p`.`cronjob_id`=`j`.`cronjob_id` AND `j`.`cronjob_type`=1
 SET `p`.`param_name`='TicketResults'
 WHERE `p`.`param_name`='Emailtype3';

UPDATE `vic_main`.`cronjob_param` `p`
 JOIN `vic_main`.`cronjob` `j`
 ON `p`.`cronjob_id`=`j`.`cronjob_id` AND `j`.`cronjob_type`=1
 SET `p`.`param_name`='NewUserRegistration'
 WHERE `p`.`param_name`='EmailNewUserRegistration';

COMMIT;
