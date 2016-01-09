<?php

class ResetController extends AjaxController {
	public function process($get,$post) {
		unset($_COOKIE['sessionId']);
		return true;
	}
}

?>