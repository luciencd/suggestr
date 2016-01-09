<?php

class IgnoreCourseController extends AjaxController {
	public function process($get,$post) {
		


		if(!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		$query = new Query('action');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->select('*', array(array('course_id', '=', $post['course_id']), 
											array('session_id', '=', $_COOKIE['sessionId'])), '', 2);
		if(count($result)!=0){
			// Abort because this course is already in the user's model
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}
		// Now add this course to the user's model
		$action = new Action();
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->set('choice', 2);
		$action->save();
		return true;
	}
}

?>