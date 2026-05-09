<?php
//suspicious!!need inspection
//same as common/class/help


/*
 Description
 * @author     tkbc.com
 * @date       25.7.2009
 * @copyright  TKBC
 * @version    1.0
 * @link       http://tkbc.cz

 Tato trida obsahu pomocn� funkce, kter� prov�d� ruzne podpurne cinnost

 Example how to use

 kontrolo spravnosti emailu

 */


class Models_Helpers_Help{

	/**
	 * spojeni na databazi game
	 * @access public
	 * @var DB
	 */
	public static $guSth;

	/**
	 * spojeni na databazi game
	 * @access public
	 * @var DB
	 */
	public static $guSth2;

	/**
	 * iso kody jazyka
	 * @access public
	 * @var array
	 */
	public static $iso;

	/**
	 * vraci jedinecny kod
	 *
	 * @param int $time  cas
	 * @param int $uid   id uzivatele
	 *
	 * @return string
	 */
	public static function UniqueCode($time,$uid){

		return dechex($time).substr(md5($uid),0,6);

	}


	/**
	 * Permutace
	 *
	 * @param array $bet pole sazek
	 *
	 * @return array
	 */
	public static function Permutace($in,$minLength = 1){


		$count = count($in);
		$members = pow(2,$count);
		$return = array();
		for ($i = 0; $i < $members; $i++) {
			$b = sprintf("%0".$count."b",$i);
			$out = array();
			for ($j = 0; $j < $count; $j++) {
				if ($b{$j} == '1') $out[] = $in[$j];
			}
			if (count($out) >= $minLength) {
				$return[] = $out;
			}
		}
		return $return;


	}




	/**
	 * vraci pole systemovych sazek
	 *
	 * @param array $bet pole sazek
	 * @param array $banker    pole bankeru
	 * @param int $num   jaky system
	 *
	 * @return string
	 */
	public static function CombSystem($bet,$banker,$num){


		$s = self::permutace($bet,$num);
		$v = array();
		foreach($s as $h){

			if(count($h) == $num) $v[] = array_merge($h,$banker);

		}

		return $v;

	}

	/**
	 * dekoduje uncodvany retezec v unicodu
	 *
	 * @param int $index  index pole
	 * @param int $n1    maximalni zanoreni
	 * @param int $maxn1   zanoreni
	 * @param array $betAr  pole sazek
	 * @param int $bankerRate  celkovy kurz bankeru
	 * @param int $rate    nasobek kurzy
	 * @param int $amount    castka na jednu sazku
	 *
	 * @return string
	 */
	public static function ReQSystem($index,$n1,$maxn1,$betAr,$bankerRate,$rate,$amount,$arb=array()){

		$sum = 0;
		$ratex = 1;

		for($x=$index;$x<=count($betAr);$x++){

			if(intval($maxn1) == intval($n1) && is_array($arb) && count($arb)>0){
				$klic = count($GLOBALS['system_special_ar']);
				$GLOBALS['system_special_ar'][$klic]['sazky'] = $arb;
				$GLOBALS['system_special_ar'][$klic]['kurz']  = ($rate*$bankerRate);
				$GLOBALS['system_special_ar'][$klic]['vyhra'] = ($rate*$bankerRate*$amount);
				return ($rate*$bankerRate*$amount);
			}
			else {if(isset($betAr[$x]['rate']))$ratex = ($betAr[$x]['rate']*$rate);}

			if(isset($betAr[$x]['rate']))$arb[] = $betAr[$x]['sazka_id'];
			$sum = $sum + Help::ReQSystem(($x+1),$n1,($maxn1+1),$betAr,$bankerRate,$ratex,$amount,$arb);
			array_pop ($arb);

		}

		return $sum;

	}


	/**
	 * dekoduje uncodvany retezec v unicodu
	 * @param string $str predavany retezec
	 * @return string
	 */
	public static function DecodeUnicodeUrl($str)
	{
		$res = '';

		$i = 0;
		$max = strlen($str) - 6;
		while ($i <= $max)
		{
			$character = $str[$i];
			if ($character == '%' && $str[$i + 1] == 'u')
			{
				$value = hexdec(substr($str, $i + 2, 4));
				$i += 6;

				if ($value < 0x0080) // 1 byte: 0xxxxxxx
				$character = chr($value);
				else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
				$character =
				chr((($value & 0x07c0) >> 6) | 0xc0)
				. chr(($value & 0x3f) | 0x80);
				else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
				$character =
				chr((($value & 0xf000) >> 12) | 0xe0)
				. chr((($value & 0x0fc0) >> 6) | 0x80)
				. chr(($value & 0x3f) | 0x80);
			}
			else
			$i++;

			$res .= $character;
		}

		return $res . substr($str, $i);
	}

