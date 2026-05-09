START TRANSACTION;

UPDATE `vic_admin`.`parameter` SET `description` = 'Paremetr testovacího alertu, může být smazáno.' WHERE `parameter`.`id` =12;
UPDATE `vic_admin`.`parameter` SET `description` = 'Emailová adresa na kterou jsou zasílány alerty.' WHERE `parameter`.`id` =16;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální změna kurzu při které se již posílá alert RateChange.' WHERE `parameter`.`id` =17;
UPDATE `vic_admin`.`parameter` SET `description` = 'Mimimální vsazená částka při které se posílá alert TicketCreated.' WHERE `parameter`.`id` =18;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální výše výhernosti uživatele při které se řadí do alertu výhernost.' WHERE `parameter`.`id` =19;
UPDATE `vic_admin`.`parameter` SET `description` = 'Období za které je braná výhernost uživatele, pro alert výhernost.' WHERE `parameter`.`id` =20;
UPDATE `vic_admin`.`parameter` SET `description` = 'Počet transakcí při kterém se pošle alert Transaction.' WHERE `parameter`.`id` =22;
UPDATE `vic_admin`.`parameter` SET `description` = 'Časový interval za který se eviduje posílané transakce pro odeslání alertu Transaction.' WHERE `parameter`.`id` =23;
UPDATE `vic_admin`.`parameter` SET `description` = 'Počet bodů za aktivaci uživatele.' WHERE `parameter`.`id` =26;
UPDATE `vic_admin`.`parameter` SET `description` = 'Poměr v kterém se počítá limit pro výměnu bodů za peníze: $amountLimit = $amountOffset + $spendRatio * $point[''balanceSpend''] - $point[''balanceExchange''];' WHERE `parameter`.`id` =27;
UPDATE `vic_admin`.`parameter` SET `description` = 'Proměná použitá pro výpošet maximálního počtu bodu vyměnitelného za penize: $amountLimit = $amountOffset + $spendRatio * $point[''balanceSpend''] - $point[''balanceExchange''];' WHERE `parameter`.`id` =28;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimalní počet bodu za které lze vsadit bodový tiket.' WHERE `parameter`.`id` =29;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální počet příležitostí na bodovém tiketu.' WHERE `parameter`.`id` =30;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální kurz bodového tiketu.' WHERE `parameter`.`id` =31;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální částka, za kterou uživatel získá body za založení peněžního tiketu.' WHERE `parameter`.`id` =32;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální počet příležitostí na peněžním tiketu za který může získat uživatel bodový bonus.' WHERE `parameter`.`id` =33;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální kurz peněžního tiketu za který může uživatel získat bodový bonus.' WHERE `parameter`.`id` =34;
UPDATE `vic_admin`.`parameter` SET `description` = 'Poměr bodů které uživatel získá za vsazení tiketu. Body se počítají s potecionální výhry, brány v úvahu jsou pouze ako tikety.' WHERE `parameter`.`id` =35;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimalni body ziskane za zalozeni penizniho tiketu.' WHERE `parameter`.`id` =36;
UPDATE `vic_admin`.`parameter` SET `description` = 'Maximalní vszené částka, kdy může být uplatněn zvýhodněný kurz.' WHERE `parameter`.`id` =37;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální počet příležitostí na tiketu ana který může být uplatněn zvýhodněný kurz.' WHERE `parameter`.`id` =38;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální kurz tiketu na který může být uplatněn zvýhodněný kurz.' WHERE `parameter`.`id` =39;
UPDATE `vic_admin`.`parameter` SET `description` = 'Cena za 5% zvyhodněný kurz.' WHERE `parameter`.`id` =40;
UPDATE `vic_admin`.`parameter` SET `description` = 'Cena za 10% zvyhodněný kurz.' WHERE `parameter`.`id` =41;
UPDATE `vic_admin`.`parameter` SET `description` = 'Cena za 15% zvyhodněný kurz.' WHERE `parameter`.`id` =42;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimalní vsazená částka na tiketu, aby byl brán v úvahu jako návštěva pobočky.' WHERE `parameter`.`id` =43;
UPDATE `vic_admin`.`parameter` SET `description` = 'Body za navštívení pobočky (udělováno max 1 denně)' WHERE `parameter`.`id` =44;
UPDATE `vic_admin`.`parameter` SET `description` = 'Pokud je nenulový jsou všechny pobočky zakázány.' WHERE `parameter`.`id` =56;
UPDATE `vic_admin`.`parameter` SET `description` = 'Pokud je nenulový jsou zakázány všechny transakce navyšující zůstatek pobočky.' WHERE `parameter`.`id` =57;
UPDATE `vic_admin`.`parameter` SET `description` = 'Pokud je nenulový jsou zakázány všechny transakce ponižující zůstatek pobočky.' WHERE `parameter`.`id` =58;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu BranchBlackList' WHERE `parameter`.`id` =70;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu BranchTicketsCount' WHERE `parameter`.`id` =71;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu CanceledTickets' WHERE `parameter`.`id` =72;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu HostDeposits' WHERE `parameter`.`id` =73;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu MailAbstract' WHERE `parameter`.`id` =74;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu NewUsers' WHERE `parameter`.`id` =75;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu TicketCreated' WHERE `parameter`.`id` =76;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu Transaction' WHERE `parameter`.`id` =77;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu UserBlacklist' WHERE `parameter`.`id` =78;
UPDATE `vic_admin`.`parameter` SET `description` = 'Zakázání alertu Winratio' WHERE `parameter`.`id` =79;



COMMIT;

