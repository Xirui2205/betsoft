<?php

class It6_Betradar_Match  extends It6_Betradar_Node {

const COMPETITOR_HOME = '1';
const COMPETITOR_AWAY = '2';	

// Betradar OddsType attribute values (special values are examples for soccer)
const ODDSTYPE_HANDICAPS = '01'; // 1 X 2 (with special values: "0:1" "1:0")
const ODDSTYPE_SCORE = '02'; // correct score
const ODDSTYPE_SCORE_ICE_HOCKEY = '2'; // correct score
const ODDSTYPE_3WAY = '10';  // 1 X 2
const ODDSTYPE_2WAY = '20';  // 1 2
const ODDSTYPE_CHAMPIONSHIP = '30';  // outright like F1 season winner
const ODDSTYPE_SHORT_TERM_OUTRIGHT = '40';  // outright like F1 race winner
const ODDSTYPE_PODIUM_FINISH = '50';  // outright like top 3 in finish
const ODDSTYPE_TOTALS = '60'; // Over Under (special value is split line for total)
const ODDSTYPE_SPREAD = '70'; // 1 2 (with special values: "-number" "+number", substract number from one and add to other)

const ODDSTYPE_FIRST_TEAM_TO_SCORE = '41'; // 1 2 None
const ODDSTYPE_3WAY_1ST_HALF = '42'; // 1 X 2
const ODDSTYPE_BOTH_TEAMS_TO_SCORE = '43'; // Yes No
const ODDSTYPE_HT_FT = '44'; // 1/1 1/X 1/2 X/1 X/X X/2 2/1 2/X 2/2
const ODDSTYPE_ODD_EVEN_GOALS = '45'; // Odd Even
const ODDSTYPE_DOUBLE_CHANCE = '46'; // 1X 12 2X (is computed from 3WAY)
const ODDSTYPE_DRAW_NO_BET = '47'; // 1 2 (after normal play)
const ODDSTYPE_GOALS_HOME_TEAM = '48'; // 0 1 2 3+
const ODDSTYPE_GOALS_AWAY_TEAM = '49'; // 0 1 2 3+
const ODDSTYPE_ASIAN_SPREAD_HANDICAPS = '51'; // 1 2 (with special values: -0.5 -0.75 -1) 
const ODDSTYPE_ASIAN_SPREAD_TOTALS = '52'; // Over Under (with special values: 2 2.25 2.5)
const ODDSTYPE_ASIAN_SPREAD_HANDICAPS_1ST_HALF = '53';  // 1 2 (with special values: -0.25 -0.5 -0.75)
const ODDSTYPE_ASIAN_SPREAD_TOTALS_1ST_HALF = '54'; // Over Under (with special values: 1 1.25 1.5)
const ODDSTYPE_EUROPEAN_HANDICAPS = '55'; // 1 X 2 (with special values: -3 -2 -1 1 2 3; where 1="1:0", -1="0:1", ...)
const ODDSTYPE_TOTALS_FT = '56'; // Over Under after normal play (with special values: 1.5 2.5 3.5)
const ODDSTYPE_3WAY_FT = '225'; // 1 X 2 
const ODDSTYPE_3WAY_2ND_HALF = '259'; // 1 X 2
const ODDSTYPE_WHICH_TEAM_TO_SCORE = '269'; // 1 2 "Both teams" None
const ODDSTYPE_CLEAN_SHEET_HOME_TEAM = '267'; // Yes No
const ODDSTYPE_CLEAN_SHEET_AWAY_TEAM = '268'; // Yes No
const ODDSTYPE_TOTALS_1ST_HALF = '284'; // Over Under (special value is split line for total) 
const ODDSTYPE_TOTALS_2ND_HALF = '285'; // Over Under (special value is split line for total)
// tennis sidebets
const ODDSTYPE_2WAY_1ST_SET = '204'; // 1 2
const ODDSTYPE_2WAY_2ND_SET = '231'; // 1 2
const ODDSTYPE_TOTALS_GAMES = '226'; // Over Under (s.v. is game count)
const ODDSTYPE_EXACT_SETS_3 = '206'; // "2 sets" "3 sets" (s.v. is set count)
const ODDSTYPE_EXACT_SETS_5 = '232'; // "3 sets" "4 sets" "5 sets" (s.v. is set count)
const ODDSTYPE_SCORE_SETS_3 = '233'; // 2:0 2:1 1:2 0:2
const ODDSTYPE_SCORE_SETS_5 = '234'; // 3:0 3:1 3:2 2:3 1:3 0:3
// ice hockey sidebets
const ODDSTYPE_3WAY_1P = '210';
const ODDSTYPE_3WAY_2P = '291';
const ODDSTYPE_3WAY_3P = '211';
const ODDSTYPE_TOTALS_1P = '212';
const ODDSTYPE_TOTALS_2P = '213';
const ODDSTYPE_TOTALS_3P = '214';
const ODDSTYPE_DOUBLE_CHANCE_1P = '215';
const ODDSTYPE_DOUBLE_CHANCE_2P = '216';
const ODDSTYPE_DOUBLE_CHANCE_3P = '217';
// basketball sidebets
const ODDSTYPE_2WAY_1ST_HALF = '223';
// american football / baseball
const ODDSTYPE_OVERTIME = '221'; // Yes No 
const ODDSTYPE_ASIAN_SPREAD_HANDICAPS_FINAL = '228'; // 1 2 (s.v. is points handicap)
const ODDSTYPE_ASIAN_SPREAD_TOTALS_FINAL = '229'; // Over Under (s.v. is split line for total points)
const ODDSTYPE_ODD_EVEN_POINTS_FINAL = '230'; // Odd Even

const DEFAULT_SPECIAL_VALUE = '*';

const OUTCOME_REMOVED = '-1'; // for match Odds elemement this is value for OutCome attribute when odd was removed from bet
                              // (for odds types without special value)

const SCORETYPE_FT = 'FT'; // full time (normal play time)
const SCORETYPE_HT = 'HT'; // half time
const SCORETYPE_OT = 'OT'; // over time
const SCORETYPE_AP = 'AP'; // after penalties
const SCORETYPE_WO = 'WO'; // walk-over (one competitor could not play or complete event), score is competitor ID that continues
const SCORETYPE_C = 'C';   // canceled

const SCORETYPE_1P = '1P'; // 1st period
const SCORETYPE_2P = '2P'; // 2nd period
const SCORETYPE_3P = '3P'; // 3rd period
const SCORETYPE_1Q = '1Q'; // 1st quarter
const SCORETYPE_2Q = '2Q'; // 2nd quarter
const SCORETYPE_3Q = '3Q'; // 3rd quarter
const SCORETYPE_4Q = '4Q'; // 4th quarter
const SCORETYPE_SET1 = 'Set1'; // 1st set
const SCORETYPE_SET2 = 'Set2'; // 2nd set
const SCORETYPE_SET3 = 'Set3'; // 3rd set
const SCORETYPE_SET4 = 'Set4'; // 4th set
const SCORETYPE_SET5 = 'Set5'; // 5th set

// defined Udetermined Winning Columns actions
const UWCACTION_CANCEL = 'cancel';

/**
 * Array of structures that describes particular odds types.<br/>
 * Each oddstype has associated list of structures each one for one bet to be created/updated.<br/>
 * Common fields:  (square brackets for optional ones)<br/>
 *    <ul>
 *    <li>priority ... integer Priority for parent bet determinimg, higher number means higher priority
 *                 (match and outright have independent priority values)</li>
 *    <li>betType ... integer BBAS bet type ID</li>
 *    <li>betSubtype ... integer|string BBAS subtype ID (integer) or callback name for dynamically determining subtype<br/>
 *                   callback subtype has interface function(import, integer bewaBetType, string specialValue, array outcomes) -> integer bewaSubtype</li>
 *    <li>[betName] ... callback for bet name creation: function(homeTeamName, awayTeamName, outcomes, specialValue, textNote = null) -> string</li>
 *    <li>[brParams] ... callback for betradar extra params serialization: function(import, string specialValue, array outcomes) -> string</li>
 *    <li>[off] ... if not empty, odds type configuration is ignored</li>
 *    </ul>
 * Fields for match:<br/>
 *    <ul>
 *    <li>[sports] ... array Keys: <ul>
 *                                 <li>match ... use config only for matched sport(s)</li>
 *                                 <li>ignore ... ignore config for matched sport(s)</li>
 *                                 </ul>
 *                       Values: one or list of BR sport IDs<br/>
 *                       (all criteria must be matched for config to be used)</li>
 *    <li>[specialValue] ... array (key => string methodName), expected keys (any of them) and expected method interfaces:<br/>
 *                       effectiveValue => new scalar value or array(methodName)<br/>
 *                       method interface: function(specialValue, outcome) -> newSpecialValue</li>
 *    <li>columns ... array( brOutcome => array('bewaId' => bewaColumnId, 'order' => integer) ) or name of method with interface:<br/>
 *                     function(importDataSource, integer bewaBetTypeId, integer bewaSubtypeId, array(string brOutcomes)) -> array(string brOutcome => array('bewaId' => bewaColumnId, 'order' => integer))<br/>
 *                     must be the set of columns from given subtype</li>
 *    <li>[complementaryColumns] ... boolean|array Will be passed to It6_Models_Bet::recomputeRates() as 3rd param, FALSE is default</li>
 *    <li>[updateOtherBetTypes] ... list of additional BBAS bet type IDs to be fetched from DB for rate update</li>
 *    <li>[updateOtherColumns] ... map(brOutcome => list of BBAS column IDs) other columns of DB bet for rate update
 *    </ul>
 * Fields for outright:<br/>
 *    <ul>
 *    <li>outright ... boolean always TRUE</li>
 *    <li>column ... integer BBAS column ID</li>
 *    </ul>
 * @var array
 */
public static $oddsTypesConfig = array(
	self::ODDSTYPE_3WAY => array(
		array(
			'priority' => 1000,
			'betType' => 19,
			'betSubtype' => 218,
			'columns' => array(
				'1' => array('bewaId' => 1857, 'order' => 1),
				'X' => array('bewaId' => 1858, 'order' => 2),
				'2' => array('bewaId' => 1859, 'order' => 3),
				'1X' => array('bewaId' => 1860, 'order' => 4),
				'12' => array('bewaId' => 1861, 'order' => 5),
				'X2' => array('bewaId' => 1862, 'order' => 6),
			),
			'complementaryColumns' => array(
				'1X' => array('1', 'X'),
				'12' => array('1', '2'),
				'X2' => array('X', '2'),
			),
			'updateOtherBetTypes' => array(24),
			'updateOtherColumns' => array(
				'1' => array(138),
				'X' => array(139),
				'2' => array(140),
				'1X' => array(141),
				'12' => array(142),
				'X2' => array(143),
			),
		),
/*
		array(
			'priority' => 1000,
			'betType' => 19,
			'betSubtype' => 23,
			'columns' => array(
				'1' => array('bewaId' => 138, 'order' => 1),
				'X' => array('bewaId' => 139, 'order' => 2),
				'2' => array('bewaId' => 140, 'order' => 3),
			),
		),
		array(
			'priority' => 950,
			'betType' => 24,
			'betSubtype' => 24,
			'columns' => array(
				'1X' => array('bewaId' => 141, 'order' => 1),
				'12' => array('bewaId' => 142, 'order' => 2),
				'X2' => array('bewaId' => 143, 'order' => 3),
			),
			'complementaryColumns' => true,
		),
*/
	),
	self::ODDSTYPE_2WAY => array(array(
		'priority' => 900,
		'betType' => 22,
		'betSubtype' => 29,
		'columns' => array(
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_HANDICAPS => array(array(
		'priority' => 800,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveFloatOrPass')),
		'betName' => 'betNameSpecialValueSimple',
		'betType' => 18,
		'betSubtype' => 23,
		'columns' => array(
			'1' => array('bewaId' => 138, 'order' => 1),
			'X' => array('bewaId' => 139, 'order' => 2),
			'2' => array('bewaId' => 140, 'order' => 3),
		),
	)),
	self::ODDSTYPE_SPREAD => array(array(
		'priority' => 700,
		'sports' => array('ignore' => array(
			It6_Betradar_Sport::BRID_BASKETBALL,
			It6_Betradar_Sport::BRID_HANDBALL,
			It6_Betradar_Sport::BRID_RUGBY,
			It6_Betradar_Sport::BRID_BASEBALL,
		)),
		'betName' => 'betNameSpecialValueHomeAway',
		'specialValue' => array(
			'effectiveValue' => 'specialValueEffectiveHomeAntisymetric',
		),
		'betType' => 25, // ???
		'betSubtype' => 29, // ???
		'brParams' => 'brParamsSerializeRealSpecialValues',
		'columns' => array( // ?
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_TOTALS => array(array(
		'priority' => 600,
		'sports' => array('ignore' => array(
			It6_Betradar_Sport::BRID_BASEBALL,
		)),
		'betType' => 20,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_SCORE => array(array(
		'priority' => 10,
		'betType' => 23,
		'betSubtype' => 'betSubtypeScore',
		'columns' => 'betSubtypeColumnsSameName',
	)),
	self::ODDSTYPE_SCORE_ICE_HOCKEY => array(array(
		'priority' => 10,
		'betType' => 23,
		'betSubtype' => 'betSubtypeScore',
		'columns' => 'betSubtypeColumnsSameName',
	)),
	self::ODDSTYPE_FIRST_TEAM_TO_SCORE => array(array(
		'off' => true,
		'priority' => 600,
		'betType' => 95,
		'betSubtype' => 119,
		'columns' => array(
			'1' => array('bewaId' => 1640, 'order' => 1),
			'None' => array('bewaId' => 1641, 'order' => 2),
			'2' => array('bewaId' => 1642, 'order' => 3),
		),
	)),
	self::ODDSTYPE_3WAY_1ST_HALF => array(array(
		'priority' => 600,
		'betType' => 16,
		//'betSubtype' => 23,
		//'columns' => array(
		//	'1' => array('bewaId' => 138, 'order' => 1),
		//	'X' => array('bewaId' => 139, 'order' => 2),
		//	'2' => array('bewaId' => 140, 'order' => 3),
		//),
		'betSubtype' => 218,
		'columns' => array(
			'1' => array('bewaId' => 1857, 'order' => 1),
			'X' => array('bewaId' => 1858, 'order' => 2),
			'2' => array('bewaId' => 1859, 'order' => 3),
			'1X' => array('bewaId' => 1860, 'order' => 4),
			'12' => array('bewaId' => 1861, 'order' => 5),
			'X2' => array('bewaId' => 1862, 'order' => 6),
		),
		'complementaryColumns' => array(
			'1X' => array('1', 'X'),
			'12' => array('1', '2'),
			'X2' => array('X', '2'),
		),
		'updateOtherBetTypes' => array(24),
		'updateOtherColumns' => array(
			'1' => array(138),
			'X' => array(139),
			'2' => array(140),
			'1X' => array(141),
			'12' => array(142),
			'X2' => array(143),
		),
	)),
	self::ODDSTYPE_3WAY_2ND_HALF => array(array(
		'priority' => 600,
		'betType' => 17,
		//'betSubtype' => 23,
		//'columns' => array(
		//	'1' => array('bewaId' => 138, 'order' => 1),
		//	'X' => array('bewaId' => 139, 'order' => 2),
		//	'2' => array('bewaId' => 140, 'order' => 3),
		//),
		'betSubtype' => 218,
		'columns' => array(
			'1' => array('bewaId' => 1857, 'order' => 1),
			'X' => array('bewaId' => 1858, 'order' => 2),
			'2' => array('bewaId' => 1859, 'order' => 3),
			'1X' => array('bewaId' => 1860, 'order' => 4),
			'12' => array('bewaId' => 1861, 'order' => 5),
			'X2' => array('bewaId' => 1862, 'order' => 6),
		),
		'complementaryColumns' => array(
			'1X' => array('1', 'X'),
			'12' => array('1', '2'),
			'X2' => array('X', '2'),
		),
		'updateOtherBetTypes' => array(24),
		'updateOtherColumns' => array(
			'1' => array(138),
			'X' => array(139),
			'2' => array(140),
			'1X' => array(141),
			'12' => array(142),
			'X2' => array(143),
		),
	)),
	self::ODDSTYPE_BOTH_TEAMS_TO_SCORE => array(array(
		'priority' => 600,
		'betType' => 115,
		'betSubtype' => 27,
		'columns' => array(
			'Yes' => array('bewaId' => 148, 'order' => 1),
			'No' => array('bewaId' => 149, 'order' => 2),
		),
	)),
	self::ODDSTYPE_HT_FT => array(array(
		'priority' => 600,
		'betType' => 50,
		'betSubtype' => 107,
		'columns' => array(
			'1/1' => array('bewaId' => 1606, 'order' => 1),
			'1/X' => array('bewaId' => 1607, 'order' => 2),
			'1/2' => array('bewaId' => 1608, 'order' => 3),
			'X/1' => array('bewaId' => 1609, 'order' => 4),
			'X/X' => array('bewaId' => 1610, 'order' => 5),
			'X/2' => array('bewaId' => 1611, 'order' => 6),
			'2/1' => array('bewaId' => 1612, 'order' => 7),
			'2/X' => array('bewaId' => 1613, 'order' => 8),
			'2/2' => array('bewaId' => 1614, 'order' => 9),
		),
	)),
	self::ODDSTYPE_ODD_EVEN_GOALS => array(array(
		'priority' => 600,
		'sports' => array('ignore' => It6_Betradar_Sport::BRID_BASEBALL),
		'betType' => 96,
		'betSubtype' => 121,
		'columns' => array(
			'Odd' => array('bewaId' => 1668, 'order' => 1),
			'Even' => array('bewaId' => 1669, 'order' => 2),
		),
	)),
	self::ODDSTYPE_DRAW_NO_BET => array(array(
		'priority' => 600,
		'betType' => 35,
		'betSubtype' => 29,
		'columns' => array(
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_GOALS_HOME_TEAM => array(array(
		'priority' => 600,
		'betType' => 155,
		'betSubtype' => 214,
		'columns' => array(
			'0' => array('bewaId' => 1843, 'order' => 1),
			'1' => array('bewaId' => 1844, 'order' => 2),
			'2' => array('bewaId' => 1845, 'order' => 3),
			'3+' => array('bewaId' => 1846, 'order' => 4),
		),
	)),
	self::ODDSTYPE_GOALS_AWAY_TEAM => array(array(
		'priority' => 600,
		'betType' => 156,
		'betSubtype' => 214,
		'columns' => array(
			'0' => array('bewaId' => 1843, 'order' => 1),
			'1' => array('bewaId' => 1844, 'order' => 2),
			'2' => array('bewaId' => 1845, 'order' => 3),
			'3+' => array('bewaId' => 1846, 'order' => 4),
		),
	)),
	self::ODDSTYPE_ASIAN_SPREAD_HANDICAPS => array(
		array(
			'sports' => array('match' => array(
				It6_Betradar_Sport::BRID_BASKETBALL,
				It6_Betradar_Sport::BRID_HANDBALL,
				It6_Betradar_Sport::BRID_RUGBY,
				It6_Betradar_Sport::BRID_BASEBALL,
			)),
			'priority' => 600,
			'betType' => 25,
			'betSubtype' => 29,
			'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
			'betName' => 'betNameSpecialValueAntisymetric',
			'columns' => array(
				'1' => array('bewaId' => 152, 'order' => 1),
				'2' => array('bewaId' => 153, 'order' => 2),
			),
		),
	),
//	self::ODDSTYPE_ASIAN_SPREAD_TOTALS => array(
//	),
	self::ODDSTYPE_ASIAN_SPREAD_HANDICAPS_1ST_HALF => array(
		array(
			'sports' => array('match' => array(
				It6_Betradar_Sport::BRID_BASKETBALL,
				It6_Betradar_Sport::BRID_HANDBALL,
				It6_Betradar_Sport::BRID_RUGBY,
			)),
			'priority' => 600,
			'betType' => 163,
			'betSubtype' => 29,
			'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
			'betName' => 'betNameSpecialValueAntisymetric',
			'columns' => array(
				'1' => array('bewaId' => 152, 'order' => 1),
				'2' => array('bewaId' => 153, 'order' => 2),
			),
		),
	),
	self::ODDSTYPE_ASIAN_SPREAD_TOTALS_1ST_HALF => array(
		array(
			'sports' => array('match' => array(
				It6_Betradar_Sport::BRID_BASKETBALL,
				It6_Betradar_Sport::BRID_HANDBALL,
			)),
			'priority' => 600,
			'betType' => 51,
			'betSubtype' => 25,
			'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
			'betName' => 'betNameSpecialValueSimple',
			'columns' => array(
				'Over' => array('bewaId' => 144, 'order' => 1),
				'Under' => array('bewaId' => 145, 'order' => 2),
			),
		),
	),
	self::ODDSTYPE_EUROPEAN_HANDICAPS => array(array(
		'off' => true,
		'priority' => 600,
		'betName' => 'betNameSpecialValueHandicapByDiff',
		'betType' => 18,
		'betSubtype' => 23,
		'columns' => array(
			'1' => array('bewaId' => 138, 'order' => 1),
			'X' => array('bewaId' => 139, 'order' => 2),
			'2' => array('bewaId' => 140, 'order' => 3),
		),
	)),
	self::ODDSTYPE_3WAY_1P => array(
		array(
			'priority' => 650,
			'betType' => 26,
			'betSubtype' => 218,
			'columns' => array(
				'1' => array('bewaId' => 1857, 'order' => 1),
				'X' => array('bewaId' => 1858, 'order' => 2),
				'2' => array('bewaId' => 1859, 'order' => 3),
				'1X' => array('bewaId' => 1860, 'order' => 4),
				'12' => array('bewaId' => 1861, 'order' => 5),
				'X2' => array('bewaId' => 1862, 'order' => 6),
			),
			'complementaryColumns' => array(
				'1X' => array('1', 'X'),
				'12' => array('1', '2'),
				'X2' => array('X', '2'),
			),
		),
/*
		array(
			'priority' => 650,
			'betType' => 26,
			'betSubtype' => 23,
			'columns' => array(
				'1' => array('bewaId' => 138, 'order' => 1),
				'X' => array('bewaId' => 139, 'order' => 2),
				'2' => array('bewaId' => 140, 'order' => 3),
			),
		),
		array(
			'priority' => 600,
			'betType' => 160,
			'betSubtype' => 24,
			'columns' => array(
				'1X' => array('bewaId' => 141, 'order' => 1),
				'12' => array('bewaId' => 142, 'order' => 2),
				'X2' => array('bewaId' => 143, 'order' => 3),
			),
			'complementaryColumns' => true,
		),
*/
	),
	self::ODDSTYPE_3WAY_2P => array(
		array(
			'priority' => 650,
			'betType' => 27,
			'betSubtype' => 218,
			'columns' => array(
				'1' => array('bewaId' => 1857, 'order' => 1),
				'X' => array('bewaId' => 1858, 'order' => 2),
				'2' => array('bewaId' => 1859, 'order' => 3),
				'1X' => array('bewaId' => 1860, 'order' => 4),
				'12' => array('bewaId' => 1861, 'order' => 5),
				'X2' => array('bewaId' => 1862, 'order' => 6),
			),
			'complementaryColumns' => array(
				'1X' => array('1', 'X'),
				'12' => array('1', '2'),
				'X2' => array('X', '2'),
			),
		),
/*
		array(
			'priority' => 650,
			'betType' => 27,
			'betSubtype' => 23,
			'columns' => array(
				'1' => array('bewaId' => 138, 'order' => 1),
				'X' => array('bewaId' => 139, 'order' => 2),
				'2' => array('bewaId' => 140, 'order' => 3),
			),
		),
		array(
			'priority' => 600,
			'betType' => 161,
			'betSubtype' => 24,
			'columns' => array(
				'1X' => array('bewaId' => 141, 'order' => 1),
				'12' => array('bewaId' => 142, 'order' => 2),
				'X2' => array('bewaId' => 143, 'order' => 3),
			),
			'complementaryColumns' => true,
		),
*/
	),
	self::ODDSTYPE_3WAY_3P => array(
		array(
			'priority' => 650,
			'betType' => 28,
			'betSubtype' => 218,
			'columns' => array(
				'1' => array('bewaId' => 1857, 'order' => 1),
				'X' => array('bewaId' => 1858, 'order' => 2),
				'2' => array('bewaId' => 1859, 'order' => 3),
				'1X' => array('bewaId' => 1860, 'order' => 4),
				'12' => array('bewaId' => 1861, 'order' => 5),
				'X2' => array('bewaId' => 1862, 'order' => 6),
			),
			'complementaryColumns' => array(
				'1X' => array('1', 'X'),
				'12' => array('1', '2'),
				'X2' => array('X', '2'),
			),
		),
/*
		array(
			'priority' => 650,
			'betType' => 28,
			'betSubtype' => 23,
			'columns' => array(
				'1' => array('bewaId' => 138, 'order' => 1),
				'X' => array('bewaId' => 139, 'order' => 2),
				'2' => array('bewaId' => 140, 'order' => 3),
			),
		),
		array(
			'priority' => 600,
			'betType' => 162,
			'betSubtype' => 24,
			'columns' => array(
				'1X' => array('bewaId' => 141, 'order' => 1),
				'12' => array('bewaId' => 142, 'order' => 2),
				'X2' => array('bewaId' => 143, 'order' => 3),
			),
			'complementaryColumns' => true,
		),
*/
	),
	self::ODDSTYPE_TOTALS_1P => array(array( 
		'priority' => 600,
		'betType' => 88,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_TOTALS_2P => array(array( 
		'priority' => 600,
		'betType' => 89,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_TOTALS_3P => array(array( 
		'priority' => 600,
		'betType' => 90,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_DOUBLE_CHANCE_1P => array(array(
		'off' => true, // it is created with 3WAY 1P
		'priority' => 600,
		'betType' => 160,
		'betSubtype' => 24,
		'columns' => array(
			'1X' => array('bewaId' => 141, 'order' => 1),
			'12' => array('bewaId' => 142, 'order' => 2),
			'X2' => array('bewaId' => 143, 'order' => 3),
		),
		'complementaryColumns' => true,
	)),
	self::ODDSTYPE_DOUBLE_CHANCE_2P => array(array(
		'off' => true, // it is created with 3WAY 2P
		'priority' => 600,
		'betType' => 161,
		'betSubtype' => 24,
		'columns' => array(
			'1X' => array('bewaId' => 141, 'order' => 1),
			'12' => array('bewaId' => 142, 'order' => 2),
			'X2' => array('bewaId' => 143, 'order' => 3),
		),
		'complementaryColumns' => true,
	)),
	self::ODDSTYPE_DOUBLE_CHANCE_3P => array(array(
		'off' => true, // it is created with 3WAY 3P
		'priority' => 600,
		'betType' => 162,
		'betSubtype' => 24,
		'columns' => array(
			'1X' => array('bewaId' => 141, 'order' => 1),
			'12' => array('bewaId' => 142, 'order' => 2),
			'X2' => array('bewaId' => 143, 'order' => 3),
		),
		'complementaryColumns' => true,
	)),
	self::ODDSTYPE_WHICH_TEAM_TO_SCORE => array(array(
		'off' => true,
		'priority' => 600,
		'betType' => 116,
		'betSubtype' => 217,
		'columns' => array(
			'None' => array('bewaId' => 1853, 'order' => 1),
			'1' => array('bewaId' => 1854, 'order' => 2),
			'2' => array('bewaId' => 1855, 'order' => 3),
			'Both teams' => array('bewaId' => 1856, 'order' => 4),
		),
	)),
	//self::ODDSTYPE_CLEAN_SHEET_HOME_TEAM => array(array(
	//)), 
	//self::ODDSTYPE_CLEAN_SHEET_AWAY_TEAM => array(array(
	//)), 
	self::ODDSTYPE_TOTALS_1ST_HALF => array(array(
		'priority' => 600,
		'betType' => 51,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_TOTALS_2ND_HALF => array(array(
		'priority' => 600,
		'betType' => 157,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),

	self::ODDSTYPE_2WAY_1ST_SET => array(array(
		'priority' => 600,
		'betType' => 66,
		'betSubtype' => 29,
		'columns' => array(
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_2WAY_2ND_SET => array(array(
		'priority' => 600,
		'betType' => 67,
		'betSubtype' => 29,
		'columns' => array(
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_TOTALS_GAMES => array(array(
		'priority' => 600,
		'betType' => 45,
		'betSubtype' => 25,
		'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
		'betName' => 'betNameSpecialValueSimple',
		'columns' => array(
			'Over' => array('bewaId' => 144, 'order' => 1),
			'Under' => array('bewaId' => 145, 'order' => 2),
		),
	)),
	self::ODDSTYPE_EXACT_SETS_3 => array(array(
		'priority' => 600,
		'betType' => 154,
		'betSubtype' => 212,
		'columns' => array(
			'2 sets' => array('bewaId' => 1838, 'order' => 1),
			'3 sets' => array('bewaId' => 1839, 'order' => 2),
		),
	)),
	self::ODDSTYPE_EXACT_SETS_5 => array(array(
		'priority' => 600,
		'betType' => 154,
		'betSubtype' => 213,
		'columns' => array(
			'3 sets' => array('bewaId' => 1840, 'order' => 1),
			'4 sets' => array('bewaId' => 1841, 'order' => 2),
			'5 sets' => array('bewaId' => 1842, 'order' => 3),
		),
	)),
	self::ODDSTYPE_SCORE_SETS_3 => array(array(
		'priority' => 600,
		'betType' => 23,
		'betSubtype' => 210,
		'columns' => array(
			'2:0' => array('bewaId' => 1828, 'order' => 1),
			'2:1' => array('bewaId' => 1829, 'order' => 2),
			'1:2' => array('bewaId' => 1830, 'order' => 3),
			'0:2' => array('bewaId' => 1831, 'order' => 4),
		),
	)),
	self::ODDSTYPE_SCORE_SETS_5 => array(array(
		'priority' => 600,
		'betType' => 23,
		'betSubtype' => 211,
		'columns' => array(
			'3:0' => array('bewaId' => 1832, 'order' => 1),
			'3:1' => array('bewaId' => 1833, 'order' => 2),
			'3:2' => array('bewaId' => 1834, 'order' => 3),
			'2:3' => array('bewaId' => 1835, 'order' => 4),
			'1:3' => array('bewaId' => 1836, 'order' => 5),
			'0:3' => array('bewaId' => 1837, 'order' => 6),
		),
	)),
	self::ODDSTYPE_2WAY_1ST_HALF => array(array(
		'sports' => array('match' => It6_Betradar_Sport::BRID_BASKETBALL),
		'priority' => 600,
		'betType' => 16,
		'betSubtype' => 29,
		'columns' => array(
			'1' => array('bewaId' => 152, 'order' => 1),
			'2' => array('bewaId' => 153, 'order' => 2),
		),
	)),
	self::ODDSTYPE_3WAY_FT => array(array(
		'priority' => 700,
		'sports' => array('match' => array(
			It6_Betradar_Sport::BRID_AMERICAN_FOOTBALL,
			It6_Betradar_Sport::BRID_BASKETBALL,
		)),
		'betType' => 165,
		'betSubtype' => 218,
		'columns' => array(
			'1' => array('bewaId' => 1857, 'order' => 1),
			'X' => array('bewaId' => 1858, 'order' => 2),
			'2' => array('bewaId' => 1859, 'order' => 3),
			'1X' => array('bewaId' => 1860, 'order' => 4),
			'12' => array('bewaId' => 1861, 'order' => 5),
			'X2' => array('bewaId' => 1862, 'order' => 6),
		),
		'complementaryColumns' => array(
			'1X' => array('1', 'X'),
			'12' => array('1', '2'),
			'X2' => array('X', '2'),
		),
		'updateOtherColumns' => array(
			'1' => array(138),
			'X' => array(139),
			'2' => array(140),
			'1X' => array(141),
			'12' => array(142),
			'X2' => array(143),
		),
	)),
	self::ODDSTYPE_OVERTIME => array(array(
		'priority' => 600,
		'betType' => 164,
		'betSubtype' => 27,
		'columns' => array(
			'Yes' => array('bewaId' => 148, 'order' => 1),
			'No' => array('bewaId' => 149, 'order' => 2),
		),
	)),
	self::ODDSTYPE_ASIAN_SPREAD_HANDICAPS_FINAL => array(
		array(
			'priority' => 600,
			'sports' => array('match' => It6_Betradar_Sport::BRID_BASEBALL),
			'betType' => 25,
			'betSubtype' => 29,
			'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
			'betName' => 'betNameSpecialValueAntisymetric',
			'columns' => array(
				'1' => array('bewaId' => 152, 'order' => 1),
				'2' => array('bewaId' => 153, 'order' => 2),
			),
		),
	),
	self::ODDSTYPE_ASIAN_SPREAD_TOTALS_FINAL => array(
		array(
			'priority' => 600,
			'sports' => array('match' => It6_Betradar_Sport::BRID_BASEBALL),
			'betType' => 20,
			'betSubtype' => 25,
			'specialValue' => array('effectiveValue' => array('specialValueEffectiveParseFloat')),
			'betName' => 'betNameSpecialValueSimple',
			'columns' => array(
				'Over' => array('bewaId' => 144, 'order' => 1),
				'Under' => array('bewaId' => 145, 'order' => 2),
			),
		),
	),
	self::ODDSTYPE_ODD_EVEN_POINTS_FINAL => array(
		array(
			'priority' => 600,
			'sports' => array('match' => It6_Betradar_Sport::BRID_BASEBALL),
			'betType' => 96,
			'betSubtype' => 121,
			'columns' => array(
				'Odd' => array('bewaId' => 1668, 'order' => 1),
				'Even' => array('bewaId' => 1669, 'order' => 2),
			),
		),
	),
	
	// outrights
	self::ODDSTYPE_CHAMPIONSHIP => array(array(
		'outright' => true,
		'priority' => 1000,
		'betType' => 31,
		'betSubtype' => 95,
		'column' => array('1' => 1446),
	)),
	self::ODDSTYPE_SHORT_TERM_OUTRIGHT => array(array(
		'outright' => true,
		'priority' => 900,
		'betType' => 37,
		'betSubtype' => 29,
		'column' => array('1' => 152, '2' => 153),
	)),
	self::ODDSTYPE_PODIUM_FINISH =>  array(array(
		'outright' => true,
		'priority' => 800,
		'betType' => 32,
		'betSubtype' => 96,
		'column' => array('1' => 1447),
	)),
);

/**
 * <pre>
 * {
 *    bewaBetTypeId => {
 *       bewaBetSubtypeId => { // can be '*' for all or semicolon separated list of IDs or just subtype ID
 *          [off => boolean,] // if not empty, configuration is ignored
 *          [effectiveScores => callbackName|array(callbackName, callbackAdditionalParams... ),] // callback that returns effective scores (the right type eg. FT), 'resultScoreFt' is default: (bewaSportId[, additionalParams...]) -> array(string score, integer homeScore, integer awayScore)
 *          [determineSpecialValue => callbackName|array,] // for non-BR bets use this callback to determine special value,
 *                                                         // interface: (struct dbBet, string svFormat) -> FALSE | struct with fields: brParams, specialValue
 *                                                         // svFormat can be specified as: 'none' | 'float', 'none' is default
 *                                                         // current implementation uses callback only for non-BR bets
 *          [modifyScores => callbackName,] // callback that modifies effecive scores using specialValues and BR params:  (array(homeScore, awayScore), specialValue, brParams) -> array(homeScore, awayScore)
 *          winningColumns => callbackName|array, // list of BBAS column IDs or callback that returns value of winning BBAS column IDs to be stored in DB: (import, bewaBet, score, array(homeScore, awayScore), specialValue, mixed userData) -> array|boolean
 *          [winningColumnsUserData => mixed,] // userData parameter for winningColumns callback
 *          [undeterminedWinningColumns => string,] // action to take on undetermined winning column(s), report error is default
 *                                                  // use some of UWCACTION_* class constants
 *          [brParams => callbackName,] // callback for parsing brParams stored in DB: (string dbBrParams) -> array
 *       },
 *    },
 *    ...
 * }
 * </pre>
 * @var struct
 */
public static $matchResultsConfig = array(
	// FIRST HALF
	16 => array(
		23 => array(
			'effectiveScores' => 'resultScoreHt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		29 => array(
			'effectiveScores' => 'resultScoreHt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153, 'equal' => null),
		),
		218 => array(
			'effectiveScores' => 'resultScoreHt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// SECOND HALF
	17 => array(
		23 => array(
			'effectiveScores' => array('resultScoreSubstract', 'FT', 'HT'),
			//'modifyScores' => 'modifyScoresAddSpecialValueScore',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => array('resultScoreSubstract', 'FT', 'HT'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// HANDICAP
	18 => array(
		23 => array(
			'effectiveScores' => 'resultScoreFt',
			'determineSpecialValue' => 'determineSpecialValueBetNameLastPart',
			'modifyScores' => 'modifyScoresAddSpecialValueScoreOrDiff',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
	),
	// 1X2
	19 => array(
		23 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// UNDER/OVER
	20 => array(
		25 => array(
			'effectiveScores' => array('resultScoreCallbackBySport', array(
				'*' => 'resultScoreFt',
				'1006' => array('resultScoreSum', array('1Q', '2Q', '3Q', '4Q'), true), // basketball
				'1015;1025;1026' => 'resultScoreLast', // rugby; handball; baseball; am.football
			)), 
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// WINNER
	22 => array(
		29 => array(
			'effectiveScores' => 'resultScoreBySportLast',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153, 'equal' => null),
		),
	),
	// EXACT RESULT
	23 => array(
		'*' => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsMatchColumnNameAndScore',
			//'winningColumnsUserData' => null,
		),
	),
	// DOUBLE MATCH
	24 => array(
		24 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => array(141, 142), 'away' => array(143, 142), 'equal' => array(143, 141)),
		),
	),
	// SPREAD/ASIAN HANDICAP
	25 => array(
		29 => array(
			'effectiveScores' => array('resultScoreCallbackBySport', array(
				'*' => 'resultScoreFt',
				'1006;1015;1025;1026' => 'resultScoreLast', // basketball; rugby; baseball; am.football
			)), 
			'determineSpecialValue' => array('determineSpecialValueTeamNamesLastPart', 'float', true),
			'modifyScores' => 'modifyScoresAddHomeAwaySpecialValuesToScores',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153, 'equal' => 153),
			'brParams' => 'brParamsUnserializeRealSpecialValues',
		),
	),
	// FIRST PERIOD 1X2
	26 => array(
		23 => array(
			'effectiveScores' => array('resultScoreByType', '1P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => array('resultScoreByType', '1P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// SECOND PERIOD 1X2
	27 => array(
		23 => array(
			'effectiveScores' => array('resultScoreByType', '2P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => array('resultScoreByType', '2P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// THIRD PERIOD 1X2
	28 => array(
		23 => array(
			'effectiveScores' => array('resultScoreByType', '3P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => array('resultScoreByType', '3P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
	// ADVANCE
	33 => array(
		29 => array(
			'off' => true, // Advance generally has not be determined by single result, but it could more than one result
			'effectiveScores' => 'resultScoreBySportLast',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153, 'equal' => null),
		),
	),
	// TWO WAY (NO DRAW)
	35 => array(
		29 => array(
			'effectiveScores' => 'resultScoreBySportLast',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153),
			'undeterminedWinningColumns' => self::UWCACTION_CANCEL,
		),
	),
	// UNDER/OVER GAMES (TENNIS)
	45 => array(
		25 => array(
			// AS VOID BET
			// 'effectiveScores' => 'resultScoreFt',
			// 'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_TOTALS_GAMES, 'Over' => 144, 'Under' => 145),
			'effectiveScores' => array('resultScoreSum', array('Set1', 'Set2', 'Set3', 'Set4', 'Set5'), true),
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// HALF TIME AND FULL TIME
	50 => array(
		107 => array(
			'effectiveScores' => array('resultScoreTupplesByType', 'FT', array('HT', 'FT')),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAwayTupple',
			'winningColumnsUserData' => array(
				'home-home' => 1606, 'home-equal' => 1607, 'home-away' => 1608, 
				'equal-home' => 1609, 'equal-equal' => 1610, 'equal-away' => 1611, 
				'away-home' => 1612, 'away-equal' => 1613, 'away-away' => 1614, 
			),
		),
	),
	// FIRST HALF UNDER/OVER
	51 => array(
		25 => array(
			'effectiveScores' => 'resultScoreHt',
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// SCORE (SETS)
	52 => array(
		210 => array(
			//'effectiveScores' => '',
			//'modifyScores' => '',
			//'winningColumns' => 'winningColumnsVoidBet',
			'winningColumns' => 'winningColumnsMatchUserColumnNameAndScore',
			'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_SCORE_SETS_3, '2:0' => 1828, '2:1' => 1829, '1:2' => 1830, '0:2' => 1831),
		),
		211 => array(
			//'effectiveScores' => '',
			//'modifyScores' => '',
			//'winningColumns' => 'winningColumnsVoidBet',
			'winningColumns' => 'winningColumnsMatchUserColumnNameAndScore',
			'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_SCORE_SETS_5, '3:0' => 1832, '3:1' => 1833, '3:2' => 1834, '2:3' => 1835, '1:3' => 1836, '0:3' => 1837),
		),
	),
	// 1ST SET WINNER
	66 => array(
		29 => array(
			// AS VOID BET
			// //'effectiveScores' => '',
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_2WAY_1ST_SET, '1' => 152, '2' => 153),
			'effectiveScores' => array('resultScoreByType', 'Set1'),
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153),
		),
	),
	// 2ND SET WINNER
	67 => array(
		29 => array(
		    // AS VOID BET
			// //'effectiveScores' => '',
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_2WAY_2ND_SET, '1' => 152, '2' => 153),
			'effectiveScores' => array('resultScoreByType', 'Set2'),
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153),
		),
	),
	// FIRST PERIOD UNDER/OVER
	88 => array(
		25 => array(
			'effectiveScores' => array('resultScoreByType', '1P'),
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// SECOND PERIOD UNDER/OVER
	89 => array(
		25 => array(
			'effectiveScores' => array('resultScoreByType', '2P'),
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// THIRD PERIOD UNDER/OVER
	90 => array(
		25 => array(
			'effectiveScores' => array('resultScoreByType', '3P'),
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// FIRST TEAM TO SCORE
	95 => array(
		119 => array(
			//'effectiveScores' => '',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsVoidBet',
			'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_FIRST_TEAM_TO_SCORE, '1' => 1640, '2' => 1642, 'None' => 1641),
		),
	),
	// EVEN/ODD
	96 => array(
		121 => array(
			'effectiveScores' => array('resultScoreCallbackBySport', array(
				'*' => 'resultScoreFt',
				1025 => 'resultScoreLast', // baseball
			)), 
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsTotalEvenOdd',
			'winningColumnsUserData' => array('odd' => 1668, 'even' => 1669),
		),
	),
	// BOTH TEAMS SCORED
	115 => array(
		27 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsTeamScoredBoth',
			'winningColumnsUserData' => array('yes' => 148, 'no' => 149),
		),
	),
	116 => array(
		//// HOME TEAM SCORED
		//27 => array(
		//	'effectiveScores' => 'resultScoreFt',
		//	//'modifyScores' => '',
		//	'winningColumns' => 'winningColumnsTeamScoredHome',
		//	'winningColumnsUserData' => array('yes' => 148, 'no' => 149),
		//),
		// TEAM SCORED
		27 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCallbackByRealBetTypeId',
			'winningColumnsUserData' => array(
			 	116 => array(
					'callback' => 'winningColumnsTeamScoredHome',
			 		'userData' => array('yes' => 148, 'no' => 149),
				),
				117 => array(
					'callback' => 'winningColumnsTeamScoredAway',
			 		'userData' => array('yes' => 148, 'no' => 149),
				),
			),
		),
		// WHICH TEAM SCORED
		217 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsWhichTeamScored',
			'winningColumnsUserData' => array('none' => 1853, 'home' => 1854, 'away' => 1855, 'both' => 1856),
		),
	),
	// COUNT OF SETS
	154 => array(
		212 => array( // up to 3 sets
			// AS VOID BET
			// //'effectiveScores' => '',
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_SCORE_SETS_3, '2 sets' => 1838, '3 sets' => 1839),
			'winningColumns' => 'winningColumnsMatchTotal',
			'winningColumnsUserData' => array('2' => 1838, '3' => 1839),
		),
		213 => array( // up to 5 sets
			// AS VOID BET
			// //'effectiveScores' => '',
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_SCORE_SETS_5, '3 sets' => 1840, '4 sets' => 1841, '5 sets' => 1842),
			'winningColumns' => 'winningColumnsMatchTotal',
			'winningColumnsUserData' => array('3' => 1840, '4' => 1841, '5' => 1842),
		),
	),
	// GOALS HOME TEAM
	155 => array(
		214 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsTeamScore',
			'winningColumnsUserData' => array('type' => 'FT', 'team' => 0, '0' => 1843, '1' => 1844, '2' => 1845, 'more' => 1846),
		),
	),
	// GOALS AWAY TEAM
	156 => array(
		214 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsTeamScore',
			'winningColumnsUserData' => array('type' => 'FT', 'team' => 1, '0' => 1843, '1' => 1844, '2' => 1845, 'more' => 1846),
		),
	),
	// SECOND HALF UNDER/OVER
	157 => array(
		25 => array(
			'effectiveScores' => array('resultScoreSubstract', 'FT', 'HT'),
			'determineSpecialValue' => array('determineSpecialValueBetNameLastPart', 'float'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareTotal',
			'winningColumnsUserData' => array('over' => 144, 'under' => 145, 'equal' => 145),
		),
	),
	// FIRST PERIOD DOUBLE CHANCE
	160 => array(
		24 => array(
			'effectiveScores' => array('resultScoreByType', '1P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => array(141, 142), 'away' => array(143, 142), 'equal' => array(143, 141)),
		),
	),
	// SECOND PERIOD DOUBLE CHANCE
	161 => array(
		24 => array(
			'effectiveScores' => array('resultScoreByType', '2P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => array(141, 142), 'away' => array(143, 142), 'equal' => array(143, 141)),
		),
	),
	// THIRD PERIOD DOUBLE CHANCE
	162 => array(
		24 => array(
			'effectiveScores' => array('resultScoreByType', '3P'),
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => array(141, 142), 'away' => array(143, 142), 'equal' => array(143, 141)),
		),
	),
	// FIRST HALF SPREAD/ASIAN HANDICAP
	163 => array(
		29 => array(
			'effectiveScores' => array('resultScoreCallbackBySport', array(
				'*' => 'resultScoreHt',
				'1006;1026' => array('resultScoreSum', array('1Q', '2Q'), true), // basketball; am.football
			)),
			'determineSpecialValue' => array('determineSpecialValueTeamNamesLastPart', 'float', true),
			'modifyScores' => 'modifyScoresAddHomeAwaySpecialValuesToScores',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 152, 'away' => 153, 'equal' => 153),
			'brParams' => 'brParamsUnserializeRealSpecialValues',
		),
	),
	// OVERTIME
	164 => array(
		27 => array(
			// AS VOID BET
			// //'effectiveScores' => '',
			// //'modifyScores' => '',
			// 'winningColumns' => 'winningColumnsVoidBet',
			// 'winningColumnsUserData' => array('oddsType' => self::ODDSTYPE_OVERTIME, 'Yes' => 148, 'No' => 149),
			'effectiveScores' => 'resultScoreFt',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 149, 'away' => 149, 'equal' => 148),
		),
	),
	// 3WAY AFTER NORMAL PLAY TIME
	165 => array(
		23 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array('home' => 138, 'away' => 140, 'equal' => 139),
		),
		218 => array(
			'effectiveScores' => 'resultScoreFt',
			//'modifyScores' => '',
			'winningColumns' => 'winningColumnsCompareHomeAway',
			'winningColumnsUserData' => array(
				'home' => array(1857, 1860, 1861),  // 1 1X 12
				'away' => array(1859, 1861, 1862),  // 2 12 X2
				'equal' => array(1858, 1860, 1862), // X 1X X2
			),
		),
	),
);

/**
 * <pre>
 * betType => struct:
 *    [off => boolean,] // if not empty, configuration is ignored
 * </pre>
 * @var struct
 */
public static $outrightResultsConfig = array(
	// GENERAL WINNER
	31 => array(
	),
	// WINNER (PODIUM)
	32 => array(
	),
	// LEAGUE/TOURNAMENT/CUP WINNER
	37 => array(
	),
);

/**
 * List of BBAS sport IDs for which the result score can be the one after FT 
 * @var array
 */
public static $sportsWithResultScoreAfterFullTime = array(
	1006, // basketball
	1011, // ice hockey
	1025, // baseball
	1026, // american football
);

/**
* List of BBAS sport IDs that are to be autoupdated by BR (satisfying condition)
* @var array
*/
public static $sportsWithBrUpdateOn = array(1001); // 1001 = soccer

/**
 * List of BBAS bet type IDs that are not to be autoupdated by BR (satisfying condition)
 * @var array
 */
public static $betTypesWithoutBrUpdateOn = array(18, 20, 23, 25);

public $isOutright = false;

/**
 * Array of competitors with type/brCompetitorId as keys and containing structures with following fields:<br/>
 *    brCompetitorId ... tournament/outright unique competitor ID<br/>
 *    brTeamId ... globally unique competitor ID<br/>
 *    text ... name of competitor as got from betradar<br/>
 * @var array
 */
public $competitors = array();
public $startTimestamp = null;
public $confirmedStartTimestamp = null;
public $calledOff = false;
public $neutralGround = false;
public $tournamentId = null;

/**
 * Match: oddsType => effectiveSpecialValue => outcome => array(keys: rate, specialValue, [realRate,])<br/>
 * Outright: oddsType => effectiveSpecialValue => brCompetitorId(see competitors) => array(keys: rate, specialValue)<br/>
 * @var array
 */
public $odds = array();

/**
 * type => value, eg. "FT" => "1:0"
 * @var array
 */
public $scores = array();

/**
 * Side bets (aka void bets) results
 * oddsType => specialValue => outcome
 * @var array
 */
public $betResults = array();

private $brBetType = It6_Betradar_Import::BET_TYPE_MATCH;

public function __construct($brId, $isOutright) {
	parent::__construct($brId);
	$this->isOutright = $isOutright;
	$this->brBetType = ($this->isOutright ? It6_Betradar_Import::BET_TYPE_OUTRIGHT : It6_Betradar_Import::BET_TYPE_MATCH);
}

public function addText($text) {
	if (!empty($text['competitors'])) {
		$type = ($this->isOutright ? $text['textAttrs']['ID'] : $text['textAttrs']['TYPE']);
		$this->competitors[$type] = array(
			'brCompetitorId' => $text['textAttrs']['ID'],
			'brTeamId' => (empty($text['textAttrs']['SUPERID']) ? 0 : $text['textAttrs']['SUPERID']), // not all competitors have SuperID
			'text' => trim($text['charData']),
		);
	}
	else if (!empty($text['eventName']))
		parent::addText($text);
}

public function setStart($timestamp) {
	$this->startTimestamp = $timestamp;
}

public function setConfirmedStart($timestamp) {
	$this->confirmedStartTimestamp = $timestamp;
}

public function setCalledOff($off) {
	$this->calledOff = $off;
}

public function setNeutralGround($neutral) {
	$this->neutralGround = $neutral;
}

public function setTournamentId($brId) {
	$this->tournamentId = $brId;
}

public function addOdds($oddsType, $outcome, $rate, $specialValue = null) {
	if (!isset($specialValue))
		$specialValueEff = static::DEFAULT_SPECIAL_VALUE;
	else
		$specialValueEff = $this->getEffectiveSpecialValue($oddsType, $outcome, $specialValue);
	$this->odds[$oddsType][$specialValueEff][$outcome] = array('rate' => $rate, 'specialValue' => $specialValue);
}

public function addScore($type, $score) {
	$this->scores[$type] = $score;
}

public function addBetResult($oddsType, $specialValue, $outcome) {
	if (!isset($specialValue))
		$specialValueEff = static::DEFAULT_SPECIAL_VALUE;
	else
		$specialValueEff = $this->getEffectiveSpecialValue($oddsType, $outcome, $specialValue);
	$this->betResults[$oddsType][$specialValueEff] = $outcome;
}

public function getDataToPreload(&$preloadedData) {
	$preloadedData->addMatch($this);
}

public function hasResults() {
	return (!empty($this->scores));
}

public function getBetradarBetType() {
	return $this->brBetType;
}

public static function specialValueToDb($specialValue) {
	return (static::DEFAULT_SPECIAL_VALUE == $specialValue ? '' : $specialValue);
}

public static function specialValueFromDb($dbSpecialValue) {
	return (empty($dbSpecialValue) ? static::DEFAULT_SPECIAL_VALUE : $dbSpecialValue);
}

/**
 * Fetches priorities for all bet types from odds types config.
 * @param integer $brSportId Only configs that are not filtered by sport filter are taken in account
 * @return array Map (bewaBetTypeId => priority)
 */
public function getBetTypesPriorities($brSportId) {
	static $cache = array();
	if (!isset($cache[$brSportId])) {
		$priorities = array();
		foreach (static::$oddsTypesConfig as $oddsType => $cfgs) {
			foreach ($cfgs as $cfg) {
				if ($this->filterOddstypeConfigBySport($cfg, $brSportId))
					continue;
				$betType = intval($cfg['betType']);
				$priority = intval($cfg['priority']);
				if (!isset($priorities[$betType]) || $priorities[$betType] < $priority)
					$priorities[$betType] = $priority;
			}
		}
		$cache[$brSportId] = $priorities;
	}
	else
		$priorities = $cache[$brSportId];
	return $priorities;
}

protected function compareForParent($betId1, $typeId1, $betId2, $typeId2, $priorities = null) {
	if (!isset($prio))
	$priorities = $this->getBetTypesPriorities($brSportId);
} 

/**
 * Retrieve current main bet ID from match bets and linked manual bets. 
 * @param It6_Betradar_Import $import
 * @return NULL|integer NULL if there is none or more than one parent IDs, parent ID if there was found single parent
 */
public function getMainBetId(&$import) {
	$matchBets = $import->getPreloadedData()->getBetsByMatchId($this->brBetType, $this->brId);
	if (empty($matchBets))
		return null;
	$main = null;
	$bets = array();
	$betsWithoutParent = array();
	foreach ($matchBets as $oddsType => $specialValues) {
		foreach ($specialValues as $specialValue => $_bets) {
			foreach ($_bets as $betId => $bet) {
				$bets[$betId] = true;
				$parentId = $bet['parentId'];
				if (null == $parentId)
					$betsWithoutParent[$betId] = true;
				else if (null == $main)
					$main = $parentId;
				else if ($main != $parentId)
					return null;
			}
		}
	}
	if (!empty($bets)) {
		$outerBets = $import->getBetsByParentId(array_keys($bets), true, false);
		if (!empty($outerBets)) {
			foreach ($outerBets as $betId => $bet) {
				$parentId = $bet['parentId'];
				if (null == $parentId)
					$betsWithoutParent[$betId] = true;
				else if (null == $main)
					$main = $parentId;
				else if ($main != $parentId)
					return null;
			}
		}
	}
	return (count($betsWithoutParent) > 1 ? null : $main);
}

/**
 * Checks all preloaded bets and linked non-betradar bets and determines which one should become the main bet.
 * @param It6_Betradar_Import $import
 * @param boolean $nonBrBets If bets that are not imported by betradar should be taken in account too
 * @param array|NULL $nonBrBets Will receive list of linked non-betradar BBAS bet IDs
 * @return integer|NULL BBAS bet ID or NULL if parent bet is unknown
 */
public function getNewMainBetId(&$import, $brSportId, &$nonBrBetIds = null) {
	$bets = array(); // bewaId => typeId
	$matchBets = $import->getPreloadedData()->getBetsByMatchId($this->brBetType, $this->brId);
	if (!empty($matchBets)) {
		foreach ($matchBets as $oddsType => $specialValues) {
			foreach ($specialValues as $specialValue => $_bets) {
				foreach ($_bets as $betId => $bet) {
					$bets[ $bet['bewaId'] ] = $bet['typeId'];
				}
			}
		}
	}
 	if (!empty($bets)) {
		$nonBrBets = $import->getBetsByParentId(array_keys($bets), true, false);
		$nonBrBetIds = array();
		foreach ($nonBrBets as $betId => $bet) {
			$bets[$betId] = $bet['typeId'];
			$nonBrBetIds[] = $betId;
		}
	}
	$main = null;
	if (!empty($bets)) {
		$priorities = $this->getBetTypesPriorities($brSportId);
		foreach ($bets as $betId => $typeId) {
			$priority = (isset($priorities[$typeId]) ? $priorities[$typeId] : 0);
			if ( empty($main)
				|| $priority > $main['priority']
				|| ($priority == $main['priority'] && $betId < $main['betId']) 
			) {
				$main = array('priority' => $priority, 'betId' => $betId, 'typeId' => $typeId);
			}
		}
	}
	return (empty($main) ? null : $main['betId']);
}

public function getBbasBet($import, $brBetType, $brId, $oddsType, $specialValue, $betTypeId, $otherBetTypeIds = null) {
	$specialValue = static::specialValueToDb($specialValue);
	$result = array();
	$bet = $import->getPreloadedData()->getBetByBetradar($brBetType, $brId, $oddsType, $specialValue, $this->isOutright);
	if (!empty($bet)) {
		foreach ($bet as $betId => &$_bet) {
			if (
				!isset($betTypeId)
				|| $betTypeId == $_bet['typeId']
				|| (is_array($otherBetTypeIds) && in_array($_bet['typeId'], $otherBetTypeIds)) 
			) {
				$_bet['brSpecialValue'] = static::specialValueFromDb($_bet['brSpecialValue']);
				$result[$betId] = $_bet;
			}
		}
	}
	return $result;
}

private function hasCancelationOutcome($outcomes) {
	return array_key_exists(static::OUTCOME_REMOVED, $outcomes);
}

/**
 * Determines if match contains score type causing bets to be canceled or cannot be evaulated at least.
 * @param string $cancelationScore If bets should be canceled or is undetermined, this parameter will receive score to be stored as result
 * @param boolean $isUndetermined Receive flag if bet should not receive status "evaluated" (if not being canceled)
 * @return boolean TRUE to be canceled, FALSE to not
 */
private function hasCancelationScore(&$cancelationScore, &$isUndetermined) {
	if (array_key_exists(self::SCORETYPE_C, $this->scores)) {
		$cancelationScore = self::SCORETYPE_C;
		return true;
	}
	if (array_key_exists(self::SCORETYPE_WO, $this->scores)) {
		$cancelationScore = self::SCORETYPE_WO;
		// if match was started bet is not canceled, but bet should not be evaluated
		// now no further check are performed, WO just means that it cannot be evaluated by BR
		//$partialScores = array(
		//	self::SCORETYPE_HT,
		//	self::SCORETYPE_1P,
		//	self::SCORETYPE_1Q,
		//	self::SCORETYPE_SET1,
		//);
		//foreach ($partialScores as $partialScore) {
		//	if (isset($this->scores[$partialScore])) {
		//		$isUndetermined = true;
		//		return false;
		//	}
		//}
		//return true;
		$isUndetermined = true;
		return false;
	}
	return false;
}

/**
 * @param integer $bbasSportId
 * @param integer $bbasBetTypeId
 */
private function shouldBeBetBrUpdateOn(&$import, $bbasSportId, $bbasBetTypeId) {
	return (
		$import->haveNewBetBrUpdateOn()
		&& (in_array($bbasSportId, static::$sportsWithBrUpdateOn) || !in_array($bbasBetTypeId, static::$betTypesWithoutBrUpdateOn))
	);
}

/**
 * Add new computed values into odds data, 'realRate' fields will be added into passed outcome structs.
 * //TODO: add also complementary odds if needed according to bet types config?
 * @param struct $oddsTypeCfg Structure from odds type config
 * @param array $outcomes (outcome => struct), will be updated to contain real odds @see $odds member
 * @param float $winRatio New win ratio to be used for real odds computation
 */
private function computeRealOdds($oddsTypeCfg, &$outcomes, $winRatio) {
	$rates = array();
	foreach ($outcomes as $outcome => $data)
		$rates[$outcome] = $data['rate'];
	$complementary  = (empty($oddsTypeCfg['complementaryColumns']) ? false : $oddsTypeCfg['complementaryColumns']);
	$realRates = It6_Models_Bet::recomputeRates($rates, $winRatio, $complementary);
	foreach ($realRates as $outcome => $rate)
		$outcomes[$outcome]['realRate'] = $rate;
}

/**
 * Merges BR outcomes with BBAS columns forming bet column-rate data
 * @param array $outcomes BR outcomes
 * @param array $columns BBAS column data for outcomes (BR outcome => BBAS data)
 * @return array (bewaColumnId => rate)
 */
private function getImportedBbasColumns($outcomes, $columns) {
	$bbasColumns = array();
	foreach ($columns as $outcome => $column) {
		if (!empty($outcomes[$outcome]))
			$bbasColumns[$column['bewaId']] = $outcomes[$outcome]['realRate'];
	}
	return $bbasColumns;
}

/**
* Merges BR outcomes with existing BBAS columns forming bet column-rate data
* @param struct $dbBet Existing BBAS bet data
* @param array $outcomes BR outcomes
* @param array $columns BBAS column data for outcomes (BR outcome => BBAS data)
* @param array|NULL $otherColumnsForUpdate Other columns for rate update (BR outcome => BBAS column ID)
* @return array (bewaColumnId => rate)
*/
private function getUpdateBbasColumns($dbBet, $outcomes, $columns, $otherColumnsForUpdate = null) {
	$bbasColumns = array();
	foreach ($dbBet['rates'] as $columnId => $rate) {
		$foundOutcome = false;
		foreach ($columns as $outcome => $column) {
			if ($columnId == $column['bewaId']) {
				$foundOutcome = $outcome;
				break;
			}
		}
		if (false === $foundOutcome && !empty($otherColumnsForUpdate)) {
			foreach ($otherColumnsForUpdate as $outcome => $columnIds) {
				if (in_array($columnId, $columnIds)) {
					$foundOutcome = $outcome;
					break;
				}
			}
		}
		if (false !== $foundOutcome)
			$bbasColumns[$columnId] = $outcomes[$foundOutcome]['realRate'];
	}
	return $bbasColumns;
}

/**
 * Determines if bet type's "valid to" time offset for given tournament.
 * @param struct $dbTournament BBAS event structure from DB
 * @param struct $dbBetType BBAS bet type structure from DB
 * @return integer|boolean Numeric value of seconds to be added to match
 *                         "valid to" time or FALSE if it shouldn't be updated.
 */
private function getBetTypeValidToOffset($dbTournament, $dbBetType) {
	$offset = (empty($dbTournament['brTimeOffset']) ? 0 : intval($dbTournament['brTimeOffset']));
	if (empty($offset))
		return 0;
	$multiplier = (empty($dbBetType['brTimeOffset']) ? 0 : floatval($dbBetType['brTimeOffset']));
	if (empty($multiplier))
		return 0;
	else
		return round($offset * $multiplier);
}

/**
 * Detect changes that should be updated in DB
 * @param struct $dbBet BBAS bet structure from DB
 * @param integer $validTo UNIX timestamp of start of match, value being imported
 * @param array $columnRates (bewaColumnId => rate), values being imported
 * @return struct|boolean structure with BBAS bet structure fields that should be updated (can be empty), FALSE is returned when change is not acceptable
 */
private function wasBetChanged($dbBet, $validTo, $columnRates) {
	$changes = array();
	if ($validTo != $dbBet['validTo'])
		$changes['validTo'] = $validTo;
	$ratesChanged = false;
	foreach ($columnRates as $columnId => $rate) {
		if (!isset($dbBet['rates'][$columnId])) {
			// ignoring that DB has not such an column
			// generally we could pick only some columns on creation
		}
		else {
			if ($dbBet['rates'][$columnId] != $rate)
				$ratesChanged = true;
		}
	}
	if ($ratesChanged)
		$changes['rates'] = $columnRates;
	return $changes;
}

/**
 * Examines sport filter if odds type config
 * @param struct $cfg Config for particular odds type
 * @param integer $brSportId BR sport ID
 * @return boolean TRUE if config should be filtered, FALSE if not
 */
protected function filterOddstypeConfigBySport($cfg, $brSportId) {
	if (!empty($cfg['sports'])) {
		if (!empty($cfg['sports']['match'])) {
			$toMatch = $cfg['sports']['match'];
			if (!is_array($toMatch))
				$toMatch = array($toMatch);
			if (!in_array($brSportId, $toMatch))
				return true;
		}
		if (!empty($cfg['sports']['ignore'])) {
			$toIgnore = $cfg['sports']['ignore'];
			if (!is_array($toIgnore))
				$toIgnore = array($toIgnore);
			if (in_array($brSportId, $toIgnore))
				return true;
		}
	}
	return false;
}

protected function importMatch(&$import, &$sport, &$category, &$tournament) {
	if (empty($this->competitors[static::COMPETITOR_HOME]) || empty($this->competitors[static::COMPETITOR_AWAY])) {
		It6_Log::warn("Not all needed competitors are present. brMatchId={$this->brId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$preload = $import->getPreloadedData();
	$dbSport = $preload->getSport($sport->brId);
	if (empty($dbSport)) {
		$import->addUnknownSport($sport);
		It6_Log::warn("Unknown sport: brSportId={$sport->brId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$dbTournament = $preload->getTournament($tournament->brId);
	if (empty($dbTournament)) {
		$import->addUnknownTournament($tournament, $category->text, $dbSport['name']);
		It6_Log::warn("Unknown tournament: brTournamentId={$tournament->brId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$teamHome = &$this->competitors[static::COMPETITOR_HOME];
	$teamAway = &$this->competitors[static::COMPETITOR_AWAY];
	$teamHomeDb = $preload->getCompetitor($teamHome['brTeamId']); // creates new automatically
	$unknownTeams = array();
	if (empty($teamHomeDb))
		$unknownTeams[$teamHome['brTeamId']] = $teamHome['text'];
	$teamAwayDb = $preload->getCompetitor($teamAway['brTeamId']);
	if (empty($teamAwayDb))
		$unknownTeams[$teamAway['brTeamId']] = $teamAway['text'];
	if (!empty($unknownTeams)) {
		$ids = implode(',', array_keys($unknownTeams));
		foreach ($unknownTeams as $brId => $name)
			$import->addUnknownTeam($brId, $name);
		It6_Log::warn("Cannot import match - unknown teams. betradarMatchId={$this->brId}, betradarTeamIds=[$ids]", It6_Log::TAG_BETRADAR_BET_OPERATION);
		return false;
	}
	$now = time();
	$validFrom = $import->getStartedAt(true);
	$validTo = $this->startTimestamp;
	$dbBets = array();
	// which bet is currently parent bet
	$parentBetIdOrig = $this->getMainBetId($import);
	foreach ($this->odds as $oddsType => $specialValues) {
		if (!isset(static::$oddsTypesConfig[$oddsType])) {
			$import->addUnknownOddsType($oddsType);
			It6_Log::warn(
				"Unsupported BR odds type: brMatchId={$this->brId} brOddsType=$oddsType",
				It6_Log::TAG_BETRADAR_BET_OPERATION
			);
			continue;
		}
		foreach (static::$oddsTypesConfig[$oddsType] as $cfg) {
			// test if config is turned off 
			if (!empty($cfg['off']))
				continue;
			// test if config should be ignored according to current sport
			if ($this->filterOddstypeConfigBySport($cfg, $sport->brId))
				continue;
			// process bets from XML feed for odds type config
			$betTypeId = $cfg['betType'];
			$betSubtypeId = $cfg['betSubtype'];
			$betType = $preload->getBetType($betTypeId);
			$betTypeSettings = $preload->getBetTypeSettings($tournament->brId, $betTypeId);
			$betTypeTextNote = $preload->getBetTypeTextNote($betTypeId, $dbSport['bewaId']);
			$otherBetTypeIds = (empty($cfg['updateOtherBetTypes']) ? null : $cfg['updateOtherBetTypes']);
			foreach ($specialValues as $specialValue => $outcomes) {
				$dbSpecialValue = static::specialValueToDb($specialValue);
				// get db bet
				$dbBet = $this->getBbasBet($import, $this->brBetType, $this->brId, $oddsType, $specialValue, $betTypeId, $otherBetTypeIds);
				if ($this->hasCancelationOutcome($outcomes)) {
					if (!empty($dbBet)) {
						$betIds = array_map(function($b) { return $b['bewaId']; }, $dbBet);
						$import->suspendBet($betIds, It6_Betradar_Import::CANCELED_IN_OFFER);
					}
					continue;
				}
				// get bet subtype
				if (!is_numeric($betSubtypeId)) {
					$method = $betSubtypeId;
					$betSubtypeId = $this->$method($import, $betTypeId, $specialValue, $outcomes);
					if (empty($betSubtypeId)) {
						It6_Log::warn(
							"Undetermined BBAS bet subtype: brMatchId={$this->brId} brOddsType=$oddsType brSpecialValue=$specialValue bewaBetTypeId=$betTypeId",
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array('brOutcomes' => $outcomes)
						);
						continue;
					}
				}
				if (empty($betSubtypeId)) {
					It6_Log::warn(
						"Undetermined BBAS bet subtype: brMatchId={$this->brId} brOddsType=$oddsType brSpecialValue=$specialValue bewaBetTypeId=$betTypeId",
						It6_Log::TAG_BETRADAR_BET_OPERATION
					);
					continue;
				}
				// get BBAS columns IDs
				if (is_array($cfg['columns']))
					$columns = $cfg['columns'];
				else {
					$method = $cfg['columns'];
					$columns = $this->$method($import, $betTypeId, $betSubtypeId, $outcomes);
					if (empty($columns)) {
						It6_Log::warn(
							"Unknown bet columns: betTypeId=$betTypeId betSubtypeId=$betSubtypeId",
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array(
								'betTypeId' => $betTypeId,
								'betSubtypeId' => $betSubtypeId,
								'outcomes' => $outcomes,
							)
						);
						continue;
					}
				}
				// recompute rates
				$minWinRatio = null;
				if (!empty($betTypeSettings[$betSubtypeId]['minWinRatio']))
					$minWinRatio = floatval($betTypeSettings[$betSubtypeId]['minWinRatio']);
				if (empty($minWinRatio))
					$minWinRatio = $import->getDefaultMinWinRatio();
				$this->computeRealOdds($cfg, $outcomes, $minWinRatio);
				// get BR params
				if (!empty($cfg['brParams'])) {
					$method = $cfg['brParams'];
					$brParams = $this->$method($import, $specialValue, $outcomes);
				}
				else
					$brParams = null;
				$extraData = array(
					'timeOffsetTournament' => $dbTournament['brTimeOffset'],
					'timeOffsetBetType' => $betType['brTimeOffset'],
				);
				if (empty($dbBet)) { // create bet
					if ($this->startTimestamp <= $now) {
						It6_Log::warn(
							"Bet cannot be created - match already started. brMatchId={$this->brId} brOddsType={$oddsType} specialValue={$specialValue}",
							It6_Log::TAG_BETRADAR_BET_OPERATION
						);
						continue;
					}
					// determine realTypeId
					if (false === $betType['groupTypeIds'])
						$realBetTypeId = null;
					else {
						// result of this function is not cached - so we assume that my previously imported bets are taken in account
						$usedRealBetTypeIds = $import->getUsedRealBetTypeIds($this->brBetType, $this->brId, $betTypeId, $parentBetIdOrig);
						if (is_array($usedRealBetTypeIds)) {
							$freeRealBetTypeIds = array_diff($betType['groupTypeIds'], $usedRealBetTypeIds);
							if (!empty($freeRealBetTypeIds)) {
								sort($freeRealBetTypeIds);
								$realBetTypeId = $freeRealBetTypeIds[0];
							}
						}
						if (empty($realBetTypeId)) {
							It6_Log::warn("No free bet type in group: brMatchId={$this->brId} betTypeId=$betTypeId", It6_Log::TAG_BETRADAR_BET_OPERATION);
							continue;
						}
					}
					$textNote = $betTypeTextNote; // would be possible to change it here
					$riskLimit = (empty($betTypeSettings[$betSubtypeId]['riskLimit']) ? $import->getDefaultRiskLimit() : $betTypeSettings[$betSubtypeId]['riskLimit']);
					if (!empty($cfg['betName'])) {
						$method = $cfg['betName'];
						$betName = $this->$method($teamHomeDb['shortName'], $teamAwayDb['shortName'], $outcomes, $specialValue, null); // $textNote not passed
					}
					else
						$betName = $this->createBetName($teamHomeDb['shortName'], $teamAwayDb['shortName'], null); // $textNote not passed
					$brUpdateOn = ($this->shouldBeBetBrUpdateOn($import, $dbSport['bewaId'], $betTypeId) ? 1 : 0);
					$dbBet = array(
						//'bewaId' => 'BBAS bet ID', // will be added in creation function
						'brBetType' => $this->brBetType,
						'brId' => $this->brId,
						'brOddsType' => $oddsType,
						'brSpecialValue' => $dbSpecialValue,
						'brCompetitorId' => null,
						'brParams' => $brParams,
						'typeId' => $betTypeId,
						'realTypeId' => $realBetTypeId,
						'subtypeId' => $betSubtypeId,
						'eventId' => $dbTournament['bewaId'],
						'betName' => $betName,
						'validFrom' => $validFrom,
						'validTo' => $validTo,
						'parentId' => $parentBetIdOrig,
						'textNote' => $textNote,
						'riskLimit' => $riskLimit,
						'brUpdate' => $brUpdateOn,
						'parentId' => $parentBetIdOrig,
					);
					$bbasColumns = $this->getImportedBbasColumns($outcomes, $columns);
					if (empty($bbasColumns)) {
						It6_Log::warn(
							'Undetermined columns for bet being created.',
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array('brMatchId' => $this->brId, 'brOddsType' => $oddsType, 'brSpecialValue' => $specialValue)
						);
					}
					else {
						$created = $import->createBet($dbBet, $bbasColumns, $extraData);
						if (!empty($dbBet['bewaId'])) {
							$brTeams = array($teamHome['brTeamId'], $teamAway['brTeamId']);
							$associated = $import->associateBetAndTeams($dbBet['bewaId'], $brTeams);
						}
					}
					if (!empty($dbBet['bewaId']))
						$dbBets[] = $dbBet;
				}
				else { // update bet
					$_dbBets = $dbBet;
					foreach ($_dbBets as $dbBet) {
						if (empty($dbBet['brUpdate'])) {
							It6_Log::info(
								'Bet update is turned off.',
								It6_Log::TAG_BETRADAR_BET_OPERATION,
								array('betId' => $dbBet['bewaId'], 'dbBet' => $dbBet)
							);
							continue;
						}
						// determine if there are some changes (validTo, rates)
						$otherColumnsForUpdate = (!empty($cfg['updateOtherColumns']) ? $cfg['updateOtherColumns'] : null);
						$columnRates = $this->getUpdateBbasColumns($dbBet, $outcomes, $columns, $otherColumnsForUpdate);
						$changes = $this->wasBetChanged($dbBet, $validTo, $columnRates);
						if (false === $changes) {
							$_changes = array(
								'validTo' => $validTo,
								'rates' => $columnRates,
							);
							It6_Log::warn(
								'Bet update not acceptable',
								It6_Log::TAG_BETRADAR_BET_OPERATION,
								array('betId' => $dbBet['bewaId'], 'dbBet' => $dbBet, 'changes' => $_changes)
							);
						}
						else if (empty($changes)) {
							It6_Log::info(
								'Bet is up to date.',
								It6_Log::TAG_BETRADAR_BET_OPERATION,
								array('betId' => $dbBet['bewaId'], 'dbBet' => $dbBet)
							);
						}
						else {
							$updated = $import->updateBet($dbBet['bewaId'], $changes, $extraData, $validTo);
							It6_Log::info(
								'Bet was updated',
								 It6_Log::TAG_BETRADAR_BET_OPERATION,
								 array(
								 	'betId' => $dbBet['bewaId'],
								 	'dbBet' => $dbBet,
								 	'changes' => $changes,
								 	'extraData' => $extraData,
								 )
							);
						}
						$dbBets[] = $dbBet;
					}
				}
			} // effSpecialValues
			//TODO: suspend all special values that are not present
		} // configs
	} // oddsTypes
	$parentBetId = $this->getNewMainBetId($import, $sport->brId, $nonBrBetIds);
	if ($parentBetIdOrig != $parentBetId) {
		$import->updateBetsParentId($this->brBetType, $this->brId, $parentBetIdOrig, $parentBetId, $nonBrBetIds);
	}
	// manage combinations (try to not delete any, only add new)
	$import->updateBetCorrelations($dbBets);
	$this->importMatchResults($import, $dbSport, $dbTournament);
}

protected function importMatchResults(&$import, $dbSport, $dbTournament) {
	if (empty($this->scores))
		return;
	$sportId = $dbSport['bewaId'];
	$eventId = $dbTournament['bewaId'];
	// retrieve bet data for this BR match
	$dbBets = $import->getPreloadedData()->getBetsByMatchId($this->brBetType, $this->brId);
	if (empty($dbBets)) {
		It6_Log::warn(
			'Cannot set bet results, bet not found',
			It6_Log::TAG_BETRADAR_RESULT_OPERATION,
			array('brMatchId' => $this->brId)
		);
		return;
	}
	$parentIds = array();
	$allBets = array();
	foreach ($dbBets as $oddsType => $specialValues) {
		foreach ($specialValues as $specialValue => $betIds) {
			foreach ($betIds as $betId => $bet) {
				$allBets[$betId] = $bet;
				if (!empty($bet['parentId']))
					$parentIds[$bet['parentId']] = true;
			}
		}
	}
	$dbBets = $import->getBetsByParentId(array_keys($parentIds), true, true);
	foreach ($dbBets as $betId => $bet) {
		if (!isset($allBets[$betId]))
			$allBets[$betId] = $bet;
	}
	unset($dbBets);
	$canceledScore = static::SCORETYPE_C;
	$isUndetermined = false; // TRUE if match result is undetermined
	$beingCanceled = $this->hasCancelationScore($canceledScore, $isUndetermined);
	foreach ($allBets as $betId => $bet) {
		$betTypeId = $bet['typeId'];
		$betSubtypeId = $bet['subtypeId'];
		if (empty(static::$matchResultsConfig[$betTypeId])) {
			$import->addUnknownResultBetType($betTypeId);
			continue;
		}
		$cfgBetSubtypeId = false;
		foreach (array_keys(static::$matchResultsConfig[$betTypeId]) as $id) {
			$ids = array_map(function($s) { return trim($s); }, explode(';', $id));
			if (in_array($betSubtypeId, $ids)) {
				$cfgBetSubtypeId = $betSubtypeId;
				break;
			}
			else if (in_array('*', $ids))
				$cfgBetSubtypeId = '*';
		}
		if (false === $cfgBetSubtypeId) {
			It6_Log::info(
				'Skipping results for unsupported bet subtype',
				It6_Log::TAG_BETRADAR_RESULT_OPERATION,
				array('betId' => $betId, 'typeId' => $betTypeId, 'subtypeId' => $betSubtypeId)
			);
			continue;
		}
		$cfg = static::$matchResultsConfig[$betTypeId][$cfgBetSubtypeId];
		if (!empty($cfg['off']))
			continue;
		if ($beingCanceled) {
			$import->updateBetResult($betId, $canceledScore, array(), $this->scores, true, true);
			It6_Log::info(
				'Bet was canceled due to BR score.',
				It6_Log::TAG_BETRADAR_RESULT_OPERATION,
				array('betId' => $betId, 'scores' => $this->scores)
			);
			continue;
		}
		else if ($isUndetermined) {
			// save scores only
			$import->updateBetResult($betId, $canceledScore, array(), $this->scores, false, true);
			It6_Log::info(
				'Bet result is undetermined, only scores saved.',
				It6_Log::TAG_BETRADAR_RESULT_OPERATION,
				array('betId' => $betId, 'scores' => $this->scores)
			);
			continue;
		}
		$method = (empty($cfg['effectiveScores']) ? 'resultScoreFt' : $cfg['effectiveScores']);
		$methodParams = array($sportId);
		if (is_array($method)) {
			$methodParams = array_merge($methodParams, array_slice($method, 1));
			$method = $method[0];
		}
		// get effective scores
		list($score, $scoreHome, $scoreAway) = call_user_func_array(array($this, $method), $methodParams);
		if (false === $score) {
			It6_Log::warn('Score not found in BR data.', It6_Log::TAG_BETRADAR_RESULT_OPERATION, array('betId' => $betId));
			continue;
		}
		// get special value
		$specialValue = null;
		$brParams = null;
		$determined = false;
		if (!empty($cfg['determineSpecialValue'])) {
			$method = $cfg['determineSpecialValue'];
			$methodParams = array($bet);
			if (is_array($method)) {
				$methodParams = array_merge($methodParams, array_slice($method, 1));
				$method = $method[0];
			}
			$determined = call_user_func_array(array($this, $method), $methodParams);
			if ($determined) {
				$specialValue = $determined['specialValue'];
				$brParams = $determined['brParams'];
			}
		}
		if (!$determined && !empty($bet['brId'])) {
			$specialValue = $bet['brSpecialValue'];
			if (empty($cfg['brParams']))
				$brParams = $bet['brParams'];
			else {
				$method = $cfg['brParams'];
				$brParams = $this->$method($bet['brParams']);
			}
		}
		// modify scores if needed
		if (!empty($cfg['modifyScores'])) {
			$method = $cfg['modifyScores'];
			list($scoreHome, $scoreAway) = $this->$method(array($scoreHome, $scoreAway), $specialValue, $brParams);
		}
		// determine winning columns
		if (is_array($cfg['winningColumns']))
			$columns = $cfg['winningColumns'];
		else {
			$method = $cfg['winningColumns'];
			$userData = (isset($cfg['winningColumnsUserData']) ? $cfg['winningColumnsUserData'] : null);
			$columns = $this->$method($import, $bet, $score, array($scoreHome, $scoreAway), $specialValue, $userData);
		}
		$setResult = true;
		$cancel = false;
		$scoreOnly = false;
		if (false === $columns) { // undetermined winning column(s)
			$action = (empty($cfg['undeterminedWinningColumns']) ? null : $cfg['undeterminedWinningColumns']);
			switch ($action) {
			case self::UWCACTION_CANCEL:
				$cancel = true;
				It6_Log::info('Undetermined winning column(s), bet is going to be canceled.', It6_Log::TAG_BETRADAR_RESULT_OPERATION, array('betId' => $betId, 'score' => $score));
				break;
			default:
				$setResult = false;
				It6_Log::warn('Cannot determine winning column(s).', It6_Log::TAG_BETRADAR_RESULT_OPERATION, array('betId' => $betId));
				break;
			}
		}
		if ($setResult) {
			$import->updateBetResult($betId, $score, $columns, $this->scores, $cancel, $scoreOnly);
		}
	}
}

protected function importOutright(&$import, &$sport, &$category) {
	if (empty($this->competitors)) {
		It6_Log::warn("No competitors are present. brMatchId={$this->brId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$preload = $import->getPreloadedData();
	$dbSport = $preload->getSport($sport->brId);
	if (empty($dbSport)) {
		$import->addUnknownSport($sport);
		It6_Log::warn("Unknown sport: brSportId={$sport->brId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$dbTournament = $preload->getTournament($this->tournamentId);
	if (empty($dbTournament)) {
		$import->addUnknownTournament($tournament, $category->text, $dbSport['name']);
		It6_Log::warn("Unknown tournament: brTournamentId={$this->tournamentId}", It6_Log::TAG_BETRADAR_OPERATION);
		return;
	}
	$now = time();
	$validFrom = $import->getStartedAt(true);
	$validTo = $this->startTimestamp;
	$dbTeams = array();
	$competitorTeamMap = array();
	$unknownTeams = array();
	foreach ($this->competitors as $brCompetitorId => $competitor) {
		$brTeamId = $competitor['brTeamId'];
		$competitorTeamMap[$brCompetitorId] = $brTeamId;
		$dbTeam = $preload->getCompetitor($brTeamId); // creates new automatically
		if (empty($dbTeam))
			$unknownTeams[$brTeamId] = $competitor['name'];
		else
			$dbTeams[$brTeamId] = $dbTeam;
	}
	if (!empty($unknownTeams)) {
		$ids = implode(',', array_keys($unknownTeams));
		foreach ($unknownTeams as $brId => $name)
			$import->addUnknownTeam($brId, $name);
		It6_Log::warn("Cannot import match - unknown teams. betradarMatchId={$this->brId}, betradarTeamIds=[$ids]", It6_Log::TAG_BETRADAR_BET_OPERATION);
		return false;
	}
	foreach ($this->odds as $oddsType => $effSpecialValues) {
		if (!isset(static::$oddsTypesConfig[$oddsType])) {
			$import->addUnknownOddsType($oddsType);
			It6_Log::warn(
				"Unsupported BR odds type: brMatchId={$this->brId} brOddsType=$oddsType",
				It6_Log::TAG_BETRADAR_BET_OPERATION
			);
			continue;
		}
		foreach (static::$oddsTypesConfig[$oddsType] as $cfg) {
			if (!empty($cfg['off']))
				continue;
			$betTypeId = $cfg['betType'];
			$betSubtypeId = $cfg['betSubtype'];
			$betType = $preload->getBetType($betTypeId);
			$betTypeSettings = $preload->getBetTypeSettings($tournament->brId, $betTypeId);
			$betTypeTextNote = $preload->getBetTypeTextNote($betTypeId, $sport->brId);
			$extraData = array( // outrights will always have zero offsets, I guess
				'timeOffsetTournament' => 0,
				'timeOffsetBetType' => 0,
			);
			foreach ($effSpecialValues as $effSpecialValue => $brCompetitors) {
				$outcomes = array();
				$specialValue = $effSpecialValue;
				$dbSpecialValue = static::specialValueToDb($specialValue);
				foreach ($brCompetitors as $brCompetitorId => $outright) {
					$rate = $outright['rate'];
					$outcomes[$brCompetitorId] = array('rate' => $rate);
				}
				$dbBets = $this->getBbasBet($import, $this->brBetType,  $this->brId, $oddsType, $specialValue, $betTypeId);
				$beingCanceled = $this->hasCancelationResult();
				if ($beingCanceled) {
					if (!empty($dbBets))
						$import->suspendBet(array_keys($dbBets), It6_Betradar_Import::CANCELED_IN_OFFER);
					continue;
				}
				// recompute rates
				$minWinRatio = null;
				if (!empty($betTypeSettings[$betSubtypeId]['minWinRatio']))
					$minWinRatio = floatval($betTypeSettings[$betSubtypeId]['minWinRatio']);
				if (empty($minWinRatio))
					$minWinRatio = $import->getDefaultMinWinRatio();
				$this->computeRealOdds($cfg, $outcomes, $minWinRatio);
				// get BR params
				if (!empty($cfg['brParams'])) {
					$method = $cfg['brParams'];
					$brParams = $this->$method($import, $specialValue, $outcomes);
				}
				else
					$brParams = null;
				// now determine which bets was removed from offer, which are new and which are to be updated
				$competitorBetIdMap = array();
				$removedBets = array();
				foreach ($dbBets as $betId => $dbBet) {
					$competitorBetIdMap[$dbBet['brCompetitorId']] = $betId;
					if (!isset($brCompetitors[$dbBet['brCompetitorId']]))
						$removedBets[] = $betId;
				}
				if (!empty($removedBets))
					$import->suspendBet($removedBets, It6_Betradar_Import::CANCELED_IN_OFFER);
				$updatedBets = array();
				$newBets = array();
				foreach ($brCompetitors as $brCompetitorId => $outright) {
					$betId = $competitorBetIdMap[$brCompetitorId];
					if (isset($dbBets[$betId]))
						$updatedBets[$betId] = false;
					else
						$newBets[$brCompetitorId] = false;
				}
				if (!empty($newBets)) {
					if ($this->startTimestamp <= $now) {
						It6_Log::warn(
							"Bet cannot be created - outright already started. brMatchId={$this->brId} brOddsType={$oddsType} specialValue={$specialValue}",
							It6_Log::TAG_BETRADAR_BET_OPERATION
						);
						continue;
					}
					else {
						foreach ($newBets as $brCompetitorId => &$bet) {
							// realTypeId should be always null, because outright has no parent bet
							$realBetTypeId = null;
							$textNote = $betTypeTextNote; // would be possible to change it here
							$riskLimit = (empty($betTypeSettings[$betSubtypeId]['riskLimit']) ? $import->getDefaultRiskLimit() : $betTypeSettings[$betSubtypeId]['riskLimit']);
							$dbTeam = $dbTeams[ $competitorTeamMap[$brCompetitorId] ];
							$betName = $dbTeam['shortName'];
							if (!empty($textNote))
								$betName .= " $textNote";
							$brUpdateOn = ($this->shouldBeBetBrUpdateOn($import, $dbSport['bewaId'], $betTypeId) ? 1 : 0);
							$columns = array($brCompetitorId => array('bewaId' => $cfg['column'], 'order' => 1));
							$dbBet = array(
								//'bewaId' => 'BBAS bet ID', // will be added in creation function
								'brBetType' => $this->brBetType,
								'brId' => $this->brId,
								'brOddsType' => $oddsType,
								'brSpecialValue' => $dbSpecialValue,
								'brCompetitorId' => $brCompetitorId,
								'brParams' => $brParams,
								'typeId' => $betTypeId,
								'realTypeId' => $realBetTypeId,
								'subtypeId' => $betSubtypeId,
								'eventId' => $dbTournament['bewaId'],
								'betName' => $betName,
								'validFrom' => $validFrom,
								'validTo' => $validTo,
								'parentId' => $parentBetIdOrig,
								'textNote' => $textNote,
								'riskLimit' => $riskLimit,
								'brUpdate' => $brUpdateOn,
								'parentId' => null,
							);
							$bbasColumns = $this->getImportedBbasColumns($outcomes, $columns);
							$created = $import->createBet($dbBet, $bbasColumns, $extraData);
							if (!empty($dbBet['bewaId']))
								$bet = $dbBet;
						}
					}
				}
				foreach ($updatedBets as $betId => &$updated) {
					$dbBet = $dbBets[$betId];
					// determine if there are some changes (validTo, rates)
					$columns = array($brCompetitorId => array('bewaId' => $cfg['column'], 'order' => 1));
					$columnRates = $this->getImportedBbasColumns($outcomes, $columns);
					$changes = $this->wasBetChanged($dbBet, $validTo, $columnRates);
					if (false === $changes) {
						$bet = array(
							'brId' => $this->brId,
							'validTo' => $validTo,
							'rates' => $columnRates,
						);
						It6_Log::warn('Bet update not acceptable', It6_Log::TAG_BETRADAR_BET_OPERATION, array('dbBet' => $dbBet, 'newBet' => $bet));
					}
					else if (!empty($changes)) {
						$updated = $import->updateBet($bet['bewaId'], $changes, $extraData, $validTo);
					}
				}
				$import->updateBetCorrelations($dbBets);
				$this->importOurightResults($import, $dbSport, $dbTournament);
			} // effSpecialalues
		} // configs
	} // oddsTypes
}

protected function importOutrightResults(&$import, $dbSport, $dbTournament) {
	if (empty($this->scores))
		return;
	$sportId = $dbSport['bewaId'];
	$eventId = $dbTournament['bewaId'];
	// retrieve bet data for this BR match
	$dbBets = $import->getPreloadedData()->getBetsByMatchId($this->brBetType, $this->brId);
	foreach ($dbBets as $matchId => $oddsTypes) {
		foreach ($oddsTypes as $oddsType => $specialValues) {
			foreach ($specialValues as $specialValue => $betIds) {
				foreach ($betIds as $betId => $bet) {
					$betTypeId = $bet['typeId'];
					$betSubtypeId = $bet['subtypeId'];
					$brCompetitorId = $bet['brCometitorId'];
					if (empty(static::$outrightResultsConfig[$betTypeId])) {
						$import->addUnknownResultBetType($betTypeId);
						continue;
					}
					$cfg = static::$outrightResultsConfig[$betTypeId];
					if (!empty($cfg['off']))
						continue;
// 					if (empty($cfg['brParams']))
// 						$brParams = $bet['brParams'];
// 					else {
// 						$method = $cfg['brParams'];
// 						$brParams = $this->$method($bet['brParams']);
// 					}
					// determine winning columns
					$position = (empty($this->scores[$brCompetitorId]) ? '' : $this->scores[$brCompetitorId]);
					$columns = $this->winningColumnsOutrightDefault($import, $betTypeId, $betSubtypeId, $position, null, $specialValue, null);
					if (false === $columns) {
						It6_Log::warn('Cannot determine winning columns.', It6_Log::TAG_BETRADAR_RESULT_OPERATION, array('betId' => $betId));
					}
					else {
						$import->updateBetResult($betId, $position, $columns, null);
					}
				}
			}
		}
	}
}

/**
 * Imports the match (wrapper that delegates to more specialized method for match or outright)
 * @param It6_Betradar_Import $import
 * @param It6_Betradar_Sport $sport
 * @param It6_Betradar_Category $category
 * @param It6_Betradar_Tournament $tournament Don't set for outright
 */
public function import(&$import, &$sport, &$category, &$tournament = null) {
	if ($this->isOutright)
		$this->importOutright($import, $sport, $category);
	else
		$this->importMatch($import, $sport, $category, $tournament);
	$tournamentId = ($this->isOutright ? $this->tournamentId : $tournament->brId);
	It6_Models_BetradarImportLog::log($sport->text, $category->text, $tournament->text, $tournamentId);
}

/**
 * Standard way how create bet name
 * @param string $homeTeamName
 * @param string $awayTeamName
 * @param string $textNote
 */
public function createBetName($homeTeamName, $awayTeamName, $textNote = null) {
	$name = "$homeTeamName - $awayTeamName";
	$textNote = trim($textNote);
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}

private function betSubtypeNeedsAllTypeSubtypesPreloaded($subtype) {
	return ('betSubtypeScore' == $subtype);
}

public function getBetTypesNeedingAllColumnSets() {
	$typeIds = array();
	foreach (array_keys($this->odds) as $oddstype) {
		if (isset(static::$oddsTypesConfig[$oddstype])) {
			foreach (static::$oddsTypesConfig[$oddstype] as $cfg) {
				if ($this->betSubtypeNeedsAllTypeSubtypesPreloaded($cfg['betSubtype']))
					$typeIds[$cfg['betType']] = true;
			}
		}
	}
	return array_keys($typeIds);
}

/**
 * @return array(tournamentBrId => bewaTypeId => subtypeSpec) where subtype spec is TRUE for all, or bewaSubtypeId
 */
public function getBetTypesToPreload() {
	$betTypes = array();
	foreach (array_keys($this->odds) as $oddsType) {
		if (isset(static::$oddsTypesConfig[$oddsType])) {
			foreach (static::$oddsTypesConfig[$oddsType] as $cfg) {
				$typeId = $cfg['betType'];
				$subtypeId = $cfg['betSubtype'];
				if ($this->betSubtypeNeedsAllTypeSubtypesPreloaded($subtypeId))
					$betTypes[$typeId] = true;
				else if (!isset($betTypes[$typeId]))
					$betTypes[$typeId] = array($subtypeId);
				else if (true !== $betTypes[$typeId] && !in_array($subtypeId, $betTypes[$typeId]))
					$betTypes[$typeId][] = $subtypeId;
			}
		}
	}
	return array($this->tournamentId => $betTypes);
}

/**
 * Returns subtype IDs that are used by odds type config.
 * @return array List of BBAS bet subtype IDs
 */
public static function getBetSubtypesFromConfig() {
	$subtypeIds = array();
	foreach (static::$oddsTypesConfig as $cfgs) {
		foreach ($cfgs as $cfg) {
			$id = $cfg['betSubtype'];
			if (!empty($id) && is_numeric($id))
				$subtypeIds[$id] = true;
		}
	}
	return array_keys($subtypeIds);
}

/**
 * Returns all bets to be queried from database
 * @return array|boolean (brOddsType => brSpecialValues) or TRUE if all for this BR match
 */
public function getBetsToPreload() {
	if ($this->hasResults())
		return true;
	else {
		$bets = array();
		foreach ($this->odds as $oddsType => $specialValues) {
			$effSpecValues = array_keys($specialValues);
			if (!empty($effSpecValues))
				$bets[$oddsType] = array_map(array($this, 'specialValueToDb'), $effSpecValues);
		}
		return $bets;
	}
}

private static function formatEffectiveValue($value) {
	if (1 == preg_match('/^[0-9.+-]+$/', $value))
		$value = str_replace('.', ',', $value);
	
	return $value;	
} 

/**
 * Parses special value from string using given format.
 * @param string $value Special value string
 * @param string $format One of:
 *                       <ul>
 *                         <li>'none' - value is only trimmed</li>
 *                         <li>'float' - float number is parsed</li>
 *                       </ul>
 *                       unknown format is equal to 'none'
 */
protected static function parseSpecialValue($value, $format) {
	$format = (empty($format) ? 'none' : strtolower($format));
	switch ($format) {
		case 'float':
			return It6_Betradar_Import::parseFloat(trim($value));
		case 'none':
		default:
			return trim((string)$value);
	}
}

private function getEffectiveSpecialValue($oddsType, $outcome, $specialValue) {
	$eff = $specialValue;
	$callback = null;
	$cfgPriority = 0;
	if (isset(static::$oddsTypesConfig[$oddsType])) {
		foreach (static::$oddsTypesConfig[$oddsType] as $cfg) {
			if (isset($cfg['specialValue']['effectiveValue'])) {
				if (!isset($cfgValue) || $cfgPriority < $cfg['priority']) {
					$cfgValue = $cfg['specialValue']['effectiveValue'];
					$cfgPriority = $cfg['priority'];
				}
			}
		}
	}
	if (isset($cfgValue)) {
		if (is_array($cfgValue)) {
			$method = $cfgValue[0];
			$eff = $this->$method($specialValue, $outcome);
		}
		else
			$eff = $cfgValue;
	}
	return $eff;
}

// ***** betName callbacks : ($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) -> string *****
/*
protected function betNameOutcome($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	if (is_array($outcomes))
		$outcomes = implode('/', $outcomes);
	$name = "$homeTeamName - $awayTeamName $outcomes";
	$textNote = trim($textNote);
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}
*/

protected function betNameSpecialValueSimple($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$name = "$homeTeamName - $awayTeamName " . static::formatEffectiveValue((string)$specialValue);
	$textNote = trim($textNote);
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}

protected function betNameSpecialValueHome($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$homeSpecVal = static::formatEffectiveValue((string)$outcomes['1']['specialValue']);
	$name = "$homeTeamName - $awayTeamName $homeSpecVal";
	$textNote = trim($textNote);
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}

/**
 * Uses real special values for home and away outcomes to create name like
 * "homeTeam homeSv - awayTeam awaySv" with optionaly text note appended.
 * Special values in name will always have sign. 
 */
protected function betNameSpecialValueHomeAway($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$fnAddPlus = function($s) { return (1 == preg_match('/^[+-]/', (string)$s) ? $s : "+$s"); };
	$homeSpecVal = static::formatEffectiveValue( $fnAddPlus($outcomes['1']['specialValue']) );
	$awaySpecVal = static::formatEffectiveValue( $fnAddPlus($outcomes['2']['specialValue']) );
	$name = "$homeTeamName $homeSpecVal - $awayTeamName $awaySpecVal";
	$textNote = trim($textNote);
	if (!empty($textNote))
	$name .= " $textNote";
	return $name;
}

protected function betNameSpecialValuePlusAbs($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$name = "$homeTeamName - $awayTeamName +" . static::formatEffectiveValue( (string)abs(floatval($specialValue)) );
	$textNote = trim($textNote);
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}

protected function betNameSpecialValueHandicapByDiff($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$name = "$homeTeamName - $awayTeamName ";
	$specialValue = floatval($specialValue);
	$s = static::formatEffectiveValue((string)$specialValue);
	if ($specialValue > 0)
		$name .= "$s:0";
	else if ($specialValue < 0)
		$name .= "0:$s";
	else {
		$name .= '0:0';
		It6_Log::warn(
			'Importing zero european handicap',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'homeTeam' => $homeTeamName,
				'awayTeam' => $awayTeamName,
				'specialValue' => $specialValue,
			)
		);
	}
	if (!empty($textNote))
		$name .= " $textNote";
	return $name;
}

/**
* Takes given special value, uses it as home special value and zero for away special value if s.v. was positive,
* and as positive away special value and zero for home special value if s.v. was negative.
* Name is then created as "team1 sv1 - team2 sv2" with optionaly text note appended.
*/
protected function betNameSpecialValuePositiveAndZero($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$s = static::formatEffectiveValue((string)abs($specialValue));
	if (0 > $specialValue) {
		$specialValue = "+0";
		$antiSpecialValue = "+$s";
	}
	else if (0 < $specialValue) {
		$specialValue = "+$s";
		$antiSpecialValue = "+0";
	}
	else {
		$specialValue = "+$s";
		$antiSpecialValue = "+$s";
	}
	$name = "$homeTeamName $homeSv - $awayTeamName $awaySv";
	$textNote = trim($textNote);
	if (!empty($textNote))
	$name .= " $textNote";
	return $name;
}

/**
 * Takes given special value, uses it as home special value and creates antisymetric away special value.
 * Name is then created as "team1 sv1 - team2 sv2" with optionaly text note appended.
 */
protected function betNameSpecialValueAntisymetric($homeTeamName, $awayTeamName, $outcomes, $specialValue, $textNote = null) {
	$s = static::formatEffectiveValue((string)abs($specialValue));
	if (0 > $specialValue) {
		$specialValue = "-$s";
		$antiSpecialValue = "+$s";
	}
	else if (0 < $specialValue) {
		$specialValue = "+$s";
		$antiSpecialValue = "-{$s}";
	}
	else {
		$specialValue = "$s";
		$antiSpecialValue = "$s";
	}
	$name = "$homeTeamName $specialValue - $awayTeamName $antiSpecialValue";
	$textNote = trim($textNote);
	if (!empty($textNote))
	$name .= " $textNote";
	return $name;
}

// ***** special value callbacks *****
//     ***** effectiveValue callbacks : ($specialValue, $outcome) -> string newSpecialValue *****

protected function specialValueEffectiveParseFloat($specialValue, $outcome) {
	if (1 == preg_match('/^[0-9,+-]+$/', $specialValue))
		$specialValue = str_replace(',', '.', $specialValue);
	return (string)floatval($specialValue);
}

protected function specialValueEffectiveFloatOrPass($specialValue, $outcome) {
	if (1 == preg_match('/^[0-9,+-]+$/', $specialValue)) {
		$specialValue = str_replace(',', '.', $specialValue);
		return (string)floatval($specialValue);
	}
	else
		return trim($specialValue);
}

protected function specialValueEffectiveAbs($specialValue, $outcome) {
	return (string)abs($this->specialValueEffectiveParseFloat($specialValue, $outcome));
}

/**
 * Expects outcomes 1 (home) or 2 (away), creates effective value as home special value (with minus sign or without sign),
 * where it is assumed that away=-home (they are antisymetric).
 */
protected function specialValueEffectiveHomeAntisymetric($specialValue, $outcome) {
	$specialValue = $this->specialValueEffectiveParseFloat($specialValue, $outcome);
	return ('2' == $outcome ? -1 : 1) * $specialValue;
}

// ***** odds type callbacks : (import, integer bewaBetType, string specialValue, array outcomes) -> integer bewaSubtype *****

/**
 * Matches subtype that has exactly the same column names,
 * new subtype is created if none was matched.
 */
protected function betSubtypeScore(&$import, $betType, $specialValue, $outcomes) {
	$columnSets = $import->getPreloadedData()->getBetTypeColumnSets($betType);
	if (empty($columnSets))
		return null;
	$toMatch = array_flip(array_keys($outcomes));
	$matchCount = count($toMatch);
	$matched = null;
	foreach ($columnSets as $betSubtype => $columns) {
		if (count($columns) != $matchCount)
			continue;
		 // outcome => dummy
		foreach ($columns as $columnId => $column) {
			$name = $column['name'];
			if (isset($toMatch[$name]))
				unset($toMatch[$name]);
			else
				break;
		}
		$notMatched = count($toMatch);
		if (0 == $notMatched)
			return $betSubtype;
	}
	// ( 'subtypeId' => subtypeId, 'columns' => (columnId => ('bewaId' => columnId, 'name' => columnName)) )
	$betSubtype = $import->createBetSubtype($betType, $outcomes, true);
	if ($betSubtype) {
		$betSubtypeId = $betSubtype['subtypeId'];
		$import->getPreloadedData()->addBetTypeColumnSet($betType, $betSubtypeId, $betSubtype['columns']);
		return $betSubtypeId;
	}
	else
		return null;
}

// ***** bet subtype column mapping callbacks: (importDataSource, integer bewaBetTypeId, integer bewaSubtypeId, array(string brOutcomes)) -> array(string brOutcome => integer bewaColumnId) *****

protected function betSubtypeColumnsSameName(&$import, $betType, $betSubtype, $outcomes) {
	$columnSets = $import->getPreloadedData()->getBetTypeColumnSets($betType);
	if (empty($columnSets[$betSubtype]))
		return array();
	$columns = array();
	foreach ($columnSets[$betSubtype] as $column) {
		$name = (string)$column['name'];
		//if (isset($outcomes[$name])) // missing will be computed
			$columns[$name] = array('bewaId' => $column['bewaId'], 'order' => $column['order']);
	}
	return $columns;
}

// ****** brParams serialization callbacks: (import, string specialValue, array outcomes) -> string *****

protected function brParamsSerializeRealSpecialValues(&$import, $specialValue, $outcomes) {
	$params = array();
	foreach ($outcomes as $name => $outcome)
		$params[$name] = $outcome['specialValue'];
	return Zend_Json::encode($params);
}

// ****** brParams unserialization callbacks: (string json) -> array *****

protected function brParamsUnserializeRealSpecialValues($brParamsJson) {
	return (empty($brParamsJson) ? array() : Zend_Json::decode($brParamsJson));
}

// ***** match result callback for determining unknown special value: (struct dbBet) -> (struct with fields: specialValue, brParams) *****

/**
 * Parses bet name for last token, brParams are empty
 * @param struct $dbBet
 * @param string $svFormat @see It6_Betradar_Match::parseSpecialValue parameter $format
 * @return struct Fields: specialValue, brParams
 */
protected function determineSpecialValueBetNameLastPart($dbBet, $svFormat = null) {
	$name = $dbBet['betName'];
	if (1 == preg_match('/\\s+(\\S+)\\s*$/', $name, $matches)) {
		return array(
			'specialValue' => $this->parseSpecialValue($matches[1], $svFormat),
			'brParams' => array(),
		);
	}
	else
		return false;
}

/**
 * Parses bet name for two special values as last tokens from team names in bet name, brParams are created as (1 => homeTeamSv, 2 => awayTeamSv).
 * Home team special value is returned as result special value.
 * @param struct $dbBet
 * @param string $svFormat @see It6_Betradar_Match::parseSpecialValue parameter $format
 * @param boolean $homeOnly If TRUE then only home special value will be used and away s.v. will be set to zero.
 * @return struct Fields: specialValue, brParams
 */
protected function determineSpecialValueTeamNamesLastPart($dbBet, $svFormat = null, $homeOnly = false) {
	$name = $dbBet['betName'];
	if (1 == preg_match('/\\s+(\\S+)\\s* - .*\\s+(\\S+)\\s*$/', $name, $matches)) {
		$svHome = $this->parseSpecialValue($matches[1], $svFormat);
		$svAway = ($homeOnly ? 0 : $this->parseSpecialValue($matches[2], $svFormat));
		return array(
			'specialValue' => $svHome,
			'brParams' => array('1' => $svHome, '2' => $svAway),
		);
	}
	else
		return false;
}

// ***** match result callback for effective score: (bewaSportId[, additionParams]) -> (string score, integer homeScore, integer awayScore) *****

/**
 * Basic score fetching callback. 
 */
protected function resultScoreByType($bbasSportId, $type) {
	if (!isset($this->scores[$type]))
		return array(false, false, false);
	$score = $this->scores[$type];
	$scores = explode(':', $score);
	$scoreHome = (empty($scores[0]) ? 0 : intval($scores[0]));
	$scoreAway = (empty($scores[1]) ? 0 : intval($scores[1]));
	return array($score, $scoreHome, $scoreAway);
}

/** 
 * delegates call to other callback associated with current sport in passed callback map<br/>
 * callback map : ( integer|string sport_spec => string callback_name | array(string callback_name [, mixed param1 [, mixed param2 [, ... ]]]) )<br/>
 * sport_spec : integer BBAS sport ID or string list of BBAS sport IDs separated by semicolon or '*' for default (= all others) 
 */
protected function resultScoreCallbackBySport($bbasSportId, $callbackMap) {
	$method = null;
	$default = null;
	foreach ($callbackMap as $sportSpecs => $_method) {
		$sportSpecs = explode(';', trim($sportSpecs));
		foreach ($sportSpecs as $spec) {
			$spec = trim($spec);
			if ($bbasSportId == $spec) {
				$method = $_method;
				break;
			}
			else if ('*' == $spec)
				$default = $_method;
		}
	}
	if (empty($method) && !empty($default))
		$method = $default;
	if (!empty($method)) {
		$params = array();
		if (is_array($method)) {
			$params = array_slice($method, 1);
			$method = $method[0];
		}
		array_unshift($params, $bbasSportId);
		return call_user_func_array(array($this, $method), $params);
	}
	else
		return array(false, false, false);
}

protected function resultScoreFt($bbasSportId) {
	return $this->resultScoreByType($bbasSportId, 'FT');
}

/**
 * for basket it is sum of
 */ 
protected function resultScoreHt($bbasSportId) {
	if (1006 == $bbasSportId)
		return $this->resultScoreSum($bbasSportId, array('1Q', '2Q'));
	else
		return $this->resultScoreByType($bbasSportId, 'HT');
}

/**
 * just final score
 */
protected function resultScoreLast($bbasSportId) {
	$type = 'FT';
	foreach (array('AP', 'OT') as $_type) {
		if (isset($this->scores[$_type])) {
			$type = $_type;
			break;
		}
	}
	return $this->resultScoreByType($bbasSportId, $type);
}

protected function resultScoreBySportLast($bbasSportId, $defaultType = null) {
	$type = (isset($defaultType) ? $defaultType : 'FT');
	if (in_array($bbasSportId, self::$sportsWithResultScoreAfterFullTime)) {
		foreach (array('AP', 'OT') as $_type) {
			if (isset($this->scores[$_type])) {
				$type = $_type;
				break;
			}
		}
	}
	return $this->resultScoreByType($bbasSportId, $type);
}

protected function resultScoreSubstract($bbasSportId, $fromType, $whatType) {
	$from = $this->resultScoreByType($bbasSportId, $fromType);
	if (false !== $from[0]) {
		$what = $this->resultScoreByType($bbasSportId, $whatType);
		if (false !== $what[0]) {
			$home = $from[1] - $what[1];
			$away = $from[2] - $what[2];
			return array("$home:$away", $home, $away);
		}
	}
	return array(false, false, false);
}

/**
 * types is list of result types to sum (eg. '1Q', '2Q')
 */
protected function resultScoreSum($bbasSportId, $types, $ignoreMissing = false) {
	$home = 0;
	$away = 0;
	foreach ($types as $type) {
		$score = $this->resultScoreByType($bbasSportId, $type);
		if (false === $score[0]) {
			if (!$ignoreMissing)
				return $score;
		}
		else {
			$home += $score[1];
			$away += $score[2];
		}
	}
	return array("$home:$away", $home, $away);
}

protected function resultScoreTupplesByType($bbasSportId, $mainType, $types) {
	foreach ($types as $type) {
		if (!isset($this->scores[$type]))
			return array(false, false, false);
	}
	$score = false;
	$scoreHome = array();
	$scoreAway = array();
	foreach ($types as $type) {
		$_score = $this->scores[$type];
		if (false === $score || $mainType == $type)
			$score = $_score;
		$scores = explode(':', $_score);
		$scoreHome[] = (empty($scores[0]) ? 0 : intval($scores[0]));
		$scoreAway[] = (empty($scores[1]) ? 0 : intval($scores[1]));
	}
	return array($score, $scoreHome, $scoreAway);
}

/**
 * Types is list of result types with scores in particular periods to make score of won periods.
 * For example if list ['Set1', 'Set2', 'Set3'] is specified, result will be score of won sets
 * (eg. "2:1" = 2 sets for home team, 1 set for away team).
 */
protected function resultScoreWonPeriods($bbasSportId, $types) {
	$home = 0;
	$away = 0;
	foreach ($types as $type) {
		if (isset($this->scores[$type])) {
			list($_home, $_away) = explode(':', $this->scores[$type]);
			if ($_home > $_away)
				++$home;
			else if ($home < $_away)
				++$away;
		}
	}
	return array("$home:$away", $home, $away);
}

// ***** callbacks for modifying result effective scores: (array(homeScore, awayScore), specialValue, brParams) -> array(homeScore, awayScore) *****

protected function modifyScoresAddSpecialValueScore($scores, $specialValue, $brParams) {
	$svScores = explode(':', $specialValue);
	$svHome = (empty($svScores[0]) ? 0 : floatval($svScores[0]));
	$svAway = (empty($svScores[1]) ? 0 : floatval($svScores[1]));
	return array($scores[0] + $svHome, $scores[1] + $svAway);
}

protected function modifyScoresAddSpecialValueScoreOrDiff($scores, $specialValue, $brParams) {
	if (1 != preg_match('/^\\d+:\\d+$/', $specialValue)) {
		$specialValue = floatval($specialValue);
		if ($specialValue > 0)
			$specialValue = "$specialValue:0";
		else if ($specialValue < 0)
			$specialValue = "0:$specialValue";
		else
			$specialValue = '0:0';
	}
	return $this->modifyScoresAddSpecialValueScore($scores, $specialValue, $brParams);
}

/**
 *  takes special values from brParams, if there are no brParams, simply adds special value to home team
 */
protected function modifyScoresAddHomeAwaySpecialValuesToScores($scores, $specialValue, $brParams) {
	if (empty($brParams)) {
		$svHome = It6_Betradar_Import::parseFloat($specialValue);
		$svAway = 0;
	}
	else {
		$svHome = (empty($brParams['1']) ? 0 : It6_Betradar_Import::parseFloat($brParams['1']));
		$svAway = (empty($brParams['2']) ? 0 : It6_Betradar_Import::parseFloat($brParams['2']));
	}
	return array($scores[0] + $svHome, $scores[1] + $svAway);
}

// ***** callbacks for winning columns determination: (import, bewaBet, score, array(homeScore, awayScore), specialValue, mixed userData) -> array|boolean *****

/**
 * helper for other callbacks
 */
protected function _chooseResult($name, $results, $canBeEmpty) {
	$column = (isset($results[$name]) ? $results[$name] : null);
	if (empty($column))
		return ($canBeEmpty ? array() : false);
	else if (is_array($column))
		return $column;
	else
		return array($column);
}

/**
 *  userData = columns for higher scores array(keys: home,away,equal; values: ID or list of IDs)
 *            If some key has NULL value, it is considered as empty result (legally no winning column).
 */
protected function winningColumnsCompareHomeAway(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$diff = floatval($scores[0]) - floatval($scores[1]);
	if (0 < $diff)
		$column = 'home';
	else if (0 > $diff)
		$column = 'away';
	else
		$column = 'equal';
	return $this->_chooseResult($column, $userData, array_key_exists($column, $userData));
}

/**
 * matches sum of home score and away against exact value
 * userData = (keys: value to match; values: column ID or list of column IDs)
 */
protected function winningColumnsMatchTotal(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$total = intval($scores[0]) + intval($scores[1]);
	return $this->_chooseResult($total, $userData, true);
}

/**
 *  userData = columns for higher scores array(keys: under,over,equal; values: ID or list of IDs)
 */
protected function winningColumnsCompareTotal(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$total = intval($scores[0]) + intval($scores[1]);
	$match = It6_Betradar_Import::parseFloat($specialValue);
	if ($total > $match)
		$column = 'over';
	else if ($total < $match)
		$column = 'under';
	else
		$column = 'equal';
	return $this->_chooseResult($column, $userData, false);
}

/**
 *  userData not used, column are determined from bet subtype
 */
protected function winningColumnsMatchColumnNameAndScore(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$subtype = $import->getBetSubtype($bbasBet['subtypeId']);
	if (empty($subtype))
		return false;
	foreach ($subtype['columns'] as $id => $column) {
		if ($column['name'] == $score)
			return array($id);
	}
	return false;
}

/**
 *  userData = keys: exactScore, values: bewaColumnId
 */
protected function winningColumnsMatchUserColumnNameAndScore(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	return $this->_chooseResult($score, $userData, false);
}

/**
 * userData are result columns - keys example for pairs:<br/>
 *    'home-home', 'home-equal', 'home-away', 'equal-home', 'equal-equal', 'equal-away', 'away-home', 'away-equal', 'away-away'<br/> 
 * scores is array( array(home1, home2, ...), array(away1, away2, ...) )
 */
protected function winningColumnsCompareHomeAwayTupple(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$dim = count($scores[0]);
	$keyParts = array();
	for ($i = 0; $i < $dim; ++$i) {
		$diff = intval($scores[0][$i]) - intval($scores[1][$i]);
		if (0 < $diff)
			$keyPart = 'home';
		else if (0 > $diff)
			$keyPart = 'away';
		else
			$keyPart = 'equal';
		$keyParts[] = $keyPart;
	}
	$key = implode('-', $keyParts);
	return $this->_chooseResult($key, $userData, false);
}


/**
 * userData are result columns - keys: even, odd
 */
protected function winningColumnsTotalEvenOdd(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$total = intval($scores[0]) + intval($scores[1]);
	if ($total % 2)
		$column = 'odd';
	else
		$column = 'even';
	return $this->_chooseResult($column, $userData, false);
}

/**
 * @param $userData are result columns - keys: yes, no
 * @param $teams list indexes into scores (eg, 0=home, 1=away)
 * @param $operator 'all' (default) | 'any'
 * @return 'yes' column(s) if all specified teams scored
 */
protected function _winningColumnsTeamScored($scores, $userData, $teams, $operator = 'all') {
	if (empty($teams))
		return false;
	$allScored = true;
	$anyScored = false;
	foreach ($teams as $team) {
		if (0 >= intval($scores[$team]))
			$allScored = false;
		else
			$anyScored = true;
	}
	switch ($operator) {
	case 'any':
		$result = $anyScored;
		break;
	case 'all':
	default:
		$result = $allScored;
		
	}
	$column = ($result ? 'yes' : 'no');
	return $this->_chooseResult($column, $userData, false);
}

/**
 * userData are result columns - keys: yes, no
 */
protected function winningColumnsTeamScoredBoth(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	return $this->_winningColumnsTeamScored($scores, $userData, array(0, 1), 'all');
} 

/**
 * userData are result columns - keys: yes, no
 */
protected function winningColumnsTeamScoredHome(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	return $this->_winningColumnsTeamScored($scores, $userData, array(0), 'all');
}

/**
 * userData are result columns - keys: yes, no
 */
protected function winningColumnsTeamScoredAway(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	return $this->_winningColumnsTeamScored($scores, $userData, array(1), 'all');
}

/**
* userData are result columns - keys: yes, no
*/
protected function winningColumnsTeamScoredAny(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	return $this->_winningColumnsTeamScored($scores, $userData, array(0, 1), 'any');
}

/**
 * userData are result columns - keys: none, home, away, both
 */
protected function winningColumnsWhichTeamScored(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$homeScore = intval($scores[0]);
	$awayScore = intval($scores[1]);
	if ($homeScore && $awayScore)
		$column = 'both';
	else if ($homeScore)
		$column = 'home';
	else if ($awayScore)
		$column = 'away';
	else
		$column = 'none';
	return $this->_chooseResult($column, $userData, false);
}

/**
 *  userData are result columns - keys are:<br/>
 *    'type' ... score type eg. 'FT'<br/>
 *    'team' ... unset for all teams, index into score otherwise (eg. 0=home, 1=away)<br/>
 *    numeric ... desired score value<br/>
 *    'more' ... more than any other specified score values
 */
protected function winningColumnsTeamScore(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$total = 0;
	if (isset($userData['team']))
		$total = intval($scores[$userData['team']]);
	else {
		foreach ($scores as $score)
			$total += intval($score);
	}
	$column = false;
	$max = 0;
	$more = false;
	foreach ($userData as $_total => $_column) {
		if (ctype_digit("$_total")) {
			if ($total == $_total) {
				$column = $_column;
				break; 
			}
		}
		else if ('more' == $_total)
			$more = $_column;
	}
	if (false === $column) {
		if (false === $more)
			return false;
		if ($total > $max)
			return array($more);
		else
			return array();
	}
	else if (!is_array($column))
		$column = array($column);
	return $column;
}

/**
 * userData are result columns together with void bet odds type - under key 'oddsType' is  odds type for void bet result, other keys are outcomes with columnId(s) as value
 */
protected function winningColumnsVoidBet(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	if (empty($userData['oddsType']))
		return false;
	$oddsType = $userData['oddsType'];
	if (empty($this->betResults[$oddsType][$specialValue]))
		return false;
	$outcome = $this->betResults[$oddsType][$specialValue];
	$columnId = null;
	foreach ($userData as $_outcome => $_columnId) {
		if ($_outcome == $outcome) {
			$columnId = $_columnId;
			break;
		}
	}
	if (empty($columnId))
		return array();
	else
		return (is_array($columnId) ? $columnId : array($columnId));
}

/**
 *  userData is map (realTypeId => callback_struct) where callback_struct is { "callback" => name of method for delegation, "userData" => user data for called callback } 
 */
protected function winningColumnsCallbackByRealBetTypeId(&$import, $bbasBet, $score, $scores, $specialValue, $userData) {
	$realTypeId = $bbasBet['realTypeId'];
	if (!isset($userData[$realTypeId]))
		return false;
	$cb = $userData[$realTypeId];
	$cbMethod = $cb['callback'];
	$cbUserData = $cb['userData'];
	return $this->$cbMethod($import, $bbasBet, $score, $scores, $specialValue, $cbUserData);
}

// OUTRIGHT

/**
* userData not used, scores not used, score is replaced by position
*/
protected function winningColumnsOutrightDefault(&$import, $bbasBet, $position, $scores, $specialValue, $userData) {
	$subtype = $import->getBetSubtype($bbasBet['subtypeId']);
	if (empty($subtype))
		return false;
	$columns = $subtype['columns'];
	if (1 == count($columns)) {
		// assume that single column is just bet on winner
		if (1 == $position) {
			$columnIds = array_keys($columns);
			return array($columnIds[0]);
		}
		else
			return array();
	}
	foreach ($subtype['columns'] as $id => $column) {
		if ($column['name'] == $position)
			return array($id);
	}
	return false;
}

} // class
