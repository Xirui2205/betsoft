START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7438', null, 'CHanging name of action branch-detail -> branch-details');

UPDATE `vic_main`.`controller_convert` SET real_action='branch-details' WHERE c_id=89;

COMMIT;
