<?php

class Models_Form_ParametersSettings extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $paramId = null, $mode = null) {
		require_once('controllers/ParametersSettingsController.php');
		parent::__construct();

        //$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
		//$logger->info('form');

		$this->setName('ParametersMainForm');
		$role = $this->ws->Params->getAllRole($paramId);	
		
		$question	= array(
			0		=> i18n::tr('Ne'),
			1		=> i18n::tr('Ano')
		);
		
		$data_type	= It6_ArrayWrapper::toNativeArray($this->ws->Params->getAllDataType());
		$data = array();
		foreach ($data_type as $dt) {
			$data[$dt['id']] = $dt['nazev'];
		}

		$nazev = $this
			->createElement('text', 'nazev')
			->setLabel(i18n::tr('nazev'))
			->setRequired(true);
		$this->addElement($nazev);

		$mandatory = $this
			->createElement('select', 'mandatory')
			->setLabel(i18n::tr('mandatory'))
			->addMultiOptions($question);
		$this->addElement($mandatory);
		
		$nasobnost = $this
			->createElement('select', 'nasobnost')
			->setLabel(i18n::tr('nasobnost'))
			->addMultiOptions($question);
		$this->addElement($nasobnost);

		$datovy_typ = $this
			->createElement('select', 'id_datovy_typ')
			->setLabel(i18n::tr('datovy_typ'))
			->addMultiOptions($data);
		$this->addElement($datovy_typ);

		// opravneni
		foreach ($role as $r) {
			// HIDDEN
			$varName = 'chkb_'.$r['acl_role_id'].'_h';
			$$varName = $this
				->createElement('checkbox', $varName)
				->setLabel($r['acl_role_name'].' (hidden)');
			$this->addElement($$varName);
			if ($r['h'] == 1) {
			  $$varName->setValue(1);
			}
            // READ
			$varName = 'chkb_'.$r['acl_role_id'].'_r';
			$$varName = $this
				->createElement('checkbox', $varName)
				->setLabel($r['acl_role_name'].' (read)');
			$this->addElement($$varName);
			if ($r['r'] == 1) {
			  $$varName->setValue(1);
			}
            // READ & WRITE
			$varName = 'chkb_'.$r['acl_role_id'].'_rw';
			$$varName = $this
				->createElement('checkbox', $varName)
				->setLabel($r['acl_role_name'].' (rw)');
			$this->addElement($$varName);
			if ($r['rw'] == 1) {
			  $$varName->setValue(1);
			}
		}

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '354&', '".$this->getName()."', 'parameters-settings', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);
	}
}