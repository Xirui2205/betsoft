ALTER TABLE `vic_admin`.`parameter`
 CHANGE `is_host` `is_host` SMALLINT( 3 ) NOT NULL COMMENT '0...not used, <>0...higher number=higher priority',
 CHANGE `is_branch` `is_branch` SMALLINT( 3 ) NOT NULL COMMENT '0...not used, <>0...higher number=higher priority',
 CHANGE `is_user` `is_user` SMALLINT( 3 ) NOT NULL  COMMENT '0...not used, <>0...higher number=higher priority',
 CHANGE `is_admin` `is_admin` SMALLINT( 3 ) NOT NULL COMMENT '0...not used, <>0...higher number=higher priority';
