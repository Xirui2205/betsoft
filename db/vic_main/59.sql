-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'homepage_live_bets_title','','LIVE Sázky',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_live_bets_text','','Stay in the game until the last second and win!',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_live_casino_title','','LIVE Kasino',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_live_casino_text','','Try your luck in our great online casino games!',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_poker_games_title','','POKER hry',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_poker_games_text','','Ultimate challenge: Grab your Freeroll tickets!',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_live_scores_title','','LIVE Výsledky',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_live_scores_text','','Will Tottenham be walking in a Wembley wonderland?',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_mobile_title','','Mobilní sázení',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_mobile_text','','Stay in the game until the last second and win!',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_bonus_title','','Bonusy',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_bonus_text','','Try your luck in our great online casino games!',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_games_title','','Hry',0,(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'homepage_games_text','','Ultimate challenge: Grab your Freeroll tickets!',0,(@idPrekladyMax := @idPrekladyMax+1));