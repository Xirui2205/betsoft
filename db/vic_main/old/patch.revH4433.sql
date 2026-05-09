INSERT INTO `vic_main`.`cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
'20', 'ActivationNewsletter'
);

INSERT INTO `vic_main`.`cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`attempts_max` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
)
VALUES (
NULL , '20', NULL , '0', '0', NULL , NULL , '30 8 * * *'
);

CREATE TABLE `newsletter_history` (
  `user_id` int(10) unsigned NOT NULL,
  `round` int(11) NOT NULL,
  `sent` datetime NOT NULL,
  `newsletter_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`round`,`newsletter_type_id`),
  KEY `newsletter_type_id` (`newsletter_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `newsletter_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `round` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `newsletter_types` (`id`, `name`, `round`) VALUES
(1, 'Doregistrace', 1);


ALTER TABLE `newsletter_history`
  ADD CONSTRAINT `newsletter_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
  ADD CONSTRAINT `newsletter_history_ibfk_2` FOREIGN KEY (`newsletter_type_id`) REFERENCES `newsletter_types` (`id`);
