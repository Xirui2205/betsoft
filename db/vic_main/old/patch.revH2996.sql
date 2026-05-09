START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H2996', 1, 'Zaokrouhlovani kurzu dle tabulky vyteznosti two_column_ratio');

CREATE TABLE IF NOT EXISTS `two_column_ratio` (
  `ratio` varchar(5) NOT NULL,
  `rate1` varchar(5) NOT NULL,
  `rate2` varchar(5) NOT NULL,
  PRIMARY KEY (`ratio`,`rate1`,`rate2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `two_column_ratio` (`ratio`, `rate1`, `rate2`) VALUES
('1.07', '1.6', '2.25'),
('1.07', '1.65', '2.15'),
('1.07', '1.7', '2.08'),
('1.07', '1.75', '2'),
('1.07', '1.8', '1.95'),
('1.07', '1.85', '1.9'),
('1.07', '1.87', '1.87'),
('1.08', '1.6', '2.2'),
('1.08', '1.65', '2.1'),
('1.08', '1.7', '2.05'),
('1.08', '1.75', '1.95'),
('1.08', '1.8', '1.9'),
('1.08', '1.85', '1.85'),
('1.09', '1.6', '2.15'),
('1.09', '1.65', '2.05'),
('1.09', '1.7', '2'),
('1.09', '1.75', '1.9'),
('1.09', '1.8', '1.87'),
('1.09', '1.83', '1.84'),
('1.1', '1.6', '2.1'),
('1.1', '1.65', '2'),
('1.1', '1.7', '1.95'),
('1.1', '1.75', '1.9'),
('1.1', '1.8', '1.84'),
('1.1', '1.82', '1.82'),
('1.11', '1.6', '2.05'),
('1.11', '1.65', '1.98'),
('1.11', '1.7', '1.9'),
('1.11', '1.75', '1.85'),
('1.11', '1.8', '1.8');

COMMIT;