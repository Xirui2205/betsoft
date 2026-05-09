INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1273', 1, 'Handicaps type text notes set from under/over notes (mantis:877)');

CREATE TEMPORARY TABLE vic_main.tmp_typ_note(src_typ_id INT(10) UNSIGNED, dst_typ_id INT(10) UNSIGNED, PRIMARY KEY (src_typ_id, dst_typ_id)) Engine=InnoDb;

START TRANSACTION;

INSERT INTO vic_main.tmp_typ_note(src_typ_id, dst_typ_id) VALUES
 (20, 18), (20, 25),
 (125, 129), (126, 130), (127, 131), (128, 132),
 (133, 129), (134, 130), (135, 131), (136, 132),
 (157, 163), (158, 166), (159, 167);

INSERT INTO vic_main.typ_sport_note(typ_id, sport_id, text_note)
 SELECT dst_typ_id, sport_id, text_note FROM vic_main.tmp_typ_note t
 JOIN vic_main.typ_sport_note n ON t.src_typ_id=n.typ_id;

COMMIT;

DROP TABLE vic_main.tmp_typ_note;
