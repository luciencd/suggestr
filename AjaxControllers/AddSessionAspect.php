<?php

class AddSessionAspectController extends AjaxController {
	public function process($get,$post) {
		
		//REALLY NEEDS WORK

		//Need to redo this function.
		if(!isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		
		//In case we need to get the id of the major from a name.
		if($post['column'] == "major_name"){

			//work out special cases.
			$query = new Query('departments');
			$result = $query->select('*', array(array('name', '=', $post['value'])), '', 2,true);
			if(Count($result) > 0){
				$department_id = $result[0]->get('id');
			}	
		}
		


		
		
		
		// Now add this course to the user's model
		$session = new Session();
		
		$session->findById($_COOKIE['sessionId']);
		$session->set($post['column'], ucwords($department_id));
		if(!is_numeric($department_id)){
			$session->set("major_id", 0);
			
		}else{
			$session->set("major_id", $department_id);
		}
		$session->set("major_input",$post['value']);
		 // Just so that the ORM class thinks something's dirty and allows entry of an empty row
		
		$session->save();
		return true;
	}
}

?>