ALTER TABLE `vic_admin`.`host` ADD `fingerprint` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
 ADD INDEX (`fingerprint`);
