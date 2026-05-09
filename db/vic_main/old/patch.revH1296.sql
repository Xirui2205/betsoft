INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1296', 1, 'Unique index for vic_main.controller_convert(req_controller,req_action,lang_id) (mantis:974)');


ALTER TABLE vic_main.controller_convert ADD UNIQUE INDEX `u__controller_convert__lang_req_ctl_req_action`(`req_controller`, `req_action`, `lang_id`);
