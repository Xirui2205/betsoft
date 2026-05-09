<?php

abstract class Models_Form_SendHostMessage {

	protected static $FORM_NAME = 'HostMessage';
	protected static $FORM_METHOD = 'post';
	protected static $FORM_CSS_CLASS = 'table-filter';
	protected static $ADMIN_SECTION_ID = '268';
	protected static $FORM_EXPORT_NAME = 'HostExport';
	protected static $FORM_EXPORT_CSS_CLASS = 'actions';

	public static function render($request, $view) {
		$form = static::getForm();

		$ws = Zend_Registry::get('ws');

		if ( null != $request->getParam('send')
				&& $form->isValid( $request->getParams() ) ) {

				try {

					$values = $form->getValues();

					foreach ( $values['hosts'] as $host ) {
						$ws->HostMessage->insert(array(
								'hostId'  => $host,
								'time'    => $values['validFromTime'],
								'time_to' => $values['validToTime'],
								'message' => $values['message'],
								'restart' => $values['restart'],
								'version' => $values['version']
							));
					}

					$view->message = UiUtil::printMessages('Message to host {0} sent successfully',$host);
					It6_Log::info(
						"Message to host %hostId% sent successfully",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $host, 'message' => $values['message'])
					);
					//$form = static::resetForm($form);
					$form = static::getForm();
				} catch (Exception $e) {
					$view->message = UiUtil::printErrors('Error sending message to host {0}',$host);
					It6_Log::warn(
						"Error sending message to host %hostId%",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $host, 'message' => $values['message'])
					);
					It6_Log:err($e);
				}
		}

		$view->form = $form;

	    $view->messages = $ws->HostMessage->getAll();

		if ( null != $request->getParam('export') ) {
			ob_end_clean();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"messages.csv\"");

			$messages = array();
			$i = 0;
			foreach ( $view->messages as $message ) {
					$messages[$i] = $message;
					$i++;
			}

			echo It6_Models_ExportHelper::assocArrayToCsv($messages);
			exit;
		}
	}

	protected static function resetForm($form) {
		$form->reset();
		return $form;
	}


	protected static function getForm() {
		$ws = Zend_Registry::get('ws');

		$form = new It6_Models_DecoratedForm_Table();
		$form->setName(static::$FORM_NAME)
			->setMethod(static::$FORM_METHOD)
			->setAttrib("cssClass", static::$FORM_CSS_CLASS);
		$form->addElement(
			$form->createElement('hidden', 'section')
					->setValue(static::$ADMIN_SECTION_ID));

		$hosts = array();
		foreach ( $ws->Host->getAll() as $host ) {
			$hosts[$host->hostId] = $host->name;
		}

		$hostId = $form->createElement('multiselect', 'hosts')
						->setMultiOptions($hosts)
						->setLabel(i18n::tr('host_name'))
						->setRequired(true);

		$form->addElement($hostId);

		$message = $form->createElement('textarea', 'message')
						->setLabel(i18n::tr('message'))
						->setRequired(true);

		$form->addElement($message);

		$validFrom = $form
			->createElement('text', 'validFromTime')
			->setLabel(i18n::tr('valid_from'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($form->getDateTimeDecorator())
			->setRequired(true);
		$form->addElement($validFrom);
		
		$validTo = $form
			->createElement('text', 'validToTime')
			->setLabel(i18n::tr('valid_to'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($form->getDateTimeDecorator())
			->setRequired(true);
		$form->addElement($validTo);

		$restart = $form
			->createElement('checkbox', 'restart')
			->setLabel(i18n::tr('restart'));
		$form->addElement($restart);

		$version = $form
			->createElement('text', 'version')
			->setLabel(i18n::tr('version'));
		$form->addElement($version);

		$form->addElement(
				'submit', 'send', array('label' => i18n::tr('send')));

		return $form;
	}

}