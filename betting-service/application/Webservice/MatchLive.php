<?php
/**
 * MatchLive related static methods.
 * @author Pavel Klinger
 * @see Entities_MatchLive
 * @see Webservice_Livebetting
 */
class Webservice_MatchLive extends Webservice_AbstractWebService {

const HP_LIVE_CALENDAR_BET_COUNT = 'homepage.liveCalendar.betCount';
const HP_LIVE_CALENDAR_SMALL_BET_COUNT = 'homepage.liveCalendarSmall.betCountPerSport';

public static $TABLE = 'match_live';
public static $TABLE_PREFIX = 'ml';
public static $IDENTITY = 'id';

public static $STATUSES = array(
		'NOT_STARTED', 'BEGIN', 'END', '1_HALF', '2_HALF', '1_THIRD',
		'2_THIRD', '3_THIRD', '1_Q', '2_Q', '3_Q', '4_Q', 'OVERTIME',
		'PAUSE', 'STOP', '1_SET', '2_SET', '3_SET', '4_SET', '5_SET',
		'WARMUP', 'UNFINISHED', 'PENALTY', 'CANCELED'
	);

protected static $CONV = array (
	'id'		 => 'id',
	'start'      => 'start',
	'status'	 => 'status',
	'sport'      => 'sport',
	'sport_id'   => 'sportId',
	'league'	 => 'league',
	'minute'	 => 'minute',
	'special_id' => 'specialId',
	'home_team'  => 'homeTeam',
	'away_team'  => 'awayTeam',
	'score_home' => 'scoreHome',
	'score_away' => 'scoreAway',
	'region'	 => 'region',
);

/**
 * Find live match by given id
 * @param integer $matchId identifier of the coupon
 * @return struct MatchLive structure
 * @see Entities_MatchLive
 */
public static function getById($matchId, $extensions = null) {
	return parent::getById($matchId, $extensions);
}

/**
 * Return all matches.
 * @return array array of the bookmaker structures
 * @see Entities_MatchLives
 */
public static function getAll($extensions = null) {
	return parent::getById($extensions);
}

/**
 * Return live matches to show on HP calendar
 * @return array of struct with live bet info
 */
public static function getHpCalendar($extensions = null) {
	$query = static::defaultQuery(static::getDb()->select())
		->where('status NOT IN (?)', array('END','UNFINISHED','CANCELED'))
		->where('DATE(start) = ?', It6_Date::dbNowAsDate())
		->order('start ASC')
		->limit(Webservice_Parameter::getGlobalParameter(self::HP_LIVE_CALENDAR_BET_COUNT))
		->query()->fetchAll();

	return $query;
}

/**
 * Return live matches to show on HP calendar
 * @return array of struct with live bet info
 */
public static function getHpCalendarSmallOnLine($extensions = null) {
	$cols = array('id','league','home_team AS homeTeam','away_team AS awayTeam','minute','status','start','score_home AS scoreHome','score_away AS scoreAway','region	');

	$where = "x.status NOT IN ('NOT_STARTED','END','UNFINISHED','CANCELED')";
	$matches = self::getCalendarSmallData($cols, $where);
	return $matches;
}

/**
 * Return live matches to show on HP calendar
 * @return array of struct with live bet info
 */
public static function getHpCalendarSmallComing($extensions = null) {
	$cols = array('id','league','home_team AS homeTeam','away_team AS awayTeam','minute','status','start');

	$where = "x.status = 'NOT_STARTED'";
	$matches = self::getCalendarSmallData($cols, $where, 'ASC');

	return $matches;
}

private static function getCalendarSmallData($cols, $where, $orderDir = 'DESC') {
	$res = static::getDb()
		->query('
			SELECT * FROM (
				SELECT (
						CASE
					WHEN @curSport=x.sport
						THEN @curRow := @curRow +1
						ELSE (@curSport := x.sport)OR(@curRow :=1)
						END
					) AS rank,
					x.sport,
					x.sport_id,
					x.'.implode(',x.',$cols).'
				FROM (
					SELECT '.self::$TABLE_PREFIX.'.*
					FROM (
						SELECT @curRow :=0, @curSport :=0
					) r,
					(
						SELECT sport
						FROM '.self::$TABLE.'
						GROUP BY sport
					) s
					JOIN '.self::$TABLE.' '.self::$TABLE_PREFIX.'
						ON s.sport = '.self::$TABLE_PREFIX.'.sport
					ORDER BY
						'.self::$TABLE_PREFIX.'.sport_id,
						'.self::$TABLE_PREFIX.'.start ' . $orderDir . '
				) x
				WHERE '.$where.'
			) y
			WHERE rank <= '.Webservice_Parameter::getGlobalParameter(self::HP_LIVE_CALENDAR_SMALL_BET_COUNT)
		)
		->fetchAll();

	$matches = array();
	foreach ($res as $match ) {
		$matches[$match['sport_id']]['sportClass'] = It6_Text::safeString($match['sport']);
		$matches[$match['sport_id']]['sport'] = $match['sport'];
		$matches[$match['sport_id']]['matches'][$match['id']] = $match;
	}

	return $matches;
}

} // class Webservice_MatchLive
