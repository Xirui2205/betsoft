-- Tetnto skript zaroven otestuje rychlost vaseho pocitace. me to trvalo 3:09

ALTER TABLE `vic_main`.`sazky` ADD `alias` INT NOT NULL ,
ADD INDEX ( `alias` ); 

ALTER TABLE `vic_main`.`sazky` ADD `parent_id` INT UNSIGNED NULL ,
ADD INDEX ( `parent_id` ); 

CREATE TABLE vic_main.sazka_kombinace_temp AS (
  SELECT sazka1_id, sazka2_id, IF(S1.podtyp_id < S2.podtyp_id, S1.sazka_id, S2.sazka_id) AS parent_candidate_id 
  FROM sazka_kombinace
  LEFT JOIN vic_main.sazky S2 ON S2.sazka_id = sazka2_id
  LEFT JOIN vic_main.sazky S1 ON S1.sazka_id = sazka1_id
  WHERE kombinace_show = 1 AND kombinace_ticket = 0
  ORDER BY IF(S1.podtyp_id < S2.podtyp_id, S1.podtyp_id, S2.podtyp_id)
);

START TRANSACTION;

UPDATE vic_main.sazky S SET
parent_id = (
  SELECT parent_candidate_id
  FROM vic_main.sazka_kombinace_temp
  WHERE sazka1_id = S.sazka_id OR sazka2_id = S.sazka_id
  LIMIT 1
);

UPDATE vic_main.sazky SET
  parent_id = NULL
WHERE parent_id = sazka_id;

COMMIT;

DROP TABLE vic_main.sazka_kombinace_temp;

