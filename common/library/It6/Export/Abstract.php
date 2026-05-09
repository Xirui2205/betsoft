<?php 
class It6_Export_Abstract{

	public static $OUTPUT_FORMAT_ODT = "odt";
	public static $OUTPUT_FORMAT_ODS = "ods";
	public static $OUTPUT_FORMAT_PDF = "pdf";
	public static $OUTPUT_FORMAT_XLS = "xls";
	public static $OUTPUT_FORMAT_CSV = "csv";

	public static $ODT_MIMETYPE = "application/vnd.oasis.opendocument.text";
	public static $ODS_MIMETYPE = "application/vnd.oasis.opendocument.spreadsheet";
	public static $PDF_MIMETYPE = "application/pdf";
	public static $XLS_MIMETYPE = "application/vnd.ms-excel";
	public static $CSV_MIMETYPE = "text/csv";

	protected $tmpFile = "";
	protected $outputFormat = "";
	protected $outputFile = "";
	protected $outputMimetype = "";

	public function output($filename){
		header('Cache-control: private');
	    header('Content-Type: '.$this->outputMimetype);
	    header('Content-Disposition:attachment; filename="'.$filename.'"');
	    header("Content-Length: ".filesize($this->outputFile));
	    header('Pragma: public');
	    readfile($this->outputFile);
	    $this->cleanUp();
	    exit();
	}

	public function cleanUp() {
		unlink($this->tmpFile);
		
		if ($this->tmpFile != $this->outputFile)
			unlink($this->outputFile);
	}

	public static function getMimetypeByOutputFormat($outputFormat) {
		switch ($outputFormat){
			case self::$OUTPUT_FORMAT_ODT: return self::$ODT_MIMETYPE;

			case self::$OUTPUT_FORMAT_ODS: return self::$ODS_MIMETYPE;

			case self::$OUTPUT_FORMAT_XLS: return self::$XLS_MIMETYPE;

			case self::$OUTPUT_FORMAT_PDF: return self::$PDF_MIMETYPE;

			case self::$OUTPUT_FORMAT_CSV: return self::$CSV_MIMETYPE;
		}
	}

	public static function joinPDFs($filePaths, $filename = null, $output = false) {
		$tmpFile = TEMPLATES_DIRECTORY . "office/workdir/" . ((!is_null($filename)) ? $filename : uniqid() . ".pdf");
		exec("pdftk " . implode(" ", $filePaths) . " cat output " . $tmpFile);

		if ($output) {
			header('Cache-control: private');
		    header('Content-Type: ' . self::$PDF_MIMETYPE);
		    header('Content-Disposition:attachment; filename="' . basename($tmpFile) . '"');
		    header("Content-Length: ".filesize($tmpFile));
		    header('Pragma: public');
		    readfile($tmpFile);
			unlink($tmpFile);
			exit();
		}
		else return $tmpFile;
	}

}