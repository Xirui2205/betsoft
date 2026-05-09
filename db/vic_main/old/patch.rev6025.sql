-- Deposit/index replaced by StaticPage/deposit-withdraw
START TRANSACTION;
INSERT INTO `vic_main`.`static_page` (
  `page_name`,
  `lang_id`,
  `template`,
  `layout_id`,
  `visible`,
  `page_title`,
  `page_description`,
  `page_keywords`,
  `page_content`
 ) VALUES
 ('deposit-withdraw',1,'index',1,1,'Vklad výběr','','','<p><br /> Victoria-Tip, a.s. se snaž&iacute; nab&iacute;dnout sv&yacute;m z&aacute;kazn&iacute;kům ty nejlep&scaron;&iacute; platebn&iacute; podm&iacute;nky.</p>\r\n<h3>Platebn&iacute; metody</h3>\r\n<h4>Platebn&iacute; karta</h4>\r\n<p><em>Visa/Visa Elektron, MasterCard</em> <br /> Platba platebn&iacute; kartou je v současnosti nejv&yacute;hodněj&scaron;&iacute;m způsobem. Jsou celosvětově zn&aacute;m&eacute; značky, kter&eacute; použ&iacute;vaj&iacute; nejmoderněj&scaron;&iacute; platebn&iacute; a bezpečnostn&iacute; technologie.</p>\r\n<h4>Pobočka</h4>\r\n<p>Na každ&eacute; z na&scaron;ich <a href=\"/cs/pobocky\">provozoven</a> si můžete dob&iacute;t V&aacute;&scaron; &uacute;čet. Stač&iacute; m&iacute;t jen u sebe registračn&iacute; kartu a hotovost, kterou chcete na V&aacute;&scaron; &uacute;čet vložit.</p>\r\n<h4>Bankovn&iacute; převod</h4>\r\n<p>Bankovn&iacute; převod je nejroz&scaron;&iacute;řeněj&scaron;&iacute; platebn&iacute; metodou. Pen&iacute;ze se přev&aacute;děj&iacute; př&iacute;mo z Va&scaron;eho bankovn&iacute;ho &uacute;čtu. Jakmile Va&scaron;i platbu obdrž&iacute;me, bude př&iacute;slu&scaron;n&aacute; č&aacute;stka přips&aacute;na na V&aacute;&scaron; &uacute;čet u Victoria-Tip (ponděl&iacute; až p&aacute;tek).</p>'),
 ('deposit-withdraw',2,'index',1,1,'How to deposit or withdraw','','',''),
 ('deposit-withdraw',16,'index',1,1,'Vklad a výber','','','');
UPDATE `vic_main`.`controller_convert` SET `real_controller`='static-page', `real_action`='deposit-withdraw' WHERE `c_id`=21;
COMMIT;
