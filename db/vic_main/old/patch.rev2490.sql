ALTER TABLE `vic_main`.`tickethash` DROP PRIMARY KEY,
 ADD PRIMARY KEY ( `tickethash` , `user_id` );
