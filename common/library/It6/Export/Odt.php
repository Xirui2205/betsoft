<?php
class It6_Export_Odt extends It6_Export_Abstract{

	public static $INTERNET_CALCULATION_ODT = "internet_calculation.odt";
	public static $BRANCH_NET_CALCULATION = "branch_net_calculation.odt";
	public static $DAILY_TOTAL_ODT = "total_report.odt";

	public function generate($values, $template, $outputFormat = null) {

		if (is_null($outputFormat))
			$this->outputFormat = self::$OUTPUT_FORMAT_ODT;
		else
			$this->outputFormat = $outputFormat;

		$this->outputMimetype = self::getMimetypeByOutputFormat($this->outputFormat);
		$this->tmpFile = TEMPLATES_DIRECTORY . uniqid("office/workdir/") . ".odt";
		copy(TEMPLATES_DIRECTORY . "office/" . $template, $this->tmpFile);

		$zip = new ZipArchive();
		if ($zip->open($this->tmpFile)) {

			$contentXmlFilename = $zip->getFromName('content.xml');


			foreach ($values as $key => $value) {
				$contentXmlFilename = str_replace("##" . strtoupper($key) . "##", $values[$key], $contentXmlFilename);
			}
			$zip->addFromString('content.xml', $contentXmlFilename);
			$zip->close();

			if ($outputFormat != self::$OUTPUT_FORMAT_ODT) {
				exec("HOME=/tmp unoconv -f " . $this->outputFormat . " " . $this->tmpFile);
				$this->outputFile = str_replace("." . self::$OUTPUT_FORMAT_ODT, "." . $this->outputFormat, $this->tmpFile);
			} else {
				$this->outputFile = $this->tmpFile;
			}
			return $this->outputFile;

		} else {
			throw Exception('Cant open template ' . $template);
		}
	}
}