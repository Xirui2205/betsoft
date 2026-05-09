UPDATE vic_admin.branch SET handle = id;
ALTER TABLE  vic_admin.`branch` CHANGE  `handle`  `handle` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  vic_admin.`branch` ADD UNIQUE (
`handle`
);
