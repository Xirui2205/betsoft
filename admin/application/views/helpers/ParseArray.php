<?php


class Zend_View_Helper_ParseArray extends Zend_View_Helper_Abstract {
	 
	public $view;

	public function parseArray($v, $iter = 0,$res = ''){
		if ($v != NULL) {
			if(is_array($v) || is_object($v)) {
				$res .= '<table class="table-detail level-'.$iter++.'">';
				foreach ($v as $key => $value) {
					$res .= '<tr>';
					$res .= '<th>'.i18n::tr($key).'</th>';
					$res .= '<td>'.$this->view->parseJson($value, $iter).'</td>';
					$res .= '</tr>';
				}
				$res .= '</table>';
			} else {

				try {
					if (strpos($v,'It6_ArrayWrapper')) {
						$arr = unserialize($v);
						$res = $this->view->parseArray($arr->collection, $iter);
					} else $res = $v;

					

				}
				catch (Exception $e){
					$res =  $v;
				}
				
			}
			return $res;
		} else return 'N/A ('. $v.')';

	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}