	/**
	 * preklada vypocte kurzy podle zadane vyhenosti
	 * @param array $odd kurzy 1 0 2
	 * @param float $win vyhernost
	 * @return array
	 */
	public static function GetOdd($odd,$win=NULL){

		if(!isset($odd["1"]) || !isset($odd["X"]) || !isset($odd["2"])) return false;
		$home = floatval($odd["1"]); $draw = floatval($odd["X"]); $visit = floatval($odd["2"]);

		$vynosnost = ( (1/$home) + (1/$draw) + (1/$visit) );
		if($win == NULL) $win = $vynosnost;else $win = floatval($win);

		$pravdepodobnost_home  = (1/($vynosnost*$home));
		$pravdepodobnost_draw  = (1/($vynosnost*$draw));
		$pravdepodobnost_visit = (1/($vynosnost*$visit));

		$vysledek_home =  (1/($pravdepodobnost_home*$win));
		$vysledek_visit = (1/($pravdepodobnost_visit*$win));
		$vysledek_draw = (1/($win - (1/$vysledek_home) - (1/$vysledek_visit) ));

		$vysledek_home_01 = (1/($win-(1/$vysledek_visit)));
		$vysledek_home_02 = (1/($win-(1/$vysledek_home)));
		$vysledek_home_12 = (1/($win-(1/$vysledek_draw)));

		$odd["1"] =  $vysledek_home;
		$odd["X"] =  $vysledek_draw;
		$odd["2"] =  $vysledek_visit;
		$odd["01"] = $vysledek_home_01;
		$odd["02"] = $vysledek_home_02;
		$odd["12"] = $vysledek_home_12;

		/*
		 $odd["1"] =  (Help::RoundOdds(100*$vysledek_home)/100);
		 $odd["X"] =  (Help::RoundOdds(100*$vysledek_draw)/100);
		 $odd["2"] =  (Help::RoundOdds(100*$vysledek_visit)/100);
		 $odd["01"] = (Help::RoundOdds(100*$vysledek_home_01)/100);
		 $odd["02"] = (Help::RoundOdds(100*$vysledek_home_02)/100);
		 $odd["12"] = (Help::RoundOdds(100*$vysledek_home_12)/100);
		 */

		if($odd["1"] < 1)   $odd["1"] = 1;
		if($odd["X"] < 1)   $odd["X"] = 1;
		if($odd["2"] < 1)   $odd["2"] = 1;
		if($odd["01"] < 1)  $odd["01"] = 1;
		if($odd["02"] < 1)  $odd["02"] = 1;
		if($odd["12"] < 1)  $odd["12"] = 1;


		return $odd;

	}

	/**
	 * zaokrouhly kurz
	 * @param array $odd kurz
	 * @return float
	 */
	public static function RoundOdds($odd){


		if ($odd < 100)       $iX  = 1;
		else if( $odd >= 100 and $odd <= 110)  $iX  = 1;
		else if( $odd > 110 and $odd <=  130)  $iX  = 2;
		else if( $odd > 130 and $odd <=  180)  $iX  = 5;
		else if( $odd > 180 and $odd <=  300)  $iX  = 10;
		else if( $odd > 300 and $odd <=  500) $iX  = 20;
		else if( $odd > 500 and $odd <=  1000) $iX  = 50;
		else if( $odd > 1000 and $odd <=  2000) $iX  = 100;
		else if( $odd > 2000 and $odd <=  5000)   $iX  = 200;
		else if( $odd > 5000 and $odd <=  10000) $iX  = 500;
		else                                      $iX  = 1000;

		$lTmp = ((10 * $odd + 3 * $iX) / (10 * $iX));

		return ($iX * $lTmp);


	}


	/**
	 * parsuje URL kombinace
	 * @param string $url URL
	 * @return string
	 */
	public static function parseDetailUrl($url){

		mb_ereg('.*-m(\\d+)',$url,$ar);



		return $ar[1];


	}


	/*
	 trida pro kontrolu mailu
	 prebira jeden parametr, kter� je povinn� a
	 vrac� true v p�ipad� spr�vn�ho form�tu
	 a false v p��pad� nespr�vn�ho form�tu
	 @param 1  string email: emailov� adresa
	 */
	public static function valideMail($email){

		mb_regex_encoding("utf-8");

		//$email_pattern = "^([^@\s]+@[a-z0-9]+\.)+[a-z]{2,}$";
		$atom = '[-a-z0-9[[:alnum:]]!#$%&\'*+/=?^_`{|}~]'; // znaky tvo��c� u�ivatelsk� jm�no
		$domain = '[a-z0-9[[:alnum:]]]([-a-z0-9[[:alnum:]]]{0,61}[a-z0-9[[:alnum:]]])'; // jedna komponenta dom�ny

		if(!mb_ereg_match("^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$",$email))
		return false;
		else
		return true;

		/*     {


		$mboxChecker = new mailBoxChecker($email);
		if ($mboxChecker && !$mboxChecker->error) {
		$mboxChecker->checkMailBox();
		$mboxChecker->setActive();
		if($mboxChecker->getActive()) return true;else return false;
		}else return false;


		}*/

	}

