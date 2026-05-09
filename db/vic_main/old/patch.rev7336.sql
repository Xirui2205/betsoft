START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7336', null, 'new controller for deposit/index');
 

UPDATE vic_main.controller_convert SET real_controller='deposit', real_action='index' WHERE c_id=21;


COMMIT;
