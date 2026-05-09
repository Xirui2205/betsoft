START TRANSACTION;

INSERT INTO `vic_main`.`point_transaction_type` (
`id` ,
`name` ,
`kind` ,
`point_type_id` ,
`note` ,
`from` ,
`thru` ,
`to` ,
`debiting` ,
`low_limit` ,
`high_limit`
)
VALUES (
'9' , 'manual', 'get', '1', '', NULL , NULL , NULL , '0', NULL , NULL
);

COMMIT;