	/**
	 * preklada v retezci nalezene preklady
	 * @param string $str jmeno text na preklad
	 * @param int $lang_id id jazyka
	 * @param DB::PEAR $db spojeni na db
	 * @return string
	 */
	public static function TranslateString($str,$lang_id=2,$db){
		return $str;
		static $translateStringProp;
		static $translateStringPropSth;

		if($translateStringProp != 88){

			$translateStringPropSth = $db->prepare('select text from preklady where lang_id=? and index_pole=?');
			if (PEAR::isError($translateStringPropSth))  return;
			$translateStringProp = 88;

		}


		if($translateStringProp != 88) return;

		$str_help = $str;

		if(preg_match_all('/\[([^\]]+)\]/',$str,$ar) && is_object($db)){

			$delka= count($ar[1]);

			$db->setFetchMode(DB_FETCHMODE_ASSOC);

			for($x=0;$x<$delka;$x++){

				$res2 =& $this->dbGame->execute($translateStringPropSth,array(intval($lang_id),Help::Slash($ar[1][$x])));
				if (PEAR::isError($res2))

				if ($row =& $res->fetchRow()) $str_help = mb_ereg_replace("\[".$ar[1][$x]."\]",$row["text"],$str_help);

			}

		}

		$str = $str_help;

		return $str;

	}

	/**
	 *Vytvori uzivatelsko heslo 5 pismen a 3 cisla
	 *vraci true uspech
	 *      false neuspech
	 */
	public static function PassGenerate(){

		$text = "";

		$pismena = array('a','b','c','d','e','f','g','h','x','y','z','t','u','v','w','i','j','k','l','m','n','o','p','r','s','a','b');

		$cislo1 = rand(0,9);
		$cislo2 = rand(0,9);
		$cislo3 = rand(0,9);

		for($x=0;$x<5;$x++){

			$text .= $pismena[rand(0,(count($pismena)-1))];

		}

		$text .= $cislo1.$cislo2.$cislo3;

		return $text;

	}

	/**
	 * Function array_insert().
	 *
	 * Returns the new number of the elements in the array.
	 *
	 * @param array $array Array (by reference)
	 * @param mixed $value New element
	 * @param int $offset Position
	 * @return int
	 */
	public static function ArrayInsert(&$array, $value, $offset)
	{
		if (is_array($array)) {
			$array  = array_values($array);
			$offset = intval($offset);
			if ($offset < 0 || $offset >= count($array)) {
				array_push($array, $value);
			} elseif ($offset == 0) {
				array_unshift($array, $value);
			} else {
				$temp  = array_slice($array, 0, $offset);
				array_push($temp, $value);
				$array = array_slice($array, $offset);
				$array = array_merge($temp, $array);
			}
		} else {
			$array = array($value);
		}
		return count($array);
	}

	/**
	 *  Vraci url pro danou hru
	 *@param $db
	 *@param $lang_id
	 *@param $preklad
	 * @return string
	 */
	public static function gameUrl($db,$lang_id,$preklad){

		if(!isset(self::$guSth)){

			self::$guSth = $db->prepare("select text from preklady where index_pole=? and lang_id=2");
			if (PEAR::isError(self::$guSth))  throw new ExHandler('Nepodarilo se nacist data z tabulky hry',"casino_ex_db");

			self::$guSth2 = $db->prepare("select lang_id,iso,iso_homepage from jazyky where lang_id=?");
			if (PEAR::isError(self::$guSth2))  throw new ExHandler('Nepodarilo se nacist data z tabulky hry',"casino_ex_db");

			$resT =& $db->execute(self::$guSth2,array($lang_id));
			if (PEAR::isError($resT))  throw new ExHandler('Nepodarilo se nacist data z tabulky hry',"casino_ex_db");

			while ($rowT =& $resT->fetchRow()) Help::$iso[$rowT['lang_id']] = $rowT['iso'];

		}


		$resT =& $db->execute(self::$guSth,array($preklad));
		if (PEAR::isError($resT))  throw new ExHandler('Nepodarilo se nacist data z tabulky hry',"casino_ex_db");

		if ($rowT =& $resT->fetchRow()) $gameName = $rowT['text']; else throw new ExHandler('Nepodarilo se ziskat nazev hry',"casino_ex_page");


		return Help::seoUrl($gameName).'-'.Help::$iso[$lang_id];

	}


	/**
	 Osetruje format a delku pro nick
	 *vraci true uspech
	 *      false neuspech
	 *@param 1  string nick: zadany nick
	 */
	public static function checkNick($nick){

		if(mb_strlen(trim($nick),'utf-8') < 4 || mb_strlen(trim($nick),'utf-8') > 20) return false;
		if(mb_ereg_match("^guest[0-9]+.*$",$nick)) return false;

		return mb_ereg_match("^[a-zA-Z][a-zA-Z0-9]+$",$nick);
	}

	/**
	 Osetruje format testovych polozek  nesmi byt escape znaky
	 *vraci true uspech
	 *      false neuspech
	 *@param 1  string nick: zadany nick
	 */
	public static function checkWord($word){

		return mb_ereg_match("^[^'\"\\\]+$",$word);

	}

	/**
	 * kontrola spravneho formatu data  dd.mm.yyyy
	 * @deprecated use It6_Date
	 * @param int $date  datum
	 * @return bool
	 */
	public static function CheckDatum($date){

		mb_regex_encoding("utf-8");

		if(mb_ereg("^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$",$date,$x)){

			if($x[1] > 31 || $x[1] < 1) return false;
			if($x[2] > 12 || $x[2] < 1) return false;
			if($x[3] > 2200 || $x[3] < 1800) return false;

			return true;

		}
		else
		return false;

	}

