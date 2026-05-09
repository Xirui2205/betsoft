-- doplnění zemí
-- nenalezeno použití atributů zeme_T, zeme_TA, zeme_ADM
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_AX', '1', 'AX');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_BL', '1', 'BL');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_BQ', '1', 'BQ');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_CW', '1', 'CW');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_GG', '1', 'GG');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_IM', '1', 'IM');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_JE', '1', 'JE');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_MF', '1', 'MF');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_PS', '1', 'PS');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_SS', '1', 'SS');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_SX', '1', 'SX');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_TL', '1', 'TL');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_UA', '1', 'UA');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_XK', '1', 'XK');
INSERT INTO `zeme` (`zeme_allow`, `nazev`, `zobrazeno`, `kod`) VALUES('1', 'zeme_ZM', '1', 'ZM');

-- přenastavení zakázaných zemí (USA, Kanada, Belgie)
UPDATE `zeme` SET zeme_allow = 1, zobrazeno = 1;
UPDATE `zeme` SET zeme_allow = 0, zobrazeno = 0 WHERE kod IN('US', 'CA', 'BE');

-- duplicitní země s kódem CH, neex. země
DELETE FROM `zeme` WHERE `zeme_id` = '245';
DELETE FROM zeme WHERE kod='XX';

-- telefonní předvolby do země
ALTER TABLE `zeme`
ADD `predvolba` varchar(8) COLLATE 'utf8_general_ci' NULL,
COMMENT='';
UPDATE `zeme` SET `predvolba` = '93' WHERE kod='AF';
UPDATE `zeme` SET `predvolba` = '358' WHERE kod='AX';
UPDATE `zeme` SET `predvolba` = '355' WHERE kod='AL';
UPDATE `zeme` SET `predvolba` = '213' WHERE kod='DZ';
UPDATE `zeme` SET `predvolba` = '1+684' WHERE kod='AS';
UPDATE `zeme` SET `predvolba` = '376' WHERE kod='AD';
UPDATE `zeme` SET `predvolba` = '244' WHERE kod='AO';
UPDATE `zeme` SET `predvolba` = '1+264' WHERE kod='AI';
UPDATE `zeme` SET `predvolba` = '672' WHERE kod='AQ';
UPDATE `zeme` SET `predvolba` = '1+268' WHERE kod='AG';
UPDATE `zeme` SET `predvolba` = '54' WHERE kod='AR';
UPDATE `zeme` SET `predvolba` = '374' WHERE kod='AM';
UPDATE `zeme` SET `predvolba` = '599' WHERE kod='AN';
UPDATE `zeme` SET `predvolba` = '297' WHERE kod='AW';
UPDATE `zeme` SET `predvolba` = '61' WHERE kod='AU';
UPDATE `zeme` SET `predvolba` = '43' WHERE kod='AT';
UPDATE `zeme` SET `predvolba` = '994' WHERE kod='AZ';
UPDATE `zeme` SET `predvolba` = '1+242' WHERE kod='BS';
UPDATE `zeme` SET `predvolba` = '973' WHERE kod='BH';
UPDATE `zeme` SET `predvolba` = '880' WHERE kod='BD';
UPDATE `zeme` SET `predvolba` = '1+246' WHERE kod='BB';
UPDATE `zeme` SET `predvolba` = '375' WHERE kod='BY';
UPDATE `zeme` SET `predvolba` = '32' WHERE kod='BE';
UPDATE `zeme` SET `predvolba` = '501' WHERE kod='BZ';
UPDATE `zeme` SET `predvolba` = '229' WHERE kod='BJ';
UPDATE `zeme` SET `predvolba` = '1+441' WHERE kod='BM';
UPDATE `zeme` SET `predvolba` = '975' WHERE kod='BT';
UPDATE `zeme` SET `predvolba` = '591' WHERE kod='BO';
UPDATE `zeme` SET `predvolba` = '599' WHERE kod='BQ';
UPDATE `zeme` SET `predvolba` = '387' WHERE kod='BA';
UPDATE `zeme` SET `predvolba` = '267' WHERE kod='BW';
UPDATE `zeme` SET `predvolba` = 'NONE' WHERE kod='BV';
UPDATE `zeme` SET `predvolba` = '55' WHERE kod='BR';
UPDATE `zeme` SET `predvolba` = '246' WHERE kod='IO';
UPDATE `zeme` SET `predvolba` = '673' WHERE kod='BN';
UPDATE `zeme` SET `predvolba` = '359' WHERE kod='BG';
UPDATE `zeme` SET `predvolba` = '226' WHERE kod='BF';
UPDATE `zeme` SET `predvolba` = '257' WHERE kod='BI';
UPDATE `zeme` SET `predvolba` = '855' WHERE kod='KH';
UPDATE `zeme` SET `predvolba` = '237' WHERE kod='CM';
UPDATE `zeme` SET `predvolba` = '1' WHERE kod='CA';
UPDATE `zeme` SET `predvolba` = '238' WHERE kod='CV';
UPDATE `zeme` SET `predvolba` = '1+345' WHERE kod='KY';
UPDATE `zeme` SET `predvolba` = '236' WHERE kod='CF';
UPDATE `zeme` SET `predvolba` = '235' WHERE kod='TD';
UPDATE `zeme` SET `predvolba` = '56' WHERE kod='CL';
UPDATE `zeme` SET `predvolba` = '86' WHERE kod='CN';
UPDATE `zeme` SET `predvolba` = '61' WHERE kod='CX';
UPDATE `zeme` SET `predvolba` = '61' WHERE kod='CC';
UPDATE `zeme` SET `predvolba` = '57' WHERE kod='CO';
UPDATE `zeme` SET `predvolba` = '269' WHERE kod='KM';
UPDATE `zeme` SET `predvolba` = '242' WHERE kod='CG';
UPDATE `zeme` SET `predvolba` = '682' WHERE kod='CK';
UPDATE `zeme` SET `predvolba` = '506' WHERE kod='CR';
UPDATE `zeme` SET `predvolba` = '225' WHERE kod='CI';
UPDATE `zeme` SET `predvolba` = '385' WHERE kod='HR';
UPDATE `zeme` SET `predvolba` = '53' WHERE kod='CU';
UPDATE `zeme` SET `predvolba` = '599' WHERE kod='CW';
UPDATE `zeme` SET `predvolba` = '357' WHERE kod='CY';
UPDATE `zeme` SET `predvolba` = '420' WHERE kod='CZ';
UPDATE `zeme` SET `predvolba` = '243' WHERE kod='CD';
UPDATE `zeme` SET `predvolba` = '243' WHERE kod='ZR';
UPDATE `zeme` SET `predvolba` = '45' WHERE kod='DK';
UPDATE `zeme` SET `predvolba` = '253' WHERE kod='DJ';
UPDATE `zeme` SET `predvolba` = '1+767' WHERE kod='DM';
UPDATE `zeme` SET `predvolba` = '1+809, 8' WHERE kod='DO';
UPDATE `zeme` SET `predvolba` = '593' WHERE kod='EC';
UPDATE `zeme` SET `predvolba` = '20' WHERE kod='EG';
UPDATE `zeme` SET `predvolba` = '503' WHERE kod='SV';
UPDATE `zeme` SET `predvolba` = '240' WHERE kod='GQ';
UPDATE `zeme` SET `predvolba` = '291' WHERE kod='ER';
UPDATE `zeme` SET `predvolba` = '372' WHERE kod='EE';
UPDATE `zeme` SET `predvolba` = '251' WHERE kod='ET';
UPDATE `zeme` SET `predvolba` = '500' WHERE kod='FK';
UPDATE `zeme` SET `predvolba` = '298' WHERE kod='FO';
UPDATE `zeme` SET `predvolba` = '679' WHERE kod='FJ';
UPDATE `zeme` SET `predvolba` = '358' WHERE kod='FI';
UPDATE `zeme` SET `predvolba` = '33' WHERE kod='FR';
UPDATE `zeme` SET `predvolba` = '594' WHERE kod='GF';
UPDATE `zeme` SET `predvolba` = '689' WHERE kod='PF';
UPDATE `zeme` SET `predvolba` = '33' WHERE kod='TF';
UPDATE `zeme` SET `predvolba` = '241' WHERE kod='GA';
UPDATE `zeme` SET `predvolba` = '220' WHERE kod='GM';
UPDATE `zeme` SET `predvolba` = '995' WHERE kod='GE';
UPDATE `zeme` SET `predvolba` = '49' WHERE kod='DE';
UPDATE `zeme` SET `predvolba` = '233' WHERE kod='GH';
UPDATE `zeme` SET `predvolba` = '350' WHERE kod='GI';
UPDATE `zeme` SET `predvolba` = '30' WHERE kod='GR';
UPDATE `zeme` SET `predvolba` = '299' WHERE kod='GL';
UPDATE `zeme` SET `predvolba` = '1+473' WHERE kod='GD';
UPDATE `zeme` SET `predvolba` = '590' WHERE kod='GP';
UPDATE `zeme` SET `predvolba` = '1+671' WHERE kod='GU';
UPDATE `zeme` SET `predvolba` = '502' WHERE kod='GT';
UPDATE `zeme` SET `predvolba` = '44' WHERE kod='GG';
UPDATE `zeme` SET `predvolba` = '44' WHERE kod='UK';
UPDATE `zeme` SET `predvolba` = '224' WHERE kod='GN';
UPDATE `zeme` SET `predvolba` = '245' WHERE kod='GW';
UPDATE `zeme` SET `predvolba` = '592' WHERE kod='GY';
UPDATE `zeme` SET `predvolba` = '509' WHERE kod='HT';
UPDATE `zeme` SET `predvolba` = 'NONE' WHERE kod='HM';
UPDATE `zeme` SET `predvolba` = '504' WHERE kod='HN';
UPDATE `zeme` SET `predvolba` = '852' WHERE kod='HK';
UPDATE `zeme` SET `predvolba` = '36' WHERE kod='HU';
UPDATE `zeme` SET `predvolba` = '354' WHERE kod='IS';
UPDATE `zeme` SET `predvolba` = '91' WHERE kod='IN';
UPDATE `zeme` SET `predvolba` = '62' WHERE kod='ID';
UPDATE `zeme` SET `predvolba` = '98' WHERE kod='IR';
UPDATE `zeme` SET `predvolba` = '964' WHERE kod='IQ';
UPDATE `zeme` SET `predvolba` = '353' WHERE kod='IE';
UPDATE `zeme` SET `predvolba` = '44' WHERE kod='IM';
UPDATE `zeme` SET `predvolba` = '972' WHERE kod='IL';
UPDATE `zeme` SET `predvolba` = '39' WHERE kod='IT';
UPDATE `zeme` SET `predvolba` = '1+876' WHERE kod='JM';
UPDATE `zeme` SET `predvolba` = '81' WHERE kod='JP';
UPDATE `zeme` SET `predvolba` = '44' WHERE kod='JE';
UPDATE `zeme` SET `predvolba` = '962' WHERE kod='JO';
UPDATE `zeme` SET `predvolba` = '7' WHERE kod='KZ';
UPDATE `zeme` SET `predvolba` = '254' WHERE kod='KE';
UPDATE `zeme` SET `predvolba` = '686' WHERE kod='KI';
UPDATE `zeme` SET `predvolba` = '381' WHERE kod='XK';
UPDATE `zeme` SET `predvolba` = '965' WHERE kod='KW';
UPDATE `zeme` SET `predvolba` = '996' WHERE kod='KG';
UPDATE `zeme` SET `predvolba` = '856' WHERE kod='LA';
UPDATE `zeme` SET `predvolba` = '371' WHERE kod='LV';
UPDATE `zeme` SET `predvolba` = '961' WHERE kod='LB';
UPDATE `zeme` SET `predvolba` = '266' WHERE kod='LS';
UPDATE `zeme` SET `predvolba` = '231' WHERE kod='LR';
UPDATE `zeme` SET `predvolba` = '218' WHERE kod='LY';
UPDATE `zeme` SET `predvolba` = '423' WHERE kod='LI';
UPDATE `zeme` SET `predvolba` = '370' WHERE kod='LT';
UPDATE `zeme` SET `predvolba` = '352' WHERE kod='LU';
UPDATE `zeme` SET `predvolba` = '853' WHERE kod='MO';
UPDATE `zeme` SET `predvolba` = '389' WHERE kod='MK';
UPDATE `zeme` SET `predvolba` = '261' WHERE kod='MG';
UPDATE `zeme` SET `predvolba` = '265' WHERE kod='MW';
UPDATE `zeme` SET `predvolba` = '60' WHERE kod='MY';
UPDATE `zeme` SET `predvolba` = '960' WHERE kod='MV';
UPDATE `zeme` SET `predvolba` = '223' WHERE kod='ML';
UPDATE `zeme` SET `predvolba` = '356' WHERE kod='MT';
UPDATE `zeme` SET `predvolba` = '692' WHERE kod='MH';
UPDATE `zeme` SET `predvolba` = '596' WHERE kod='MQ';
UPDATE `zeme` SET `predvolba` = '222' WHERE kod='MR';
UPDATE `zeme` SET `predvolba` = '230' WHERE kod='MU';
UPDATE `zeme` SET `predvolba` = '262' WHERE kod='YT';
UPDATE `zeme` SET `predvolba` = '52' WHERE kod='MX';
UPDATE `zeme` SET `predvolba` = '691' WHERE kod='FM';
UPDATE `zeme` SET `predvolba` = '373' WHERE kod='MD';
UPDATE `zeme` SET `predvolba` = '377' WHERE kod='MC';
UPDATE `zeme` SET `predvolba` = '976' WHERE kod='MN';
UPDATE `zeme` SET `predvolba` = '382' WHERE kod='ME';
UPDATE `zeme` SET `predvolba` = '1+664' WHERE kod='MS';
UPDATE `zeme` SET `predvolba` = '212' WHERE kod='MA';
UPDATE `zeme` SET `predvolba` = '258' WHERE kod='MZ';
UPDATE `zeme` SET `predvolba` = '95' WHERE kod='MM';
UPDATE `zeme` SET `predvolba` = '264' WHERE kod='NA';
UPDATE `zeme` SET `predvolba` = '674' WHERE kod='NR';
UPDATE `zeme` SET `predvolba` = '977' WHERE kod='NP';
UPDATE `zeme` SET `predvolba` = '31' WHERE kod='NL';
UPDATE `zeme` SET `predvolba` = '687' WHERE kod='NC';
UPDATE `zeme` SET `predvolba` = '64' WHERE kod='NZ';
UPDATE `zeme` SET `predvolba` = '505' WHERE kod='NI';
UPDATE `zeme` SET `predvolba` = '227' WHERE kod='NE';
UPDATE `zeme` SET `predvolba` = '234' WHERE kod='NG';
UPDATE `zeme` SET `predvolba` = '683' WHERE kod='NU';
UPDATE `zeme` SET `predvolba` = '672' WHERE kod='NF';
UPDATE `zeme` SET `predvolba` = '850' WHERE kod='KP';
UPDATE `zeme` SET `predvolba` = '1+670' WHERE kod='MP';
UPDATE `zeme` SET `predvolba` = '47' WHERE kod='NO';
UPDATE `zeme` SET `predvolba` = '968' WHERE kod='OM';
UPDATE `zeme` SET `predvolba` = '92' WHERE kod='PK';
UPDATE `zeme` SET `predvolba` = '680' WHERE kod='PW';
UPDATE `zeme` SET `predvolba` = '970' WHERE kod='PS';
UPDATE `zeme` SET `predvolba` = '507' WHERE kod='PA';
UPDATE `zeme` SET `predvolba` = '675' WHERE kod='PG';
UPDATE `zeme` SET `predvolba` = '595' WHERE kod='PY';
UPDATE `zeme` SET `predvolba` = '51' WHERE kod='PE';
UPDATE `zeme` SET `predvolba` = '63' WHERE kod='PH';
UPDATE `zeme` SET `predvolba` = 'NONE' WHERE kod='PN';
UPDATE `zeme` SET `predvolba` = '48' WHERE kod='PL';
UPDATE `zeme` SET `predvolba` = '351' WHERE kod='PT';
UPDATE `zeme` SET `predvolba` = '1+939' WHERE kod='PR';
UPDATE `zeme` SET `predvolba` = '974' WHERE kod='QA';
UPDATE `zeme` SET `predvolba` = '262' WHERE kod='RE';
UPDATE `zeme` SET `predvolba` = '40' WHERE kod='RO';
UPDATE `zeme` SET `predvolba` = '7' WHERE kod='RU';
UPDATE `zeme` SET `predvolba` = '250' WHERE kod='RW';
UPDATE `zeme` SET `predvolba` = '590' WHERE kod='BL';
UPDATE `zeme` SET `predvolba` = '290' WHERE kod='SH';
UPDATE `zeme` SET `predvolba` = '1+869' WHERE kod='KN';
UPDATE `zeme` SET `predvolba` = '1+758' WHERE kod='LC';
UPDATE `zeme` SET `predvolba` = '590' WHERE kod='MF';
UPDATE `zeme` SET `predvolba` = '508' WHERE kod='PM';
UPDATE `zeme` SET `predvolba` = '1+784' WHERE kod='VC';
UPDATE `zeme` SET `predvolba` = '685' WHERE kod='WS';
UPDATE `zeme` SET `predvolba` = '378' WHERE kod='SM';
UPDATE `zeme` SET `predvolba` = '239' WHERE kod='ST';
UPDATE `zeme` SET `predvolba` = '966' WHERE kod='SA';
UPDATE `zeme` SET `predvolba` = '221' WHERE kod='SN';
UPDATE `zeme` SET `predvolba` = '381' WHERE kod='RS';
UPDATE `zeme` SET `predvolba` = '248' WHERE kod='SC';
UPDATE `zeme` SET `predvolba` = '232' WHERE kod='SL';
UPDATE `zeme` SET `predvolba` = '65' WHERE kod='SG';
UPDATE `zeme` SET `predvolba` = '1+721' WHERE kod='SX';
UPDATE `zeme` SET `predvolba` = '421' WHERE kod='SK';
UPDATE `zeme` SET `predvolba` = '386' WHERE kod='SI';
UPDATE `zeme` SET `predvolba` = '677' WHERE kod='SB';
UPDATE `zeme` SET `predvolba` = '252' WHERE kod='SO';
UPDATE `zeme` SET `predvolba` = '27' WHERE kod='ZA';
UPDATE `zeme` SET `predvolba` = '500' WHERE kod='GS';
UPDATE `zeme` SET `predvolba` = '82' WHERE kod='KR';
UPDATE `zeme` SET `predvolba` = '211' WHERE kod='SS';
UPDATE `zeme` SET `predvolba` = '34' WHERE kod='ES';
UPDATE `zeme` SET `predvolba` = '94' WHERE kod='LK';
UPDATE `zeme` SET `predvolba` = '249' WHERE kod='SD';
UPDATE `zeme` SET `predvolba` = '597' WHERE kod='SR';
UPDATE `zeme` SET `predvolba` = '47' WHERE kod='SJ';
UPDATE `zeme` SET `predvolba` = '268' WHERE kod='SZ';
UPDATE `zeme` SET `predvolba` = '46' WHERE kod='SE';
UPDATE `zeme` SET `predvolba` = '41' WHERE kod='CH';
UPDATE `zeme` SET `predvolba` = '963' WHERE kod='SY';
UPDATE `zeme` SET `predvolba` = '886' WHERE kod='TW';
UPDATE `zeme` SET `predvolba` = '992' WHERE kod='TJ';
UPDATE `zeme` SET `predvolba` = '255' WHERE kod='TZ';
UPDATE `zeme` SET `predvolba` = '66' WHERE kod='TH';
UPDATE `zeme` SET `predvolba` = '670' WHERE kod='TL';
UPDATE `zeme` SET `predvolba` = '228' WHERE kod='TG';
UPDATE `zeme` SET `predvolba` = '690' WHERE kod='TK';
UPDATE `zeme` SET `predvolba` = '676' WHERE kod='TO';
UPDATE `zeme` SET `predvolba` = '1+868' WHERE kod='TT';
UPDATE `zeme` SET `predvolba` = '216' WHERE kod='TN';
UPDATE `zeme` SET `predvolba` = '90' WHERE kod='TR';
UPDATE `zeme` SET `predvolba` = '993' WHERE kod='TM';
UPDATE `zeme` SET `predvolba` = '1+649' WHERE kod='TC';
UPDATE `zeme` SET `predvolba` = '670' WHERE kod='TP';
UPDATE `zeme` SET `predvolba` = '688' WHERE kod='TV';
UPDATE `zeme` SET `predvolba` = '256' WHERE kod='UG';
UPDATE `zeme` SET `predvolba` = '380' WHERE kod='UA';
UPDATE `zeme` SET `predvolba` = '971' WHERE kod='AE';
UPDATE `zeme` SET `predvolba` = '44' WHERE kod='GB';
UPDATE `zeme` SET `predvolba` = '1' WHERE kod='US';
UPDATE `zeme` SET `predvolba` = 'NONE' WHERE kod='UM';
UPDATE `zeme` SET `predvolba` = '598' WHERE kod='UY';
UPDATE `zeme` SET `predvolba` = '998' WHERE kod='UZ';
UPDATE `zeme` SET `predvolba` = '678' WHERE kod='VU';
UPDATE `zeme` SET `predvolba` = '39' WHERE kod='VA';
UPDATE `zeme` SET `predvolba` = '58' WHERE kod='VE';
UPDATE `zeme` SET `predvolba` = '84' WHERE kod='VN';
UPDATE `zeme` SET `predvolba` = '1+284' WHERE kod='VG';
UPDATE `zeme` SET `predvolba` = '1+340' WHERE kod='VI';
UPDATE `zeme` SET `predvolba` = '681' WHERE kod='WF';
UPDATE `zeme` SET `predvolba` = '212' WHERE kod='EH';
UPDATE `zeme` SET `predvolba` = '967' WHERE kod='YE';
UPDATE `zeme` SET `predvolba` = '260' WHERE kod='ZM';
UPDATE `zeme` SET `predvolba` = '263' WHERE kod='ZW';

