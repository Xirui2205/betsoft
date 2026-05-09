DROP TABLE `vic_main`.`content`;
DROP TABLE `vic_main`.`content_data`;
DROP TABLE `vic_main`.`controller_has_content`;

DROP TABLE IF EXISTS `vic_main`.`banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `banner_id` int(20) NOT NULL AUTO_INCREMENT,
  `controller_id` mediumint(8) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `target` enum('_blank','_self') NOT NULL,
  PRIMARY KEY (`banner_id`),
  KEY `controller_id` (`controller_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `banners`
--

INSERT INTO `vic_main`.`banners` (`banner_id`, `controller_id`, `location_id`, `lang_id`, `title`, `image_name`, `url`, `text`, `target`) VALUES
(1, 44, 1, 1, 'title #1', 'test1.jpg', 'http://www.google.com', 'test text #1 cs', '_self'),
(2, 44, 2, 1, 'title #2', 'test5.jpg', 'seznam.cz', 'test text #2 cs', '_blank'),
(3, 44, 1, 2, 'title #1', 'test1.jpg', 'google.com', 'test banner #1 text eng', '_blank'),
(4, 44, 2, 2, 'title #2', 'test1.jpg', '', 'test banner #2 text eng', '_blank'),
(5, 44, 1, 16, 'title #1', 'test1.jpg', 'google.com', 'test #1 text sk', '_blank'),
(6, 44, 2, 16, 'title #2', 'test2.jpg', '', 'test #1 text sk', '_blank');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `banners`
--
ALTER TABLE `vic_main`.`banners`
  ADD CONSTRAINT `fk__banners__controller_id` FOREIGN KEY (`controller_id`) REFERENCES `vic_main`.`controller_convert` (`c_id`),
  ADD CONSTRAINT `fk__banners__lang_id` FOREIGN KEY (`lang_id`) REFERENCES `vic_main`.`jazyky` (`lang_id`);