	/**
	 * vraci naformatovane cislo z DB  do formatu dd.mm.yyyy HH:MM:SS
	 * @deprecated use It6_Date
	 * @param int $date  datum time
	 * @return string
	 */
	public static function GetDatumTime($date){

		mb_regex_encoding("utf-8");

		if(mb_ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$",$date,$x)){

			return $x[3].".".$x[2].".".$x[1]." ".$x[4].":".$x[5].":".$x[6];

		}
		else
		return "";

	}

	/**
	 * vraci naformatovane cislo z DB  do formatu dd.mm.yyyy
	 * @deprecated use It6_Date
	 * @param int $date  datum time
	 * @return string
	 */
	public static function GetDatum($date){
		if (1 == preg_match('/^(\\d{4})-([01]\\d)-([0123]\\d)$/', $date, $matches))
			return ltrim($matches[3], '0') . '.' . ltrim($matches[2], '0') . '.' . $matches[1];
		else
			return '';
	}

	/**
	 * vraci naformatovane cislo ze stranek do formatu pro DB yyyy-mm-dd  HH:MM:SS
	 * @param int $date  datum time
	 * @return string
	 */
	public static function GetDatumTimeDB($date){

		mb_regex_encoding("utf-8");

		if(mb_ereg("^([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$",$date,$x)){

			return $x[3]."-".$x[2]."-".$x[1]." ".$x[4].":".$x[5].":".$x[6];

		}
		else
		return "";

	}

