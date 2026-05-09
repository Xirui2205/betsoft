UPDATE `vic_main`.`static_page` SET `page_content` = '
<h1>Spolupráce</h1>
	<div class="content">
		<h2>Vážení obchodní partneři,</h2>
	<p>poskytneme Vám možnost podnikat na trhu kurzového sázení bez jakýchkoliv znalostí daného oboru, bez zdlouhavého získávání praxe a zkušeností. A hlavně bez rizika.</p>

	<h2>Benefity a záruky</h2>
	<ul>
		<li>finanční síla a stabilita společnosti Victoria-Tip, a.s.</li>
		<li>přes 300 poboček po celé České republice</li>
		<li>neustále se rozvíjející sázková kancelář</li>
		<li>generální sponzor fotbalového klubu Teplice</li>
	</ul>

	<h2>Co potřebujete Vy jako provozovatel sázkové kanceláře</h2>

	<ul>
		<li>(pod)nájemní smlouvu k objektu, ve kterém chcete pobočku provozovat</li>
		<li>v případě podnájmu souhlas vlastníka nemovitosti</li>
		<li>výpis z obchodního rejstříku a osvědčení o registraci</li>
		<li>internetové připojení</li>
	</ul>

	<h2>Co potřebují Vaši zaměstnanci</h2>

	<ul>
		<li>čistý výpis z rejstříku trestů (ne starší než 3 měsíce)</li>
		<li>fotografii (pasový formát) na průkaz k oprávnění k příjmu sázek - průkaz vydává Victoria-Tip, a.s.</li>
	</ul>

	<div class="line-delim">&nbsp;</div>

	<h2 class="blue">Máte zájem o zřízení terminálové sběrny?</h2>

	<p>Kontaktujte naši klientskou infolinku:</p>

	<p><strong>Telefon:</strong> +420 412 879 011, +420 777 760 808<br>
	<strong>E-mail:</strong> <a href="mailto:info@compbet.com">info@compbet.com</a></p>

	<p>Nebo vyplňte on-line registrační formulář.<br>
	V nejbližší době se Vám ozvou naši regionální manažeři s bližší nabídkou.</p>

	<p><a class="button rounded shadowed" href="/cs/registrace-poskytovatele">registrační formulář</a></p>

	<p>V součásné době hledáme partnery po celém území ČR. <a href="/cs/spoluprace/poptavka">Více</a></p>
</div>
' WHERE `static_page`.`page_name` = 'cooperation' AND `static_page`.`lang_id` = 1 LIMIT 1 ;