-- currency do země pro zvolení výchozí hodnoty při registraci
ALTER TABLE `zeme` ADD `mena_id` SMALLINT(5) UNSIGNED NULL DEFAULT '2' , ADD INDEX (`mena_id`) ;
ALTER TABLE `zeme` ADD CONSTRAINT `zeme_mena` FOREIGN KEY (`mena_id`) REFERENCES `vic_main`.`mena`(`mena_id`) ON DELETE SET NULL ON UPDATE CASCADE; 
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='10';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='21';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='22';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='23';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='29';
UPDATE `zeme` SET `mena_id` = '11' WHERE zeme_id='31';
UPDATE `zeme` SET `mena_id` = '25' WHERE zeme_id='38';
UPDATE `zeme` SET `mena_id` = '20' WHERE zeme_id='41';
UPDATE `zeme` SET `mena_id` = '26' WHERE zeme_id='45';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='46';
UPDATE `zeme` SET `mena_id` = '19' WHERE zeme_id='50';
UPDATE `zeme` SET `mena_id` = '8' WHERE zeme_id='3';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='4';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='9';
UPDATE `zeme` SET `mena_id` = '13' WHERE zeme_id='2';
UPDATE `zeme` SET `mena_id` = '16' WHERE zeme_id='8';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='1';
UPDATE `zeme` SET `mena_id` = '35' WHERE zeme_id='52';
UPDATE `zeme` SET `mena_id` = '27' WHERE zeme_id='55';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='60';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='61';
UPDATE `zeme` SET `mena_id` = '12' WHERE zeme_id='63';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='67';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='68';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='72';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='74';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='77';
UPDATE `zeme` SET `mena_id` = '12' WHERE zeme_id='78';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='82';
UPDATE `zeme` SET `mena_id` = '12' WHERE zeme_id='85';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='88';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='90';
UPDATE `zeme` SET `mena_id` = '13' WHERE zeme_id='91';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='93';
UPDATE `zeme` SET `mena_id` = '28' WHERE zeme_id='96';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='97';
UPDATE `zeme` SET `mena_id` = '21' WHERE zeme_id='99';
UPDATE `zeme` SET `mena_id` = '14' WHERE zeme_id='101';
UPDATE `zeme` SET `mena_id` = '29' WHERE zeme_id='102';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='103';
UPDATE `zeme` SET `mena_id` = '30' WHERE zeme_id='104';
UPDATE `zeme` SET `mena_id` = '31' WHERE zeme_id='105';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='106';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='110';
UPDATE `zeme` SET `mena_id` = '10' WHERE zeme_id='113';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='117';
UPDATE `zeme` SET `mena_id` = '32' WHERE zeme_id='121';
UPDATE `zeme` SET `mena_id` = '19' WHERE zeme_id='128';
UPDATE `zeme` SET `mena_id` = '15' WHERE zeme_id='132';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='133';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='134';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='137';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='140';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='146';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='147';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='150';
UPDATE `zeme` SET `mena_id` = '33' WHERE zeme_id='154';
UPDATE `zeme` SET `mena_id` = '34' WHERE zeme_id='155';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='160';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='163';
UPDATE `zeme` SET `mena_id` = '20' WHERE zeme_id='164';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='166';
UPDATE `zeme` SET `mena_id` = '35' WHERE zeme_id='167';
UPDATE `zeme` SET `mena_id` = '35' WHERE zeme_id='168';
UPDATE `zeme` SET `mena_id` = '36' WHERE zeme_id='174';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='176';
UPDATE `zeme` SET `mena_id` = '35' WHERE zeme_id='177';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='178';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='179';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='180';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='183';
UPDATE `zeme` SET `mena_id` = '17' WHERE zeme_id='184';
UPDATE `zeme` SET `mena_id` = '22' WHERE zeme_id='185';
UPDATE `zeme` SET `mena_id` = '18' WHERE zeme_id='191';
UPDATE `zeme` SET `mena_id` = '37' WHERE zeme_id='192';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='194';
UPDATE `zeme` SET `mena_id` = '20' WHERE zeme_id='195';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='196';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='198';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='203';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='206';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='208';
UPDATE `zeme` SET `mena_id` = '38' WHERE zeme_id='210';
UPDATE `zeme` SET `mena_id` = '35' WHERE zeme_id='212';
UPDATE `zeme` SET `mena_id` = '23' WHERE zeme_id='217';
UPDATE `zeme` SET `mena_id` = '24' WHERE zeme_id='219';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='224';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='227';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='230';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='231';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='237';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='246';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='247';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='248';
UPDATE `zeme` SET `mena_id` = '13' WHERE zeme_id='250';
UPDATE `zeme` SET `mena_id` = '13' WHERE zeme_id='251';
UPDATE `zeme` SET `mena_id` = '13' WHERE zeme_id='252';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='244';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='253';
UPDATE `zeme` SET `mena_id` = '30' WHERE zeme_id='254';
UPDATE `zeme` SET `mena_id` = '9' WHERE zeme_id='257';
UPDATE `zeme` SET `mena_id` = '2' WHERE zeme_id='259';
UPDATE `zeme` SET `mena_id` = '39' WHERE zeme_id='239';

