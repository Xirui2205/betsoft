<?php

class Models_Form_BranchBank extends It6_Models_DecoratedForm_Table{

	public function __construct($section, $accountType, $showSameAsProvision=false){
		parent::__construct();

		$conv			= $this->ws->BankAccount->getConvTable();
		$intValidator	= new Zend_Validate_Int();

		$this->setName('BranchBankForm_'.$accountType['name']);

		if($showSameAsProvision == true) {
			$sameAsProvision = $this
				->createElement('button', 'sameAsProvision')
				->setAttrib('onClick', 'sameAsBranchBankBalanceForm()')
				->setlabel(i18n::tr('same_as_provision'));
			$this->addElement($sameAsProvision);
		}

		$type = $this
			->createElement('hidden', 'typeId')
			->setValue($accountType['id']);
		$this->addElement($type);


		$branchId = $this->createElement('hidden', 'branchId');
		$this->addElement($branchId);


		$bankName = $this
			->createElement('text', 'bankName')
			->setLabel(i18n::tr('Bank name'));
		$this->addElement($bankName);


		$bankBranch = $this
			->createElement('text', 'bankBranch')
			->setlabel(i18n::tr('Branch'));
		$this->addElement($bankBranch);

		$accountPrefix = $this
			->createElement('text', 'accountPrefix')
			->setLabel(i18n::tr('Account Prefix'))
			->addValidator($intValidator);
		$this->addElement($accountPrefix);

		$accountNumber = $this
			->createElement('text', 'accountNumber')
			->setLabel(i18n::tr('Account Number'))
			->addValidator(new Zend_Validate_Regex('/^\\d+$/'))
			->setRequired(true);
		$this->addElement($accountNumber);

		$bankCode = $this
			->createElement('text', 'bankCode')
			->setLabel(i18n::tr('Bank Code'))
			->addValidator($intValidator)
			->setRequired(true);
		$this->addElement($bankCode);

		$currency = $this
			->createElement('select', 'currency')
			->setLabel(i18n::tr('Currency'))
			->addMultiOptions(Models_Branch::getCurrency())
			->setRequired(true);
		$this->addElement($currency);

		$note = $this
			->createElement('text', 'note')
			->setLabel(i18n::tr('Note'));
		$this->addElement($note);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitGeneric($section, 'BranchBankForm_".$accountType['name']."', 'branch-bank', '&save=".$accountType['name']."')");
		$this->addElement($submit);
	}
}
