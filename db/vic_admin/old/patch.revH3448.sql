START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (
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
'branch.inspectTickets', '582', '0', '1', '0', NULL , '1', '1', '1', 'Vyplnit ID poboček, kterých tikety mají být zvírazněny při kontrole posledních vsazených tiketů. Vice pobocek zadavat oddeleno strednikama.'
);


commit;