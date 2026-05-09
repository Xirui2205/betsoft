DROP TABLE IF EXISTS `vic_ut_main`.`controller_has_content`;
DROP TABLE IF EXISTS `vic_ut_main`.`content`;
DROP TABLE IF EXISTS `vic_ut_main`.`content_data`;


DROP TABLE IF EXISTS `vic_ut_main`.`banners`;
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
-- Constraints for dumped tables
--

--
-- Constraints for table `banners`
--
ALTER TABLE `vic_ut_main`.`banners`
  ADD CONSTRAINT `banners_ibfk_1` FOREIGN KEY (`controller_id`) REFERENCES `vic_ut_main`.`controller_convert` (`c_id`),
  ADD CONSTRAINT `banners_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `vic_ut_main`.`jazyky` (`lang_id`);

