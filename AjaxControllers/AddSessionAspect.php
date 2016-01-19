<?php

class AddSessionAspectController extends AjaxController {
	public function process($get,$post) {
		

		//Need to redo this function.
		if(!isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		
		$query = new Query('departments');
		$result = $query->select('*', array(array('name', '=', $post['value'])), '', 2,true);


		if(Count($result) > 0){
			$department_id = $result[0]->get('id');
		}
		

		if(!is_numeric($department_id)){
			return false;
		}
		
		
		// Now add this course to the user's model
		$session = new Session();
		
		$session->findById($_COOKIE['sessionId']);
		$session->set($post['column'], ucwords($department_id));
		$session->set("department_name", $department_id); // Just so that the ORM class thinks something's dirty and allows entry of an empty row
		$session->save();
		return true;
	}
}

?>