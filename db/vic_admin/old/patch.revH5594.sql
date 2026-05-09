START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`, `manually_inserted`) VALUES 
('report.usersBalanceMonthly.to', 'vladimir.holicka@compbet.com,tomas.polz@compbet.com', 0, 0, 0, NULL, 1, 0, 1, 'Emailové adresy na které jsou zasílány měsíční reporty výhernosti uživatelů.', NULL),
('report.usersBalanceMonthly.to_bcc', 'jiri.ulbrich@compbet.com', 0, 0, 0, NULL, 1, 0, 1, 'Skryté emailové adresy na které jsou zasílány měsíční reporty výhernosti uživatelů.', NULL);

COMMIT;
