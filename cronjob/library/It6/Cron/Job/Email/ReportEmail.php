<?php
abstract class It6_Cron_Job_Email_ReportEmail extends It6_Cron_Job_Email_ControllerAbstract {

	const PARAM_TO = "to";
	const PARAM_TO_BCC = "to_bcc";
	const PARAM_CSV_PATH = "csv_path";
	const PARAM_FILENAME = "filename";
	const PARAM_SUBJECT = "subject";
	const PARAM_DATA_ALL = "data_all";
	const PARAM_DATA_INTERNET = "data_internet";
	const PARAM_DATA_BRANCH = "data_branch";
	const PARAM_DATA_LIVE = "data_live";
	const PARAM_PDF_PATH = "pdf_path";
	const PARAM_DATE = "date";

	public $subject;

	public function __construct(array $params) {
		$this->params = $params;
	}

	public function prepareEmail() {
		parent::prepareEmail();
		$this->subject = $this->params[self::PARAM_SUBJECT];
	}

	public function getToField() {
		return $this->params[self::PARAM_TO];
	}

	public function getBccField() {
		if (!empty($this->params[self::PARAM_TO_BCC])) {
			return $this->params[self::PARAM_TO_BCC];
		} else {
			return false;
		}
	}

	public function getAttachment() {
		if (!empty($this->params[self::PARAM_CSV_PATH])) {
			$file = file_get_contents($this->params[self::PARAM_CSV_PATH]);

			return array(
				$file,
				'text/csv',
				Zend_Mime::DISPOSITION_ATTACHMENT,
				Zend_Mime::ENCODING_BASE64,
				$this->params[self::PARAM_FILENAME],
			);
		} else if (!empty($this->params[self::PARAM_PDF_PATH])) {
			$file = file_get_contents($this->params[self::PARAM_PDF_PATH]);
			unlink($this->params[self::PARAM_PDF_PATH]);
			unlink(str_replace('.pdf', '.odt', $this->params[self::PARAM_PDF_PATH]));

			return array(
				$file,
				'application/pdf',
				Zend_Mime::DISPOSITION_ATTACHMENT,
				Zend_Mime::ENCODING_BASE64,
				$this->params[self::PARAM_FILENAME],
			);
		} else {
			return false;
		}
	}
}