-- Neni jiste ze se cizi klic jmenuje takto, takze pripadne si ho dropnete sami.
-- Jde o to, ze prozatim by vic_main.bookmaker_ticket_prove_log.bookmaker_id nemel mit referenci,
-- (nyni odkazuje na vic_main.bookmaker.bookmaker_id), jinak bude zlobit schvalovani tiketu.
-- (Co bude s tabulkou dale se teprve ukaze...)
ALTER TABLE `vic_main`.`bookmaker_ticket_prove_log` DROP FOREIGN KEY `FK_bookmaker_ticket_prove_log_2`;