-- uprava metody registrace nextstep -> confirm
UPDATE `controller_convert` SET
`c_id` = '108',
`lang_id` = '1',
`parent_id` = '24',
`poradi` = '0',
`zobrazeno` = '1',
`cols` = '3',
`left_side` = NULL,
`right_side` = NULL,
`title` = NULL,
`description` = NULL,
`keywords` = NULL,
`text` = '',
`req_controller` = 'registrace',
`req_action` = 'potvrzeni',
`real_controller` = 'Registration',
`real_action` = 'confirm',
`after_login` = '0',
`actionless` = '0',
`nonassoc_params` = '0'
WHERE `c_id` = '108' AND `lang_id` = '1';
UPDATE `controller_convert` SET
`c_id` = '108',
`lang_id` = '2',
`parent_id` = '24',
`poradi` = '0',
`zobrazeno` = '1',
`cols` = '3',
`left_side` = NULL,
`right_side` = NULL,
`title` = NULL,
`description` = NULL,
`keywords` = NULL,
`text` = '',
`req_controller` = 'registracia',
`req_action` = 'potvrdenie',
`real_controller` = 'Registration',
`real_action` = 'confirm',
`after_login` = '0',
`actionless` = '0',
`nonassoc_params` = '0'
WHERE `c_id` = '108' AND `lang_id` = '2';
UPDATE `controller_convert` SET
`c_id` = '108',
`lang_id` = '16',
`parent_id` = '24',
`poradi` = '0',
`zobrazeno` = '1',
`cols` = '3',
`left_side` = NULL,
`right_side` = NULL,
`title` = NULL,
`description` = NULL,
`keywords` = NULL,
`text` = '',
`req_controller` = 'registration',
`req_action` = 'confirm',
`real_controller` = 'Registration',
`real_action` = 'confirm',
`after_login` = '0',
`actionless` = '0',
`nonassoc_params` = '0'
WHERE `c_id` = '108' AND `lang_id` = '16';

