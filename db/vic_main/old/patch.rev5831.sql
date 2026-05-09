START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`type` ,
`mandatory` ,
`is_admin` ,
`is_editable` ,
`description`
)
VALUES (
NULL , 'ticket.forfeit.limit', '30', '0', '0', '0', NULL , '1', '0', '0', ''
);

COMMIT;

