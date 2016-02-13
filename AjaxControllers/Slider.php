<?php
class SliderController extends AjaxController {
	public $template = "Slider";

	public function process($get,$post) {
		
		
		//Updating difficulty slider. maybe put in separate function?
		

		//Should expand this to updating an array of sliders...
		$Data = $GLOBALS['MODEL']['Data'];
		$Data->load();

		/*
		In order to get correct difficulty, you need to reload everything...
		*/
		//$Data
		$student = $Data->getStudent($_COOKIE['sessionId']);

		//Get the classes you just added, and determine the difficulty.
		$idsAlreadyAdded = $student->getAdded();


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