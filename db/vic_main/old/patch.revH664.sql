START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('Zamezeni opakovanemu tisteni smlouvy', 1, '(mantis:917)');

ALTER TABLE `vic_main`.`uzivatel` ADD `can_print_agreement` TINYINT( 1 ) NOT NULL;


COMMIT;
