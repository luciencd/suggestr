<?php

class RemoveCourseController extends AjaxController {
	public function process($get,$post) {
		$_COOKIE['sessionId'] = 1;


		if(!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Error: not numeric sessionid or courseid';
			return false;
		}
		$query = new Query('action');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->delete(array(array('course_id', '=', $post['course_id']), 
									   array('session_id', '=', $_COOKIE['sessionId'])));
		if(!$result){
			// Abort because this course is already in the user's model
			$this->failureReason = 'Error: Already in model.';
			return false;
		}
		return true;
	}
}

?>