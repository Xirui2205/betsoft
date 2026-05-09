INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('7959', 1, 'hide section 149');

UPDATE `vic_admin`.`sekce` SET zobrazeno='0' WHERE sekce_id='149';
