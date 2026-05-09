INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7222', null,'Event seoUrl update');

DROP INDEX u__seo_url__type_url ON `vic_main`.`seo_url`;
