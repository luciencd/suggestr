<?php
class SliderController extends AjaxController {
	public $template = "Slider";

	public function process($get,$post) {
		
		
		//Updating difficulty slider. maybe put in separate function?
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']),
											array('choice', '=', 0)));

		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}

		//Should expand this to updating an array of sliders...
		$Data = $GLOBALS['MODEL']['Data'];

		/*
		In order to get correct difficulty, you need to reload everything...
		*/
		$Data->load();


		$this->pageData['percent'] = 100.0*$Data->semesterDifficulty($idsAlreadyAdded);

		$this->pageData['sliderName'] = "difficulty";
		//100*$Data->semesterDifficulty($idsAlreadyAdded);


		//echo 100*$Data->semesterDifficulty($idsAlreadyAdded);
		//echo 100*$Data->semesterDifficulty($idsAlreadyAdded);
		return true;
		//header('Content-Type: application/json');
		//echo json_encode(array('difficulty' => 100*$Data->semesterDifficulty($idsAlreadyAdded)));
	}
}
?>