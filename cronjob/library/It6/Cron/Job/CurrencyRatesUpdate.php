<?php

class It6_Cron_Job_CurrencyRatesUpdate extends It6_Cron_Job_Abstract {

	private static function file_get_contents_curl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	public function execute(array $params = array(), &$errorMessage = null) {
		$db = Zend_Registry::get('db');
		try {
			$currencyXmlString = self::file_get_contents_curl
							('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
			$currencyXml = simplexml_load_string($currencyXmlString);
			$db->insert('mena_kurz', array('mena_id' => 2, 'kurz' => 1));
			foreach ($currencyXml->xpath('//*[@currency]') as $node) {
				$attributes = $node->attributes();
				$menaId = $db->select()
						->from('mena', array('mena_id'))
						->where('mena_text = ?', $attributes->currency)
						->query()
						->fetch();				
				$db->insert('mena_kurz', array(
					'mena_id' => $menaId['mena_id'],
					'kurz' => $attributes->rate));
			}
		} catch (Exception $e) {
			It6_Log::err('Currency rates not updated. Exception:'
					. $e->getMessage());
		}
	}

}
