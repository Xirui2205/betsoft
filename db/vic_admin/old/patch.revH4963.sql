START TRANSACTION;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES 
('affiliatePartner.registration.timeLimit', 600, 0, 0, 0, NULL, 1, 1, 1, 'Časový limit pro registraci uživatele z affiliatu (vteřiny).');

commit;