	/**
	 * vraci naformatovane cislo ze stranek do formatu pro DB yyyy-mm-dd
	 * @deprecated use It6_Date
	 * @param int $date  datum time
	 * @return string
	 */
	public static function GetDatumDB($date){
		if (1 == preg_match('/^([0123]?\\d)\\.([01]?\\d)\\.(\\d{4})$/', $date, $matches))
			return $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	/**
	 * kontrola spravneho formatu data a casu  dd.mm.yyyy HH:MM:SS
	 * @deprecated use It6_Date
	 * @param int $date  datum
	 * @return bool
	 */
	public static function CheckDatumTime($date){

		mb_regex_encoding("utf-8");

		if(mb_ereg("^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$",$date,$x)){

			if($x[1] > 31 || $x[1] < 1) return false;
			if($x[2] > 12 || $x[2] < 1) return false;
			if($x[3] > 2200 || $x[3] < 1800) return false;
			if($x[4] > 24 || $x[4] < 0) return false;
			if($x[5] > 60 || $x[5] < 0) return false;
			if($x[6] > 60 || $x[6] < 0) return false;

			return true;

		}
		else
		return false;

	}

	/**
	 * kontrola spravneho formatu data a casu  yyyy-mm-dd HH:MM:SS
	 * @deprecated use It6_Date
	 * @param int $date  datum
	 * @return bool
	 */
	public static function CheckDatumTimeDB($date){

		mb_regex_encoding("utf-8");

		if(mb_ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$",$date,$x)){

			if($x[3] > 31 || $x[3] < 1) return false;
			if($x[2] > 12 || $x[2] < 1) return false;
			if($x[1] > 2200 || $x[1] < 1800) return false;
			if($x[4] > 24 || $x[4] < 0) return false;
			if($x[5] > 60 || $x[5] < 0) return false;
			if($x[6] > 60 || $x[6] < 0) return false;

			return true;

		}
		else
		return false;

	}

	/**
	 * vraci razitko datumu a casu  dd.mm.yyyy HH:MM:SS
	 * @deprecated use It6_Date
	 * @param int $date  datum
	 * @return bool
	 */
	public static function GetTimeStamp($date){

		if(!Help::CheckDatumTime($date)) return false;

		$ar = preg_split('/[. :]/',$date);

		return mktime ( (isset($ar[3])?$ar[3]:0), (isset($ar[4])?$ar[4]:0), (isset($ar[5])?$ar[5]:0), $ar[1], $ar[0] , $ar[2] );

	}

	/**
	 * vraci UNIXovy timestamp jako retezec naformatovany pro DB
	 */
	public static function GetTimestampDB($timestamp) {
		return strftime('%Y-%m_%d %H:%M:%S', $timestamp);
	}

	/*
	 Osetruje format a delku pro heslo
	 vraci true uspech
	 false neuspech
	 @param 1  string nick: zadane heslo
	 */
	public function checkPass($pass){

		if(mb_strlen(trim($pass),'utf-8') < 6 || mb_strlen(trim($pass),'utf-8') > 16) return false;
		//return mb_ereg_match("^[a-zA-Z]{1}([a-zA-Z]*[0-9]{1,}[a-zA-Z]*[0-9]{1,}[a-zA-Z]*)$",$pass);
		//if(!mb_ereg_match("^[a-zA-Z]{1}[a-zA-Z0-9]+$",$pass)) return false;
		//preg_match_all("/(\d{1})/",$pass,$num);
		//print_r($num);
		//if(count($num[0]) <2) return false;

		return true;

	}


	/*
	 Metoda prevede html znaky na kody a escapuje nebezpecne znaky ' " \
	 */
	public static function slash($h){

		//return mysql_real_escape_string(trim($h));
		return addslashes(trim($h));

	}

	/**
	 * Metoda prevede html znaky na kody a escapuje nebezpecne znaky "
	 * @param string $h  retezec
	 * @return string
	 */
	public static function Html($h){

		//return htmlentities($h,ENT_QUOTES,'UTF-8');
		return htmlspecialchars($h, ENT_QUOTES,'UTF-8');

	}

	/**
	 * Metoda prevede html znaky na kody a escapuje nebezpecne znaky  pouziva se u Javascriptu
	 * @param string $h  retezec
	 * @return string
	 */
	public static function Script($h){

		return htmlspecialchars(addslashes(mb_ereg_replace("[\n|\t|\r]+"," ",$h)),ENT_QUOTES,'UTF-8');

	}



	public  static function session_real_decode($data)
	{

		$vars=preg_split(
             '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/',
		$data,-1,PREG_SPLIT_NO_EMPTY |
		PREG_SPLIT_DELIM_CAPTURE
		);
		for($i=0; isset($vars[$i]); $i++) {
			$result[$vars[$i++]]=unserialize($vars[$i]);
		}
		return $result;

	}


	public static function cryptPass($heslo){

		//return md5($heslo);
		require_once "Crypt/Rc4.php";
		$key = RC4CRYPTKEY;
		$rc4 = new Crypt_RC4;
		$rc4->key($key);
		$rc4->crypt($heslo);

		return md5($heslo);

	}

	/**
	 * kontroluje spravnost ip adresy
	 * @param string $h  retezec
	 * @return string
	 */

	public static function IsIp($ip){

		mb_regex_encoding("utf-8");

		return mb_ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$",$ip);

	}

	/**
	 * Provadi download souboru
	 * @param string $file  cesta k souboru vcetne nazvu
	 * @param string $name  nazev souboru
	 * @return string
	 */
	public static function OutputFile($file,$name){

		if(!file_exists($file))
		die('file not exist!');
		$size = filesize($file);
		$name = rawurldecode($name);

		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "Opera";
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "IE";
		else
		$UserBrowser = '';

		/// important for download im most browser
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?'application/octetstream' : 'application/octet-stream';
		@ob_end_clean(); /// decrease cpu usage extreme
		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename="'.$name.'"');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Accept-Ranges: bytes');
		header("Cache-control: private");
		header('Pragma: private');

		/////  multipart-download and resume-download
		if(isset($_SERVER['HTTP_RANGE']))
		{
			list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
			str_replace($range, "-", $range);
			$size2 = $size-1;
			$new_length = $size-$range;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range$size2/$size");
		}
		else
		{
			$size2=$size-1;
			header("Content-Length: ".$size);
		}
		$chunksize = 1*(1024*1024);
		$bytes_send = 0;
		if ($file = fopen($file, 'r'))
		{
			if(isset($_SERVER['HTTP_RANGE']))
			fseek($file, $range);
			while(!feof($file) and (connection_status()==0))
			{
				$buffer = fread($file, $chunksize);
				echo($buffer);//print($buffer); // is also possible
				flush();
				$bytes_send += strlen($buffer);

			}
			fclose($file);
		}
		else
		die('error can not open file');
		if(isset($new_length))
		$size = $new_length;
		//die();
	}


	/**
	 * prevede nase cislo tiketu na dlouhy format
	 * @param $id nase cislo tiketu
	 * @param $key cas "zalozen" tiketu c db formatu
	 */
	public static  function enTicket($id,$key) {
		throw new Exception('Deprecated. Use It6_Models_Ticket::getReadableId()');
		if(empty($id)) return false;
		$id = "$id"; $key = "$key";

		$key = Date::getUnixDate($key) % 10000000; // 10mil
		$key = substr($key."0000000",0,8);
		$new = $id + $key;

		$key = "$key";
		$o = "";
		for($i=0;$i<mb_strlen($id);$i++) {

			$o = $o.($key[$i]).($id[$i]);
		}

		return $o;
	}

	/**
	 * prevede dlouhe cislo tiketu na nas vnitrni format
	 * @param $id dlouhe cislo tiketu
	 */
	public static function deTicket($id) {
		throw new Exception('Deprecated. Use It6_Models_Ticket::getPrivateId()');
		if(empty($id)) return false;
		$id = "$id";

		$o = "";
		for($i=mb_strlen($id)-1;$i>=0;$i-=2) {
			$o = $id[$i].$o;
		}

		return $o;

	}

