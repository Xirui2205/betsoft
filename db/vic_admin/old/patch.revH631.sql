INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (631, 1, 'adding parameter for generating time frame of todays offer');


INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`,`is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`)
VALUES (124, 'offer.today.timeFrame.end', '06:00:00', 0, 0, 0, NULL, 1, 0, 1, 'Čas v UTC kdy končí den pro potřeby generování dnešní nabídky. 00:00:00 = půlnoc, 01:00:00 = 1 hod po půlnoci následujícího dne.');
