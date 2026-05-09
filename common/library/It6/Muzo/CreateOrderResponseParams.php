<?php

class It6_Muzo_CreateOrderResponseParams extends It6_Muzo_ParameterSet {

	public function __construct(array &$data = null) {
		parent::__construct( array(

			// !!! parameter order matters !!!

			'OPERATION' => array(
					'value' => 'CREATE_ORDER',
					'validatorClass'=> 'It6_Muzo_Validator_Enum',
					'validatorParams' => array(It6_Muzo_Operation::CREATE_ORDER)
				),
			'ORDERNUMBER' => array(
					'validatorClass'=> 'It6_Muzo_Validator_Int',
					'validatorParams' => array('maxDigits' => 15)
				),
			'MERORDERNUMBER' => array(
					'optional' => true,
					'validatorClass'=> 'It6_Muzo_Validator_Int',
					'validatorParams' => array('maxDigits' => 16)
				),
			'MD' => array(
					'optional' => true,
					'validatorClass' => 'It6_Muzo_Validator_RegExp',
					'validatorParams' => array('pattern' => '!^[\x20-\x7e]{0,30}$!', 'errorMessage' => 'must be string data with at most 30 chars in 0x20-0x7e ASCII range')
				),
			'PRCODE' => array(
					'validatorClass'=> 'It6_Muzo_Validator_Int'
				),
			'SRCODE' => array(
					'validatorClass'=> 'It6_Muzo_Validator_Int'
				),
			'RESULTTEXT' => array(
					'optional' => true
				),
			'DIGEST' => array(
					'validatorClass' => 'It6_Muzo_Validator_NotEmpty',
					'notInDigest' => true
				),
			)
		);

		if (!empty($data))
			$this->readParams($data, false);
	}

}
