START TRANSACTION;

SET NAMES utf8;

INSERT INTO `parameter` (
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
`description` ,
`manually_inserted`
)
VALUES (NULL , 'send.Email.when.blocking.users', '0', '0', '0', '0', NULL , '1', '0', '1', 'Notifikace hráče emailem při zablokování', '0'),
(NULL , 'send.Email.when.unblocking.users', '0', '0', '0', '0', NULL , '1', '0', '1', 'Notifikace hráče emailem při odblokování', '0'),
(NULL , 'send.SMS.when.blocking.users', '0', '0', '0', '0', NULL , '1', '0', '1', 'Notifikace hráče sms při zablokování', '0'),
(NULL , 'send.SMS.when.unblocking.users', '0', '0', '0', '0', NULL , '1', '0', '1', 'Notifikace hráče sms při odblokování', '0');


INSERT INTO `sms_type` (
`id` ,
`name`
)
VALUES (NULL , 'SMS informace o blokování a odblokovaní uživatele');

COMMIT;