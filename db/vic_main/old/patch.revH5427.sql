START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H5427', 1, 'newsletter type - Mobile app (mantis:261)');

INSERT INTO `newsletter_types` (`id`, `name`, `round`)
VALUES ('2', 'Mobilní aplikace', '1');

COMMIT;