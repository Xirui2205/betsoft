<?php

class UiUtil {


	public static function printNotice($m, $tr = FALSE /* , ... */) {
		return UiUtil::printBox($m,'message', $tr);
	}

	public static function printMessages($m, $tr = FALSE /* , ... */) {
		return UiUtil::printBox($m,'message', $tr);
	}

	public static function printErrors($m, $tr = FALSE /* , ... */) {
		return UiUtil::printBox($m,'error', $tr);
	}

	public static function printWarnings($m, $tr = FALSE /* , ... */) {
		return UiUtil::printBox($m,'warning', $tr);
	}

	public static function printBox($msgs,$class, $tr = FALSE /* , ... */) {
		$argv = array_slice(func_get_args(),2);
		$argv = $argv[0];
		$out = '';
		$beg = '<div class="feedback '.$class.'">';
		$end = '</div>';
		if (is_array($msgs)) {
			foreach ($msgs as $m) {
				if ($tr) $m = i18n::tr($m, $argv);
				$out .= $beg.$m.$end;
			}
		} else {
			if ($tr) $msgs = i18n::tr($msgs, $argv);
			$out .= $beg.$msgs.$end;
		}
		return $out;
	}

	public static function printCatalogIcon($type, $openerElm, $openerElm2 = NULL, $optPar = NULL, $eventId = NULL) {
		$s = 'CATALOG_'.$type.'_SECTION';
		$w = 'CATALOG_'.$type.'_WIDTH';
		$h = 'CATALOG_'.$type.'_HEIGHT';
		eval("\$s = \"$s\";");
		eval("\$section = $s;");
		eval("\$w = \"$w\";");
		eval("\$width = $w;");
		eval("\$h = \"$h\";");
		eval("\$height = $h;");

		return '<img src="_clip/translate.gif" alt="Týmy" class="img catalog" onclick="javascript:openCatalog(\''.$section.'\',\''.$openerElm.'\','.$width.','.$height.',\'\',\''.$openerElm2.'\',\''.$optPar.'\',\''.$eventId.'\');void(0);" />';
	}

	public static function wrapFeedback($feedback) {
		return '<button id="feedback-wrapper-toggler" onclick="toggleFeedbackWrapper()">+</button><div id="feedback-wrapper">'.$feedback.'</div>';
	}
}
