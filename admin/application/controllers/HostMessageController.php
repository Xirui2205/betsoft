<?php

class HostMessageController extends It6_Controller_Abstract {

public function sendAction() {
	$request = $this->getRequest();
	
	Models_Form_SendHostMessage::render($request, $this->view);
}

}
