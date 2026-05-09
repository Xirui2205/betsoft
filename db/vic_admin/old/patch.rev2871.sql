START TRANSACTION;

UPDATE `vic_admin`.`parameter` SET `name` = 'pagination.Bet.count' WHERE `parameter`.`id` =54;
UPDATE `vic_admin`.`parameter` SET `description` = 'počet záznamů na stránce obrazovka sázek' WHERE `parameter`.`id` =54;

INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`is_admin` ,
`type` ,
`mandatory` ,
`description`
)
VALUES
( NULL , 'pagination.Ticket.count', '20', '0', '0', '0', '1', NULL , '1', 'počet záznamů na stránce tikety' ),
( NULL , 'pagination.PointTransaction.count', '20', '0', '0', '1', '0', NULL , '1', 'počet záznamů na stránce bodové transakce' ),
( NULL , 'pagination.Transaction.count', '20', '0', '0', '1', '0', NULL , '1', 'počet záznamů na stránce transakce' );

COMMIT;

