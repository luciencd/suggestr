<?php

class TookCourseController extends AjaxController {
	public function process($get,$post) {
		
		//return "fuck";

		if(!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		$session = new Session();
		$session->findById($_COOKIE['sessionId']);
		$major_id = $session->get('department_id');

		$query = new Query('action');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->select('*', array(array('course_id', '=', $post['course_id']), 
											array('session_id', '=', $_COOKIE['sessionId'])), '', 1);
		if(count($result)!=0){
			// Abort because this course is already in the user's model
			$this->failureReason = 'Sorry, there was an error';
			return false;
		}
		// Now add this course to the user's model
		$action = new Action();
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->set('major',"");
		$action->set('year',"");
		$action->set('choice', 1);
		$action->set('POST',"");
		$action->save();
		//echo $action;
		#$this->pageData = var_dump($action);
		return true;
	}
}

?>