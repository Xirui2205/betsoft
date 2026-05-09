<?php
##############################
#PHP 5.x + MySQL Database 5.0
#PEAR:DB
##############################

//IT6:PETR for confirmation testing only, remove later
//if(!defined('BOOK_CHECK_TIME')) define("BOOK_CHECK_TIME", 600);
//if(!defined('BOOK_CHECK_TIME_MORE')) define("BOOK_CHECK_TIME_MORE", 780);
//if(!defined('COUPON_LIFETIME')) define("COUPON_LIFETIME", 960);


#Maximalni pocet sazek na systemovem tiketu#
if(!defined('BET_SYSTEM_MAX'))  define('BET_SYSTEM_MAX',10);

//mail na ktery se zasilaji chybove zpravy, ktere nastanou ve scriptech
if(!defined('ADMINERRORMAIL'))     define('ADMINERRORMAIL','info@vic.com');

if(!defined('RC4CRYPTKEY'))     define('RC4CRYPTKEY',"J25etb89lc89omPl32dr39mf322rf33un2c8asi922no03and32b239t");

// systemova grupa web adminu
if(!defined('WEBMINS_GROUP')) define("WEBMINS_GROUP","webmins");

//mena id eura
if(!defined('EUR_ID'))     define('EUR_ID',2);

// stav session = OK
if(!defined('SESSION_GAME_OK')) define('SESSION_GAME_OK',2);

// OK stav ucet_status v tab. uzivatel_im_data
if(!defined('ACCOUNT_OK')) define('ACCOUNT_OK',1);

// stav uctu, kdy uzivatel pozadal o vygenerovani noveho hesla - pri pristim prihlaseni je povinen toto heslo zmenit za svoje heslo
if(!defined('ACCOUNT_OTP')) define('ACCOUNT_OTP',2);

// OK stav zakazany v tab. uzivatel
if(!defined('USER_OK')) define('USER_OK',0);

// implicitni locale
if (!defined('DEFAULT_LOCALE')) define('DEFAULT_LOCALE', 'en_US');

// zkratka centralni meny
if (!defined('CENTRAL_CURRENCY_NAME')) define('CENTRAL_CURRENCY_NAME', 'CZK'); // EUR/CZK/...
if (!defined('CENTRAL_CURRENCY_ISO')) define('CENTRAL_CURRENCY_ISO', 203); // DULEZITE! EUR=978, CZK=203
if (!defined('DEFAULT_CURRENCY_ISO')) define('DEFAULT_CURRENCY_ISO', 203); // mena pro neprihlaseneho uzivatele

// cesta k promo news obrazkum
if(!defined('IMG_STORAGE')) define('IMG_STORAGE',"./_news/img/");

// cesta k nahrani souboru
if(!defined('UPLOAD_DOC_DIR')) define('UPLOAD_DOC_DIR',"../tmp/");

//maximalni pocet neuspesnych loginu
if(!defined('MAX_LOGIN')) define("MAX_LOGIN",6);

//Pocet sekund kdy se nemuze prihlasit
if(!defined('MAX_LOGIN_TIMEOUT')) define("MAX_LOGIN_TIMEOUT",7200);

//Pocet sekund pro overeni sazky
//DEPRECATED: if(!defined('BOOK_CHECK_TIME')) define("BOOK_CHECK_TIME", 90);

//Pocet sekund o kolik bookmaker prodlouzi overeni sazky
//DEPRECATED: if(!defined('BOOK_CHECK_TIME_MORE')) define("BOOK_CHECK_TIME_MORE", 60);

//Po kolika sekundach je vyprsi blokovani schvalovani jinych tiketu v kroku 1
if(!defined('COUPON_LIFETIME')) define('COUPON_LIFETIME', 120);

//Pocet sekund pred zacatkem udalosti, po kterem uz nelze prijimat sazky na udalost
if(!defined('BET_STOP_TIME')) define('BET_STOP_TIME', 15);

//Pocet sekund schvaleni live sazek
define("LIVE_WAIT_TIME",    10);

//Pocet live sazek v kalendar
define("PAGE_CALENDAR",    10);

//Live bet
if(!defined("LIVE_NOT_STARTED")) define("LIVE_NOT_STARTED",0);    //nezahajeno
if(!defined("LIVE_BEGIN")) define("LIVE_BEGIN",1);                //prave zacalo
if(!defined("LIVE_END")) define("LIVE_END",2);                    //ukonceno
if(!defined("LIVE_1_HALF")) define("LIVE_1_HALF",3);              //1 POLOCAS
if(!defined("LIVE_2_HALF")) define("LIVE_2_HALF",4);              //2 POLOCAS
if(!defined("LIVE_1_THIRD")) define("LIVE_1_THIRD",5);            //1 TRETINA
if(!defined("LIVE_2_THIRD")) define("LIVE_2_THIRD",6);            //2 TRETINA
if(!defined("LIVE_3_THIRD")) define("LIVE_3_THIRD",7);            //3 TRETINA
if(!defined("LIVE_1_Q")) define("LIVE_1_Q",8);                    //1 ctvrtina
if(!defined("LIVE_2_Q")) define("LIVE_2_Q",9);                    //2 ctvrtina
if(!defined("LIVE_3_Q")) define("LIVE_3_Q",10);                   //3 ctvrtina
if(!defined("LIVE_4_Q")) define("LIVE_4_Q",11);                   //4 ctvrtina
if(!defined("OVERTIME")) define("OVERTIME",12);                   //prodlouzeni
if(!defined("PAUSE")) define("PAUSE",13);                         //prestavka
if(!defined("STOP")) define("STOP",19);                         //zastaveno
if(!defined("LIVE_1_SET")) define("LIVE_1_SET",14);                    //1 set
if(!defined("LIVE_2_SET")) define("LIVE_2_SET",15);                    //2 set
if(!defined("LIVE_3_SET")) define("LIVE_3_SET",16);                   //3 set
if(!defined("LIVE_4_SET")) define("LIVE_4_SET",17);                   //4 set
if(!defined("LIVE_5_SET")) define("LIVE_5_SET",18);                   //5 set
if(!defined("LIVE_WARMUP")) define("LIVE_WARMUP",20);                   //Warm up
if(!defined("LIVE_UNFINISHED")) define("LIVE_UNFINISHED",21);                   //Nedohrano
if(!defined("LIVE_PENALTY")) define("LIVE_PENALTY",25);                   //Penalty
if(!defined("LIVE_CANCELED")) define("LIVE_CANCELED",26);                                      //Zruseny zapas
if(!defined("LIVE_RACE_RUNNING")) define("LIVE_RACE_RUNNING",27);
// kam se cachuji DB konstanty
if(!defined('CACHE_DIRECTORY')) define('CACHE_DIRECTORY','/tmp/');

/**
 * zda jsou dostupne casino turnaje
 */
if(!defined ('CASINO_TOURNAMENTS_ENABLED')) define('CASINO_TOURNAMENTS_ENABLED',true);

if(!defined ('MAX_BALANCE')) define('MAX_BALANCE',999999999.99);
if(!defined ('MIN_BALANCE')) define('MIN_BALANCE',-999999999.99);

if (!defined('RISK_LIMIT')) define('RISK_LIMIT', 0); // value? (legacy, found in class.XMLImportServer.php)

/* GFX */
//if (!defined('GFX_SERVER')) define('GFX_SERVER', "//img.test.compbet.com/");
if (!defined('GFX_SERVER')) define('GFX_SERVER', "/images/gallery/");
