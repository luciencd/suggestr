<?php
require_once('Controllers/suggestions.php');

class MajorRelationsController extends AjaxController {
	public $template = "MajorRelations";//Identical to search one.

	public function process($get,$post) {

		//header('Content-Type: application/json');
		//echo json_encode(array('foo' => 'bar'));

		$Data = new Database();
		$DUMP = $Data->updateMajorRelations();

		$this->pageData['SQL'] = $DUMP[0];
		$this->pageData['count'] = $DUMP[1];
		//$this->pageData['SQL'] = $DUMP[0];
		return true;

		
	}
}

?>