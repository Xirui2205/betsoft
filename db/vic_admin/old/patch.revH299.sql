START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H299', 1, 'new section for controller convert (mantis:824)');

SET NAMES utf8;


UPDATE `vic_admin`.`parameter` SET `is_editable` = '1' WHERE `parameter`.`name`  IN
	('ticket.max.bet.count.simple','ticket.max.bet.count.ako','ticket.max.bet.count.system',
	 'ticket.max.bet.count.maxikombi','ticket.max.group.count.maxikombi','ticket.forfeit.limit','ticket.cancelation.limit');

COMMIT;
