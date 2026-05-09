START TRANSACTION;
INSERT INTO `vic_main`.`offergen_typ_tiskopisu` (`typ_tiskopisu_nazev`, `obnova1`, `obnova2`, `mazat`) VALUES
('nabidka', 300, 600, 0),
('vysledky', 300, 600, 0);

-- uzivatel je na BBAS zalozen (pozadavek na podporu), urceno pro localhost nebo devel server
CREATE USER 'offergen'@'localhost' IDENTIFIED BY 'hahaGen842';
GRANT USAGE ON *.* TO 'offergen'@'localhost' IDENTIFIED BY 'hahaGen842' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
GRANT SELECT, EXECUTE ON `vic_main`.* TO 'offergen'@'localhost';
GRANT SELECT , INSERT , UPDATE , DELETE ON `vic_main`.`offergen_typ_tiskopisu` TO 'offergen'@'localhost';
GRANT SELECT , INSERT , UPDATE , DELETE ON `vic_main`.`offergen_tiskopis` TO 'offergen'@'localhost';

COMMIT;

FLUSH PRIVILEGES;
