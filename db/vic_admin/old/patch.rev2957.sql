ALTER TABLE `vic_admin`.`branch_has_parameter` CHANGE `value` `value` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `type`, `mandatory`, `description`) VALUES (NULL, 'branch.Advert.ticketHeader', 'toto je globalni reklamni text na tiketu', '0', '1', '0', '0', '', '1', 'reklamní text na tiketu');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`branch_has_parameter` (`branch_id`, `parameter_id`, `value`) VALUES (2, @pid, 'reklamna na tiketu pro pobočku ID2');
COMMIT;
