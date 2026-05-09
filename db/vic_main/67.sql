-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'reg_address_not_found','','Adresa nebyla dekódována, opravte prosím pole.',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'reg_address_not_complete','','Adresa není kompletní, opravte prosím pole.',0,(@idPrekladyMax := @idPrekladyMax+1));