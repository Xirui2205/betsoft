<?php

class HostDashboardController extends It6_Controller_Abstract {

	const HOST_DASHBOARD = 257;
	protected $viewSectionId = 257;

	public function  viewAction() {
		$ws = Zend_Registry::get('ws');


		if ( !empty($_POST) ) {

			if ( !empty($_POST['banAll'])  ) {
				$ws->Host->banAll();
				$this->view->message = UiUtil::printMessages('All hosts banned.');
			}
			else if ( !empty($_POST['banAllIn'])  ) {
				$ws->Host->banAllIn();
				$this->view->message = UiUtil::printMessages('All hosts in banned.');
			}
			else if ( !empty($_POST['banAllOut'])  ) {
				$ws->Host->banAllOut();
				$this->view->message = UiUtil::printMessages('All hosts out banned.');
			}
			if ( !empty($_POST['allowAll'])  ) {
				$ws->Host->banAll(true);
				$this->view->message = UiUtil::printMessages('All hosts ban canceled.');
			}
			else if ( !empty($_POST['allowAllIn'])  ) {
				$ws->Host->banAllIn(true);
				$this->view->message = UiUtil::printMessages('All hosts in ban canceled.');
			}
			else if ( !empty($_POST['allowAllOut'])  ) {
				$ws->Host->banAllOut(true);
				$this->view->message = UiUtil::printMessages('All hosts out ban canceled .');
			}
			else {
				foreach ( $_POST as $post => $v ) {
					$post = explode(':',$post);

					if ( 'ban' == $post[0] && isset($post[1]) ) {
						$ws->Host->ban($post[1]);
						$this->view->message = UiUtil::printMessages('Host was banned.');
					}

					if ( 'allow' == $post[0] && isset($post[1]) ) {
						$ws->Host->allow($post[1]);
						$this->view->message = UiUtil::printMessages('Host was allowed.');
					}

					if ( 'banIn' == $post[0] && isset($post[1]) ) {
						$ws->Host->banIn($post[1]);
						$this->view->message = UiUtil::printMessages('Host in was banned.');
					}

					if ( 'allowIn' == $post[0] && isset($post[1]) ) {
						$ws->Host->allowIn($post[1]);
						$this->view->message = UiUtil::printMessages('Host in was allowed.');
					}

					if ( 'banOut' == $post[0] && isset($post[1]) ) {
						$ws->Host->banOut($post[1]);
						$this->view->message = UiUtil::printMessages('Host out was banned.');
					}

					if ( 'allowOut' == $post[0] && isset($post[1]) ) {
						$ws->Host->allowOut($post[1]);
						$this->view->message = UiUtil::printMessages('Host out was allowed.');
					}

					if ( 'clearFingerprint' == $post[0] && isset($post[1]) ) {
						$ws->Host->clearFingerprint($post[1]);
						$this->view->message = UiUtil::printMessages('Host fingerprint was cleared.');
					}
				}
			}
		}

		Models_Host::createTable($this->view, $this->getRequest()->getPost());
		
		$this->view->allBanned = $ws->Host->allBanned();
		$this->view->allInBanned = $ws->Host->allInBanned();
		$this->view->allOutBanned = $ws->Host->allOutBanned();			

		$this->view->viewSectionId = $this->viewSectionId;

	}

} // HostDashboardController
