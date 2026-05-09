START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4347', 1, 'Parametry pro narozeninovy a registracni bonus (mantis:147)');

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
NULL , 'web.registrationBonusAmount', '50', '', '', '', NULL , '1', '', '1', 'Bonus v Kč uživateli za kazdy rok od data registrace.'
);


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
NULL , 'web.birthdayBonusAmount', '50', '', '', '', NULL , '1', '', '1', 'Částka v Kč pro narozeninovy bonus'
);

COMMIT;
