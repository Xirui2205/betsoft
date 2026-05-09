
CREATE TABLE IF NOT EXISTS `approval_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='skupiny pro schvalovani bookmakerem' AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `approval_group_threshold`
--

CREATE TABLE IF NOT EXISTS `approval_group_threshold` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `approval_group_id` int(10) unsigned NOT NULL,
  `odd_upper_threshold` double(8,2) NOT NULL,
  `stake_lower_threshold` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_group_id` (`approval_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_group_threshold`
--
ALTER TABLE `approval_group_threshold`
  ADD CONSTRAINT `approval_group_threshold_ibfk_1` FOREIGN KEY (`approval_group_id`) REFERENCES `approval_group` (`id`);


ALTER TABLE `udalost` ADD `approval_group_id` INT( 10 ) UNSIGNED NULL ,
ADD INDEX ( `approval_group_id` ) ;


ALTER TABLE `sport` ADD `approval_group_id` INT( 10 ) UNSIGNED NULL ,
ADD INDEX ( `approval_group_id` ) ;
