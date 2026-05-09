<?php

class Models_Form_BalancingFilter extends It6_Models_DecoratedForm_Table {
	
	public function __construct($section, $noHost = false) {

		$ws = Zend_Registry::get('ws');

		parent::__construct();

		$formName = 'BalancingFilterForm';

		$this->setName($formName)
			->setMethod('get')
			->setAttrib("cssClass", "table-filter");

		$this->addElement(
			$this->createElement('hidden', 'section')
					->setValue($section));

		if ( !$noHost ) {
			$hosts = array();
			$hosts[0] = 'nevybráno';
			foreach ( $ws->Host->getAllOrder(array('name')) as $host ) {
				//if ( $host->hostId == It6_Models_Host::ID_INTERNET ) continue;
				$hosts[$host->hostId] = $host->name;
			}

			$hostId = $this->createElement('select', 'hosts')
				->setMultiOptions($hosts)
				//->setAttrib('onChange', 'showRelevant(this.name);')
				->setLabel(i18n::tr('host'));

			$this->addElement($hostId);

			$branchId = $this
				->createElement('text', 'branchId')
				->setLabel(i18n::tr('branch_id')
			);
			$this->addElement($branchId);

			$handle = $this
				->createElement('text', 'branchHandle')
				->setLabel(i18n::tr('branch id')
			);
			$this->addElement($handle);
		}

		$dateStart = $this
			->createElement('text', 'dateStart')
			->setLabel(i18n::tr('dateStart'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateStart);

		$dateTo = $this
			->createElement('text', 'dateTo')
			->setLabel(i18n::tr('dateTo'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateTo);

		if ( $noHost ) {
			$distinguishBranchesAndInternet = $this
				->createElement('checkbox', 'distinguishBranchesAndInternet')
				->setLabel(i18n::tr('distinguishBranchesAndInternet'));
			$this->addElement($distinguishBranchesAndInternet);
		}
		$this->addElement('submit', 'filter', array('label' => i18n::tr('Filter')));
	}
}