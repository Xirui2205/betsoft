START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1318', NULL, 'Marking used client card numbers that are associated to users (mantis:1024)');

-- we cannot determine who and when used client card, so we use Internet as admin and date of registration

UPDATE vic_main.client_card_number_feed c
 JOIN vic_main.uzivatel u ON u.client_card_number=c.id
 SET c.used=u.datum_registrace, c.admin_id=1
 WHERE c.used IS NULL;

COMMIT;
