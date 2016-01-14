<?php

class AddSessionAspectController extends AjaxController {
	public function process($get,$post) {
		


		if(!isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		
		$query = new Query('departments');
		$result = $query->select('*', array(array('name', '=', $post['value'])), '', 2,true);

		if(count($result)!=0){
			// Abort because this tag has already been voted on by this session on this course.
			$this->failureReason = 'department doesn\'t exist.';
			//$result[0]->delete();
			
		}

		// Now add this course to the user's model
		$session = new Session();
		$session->findById($_COOKIE['sessionId']);
		$session->set($post['column'], $post['value']); // Just so that the ORM class thinks something's dirty and allows entry of an empty row
		$session->save();
		return true;
	}
}

?>