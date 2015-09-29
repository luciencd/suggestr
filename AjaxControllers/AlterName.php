<?php

class Global_MakeDayController extends AjaxController {
	public $template = "Global_MakeDay";
	public function process($get,$post) {
		if(1==100){
			$this->pageData['failureReason'] = 'you fucked up!';
			return false;
		}
		$this->pageData['test'] = 100;
		return true;
	}
}

?>