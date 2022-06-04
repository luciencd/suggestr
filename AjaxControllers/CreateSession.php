<?php
require_once('./AjaxModels/SessionModel.php');
class CreateSessionController extends AjaxController {
	public function process($get,$post) {

		$session_model = new SessionModel();
		$session_model->handleSession();
		header('Location: /');
		#return $session_model->getCurrentSessionId();
	}
}

?>