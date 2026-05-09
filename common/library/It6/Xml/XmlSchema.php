<?php

class It6_Xml_XmlSchema {

const SCHEMA_BBAS_TRANS = 'bewatrans.xsd';
const SCHEMA_BBAS_STATIC_PAGES = 'bewastaticpages.xsd';

/**
 * Validate XML document against given schema
 * @param string $document Filename or XML source
 * @param string $schema Name of XML schema (use SCHEMA_* constants)
 * @param DOMDocument [optional] Output variable for DOMDocument loaded
 * @param boolean $documentIsFileName [optional] TRUE if $document is file name; default TRUE
 */
private static function validate($document, $schema, &$dom = null, $documentIsFileName = true) {
	try {
		$dom = new DOMDocument();
		if ($documentIsFileName)
			$dom->load($document);
		else
			$dom->loadXML($document);
		$xsd = dirname(__FILE__) . "/$schema";
		return $dom->schemaValidate($xsd);
	}
	catch (Exception $e) {
		return false;
	}
}

public static function isValidBbasTransDocument($document, &$dom = null, $documentIsFileName = true) {
	return self::validate($document, self::SCHEMA_BBAS_TRANS, $dom, $documentIsFileName);
}

public static function isValidBbasStaticPagesDocument($document, &$dom = null, $documentIsFileName = true) {
	return self::validate($document, self::SCHEMA_BBAS_STATIC_PAGES, $dom, $documentIsFileName);
}

} // class
