START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4252', 1, 'Preklady pro obrazky (mantis:84)');

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'gallery_edit_ok',		'',	'Soubor byl úspěšně editován.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_delete_ok',		'',	'Soubor byl úspěšně vymazán.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_delete_error',		'',	'Nepodařilo se vymazat obrázek!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_select_file',		'',	'Musíte vybrat soubor',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_max_size_info',		'',	'Maximální velikost souboru je',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_allowed_formats',		'',	'Povolené formáty',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_file_exists',		'',	'Soubor již existuje.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_insert_ok',		'',	'Soubor byl úspěšně vložen.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_upload_error',		'',	'Nepodařilo se nahrát soubor!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'error',		'',	'Chyba',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_delete_ok',		'',	'Kategorie byla vymazaná.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_delete_error',		'',	'Kategorii se nepodařilo vymazat.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_insert_error',		'',	'Nepodarilo se provest dotaz: Vytvorit kategorii obrazku',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_insert_ok',		'',	'Kategorie byla vytvořena.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_count',		'',	'Počet záznamů',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_alt',		'',	'Zvolte alternativní text do obrázku',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_delete',		'',	'Vymazat',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_really_delete',		'',	'Opravdu vymazat?',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_search',		'',	'Vyhledat',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_file_name',		'',	'Název souboru',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_file_description',		'',	'Popis',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_file',		'',	'Soubor',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_alt_text',		'',	'Alternativní text',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_files',		'',	'Obrázky a objekty',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category',		'',	'Kategorie',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_edit',		'',	'Editovat',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_delete',		'',	'Vymazat kategorii',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_really_delete',		'',	'Opravdu smazat kategorii?',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_new',		'',	'Nová kategorie',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_category_create',		'',	'Vytvořit',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'gallery_image_add',		'',	'Přidat obrázek',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;