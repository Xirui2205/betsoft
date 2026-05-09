--
-- Table structure for table `offergen_tiskopis`
--

CREATE TABLE IF NOT EXISTS `offergen_tiskopis` (
  `id_tiskopis` int(11) NOT NULL AUTO_INCREMENT,
  `id_typ_tiskopisu` int(11) NOT NULL,
  `parametry` varchar(100) DEFAULT NULL,
  `pozadavek` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pripraven` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pocet_stran` int(11) DEFAULT NULL,
  `jazyk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tiskopis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `offergen_tiskopis`
--


-- --------------------------------------------------------

--
-- Table structure for table `offergen_typ_tiskopisu`
--

CREATE TABLE IF NOT EXISTS `offergen_typ_tiskopisu` (
  `id_typ_tiskopisu` int(11) NOT NULL AUTO_INCREMENT,
  `typ_tiskopisu_nazev` varchar(30) NOT NULL DEFAULT '',
  `obnova1` int(11) DEFAULT NULL,
  `obnova2` int(11) DEFAULT NULL,
  `mazat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_typ_tiskopisu`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
