START TRANSACTION;
INSERT INTO vic_main.udalost_betradar (udalost_id, betradar_udalost_id) 
SELECT U.udalost_id, U.betradar_udalost_id FROM vic_main.udalost U
WHERE U.betradar_udalost_id NOT IN 
(SELECT UB.betradar_udalost_id FROM vic_main.udalost_betradar UB WHERE UB.udalost_id = U.udalost_id);
COMMIT;
