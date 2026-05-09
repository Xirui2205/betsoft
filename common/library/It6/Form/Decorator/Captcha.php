<?php

class It6_Form_Decorator_Captcha extends Zend_Form_Decorator_Captcha {

	 public function render($content) {
		$element = $this->getElement();
		if (!method_exists($element, 'getCaptcha')) {
			return $content;
		}

		$view = $element->getView();
		if (null === $view) {
			return $content;
		}


		$name = $element->getFullyQualifiedName();

		$hiddenName = $name . '[id]';
		$textName = $name . '[input]';

		$label = $element->getDecorator("Label");
		if ($label) {
			$label->setOption("id", $element->getId() . "-input");
		}

		$placement = $this->getPlacement();
		$separator = $this->getSeparator();

		$captcha = $element->getCaptcha();
		$markup = $captcha->render($view, $element);
		$hidden = $view->formHidden($hiddenName, $element->getValue(), $element->getAttribs());
		$text = $view->formText($textName, '', array_merge($element->getAttribs(), array('size' => $captcha->getWordlen())));


		$jsCode = '
			<a href="javascript:void(0);" id="reloadCaptcha">
				'.$view->translate("reload_captcha").'
			</a>
			<script type="text/javascript">
				$("#captcha").html("<img src=\"/images/loader-small.gif\" alt=\"loader\" />");
				function reloadCaptcha() {
					$.get(
						"'.$view->UrlSet(40).'",
						{ name: "'.$name.'" },
						function(data){ $("#captcha").html(data); },
						"html"
					);
				}
				$("#reloadCaptcha").click( function(e){ e.preventDefault(); reloadCaptcha(); } );
				$(document).ready(reloadCaptcha());
			</script>
		';

		switch ($placement) {
			case 'PREPEND':
				$content = '<div><div id="captcha">'.$markup.$text.'</div>'.$hidden.$jsCode.'</div>'.$separator.$content;
				break;
			case 'APPEND':
			default:
				$content = $content.$separator.'<div id="captcha">'.$markup.$text.'</div>'.$hidden.$jsCode;
		}

		return $content;
	}

}
