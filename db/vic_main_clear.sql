-- skript pro vymazani tiketu a pridruzenych dat pro potreby testovani

TRUNCATE archive_cronjob_param;
TRUNCATE archive_cronjob;
-- better use script vic_main_clean_cronjob.sql
-- TRUNCATE cronjob_param;
-- TRUNCATE cronjob;
TRUNCATE ticket_kurz;
TRUNCATE tickethash_ticket;
TRUNCATE tickethash;
TRUNCATE ticket_combination;
TRUNCATE ticket;

