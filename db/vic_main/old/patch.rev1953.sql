ALTER TABLE `vic_main`.`ticket`
	CHANGE `branch_id` `host_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ,
	CHANGE `collection_branch_id` `collection_host_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;

 
