START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H1898', 1, 'teamp players and autocomplete (mantis:777)');

SET NAMES utf8;

CREATE TABLE IF NOT EXISTS `team_player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `team_player`
  ADD CONSTRAINT `team_player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);


COMMIT;
