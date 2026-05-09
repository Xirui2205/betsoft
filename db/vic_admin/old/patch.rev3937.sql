start transaction;
UPDATE `vic_admin`.`parameter` SET `description` = 'Výše vkladu na tiket od kterého se posílá alert o stornu.' WHERE `parameter`.`id` =82;
UPDATE `vic_admin`.`parameter` SET `description` = 'Emailové adresy na které jsou zasílány alerty (oddělené čárkou bez mezer)' WHERE `parameter`.`id` =16;
commit;