	public static function seoUrl($string){
		$string = mb_ereg_replace("([[:space:]])+","-", trim((string)$string));
		$utf8table = array ("\xC3\x80" => "A", "\xC3\x81" => "A", "\xC3\x82" => "A", "\xC3\x83" =>
"A",
    "\xC3\x84" => "A", "\xC3\x85" => "A", "\xC3\x86" => "AE","\xC3\x87" =>
"C",
    "\xC3\x88" => "E", "\xC3\x89" => "E", "\xC3\x8A" => "E", "\xC3\x8B" =>
"E",
    "\xC3\x8C" => "I", "\xC3\x8D" => "I", "\xC3\x8E" => "I", "\xC3\x8F" =>
"I",
    "\xC3\x90" => "D", "\xC3\x91" => "N", "\xC3\x92" => "O", "\xC3\x93" =>
"O",
    "\xC3\x94" => "O", "\xC3\x95" => "O", "\xC3\x96" => "O", "\xC3\x98" =>
"O",
    "\xC3\x99" => "U", "\xC3\x9A" => "U", "\xC3\x9B" => "U", "\xC3\x9C" =>
"U",
    "\xC3\x9D" => "Y", "\xC3\x9E" => "P", "\xC3\x9F" => "ss",
    "\xC3\xA0" => "a", "\xC3\xA1" => "a", "\xC3\xA2" => "a", "\xC3\xA3" =>
"a",
    "\xC3\xA4" => "a", "\xC3\xA5" => "a", "\xC3\xA6" => "ae","\xC3\xA7" =>
"c",
    "\xC3\xA8" => "e", "\xC3\xA9" => "e", "\xC3\xAA" => "e", "\xC3\xAB" =>
"e",
    "\xC3\xAC" => "i", "\xC3\xAD" => "i", "\xC3\xAE" => "i", "\xC3\xAF" =>
"i",
    "\xC3\xB0" => "o", "\xC3\xB1" => "n", "\xC3\xB2" => "o", "\xC3\xB3" =>
"o",
    "\xC3\xB4" => "o", "\xC3\xB5" => "o", "\xC3\xB6" => "o", "\xC3\xB8" =>
"o",
    "\xC3\xB9" => "u", "\xC3\xBA" => "u", "\xC3\xBB" => "u", "\xC3\xBC" =>
"u",
    "\xC3\xBD" => "y", "\xC3\xBE" => "p", "\xC3\xBF" => "y",
    "\xC4\x80" => "A", "\xC4\x81" => "a", "\xC4\x82" => "A", "\xC4\x83" =>
"a",
    "\xC4\x84" => "A", "\xC4\x85" => "a", "\xC4\x86" => "C", "\xC4\x87" =>
"c",
    "\xC4\x88" => "C", "\xC4\x89" => "c", "\xC4\x8A" => "C", "\xC4\x8B" =>
"c",
    "\xC4\x8C" => "C", "\xC4\x8D" => "c", "\xC4\x8E" => "D", "\xC4\x8F" =>
"d",
    "\xC4\x90" => "D", "\xC4\x91" => "d", "\xC4\x92" => "E", "\xC4\x93" =>
"e",
    "\xC4\x94" => "E", "\xC4\x95" => "e", "\xC4\x96" => "E", "\xC4\x97" =>
"e",
    "\xC4\x98" => "E", "\xC4\x99" => "e", "\xC4\x9A" => "E", "\xC4\x9B" =>
"e",
    "\xC4\x9C" => "G", "\xC4\x9D" => "g", "\xC4\x9E" => "G", "\xC4\x9F" =>
"g",
    "\xC4\xA0" => "G", "\xC4\xA1" => "g", "\xC4\xA2" => "G", "\xC4\xA3" =>
"g",
    "\xC4\xA4" => "H", "\xC4\xA5" => "h", "\xC4\xA6" => "H", "\xC4\xA7" =>
"h",
    "\xC4\xA8" => "I", "\xC4\xA9" => "i", "\xC4\xAA" => "I", "\xC4\xAB" =>
"i",
    "\xC4\xAC" => "I", "\xC4\xAD" => "i", "\xC4\xAE" => "I", "\xC4\xAF" =>
"i",
    "\xC4\xB0" => "I", "\xC4\xB1" => "i", "\xC4\xB2" => "IJ","\xC4\xB3" =>
"ij",
    "\xC4\xB4" => "J", "\xC4\xB5" => "j", "\xC4\xB6" => "K", "\xC4\xB7" =>
"k",
    "\xC4\xB8" => "k", "\xC4\xB9" => "L", "\xC4\xBA" => "l", "\xC4\xBB" =>
"L",
    "\xC4\xBC" => "l", "\xC4\xBD" => "L", "\xC4\xBE" => "l", "\xC4\xBF" =>
"L",
    "\xC5\x80" => "l", "\xC5\x81" => "L", "\xC5\x82" => "l", "\xC5\x83" =>
"N",
    "\xC5\x84" => "n", "\xC5\x85" => "N", "\xC5\x86" => "n", "\xC5\x87" =>
"N",
    "\xC5\x88" => "n", "\xC5\x89" => "n", "\xC5\x8A" => "N", "\xC5\x8B" =>
"n",
    "\xC5\x8C" => "O", "\xC5\x8D" => "o", "\xC5\x8E" => "O", "\xC5\x8F" =>
"o",
    "\xC5\x90" => "O", "\xC5\x91" => "o", "\xC5\x92" => "CE","\xC5\x93" =>
"ce",
    "\xC5\x94" => "R", "\xC5\x95" => "r", "\xC5\x96" => "R", "\xC5\x97" =>
"r",
    "\xC5\x98" => "R", "\xC5\x99" => "r", "\xC5\x9A" => "S", "\xC5\x9B" =>
"s",
    "\xC5\x9C" => "S", "\xC5\x9D" => "s", "\xC5\x9E" => "S", "\xC5\x9F" =>
"s",
    "\xC5\xA0" => "S", "\xC5\xA1" => "s", "\xC5\xA2" => "T", "\xC5\xA3" =>
"t",
    "\xC5\xA4" => "T", "\xC5\xA5" => "t", "\xC5\xA6" => "T", "\xC5\xA7" =>
"t",
    "\xC5\xA8" => "U", "\xC5\xA9" => "u", "\xC5\xAA" => "U", "\xC5\xAB" =>
"u",
    "\xC5\xAC" => "U", "\xC5\xAD" => "u", "\xC5\xAE" => "U", "\xC5\xAF" =>
"u",
    "\xC5\xB0" => "U", "\xC5\xB1" => "u", "\xC5\xB2" => "U", "\xC5\xB3" =>
"u",
    "\xC5\xB4" => "W", "\xC5\xB5" => "w", "\xC5\xB6" => "Y", "\xC5\xB7" =>
"y",
    "\xC5\xB8" => "Y", "\xC5\xB9" => "Z", "\xC5\xBA" => "z", "\xC5\xBB" =>
"Z",
    "\xC5\xBC" => "z", "\xC5\xBD" => "Z", "\xC5\xBE" => "z", "\xC6\x8F" =>
"E",
    "\xC6\xA0" => "O", "\xC6\xA1" => "o", "\xC6\xAF" => "U", "\xC6\xB0" =>
"u",
    "\xC7\x8D" => "A", "\xC7\x8E" => "a", "\xC7\x8F" => "I",
    "\xC7\x90" => "i", "\xC7\x91" => "O", "\xC7\x92" => "o", "\xC7\x93" =>
"U",
    "\xC7\x94" => "u", "\xC7\x95" => "U", "\xC7\x96" => "u", "\xC7\x97" =>
"U",
    "\xC7\x98" => "u", "\xC7\x99" => "U", "\xC7\x9A" => "u", "\xC7\x9B" =>
"U",
    "\xC7\x9C" => "u",
    "\xC7\xBA" => "A", "\xC7\xBB" => "a", "\xC7\xBC" => "AE","\xC7\xBD" =>
"ae",
    "\xC7\xBE" => "O", "\xC7\xBF" => "o",
    "\xC9\x99" => "e");
		$string =  strtr($string, $utf8table);

		$string = mb_ereg_replace("[,\.;\":'/\\ !?\(\)&=_-]+","-",$string);
		$string =  mb_ereg_replace("^-(.*)$","\\1",$string);
		$string =  mb_ereg_replace("^(.*)-$","\\1",$string);

		return mb_strtolower($string);

	}

