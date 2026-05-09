INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1520', 1, 'Stems for branch profile (mantis:1056)');

CREATE  TABLE IF NOT EXISTS `vic_admin`.`stem` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(48) NOT NULL ,
  `admin_id` INT UNSIGNED NULL,
  `desc` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `i__stem__admin_id` (`admin_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `vic_admin`.`stem` ADD FOREIGN KEY ( `admin_id` ) REFERENCES `vic_admin`.`admin` (`admin_id`);


ALTER TABLE `vic_admin`.`branch` ADD `stem_id` INT UNSIGNED NULL ;
ALTER TABLE `vic_admin`.`branch` ADD INDEX `i__branch__stem_id` ( `stem_id` ) ;
ALTER TABLE `vic_admin`.`branch` ADD FOREIGN KEY (`stem_id`) REFERENCES `vic_admin`.`stem`(`id`);
ALTER TABLE `vic_admin`.`stem` ADD UNIQUE `u__stem__name` ( `name` );