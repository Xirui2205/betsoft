START TRANSACTION;

ALTER TABLE `sazky` ADD `alias_released_new` TINYINT( 1 ) NULL COMMENT 'priznak uvolneneho noveho aliasu';

COMMIT;
