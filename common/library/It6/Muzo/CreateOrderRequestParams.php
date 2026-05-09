<?php

class It6_Muzo_CreateOrderRequestParams extends It6_Muzo_ParameterSet {

	public function __construct() {
		parent::__construct( array(

			// !!! parameter order matters !!!

			'MERCHANTNUMBER' => array(
					'validatorClass' => 'It6_Muzo_Validator_Int'
				),
			'OPERATION' => array(
					'value' => 'CREATE_ORDER',
					'validatorClass'=> 'It6_Muzo_Validator_Enum',
					'validatorParams' => array(It6_Muzo_Operation::CREATE_ORDER)
				),
			'ORDERNUMBER' => array(
					'validatorClass'=> 'It6_Muzo_Validator_Int',
					'validatorParams' => array('maxDigits' => 15)
				),
			'AMOUNT' => array(
					'validatorClass'=> 'It6_Muzo_Validator_Int',
					'validatorParams' => array('maxDigits' => 12)
				),
			'CURRENCY' => array(
					'optional' => true,
					'validatorClass' => 'It6_Muzo_Validator_Enum',
					'validatorParams' => array(It6_Muzo_CurrencyCode::CZK, It6_Muzo_CurrencyCode::EUR)
				),
			'DEPOSITFLAG' => array(
					'validatorClass' => 'It6_Muzo_Validator_Bit',
				),
			'MERORDERNUMBER' => array(
					'optional' => true,
					'validatorClass'=> 'It6_Muzo_Validator_Int',
					'validatorParams' => array('maxDigits' => 16)
				),
			'URL' => array(
					'validatorClass' => 'It6_Muzo_Validator_RegExp',
					'validatorParams' => array('pattern' => '!^(?:http|https)://.+$!i', 'errorMessage' => 'must be valid URL starting with http:// or https:// protocol prefix')
				),
			'DESCRIPTION' => array(
					'optional' => true,
					'validatorClass' => 'It6_Muzo_Validator_RegExp',
					'validatorParams' => array('pattern' => '!^[\x20-\x7e]{0,125}$!', 'errorMessage' => 'must be string data with at most 125 chars in 0x20-0x7e ASCII range')
				),
			'MD' => array(
					'optional' => true,
					'validatorClass' => 'It6_Muzo_Validator_RegExp',
					'validatorParams' => array('pattern' => '!^[\x20-\x7e]{0,30}$!', 'errorMessage' => 'must be string data with at most 30 chars in 0x20-0x7e ASCII range')
				),
			'DIGEST' => array(
					'validatorClass' => 'It6_Muzo_Validator_NotEmpty',
					'notInDigest' => true
				),
			)
		);
	}

}
