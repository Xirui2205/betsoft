START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
('report.dailyReport.to', 'jiri.ulbrich@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány celkové denní reporty.'),
('report.monthlyReport.to', 'jiri.ulbrich@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány celkové měsíční reporty.');

commit;