	public static function create_code($length = 8, $use_upper=1, $use_lower=1, $use_number=1, $use_custom=""){
		$upper = "ABCDEFGHJKLMNPQRSTVWXYZ";
		$lower = "abcdefghjmnpqrstvwxyz";
		$number = "23456789";
		if($use_upper){
			$seed_length += strlen($upper);
			$seed .= $upper;
		}
		if($use_lower){
			$seed_length += strlen($lower);
			$seed .= $lower;
		}
		if($use_number){
			$seed_length += strlen($number);
			$seed .= $number;
		}
		if($use_custom){
			$seed_length +=strlen($use_custom);
			$seed .= $use_custom;
		}
		for($x=1;$x<=$length;$x++){
			$password .= $seed{rand(0,$seed_length-1)};
		}
		return($password);
	}

	/*
	 * z ceho se pocita vsazeno pro konkretni casino hry
	 * @param int $gid identifikator hry
	 * @return string pouzitelny v SQL dotazu
	 */
	public static function cgPrepareVsazeno($gid) {
		switch($gid) {
			case  4:    //  Retro (Red diamond) = jackpot_vsazeno+(vsazeno*sloupec)
			case  5: $o = "jackpot_vsazeno + (vsazeno * sloupec)"; break;// Bingo = jackpot_vsazeno+(vsazeno*sloupec)

			case  7:    // SuperHeroes =
			case  8: $o = "vsazeno * radky"; break; // Tunning = vsazeno*radky

			case 25: $o = "vsazeno * radky * level"; break;//   Hockey Madness = vsazeno*radky*level
			case 28: $o = "vsazeno * radky * level"; break;//   Beach girls = vsazeno*radky*level
			case 34: $o = "vsazeno * radky * level"; break;//   Dragon figter = vsazeno*radky*level
			case 36: $o = "vsazeno * radky * level"; break;//   Old legend= vsazeno*radky*level
			case 45: $o = "vsazeno * radky * level"; break;//   Snowpark = vsazeno*radky*level
			case 46: $o = "vsazeno * radky * level"; break;//   Euro CUp = vsazeno*radky*level

			case 18:    // All American
			case  3:    // Jacks or Better
			case 30:    // Jacks or Better 5
			case 31:    // Jacks or Better 10
			case 32:    // Jacks or Better 25
			case 15:    // Deuce wild
			case 42:    // Deuce wild 5
			case 43:    // Deuce wild 10
			case 44:    // Deuce wild 25
			case 21:    // Joker Poker
			case 38:    // JokerPoker 5
			case 39:    // JokerPoker 10
			case 40: $o = "hands * vsazeno * sloupec"; break;// JokerPoker 25

			case  2: $o = "vsazeno + jackpot_vsazeno + (2 * vsazeno * bet)"; break;//   Caribbean poker

			case  6: $o = "vsazeno + pojisteni_vsazeno"; break;//   Black jack

			case 33: $o = "vsazeno + bonus"; break;//   LetEmRide

			default: $o = "vsazeno";
		}
		return $o;
	}

