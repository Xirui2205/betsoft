INSERT INTO vic_session.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (3849, 1, 'sessions for mobile app');

--
-- Table structure for table `session_app`
--

CREATE TABLE IF NOT EXISTS `session_app` (
  `session_id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
