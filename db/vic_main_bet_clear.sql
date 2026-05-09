-- smazat aliasy
TRUNCATE TABLE vic_main.`bet_free_alias`;
-- a znovu je naplnit
CALL fn_init_bet_free_alias();

truncate vic_admin.log;
truncate vic_main.tymy_sazky;
truncate vic_main.sazka_kurz;
truncate vic_main.sazka_kurz_aktualni;
truncate vic_main.sazky;
truncate vic_main.sazka_kombinace;
truncate vic_main.vyherci_sazky;

