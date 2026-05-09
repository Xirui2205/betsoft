INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H632', NULL, 'Privileges for offergen for table vic_admin.parameter (mantis:751)');


--code is environment dependant, uncomment lines as needed


--__FOR_TESTING_SERVER _SB5
--GRANT SELECT ON `vic_admin` .`parameter` TO 'offergen'@'localhost';
--FLUSH PRIVILEGES;

--__FOR_PRODUCTION_SERVERS_SB1_+_SB2
--GRANT SELECT ON `vic_admin` .`parameter` TO 'offergen'@'10.10.10.101';
--FLUSH PRIVILEGES;
--GRANT SELECT ON `vic_admin` .`parameter` TO 'offergen'@'10.10.10.102';
--FLUSH PRIVILEGES;
