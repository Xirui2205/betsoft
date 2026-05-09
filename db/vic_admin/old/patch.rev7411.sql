ALTER TABLE `vic_admin`.`branch`
ADD `info` TEXT NULL DEFAULT NULL ,
ADD `longitude` FLOAT NULL DEFAULT NULL ,
ADD `latitude` FLOAT NULL DEFAULT NULL;


ALTER TABLE `vic_admin`.`branch_location`
ADD `latitude` FLOAT NOT NULL ,
ADD `longitude` FLOAT NOT NULL;



START TRANSACTION;


INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7411', null, 'added "info", "longitude" and "latitide" cols to vic_admin.branch');


UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.145784',
`longitude` = '14.566955' WHERE `branch_location`.`id` =1;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.14219',
`longitude` = '16.725769' WHERE `branch_location`.`id` =2;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.426019',
`longitude` = '15.806122' WHERE `branch_location`.`id` =3;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.192726',
`longitude` = '12.762909' WHERE `branch_location`.`id` =4;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.75731',
`longitude` = '15.050812' WHERE `branch_location`.`id` =5;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.850381',
`longitude` = '17.967682' WHERE `branch_location`.`id` =6;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.814949',
`longitude` = '16.995392' WHERE `branch_location`.`id` =7;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.898173',
`longitude` = '16.146698' WHERE `branch_location`.`id` =8;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.614269',
`longitude` = '13.221588' WHERE `branch_location`.`id` =9;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.08182',
`longitude` = '14.479065' WHERE `branch_location`.`id` =10;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.593699',
`longitude` = '13.908234' WHERE `branch_location`.`id` =11;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.423481',
`longitude` = '15.723724' WHERE `branch_location`.`id` =12;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '49.215803',
`longitude` = '17.764435' WHERE `branch_location`.`id` =13;
UPDATE `vic_admin`.`branch_location` SET `latitude` = '50.071244',
`longitude` = '14.439354' WHERE `branch_location`.`id` =14;



COMMIT;
