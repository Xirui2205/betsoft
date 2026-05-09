<?php

class It6_Muzo_SrCode {
	// for PRCODES 1,5,15,20
	const DUMMY = 0;
	const ORDERNUMBER = 1;
	const MERCHANTNUMBER = 2;
	const AMOUNT = 6;
	const CURRENCY = 7;
	const DEPOSITFLAG = 8;
	const MERORDERNUMBER = 10;
	const CREDITNUMBER = 11;
	const OPERATION = 12;
	const BATCH = 18;
	const ORDER = 22;
	const URL = 24;
	const MD = 25;
	const DESC = 26;
	const DIGEST = 34;
	// for PRCODE 28
	const NOT_AUTHENTICATED_IN_3D = 3000;
	const AUTHENTICATED_IN_3D = 3001;
	const NOT_3D_CARD = 3002;
	const NOT_ACTIVATED_IN_3D = 3004;
	const DECLINED_FOR_TECH_PROBLEM_CONTACT_CARD_ISSUER = 3005;
	const DECLINED_FOR_TECH_PROBLEM = 3006;
	const DECLINED_FOR_TECH_PROBLEM_CONTACT_MERCHANT = 3007;
	const DECLINED_UNSUPPORTED_CARD_PRODUCT = 3008;
	// for PRCODE 30
	const DECLINED_CARD_BLOCKED = 1001;
	const DECLINED_DO_NOT_HONOR = 1002;
	const DECLINED_CARD_PROBLEM = 1003;
	const DECLINED_TECH_PROBLEM = 1004;
	const DECLINED_ACCOUNT_PROBLEM = 1005;

	public static $messages = array(
		'cz' => array(
				self::DUMMY => 'OK',
				self::ORDERNUMBER => 'ORDERNUMBER',
				self::MERCHANTNUMBER => 'MERCHANTNUMBER',
				self::AMOUNT => 'AMOUNT',
				self::CURRENCY => 'CURRENCY',
				self::DEPOSITFLAG => 'DEPOSITFLAG',
				self::MERORDERNUMBER => 'MERORDERNUMBER',
				self::CREDITNUMBER => 'CREDITNUMBER',
				self::OPERATION => 'OPERATION',
				self::BATCH => 'BATCH',
				self::ORDER => 'ORDER',
				self::URL => 'URL',
				self::MD => 'MD',
				self::DESC => 'DESC',
				self::DIGEST => 'DIGEST',
				self::NOT_AUTHENTICATED_IN_3D => 'Neověřeno v 3D. Vydavatel karty není zapojen do 3D nebo karta nebyla aktivována. Kontaktujte vydavatele karty.',
				self::AUTHENTICATED_IN_3D => 'Držitel karty ověřen.',
				self::NOT_3D_CARD => 'Neověřeno v 3D. Vydavatel karty nebo karta není zapojena do 3D. Kontaktujte vydavatele karty.',
				self::NOT_ACTIVATED_IN_3D => 'Neověřeno v 3D. Vydavatel karty není zapojen do 3D nebo karta nebyla aktivována. Kontaktujte vydavatele karty.',
				self::DECLINED_FOR_TECH_PROBLEM_CONTACT_CARD_ISSUER => 'Zamítnuto v 3D. Technický problém při ověření držitele karty. Kontaktujte vydavatele karty.',
				self::DECLINED_FOR_TECH_PROBLEM => 'Zamítnuto v 3D. Technický problém při ověření držitele karty.',
				self::DECLINED_FOR_TECH_PROBLEM_CONTACT_MERCHANT => 'Zamítnuto v 3D. Technický problém v systému zúčtující banky. Kontaktujte obchodníka.',
				self::DECLINED_UNSUPPORTED_CARD_PRODUCT => 'Zamítnuto v 3D. Použit nepodporovaný karetní produkt. Kontaktujte vydavatele karty.',
				self::DECLINED_CARD_BLOCKED => 'Zamítnuto v autorizačním centru, karta blokována.',
				self::DECLINED_DO_NOT_HONOR => 'Zamítnuto v autorizačním centru, autorizace zamítnuta. Vydavatel, nebo finanční asociace zamítla autorizaci BEZ udání důvodu.',
				self::DECLINED_CARD_PROBLEM => 'Zamítnuto v autorizačním centru, problém karty.',
				self::DECLINED_TECH_PROBLEM => 'Zamítnuto v autorizačním centru, technický problém.',
				self::DECLINED_ACCOUNT_PROBLEM => 'Zamítnuto v autorizačním centru, problém účtu.'
			),
		'en' => array(
				self::DUMMY => 'OK',
				self::ORDERNUMBER => 'ORDERNUMBER',
				self::MERCHANTNUMBER => 'MERCHANTNUMBER',
				self::AMOUNT => 'AMOUNT',
				self::CURRENCY => 'CURRENCY',
				self::DEPOSITFLAG => 'DEPOSITFLAG',
				self::MERORDERNUMBER => 'MERORDERNUMBER',
				self::CREDITNUMBER => 'CREDITNUMBER',
				self::OPERATION => 'OPERATION',
				self::BATCH => 'BATCH',
				self::ORDER => 'ORDER',
				self::URL => 'URL',
				self::MD => 'MD',
				self::DESC => 'DESC',
				self::DIGEST => 'DIGEST',
				self::NOT_AUTHENTICATED_IN_3D => 'Declined in 3D. Cardholder not authenticated in 3D.Contact your card issuer.',
				self::AUTHENTICATED_IN_3D => 'Authenticated.',
				self::NOT_3D_CARD => 'Not Authenticated in 3D. Issuer or Cardholder not participating in 3D. Contact your card issuer.',
				self::NOT_ACTIVATED_IN_3D => 'Not Authenticated in 3D. Issuer not participating or Cardholder not enrolled. Contact your card issuer.',
				self::DECLINED_FOR_TECH_PROBLEM_CONTACT_CARD_ISSUER => 'Declined in 3D. Technical problem during Cardholder authentication. Contact your card issuer.',
				self::DECLINED_FOR_TECH_PROBLEM => 'Declined in 3D. Technical problem during Cardholder authentication.',
				self::DECLINED_FOR_TECH_PROBLEM_CONTACT_MERCHANT => 'Declined in 3D. Acquirer technical problem. Contact the merchant.',
				self::DECLINED_UNSUPPORTED_CARD_PRODUCT => 'Declined in 3D. Unsupported card product. Contact your card issuer.',
				self::DECLINED_CARD_BLOCKED => 'Declined in AC, Card blocked.',
				self::DECLINED_DO_NOT_HONOR => 'Declined in AC, Declined.',
				self::DECLINED_CARD_PROBLEM => 'Declined in AC, Card problem.',
				self::DECLINED_TECH_PROBLEM => 'Declined in AC, Technical problem in authorization process.',
				self::DECLINED_ACCOUNT_PROBLEM => 'Declined in AC, Account problem.'
			)
	);
}