	/*
	 * z ceho se pocita vyhra pro konkretni casino hry
	 * @param int $gid identifikator hry
	 * @return string pouzitelny v SQL dotazu
	 */
	public static function cgPrepareVyhra($gid) {
		switch($gid) {
			case  2:    // Caribbean poker
			case  4:    // Retro (Red diamond)
			case  5:    // Bingo
			case 28: $o = "vyhra + jackpot_vyhra"; break;// Beach girls

			default: $o = "vyhra";
		}
		return $o;
	}

	/**
	 * kolik hands patri ke hre ve vicerukych hrach (videopoker) podle id hry
	 * @param int $gid identifikator hry
	 * @return string pouzitelny v SQL dotazu
	 */
	public static function cgPrepareHands($gid) {
		switch($gid) {
			case  3: $o = "hands =  1"; break;//    Jacks or Better = hands*vsazeno*sloupec
			case 30: $o = "hands =  5"; break;//    Jacks or Better 5 = hands*vsazeno*sloupec
			case 31: $o = "hands = 10"; break;//    Jacks or Better 10 = hands*vsazeno*sloupec
			case 32: $o = "hands = 25"; break;//    Jacks or Better 25 = hands*vsazeno*sloupec

			case 15: $o = "hands =  1"; break;//    Deuce wild = vsazeno*sloupec
			case 42: $o = "hands =  5"; break;//    Deuce wild 5 = hands*vsazeno*sloupec
			case 43: $o = "hands = 10"; break;//    Deuce wild 10 = hands*vsazeno*sloupec
			case 44: $o = "hands = 25"; break;//    Deuce wild 25 = hands*vsazeno*sloupec

			case 21: $o = "hands =  1"; break;//    Joker Poker = vsazeno*sloupec
			case 38: $o = "hands =  5"; break;//    JokerPoker 5 =hands*vsazeno*sloupec
			case 39: $o = "hands = 10"; break;//    JokerPoker 10 =hands*vsazeno*sloupec
			case 40: $o = "hands = 25"; break;//    JokerPoker 25 =hands*vsazeno*sloupec

			default: $o = "";
		}
		return !empty($o)?" and ".$o:"";
	}

	public static function arr2json($arr){
		$json = array();
		foreach($arr as $k=>&$val) $json[] = $k.':'.self::php2js($val);
		if(count($json) > 0) return '{'.implode(',', $json).'}';
		else return '';
	}
	public static function php2js($val){
		if(is_array($val)) return self::arr2json($val);
		if(is_string($val)) return '"'.addslashes($val).'"';
		if(is_bool($val)) return 'Boolean('.(int) $val.')';
		if(is_null($val)) return '""';
		return $val;
	}

	/**
	 * @deprecated
	 * @see It6_Text::camelCaseToDashed()
	 */
	public static function camelCaseToDashed($cc) {
		if (1 == preg_match('/^([^[:upper:]]+)([[:upper:]].*)$/', $cc, $matches)) {
			$d = $matches[1];
			preg_match_all('/([[:upper:]])([^[:upper:]]*)/', $matches[2], $matches2, PREG_SET_ORDER);
			foreach ($matches2 as $match)
				$d .= '-' . strtolower($match[1]) . $match[2];
			return $d;
		}
		else
			return $cc;
	}

	/**
	 * @param $response Zend_Controller_Respose
	 * @param $controller name of controller
	 * @param $action name of action (defaults to 'index')
	 * @param $query query string (without leading question mark), URL-encoded
	 * @param $lang language code (default to value stored in session)
	 */
	public static function redirect($response, $controller = 'index', $action = 'index', $query = '', $lang = null) {
		$url = PROTOCOL . $_SERVER['HTTP_HOST'] . '/';
		if (empty($lang)) {
			if (!empty($_SESSION['lang']))
				$lang = $_SESSION['lang'];
			else
				$lang = DEFAULT_LANG;
		}
		$url .= $lang . '/';
		if ('index' != $controller || 'index' != $action)
			$url .= $controller . '/';
		else if ('index' != $action)
			$url .= $action . '/';
		if (!empty($query))
			$url .= '?' . $query;
		$response->setRedirect($url);
	}

}
