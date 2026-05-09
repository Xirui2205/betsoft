START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H5161', 0, 'preklad pro pobocky - format oteviraci doby (mantis:220)');

UPDATE `preklady` SET
text = 'Hodiny zadejte ve formátu xx:xx-xx:xx bez mezer. Jednotlivé intervaly doby otevření oddělte středníkem nebo čárkou (např: 09:00-12:30;13:00-21:00)'
where index_pole = 'branch_opening_hours_edit_description'
and lang_id = 1;

COMMIT;