<?php
//Working on tags.
class GetMajorsController extends AjaxController {
	public $template = "departments";
	public function process($get,$post) {
		
		//Checking the inputs //Currently sessions don't work.
		
		//Check if the vote is positive or negative(future version)
		$query = new Query('departments');
		$result = $query->select('*', '', '', '',true);
		//Checking if particular session has already pressed this tag button.
		$array = array();
		$count = 0;
		foreach($result as $orm){
			array_push($array,array('id' => $orm->get('id'),'name' => $orm->get('name')));
			//echo implode("|",array('id' => $count,'name' => $orm->get('name')));
			$count+=1;
		}
		$output = json_encode($array);

		$this->pageData['name'] = $output;
		
		return true;
	}
}

?>