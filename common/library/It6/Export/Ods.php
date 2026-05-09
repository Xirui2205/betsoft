<?php
class It6_Export_Ods extends It6_Export_Abstract{

	public static $BRANCH_CALCULATION_ODS = "branch_calculation.ods";
	public static $BRANCH_NET_CALCULATION = "branch_net_calculation.ods";

	public function generate($values, $template, $outputFormat = null) {

		if (is_null($outputFormat))
			$this->outputFormat = self::$OUTPUT_FORMAT_ODS;
		else 
			$this->outputFormat = $outputFormat;

		$this->outputMimetype = self::getMimetypeByOutputFormat($this->outputFormat);
		
		$this->tmpFile = TEMPLATES_DIRECTORY . uniqid("workdir/") . ".ods";
		
		copy(TEMPLATES_DIRECTORY . $template, $this->tmpFile);
		
		$zip = new ZipArchive();
		if ($zip->open($this->tmpFile)) {

			$contentXmlFilename = $zip->getFromName('content.xml');

			$dom = new DOMDocument();
			$dom->loadXML($contentXmlFilename);
			$rows = $dom->getElementsByTagNameNS($dom->documentElement->lookupnamespaceURI('table'), 'table-row');

			$items = array();
			$variablesFound = false;

			foreach($rows as $row) {
				$cells = $row->getElementsByTagNameNS($dom->documentElement->lookupnamespaceURI('table'), 'table-cell');
				foreach ($cells as $cell) 
					{
						if (substr($cell->nodeValue, 0, 2) == "##") 
							{
								$items[] = str_replace("#", "", $cell->nodeValue);
								$variablesFound = true;
							}
					}

				if ($variablesFound){
					$rowText = $dom->saveXML($row->cloneNode(true));

					foreach ($values as $value) {
						$newDom = new DOMDocument();
						$newDomRowText = $rowText;

						$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
						$logger->info($newDomRowText);

						$newDomRowTextNode = "<table:table-row xmlns:table=\"urn:oasis:names:tc:opendocument:xmlns:table:1.0\" xmlns:office=\"urn:oasis:names:tc:opendocument:xmlns:office:1.0\" xmlns:text=\"urn:oasis:names:tc:opendocument:xmlns:text:1.0\" table:style-name=\"ro1\">";

						//novy element radku tabulky
						$cells = $row->getElementsByTagNameNS($dom->documentElement->lookupnamespaceURI('table'), 'table-cell');
						foreach ($cells as $cell) {
							//zjistim nazev promene
							$key  = str_replace("#", "", $cell->nodeValue);
							//at se tam nezobrazuji ty apostrofy a  chova se to hned jak cisla :-)
							if (is_numeric($value[strtolower($key)])) {
								$cell->setAttribute("office:value-type", "float");
								$cell->setAttribute("office:value", $value[strtolower($key)]);

							}	
							$cellText = $dom->saveXML($cell->cloneNode(true));

							$newDomRowTextNode .= str_replace("##" . $key . "##", $value[strtolower($key)], $cellText);
						}


						$newDomRowTextNode .= "</table:table-row>";
						$logger->info($newDomRowTextNode);
						$newDom->loadXML($newDomRowTextNode);
						// vlozeni uzlu do dokumentu
						$newDom = $dom->importNode($newDom->documentElement, true);
						$row->parentNode->insertBefore($newDom, $row);
					}
					$row->parentNode->removeChild($row);

					break;
					}
					
					
			}

			if (!$variablesFound)
				throw new Exception("No row with variables found in template !" . $template);	

			$zip->addFromString('content.xml', $dom->saveXML());
			$zip->close();
	 
	 		if ($outputFormat != self::$OUTPUT_FORMAT_ODS) {
	 			exec("unoconv -f " . $this->outputFormat . " " . $this->tmpFile);

	 			$this->outputFile = str_replace("." . self::$OUTPUT_FORMAT_ODS, "." . $this->outputFormat, $this->tmpFile);
	 		} 

	 		else $this->outputFile = $this->tmpFile;

	 		return $this->outputFile;
		}
		else throw Exception('Cant open template ' . $template);
	}
}