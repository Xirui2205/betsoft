START TRANSACTION;

INSERT INTO  `vic_admin`.`parameter` (
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
NULL ,  'homepage.liveCalendar.betCount',  '5',  '0',  '0',  '0',  '',  '0',  '0',  '1',  'Počet live sázek k zobrazení na kalendáři na HP.'
), (
NULL ,  'homepage.liveCalendarSmall.betCountPerSport',  '3',  '0',  '0',  '0',  '',  '0',  '0',  '1',  'Počet live sázek k zobrazeni v kalendari ve pravem sloupci pro kazdy sport.'
);

COMMIT;
