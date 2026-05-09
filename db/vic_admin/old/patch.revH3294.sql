START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
('report.branchesDaily.to', 'pavel.heran@compbet.com,jiri.ulbrich@compbet.com,vladimir.holicka@compbet.com,tomas.polz@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány denní reporty všech poboček.'),
('report.branchesMonthly.to', 'info@compbet.com,jana.jonakova@compbet.com,martina.liscova@compbet.com,technik@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány měsíční reporty všech poboček.'),
('report.stemDaily.to', 'pavel.heran@compbet.com,jiri.ulbrich@compbet.com,vladimir.holicka@compbet.com,tomas.polz@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány denní reporty všech poboček.'),
('report.stemMonthly.to', 'info@compbet.com', 0, 0, 1, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány denní reporty všech poboček.');

commit;
