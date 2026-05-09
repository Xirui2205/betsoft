START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (1102, 1, 'Changed value and description on pagination parameters');



UPDATE `vic_admin`.`parameter` SET `value` = '10',
`description` = 'Určuje počet záznamů na stránce "/vklad-vyber/transakce/". Může nabývat hodnoty: 0(vše), 10, 20 nebo 30. (see: common/library/It6/WsForm/Paginator.php)' WHERE `parameter`.`id` =59;

UPDATE `vic_admin`.`parameter` SET `value` = '10',
`description` = 'Určuje počet záznamů na stránce "/vklad-vyber/bodove-transakce/". Může nabývat hodnoty: 0(vše), 10, 20 nebo 30. (see: common/library/It6/WsForm/Paginator.php)' WHERE `parameter`.`id` =58;


COMMIT;
