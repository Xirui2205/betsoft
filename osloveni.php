/*
CREATE TABLE IF NOT EXISTS `uzivatel_osloveni` (
  `jmeno` varchar(50) DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_estonian_ci DEFAULT NULL,
  `osloveni` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

SELECT DISTINCT jmeno,email,anonymous FROM `uzivatel` WHERE anonymous = 0
