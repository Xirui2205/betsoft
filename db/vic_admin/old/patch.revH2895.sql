START TRANSACTION;

ALTER TABLE `parameter` CHANGE `value` `value` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
('web.JqueryRangeSliderValues', '1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.8,1.9,2,2.1,2.2,2.3,2.4,2.5,2.6,2.7,2.8,2.9,3,3.1,3.2,3.3,3.4,3.5,3.6,3.7,3.8,3.9,4,5,6,7,8,9,10,30,50,100', 0, 0, 0, NULL, 1, 0, 1, 'Hodnoty pro posuvník rychlého filtru na kurzy. Hodnoty musí být oddělené čárkou, od nejmenší k největší z leva do prava. Oddělovač desetin je tečka.');

commit;
