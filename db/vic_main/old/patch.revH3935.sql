start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'Your zip code is not in the valid format.', '', 'Vaše poštovní směrovací číslo není v platném formátu.', 1, @a + 1),
(2, 'Your zip code is not in the valid format.', '', 'Your zip code is not in the valid format.', 1, @a + 1),
(16, 'Your zip code is not in the valid format.', '', 'Vaše poštové smerovacie číslo nie je v platnom formáte.', 1, @a + 1),
(1, 'Entered zipcode does not exist.', '', 'Zadané PSČ neexistuje.', 1, @a + 2),
(2, 'Entered zipcode does not exist.', '', 'Entered zipcode does not exist.', 1, @a + 2),
(16, 'Entered zipcode does not exist.', '', 'Zadané PSČ neexistuje.', 1, @a + 2),
(1, 'Zip code must be at least \'%minchars\' characters.', '', 'PSČ musí mít minimálně \'%minchars%\' znaků.', 1, @a + 3),
(2, 'Zip code must be at least \'%minchars\' characters.', '', 'Zip code must be at least \'%minchars\' characters.', 1, @a + 3),
(16, 'Zip code must be at least \'%minchars\' characters.', '', 'PSČ musí mať viac ako \'%minchars%\' znaky.', 1, @a + 3),
(1, 'Zip code must have no more then \'%maxchars%\' characters.', '', 'PSČ nesmí mít více než \'%maxchars%\' znaků.', 1, @a + 4),
(2, 'Zip code must have no more then \'%maxchars%\' characters.', '', 'Zip code must have no more then \'%maxchars%\' characters.', 1, @a + 4),
(16, 'Zip code must have no more then \'%maxchars%\' characters.', '', 'PSČ nesmie mať viac ako \'%maxchars%\' znakov.', 1, @a + 4);

commit;
