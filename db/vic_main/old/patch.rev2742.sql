ALTER TABLE  `uzivatel` ADD  `citizen_id` VARCHAR( 100 ) NOT NULL AFTER  `heslo`;

ALTER TABLE  `uzivatel` DROP INDEX  `Index_2`;

ALTER TABLE  `uzivatel` ADD UNIQUE (
`nick`
);

ALTER TABLE  `uzivatel` ADD UNIQUE (
`email`
);
