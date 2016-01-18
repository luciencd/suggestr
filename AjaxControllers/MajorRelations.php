<?php
require_once('Controllers/suggestions.php');

class MajorRelationsController extends AjaxController {
	public $template = "MajorRelations";//Identical to search one.

	public function process($get,$post) {
		$Data = new Database();//Find a way to make this local to suggestr.php or something.
		
		$Data->updateMajorRelations();
		return false;
	}
}

?>