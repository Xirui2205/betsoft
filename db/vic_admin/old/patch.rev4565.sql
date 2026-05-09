start transaction;
update vic_admin.`branch` set `branch_location_id`=1;

set foreign_key_checks=0;
TRUNCATE TABLE vic_admin.`branch_location`;
set foreign_key_checks=1;

INSERT INTO vic_admin.`branch_location` (`id`, `name`) VALUES
(1, 'Jihočeský kraj'),
(2, 'Jihomoravský kraj'),
(3, 'Královéhradecký kraj'),
(4, 'Karlovarský kraj'),
(5, 'Liberecký kraj'),
(6, 'Moravskoslezský kraj'),
(7, 'Olomoucký kraj'),
(8, 'Pardubický kraj'),
(9, 'Plzeňský kraj'),
(10, 'Středočeský kraj'),
(11, 'Ústecký kraj'),
(12, 'Kraj Vysočina'),
(13, 'Zlínský kraj'),
(14, 'Hlavní město Praha');

commit;