-- předvolby v tabulce zeme, komentář
ALTER TABLE `area_codes`
COMMENT='ZRUŠENO - předvolby jsou nově uvedeny v tabulce zeme';
ALTER TABLE `uzivatel` CHANGE `area_code` `area_code` SMALLINT(5) UNSIGNED NOT NULL; 
ALTER TABLE `uzivatel` ADD CONSTRAINT `predvolba` FOREIGN KEY (`area_code`) REFERENCES `vic_main`.`zeme`(`zeme_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- uzivatel +adresa,lat,lng
ALTER TABLE `uzivatel`
ADD `address` varchar(255) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'adresa podle našeptávače google maps autocomplete' AFTER `email`,
ADD `lat` decimal(10,8) NOT NULL AFTER `address`,
ADD `lng` decimal(11,8) NOT NULL AFTER `lat`,
COMMENT='';
-- uzivatel, branch_id, citizen_id odpadá
ALTER TABLE `uzivatel` CHANGE `branch_id` `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `uzivatel` CHANGE `citizen_id` `citizen_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; 

-- aktualizace frází
UPDATE `preklady` SET `text` = 'Telefon (bez předvolby)' WHERE `text` = 'Telefon (bez +420)';
UPDATE `preklady` SET `text` = 'Hesla se neshodují' WHERE `text` = 'Hesla se neshodují.';

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'reg_salutation',		'',	'Oslovení',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_mr',		'',	'Pan',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_ms',		'',	'Paní',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_address',		'',	'Adresa (ulice číslo, město,...)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_callcode',		'',	'Předvolba',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_currency',		'',	'Měna',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'read_terms',		'',	'zobrazit podmínky',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'forbidden_country_alert',		'',	'Omlouváme se, z této země bohužel nepovolujeme registraci.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'captcha_label',		'',	'Opište prosím uvedený text',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'is_empty_value',		'',	'Povinná položka',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_email_invalid',		'',	'Neplatná e-mailová adresa',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'only_numbers',		'',	'Jsou povoleny pouze čísla',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_birthday_format',		'',	'Nesprávně zadané datum',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_young',		'',	'Omlouváme se, pro mladistvé není registrace povolena',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_no_spam_promise',		'',	'Žádný spam, slibujeme.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_success',		'',	'Potvrzení registrace',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_success_text',		'',	'Vaše registrace proběhla úspěšně, děkujeme Vám. Nyní klikněte na aktivační odkaz v obdrženém e-mailu (zkontrolujte případně také složku SPAM).',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_general_error',		'',	'Opravte prosím vyznačené prvky a odešlete formulář znovu.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_general_error_funny_title',		'',	'Ajajaj!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'empty captcha value',		'',	'Povinná položka',		0,	(@idPrekladyMax := @idPrekladyMax+1) );