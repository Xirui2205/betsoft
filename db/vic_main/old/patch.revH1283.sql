START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1283', 1, 'New translation resources for distinguishBranchesAndInternet property');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'net_ticket_count', NULL, 'Počet tiketů (internet)', 0),
 (1, 'branch_ticket_count', NULL, 'Počet tiketů (pobočky)', 0),
 (1, 'net_ticket_amount', NULL, 'Přijaté tikety částka (internet)', 0),
 (1, 'branch_ticket_amount', NULL, 'Přijaté tikety částka (pobočky)', 0),
 (1, 'net_collect_ticket_count', NULL, 'Vyplacené tikety částka (internet)', 0),
 (1, 'distinguishBranchesAndInternet', NULL, 'Rozlišit pobočky a internet', 0),
 (1, 'branch_collect_ticket_count', NULL, 'Vyplacené tikety částka (pobočky)', 0);
 
COMMIT;
