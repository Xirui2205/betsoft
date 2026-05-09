START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (8105, 1, 'adding controller/action combo for cooperation/demand');



SET NAMES UTF8;


INSERT INTO `vic_main`.`controller_convert`
	(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
VALUES
	(92, 1, 0, 1, 1, 'Spolupráce - poptávka', 'Seznam měst kde hledáme partnery.', 'Spolupráce - poptávka', 'spoluprace', 'poptavka', 'static-page', 'cooperation-demand', 1, 0),
	(92, 2, 0, 1, 1, 'Cooperation - demand', 'Cities where we would like to find partners.', 'Cooperation - demand', 'cooperation', 'demand', 'static-page', 'cooperation-demand', 1, 0),
	(92, 16, 0, 1, 1, 'Spolupráce - poptávka', 'Seznam měst kde hledáme partnery.', 'Spolupráce - poptávka', 'spoluprace', 'poptavka', 'static-page', 'cooperation-demand', 1, 0);


UPDATE `vic_main`.`static_page` SET page_content = '<p>V&aacute;žen&iacute; obchodn&iacute; partneři,</p>
<p><strong>poskytneme V&aacute;m možnost podnikat </strong>na trhu kurzov&eacute;ho s&aacute;zen&iacute; bez jak&yacute;chkoliv znalost&iacute; dan&eacute;ho oboru, bez zdlouhav&eacute;ho z&iacute;sk&aacute;v&aacute;n&iacute; praxe a zku&scaron;enost&iacute;. A hlavně <strong>bez rizika.</strong></p>
<p><span style="text-decoration: underline;">Benefity a z&aacute;ruky</span>:</p>
<p>&bull; finančn&iacute; s&iacute;la a stabilita společnosti Victoria-Tip, a.s.<br />&bull; přes 300 poboček po cel&eacute; Česk&eacute; republice<br />&bull; neust&aacute;le se rozv&iacute;jej&iacute;c&iacute; s&aacute;zkov&aacute; kancel&aacute;ř<br />&bull; gener&aacute;ln&iacute; sponzor fotbalov&eacute;ho klubu Teplice</p>
<p><span style="text-decoration: underline;">Co potřebujete Vy jako provozovatel s&aacute;zkov&eacute; kancel&aacute;ře:</span></p>
<p>&bull; (pod)n&aacute;jemn&iacute; smlouvu k objektu, ve kter&eacute;m chcete pobočku provozovat<br />&bull; v př&iacute;padě podn&aacute;jmu souhlas vlastn&iacute;ka nemovitosti<br />&bull; v&yacute;pis z obchodn&iacute;ho rejstř&iacute;ku a osvědčen&iacute; o registraci<br />&bull; internetov&eacute; připojen&iacute;</p>
<p><span style="text-decoration: underline;">Co potřebuj&iacute; Va&scaron;i zaměstnanci:</span></p>
<p>&bull; čist&yacute; v&yacute;pis z rejstř&iacute;ku trestů (ne star&scaron;&iacute; než 3 měs&iacute;ce)<br />&bull; fotografii (pasov&yacute; form&aacute;t) na průkaz k opr&aacute;vněn&iacute; k př&iacute;jmu s&aacute;zek - průkaz vyd&aacute;v&aacute; Victoria-Tip, a.s.</p>
<p><strong>M&aacute;te z&aacute;jem o zř&iacute;zen&iacute; termin&aacute;lov&eacute; sběrny? </strong>Aktu&aacute;lně hled&aacute;me prostory pro zř&iacute;zen&iacute; nov&yacute;ch poboček na <a href="/cs/spoluprace/poptavka">mnoha m&iacute;stech.</a></p>
<p>Pak kontaktujte na&scaron;i klientskou infolinku:</p>
<p><strong>tel.:</strong> +420 412 879&nbsp;011,&nbsp;+420 777 760 808 <br /><strong>email:</strong> <a href="mailto:info@compbet.com">info@compbet.com</a></p>
<p>Nebo vyplňte on-line&nbsp;registračn&iacute; formul&aacute;ř <a href="/cs/registrace-poskytovatele">ZDE</a>.</p>
<p>V&nbsp;nejbliž&scaron;&iacute; době se V&aacute;m ozvou na&scaron;i region&aacute;ln&iacute; manažeři s bliž&scaron;&iacute; nab&iacute;dkou.</p>
<p>Děkujeme.</p>
<p><a id="bugnotes" name="bugnotes"></a></p>' WHERE page_name="cooperation" AND lang_id=1;


INSERT INTO `vic_main`.`static_page`
	(`page_name`, `lang_id`, `template`, `visible`, `page_title`, `page_description`, `page_keywords`, `page_content`)
VALUES
	('cooperation-demand', 1, 'index', 1, 'Nabídka spolupráce', 'Seznam lokací kde aktivně hledáme partnery ke spolupráci.', 'spolupráce, místo, partner', 'V součásné době hledáme partnery ke spolupráci na celém území ČR.'),
	('cooperation-demand', 2, 'index', 1, 'Cooperation offer', 'List of locatons where we are actively seeking partners.', 'cooperation, location, partner', 'Presenly we are looking for poartenrs all over Czech Republic'),
	('cooperation-demand', 16, 'index', 1, 'Nabídka spolupráce', 'Seznam lokací kde aktivně hledáme partnery ke spolupráci.', 'spolupráce, místo, partner', 'V součásné době hledáme partnery ke spolupráci na celém území ČR.');



COMMIT;
