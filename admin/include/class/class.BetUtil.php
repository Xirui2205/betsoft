<?php
class BetUtil {

	public static function isBetWinning(Array $h2) {
		if (is_array($h2['vysledek'])) {
			return (in_array($h2['sloupec_id'],$h2['vysledek']) ? TRUE : FALSE);
		} else {
			return (in_array($h2['sloupec_id'],explode(';',$h2['vysledek'])) ? TRUE : FALSE);
		}
	}

	public static function isBetPaidOut($row) {
		if ($row['proplacena'] == 1)
			return TRUE;
		else 
			return FALSE;
	}

}
