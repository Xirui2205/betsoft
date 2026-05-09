START TRANSACTION;

SET NAMES utf8;

CREATE TABLE IF NOT EXISTS `user_activation_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `user_id` int(10) NOT NULL,
  `admin_id` int(10) NOT NULL,
  `branch_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Preklady --
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES(1, 'user-provision', NULL , 'Provize pro obsluhu',  '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2,  'user-provision', NULL , 'Rewards for service',  '0', @pid);
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16,  'user-provision', NULL , 'Provize pre obsluhu',  '0', @pid);

COMMIT;
