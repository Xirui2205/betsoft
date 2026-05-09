<?php

class It6_Muzo_PrCode {
	const OK = 0;
	const FIELD_TOO_LONG = 1;
	const FIELD_TOO_SHORT = 2;
	const INCORRECT_FIELD_CONTENT = 3;
	const EMPTY_FIELD = 4;
	const MISSING_FIELD = 5;
	const UNKNOWN_MERCHANT = 11;
	const DUPLICATE_ORDER_NUNBER = 14;
	const OBJECT_NOT_FOUND = 15;
	const DEPOSIT_AMOUNT_EXCEEDS_APPROVED = 17;
	const SUM_OF_CREDITED_EXCEEDS_DEPOSITED = 18;
	const INVALID_OBJECT_STATE_FOR_OPERATION = 20;
	const CONNECTION_TO_AC_PROBLEM = 26;
	const INCORRECT_ORDER_TYPE = 27;
	const DECLINED_IN_3D = 28;
	const DECLINED_IN_AC = 30;
	const WRONG_DIGEST = 31;
	const TECHNICAL_PROBLEM = 1000;

	public static $messages = array(
		'cz' => array(
				self::OK => 'OK',
				self::FIELD_TOO_LONG => 'Pole příliš dlouhé',
				self::FIELD_TOO_SHORT => 'Pole příliš krátké',
				self::INCORRECT_FIELD_CONTENT => 'Chybný obsah pole',
				self::EMPTY_FIELD => 'Pole je prázdné',
				self::MISSING_FIELD => 'Chybí povinné pole',
				self::UNKNOWN_MERCHANT => 'Neznámý obchodník',
				self::DUPLICATE_ORDER_NUNBER => 'Duplikátní číslo objednávky',
				self::OBJECT_NOT_FOUND => 'Objekt nenalezen',
				self::DEPOSIT_AMOUNT_EXCEEDS_APPROVED => 'Částka k úhradě překročila autorizovanou částku',
				self::SUM_OF_CREDITED_EXCEEDS_DEPOSITED => 'Součet kreditovaných částek překročil uhrazenou částku',
				self::INVALID_OBJECT_STATE_FOR_OPERATION => 'Objekt není ve stavu odpovídajícím této operaci',
				self::CONNECTION_TO_AC_PROBLEM => 'Technické problém při spojení s autorizačním centrem',
				self::INCORRECT_ORDER_TYPE => 'Chybný typ objednávky',
				self::DECLINED_IN_3D => 'Zamítnuto v 3D',
				self::WRONG_DIGEST => 'Zamítnuto v autorizačním centru',
				self::WRONG_DIGEST => 'Chybný podpis',
				self::TECHNICAL_PROBLEM => 'Technický problém'
			),
		'en' => array(
				self::OK => 'OK',
				self::FIELD_TOO_LONG => 'Field too long',
				self::FIELD_TOO_SHORT => 'Field too short',
				self::INCORRECT_FIELD_CONTENT => 'Incorrect content of field',
				self::EMPTY_FIELD => 'Field is null',
				self::MISSING_FIELD => 'Missing required field',
				self::UNKNOWN_MERCHANT => 'Unknown merchant',
				self::DUPLICATE_ORDER_NUNBER => 'Duplicate order number',
				self::OBJECT_NOT_FOUND => 'Object not found',
				self::DEPOSIT_AMOUNT_EXCEEDS_APPROVED => 'Amount to deposit exceeds approved amount',
				self::SUM_OF_CREDITED_EXCEEDS_DEPOSITED => 'Total sum of credited amounts exceeded deposited amount',
				self::INVALID_OBJECT_STATE_FOR_OPERATION => 'Object not in valid state for operation',
				self::CONNECTION_TO_AC_PROBLEM => 'Technical problem in connection to authorization center',
				self::INCORRECT_ORDER_TYPE => 'Incorrect order type',
				self::DECLINED_IN_3D => 'Declined in 3D',
				self::WRONG_DIGEST => 'Declined in AC',
				self::WRONG_DIGEST => 'Wrong digest',
				self::TECHNICAL_PROBLEM => 'Technical problem'
			)
	);
}
