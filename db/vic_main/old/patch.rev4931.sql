ALTER TABLE vic_main.`typ` ADD `offer_category_id` INT UNSIGNED NULL ;

update vic_main.typ set `offer_category_id`=2;
update vic_main.typ set `offer_category_id`=3 where typ_id = 23;
update vic_main.typ set `offer_category_id`=1 where typ_id IN (19,22,32,31);

CREATE TABLE IF NOT EXISTS vic_main.`offer_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

start transaction;

INSERT INTO vic_main.`offer_category` (`id`, `name`) VALUES
(1, 'hlavní'),
(2, 'hlavní + podpůrky'),
(3, 'přesné výsledky');


commit;
