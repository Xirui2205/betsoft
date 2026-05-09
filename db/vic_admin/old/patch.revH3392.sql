START TRANSACTION;

UPDATE `vic_admin`.`parameter` SET `name` = 'report.totalReport.to', `parameter`.`description` = 'Emailové adresy na které jsou zasílány celkové denní a měsíční reporty.' WHERE `parameter`.`name` = 'report.dailyReport.to' LIMIT 1;
UPDATE `vic_admin`.`parameter` SET `name` = 'report.totalReport.to_bcc', `parameter`.`description` = 'Skryté emailové adresy na které jsou zasílány celkové denní a měsíční reporty.' WHERE `parameter`.`name` = 'report.monthlyReport.to' LIMIT 1;

commit;
