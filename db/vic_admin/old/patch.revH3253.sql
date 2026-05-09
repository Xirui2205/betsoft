START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES (3253, 1, 'Prepis controlleru v sekci pro limity');

SET NAMES utf8;

-- UPDATE SECTION 77 limits/index
-- ------------------------------------

UPDATE vic_admin.`sekce` SET `controller` = `limits`, `action` = `index` WHERE `sekce_id` = 77;
insert into vic_admin.`preklady`(`lang_id`, `index_pole`, `text`, `translate`) values(1, 'save', 'Uložit', 1);

COMMIT;
