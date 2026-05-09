<?php

class It6_FeedbackMsg {

	public static function printNotice($m, $translate=true) {
		return self::printBox($m,'alert-success', $translate);
	}

	public static function printError($m, $translate=true, $from=null) {
		return self::printBox($m,'alert-danger', $translate, $from);
	}

	public static function printWarning($m, $translate=true) {
		return self::printBox($m,'alert-warning', $translate);
	}

	public static function printBox($msgs, $class, $translate, $from=null) {
		if ($translate === true) $msgs = Zend_Registry::get('translate')->trans($msgs);

		$out = '';
		$del_begin = '<p>';
		$del_end = '</p>';
		$end = '</div>';

		if ($from == 'reg') {

			$div_one = '<div class="error-box alert">';
			$div_two = '<div class="msg">';
			$link = '<a class="toggle-alert" href="#">Toggle</a>';
			$out .= $div_one . $div_two . $msgs . $end . $del_begin . $link . $del_end . $end;

		} else {

			//$beg = '<div class="notify-larger '.$class.' bottom10">';
			
			
			$h2 = array(
				'alert-success' => Zend_Registry::get('translate')->trans('alert-success-msg'),
				'alert-danger' => Zend_Registry::get('translate')->trans('reg_general_error_funny_title'),
				'alert-warning' => Zend_Registry::get('translate')->trans('alert-warning-msg')
			);
			
			$beg = '<div role="alert" class="alert ' . $class . ' alert-dismissible">
							<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<h2>' . $h2[$class] . '</h2>';
	
			if (is_array($msgs)) {
				foreach ($msgs as $m) {
					$out .= $beg . $del_begin . $m . $del_end .$end;
				}
			} else {
				$out .= $beg . $del_begin . $msgs . $del_end . $end;
			}

		}

		return $out;
	}
}