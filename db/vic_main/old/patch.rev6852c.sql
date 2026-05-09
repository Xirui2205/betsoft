ALTER TABLE `vic_main`.`seo_url` ADD CONSTRAINT `u__seo_url__type_url` UNIQUE (`lang_id`,`type`,`url`);
