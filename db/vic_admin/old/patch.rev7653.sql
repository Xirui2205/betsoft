START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7653', 1, 'Transaction confirmation Alert');
 
SET NAMES UTF8;

INSERT INTO `vic_admin`.`parameter` (`name`,`value`,`is_host`,`is_branch`,`is_user`,`type`,`mandatory`,`is_admin`,`is_editable`,`description`) VALUES
 ('confirmation.ticket.user.bet.history.count', '4', 0, 0, 0, null, 0, 0, 1, 'Maximální počet tiketů uživatele se sázkou (včetně nového), aby nový tiket se stejnou sázkou nešel do schvalovačky'),
 ('confirmation.ticket.user.bet.history.balance', '300', 0, 0, 0, null, 0, 0, 1, 'Maximální suma vkladů na tikety uživatele se sázkou (včetně nového), aby nový tiket se stejnou sázkou nešel do schvalovačky');

COMMIT;
